<?php

use VWM\Framework\Test as Testing;

class SupplierTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_SUPPLIER
	);

	public function testGetSupplierIdByName() {
		$supplier = new Supplier($this->db);
		$name = "PPG";
		$supplierId = $supplier->getSupplierIdByName($name);
		$this->assertEquals(2, $supplierId);
	}
	
}