<?php
class Bookmark {    
    
        private $id;
        private $name;
        private $controller;
        private $db;
        public $errors;
    
	function __construct($db, Array $array = null) {
                $this->db=$db;
		
		if(isset($array)) {
			$this->initByArray($array);
		}
        }
        
	private function initByArray($array) {                        
		foreach($array as $key => $value) {
			try {
				$this->__set($key, $value);
			}catch(Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}   
        
        private function escapeValue($value) {
		$value = strip_tags($value);
		return $value;
	}
        
        private function set_id($value) {
		try {
			$this->id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!");
		}
	}
	
	private function set_name($value) {
		
		try {
			$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->name = $value;
		} catch(Exception $e) {
			$this->errors["name"] = $e->getMessage();
			throw new Exception("name cannot be empty!");
		}
	}        
        
        private function set_controller($value) {
		try {
			$this->controller = $value;
		} catch(Exception $e) {
			throw new Exception("Bookmark Type: " . $e->getMessage());
		}
	}
        
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
        
	public function saveBookmark() {

                if ($this->id != NULL){
                        $query = "UPDATE " . TB_BOOKMARKS_TYPE . " SET 
					name = '".mysql_escape_string($this->name)."',
					controller = '".mysql_escape_string($this->controller)."'
					WHERE id = {$this->id}";
                }
                else {                        
                        $query = " SELECT * FROM " . TB_BOOKMARKS_TYPE . " WHERE name = '".mysql_escape_string($this->name)."'";
                        $this->db->query($query);
                        $arr = $this->db->fetch_all_array();
                        if( count($arr) == 0 )
                        {   
                            $query = "INSERT INTO " . TB_BOOKMARKS_TYPE . " (name, controller) VALUES ('"
						/*.$this->id.","*/
                                                .($this->name)."','" 
                                                .($this->controller)."')";
                        }
                }	
		
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
        
	public function deleteBookmark($bookmarkID) {//var_dump($bookmarkID);
		$query = "DELETE FROM ". TB_BOOKMARKS_TYPE . " WHERE id = ".$bookmarkID."";
		$query = mysql_escape_string($query);
		$this->db->query($query);
                                
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
        
	public function unsafe_set_value($property,$value) {
		$this->$property = $value;
	}        
        
	 public function getErrorMessage() {
	 	if(!empty($this->errors)) {
	 		foreach($this->errors as $e) {
	 			$msg .= $e . "<br/>";
	 		}
	 		return $msg;
	 	} else {
	 		return false;
	 	}
	 }
         
	private function checkEmpty($value) {
		if(!isset($value) or empty($value)) {
			throw new Exception("Value is empty");
		} else if(strlen(html_entity_decode($value)) > 22) {
			throw new Exception("Value is too long (max 22 symbols)");
		}
	}         
         
	/**
	 * 
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method get_%property_name%, if method does not exists - return property value; 
	 */
	public function __get($name) {       
        	if(method_exists($this, "get_".$name)) {
        		$methodName = "get_".$name;
        		$res = $this->$methodName();
        		return $res;
        	}
	        else if(property_exists($this,$name)) {
	        	return $this->$name;
	        } else {
	        	return false;
	        }
	}
	
	/**
	* 
	* Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	* @param unknown_type $name - name of property
	* @param unknown_type $value - value to set
	*/
	public function __set($name,$value) {
	        
	    	/*Call setter only if setter exists*/
	        if(method_exists($this, "set_".$name)) {
        		$methodName = "set_".$name;
        		$this->$methodName($value);
        	}
        	/**
        	 * Set property value only if property does not exists (in order to do not revrite privat or protected properties), 
        	 * it will craete dynamic property, like usually does PHP
        	*/
	        else if(!property_exists($this,$name)){
	        	/**
	        	 * Disallow add new properties dynamicly (cause of its change type of object to stdObject, i dont want that)
	        	 */
	        	//$this->$name = $value;
	        }
	        /**
	         * property exists and private or protected, do not touch. Keep OOP
	         */ 
	        else {
	        	//Do nothing
	        }
	 }        
    
}

?>