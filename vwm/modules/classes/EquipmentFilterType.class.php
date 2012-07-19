<?php

class EquipmentFilterType {

	/**
	 *
	 * @var int 
	 */
	public $equipment_filter_type_id;
	
	/**
	 *
	 * @var string 
	 */
	public $name;
	
	/**
	 * db connection
	 * @var DB 
	 */
    private $db;

	function EquipmentFilterType(db $db, $equipment_filter_type_id = null) {
		$this->db = $db;
		
		if (isset($equipment_filter_type_id)) {
			$this->equipment_filter_type_id = $equipment_filter_type_id;
			$this->_load();
		}
	}

	
	/**
	 * insert equipment filter type
	 */
	public function save() {

		$query = "INSERT INTO " . TB_EQUIPMENT_FILTER_TYPE . "(name) VALUES ('" . $this->db->sqltext($this->name) . "')";
		$this->db->query($query);
		
	}
	
	public function delete() {
		
		$sql = "DELETE FROM ". TB_EQUIPMENT_FILTER_TYPE. "
				 WHERE equipment_filter_type_id=" . $this->db->sqltext($this->equipment_filter_type_id); 
		$this->db->query($sql);
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
		 */
		else if (!property_exists($this, $name)) {
			$this->$name = $value;
		}
		/*
		 * property exists and private or protected, do not touch. Keep OOP
		 */
		else {
			//Do nothing
		}
	}
	
	private function _load() {
		
		if (!isset($this->equipment_filter_type_id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM ". TB_EQUIPMENT_FILTER_TYPE. "
				 WHERE equipment_filter_type_id=" . $this->db->sqltext($this->equipment_filter_type_id) . 
				" LIMIT 1"; 
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