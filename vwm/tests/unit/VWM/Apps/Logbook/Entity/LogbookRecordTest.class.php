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
        $equipmentId = 0;
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
        $dateTime = time();
        $nextDate = $dateTime;
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
        $logbook->setEquipmentId($equipmentId);
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
        $logbook->setDateTime($dateTime);
        $logbook->setIsRecurring($isRecurring);
        $logbook->setPeriodicity($periodicity);
        $logbook->setParentId($parentId);
        $logbook->setNextDate($nextDate);
        $logbookId = $logbook->save();
        
        $query = "SELECT * FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($logbookId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        $this->assertEquals($result[0]['id'], $logbookId);
        $this->assertEquals($result[0]['facility_id'], $facilityId);
        $this->assertEquals($result[0]['inspection_sub_type'], $inspectionSubType);
        $this->assertEquals($result[0]['inspection_person_id'], $inspectionPersonId);
        $this->assertEquals($result[0]['date_time'], $dateTime);
        $this->assertEquals($result[0]['description_id'], $descriptionId);
        $this->assertEquals($result[0]['description_notes'], $logBookDescriptionNotes);
        $this->assertEquals($result[0]['sub_type_notes'], $subtypeNotes);
        $this->assertEquals($result[0]['qty'], $qty);
        $this->assertEquals($result[0]['gauge_type'], $gaugeType);
        $this->assertEquals($result[0]['gauge_value_from'], $gaugeValueFrom);
        $this->assertEquals($result[0]['gauge_value_to'], $gaugeValueTo);
        $this->assertEquals($result[0]['equipment_id'], $equipmentId);
        $this->assertEquals($result[0]['min_gauge_range'], $minGaugeRange);
        $this->assertEquals($result[0]['max_gauge_range'], $maxGaugeRange);
        $this->assertEquals($result[0]['inspection_addition_type'], $inspectionAdditionType);
        $this->assertEquals($result[0]['unittype_id'], $gaugeUnitTypeId);
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['is_recurring'], $isRecurring);
        $this->assertEquals($result[0]['periodicity'], $periodicity);
        $this->assertEquals($result[0]['parent_id'], $parentId);
        $this->assertEquals($result[0]['next_date'], $nextDate);
        
        //Update
        $facilityId = 2;
        $inspectionPersonId = 2;
        $inspectionTypeId = 2;
        $inspectionSubType = 'Subtype 5';
        $descriptionId = 2;
        $equipmentId = 1;
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
        $dateTime = time();
        $nextDate = $dateTime;
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
        $logbookUpdate->setEquipmentId($equipmentId);
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
        $logbookUpdate->setDateTime($dateTime);
        $logbookUpdate->setIsRecurring($isRecurring);
        $logbookUpdate->setPeriodicity($periodicity);
        $logbookUpdate->setParentId($parentId);
        $logbookUpdate->setNextDate($nextDate);
        
        $logbookUpdate->save();
        
        $query = "SELECT * FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($logbookUpdate->getId())} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        $this->assertEquals($result[0]['id'], $logbookId);
        $this->assertEquals($result[0]['facility_id'], $facilityId);
        $this->assertEquals($result[0]['inspection_sub_type'], $inspectionSubType);
        $this->assertEquals($result[0]['inspection_person_id'], $inspectionPersonId);
        $this->assertEquals($result[0]['date_time'], $dateTime);
        $this->assertEquals($result[0]['description_notes'], $logBookDescriptionNotes);
        $this->assertEquals($result[0]['sub_type_notes'], $subtypeNotes);
        $this->assertEquals($result[0]['qty'], $qty);
        $this->assertEquals($result[0]['gauge_type'], $gaugeType);
        $this->assertEquals($result[0]['gauge_value_from'], $gaugeValueFrom);
        $this->assertEquals($result[0]['gauge_value_to'], $gaugeValueTo);
        $this->assertEquals($result[0]['equipment_id'], $equipmentId);
        $this->assertEquals($result[0]['min_gauge_range'], $minGaugeRange);
        $this->assertEquals($result[0]['max_gauge_range'], $maxGaugeRange);
        $this->assertEquals($result[0]['inspection_addition_type'], $inspectionAdditionType);
        $this->assertEquals($result[0]['unittype_id'], $gaugeUnitTypeId);
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['is_recurring'], $isRecurring);
        $this->assertEquals($result[0]['periodicity'], $periodicity);
        $this->assertEquals($result[0]['parent_id'], $parentId);
        $this->assertEquals($result[0]['next_date'], $nextDate);
    }
    
    public function testConvertToLogbookRecordToDo()
    {
        $logbookId = 1;
        $logbook = new LogbookRecord();
        $logbook->setId($logbookId);
        $logbook->load();
        $logbookRecordToDo = $logbook->convertToLogbookRecordToDo();
        
        $this->assertTrue($logbookRecordToDo instanceof LogbookRecordToDo);
        
        $this->assertEquals($logbook->getId(), $logbookRecordToDo->getParentId());
        $this->assertEquals($logbook->getDescription(), $logbookRecordToDo->getDescription());
        $this->assertEquals($logbook->getDescriptionId(), $logbookRecordToDo->getDescriptionId());
        $this->assertEquals($logbook->getDescriptionNotes(), $logbookRecordToDo->getDescriptionNotes());
        $this->assertEquals($logbook->getFacilityId(), $logbookRecordToDo->getFacilityId());
        $this->assertEquals($logbook->getDateTime(), $logbookRecordToDo->getDateTime());
        $this->assertEquals($logbook->getEquipmentId(), $logbookRecordToDo->getEquipmentId());
        $this->assertEquals($logbook->getHasInspectionAdditionType(), $logbookRecordToDo->getHasInspectionAdditionType());
        $this->assertEquals($logbook->getHasQty(), $logbookRecordToDo->getHasQty());
        $this->assertEquals($logbook->getGaugeValueFrom(), $logbookRecordToDo->getGaugeValueFrom());
        $this->assertEquals($logbook->getGaugeValueTo(), $logbookRecordToDo->getGaugeValueTo());
        $this->assertEquals($logbook->getInspectionPersonId(), $logbookRecordToDo->getInspectionPersonId());
        $this->assertEquals($logbook->getPeriodicity(), $logbookRecordToDo->getPeriodicity());
        $this->assertEquals($logbook->getIsRecurring(), $logbookRecordToDo->getIsRecurring());
        $this->assertEquals($logbook->getLogbookUnitType(), $logbookRecordToDo->getLogbookUnitType());
        $this->assertEquals($logbook->getSubTypeNotes(), $logbookRecordToDo->getSubTypeNotes());
        $this->assertEquals($logbook->getQty(), $logbookRecordToDo->getQty());
        $this->assertEquals($logbook->getInspectionTypeId(), $logbookRecordToDo->getInspectionTypeId());
        $this->assertEquals($logbook->getInspectionSubType(), $logbookRecordToDo->getInspectionSubType());
        $this->assertEquals($logbook->getHasSubTypeNotes(), $logbookRecordToDo->getHasSubTypeNotes());
    }
    
    

}
?>
