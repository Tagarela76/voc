<?php

class EquipmentLighting {

	/**
	 *
	 * @var int 
	 */
	public $equipment_lighting_id;

	/**
	 *
	 * @var string
	 */
	public $name;

	/**
	 *
	 * @var int 
	 */
	public $bulb_type;

	/**
	 *
	 * @var int 
	 */
	public $equipment_id;

	/**
	 *
	 * @var string 
	 */
	public $size;

	/**
	 *
	 * @var string 
	 */
	public $voltage;

	/**
	 *
	 * @var int 
	 */
	public $wattage;

	/**
	 *
	 * @var int 
	 */
	public $color;

	/**
	 * db connection
	 * @var db 
	 */
	private $db;

	/**
	 *
	 * @var EquipmentLightingBulbType 
	 */
	private $bulbTypeObject;

	/**
	 *
	 * @var EquipmentLightingColor 
	 */
	private $colorObject;

	function __construct(db $db, $equipment_lighting_id = null) {
		$this->db = $db;

		if (isset($equipment_lighting_id)) {
			$this->equipment_lighting_id = $equipment_lighting_id;
			$this->_load();
		}
	}

	/**
	 * update equipment lighting 
	 */
	public function updateEquipmentLighting() {

		$query = "UPDATE " . TB_EQUIPMENT_LIGHTING . "
				  set name= '" . $this->db->sqltext($this->name) . "', 
						equipment_id= " . $this->db->sqltext($this->equipment_id) . ", 		 	 	 	 	 	 	
						bulb_type= " . $this->db->sqltext($this->bulb_type) . ", 		 	 	 	 	 	 	
						size= '" . $this->db->sqltext($this->size) . "', 		 	 	 	 	 	 	
						voltage= '" . $this->db->sqltext($this->voltage) . "', 		 	 	 	 	 	 	
						wattage= " . $this->db->sqltext($this->wattage) . ", 		 	 	 	 	 	 	
						color= " . $this->db->sqltext($this->color) . "
				  WHERE equipment_lighting_id= " . $this->db->sqltext($this->equipment_lighting_id);
		$this->db->query($query);
		return $this->equipment_lighting_id;
	}

	/**
	 * add new equipment lifhting 
	 */
	public function addNewEquipmentLighting() {

		$query = "INSERT INTO " . TB_EQUIPMENT_LIGHTING . "(name, equipment_id, bulb_type, size, voltage, wattage, color)
					VALUES ( 
					'" . $this->db->sqltext($this->name) . "'
					, " . $this->db->sqltext($this->equipment_id) . "	
					, " . $this->db->sqltext($this->bulb_type) . "
					, '" . $this->db->sqltext($this->size) . "'
					, '" . $this->db->sqltext($this->voltage) . "'
					, " . $this->db->sqltext($this->wattage) . "	
					, " . $this->db->sqltext($this->color) . ")";
		$this->db->query($query);
		$equipment_lighting_id = $this->db->getLastInsertedID();
		$this->equipment_lighting_id = $equipment_lighting_id;
		return $equipment_lighting_id;
	}

	/**
	 * update or unsert equipment lighting
	 * @return int 
	 */
	public function save() {

		if (!isset($this->equipment_lighting_id)) {
			$equipment_lighting_id = $this->addNewEquipmentLighting();
		} else {
			$equipment_lighting_id = $this->updateEquipmentLighting();
		}
		return $equipment_lighting_id;
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

		if (!isset($this->equipment_lighting_id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_EQUIPMENT_LIGHTING . "
				 WHERE equipment_lighting_id=" . $this->db->sqltext($this->equipment_lighting_id) .
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
	 * get equipment lighting bulb type list
	 * @return array|bool array of EquipmentLightingBulbType or false on failure
	 */
	public function getLightingBulbTypeList() {

		$lightingBulbTypes = array();

		$sql = "SELECT * FROM " . TB_EQUIPMENT_LIGHTING_BULB_TYPE;
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		foreach ($rows as $row) {
			$lightingBulbType = new EquipmentLightingBulbType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($lightingBulbType, $key)) {
					$lightingBulbType->$key = $value;
				}
			}
			$lightingBulbTypes[] = $lightingBulbType;
		}
		return $lightingBulbTypes;
	}

	/**
	 * get equipment lighting color list
	 * @return array|bool array of EquipmentLightingColor or false on failure
	 */
	public function getLightingColorList() {

		$lightingColors = array();

		$sql = "SELECT * FROM " . TB_EQUIPMENT_LIGHTING_COLOR;
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		foreach ($rows as $row) {
			$lightingColor = new EquipmentLightingColor($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($lightingColor, $key)) {
					$lightingColor->$key = $value;
				}
			}
			$lightingColors[] = $lightingColor;
		}
		return $lightingColors;
	}

	/**
	 *
	 * @param EquipmentLightingBulbType $equipmentLightingBulbType 
	 */
	public function setBulbType(EquipmentLightingBulbType $equipmentLightingBulbType) {
		$this->bulbTypeObject = $equipmentLightingBulbType;
	}

	/**
	 *
	 * @return EquipmentLightingBulbType  
	 */
	public function getBulbType() {
		return $this->bulbTypeObject;
	}

	/**
	 *
	 * @param EquipmentLightingColor $equipmentLightingColor 
	 */
	public function setColor(EquipmentLightingColor $equipmentLightingColor) {
		$this->colorObject = $equipmentLightingColor;
	}

	/**
	 *
	 * @return EquipmentLightingColor  
	 */
	public function getColor() {
		return $this->colorObject;
	}

	/**
	 *
	 * delete equipment lighting
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_EQUIPMENT_LIGHTING . "
				 WHERE equipment_lighting_id=" . $this->db->sqltext($this->equipment_lighting_id);
		$this->db->query($sql);
	}

}

?>