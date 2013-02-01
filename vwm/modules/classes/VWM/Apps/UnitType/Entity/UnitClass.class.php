<?php

namespace VWM\Apps\UnitType\Entity;

use VWM\Framework\Model;

class UnitClass extends Model {

	protected $id;
	protected $name;
	protected $description;

	/**
	 * @var UnitType[]
	 */
	protected $unitTypes = array();

	const TABLE_NAME = '`unit_class`';

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function save() {
		throw new \Exception('You cannot add/edit this entity');
	}

	public function load() {
		if(!$this->getId()) {
			throw new \Exception("You cannot get type without unit class id");
		}

		$sql = "SELECT * " .
				"FROM ".self::TABLE_NAME. " uc " .
				"WHERE uc.id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);

		if($this->db->num_rows() == 0) {
			return false;
		}

		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		return true;
	}


	public function addUnitType(UnitType $unitType) {
		$this->unitTypes[] = $unitType;
	}

	public function getUnitTypes() {
		//	load from sql
		return $this->unitTypes;
	}

}

?>
