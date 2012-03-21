<?php

class InventoryNew {

	/**
	 * @var db - xnyo database object
	 */
	protected $db;
	protected $id;
	protected $facility_id;
	protected $name;
	protected $in_stock = 0;
	protected $amount = 0;
	protected $limit = 0;
	public $pxCount;
	public $errors;
	public $url;

	/**
	 * @var float usage for period 
	 */
	protected $usage;

	/**
	 * @var DateTime The day from we start count usage
	 */
	protected $period_start_date;

	/**
	 * @var DateTime The day from we start count usage
	 */
	protected $period_end_date;

	public function __construct(db $db, Array $array = null) {
		$this->db = $db;

		//	first day of this month by default
		$date = new DateTime('first day of this month');
		$date->setTime(0, 0, 0);
		$this->period_start_date = $date;
		//	this is today by default
		$this->period_end_date = new DateTime();


		if (isset($array)) {
			$this->initByArray($array);
		}
	}

	protected function initByArray($array) {
		foreach ($array as $key => $value) {
			try {
				$this->__set($key, $value);
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
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

	public function get_usage() {
		return $this->usage;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_in_stock() {
		return $this->in_stock;
	}

	public function get_facility_id() {
		return $this->facility_id;
	}

	public function get_inventry_id() {
		return $this->id;
	}

	public function get_amount() {
		return $this->amount;
	}

	public function get_inventry_limit() {
		return $this->limit;
	}

	public function set_sum($value) {
		try {
			$this->usage = $value;
		} catch (Exception $e) {
			throw new Exception("Usage cannot be empty!" . $e->getMessage());
		}
	}

	public function set_usage($value) {
		try {
			$this->usage = $value;
		} catch (Exception $e) {
			throw new Exception("Usage cannot be empty!" . $e->getMessage());
		}
	}

	public function set_name($value) {
		try {
			$this->name = $value;
		} catch (Exception $e) {
			throw new Exception("Name cannot be empty!" . $e->getMessage());
		}
	}

	public function set_facility_id($value) {
		try {
			$this->facility_id = $value;
		} catch (Exception $e) {
			throw new Exception("Product id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_inventory_id($value) {
		try {
			$this->id = $value;
		} catch (Exception $e) {
			throw new Exception("Inventory id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_in_stock($value) {

		$this->in_stock = $value;
	}

	public function set_amount($value) {

		$this->amount = $value;
	}

	public function set_inventory_limit($value) {

		$this->limit = $value;
	}

}

?>
