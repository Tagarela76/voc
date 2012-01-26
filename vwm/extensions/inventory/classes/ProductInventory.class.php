<?php

class ProductInventory {

	/**
	 * @var db - xnyo database object
	 */
	private $db;
	
	
	private $id;
	private $product_id;
	private $in_stock;
	private $in_stock_unit_type = self::GALLON_UNIT_TYPE_ID;

	/**
	 * @var float usage for period 
	 */
	private $usage;

	/**
	 * @var DateTime The day from we start count usage
	 */
	private $period_start_date;

	/**
	 * @var DateTime The day from we start count usage
	 */
	private $period_end_date;

	const GALLON_UNIT_TYPE_ID = 1;

	public function __construct(db $db) {
		$this->db = $db;
		
		//	first day of this month by default
		$this->period_start_date = new DateTime('first day of this month');
		//	this is today by default
		$this->period_end_date = new DateTime();
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

}

?>
