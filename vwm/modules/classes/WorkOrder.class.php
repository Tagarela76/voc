<?php

class WorkOrder {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var string 
	 */
	public $number;
	
	/**
	 *
	 * @var string 
	 */
	public $description;
	
	/**
	 *
	 * @var string 
	 */
	public $customer_name;

	/**
	 *
	 * @var string 
	 */
	public $facility_id;

	/**
	 *
	 * @var string 
	 */
	public $status;
	
	/**
	 * db connection
	 * @var db 
	 */
	private $db;


	function __construct(db $db, $workOrderId = null) {
		$this->db = $db;

		if (isset($workOrderId)) {
			$this->id = $workOrderId;
			$this->_load();
		}
	}

	/**
	 * add work order
	 * @return int 
	 */
	public function addWorkOrder() {

		$query = "INSERT INTO " . TB_WORK_ORDER . "(number, description, customer_name, facility_id, status) 
				VALUES ( 
				'" . $this->db->sqltext($this->number) . "'
				, '" . $this->db->sqltext($this->description) . "'
				, '" . $this->db->sqltext($this->customer_name) . "'
				, '" . $this->db->sqltext($this->facility_id) . "'	
				, '" . $this->db->sqltext($this->status) . "'	
				)";
		$this->db->query($query); 
		$work_order_id = $this->db->getLastInsertedID();
		$this->id = $work_order_id;
		return $work_order_id;
	}

	/**
	 * update work order
	 * @return int 
	 */
	public function updateWorkOrder() {

		$query = "UPDATE " . TB_WORK_ORDER . "
					set number='" . $this->db->sqltext($this->number) . "',
						description='" . $this->db->sqltext($this->description) . "',
						customer_name='" . $this->db->sqltext($this->customer_name) . "',	
						facility_id='" . $this->db->sqltext($this->facility_id) . "',
						status='" . $this->db->sqltext($this->status) . "'	
					WHERE id= " . $this->db->sqltext($this->id);
		$this->db->query($query);

		return $this->id;
	}

	/**
	 *
	 * delete work order
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_WORK_ORDER . "
				 WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}

	/**
	 * insert or update work order
	 * @return int 
	 */
	public function save() {

		if (!isset($this->id)) {
			$work_order_id = $this->addWorkOrder();
		} else {
			$work_order_id = $this->updateWorkOrder();
		}
		return $work_order_id;
	}

	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param string $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {


		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else {
			return $this->$name;
		}
	}

	/**
	 * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	 * @param string $name - name of property
	 * @param mixed $value - value to set
	 */
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/*
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			$this->$name = $value;
		}
		/*
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_WORK_ORDER . "
				 WHERE id='" . $this->db->sqltext($this->id) .
				"' LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}

}

?>