<?php

namespace VWM\Apps\UnitType\Manager;

use \VWM\Apps\UnitType\Entity\UnitType;


class UnitTypeManager {

	private $db;
	
	const TB_UNIT_TYPE = 'unittype';
	const TB_UNIT_CLASS = 'unit_class';
    
    const TEMPERATURE_UNIT_CLASS = 9;

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
	 * get Unit Type List By Unit Type Class if unitTypeList not isset select from db
     * 
	 * @param string $unitClass
	 * @param array $unitTypeList[]
     * 
	 * return array
	 */
	public function getUnitTypeListByUnitClass($unitClass, $unitTypeList=null) {
        
		$unitTypes = array();
		
		if(is_null($unitClass)){
			return false;
		}
		
		if (is_null($unitTypeList)) {
			$query = "SELECT u.unittype_id, u.name, u.unittype_desc, u.unit_class_id ".
					 "FROM ".  self::TB_UNIT_TYPE ." u ".
					 "LEFT JOIN ". self::TB_UNIT_CLASS ." uc ON uc.id = u.unit_class_id ".
					 "WHERE uc.description = '{$this->db->sqltext($unitClass)}'";
			$this->db->query($query);
			$unitList = $this->db->fetch_all_array();
			foreach($unitList as $unit){
				$unitType = new \VWM\Apps\UnitType\Entity\UnitType($this->db);
				$unitType->initByArray($unit);
				$unitTypes[] = $unitType;
			}
		} else {

			foreach ($unitTypeList as $unitType) {
				if ($unitType->getUnitClass()->getDescription() == $unitClass) {
					$unitTypes[] = $unitType;
				}
			}
		}
		
		return $unitTypes;
	}
    
    /**
     * 
     * get UnitTypeList By Unit ClassId
     * 
     * @param int $unit_class_id
     * 
     * @return boolean|\VWM\Apps\UnitType\Entity\UnitType[]
     */
	public function getUnitTypeListByUnitClassId($unitClassId)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $unitTypes = array();

        if (is_null($unitClassId)) {
            return false;
        }

        $query = "SELECT * FROM " . self::TB_UNIT_TYPE . " " .
                "WHERE unit_class_id = '{$db->sqltext($unitClassId)}'";
        $this->db->query($query);
        $unitList = $this->db->fetch_all_array();
        foreach ($unitList as $unit) {
            $unitType = new UnitType($db);
            $unitType->initByArray($unit);
            $unitTypes[] = $unitType;
        }

        return $unitTypes;
    }
	
}

?>
