<?php

class EquipmentFilterTest extends DbTestCase {

	protected $fixtures = array(
		'equipment_filter'
	);

	public function testDeleteEquipmentFilter() {
		$equipmentFilter= new EquipmentFilter($this->db, 3);

		// delete equipment filter
		$equipmentFilter->delete();
		
		// get equipment filter that doesn't exist
		$eqFilter = new EquipmentFilter($this->db, 3); 
		$this->assertTrue(!is_null($eqFilter));
	}
	
	public function testViewEquipmentFilter() {
		$equipmentFilter = new EquipmentFilter($this->db, 3);

		$this->assertTrue($equipmentFilter->name == 'test3');
	}
	
	public function testSaveEquipmentFilter() {
		$equipmentFilter = new EquipmentFilter($this->db);

		$equipmentFilter->equipment_filter_type_id =  '1';
		$equipmentFilter->equipment_id = '3333';
		$equipmentFilter->height_size = '33';
		$equipmentFilter->length_size = '33';
		$equipmentFilter->width_size = '33';
		$equipmentFilter->name = 'test44';
		$equipmentFilter->qty = '3';

		$equipmentFilter->save();
		
		$myTestEqFilter = Phactory::get(TB_EQUIPMENT_FILTER, array('equipment_filter_id'=>"2"));
		$this->assertTrue($myTestEqFilter->name == 'test2');
		
	}
	
	public function testViewListEquipmentFilterTypes() {
		$equipmentFilter = new EquipmentFilter($this->db);

		$equipmentFilterTypesList = $equipmentFilter->getFilterTypesList();

		$this->assertTrue(!is_null($equipmentFilterTypesList));
		
	}
	
}