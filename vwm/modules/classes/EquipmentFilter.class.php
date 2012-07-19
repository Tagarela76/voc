<?php

class EquipmentFilter {

	/**
	 *
	 * @var int 
	 */
	public $equipment_filter_id;

	/**
	 *
	 * @var string
	 */
	public $name;

	/**
	 *
	 * @var int
	 */
	public $equipment_id;

	/**
	 *
	 * @var int
	 */
	public $length_size;

	/**
	 *
	 * @var int
	 */
	public $width_size;

	/**
	 *
	 * @var int
	 */
	public $height_size;

	/**
	 *
	 * @var int
	 */
	public $equipment_filter_type_id;

	/**
	 *
	 * @var string 
	 */
	public $qty;

	/**
	 * db connection
	 * @var db 
	 */
	private $db;

	/** 	 
	 * @var EquipmentFilterType
	 */
	private $filterType;

	function __construct(db $db, $equipment_filter_id = null) {
		$this->db = $db;

		if (isset($equipment_filter_id)) {
			$this->equipment_filter_id = $equipment_filter_id;
			$this->_load();
		}
	}

	/**
	 * add equipment filter
	 * @return int 
	 */
	public function addNewEquipmentFilter() {

		$query = "INSERT INTO " . TB_EQUIPMENT_FILTER . "(name, equipment_id, height_size, equipment_filter_type_id, qty, width_size, length_size) 
				VALUES ( 
				'" . $this->db->sqltext($this->name) . "'
				," . $this->db->sqltext($this->equipment_id) . "
				, " . $this->db->sqltext($this->height_size) . "
				, " . $this->db->sqltext($this->equipment_filter_type_id) . "
				, " . $this->db->sqltext($this->qty) . "
				, " . $this->db->sqltext($this->width_size) . "
				, " . $this->db->sqltext($this->length_size) . "
				)";
		$this->db->query($query); //die($query);
		$equipment_filter_id = $this->db->getLastInsertedID();
		$this->equipment_filter_id = $equipment_filter_id;
		return $equipment_filter_id;
	}

	/**
	 * update equipment filter
	 * @return int 
	 */
	public function updateEquipmentFilter() {

		$query = "UPDATE " . TB_EQUIPMENT_FILTER . "
					set name='" . $this->db->sqltext($this->name) . "',
						height_size=" . $this->db->sqltext($this->height_size) . ",
						equipment_id=" . $this->db->sqltext($this->equipment_id) . ",
						equipment_filter_type_id=" . $this->db->sqltext($this->equipment_filter_type_id) . ",
						qty=" . $this->db->sqltext($this->qty) . ",
						width_size=" . $this->db->sqltext($this->width_size) . ",
						length_size=" . $this->db->sqltext($this->length_size) . "	
					WHERE equipment_filter_id= " . $this->db->sqltext($this->equipment_filter_id);
		$this->db->query($query);

		return $this->equipment_filter_id;
	}

	/**
	 *
	 * delete equipment filter
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_EQUIPMENT_FILTER . "
				 WHERE equipment_filter_id=" . $this->db->sqltext($this->equipment_filter_id);
		$this->db->query($sql);
	}

	/**
	 * insert or update equipment filter
	 * @return int 
	 */
	public function save() {

		if (!isset($this->equipment_filter_id)) {
			$equipment_filter_id = $this->addNewEquipmentFilter();
		} else {
			$equipment_filter_id = $this->updateEquipmentFilter();
		}
		return $equipment_filter_id;
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

	/**
	 *
	 * @param EquipmentFilterType $equipmentFilterType 
	 */
	public function setFilterType(EquipmentFilterType $equipmentFilterType) {
		$this->filterType = $equipmentFilterType;
	}

	/**
	 *
	 * @return EquipmentFilterType  
	 */
	public function getFilterType() {
		return $this->filterType;
	}

	private function _load() {

		if (!isset($this->equipment_filter_id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_EQUIPMENT_FILTER . "
				 WHERE equipment_filter_id=" . $this->db->sqltext($this->equipment_filter_id) .
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

	/**
	 * get equipment filter type list
	 * @return array|bool array of EquipmentFilterType or false on failure
	 */
	public function getFilterTypesList() {

		$filterTypes = array();

		$sql = "SELECT * FROM " . TB_EQUIPMENT_FILTER_TYPE;
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		foreach ($rows as $row) {
			$filterType = new EquipmentFilterType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($filterType, $key)) {
					$filterType->$key = $value;
				}
			}
			$filterTypes[] = $filterType;
		}
		return $filterTypes;
	}

}

?>