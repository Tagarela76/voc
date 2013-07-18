<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;
use \VWM\Apps\UnitType\Entity\UnitType;

class LogbookRecord extends Model
{

    /**
     *
     * logbook id
     *
     * @var int
     */
    protected $id;

    /**
     *
     * facility id
     *
     * @var int
     */
    protected $facility_id;

    /**
     * 
     * department id
     * 
     * @var int
     */
    protected $department_id = null;

    /**
     *
     * inspection person id
     *
     * @var int
     */
    protected $inspection_person_id;

    /**
     *
     * inspection type
     *
     * @var VWM\Apps\Logbook\Entity\LogbookInspectionType
     */
    protected $inspectionType;

    /**
     *
     * inspection type
     *
     * @var string
     */
    protected $inspection_type_id = null;

    /**
     *
     * inspection sub type
     *
     * @var string
     */
    protected $inspection_sub_type = null;

    /**
     *
     * logbook description
     *
     * @var string
     */
    protected $description_id;

    /**
     *
     *  time in unix type
     *
     * @var int
     */
    protected $date_time;

    /**
     *
     * equipmant id
     *
     * @var int
     */
    protected $equipment_id = 0;

    /* Addition fields */

    /**
     *
     * Addition field - inspection type permit
     *
     * @var boolean
     */
    protected $permit = null;

    /**
     *
     * Addition field - inspection sub type qty
     *
     * @var int
     */
    protected $qty = null;

    /**
     *
     * Addition field - loogbook description notes
     *
     * @var string
     */
    protected $description_notes = null;

    /**
     *
     * @var string
     */
    protected $sub_type_notes = null;
    //check for additio fields
    /**
     *
     * if logbook has Permit
     *
     * @var boolean
     */
    protected $hasPermit = 0;

    /**
     *
     * if logbook has qty
     *
     * @var boolean
     */
    protected $hasQty = 0;

    /**
     *
     * if logbook has Gauge
     *
     * @var boolean
     */
    protected $hasVolueGauge = 0;

    /**
     *
     * gauge type
     *
     * @var int
     */
    protected $gauge_type = null;

    /**
     *
     * gauge value from
     *
     * @var int
     */
    protected $gauge_value_from = 0;

    /**
     *
     * @var int
     */
    protected $gauge_value_to = 0;

    /**
     *
     * if logbook has description notes
     *
     * @var boolean
     */
    protected $hasDescriptionNotes = 0;

    /**
     *
     * if logbook has sub types notes
     *
     * @var boolean
     */
    protected $hasSubTypeNotes = 0;

    /**
     *
     * if logbook has addition types
     *
     * @var boolean
     */
    protected $hasInspectionAdditionType = 0;

    /**
     *
     * replaced bulbs
     *
     * @var boolean
     */
    protected $replaced_bulbs = 0;

    /**
     *
     * min limit of gauge
     *
     * @var int
     */
    protected $min_gauge_range = 0;

    /**
     *
     * gauge unit type
     * 
     * @var int
     */
    protected $unittype_id = null;

    /**
     *
     * gauge unit type
     * 
     * @var \VWM\Apps\UnitType\Entity\UnitType 
     */
    protected $logbookUnitType = null;

    /**
     *
     * logbook Description
     * 
     * @var VWM\Apps\Logbook\Entity\LogbookDescription 
     */
    protected $description = null;

    /**
     *
     * max limit of gauge
     *
     * @var int
     */
    protected $max_gauge_range = 100;
    protected $inspection_addition_type = null;

    /**
     *
     * is logbook record recursive
     * 
     * @var boolean 
     */
    protected $is_recurring = 0;

    /**
     *
     * periodicity of recurring logbooks
     * 
     * @var int 
     */
    protected $periodicity = 0;
    
    /**
     *
     * id of parent recurring logbook
     * 
     * @var int 
     */
    protected $parentId = 0;

    /* CONSTANTS */

    const TABLE_NAME = 'logbook_record';
    const FILENAME = '/modules/classes/VWM/Apps/Logbook/Resources/inspectionTypes.json';
    /* Type of Value Gauge */
    const TEMPERATURE_GAUGE = 0;
    const MANOMETER_GAUGE = 1;
    const CLARIFIER_GAUGE = 2;
    const GAS_GAUGE = 3;
    const ELECTRIC_GAUGE = 4;
    const PROPANE_GAS_GAUGE = 5;
    const TIME_GAUGE = 6;
    const FUEL_GAUGE = 7;
    const MIN_GAUGE_RANGE = 0;
    const MAX_GAUGE_RANGE = 100;
    const GAUGE_RANGE_STEP = 100;

