<?php

use VWM\Framework\Test as Testing;
use \VWM\Hierarchy\Department;

class PfpTypesTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_PFP_TYPES, TB_PFP, TB_FACILITY, TB_PFP2PFP_TYPES, TB_PFP_2_DEPARTMENT, TB_DEPARTMENT
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
		
		//$this->assertEquals(count($pfpProducts), 1);		
		//$this->assertEquals($pfpProducts[0]->id, 1);
	}
	
	public function testGetDepartments(){
		$testDepartments = array('1','2','3');
		$pfpTypes = new PfpTypes($this->db, '1');
		$pfpTypes->setDepartments($testDepartments);
		$departments = $pfpTypes->getDepartments();
		$this->assertEquals($testDepartments, $departments);
		
		$newPfpTypes = new PfpTypes($this->db, '1');
		$newDepartments = $newPfpTypes->getDepartments();
		$department = new \VWM\Hierarchy\Department($this->db, 1);
		$this->assertTrue(in_array($department, $newDepartments));
		
		
	}
	
	public function testSave(){
		$departments = array();
		
		$department = new Department($this->db, '1');
		$departments[] = $department;
		$department = new Department($this->db, '2');
		$departments[] = $department;
		$department = new Department($this->db, '3');
		$departments[] = $department;
		
		//$testDepartments = array($departments);
		
		$pfpTypes = new PfpTypes($this->db, '1');
		$pfpTypes->setDepartments($departments);
		$pfpTypes->saveDepartmentPFP();
		
		$testDepartments = $pfpTypes->getDepartments();
		$this->assertEquals($testDepartments, $departments);
		
		
	}
	
	
}