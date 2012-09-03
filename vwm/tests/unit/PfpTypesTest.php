<?php

use VWM\Framework\Test as Testing;

class PfpTypesTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_PFP_TYPES, TB_PFP, TB_FACILITY
	);

	public function testAddPFPType() {
		$pfpTypes = new PfpTypes($this->db, '1');
		$this->assertTrue($pfpTypes instanceof PfpTypes);
		
        $pfpTypes = new PfpTypes($this->db);
        $pfpTypes->name = "test10";
		$pfpTypes->facility_id = "1";
        $pfpTypes->save(); 
        $pfpTypesTest = Phactory::get(TB_PFP_TYPES, array('id'=>"5"));
		$this->assertTrue($pfpTypesTest->name == 'test10');
	}
	
	public function testDeletePFPType() {
		
		$pfpTypes = new PfpTypes($this->db, '1');
		$pfpTypes->delete();
		$pfpTypesTest = Phactory::get(TB_PFP_TYPES, array('id'=>"1"));
		$this->assertTrue(is_null($pfpTypesTest));
	}
    
    public function testGetPfpProductsByTypeId() {
		
		$pfpTypes = new PfpTypes($this->db, '1');
		$pfpProducts = $pfpTypes->getPfpProducts();
		$this->assertTrue(count($pfpProducts) == 2);
	}
	
}