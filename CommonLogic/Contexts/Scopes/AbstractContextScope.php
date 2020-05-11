<?php
namespace exface\Core\CommonLogic\Contexts\Scopes;

use exface\Core\Interfaces\Contexts\ContextScopeInterface;
use exface\Core\Interfaces\Contexts\ContextInterface;
use exface\Core\Contexts\FilterContext;
use exface\Core\Contexts\ActionContext;
use exface\Core\CommonLogic\Workbench;
use exface\Core\Factories\ContextFactory;
use exface\Core\Factories\SelectorFactory;
use exface\Core\Exceptions\Security\AccessPermissionDeniedError;
use exface\Core\Interfaces\Log\LoggerInterface;

abstract class AbstractContextScope implements ContextScopeInterface
{
    private $active_contexts = array();

    private $exface = NULL;

    private $name = null;

    public function __construct(Workbench $exface)
    {
        $this->exface = $exface;
        $this->name = str_replace('ContextScope', '', substr(get_class($this), (strrpos(get_class($this), '\\') + 1)));
    }

    /**
     * Performs all neccessary logic to get the context scope up and running.
     * This may be connecting to DBs,
     * reading files, preparing data structures, etc. This method is called right after each context scope is
     * created.
     *
     * @return AbstractContextScope
     */
    public function init()
    {
        return $this;
    }

    /**
     * Returns the filter context of the current scope.
     * Shortcut for calling get_context('filter')
     *
     * @return FilterContext
     */
    public function getFilterContext()
    {
        return $this->getContext('exface.Core.FilterContext');
    }

    /**
     * Returns the action context of the current scope.
     * Shortcut for calling get_context ('action')
     *
     * @return ActionContext
     */
    public function getActionContext()
    {
        return $this->getContext('exface.Core.ActionContext');
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::getContextsLoaded()
     */
    public function getContextsLoaded()
    {
        return $this->active_contexts;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::getContext()
     */
    public function getContext($alias)
    {
        // If no context matching the alias exists, try to create one
        if (! $this->active_contexts[$alias]) {
            $selector = SelectorFactory::createContextSelector($this->getWorkbench(), $alias);            
            $context = ContextFactory::createInScope($selector, $this);
            $context = $this->getContextManager()->authorize($context);
            $this->active_contexts[$alias] = $context;
            $this->loadContextData($context);
        }
        return $this->active_contexts[$alias];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::removeContext()
     */
    public function removeContext($alias)
    {
        unset($this->active_contexts[$alias]);
        return $this;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::reloadContext()
     */
    public function reloadContext(ContextInterface $context)
    {
        $this->loadContextData($context);
        return;
    }

    /**
     * Loads data saved in the current context scope into the given context object
     *
     * @return AbstractContextScope
     */
    abstract protected function loadContextData(ContextInterface $context);

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::saveContexts()
     */
    abstract public function saveContexts();

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\WorkbenchDependantInterface::getWorkbench()
     */
    public function getWorkbench()
    {
        return $this->exface;
    }

    /**
     *
     * {@inheritdoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::getContextManager()
     */
    public function getContextManager()
    {
        return $this->getWorkbench()->getContext();
    }

    /**
     * 
     * {@inheritDoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::getScopeId()
     */
    public function getScopeId()
    {
        return;
    }

    /**
     *
     * {@inheritdoc}
     * @see \exface\Core\Interfaces\Contexts\ContextScopeInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }
}
?>