<?php

use VWM\Framework\Test as Testing;

class WorkOrderTest extends Testing\DbTestCase {

	protected $fixtures = array(
		'work_order'
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
		$workOrder->customerName = 'nick smith';
		$workOrder->facilityId = '100';
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
	
}