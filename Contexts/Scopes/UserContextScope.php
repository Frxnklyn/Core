<?php namespace exface\Core\Contexts\Scopes;

use exface\Core\Interfaces\Contexts\ContextInterface;
use exface\Core\Factories\DataSheetFactory;

class UserContextScope extends AbstractContextScope {
	private $user_data = null;
	private $user_locale = null;
	
	public function get_user_name(){
		return $this->get_workbench()->cms()->get_user_name();
	}
	
	public function get_user_id(){
		return $this->get_user_data()->get_uid_column()->get_cell_value(0);
	}
		
	/**
	 * Returns the absolute path to the base installation folder (e.g. c:\xampp\htdocs\exface\exface\UserData\username)
	 * @return string
	 */
	public function get_user_data_folder_absolute_path(){
		$path = $this->get_workbench()->filemanager()->get_path_to_user_data_folder() . DIRECTORY_SEPARATOR . $this->get_user_data_folder_name();
		if (!file_exists($path)){
			mkdir($path);
		}
		return $path;
	}
	
	public function get_user_data_folder_name(){
		return $this->get_user_name() ? $this->get_user_name() : '.anonymous';
	}
	
	/**
	 * TODO
	 * @see \exface\Core\Contexts\Scopes\AbstractContextScope::load_context_data()
	 */
	public function load_context_data(ContextInterface $context){
		
	}
	
	public function save_contexts(){
	
	}
	
	/**
	 * Returns a data sheet with all data from the user object
	 * @return \exface\Core\Interfaces\DataSheets\DataSheetInterface
	 */
	protected function get_user_data(){
		if (is_null($this->user_data)){
			$user_object = $this->get_workbench()->model()->get_object('exface.Core.USER');
			$ds = DataSheetFactory::create_from_object($user_object);
			$ds->get_columns()->add_from_expression($user_object->get_uid_alias());
			$ds->get_columns()->add_from_expression('USERNAME');
			$ds->get_columns()->add_from_expression('FIRST_NAME');
			$ds->get_columns()->add_from_expression('LAST_NAME');
			$ds->add_filter_from_string('USERNAME', $this->get_user_name());
			$ds->data_read();
			$this->user_data = $ds;
		}
		return $this->user_data;
	}
	
	/**
	 * Returns the locale, set for the current user
	 * @return string
	 */
	public function get_user_locale(){
		if (is_null($this->user_locale) && $cms_locale = $this->get_workbench()->CMS()->get_user_locale()){
			$this->set_user_locale($cms_locale);
		}
		return $this->user_locale;
	}
	
	/**
	 * Sets the locale for the current user
	 * @param string $string
	 * @return \exface\Core\Contexts\Scopes\UserContextScope
	 */
	public function set_user_locale($string){
		$this->user_locale = $string;
		return $this;
	}
}
?>