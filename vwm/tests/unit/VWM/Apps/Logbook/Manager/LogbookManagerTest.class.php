<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookRecordToDo;

class LogbookManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionPerson::TABLE_NAME,
        LogbookRecord::TABLE_NAME
    );

    public function testGetLogbookInspectionPersonListByFacilityId()
    {

        $facilityId = 1;
        $manager = new LogbookManager();
        $inspectionPersonList = $manager->getLogbookInspectionPersonListByFacilityId($facilityId);
        $this->assertEquals(count($inspectionPersonList), 3);

        $this->assertEquals($inspectionPersonList[0]->getName(), 'Tagarela');
        $this->assertEquals($inspectionPersonList[1]->getName(), 'Mika');
        $this->assertEquals($inspectionPersonList[2]->getName(), 'Mulan');
    }

    public function testGetRecurringLogbookList()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $facilityId = 2;

        $recurringLogbookList = $lbManager->getRecurringLogbookList($facilityId);

        $this->assertTrue($recurringLogbookList[0] instanceof LogbookRecord);
        $this->assertEquals(count($recurringLogbookList), 1);

        $recurringLogbookList = $lbManager->getRecurringLogbookList();

        $this->assertTrue($recurringLogbookList[0] instanceof LogbookRecord);
        $this->assertEquals(count($recurringLogbookList), 2);
    }

    public function testGetCountRecurringLogbookList()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $facilityId = 2;
        $count = $lbManager->getCountRecurringLogbookList($facilityId);
        $this->assertEquals($count, 1);

        $count = $lbManager->getCountRecurringLogbookList();
        $this->assertEquals($count, 2);
    }

    public function testGetCurrentRecurringLogbookList()
    {
        //data
        $facilityId = 1;
        $inspectionPersonId = 1;
        $inspectionTypeId = 1;
        $inspectionSubType = 'subtype 4';
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
        $isRecurring = 1;
        $periodicity = LogbookRecord::DAILY;
        $parentId = 0;

        //create current reccuring logbooks
        for ($i = 0; $i < 3; $i++) {
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
            
            $logbookId = $logbook->save();
        }
        
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $currentRecurringLogbookList = $lbManager->getCurrentRecurringLogbookList();
        
        $this->assertEquals(count($currentRecurringLogbookList), $i);
        
        $this->assertTrue($currentRecurringLogbookList[0] instanceof LogbookRecord);
        
        foreach($currentRecurringLogbookList as $currentRecurringLogbook)
        {
            $this->assertEquals($currentRecurringLogbook->getIsRecurring(), $isRecurring);
        }
    }

}
?>