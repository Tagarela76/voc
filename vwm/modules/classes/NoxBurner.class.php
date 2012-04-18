<?php

class NoxBurner {

	/**
	 * @var db - xnyo database object
	 */
	private $db;

	private $burner_id;
	private $manufacturer_id;
	private $serial;
	private $model;
	private $btu;
	private $department_id;
	private $input;
	private $output;	
//	private $equipment_id;	
	
	public function __construct(db $db, Array $array = null) {
		$this->db = $db;
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

                if ($this->burner_id != NULL){
                        $query = "UPDATE burner SET 
								manufacturer_id = '".mysql_escape_string($this->manufacturer_id)."',
								department_id = '".mysql_escape_string($this->department_id)."',
								model = '".mysql_escape_string($this->model)."',
								serial = '".mysql_escape_string($this->serial)."',
								input = '".mysql_escape_string($this->input)."',
								output = '".mysql_escape_string($this->output)."',
						
								btu = '".mysql_escape_string($this->btu)."'
								WHERE burner_id = {$this->burner_id}";
                }
                else {                        
   
                       $query = "INSERT INTO burner VALUES (NULL,'"
												.mysql_escape_string($this->department_id)."','"
												.mysql_escape_string($this->manufacturer_id)."','"
												.mysql_escape_string($this->model)."','" 
												.mysql_escape_string($this->serial)."','"
												.mysql_escape_string($this->input)."','"
												.mysql_escape_string($this->output)."','" 
												.mysql_escape_string($this->btu)."')";
                                               

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

	public function get_burner_id(){
		return $this->burner_id;
	}
	public function get_department_id(){
		return $this->department_id;
	}	
	public function get_manufacturer_id(){
		return $this->manufacturer_id;
	}	

	public function get_serial(){
		return $this->serial;
	}
	public function get_model(){
		return $this->model;
	}	
	
	public function get_btu(){
		return $this->btu;
	}
	public function get_input(){
		return $this->input;
	}	
    public function get_output(){
		return $this->output;
	}
/*	
    public function get_equipment_id(){
		return $this->equipment_id;
	}	
*/
	public function set_burner_id($value) {
		try {
			$this->burner_id = $value;
		} catch(Exception $e) {
			throw new Exception("burner Id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_manufacturer_id($value) {
		try {
			$this->manufacturer_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("manufacturer id cannot be empty!" . $e->getMessage());
		}
	}
	public function set_department_id($value) {
		try {
			$this->department_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Department id cannot be empty!" . $e->getMessage());
		}
	}	
	public function set_serial($value) {
		try {
			$this->serial = $value;
	
		} catch(Exception $e) {
			throw new Exception("serial id cannot be empty!" . $e->getMessage());
		}
	}	
	public function set_model($value) {
		try {
			$this->model = $value;
	
		} catch(Exception $e) {
			throw new Exception("model cannot be empty!" . $e->getMessage());
		}
	}	
	public function set_btu	($value) {
		try {
			$this->btu	 = $value;
	
		} catch(Exception $e) {
			throw new Exception("btu type id cannot be empty!" . $e->getMessage());
		}
	}
	public function set_input	($value) {
		try {
			$this->input	 = $value;
	
		} catch(Exception $e) {
			throw new Exception("Input type id cannot be empty!" . $e->getMessage());
		}
	}	

	public function set_output($value) {
		try {
			$this->output = $value;
	
		} catch(Exception $e) {
			throw new Exception("Output id cannot be empty!" . $e->getMessage());
		}
	}
/*	
	public function set_equipment_id($value) {
		try {
			$this->equipment_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Equipment id cannot be empty!" . $e->getMessage());
		}
	}
 */	
}
?>
