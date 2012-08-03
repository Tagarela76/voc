<?php

use VWM\Framework\Test as Testing;

class ProductTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP, TB_PFP2PRODUCT, TB_PFP2COMPANY
	);

	public function testGetPFPList() {
		$product = new Product($this->db);
		$pfpList = $product->getAssign2CompanyPFPListByProduct('1'); 
		
		$this->assertTrue(is_array($pfpList));
        $this->assertTrue(count($pfpList) == 1);
		
		$pfpList = $product->getUnassign2CompanyPFPListByProduct('3'); 
		
		$this->assertTrue(is_array($pfpList));
        $this->assertTrue(count($pfpList) == 1);
	}
	
}