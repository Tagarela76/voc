<?php

class EquipmentLightingColorTest extends DbTestCase {

	protected $fixtures = array(
		'equipment_lighting_color'
	);


	public function testDeleteEquipmentLightingColor() {
		$equipmentColor = new EquipmentLightingColor($this->db, 2);
		// delete equipment type
		$equipmentColor->delete();

	}
	
	public function testViewEquipmentLightingColor() {
		$equipmentColor = new EquipmentLightingColor($this->db, 3);

		$this->assertTrue($equipmentColor->name == 'test3');
	}
	
}