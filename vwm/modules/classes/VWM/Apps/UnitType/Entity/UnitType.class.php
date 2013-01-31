<?php

namespace VWM\Apps\UnitType\Entity;

use VWM\Framework\Model;

/**
 * Unit Type Entity Model
 */
class UnitType extends Model {

	protected $unittype_id;

	protected $name;

	protected $unittype_desc;

	protected $formula;

	protected $type_id;

	protected $system;

	protected $unit_class_id;

	/**
	 * @var Type
	 */
	protected $type;

	/**
	 * @var UnitClass
	 */
	protected $unitClass;

	const TABLE_NAME = '`unittype`';

	public function getUnitTypeId() {
		return $this->unittype_id;
	}

	public function setUnitTypeId($unittype_id) {
		$this->unittype_id = $unittype_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getUnitTypeDesc() {
		return $this->unittype_desc;
	}

	public function setUnitTypeDesc($unittype_desc) {
		$this->unittype_desc = $unittype_desc;
	}

	public function getFormula() {
		return $this->formula;
	}

	public function setFormula($formula) {
		$this->formula = $formula;
	}

	public function getTypeId() {
		return $this->type_id;
	}

	public function setTypeId($type_id) {
		$this->type_id = $type_id;
	}

	public function getSystem() {
		return $this->system;
	}

	public function setSystem($system) {
		$this->system = $system;
	}

	public function getUnitClassId() {
		return $this->unit_class_id;
	}

	public function setUnitClassId($unit_class_id) {
		$this->unit_class_id = $unit_class_id;
	}

	/**
	 * @return Type
	 */
	public function getType() {
		if($this->type) {
			return $this->type;
		}

		if(!$this->getTypeId()) {
			throw new \Exception("You cannot get type without type id");
		}

		$type = new Type($this->db);
		$type->setTypeId($this->getTypeId());
		if(!$type->load()) {
			return false;
		}
		$this->setType($type);

		return $this->type;
	}

	public function setType(Type $type) {
		$this->type = $type;
	}

	/**
	 *
	 * @return UnitClass
	 */
	public function getUnitClass() {

		if($this->unitClass) {
			return $this->unitClass;
		}

		if(!$this->getUnitClassId()) {
			throw new \Exception("You cannot get type without type id");
		}

		$unitClass = new UnitClass($this->db);
		$unitClass->setId($this->getUnitClassId());
		if(!$unitClass->load()) {
			return false;
		}
		$this->setUnitClass($unitClass);

		return $this->unitClass;
	}

	public function setUnitClass($unitClass) {
		$this->unitClass = $unitClass;
	}


	public function save() {
		throw new \Exception('You cannot add/edit this entity');
	}


}

?>
