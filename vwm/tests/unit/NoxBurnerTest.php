<?php

use VWM\Framework\Test as Testing;

class NoxBurnerTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_DEPARTMENT, 'burner',
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
	
	
	public function testSave() {
		$noxBurner = new NoxBurner($this->db);
		$noxBurner->department_id = 1;
		$noxBurner->manufacturer_id = 1;
		$noxBurner->model = 'testModel';
		$noxBurner->serial = '777';
		$noxBurner->input = '10000';
		$noxBurner->output = '9000';
		$noxBurner->btu = '10000';
		
		$result = $noxBurner->save();
		$this->assertTrue($result);
		$sql = "SELECT * FROM burner WHERE model='{$noxBurner->model}' AND " .
				"serial = '{$noxBurner->serial}'";
		$this->db->query($sql);
		$this->assertEquals($this->db->num_rows(), 1);
		
		$updatedModel = 'testModelUpdated'; 
		$noxBurner->model = $updatedModel;
		$resultUpdated = $noxBurner->save();
		$this->assertTrue($resultUpdated);
		
		$sql = "SELECT * FROM burner WHERE model='{$noxBurner->model}' AND " .
				"serial = '{$noxBurner->serial}'";
		$this->db->query($sql);
		$this->assertEquals($this->db->num_rows(), 1);
	}

		
}