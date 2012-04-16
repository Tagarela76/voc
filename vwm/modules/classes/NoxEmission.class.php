<?php

class NoxEmission {

	/**
	 * @var db - xnyo database object
	 */
	private $db;

	private $nox_id;
	private $department_id;
	private $description;

	private $input;
	
	private $output;
	private $gas_unit_used;
	/**
	 * @var INT The time when burner began to work
	 */		
	private $start_time;
	/**
	 * @var INT The time when burner stoppped working
	 */		
	private $end_time;	
	private $burner_id;
	private $note;	

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

                if ($this->nox_id != NULL){
                        $query = "UPDATE nox SET 
								description = '".mysql_escape_string($this->description)."',
								input = '".mysql_escape_string($this->input)."',
								output = '".mysql_escape_string($this->output)."',
								gas_unit_used = '".mysql_escape_string($this->gas_unit_used)."'
								WHERE nox_id = {$this->nox_id}";
                }
                else {                        
   
                       $query = "INSERT INTO nox VALUES (NULL,'"
												.mysql_escape_string($this->department_id)."','"
												.mysql_escape_string($this->description)."','" 
												.mysql_escape_string($this->input)."','" 
												.mysql_escape_string($this->output)."','"
												.mysql_escape_string($this->gas_unit_used)."','" 
												.mysql_escape_string($this->start_time)."','" 
												.mysql_escape_string($this->end_time)."','"
												.mysql_escape_string($this->burner_id)."','" 
												.mysql_escape_string($this->note)."')";
                                               

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

	public function get_nox_id(){
		return $this->nox_id;
	}
	
	public function get_department_id(){
		return $this->department_id;
	}	

	public function get_description(){
		return $this->description;
	}	
	
	public function get_input(){
		return $this->input;
	}	
    public function get_output(){
		return $this->output;
	}
	public function get_gas_unit_used(){
		return $this->gas_unit_used;
	}
	
	public function get_start_time(){
		return $this->start_time;
	}	

	public function get_end_time(){
		return $this->end_time;
	}	
	
	public function get_burner_id(){
		return $this->burner_id;
	}	
    public function get_note(){
		return $this->note;
	}	

	public function set_nox_id($value) {
		try {
			$this->nox_id = $value;
		} catch(Exception $e) {
			throw new Exception("Nox Id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_department_id($value) {
		try {
			$this->department_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Department id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_description($value) {
		try {
			$this->description = $value;
	
		} catch(Exception $e) {
			throw new Exception("Description id cannot be empty!" . $e->getMessage());
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

	public function set_gas_unit_used($value) {
		try {
			$this->gas_unit_used = $value;
		} catch(Exception $e) {
			throw new Exception("Gas unit used cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_start_time($value) {
		try {
			$this->start_time = $value;
	
		} catch(Exception $e) {
			throw new Exception("Start time id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_end_time($value) {
		try {
			$this->end_time = $value;
	
		} catch(Exception $e) {
			throw new Exception("End time id cannot be empty!" . $e->getMessage());
		}
	}	
	public function set_burner_id	($value) {
		try {
			$this->burner_id	 = $value;
	
		} catch(Exception $e) {
			throw new Exception("Burner id type id cannot be empty!" . $e->getMessage());
		}
	}	

	public function set_note($value) {
			$this->note = $value;
	}	
		
	
}
?>
