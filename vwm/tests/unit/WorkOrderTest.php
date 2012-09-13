<?php

use VWM\Framework\Test as Testing;

class WorkOrderTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY, TB_DEPARTMENT, TB_SUPPLIER, TB_PRODUCT, TB_USAGE, TB_MIXGROUP, TB_WORK_ORDER
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
		$workOrder->facility_id = '2';
		$workOrder->status = 'new';
		$workOrder->vin = 'new';
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
	
	public function testInitByArray() {
		$woId = 1;
		
		$workOrderOriginal = new WorkOrder($this->db, $woId);
		
		$sql = "SELECT * FROM ".TB_WORK_ORDER." WHERE id = {$woId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$workOrderChecked = new WorkOrder($this->db);
		$workOrderChecked->initByArray($row);
		$this->assertEquals($workOrderOriginal, $workOrderChecked);
	}
	
}