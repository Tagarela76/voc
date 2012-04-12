<?php

class UserLogging {

	/**
	 * @var db - xnyo database object
	 */
	private $db;
	
	
	private $log_id;
	private $user_id;
	private $action;
	private $action_type;

	/**
	 * @var DateTime The time when user done some action
	 */
	private $date;



	public function __construct(db $db, Array $array = null) {
		$this->db = $db;

		//	this is today by default
		$this->date = time();
		
		
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
	
	public function save() {

                if ($this->log_id != NULL){
                        $query = "UPDATE user_logging SET 
								user_id = '".mysql_escape_string($this->user_id)."',
								action_type = '".mysql_escape_string($this->action_type)."',
								action = '".mysql_escape_string($this->action)."',
								date = '".mysql_escape_string($this->date)."'
								WHERE log_id = {$this->log_id}";
                }
                else {                        
   
                       $query = "INSERT INTO user_logging VALUES (NULL,'"
												.mysql_escape_string($this->user_id)."','"
												.mysql_escape_string($this->action_type)."','" 
												.mysql_escape_string($this->action)."','" 
												.mysql_escape_string($this->date)."')";
                                               

                }	

		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	

	/**
	 * 
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method get_%property_name%, if method does not exists - return property value; 
	 */
	public function __get($name) {
		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else if (property_exists($this, $name)) {
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
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/**
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties), 
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			/**
			 * Disallow add new properties dynamicly (cause of its change type of object to stdObject, i dont want that)
			 */
			//$this->$name = $value;
		}
		/**
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	public function get_log_id(){
		return $this->log_id;
	}
	
	public function get_user_id(){
		return $this->user_id;
	}	

	public function get_action(){
		return $this->action;
	}	
	
	public function get_action_type(){
		return $this->action_type;
	}	
    public function get_date(){
		return $this->date;
	}


	public function set_log_id($value) {
		try {
			$this->log_id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_user_id($value) {
		try {
			$this->user_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("User id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_action($value) {
		try {
			$this->action = $value;
	
		} catch(Exception $e) {
			throw new Exception("Action id cannot be empty!" . $e->getMessage());
		}
	}	
	public function set_action_type	($value) {
		try {
			$this->action_type	 = $value;
	
		} catch(Exception $e) {
			throw new Exception("Action type id cannot be empty!" . $e->getMessage());
		}
	}	

	public function set_date($value) {
		try {
			$this->date = $value;
	
		} catch(Exception $e) {
			throw new Exception("Date id cannot be empty!" . $e->getMessage());
		}
	}	
		
	
}
?>
