<?php

use VWM\Framework\Test as Testing;

class NoxBurnerTest extends Testing\DbTestCase {

	protected $fixtures = array(
		'burner'
	);
	
	public function testSetBurnersRatio() {
		
		$noxBurner = new NoxBurner($this->db);
		$burnerId = 1;
		$ratio = 10;
		$noxBurner->setRatio2Burner($burnerId, $ratio);
		$burnerRatio = Phactory::get('burner', array('burner_id'=>"1"));

		$this->assertTrue(!is_null($burnerRatio));
		$this->assertTrue($burnerRatio->ratio == $ratio);
	}
	
	public function testGetBurnersRatio() {
		
		$noxBurner = new NoxBurner($this->db);
		$facilityId = 1;
		$ratio = $noxBurner->getCommonRatio4Facility($facilityId);
		$this->assertTrue($ratio == 10);
	}

		
}