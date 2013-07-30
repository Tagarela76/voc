<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Company;
use VWM\Apps\UnitType\Manager\UnitTypeManager;

class ReminderManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        Company::TABLE_NAME, Facility::TABLE_NAME, TB_USER, Reminder::TABLE_NAME,
        Reminder::TB_REMIND2USER, UnitTypeManager::TB_UNIT_TYPE
    );

    public function testReminder()
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        $this->assertTrue($rManager instanceof ReminderManager);
    }

    public function testSetRemind2User()
    {
        // get reminder with id = 1
        $reminderId = '1';
        $userId = '4';
        $rManager = \VOCApp::getInstance()->getService('reminder');
        
        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 2);

        $rManager->setRemind2User($userId, $reminderId);
        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 3);
    }

    public function testgetUsersByReminderId()
    {
        $reminderId = '1';
        $rManager = \VOCApp::getInstance()->getService('reminder');
        $users = $rManager->getUsersByReminderId($reminderId);

        $this->assertTrue(is_array($users));
        $this->assertTrue(count($users) == 2);
    }

    public function testUnSetRemind2User()
    {
        $reminderId = '1';
        $rManager = \VOCApp::getInstance()->getService('reminder');
        $users = $rManager->getUsersByReminderId($reminderId);

        $this->assertTrue(count($users) == 2);

        $rManager->unSetRemind2User($reminderId);
        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 0);
    }

    public function testGetReminders()
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        $reminders = $rManager->getReminders();
        $this->assertTrue(count($reminders) == 3);
        $this->assertTrue($reminders[0] instanceof Reminder);
    }

    public function testGetNextRemindDate()
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        //daily
        $periodicity = 0;
        $date = '01/01/2013';
        $date = explode('/', $date);
        $unitDate = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $nextDate = $rManager->getNextRemindDate($periodicity, $unitDate);
        $nextDate = (date('d/m/Y', $nextDate));
        $this->assertEquals($nextDate, '02/01/2013');
    }
    
    public function testCalculateTimeByNumberAndUnitType()
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        /*26.12.20013*/
        $time = mktime(0, 0, 0, 12, 26, 2013);
        //days
        $number = 12;
        $unitTypeId = 40;
        $beforeReminderDate = $rManager->calculateTimeByNumberAndUnitType($time, $number, $unitTypeId);
        $expectedDate = mktime(0, 0, 0, 12, 14, 2013);
        $this->assertEquals($beforeReminderDate, $expectedDate);
        //weeks
        $number = 2;
        $unitTypeId = 44;
        $beforeReminderDate = $rManager->calculateTimeByNumberAndUnitType($time, $number, $unitTypeId);
        $expectedDate = mktime(0, 0, 0, 12, 12, 2013);
        $this->assertEquals($beforeReminderDate, $expectedDate);
        //months
        $number = 3;
        $unitTypeId = 45;
        $beforeReminderDate = $rManager->calculateTimeByNumberAndUnitType($time, $number, $unitTypeId);
        $expectedDate = mktime(0, 0, 0, 9, 26, 2013);
        $this->assertEquals($beforeReminderDate, $expectedDate);
        //years
        $number = 13;
        $unitTypeId = 46;
        $beforeReminderDate = $rManager->calculateTimeByNumberAndUnitType($time, $number, $unitTypeId);
        $expectedDate = mktime(0, 0, 0, 12, 26, 2000);
        $this->assertEquals($beforeReminderDate, $expectedDate);
        
    }

}
