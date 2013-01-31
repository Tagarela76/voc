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

}

?>
