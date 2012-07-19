<?php

use VWM\Framework\Test as Testing;

class EquipmentLightingBulbTypeTest extends Testing\DbTestCase {

	protected $fixtures = array(
		'equipment_lighting_bulb_type'
	);


	public function testDeleteEquipmentLightingBulbType() {
		$equipmentLightingBulbType = new EquipmentLightingBulbType($this->db, 2);
		// delete equipment type
		$equipmentLightingBulbType->delete();

	}
	
	public function testViewEquipmentLightingBulbType() {
		$equipmentLightingBulbType = new EquipmentLightingBulbType($this->db,3);

		$this->assertTrue($equipmentLightingBulbType->name == 'test3');
	}
	
}