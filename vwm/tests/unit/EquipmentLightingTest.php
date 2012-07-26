<?php

use VWM\Framework\Test as Testing;

class EquipmentLightingTest extends Testing\DbTestCase {

	protected $fixtures = array(
		'equipment_lighting'
	);

	
	public function testGetEquipmentLightingList() {
		
		$equipmentLighting = new EquipmentLighting($this->db, 2);

		$this->assertTrue(!is_null($equipmentLighting));
	}
	
	
	public function testEquipmentLightingSave() {
		$equipmentLighting = new EquipmentLighting($this->db);

		$equipmentLighting->name = 'test44';
		$equipmentLighting->equipment_id	= '3215065';
		$equipmentLighting->bulb_type = '3';
		$equipmentLighting->size =  ' 3 inches';
		$equipmentLighting->voltage = '0-50';
		$equipmentLighting->wattage = '3';
		$equipmentLighting->color = '2';
		$equipmentLighting->quantity = '6';
		
		$equipmentLighting->save();

	}
	
	
	
}