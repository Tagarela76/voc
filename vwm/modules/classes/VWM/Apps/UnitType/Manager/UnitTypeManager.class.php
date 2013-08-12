<?php

namespace VWM\Apps\UnitType\Manager;

use \VWM\Apps\UnitType\Entity\UnitType;
use \VWM\Apps\Reminder\Entity\Reminder;

class UnitTypeManager
{

    private $db;

    const TB_UNIT_TYPE = 'unittype';
    const TB_UNIT_CLASS = 'unit_class';
    const FUEL_UNIT_CLASS = 2;
    const TIME_UNIT_CLASS = 7;
    const TEMPERATURE_UNIT_CLASS = 9;
    const ACIDITY_UNIT_CLASS = 10;

    public function __construct(\db $db)
    {
        $this->db = $db;
    }

    /**
     *  get unit class list by unit type list
     * @param $unitTypeList[]
     */
    public function getUnitClassListByUnitTypeList($unitTypeList)
    {

        $classes = array();
        foreach ($unitTypeList as $unitType) {
            $uClass = $unitType->getUnitClass();
            if (!$classes[$uClass->getId()]) {
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
    public function getUnitTypeEx($unitTypeList)
    {
        $unitsTypeEx = array();
        foreach ($unitTypeList as $unitType) {
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
    public function getUnitTypeListByUnitClass($unitClass, $unitTypeList = null)
    {
        $unitTypes = array();

        if (is_null($unitClass)) {
            return false;
        }

        if (is_null($unitTypeList)) {
            $query = "SELECT u.unittype_id, u.name, u.unittype_desc, u.unit_class_id " .
                    "FROM " . self::TB_UNIT_TYPE . " u " .
                    "LEFT JOIN " . self::TB_UNIT_CLASS . " uc ON uc.id = u.unit_class_id " .
                    "WHERE uc.description = '{$this->db->sqltext($unitClass)}'";
            $this->db->query($query);
            $unitList = $this->db->fetch_all_array();
            foreach ($unitList as $unit) {
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

    /**
     * 
     * get unit type list by gauge id
     * 
     * @param int $gaugeTypeId
     * 
     * @return \VWM\Apps\UnitType\Entity\UnitType[]
     */
    public function getUnitTypeListBuGaugeId($gaugeTypeId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        switch ($gaugeTypeId) {
            case 0:
                $unitTypeList = $this->getUnitTypeListByUnitClassId(self::TEMPERATURE_UNIT_CLASS);
                break;
            case 2:
                $unitTypeList = $this->getUnitTypeListByUnitClassId(self::ACIDITY_UNIT_CLASS);
                break;
            case 6:
                $unitTypeList = $this->getUnitTypeListByUnitClassId(self::TIME_UNIT_CLASS);
                break;
            case 7:
                $unitTypeList = $this->getUnitTypeListByUnitClassId(self::FUEL_UNIT_CLASS);
                break;
        }
        return $unitTypeList;
    }

    /**
     * 
     * get unit type by reminder periodicity
     * 
     * @param int $periodicity
     * 
     * @return \VWM\Apps\UnitType\Entity\UnitType[]
     */
    public function getTimeUnitTypeListByPeriodicity($periodicity = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $unitTypes = array();
        $query = "SELECT * FROM " . self::TB_UNIT_TYPE . " " .
                "WHERE unit_class_id=" . self::TIME_UNIT_CLASS;
                
        if (!is_null($periodicity)) {
            $conditions = $this->getUnitTypesByPeriodicity($unitTypes, $periodicity);
            $conditions = implode(',', $conditions);
            $query.= " AND name IN({$conditions})";
        }
        
        $db->query($query);

        $rows = $db->fetch_all_array();
        
        foreach ($rows as $row) {
            $unitType = new UnitType($db);
            $unitType->initByArray($row);
            $unitTypes[] = $unitType;
        }

        return $unitTypes;
    }
    
    /**
     * 
     * get conditions
     * 
     * @param string $unitTypes[]
     * @param int $periodicity
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function getUnitTypesByPeriodicity($unitTypes, $periodicity = null)
    {
        if (is_null($periodicity)) {
            return;
        } else {
            switch ($periodicity) {
                case Reminder::DAILY:
                    if (!in_array("'days'", $unitTypes)) {
                        $unitTypes[] = "'days'";
                    }
                    $this->getUnitTypesByPeriodicity($unitTypes);
                    break;
                case Reminder::WEEKLY:
                    if (!in_array("'days'", $unitTypes)) {
                        $unitTypes[] = "'days'";
                    }
                    $unitTypes = $this->getUnitTypesByPeriodicity($unitTypes, Reminder::DAILY);
                    break;
                case Reminder::MONTHLY:
                    if (!in_array("'weeks'", $unitTypes)) {
                        $unitTypes[] = "'week'";
                    }
                    $unitTypes = $this->getUnitTypesByPeriodicity($unitTypes, Reminder::WEEKLY);
                    break;
                case Reminder::YEARLY:
                    if (!in_array("'months'", $unitTypes)) {
                        $unitTypes[] = "'month'";
                    }
                    $unitTypes = $this->getUnitTypesByPeriodicity($unitTypes, Reminder::MONTHLY);
                    break;
                case Reminder::EVERY2YEAR:
                    if (!in_array("'year'", $unitTypes)) {
                        $unitTypes[] = "'year'";
                    }
                    $unitTypes = $this->getUnitTypesByPeriodicity($unitTypes, Reminder::YEARLY);
                    break;
                case Reminder::EVERY3YEAR:
                    if (!in_array("'year'", $unitTypes)) {
                        $unitTypes[] = "'year'";
                    }
                    $unitTypes = $this->getUnitTypesByPeriodicity($unitTypes, Reminder::YEARLY);
                    break;
                default :
                    throw new \Exception('no such periodicity');
                    break;
            }
        }
        return $unitTypes;
    }

}
?>
