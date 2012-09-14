<?php

use VWM\Framework\Test as Testing;

class NoxEmissionManagerTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY, TB_DEPARTMENT, 'burner'
	);
	
	public function testGetBurnerListByFacility() {
		
		$noxEmissionManager = new NoxEmissionManager($this->db);
		$facilityID = 1;
		$burnerList = $noxEmissionManager->getBurnerListByFacility($facilityID);
		
		$this->assertTrue(count($burnerList) == 2);
	}

		
}