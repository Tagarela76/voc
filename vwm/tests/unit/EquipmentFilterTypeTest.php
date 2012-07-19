<?php

class EquipmentFilterTypeTest extends DbTestCase {

	protected $fixtures = array(
		'equipment_filter_type'
	);


	public function testDeleteEquipmentFilterTypes() {
		$equipmentFilterType = new EquipmentFilterType($this->db, 4);

		// delete equipment type
		$equipmentFilterType->delete();
		
	}
	
	public function testViewEquipmentFilterTypes() {
		$equipmentFilterType = new EquipmentFilterType($this->db, 2);

		$this->assertTrue(!is_null($equipmentFilterType));
		$this->assertTrue($equipmentFilterType->name == 'test2');
	}
	
}