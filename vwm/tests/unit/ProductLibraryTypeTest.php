<?php

use VWM\Framework\Test as Testing;

class ProductLibraryTypeTest extends Testing\DbTestCase {

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PRODUCT_LIBRARY_TYPE
    );

    public function testProductLibraryType() {
        $productLibraryType = new ProductLibraryType($this->db, '1');
        $this->assertTrue($productLibraryType instanceof ProductLibraryType);
    }

	public function testAddProductLibraryType() {
		
		$productLibraryType = new ProductLibraryType($this->db);
		$productLibraryType->name = 'test';
		$productLibraryType->save();
		
		$myTestProductLibraryType = Phactory::get(TB_PRODUCT_LIBRARY_TYPE, array('name'=>"test"));
		$this->assertTrue($myTestProductLibraryType->name == 'test');
	}
	
	public function testGetProductLibraryTypes() {
		
        $productLibraryType = new ProductLibraryType($this->db);
		$productLibraryTypes = $productLibraryType->getProductLibraryTypes();
		
        $this->assertTrue($productLibraryTypes[0] instanceof ProductLibraryType);
		$this->assertTrue(count($productLibraryTypes) == 4);
    }

	public function testDeleteProductLibraryType() {
		
		$productLibraryType = new ProductLibraryType($this->db, '1');
		$productLibraryType->delete();
		$deletedProductLibraryType = Phactory::get(TB_PRODUCT_LIBRARY_TYPE, array('name'=>"test1"));
		$this->assertTrue(is_null($deletedProductLibraryType));
	}
}
