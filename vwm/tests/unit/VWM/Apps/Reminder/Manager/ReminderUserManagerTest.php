<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\ReminderUser;
use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Apps\User\Entity\User;

class ReminderUserManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        ReminderUser::TABLE_NAME,
        ReminderUserManager::TABLE_NAME,
        Reminder::TABLE_NAME,
        User::TABLE_NAME
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
        $this->assertEquals(count($users), 4);
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

        $this->assertEquals(count($rows), 4);

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
        $this->assertEquals(count($reminderUsers), 4);
        
        $reminderUsers = $rUManager->getReminderUserListByFacilityId($facilityId, 'registered');
        
        $this->assertTrue($reminderUsers[0] instanceof ReminderUser);
        $this->assertEquals(count($reminderUsers), 2);
        
        $reminderUsers = $rUManager->getReminderUserListByFacilityId($facilityId, 'unregistered');
        
        $this->assertTrue($reminderUsers[0] instanceof ReminderUser);
        $this->assertEquals(count($reminderUsers), 2);
        
    }
    
    public function testGetReminderUserByUserId()
    {
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        $userId = 1;
        $reminderUser = $rUManager->getReminderUserByUserId($userId);
        
        $this->assertTrue($reminderUser instanceof ReminderUser);
        $this->assertEquals($reminderUser->getUserId(), $userId);
    }
    
   public function testGetUsersWithReminderByFacilityId()
    {
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        $db =  \VOCApp::getInstance()->getService('db');
        $facilityId = 1;
        
        $reminderUser = new ReminderUser();
        $reminderUser->setEmail('test@mail.ru');
        $reminderUser->setFacilityId($facilityId);
        $reminderUser->setUserId(0);
        $reminderUser->save();
        
        $sql = "SELECT * FROM " . ReminderUser::TABLE_NAME . " " .
                "WHERE facility_id={$db->sqltext($facilityId)}";
                
        $db->query($sql);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals(count($rows), 5);
        
        $reminderUsersList = $rUManager->getUsersWithReminderByFacilityId($facilityId);
        $this->assertEquals(count($reminderUsersList), 4);
    }
    
    public function testGetReminderListByReminderUserId()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        $reminderUserId = 1;
               
        $reminderList = $rUManager->getReminderListByReminderUserId($reminderUserId);
        
        $this->assertTrue($reminderList[0] instanceof \VWM\Apps\Reminder\Entity\Reminder);
        $this->assertEquals(count($reminderList), 1);
    }
    
    public function testGetUserByReminderUserId()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        
        $userId = 1;
        $user = $rUManager->getUserByReminderUserId($userId);
        
        $this->assertTrue($user instanceof \VWM\Apps\User\Entity\User);
    }

}
?>
