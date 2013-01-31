<?php

namespace VWM\Apps\UnitType;

use VWM\Apps\UnitType\Entity\UnitType;

/**
 * Unit Type Collection class
 */
class UnitTypeCollection {

	/**
	 * @var UnitType[]
	 */
	protected $unitTypes = array();

	protected $unitTypeClases = array();

	protected $unitTypeNames = array();

	public function getUnitTypes() {
		return $this->unitTypes;
	}

	public function setUnitTypes($unitTypes) {
		$this->unitTypes = $unitTypes;
	}

	public function getUnitTypeClases() {
		return $this->unitTypeClases;
	}

	public function setUnitTypeClases($unitTypeClases) {
		$this->unitTypeClases = $unitTypeClases;
	}

	public function getUnitTypeNames() {
		return $this->unitTypeNames;
	}

	public function setUnitTypeNames($unitTypeNames) {
		$this->unitTypeNames = $unitTypeNames;
	}

	public function getUnitTypeClassesByUnitTypeName($name){
		$unittype = array();
		$unitTypeClasses = $this->getUnitTypeClases();
		foreach($unitTypeClasses as $unitTypeClass){
			if($unitTypeClass->getName()==$name){
				$unittype[] = $unitTypeClass;
			}
		}
		return $unittype;

	}

	/**
	 * Ini collection by UnitType[]
	 * @param type $unitTypeList
	 */
	public function initByUnitTypeList($unitTypeList) {
		$this->setUnitTypes($unitTypeList);


		foreach($unitTypeList as $unitType) {
			// weight? volume?
			$type = array(
				'unittype_id' => $unitType->getUnitTypeId(),
				'type_id' => $unitType->getTypeId(),
				'name' => $unitType->getName()
			);
			$unitTypes[] = $type;
//r_dump($unitTypes);
			if(!in_array($unitType->getName(), $unitTypesName)){
				$unitTypesName[] = $unitType->getName();
			}
		}
		$unitTypeCollection->setUnitTypeClases($unittypes);
		$unitTypeCollection->setUnitTypes($unitTypeList);
		$unitTypeCollection->setUnitTypeNames($unitTypesName);
	}



}

?>
