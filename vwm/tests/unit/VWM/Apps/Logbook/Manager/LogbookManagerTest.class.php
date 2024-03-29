<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookPendingRecord;

class LogbookManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionPerson::TABLE_NAME,
        LogbookRecord::TABLE_NAME,
        LogbookPendingRecord::TABLE_NAME
        
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
        $nextDate = time();
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
            $logbook->setNextDate($nextDate);
            $logbook->setIsRecurring($isRecurring);
            $logbook->setPeriodicity($periodicity);
            $logbook->setParentId($parentId);

            $logbookId = $logbook->save();
        }

        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $currentRecurringLogbookList = $lbManager->getCurrentRecurringLogbookList();

        $this->assertEquals(count($currentRecurringLogbookList), $i);

        $this->assertTrue($currentRecurringLogbookList[0] instanceof LogbookRecord);

        foreach ($currentRecurringLogbookList as $currentRecurringLogbook) {
            $this->assertEquals($currentRecurringLogbook->getIsRecurring(), $isRecurring);
        }
    }

    public function testGetNextLogbookDate()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $currentDateString = '22/07/2013';
        //expected date 
        $dailyExpectedDate = '23/07/2013';
        $weeklyExpectedDate = '29/07/2013';
        $mothlyExpectedDate = '22/08/2013';
        $yearlyExpectedDate = '22/07/2014';
        
        $currentDateString = explode('/', $currentDateString);
        $unixCurrDate = mktime(0, 0, 0, $currentDateString[1], $currentDateString[0], $currentDateString[2]);
        
        //daily periodicity
        $date = $lbManager->getNextLogbookDate(LogbookRecord::DAILY, $unixCurrDate);
        $date = date('d/m/Y', $date);
        $this->assertEquals($date, $dailyExpectedDate);
        
        //weekly periodicity
        $date = $lbManager->getNextLogbookDate(LogbookRecord::WEEKLY, $unixCurrDate);
        $date = date('d/m/Y', $date);
        $this->assertEquals($date, $weeklyExpectedDate);
        
        //monthly periodicity
        $date = $lbManager->getNextLogbookDate(LogbookRecord::MONTHLY, $unixCurrDate);
        $date = date('d/m/Y', $date);
        $this->assertEquals($date, $mothlyExpectedDate);
        
       //yearly periodicity
        $date = $lbManager->getNextLogbookDate(LogbookRecord::YEARLY, $unixCurrDate);
        $date = date('d/m/Y', $date);
        $this->assertEquals($date, $yearlyExpectedDate);
        
    }
    
    public function testCalculateNextLogbookDate()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        
        $currentDateString = '1/04/2013';
        $currentDateString = explode('/', $currentDateString);
        $currentDateString = mktime(0, 0, 0, $currentDateString[1], $currentDateString[0], $currentDateString[2]);
        
        $expectedDate = date('d/m/Y', time());
        $expectedDate = explode('/', $expectedDate);
        $expectedDate = mktime(0, 0, 0, $expectedDate[1], $expectedDate[0], $expectedDate[2]);
        
        //check Daily recurring
        $nextLogbookDate = $lbManager->calculateNextLogbookDate(LogbookRecord::DAILY, $currentDateString);
        $this->assertTrue($expectedDate<$nextLogbookDate);
        
        //check weekly recurring
        $nextLogbookDate = $lbManager->calculateNextLogbookDate(LogbookRecord::WEEKLY, $currentDateString);
        $this->assertTrue($expectedDate<$nextLogbookDate);
        
        //check Daily recurring
        $nextLogbookDate = $lbManager->calculateNextLogbookDate(LogbookRecord::MONTHLY, $currentDateString);
        $this->assertTrue($expectedDate<$nextLogbookDate);
        
        //check Daily recurring
        $nextLogbookDate = $lbManager->calculateNextLogbookDate(LogbookRecord::YEARLY, $currentDateString);
        $this->assertTrue($expectedDate<$nextLogbookDate);
        
    }
    
    public function testGetLogbookPendingRecordListByFacilityId()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $facilityId = 1;

        $logbookPendingRecordList = $lbManager->getLogbookPendingRecordListByFacilityId($facilityId);

        $this->assertTrue($logbookPendingRecordList[0] instanceof LogbookPendingRecord);
        $this->assertEquals(count($logbookPendingRecordList), 2);

    }
    
    public function testDeleteAllLogbookPendingRecordByParentId()
    {
        $logbookId = 1;
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $db = \VOCApp::getInstance()->getService('db');
        $logbook = new LogbookRecord();
        $logbook->setId($logbookId);
        $logbook->load();
        $sql = "SELECT * FROM ".LogbookPendingRecord::TABLE_NAME." ".
               "WHERE parent_id = {$db->sqltext($logbook->getId())}";
        $db->query($sql);
        $this->assertFalse($db->num_rows() == 0);
        $lbManager->deleteAllLogbookPendingRecordByParentId($logbook->getId());
        $this->assertTrue($db->num_rows() == 0);
    }

}
?>