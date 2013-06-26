<?php

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;
use \VWM\Hierarchy\Facility;

class ReminderManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        //TB_FACILITY, TB_USER, TB_REMINDER, TB_REMIND2USER
        Facility::TABLE_NAME, TB_USER, Reminder::TABLE_NAME, Reminder::TB_REMIND2USER
    );

    public function testReminder()
    {
        $rManager = VOCApp::getInstance()->getService('reminder');
        $this->assertTrue($rManager instanceof VWM\Apps\Reminder\Manager\ReminderManager);
    }

    public function testSetRemind2User()
    {

        // get reminder with id = 1
        $reminderId = '1';
        $userId = '4';
        $rManager = VOCApp::getInstance()->getService('reminder');
        $rManager = new VWM\Apps\Reminder\Manager\ReminderManager();

        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 2);

        $rManager->setRemind2User($userId, $reminderId);
        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 3);
    }

    public function testgetUsersByReminderId()
    {
        $reminderId = '1';
        $rManager = VOCApp::getInstance()->getService('reminder');
        $users = $rManager->getUsersByReminderId($reminderId);

        $this->assertTrue(is_array($users));
        $this->assertTrue(count($users) == 2);
    }

    public function testUnSetRemind2User()
    {
        $reminderId = '1';
        $rManager = VOCApp::getInstance()->getService('reminder');
        $users = $rManager->getUsersByReminderId($reminderId);

        $this->assertTrue(count($users) == 2);

        $rManager->unSetRemind2User($reminderId);
        $users = $rManager->getUsersByReminderId($reminderId);
        $this->assertTrue(count($users) == 0);
    }

    public function testGetReminders()
    {
        $rManager = VOCApp::getInstance()->getService('reminder');
        $reminders = $rManager->getReminders();
        $this->assertTrue(count($reminders) == 3);
        $this->assertTrue($reminders[0] instanceof Reminder);
    }

}