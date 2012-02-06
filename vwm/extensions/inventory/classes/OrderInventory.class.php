<?php

class OrderInventory {

	/**
	 * @var db - xnyo database object
	 */
	private $db;
	
	
	private $order_id;
	private $order_product_id;
	private $order_facility_id;
	private $order_name;
	private $order_status = self::IN_PROGRESS;
	
	private $order_total;

	public $errors;


	/**
	 * @var DateTime The day when order was created
	 */
	private $order_created_date;
	private $order_completed_date;


	const IN_PROGRESS = 1;
	const CONFIRM = 2;
	const COMPLETED = 3;
	const CANCELED = 4;


	public function __construct(db $db, Array $array = null) {
		$this->db = $db;

		//	this is today by default
		$this->order_created_date = time();
		
		
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

                if ($this->order_id != NULL){
                        $query = "UPDATE inventory_order SET 
							order_product_id = '".mysql_escape_string($this->order_product_id)."',
							order_facility_id = '".mysql_escape_string($this->order_facility_id)."',
							order_name = '".mysql_escape_string($this->order_name)."',
							order_status = '".mysql_escape_string($this->order_status)."',
							order_order_completed_date = '".mysql_escape_string($this->order_completed_date)."',
							order_total	= '".mysql_escape_string($this->order_total)."'	
							WHERE order_id = {$this->order_id}";
                }
                else {                        
   
                            $query = "INSERT INTO inventory_order VALUES (NULL,'"
												.($this->order_product_id)."','" 
												.($this->order_facility_id)."','" 
                                                .($this->order_name)."','" 
												.($this->order_status)."','"
												.($this->order_created_date)."','"
												.($this->order_total). "',NULL)";
                                               

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

	public function get_order_product_id(){
		return $this->order_product_id;
	}
	
	public function get_order_facility_id(){
		return $this->order_facility_id;
	}	
	
    public function get_order_created_date(){
		return $this->order_created_date;
	}
	
    public function get_order_completed_date(){
		return $this->order_completed_date;
	}	
	
    public function get_order_name(){
		return $this->order_name;
	}	
	
	public function get_order_status(){
		return $this->order_status;
	}
	
	public function get_order_total(){
		return $this->order_total;
	}	
	
	public function get_order_id(){
		return $this->order_id;
	}	
	
	

	public function set_order_id($value) {
		try {
			$this->order_id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_order_product_id($value) {
		try {
			$this->order_product_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Product id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_order_facility_id($value) {
		try {
			$this->order_facility_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Facility id cannot be empty!" . $e->getMessage());
		}
	}	
	
	public function set_order_name($value) {
		try {
			$this->order_name = $value;
		} catch(Exception $e) {
			throw new Exception("Name cannot be empty!" . $e->getMessage());
		}
	}	
	
	public function set_order_status($value) {
		try {
			$this->order_status = $value;
		} catch(Exception $e) {
			throw new Exception("Status cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_order_created_date($value) {
		try {
			if ($value instanceof DateTime) {
				$this->order_created_date = $value;	
			} else {
				//	this is unixtimestamp
				$this->order_created_date = DateTime::createFromFormat('U', $value);
			}
			
		} catch(Exception $e) {
			throw new Exception("Date cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_order_completed_date($value) {
		try {
			$this->order_completed_date = $value;
		} catch(Exception $e) {
			throw new Exception("Date cannot be empty!" . $e->getMessage());
		}
	}	
	
	public function set_order_total($value) {

			$this->order_total = $value;

	}	
}
?>
