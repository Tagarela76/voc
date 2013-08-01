<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\ReminderUser;

class ReminderUserManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        ReminderUser::TABLE_NAME,
        ReminderUserManager::TABLE_NAME
    );

    public function testReminderUserManager()
    {
        $rManager = \VOCApp::getInstance()->getService('reminderUser');
        $this->assertTrue($rManager instanceof ReminderUserManager);
    }

    public function testSetReminder2ReminderUser()
    {
        $userId = 1;
        $reminderId = 2;

        $db = \VOCApp::getInstance()->getService('db');
        $rManager = \VOCApp::getInstance()->getService('reminderUser');
        $id = $rManager->setReminder2ReminderUser($userId, $reminderId);

        $sql = "SELECT * FROM " . ReminderUserManager::TABLE_NAME . " " .
                "WHERE id = {$db->sqltext($id)}";
        $db->query($sql);
        $rows = $db->fetch_all_array();

        $this->assertEquals($rows[0]['reminder_id'], $reminderId);
        $this->assertEquals($rows[0]['reminder_user_id'], $userId);
    }

    public function testGetReminderUsersByReminderId()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        $users = $rUManager->getReminderUsersByReminderId(1);
        $this->assertEquals(count($users), 2);
        $this->assertTrue($users[0] instanceof ReminderUser);
    }

    public function testUnSetReminder2ReminderUser()
    {
        $reminderId = 1;

        $db = \VOCApp::getInstance()->getService('db');
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');

        $sql = "SELECT * FROM " . ReminderUserManager::TABLE_NAME . " " .
                "WHERE reminder_id = {$db->sqltext($reminderId)}";
        $db->query($sql);
        $rows = $db->fetch_all_array();

        $this->assertEquals(count($rows), 2);

        $rUManager->unSetReminder2ReminderUser($reminderId);

        $db->query($sql);
        $rows = $db->fetch_all_array();

        $this->assertEquals(count($rows), 0);
    }
    
    public function testGetReminderUserListByFacilityId()
    {
        $facilityId = 1;

        $rUManager = \VOCApp::getInstance()->getService('reminderUser');

        $reminderUsers = $rUManager->getReminderUserListByFacilityId($facilityId);
        
        $this->assertTrue($reminderUsers[0] instanceof ReminderUser);
        $this->assertEquals(count($reminderUsers), 2);
        
    }

}
?>
1