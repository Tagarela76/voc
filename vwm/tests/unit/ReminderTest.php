<?php

use VWM\Framework\Test as Testing;

class ReminderTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY, TB_USER, TB_REMINDER, TB_REMIND2USER
	);

	public function testReminder() {
		$reminder = new Reminder($this->db, '1');
		$this->assertTrue($reminder instanceof Reminder);

	}
	
	public function testAddReminder() {
		
		$reminder = new Reminder($this->db);
		$reminder->name = 'reminders_4';
		$date = date('Y-m-d');
		$date = time($date);
		$reminder->date = $date;
		$reminder->facility_id = '1';
		$reminder->save();
		
		$myTestReminder = Phactory::get(TB_REMINDER, array('name'=>"reminders_4"));
		// i have 3 reminders in my test data base, so if i added a new reminder successfully - i can get this reminder as fourth in my data base
		$this->assertTrue($myTestReminder->id == '4');
	}
	
	public function testUpdateReminder() {
		
		// get reminder with id = 1
		// name = reminders_1
		$reminder = new Reminder($this->db, '1');
		// updaete, set name is reminders_4
		$reminder->name = 'reminders_4';
		$reminder->save();
		// check
		$myTestReminder = Phactory::get(TB_REMINDER, array('name'=>"reminders_4"));
		
		$this->assertTrue($myTestReminder->id == '1');
	}
	
	public function testDeleteReminder() {
		
		$reminder = new Reminder($this->db, '1');
		$selectedReminder = Phactory::get(TB_REMINDER, array('name'=>"reminders_1"));
		$this->assertTrue(!is_null($selectedReminder));
		$reminder->delete();
		$deletedReminder = Phactory::get(TB_REMINDER, array('name'=>"reminders_1"));
		$this->assertTrue(is_null($deletedReminder));
	}
	
	
	public function testInitByArray() {
		$remindersId = 1;
		
		$remindersOriginal = new Reminder($this->db, $remindersId);
		
		$sql = "SELECT * FROM ".TB_REMINDER." WHERE id = {$remindersId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$remindersChecked = new Reminder($this->db);
		$remindersChecked->initByArray($row);
		$this->assertEquals($remindersOriginal, $remindersChecked);
	}
    
    public function testLoadUsers() {
		
		// get reminder with id = 1
		$reminder = new Reminder($this->db, '1');
        $users = $reminder->getUsers();
      
		$this->assertTrue(is_array($users));
        $this->assertTrue(count($users) == 2);
	}
    
    public function testSetRemind2User() {
		
		// get reminder with id = 1
		$reminder = new Reminder($this->db, '1');
        
        $users = $reminder->getUsers();
        $this->assertTrue(count($users) == 2);
        
        $reminder->setRemind2User('4');   
        $users = $reminder->getUsers();
        $this->assertTrue(count($users) == 3);
	}
    
    public function testUnSetRemind2User() {
		
		// get reminder with id = 1
		$reminder = new Reminder($this->db, '1');
        
        $users = $reminder->getUsers();
        $this->assertTrue(count($users) == 2);
        
        $reminder->unSetRemind2User();   
        $users = $reminder->getUsers();
        $this->assertTrue(count($users) == 0);
	}
	
	public function testGetReminders() {

		$reminderManager = new ReminderManager($this->db);

        $this->assertTrue(count($reminderManager) == 3);

        $this->assertTrue($reminderManager[0] instanceof Reminder);
	}
}