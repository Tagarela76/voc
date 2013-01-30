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


}

?>
