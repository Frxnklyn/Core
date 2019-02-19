<?php
namespace exface\Core\Templates\AbstractHttpTemplate\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use exface\Core\Interfaces\WorkbenchInterface;
use Psr\Http\Message\UriInterface;
use exface\Core\Interfaces\Templates\HttpTemplateInterface;
use exface\Core\Exceptions\Templates\TemplateRoutingError;
use exface\Core\Exceptions\Templates\TemplateIncompatibleError;
use exface\Core\Interfaces\Log\LoggerInterface;
use exface\Core\Factories\TemplateFactory;
use GuzzleHttp\Psr7\Response;

/**
 * This PSR-15 middleware will look for a template responsible for the given request
 * based on the routing configuration in the key TEMPLATES.ROUTES of System.config.json.
 * 
 * If one of the template URL patterns matches the URI of the request, the middleware
 * will pass the request to the template handler. If not, the request will be passed
 * on along the responsibilty chain.
 * 
 * Using this middleware, ExFace can be easily integrated into any PSR-15 comilant
 * framework by merely adding the middleware to the stack.
 * 
 * @author Andrej Kabachnik
 *
 */
class TemplateResolverMiddleware implements MiddlewareInterface
{
    private $workbench = null;
    
    /**
     * 
     * @param WorkbenchInterface $workbench
     */
    public function __construct(WorkbenchInterface $workbench)
    {
        $this->workbench = $workbench;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Psr\Http\Server\MiddlewareInterface::process()
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $template = $this->getTemplateForUri($request->getUri());
        } catch (TemplateRoutingError $e) {
            $this->workbench->getLogger()->logException($e);
            return new Response(500, [], $e->getMessage());
        }
        
        if (! ($template instanceof RequestHandlerInterface)) {
            throw new TemplateIncompatibleError('Template "' . $template->getAliasWithNamespace() . '" is cannot be used as a standard HTTP request handler - please check system configuration option TEMPLATES.ROUTES!');
        }
        
        return $template->handle($request);
    }
    
    /**
     * 
     * @param UriInterface $uri
     * @throws TemplateRoutingError
     * @return HttpTemplateInterface
     */
    protected function getTemplateForUri(UriInterface $uri) : HttpTemplateInterface
    {
        $url = $uri->getPath() . '?' . $uri->getQuery();
        $routes = $this->workbench->getConfig()->getOption('TEMPLATES.ROUTES');
        if ($routes->isEmpty()) {
            throw new TemplateRoutingError('No route configuration found is system config option TEMPLATES.ROUTES - (re)install at least one template!');
        }
        foreach ($routes as $pattern => $templateAlias) {
            if (preg_match($pattern, $url) === 1) {
                return TemplateFactory::createFromString($templateAlias, $this->workbench);
            }
        }
        throw new TemplateRoutingError('No route can be found for URL "' . $url . '" - please check system configuration option TEMPLATES.ROUTES or reinstall your template!');
    }
}