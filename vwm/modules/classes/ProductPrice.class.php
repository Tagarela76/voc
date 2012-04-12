<?php

class ProductPrice {

	/**
	 * @var db - xnyo database object
	 */
	private $db;
	
	
	private $price_id;
	private $product_id;
	private $price;
	private $unittype = self::GALLON;

	private $supman_id; // supplier
	private $jobber_id; // Person from jobber
	public $errors;


	/**
	 * @var DateTime The day when order was created
	 */



	const GALLON = 1;

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

                if ($this->price_id != NULL){
                        $query = "UPDATE price4product SET 
							product_id = '".mysql_escape_string($this->product_id)."',
							jobber_id = '".mysql_escape_string($this->jobber_id)."',	
							price = '".mysql_escape_string($this->price)."',
							unittype = '".mysql_escape_string($this->unittype)."',
							supman_id = '".mysql_escape_string($this->supman_id)."'
							WHERE price_id = {$this->price_id}";
                }
                else {                        
   
                            $query = "INSERT INTO price4product VALUES (NULL,'"
												.mysql_escape_string($this->jobber_id)."','" 
												.mysql_escape_string($this->supman_id)."','" 
												.mysql_escape_string($this->product_id)."','" 
                                                .mysql_escape_string($this->unittype)."','" 
												.mysql_escape_string($this->price)."')";
                                               

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
			$this->$name = $value;
		}
		/**
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	
	
	public function get_price_id(){
		return $this->price_id;
	}
	
	public function get_product_id(){
		return $this->product_id;
	}

    public function get_price(){
		return $this->price;
	}
	
    public function get_unittype(){
		return $this->unittype;
	}
	
    public function get_supman_id(){
		return $this->supman_id;
	}	
	
    public function get_jobber_id(){
		return $this->jobber_id;
	}	

	
	public function set_price_id($value) {
		try {
			$this->price_id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_product_id($value) {
		try {
			$this->product_id = $value;
	
		} catch(Exception $e) {
			throw new Exception("Product id cannot be empty!" . $e->getMessage());
		}
	}
	
	
	
	public function set_price($value) {
		try {
			$this->price = $value;
	
		} catch(Exception $e) {
			throw new Exception("Price cannot be empty!" . $e->getMessage());
		}
	}	
	
	public function set_unittype($value) {
		try {
			$this->unittype = $value;
		} catch(Exception $e) {
			throw new Exception("unite cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_supman_id($value) {
		try {
			$this->supman_id = $value;
		} catch(Exception $e) {
			throw new Exception("supman cannot be empty!" . $e->getMessage());
		}
	}
	
	public function set_jobber_id($value) {
		try {
			$this->jobber_id = $value;
		} catch(Exception $e) {
			throw new Exception("Jobber ID cannot be empty!" . $e->getMessage());
		}
	}	

}
?>
