<?php

use VWM\Framework\Test as Testing;

class SalesBrochureTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_SALES_BROCHURE
	);

	public function testInitSalesBrochure() {
		
		$salesBrochure = new SalesBrochure($this->db, '1');
		$this->assertTrue($salesBrochure instanceof SalesBrochure);
	}
	
	public function testAddSalesBrochure() {
		
		$salesBrochure = new SalesBrochure($this->db);
		$salesBrochure->sales_client_id = '2';
		$salesBrochure->title_up = 'test5';
		$salesBrochure->title_down = 'test6';
		$salesBrochure->save();
		
		$mySalesBrochure = Phactory::get(TB_SALES_BROCHURE, array('sales_client_id'=>"2"));
		$this->assertTrue($mySalesBrochure->title_up == 'test5');
	}
	
	public function testDeleteSalesBrochure() {
		
		$salesBrochure = new SalesBrochure($this->db, '1');
		$salesBrochure->delete();
		$deletedSalesBrochure = Phactory::get(TB_SALES_BROCHURE, array('id'=>"1"));
		$this->assertTrue(is_null($deletedSalesBrochure));
	}
	
}