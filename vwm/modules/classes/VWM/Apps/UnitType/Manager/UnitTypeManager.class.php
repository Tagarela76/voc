<?php

namespace VWM\Apps\UnitType\Manager;


class UnitTypeManager {

	private $db;

	public function __construct(\db $db) {
		$this->db = $db;
	}

	/**
	 *  get unit class list by unit type list
	 * @param $unitTypeList[]
	 */
	public function getUnitClassListByUnitTypeList($unitTypeList) {

		$classes = array();
		foreach ($unitTypeList as $unitType) {
			$uClass = $unitType->getUnitClass();
			if(!$classes[$uClass->getId()]) {
				$classes[$uClass->getId()] = $uClass;
			}

			$classes[$uClass->getId()]->addUnitType($unitType);

		}

		$output = array();
		foreach ($classes as $class) {
			$output[] = $class;
		}
		return $output;

	}
	
	/**
	 *  get UnitTypeEx by unit type list
	 * @param $unitTypeList[]
	 */
	public function getUnitTypeEx($unitTypeList){
		$unitsTypeEx = array();
		foreach ($unitTypeList as $unitType){
			$unitTypeEx = array(
			'unittype_id' => $unitType->getUnitClassId(),
			'type_id' => $unitType->getTypeId(),
			'name' => $unitType->getName(),
			);
			$unitsTypeEx[] = $unitTypeEx;
		}
		return $unitsTypeEx;
	}
	
	/**
	 * get Unit Type List By Unit Type Class
	 * @param string $unitClass
	 * @param array $unitTypeList[]
	 * return array
	 */
	public function getUnitTypeListByUnitClass($unitClass, $unitTypeList) {
		$unitTypes = array();
		foreach ($unitTypeList as $unitType){
			if($unitType->getUnitClass()->getDescription() == $unitClass){
				$unitTypes[] = $unitType;
			}
		}
		return $unitTypes;
	}

}

?>