    //reminder periodicity
    const DAILY = 0;
    const WEEKLY = 1;
    const MONTHLY = 2;
    const YEARLY = 3;

    public function __construct($id = null)
    {
        $this->modelName = "LogbookRecord";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getInspectionPersonId()
    {
        return $this->inspection_person_id;
    }

    public function setInspectionPersonId($inspection_person_id)
    {
        $this->inspection_person_id = $inspection_person_id;
    }

    public function getInspectionType()
    {
        if (!is_null($this->inspectionType)) {
            return $this->inspectionType;
        }

        if (is_null($this->inspection_type_id)) {
            return false;
        }

        $logbookInspectionType = new LogbookInspectionType();
        $logbookInspectionType->setId($this->inspection_type_id);
        $logbookInspectionType->load();
        $id = $logbookInspectionType->getId();
        $logbookInspectionType = json_decode($logbookInspectionType->settings);
        $logbookInspectionType->id = $id;
        return $logbookInspectionType;
    }

    public function setInspectionType($inspectionType)
    {
        $this->inspectionType = $inspectionType;
    }

    public function getInspectionSubType()
    {
        return $this->inspection_sub_type;
    }

    public function setInspectionSubType($inspection_sub_type)
    {
        $this->inspection_sub_type = $inspection_sub_type;
    }

    public function getDescriptionId()
    {
        return $this->description_id;
    }

    public function setDescriptionId($descriptionId)
    {
        $this->description_id = $descriptionId;
    }

    public function getDateTime()
    {
        return $this->date_time;
    }

    public function setDateTime($date_time)
    {
        $this->date_time = $date_time;
    }

    public function getEquipmentId()
    {
        return $this->equipment_id;
    }

    public function setEquipmentId($equipment_id)
    {
        $this->equipment_id = $equipment_id;
    }

    public function getPermit()
    {
        return $this->permit;
    }

    public function setPermit($permit)
    {
        $this->permit = $permit;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    public function getDescriptionNotes()
    {
        return $this->description_notes;
    }

    public function setDescriptionNotes($description_notes)
    {
        $this->description_notes = $description_notes;
    }

    public function getSubTypeNotes()
    {
        return $this->sub_type_notes;
    }

    public function setSubTypeNotes($sub_type_notes)
    {
        $this->sub_type_notes = $sub_type_notes;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }

    public function getDepartmentId()
    {
        return $this->department_id;
    }

    public function setDepartmentId($department_id)
    {
        $this->department_id = $department_id;
    }

    public function getHasPermit()
    {
        return $this->hasPermit;
    }

    public function setHasPermit($hasPermit)
    {
        $this->hasPermit = $hasPermit;
    }

    public function getHasQty()
    {
        return $this->hasQty;
    }

    public function setHasQty($hasQty)
    {
        $this->hasQty = $hasQty;
    }

    public function getHasDescriptionNotes()
    {
        return $this->hasDescriptionNotes;
    }

    public function setHasDescriptionNotes($hasDescriptionNotes)
    {
        $this->hasDescriptionNotes = $hasDescriptionNotes;
    }

    public function getHasSubTypeNotes()
    {
        return $this->hasSubTypeNotes;
    }

    public function setHasSubTypeNotes($hasSubTypeNotes)
    {
        $this->hasSubTypeNotes = $hasSubTypeNotes;
    }

    public function getHasVolueGauge()
    {
        return $this->hasVolueGauge;
    }

    public function setHasVolueGauge($hasVolueGauge)
    {
        $this->hasVolueGauge = $hasVolueGauge;
    }

    public function getHasInspectionAdditionType()
    {
        return $this->hasInspectionAdditionType;
    }

    public function setHasInspectionAdditionType($hasInspectionAdditionType)
    {
        $this->hasInspectionAdditionType = $hasInspectionAdditionType;
    }

    public function getValueGaugeType()
    {
        return $this->gauge_type;
    }

    public function setValueGaugeType($vulueGaugeType)
    {
        $this->gauge_type = $vulueGaugeType;
    }

    public function getGaugeValueFrom()
    {
        return $this->gauge_value_from;
    }

    public function setGaugeValueFrom($gauge_value_from)
    {
        $this->gauge_value_from = $gauge_value_from;
    }

    public function getGaugeValueTo()
    {
        return $this->gauge_value_to;
    }

    public function setGaugeValueTo($gauge_value_to)
    {
        $this->gauge_value_to = $gauge_value_to;
    }

    public function getReplacedBulbs()
    {
        return $this->replaced_bulbs;
    }

    public function setReplacedBulbs($replacedBulbs)
    {
        $this->replaced_bulbs = $replacedBulbs;
    }

    public function getMinGaugeRange()
    {
        return $this->min_gauge_range;
    }

    public function setMinGaugeRange($min_gauge_range)
    {
        $this->min_gauge_range = $min_gauge_range;
    }

    public function getMaxGaugeRange()
    {
        return $this->max_gauge_range;
    }

    public function setMaxGaugeRange($max_gauge_range)
    {
        $this->max_gauge_range = $max_gauge_range;
    }

    public function getInspectionAdditionType()
    {
        return $this->inspection_addition_type;
    }

    public function setInspectionAdditionType($inspection_addition_type)
    {
        $this->inspection_addition_type = $inspection_addition_type;
    }

    public function getUnittypeId()
    {
        return $this->unittype_id;
    }

    public function setUnittypeId($unittype_id)
    {
        $this->unittype_id = $unittype_id;
    }

    public function getLogbookUnitType()
    {
        if (is_null($this->getUnittypeId())) {
            return false;
        }
        //var_dump($this->getUnittypeId());
        if (is_null($this->logbookUnitType)) {
            $db = \VOCApp::getInstance()->getService('db');
            $unitType = new UnitType($db);
            $unitType->setUnitTypeId($this->getUnittypeId());
            $unitType->load();
            $this->setLogbookUnitType($unitType);
        }
        return $this->logbookUnitType;
    }

    public function setLogbookUnitType(\VWM\Apps\UnitType\Entity\UnitType $logbookUnitType)
    {
        $this->logbookUnitType = $logbookUnitType;
    }

    public function getInspectionTypeId()
    {
        return $this->inspection_type_id;
    }

    public function setInspectionTypeId($inspection_type_id)
    {
        $this->inspection_type_id = $inspection_type_id;
    }

    public function getDescription()
    {
        if (!is_null($this->description)) {
            return $this->description;
        }
        if (is_null($this->description_id)) {
            return false;
        }

        $description = new LogbookDescription();
        $description->setId($this->description_id);
        $description->load();
        $this->description = $description;
        return $description;
    }

    public function setDescription(VWM\Apps\Logbook\Entity\LogbookDescription $description)
    {
        $this->description = $description;
    }

    public function getIsRecurring()
    {
        return $this->is_recurring;
    }

    public function setIsRecurring($isRecurring)
    {
        $this->is_recurring = $isRecurring;
    }

    public function getPeriodicity()
    {
        return $this->periodicity;
    }

    public function setPeriodicity($periodicity)
    {
        $this->periodicity = $periodicity;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($this->getId())) {
            return false;
        }
        $sql = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->getId())} " .
                "LIMIT 1";
        $db->query($sql);

