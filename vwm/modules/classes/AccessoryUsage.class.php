<?php

class AccessoryUsage {
	
	/**
	 * xnyo data base
	 * @var db
	 */
	private $db;
	
	
	public $id;
	public $accessory_id;
	public $department_id;
			
	/**	 
	 * @var DateTime
	 */
	public $date;
	public $usage;
	
	
	public function __construct(db $db) {
		$this->db = $db;
		$this->date = new DateTime();
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
			$this->$name = $value;
		}
		/**
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}
	
	
	public function set_date(DateTime $date) {
		$this->date = $date;
	}
	
	public function save() {
		if ($this->id) {
			
		} else {
			$this->insert();
		}
	}
	
	
	private function insert() {
		
		$sql = "INSERT INTO `accessory_usage` (`accessory_id`, `department_id`, `date`, `usage`) VALUES (" .
			mysql_escape_string($this->accessory_id).", " .
			mysql_escape_string($this->department_id).", " .	
			$this->date->getTimestamp().", " .
			mysql_escape_string($this->usage).")";			
		$this->db->exec($sql);
		
		$this->id = $this->db->getLastInsertedID();
	}
	
	
	private function update() {
		
	}
		
	
}

?>
