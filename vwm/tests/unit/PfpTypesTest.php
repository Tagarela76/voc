<?php

use VWM\Framework\Test as Testing;

class PfpTypesTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_PFP_TYPES, TB_PFP, TB_FACILITY, TB_PFP2PFP_TYPES
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
    
    public function testGetPfpProducts() {		
		$pfpTypes = new PfpTypes($this->db, '1');
		$pfpProducts = $pfpTypes->getPfpProducts();
		
		$this->assertEquals(count($pfpProducts), 1);		
		$this->assertEquals($pfpProducts[0]->id, 1);
	}
	
}