<?php
namespace exface\Core\Exceptions\DataSources;

use exface\Core\Interfaces\DataSources\DataConnectionInterface;
use exface\Core\Exceptions\ExceptionTrait;

/**
 * This trait enables an exception to output data connectior specific debug information.
 *
 * @author Andrej Kabachnik
 *
 */
trait DataConnectorExceptionTrait {
	
	use ExceptionTrait {
		create_widget as create_parent_widget;
	}
	
	private $connector = null;
	
	/**
	 * 
	 * @param DataConnectionInterface $connector
	 * @param string $message
	 * @param string $alias
	 * @param \Throwable $previous
	 */
	public function __construct (DataConnectionInterface $connector, $message, $alias = null, $previous = null) {
		parent::__construct($message, null, $previous);
		$this->set_alias($alias);
		$this->set_connector($connector);
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\Exceptions\DataConnectorExceptionInterface::get_connector()
	 */
	public function get_connector(){
		return $this->connector;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \exface\Core\Interfaces\Exceptions\DataConnectorExceptionInterface::set_connector()
	 */
	public function set_connector(DataConnectionInterface $connector){
		$this->connector = $connector;
		return $this;
	}
	
}
?>