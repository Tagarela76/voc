<?php

use VWM\Framework\Test as Testing;

class WorkOrderTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_DEPARTMENT, TB_WORK_ORDER, TB_USAGE
	);

	public function testWorkOrder() {
		$workOrder = new WorkOrder($this->db, 'test1');
		$this->assertTrue($workOrder instanceof WorkOrder);
		$this->assertTrue(!is_null($workOrder));
	}
	
	public function testAddWorkOrder() {
		
		$workOrder = new WorkOrder($this->db);
		$workOrder->number = '545-wr';
		$workOrder->description = 'test ';
		$workOrder->customer_name = 'nick smith';
		$workOrder->facility_id = '100';
		$workOrder->status = 'new';
		$workOrder->save();
		
		$myTestWorkOrder = Phactory::get(TB_WORK_ORDER, array('description'=>"test"));
		$this->assertTrue($myTestWorkOrder->number == '545-wr');
	}
	
	public function testDeleteWorkOrder() {
		
		$workOrder = new WorkOrder($this->db, '1');
		$workOrder->delete();
		$deletedWorkOrder = Phactory::get(TB_WORK_ORDER, array('number'=>"test1"));
		$this->assertTrue(is_null($deletedWorkOrder));
	}
	
	public function testGetMixes() {
		
		$workOrder = new WorkOrder($this->db, '1');
		$mixes = $workOrder->getMixes();
		
		$this->assertTrue($mixes[0] instanceof MixOptimized);
	}
	
}