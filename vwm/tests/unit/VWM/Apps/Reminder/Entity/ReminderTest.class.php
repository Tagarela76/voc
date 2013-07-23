<?php

namespace VWM\Apps\Reminder\Entity;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Company;

class ReminderTest extends Testing\DbTestCase {

	protected $fixtures = array(
        Company::TABLE_NAME, Facility::TABLE_NAME, TB_USER,  Reminder::TABLE_NAME, Reminder::TB_REMIND2USER
	);

	public function testReminder()
    {
        $reminderId = '1';
        $reminder = new Reminder();
        $reminder->setId($reminderId);
        $reminder->load();
        $this->assertTrue($reminder instanceof Reminder);
    }

	public function testSave()
    {
        $reminder = new Reminder();
        $reminder->setName('reminders_4');
        $date = date('Y-m-d');
        $date = time($date);
        $reminder->setDate($date);
        $reminder->setDeliveryDate($date);
        $reminder->setType('Permit');
        $reminder->setPriority(5);
        $reminder->setFacilityId('1');
        $reminder->save();


        $myTestReminder = \Phactory::get(TB_REMINDER, array('name' => "reminders_4"));
        // i have 3 reminders in my test data base, so if i added a new reminder successfully - i can get this reminder as fourth in my data base
        $this->assertTrue($myTestReminder->id == '4');

        //test update
        // get reminder with id = 1
		// name = reminders_1
		$reminder = new Reminder();
        $reminder->setId('1');
        $reminder->load();
		// updaete, set name is reminders_4
		$reminder->setName('reminders_4');
        $reminder->setPriority(100);
        $reminder->setType('Permit');
		$reminder->save();
		// check
		$myTestReminder = \Phactory::get(TB_REMINDER, array('name'=>"reminders_4"));

		$this->assertTrue($myTestReminder->getId() == '1');
    }


	public function testDeleteReminder() {

		$reminder = new Reminder();
        $reminder->setId('1');
        $reminder->load();
		$selectedReminder = \Phactory::get(TB_REMINDER, array('name'=>"reminders_1"));
		$this->assertTrue(!is_null($selectedReminder));
		$reminder->delete();
		$deletedReminder = \Phactory::get(TB_REMINDER, array('name'=>"reminders_1"));
		$this->assertTrue(is_null($deletedReminder));
	}


	public function testInitByArray() {
		$remindersId = 1;

		$remindersOriginal = new Reminder();
		$remindersOriginal->setId($remindersId);
        $remindersOriginal->load();

		$sql = "SELECT * FROM ".Reminder::TABLE_NAME." WHERE id = {$remindersId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);

		$remindersChecked = new Reminder($this->db);
		$remindersChecked->initByArray($row);
		$this->assertEquals($remindersOriginal, $remindersChecked);
	}
}