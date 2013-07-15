<?php

use VWM\Framework\Test as Testing;

class FacilityTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_FACILITY, TB_DEPARTMENT, TB_WORK_ORDER, TB_PFP_TYPES, TB_REMINDER
	);

	public function testGetRepairOrdersList() {
		
		$facility = new Facility($this->db);
		$repairOrder = $facility->getRepairOrdersList('1');
		
		$this->assertTrue($repairOrder[0] instanceof RepairOrder);
		$this->assertTrue(sizeof($repairOrder) == 2);
	}
	
	public function testCountRepairOrderInFacility() {
		
		$facility = new Facility($this->db);
		$repairOrderCount = $facility->countRepairOrderInFacility('1');

		$this->assertTrue($repairOrderCount == 2);
	}
    
    public function testGetPfpTypesCount() {
        $facility = new Facility($this->db);
        $pfpTypesCount = $facility->getPfpTypesCount(1);
        $this->assertTrue($pfpTypesCount == 3);
    }
    
    public function testGetPfpTypes() {
        $facility = new Facility($this->db);
        $pfpTypes = $facility->getPfpTypes(1);
        $this->assertTrue($pfpTypes[0] instanceof PfpTypes);
    }
	
	public function testGetRemindersList() {
		
		$facility = new Facility($this->db);
		$reminders = $facility->getRemindersList('1');
		
		$this->assertTrue($reminders[0] instanceof Reminder);
		$this->assertTrue(sizeof($reminders) == 3);
	}
	
	public function testCountRemindersInFacility() {
		
		$facility = new Facility($this->db);
		$remindersCount = $facility->countRemindersInFacility('1');

		$this->assertTrue($remindersCount == 3);
	}
	
}
