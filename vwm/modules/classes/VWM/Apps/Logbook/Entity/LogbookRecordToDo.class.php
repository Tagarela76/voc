<?php
namespace VWM\Apps\Logbook\Entity;

use VWM\Apps\Logbook\Entity\LogbookRecord;

class LogbookRecordToDo extends LogbookRecord
{
    const TABLE_NAME = 'logbook_record_to_do';
    
    public function __construct($id = null)
    {
        parent::__construct($id);
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
        // The Logbook Record To Do always has a field of is_recurring 0
        $this->setIsRecurring(0);
        
        //check gauge range
        $minGaugeRange = $this->getMinGaugeRange();
        $maxGaugeRange = $this->getMaxGaugeRange();

        //set nextGauge

        $sql = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "inspection_sub_type = '{$db->sqltext($inspectionSubType)}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description_id = '{$db->sqltext($this->getDescriptionId())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "gauge_type = {$db->sqltext($gaugeType)}, " .
                "gauge_value_from = '{$db->sqltext($gaugeValueFrom)}', " .
                "gauge_value_to = '{$db->sqltext($gaugeValueTo)}', " .
                "equipment_id = '{$db->sqltext($this->getEquipmentId())}', " .
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
       
        $this->setId($id);

        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $qty = $this->getQty();
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
                "inspection_sub_type = '{$db->sqltext($inspectionSubType)}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description_id = '{$db->sqltext($this->getDescriptionId())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "gauge_type = {$db->sqltext($gaugeType)}, " .
                "gauge_value_from = {$db->sqltext($gaugeValueFrom)}, " .
                "gauge_value_to = {$db->sqltext($gaugeValueTo)}, " .
                "equipment_id = '{$db->sqltext($this->getEquipmentId())}', " .
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
        
        return $this->getId();
    }
    
    
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM ".self::TABLE_NAME." ".
               "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($sql);   
    }
}
?>
