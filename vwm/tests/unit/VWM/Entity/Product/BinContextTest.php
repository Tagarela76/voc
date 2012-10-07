<?php

namespace VWM\Entity\Product;

use VWM\Framework\Test\DbTestCase;
use VWM\Entity\Crib\Bin;

class BinContextTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_FACILITY,
		TB_SUPPLIER,
		TB_PRODUCT,
		Bin::TABLE_NAME,
		BinContext::TABLE_NAME,
	);
			
	public function testLoad() {
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
}

?>
