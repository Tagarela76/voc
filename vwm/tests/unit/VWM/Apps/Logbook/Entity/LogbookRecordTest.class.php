<?php

namespace VWM\Apps\Logbook\Entity;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookRecord;

class LogbookRecordTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookRecord::TABLE_NAME
    );

    public function testSave()
    {
        $db = \VOCApp::getInstance()->getService('db');

        //data
        $facilityId = 1;
        $inspectionPersonId = 1;
        $inspectionTypeId = 1;
        $inspectionSubType = 'Subtype 4';
        $descriptionId = 1;
        $permit = 1;
        $equipmentId = 0;
        $departmentId = NULL;
        $minGaugeRange = 0;
        $maxGaugeRange = 100;
        $inspectionAdditionType = 'gauge';
        $qty = 4;
        $logBookDescriptionNotes = 'Description Notes 4';
        $subtypeNotes = 'Sub Type Notes 4';
        $gaugeUnitTypeId = 49;
        $gaugeType = NULL;
        $gaugeValueFrom = 1;
        $gaugeValueTo = 15;
        $replacedBulbs = 1;
        $dateTime = time();
        $isRecurring = 0;
        $periodicity = LogbookRecord::DAILY;
        $parentId = 0;
            
        //initialize logbook
        $logbook = new LogbookRecord();
        $logbook->setFacilityId($facilityId);
        $logbook->setInspectionPersonId($inspectionPersonId);
        $logbook->setInspectionTypeId($inspectionTypeId);
        $logbook->setInspectionSubType($inspectionSubType);
        $logbook->setDescriptionId($descriptionId);
        $logbook->setPermit($permit);
        $logbook->setEquipmentId($equipmentId);
        $logbook->setDepartmentId($departmentId);
        $logbook->setMinGaugeRange($minGaugeRange);
        $logbook->setMaxGaugeRange($maxGaugeRange);
        //set addition fields
        $logbook->setInspectionAdditionType($inspectionAdditionType);
        $logbook->setQty($qty);
        $logbook->setDescriptionNotes($logBookDescriptionNotes);
        $logbook->setSubTypeNotes($subtypeNotes);
        $logbook->setUnittypeId($gaugeUnitTypeId);
        $logbook->setValueGaugeType($gaugeType);
        $logbook->setGaugeValueFrom($gaugeValueFrom);
        $logbook->setGaugeValueTo($gaugeValueTo);
        $logbook->setReplacedBulbs($replacedBulbs);
        $logbook->setDateTime($dateTime);
        $logbook->setIsRecurring($isRecurring);
        $logbook->setPeriodicity($periodicity);
        $logbook->setParentId($parentId);
        
        $logbookId = $logbook->save();
        
        $query = "SELECT * FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($logbookId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        $this->assertEquals($result[0]['id'], $logbookId);
        $this->assertEquals($result[0]['facility_id'], $facilityId);
        $this->assertEquals($result[0]['department_id'], $departmentId);
        $this->assertEquals($result[0]['inspection_sub_type'], $inspectionSubType);
        $this->assertEquals($result[0]['inspection_person_id'], $inspectionPersonId);
        $this->assertEquals($result[0]['date_time'], $dateTime);
        $this->assertEquals($result[0]['description_id'], $descriptionId);
        $this->assertEquals($result[0]['description_notes'], $logBookDescriptionNotes);
        $this->assertEquals($result[0]['permit'], $permit);
        $this->assertEquals($result[0]['sub_type_notes'], $subtypeNotes);
        $this->assertEquals($result[0]['qty'], $qty);
        $this->assertEquals($result[0]['gauge_type'], $gaugeType);
        $this->assertEquals($result[0]['gauge_value_from'], $gaugeValueFrom);
        $this->assertEquals($result[0]['gauge_value_to'], $gaugeValueTo);
        $this->assertEquals($result[0]['equipment_id'], $equipmentId);
        $this->assertEquals($result[0]['replaced_bulbs'], $replacedBulbs);
        $this->assertEquals($result[0]['min_gauge_range'], $minGaugeRange);
        $this->assertEquals($result[0]['max_gauge_range'], $maxGaugeRange);
        $this->assertEquals($result[0]['inspection_addition_type'], $inspectionAdditionType);
        $this->assertEquals($result[0]['unittype_id'], $gaugeUnitTypeId);
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['is_recurring'], $isRecurring);
        $this->assertEquals($result[0]['periodicity'], $periodicity);
        $this->assertEquals($result[0]['parent_id'], $parentId);
        
        //Update
        $facilityId = 2;
        $inspectionPersonId = 2;
        $inspectionTypeId = 2;
        $inspectionSubType = 'Subtype 5';
        $descriptionId = 2;
        $permit = 0;
        $equipmentId = 1;
        $departmentId = 1;
        $minGaugeRange = 10;
        $maxGaugeRange = 300;
        $inspectionAdditionType = 'TEMPERATURE';
        $qty = 40;
        $logBookDescriptionNotes = 'Description Notes 5';
        $subtypeNotes = 'Sub Type Notes 5';
        $gaugeUnitTypeId = 49;
        $gaugeType = 1;
        $gaugeValueFrom = 10;
        $gaugeValueTo = 150;
        $replacedBulbs = 0;
        $dateTime = time();
        $isRecurring = 1;
        $periodicity = LogbookRecord::WEEKLY;
        $parentId = 1;
        //initialize logbook
        $logbookUpdate = new LogbookRecord();
        $logbookUpdate->setId($logbookId);
        $logbookUpdate->load();
        
        $logbookUpdate->setFacilityId($facilityId);
        $logbookUpdate->setInspectionPersonId($inspectionPersonId);
        $logbookUpdate->setInspectionTypeId($inspectionTypeId);
        $logbookUpdate->setInspectionSubType($inspectionSubType);
        $logbookUpdate->setDescriptionId($descriptionId);
        $logbookUpdate->setPermit($permit);
        $logbookUpdate->setEquipmentId($equipmentId);
        $logbookUpdate->setDepartmentId($departmentId);
        $logbookUpdate->setMinGaugeRange($minGaugeRange);
        $logbookUpdate->setMaxGaugeRange($maxGaugeRange);
        //set addition fields
        $logbookUpdate->setInspectionAdditionType($inspectionAdditionType);
        $logbookUpdate->setQty($qty);
        $logbookUpdate->setDescriptionNotes($logBookDescriptionNotes);
        $logbookUpdate->setSubTypeNotes($subtypeNotes);
        $logbookUpdate->setUnittypeId($gaugeUnitTypeId);
        $logbookUpdate->setValueGaugeType($gaugeType);
        $logbookUpdate->setGaugeValueFrom($gaugeValueFrom);
        $logbookUpdate->setGaugeValueTo($gaugeValueTo);
        $logbookUpdate->setReplacedBulbs($replacedBulbs);
        $logbookUpdate->setDateTime($dateTime);
        $logbookUpdate->setIsRecurring($isRecurring);
        $logbookUpdate->setPeriodicity($periodicity);
        $logbookUpdate->setParentId($parentId);
        
        $logbookUpdate->save();
        
        $query = "SELECT * FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($logbookUpdate->getId())} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        $this->assertEquals($result[0]['id'], $logbookId);
        $this->assertEquals($result[0]['facility_id'], $facilityId);
        $this->assertEquals($result[0]['department_id'], $departmentId);
        $this->assertEquals($result[0]['inspection_sub_type'], $inspectionSubType);
        $this->assertEquals($result[0]['inspection_person_id'], $inspectionPersonId);
        $this->assertEquals($result[0]['date_time'], $dateTime);
        $this->assertEquals($result[0]['description_id'], $descriptionId);
        $this->assertEquals($result[0]['description_notes'], $logBookDescriptionNotes);
        $this->assertEquals($result[0]['permit'], $permit);
        $this->assertEquals($result[0]['sub_type_notes'], $subtypeNotes);
        $this->assertEquals($result[0]['qty'], $qty);
        $this->assertEquals($result[0]['gauge_type'], $gaugeType);
        $this->assertEquals($result[0]['gauge_value_from'], $gaugeValueFrom);
        $this->assertEquals($result[0]['gauge_value_to'], $gaugeValueTo);
        $this->assertEquals($result[0]['equipment_id'], $equipmentId);
        $this->assertEquals($result[0]['replaced_bulbs'], $replacedBulbs);
        $this->assertEquals($result[0]['min_gauge_range'], $minGaugeRange);
        $this->assertEquals($result[0]['max_gauge_range'], $maxGaugeRange);
        $this->assertEquals($result[0]['inspection_addition_type'], $inspectionAdditionType);
        $this->assertEquals($result[0]['unittype_id'], $gaugeUnitTypeId);
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['is_recurring'], $isRecurring);
        $this->assertEquals($result[0]['periodicity'], $periodicity);
        $this->assertEquals($result[0]['parent_id'], $parentId);
    }

}
?>