        if ($db->num_rows() == 0) {
            return false;
        }
        $rows = $db->fetch(0);
        $this->initByArray($rows);
        //initialize addition fields
        $this->getAvailableLogbookAdditionFields();
    }

    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $qty = $this->getQty();
        $departmentId = $this->getDepartmentId();
        $subTipesNotes = $this->getSubTypeNotes();
        $dateTime = $this->getDateTime();
        $descriptionNotes = $this->getDescriptionNotes();
        $gaugeType = $this->getValueGaugeType();
        $gaugeValueFrom = $this->getGaugeValueFrom();
        $gaugeValueTo = $this->getGaugeValueTo();
        $replacedBulbs = $this->getReplacedBulbs();
        $inspectionAdditionType = $this->getInspectionAdditionType();
        $unittypeId = $this->getUnittypeId();
        $inspectionSubType = $this->getInspectionSubType();

        if (is_null($qty)) {
            $qty = 'NULL';
        }
        if (is_null($departmentId)) {
            $departmentId = 'NULL';
        }
        if (is_null($dateTime)) {
            $dateTime = time();
        }
        if (is_null($subTipesNotes)) {
            $subTipesNotes = 'NONE';
        }
        if (is_null($descriptionNotes)) {
            $descriptionNotes = 'NONE';
        }
        if (is_null($gaugeType)) {
            $gaugeType = 'NULL';
        }
        if (is_null($gaugeValueFrom)) {
            $gaugeValueFrom = 'NULL';
        }
        if (is_null($gaugeValueTo)) {
            $gaugeValueTo = 'NULL';
        }
        if (is_null($replacedBulbs)) {
            $replacedBulbs = 'NULL';
        }
        if (is_null($inspectionAdditionType)) {
            $inspectionAdditionType = 'NULL';
        }
        if (is_null($unittypeId)) {
            $unittypeId = 'NULL';
        }
        if (is_null($inspectionSubType)) {
            $inspectionSubType = 'NULL';
        }

        //check gauge range
        $minGaugeRange = $this->getMinGaugeRange();
        $maxGaugeRange = $this->getMaxGaugeRange();

        //set nextGauge

        $sql = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "department_id = {$db->sqltext($departmentId)}, " .
                "inspection_sub_type = '{$db->sqltext($inspectionSubType)}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description_id = '{$db->sqltext($this->getDescriptionId())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "permit = {$db->sqltext($this->getPermit())}, " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "gauge_type = {$db->sqltext($gaugeType)}, " .
                "gauge_value_from = '{$db->sqltext($gaugeValueFrom)}', " .
                "gauge_value_to = '{$db->sqltext($gaugeValueTo)}', " .
                "equipment_id = '{$db->sqltext($this->getEquipmentId())}', " .
                "replaced_bulbs = {$db->sqltext($this->getReplacedBulbs())}, " .
                "min_gauge_range = {$db->sqltext($minGaugeRange)}, " .
                "max_gauge_range = {$db->sqltext($maxGaugeRange)}, " .
                "unittype_id = '{$db->sqltext($unittypeId)}', " .
                "inspection_addition_type = '{$db->sqltext($inspectionAdditionType)}', " .
                "inspection_type_id = '{$db->sqltext($this->getInspectionTypeId())}', " .
                "is_recurring = '{$db->sqltext($this->getIsRecurring())}', " .
                "periodicity = '{$db->sqltext($this->getPeriodicity())}', " .
                "parent_id = '{$db->sqltext($this->getParentId())}', " .
                "qty = '{$db->sqltext($qty)}'";

        $db->query($sql);
        $id = $db->getLastInsertedID();
        if (isset($id)) {
            // $this->updateGaugeRange();
        }
        $this->setId($id);

        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $qty = $this->getQty();
        $departmentId = $this->getDepartmentId();
        $subTipesNotes = $this->getSubTypeNotes();
        $dateTime = $this->getDateTime();
        $descriptionNotes = $this->getDescriptionNotes();
        $gaugeType = $this->getValueGaugeType();
        $gaugeValueFrom = $this->getGaugeValueFrom();
        $gaugeValueTo = $this->getGaugeValueTo();
        $inspectionAdditionType = $this->getInspectionAdditionType();
        $unittypeId = $this->getUnittypeId();
        $inspectionSubType = $this->getInspectionSubType();
        if (is_null($qty)) {
            $qty = 'NULL';
        }
        if (is_null($departmentId)) {
            $departmentId = 'NULL';
        }
        if (is_null($dateTime)) {
            $dateTime = time();
        }
        if (is_null($subTipesNotes)) {
            $subTipesNotes = 'NONE';
        }
        if (is_null($descriptionNotes)) {
            $descriptionNotes = 'NONE';
        }
        if (is_null($gaugeType)) {
            $gaugeType = 'NULL';
        }
        if (is_null($gaugeValueFrom)) {
            $gaugeValueFrom = 'NULL';
        }
        if (is_null($gaugeValueTo)) {
            $gaugeValueTo = 'NULL';
        }

        if (is_null($inspectionAdditionType)) {
            $inspectionAdditionType = 'NULL';
        }
        if (is_null($unittypeId)) {
            $unittypeId = 'NULL';
        }
        if (is_null($inspectionSubType)) {
            $inspectionSubType = 'NULL';
        }

        $sql = "UPDATE " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "department_id = {$db->sqltext($departmentId)}, " .
                "inspection_sub_type = '{$db->sqltext($inspectionSubType)}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description_id = '{$db->sqltext($this->getDescriptionId())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "permit = {$db->sqltext($this->getPermit())}, " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "gauge_type = {$db->sqltext($gaugeType)}, " .
                "gauge_value_from = {$db->sqltext($gaugeValueFrom)}, " .
                "gauge_value_to = {$db->sqltext($gaugeValueTo)}, " .
                "equipment_id = '{$db->sqltext($this->getEquipmentId())}', " .
                "replaced_bulbs = '{$db->sqltext($this->getReplacedBulbs())}', " .
                "min_gauge_range = {$db->sqltext($this->getMinGaugeRange())}, " .
                "max_gauge_range = {$db->sqltext($this->getMaxGaugeRange())}, " .
                "inspection_addition_type = '{$db->sqltext($inspectionAdditionType)}', " .
                "unittype_id = '{$db->sqltext($unittypeId)}', " .
                "inspection_type_id = '{$db->sqltext($this->getInspectionTypeId())}', " .
                "is_recurring = '{$db->sqltext($this->getIsRecurring())}', " .
                "periodicity = '{$db->sqltext($this->getPeriodicity())}', " .
                "parent_id = '{$db->sqltext($this->getParentId())}', " .
                "qty = '{$db->sqltext($qty)}' " .
                "WHERE id={$db->sqltext($this->getId())}";

        $db->query($sql);
        $id = $db->getLastInsertedID();
        if (isset($id)) {
            // $this->updateGaugeRange();
        }
        return $this->getId();
    }

    /*
     * redefine abstract method
     */

    public function getAttributes()
    {
        
    }

    /**
     * 
     * Getting addition fields of logbook from json file
     * 
     * @param VWM\Apps\Logbook\Entity\LogbookInspectionType $inspectionTypeName
     * @param string $inspectionSubTypeName
     * @param VWM\Apps\Logbook\Entity\LogbookDescription $inspectionDescription
     * 
     * @return boolean
     */
    public function getAvailableLogbookAdditionFields($inspectionType = null, $inspectionSubTypeName = null, $inspectionDescription = null)
    {
        if (is_null($inspectionType)) {
            $inspectionType = $this->getInspectionType();
        }
        if (is_null($inspectionSubTypeName)) {
            $inspectionSubTypeName = $this->getInspectionSubType();
        }
        if (is_null($inspectionDescription)) {
            $inspectionDescription = $this->getDescription();
        }

        $itmanager = \VOCApp::getInstance()->getService('inspectionType');

        $inspectionSubType = $itmanager->getInspectionSubTypeByTypeAndSubTypeDescription($inspectionType->typeName, $inspectionSubTypeName);

        /* set addition fields available */
        if (!is_null($inspectionType->additionFieldList)) {
            $this->setHasInspectionAdditionType(1);
        }
        $this->setHasPermit($inspectionType->permit);
        $this->setHasQty($inspectionSubType->qty);
        $this->setHasSubTypeNotes($inspectionSubType->notes);
        $this->setHasDescriptionNotes($inspectionDescription->getNotes());
        $this->setHasVolueGauge($inspectionSubType->valueGauge);

        return true;
    }

    /**
     * delete logbook
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "DELETE FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->getId())}";
        $db->query($query);
    }

    /**
     *  update logbook gauge range for facility
     */
    private function updateGaugeRange()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $gaugeType = $this->getValueGaugeType();
        $minGaugeRange = $this->getMinGaugeRange();
        $maxGaugeRange = $this->getMaxGaugeRange();

        //set min range for electric gauge 
        if ($gaugeType == self::ELECTRIC_GAUGE || $gaugeType == self::GAS_GAUGE || $gaugeType == self::PROPANE_GAS_GAUGE) {
            $minGaugeRange = $this->getGaugeValueTo();
            $maxGaugeRange = $minGaugeRange + self::MAX_GAUGE_RANGE;
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET " .
                "min_gauge_range = {$db->sqltext($minGaugeRange)}, " .
                "max_gauge_range = {$db->sqltext($maxGaugeRange)} " .
                "WHERE gauge_type = {$db->sqltext($gaugeType)} AND " .
                "facility_id = {$db->sqltext($this->getFacilityId())}";

        $db->query($query);
    }

}
?>