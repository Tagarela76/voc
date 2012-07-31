<?php

use VWM\Framework\Test as Testing;

class FacilityTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY, TB_DEPARTMENT, TB_WORK_ORDER
	);

	public function testGetWorkOrdersList() {
		
		$facility = new Facility($this->db);
		$workOrder = $facility->getWorkOrdersList('1');
		
		$this->assertTrue($workOrder[0] instanceof WorkOrder);
		$this->assertTrue(sizeof($workOrder) == 2);
	}
	
	public function testCountWorkOrderInFacility() {
		
		$facility = new Facility($this->db);
		$workOrderCount = $facility->countWorkOrderInFacility('1');

		$this->assertTrue($workOrderCount == 2);
	}
	
}