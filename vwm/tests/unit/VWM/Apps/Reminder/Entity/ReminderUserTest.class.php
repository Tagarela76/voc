<?php

namespace VWM\Apps\Reminder\Entity;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;

class ReminderUserTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        ReminderUser::TABLE_NAME
    );

    
    public function testReminder()
    {
        $reminderUserId = '1';
        $reminderUser = new ReminderUser();
        $reminderUser->setId($reminderUserId);
        $reminderUser->load();
        $this->assertTrue($reminderUser instanceof ReminderUser);
    }
    
    public function testSave()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminderUser = new ReminderUser();
        /*TEST INSERT*/
        $userId = 1;
        $email = 'tagarela@gmail.com';
        $facilityId = 1;
        
        $reminderUser->setUserId($userId);
        $reminderUser->setEmail($email);
        $reminderUser->setFacilityId($facilityId);
        $id = $reminderUser->save();
        
        $query = "SELECT * FROM ".ReminderUser::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($userId, $rows[0]['user_id']);
        $this->assertEquals($email, $rows[0]['email']);
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
        
        /*TEST UPDATE*/
        $userUpdateId = 2;
        $facilityId = 2;
        $emailUpdate = 'tagarela@mail.ru';
        
        $reminderUser->setUserId($userUpdateId);
        $reminderUser->setEmail($emailUpdate);
        $reminderUser->setFacilityId($facilityId);
        $reminderUser->save();
        
        $query = "SELECT * FROM ".ReminderUser::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($userUpdateId, $rows[0]['user_id']);
        $this->assertEquals($emailUpdate, $rows[0]['email']);
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
        
        /*TEST INSERT with out user*/
        $newReminderUser = new ReminderUser();
        $emailUpdate = 'newTagarela@mail.ru';
        $facilityId = 1;
        
        $newReminderUser->setEmail($emailUpdate);
        $newReminderUser->setFacilityId($facilityId);
        $id = $newReminderUser->save();
        
        $query = "SELECT * FROM ".ReminderUser::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertNull($rows[0]['user_id']);
        $this->assertEquals($emailUpdate, $rows[0]['email']);
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
    }

}
?>
