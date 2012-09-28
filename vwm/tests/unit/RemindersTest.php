<?php

use VWM\Framework\Test as Testing;

class RemindersTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY, TB_USER, TB_REMINDERS, TB_REMIND2USER
	);

	public function testReminders() {
		$reminders = new Reminders($this->db, '1');
		$this->assertTrue($reminders instanceof Reminders);

	}
	
	public function testAddReminders() {
		
		$reminders = new Reminders($this->db);
		$reminders->name = 'reminders_4';
		$date = date('Y-m-d');
		$date = time($date);
		$reminders->date = $date;
		$reminders->facility_id = '1';
		$reminders->save();
		
		$myTestReminders = Phactory::get(TB_REMINDERS, array('name'=>"reminders_4"));
		// i have 3 reminders in my test data base, so if i added a new reminder successfully - i can get this reminder as fourth in my data base
		$this->assertTrue($myTestReminders->id == '4');
	}
	
	public function testUpdateReminders() {
		
		// get reminder with id = 1
		// name = reminders_1
		$reminders = new Reminders($this->db, '1');
		// updaete, set name is reminders_4
		$reminders->name = 'reminders_4';
		$reminders->save();
		// check
		$myTestReminders = Phactory::get(TB_REMINDERS, array('name'=>"reminders_4"));
		
		$this->assertTrue($myTestReminders->id == '1');
	}
	
	public function testDeleteReminders() {
		
		$reminders = new Reminders($this->db, '1');
		$selectedReminders = Phactory::get(TB_REMINDERS, array('name'=>"reminders_1"));
		$this->assertTrue(!is_null($selectedReminders));
		$reminders->delete();
		$deletedReminders = Phactory::get(TB_REMINDERS, array('name'=>"reminders_1"));
		$this->assertTrue(is_null($deletedReminders));
	}
	
	
	public function testInitByArray() {
		$remindersId = 1;
		
		$remindersOriginal = new Reminders($this->db, $remindersId);
		
		$sql = "SELECT * FROM ".TB_REMINDERS." WHERE id = {$remindersId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$remindersChecked = new Reminders($this->db);
		$remindersChecked->initByArray($row);
		$this->assertEquals($remindersOriginal, $remindersChecked);
	}
    
    public function testLoadUsers() {
		
		// get reminder with id = 1
		$reminders = new Reminders($this->db, '1');
        $users = $reminders->getUsers();
      
		$this->assertTrue(is_array($users));
        $this->assertTrue(count($users) == 2);
	}
    
    public function testSetRemind2User() {
		
		// get reminder with id = 1
		$reminders = new Reminders($this->db, '1');
        
        $users = $reminders->getUsers();
        $this->assertTrue(count($users) == 2);
        
        $reminders->setRemind2User('4');   
        $users = $reminders->getUsers();
        $this->assertTrue(count($users) == 3);
	}
    
    public function testUnSetRemind2User() {
		
		// get reminder with id = 1
		$reminders = new Reminders($this->db, '1');
        
        $users = $reminders->getUsers();
        $this->assertTrue(count($users) == 2);
        
        $reminders->setUnRemind2User('1');   
        $users = $reminders->getUsers();
        $this->assertTrue(count($users) == 1);
	}
	
	public function testGetReminders() {

		$reminders = new Reminders($this->db);

        $this->assertTrue(count($reminders) == 3);

        $this->assertTrue($reminders[0] instanceof Reminders);
	}
}