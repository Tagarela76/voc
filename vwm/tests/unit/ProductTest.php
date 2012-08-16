<?php

use VWM\Framework\Test as Testing;

class ProductTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP, TB_PFP2PRODUCT, TB_PFP2COMPANY, TB_PRODUCT2PRODUCT_LIBRARY_TYPE, TB_PRODUCT_LIBRARY_TYPE
	);

	public function testGetPFPList() {
		$product = new Product($this->db);
		$pfpList = $product->getAvailable2CompanyPFPListByProduct('1','1');
		
		$this->assertTrue(is_array($pfpList));
        $this->assertTrue(count($pfpList) == 1);
		
		$pfpList = $product->getUnavailable2CompanyPFPListByProduct('3', '1');
		
		$this->assertTrue(is_array($pfpList));
        $this->assertTrue(count($pfpList) == 0);
	}
	
	public function testGetProductLibraryTypes() {
		
		$product = new Product($this->db);
		$proctLibraryTypes = $product->getProductLibraryTypes('1');
		$this->assertTrue(count($proctLibraryTypes) == 2);
		$this->assertTrue($proctLibraryTypes[0] instanceof ProductLibraryType);
	}
	public function testAddProductLibraryTypes() {
		
		$product = new Product($this->db);
		$productId = 1;
		$productLibraryTypes = array("3", "4");
		$product->addProductLibraryTypes($productId, $productLibraryTypes);
		
		$proctLibraryTypes = $product->getProductLibraryTypes('1');
		$this->assertTrue(count($proctLibraryTypes) == 4);

	}
	
}