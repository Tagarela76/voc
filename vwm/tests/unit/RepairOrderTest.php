<?php

use VWM\Framework\Test as Testing;

class RepairOrderTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_FACILITY, TB_DEPARTMENT, TB_SUPPLIER, TB_PRODUCT, TB_USAGE, TB_MIXGROUP, TB_WORK_ORDER
	);

	public function testRepairOrder() {
		$repairOrder = new RepairOrder($this->db, 'test1');
		$this->assertTrue($repairOrder instanceof RepairOrder);
		$this->assertTrue(!is_null($repairOrder));
	}

	public function testAddRepairOrder() {

		$repairOrder = new RepairOrder($this->db);
		$repairOrder->number = '545-wr';
		$repairOrder->description = 'test';
		$repairOrder->customer_name = 'nick smith';
		$repairOrder->facility_id = '2';
		$repairOrder->status = 'new';
		$repairOrder->vin = 'new';
		$repairOrder->save();

		$myTestRepairOrder = Phactory::get(TB_WORK_ORDER, array('description'=>"test"));
		$this->assertTrue($myTestRepairOrder->number == '545-wr');
	}

	public function testDeleteRepairOrder() {

		$repairOrder = new RepairOrder($this->db, '1');
		$repairOrder->delete();
		$deletedRepairOrder = Phactory::get(TB_WORK_ORDER, array('number'=>"test1"));
		$this->assertTrue(is_null($deletedRepairOrder));
	}

	public function testGetMixes() {

		$repairOrder = new RepairOrder($this->db, '1');
		$mixes = $repairOrder->getMixes();

		$this->assertTrue($mixes[0] instanceof MixOptimized);
	}

	public function testInitByArray() {
		$woId = 1;

		$repairOrderOriginal = new RepairOrder($this->db, $woId);

		$sql = "SELECT * FROM ".TB_WORK_ORDER." WHERE id = {$woId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);

		$repairOrderChecked = new RepairOrder($this->db);
		$repairOrderChecked->initByArray($row);
		$this->assertEquals($repairOrderOriginal, $repairOrderChecked);
	}

}