<?php namespace exface\Core\Exceptions\Contexts;

/**
 * Exception thrown if a context fails to save it's data in the respective scope.
 *
 * @author Andrej Kabachnik
 *
 */
class ContextSaveError extends ContextRuntimeError {
	
	public static function get_default_alias(){
		return '6T5E3ID';
	}
	
}