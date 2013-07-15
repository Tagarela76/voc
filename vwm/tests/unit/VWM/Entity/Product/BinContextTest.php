<?php

namespace VWM\Entity\Product;

use VWM\Framework\Test\DbTestCase;
use VWM\Entity\Crib\Bin;

class BinContextTest extends DbTestCase {
	
	protected $fixtures = array(
/*		TB_FACILITY,
		TB_SUPPLIER,
		TB_PRODUCT,
		Bin::TABLE_NAME,
		BinContext::TABLE_NAME,*/
	);
			
	public function testLoad()
    {
        $this->markTestIncomplete();

		$binContext = new BinContext($this->db);
		
		$productId = 1;
		$binId = 1;
		$r = $binContext->load($productId, $binId);
		$this->assertTrue($r);
		
		$sql = "SELECT * FROM ".BinContext::TABLE_NAME." " .
				"WHERE product_id = {$productId} " .
				"AND bin_id = {$binId}";
		$this->db->query($sql);			
		$row = $this->db->fetch_array(0);
		$expectedBinContext = new BinContext($this->db);
		$expectedBinContext->initByArray($row);
		
		$this->assertEquals($expectedBinContext, $binContext);
		
	}
	
	public function testInsert()
    {
        $this->markTestIncomplete();

		$binContext = new BinContext($this->db);
		$binContext->setProductId("2");
		$binContext->setBinId("2");
		$binContext->setCurrentQty("3");
		
		$r = $binContext->save();

		$this->assertTrue($r);
		
		// if we add a new item
		$sql = "SELECT * FROM ". BinContext::TABLE_NAME;
		$this->db->query($sql);
		$this->assertEquals(3, $this->db->num_rows());
		
		// if we add a new item with product id = 2
		$sql = "SELECT * FROM ". BinContext::TABLE_NAME ." WHERE product_id = 2";
		$this->db->query($sql);
		$this->assertEquals(2, $this->db->num_rows());
		
	}
}

?>
