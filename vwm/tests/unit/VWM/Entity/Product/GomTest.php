<?php

namespace VWM\Entity\Product;

use VWM\Framework\Test\DbTestCase;
use VWM\Entity\Crib\Bin;

class GomTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_FACILITY,
		TB_SUPPLIER,
		TB_PRODUCT,
		TB_GOM,
		Bin::TABLE_NAME,
		BinContext::TABLE_NAME,
	);


	public function testSave() {		
				
		$gom = new Gom($this->db);
		$gom->setName("gom-test");
		$gom->setProductNr("__gom-test");
		$gom->setJobberId("1");
		$gom->setSupplierId("1");
		$gom->setCode("code");
		$gom->setProductInstock("2");
		$gom->setProductLimit("20");
		$gom->setProductAmount("2");
		$gom->setProductStocktype("2");
		$gom->setProductPricing("2.00");
		$gom->setPriceUnitType("1");
		
		$r = $gom->save();
		$expectedId = 3;
		$this->assertEquals($expectedId, $r);
		
		$sql = "SELECT * FROM ". TB_ACCESSORY ." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);
		$expectedGom = new Gom($this->db);
		$expectedGom->initByArray($row);
		$this->assertInstanceOf('\VWM\Entity\Product\Gom', $expectedGom);
		$this->assertEquals($expectedGom, $gom);
		
		//UPDATE
		$gom->setCode("new-code");
		$gom->save();
		
		$sql = "SELECT * FROM ".  TB_ACCESSORY." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedUpdatedGom = new Gom($this->db);
		$expectedUpdatedGom->initByArray($row);
		$this->assertEquals($expectedUpdatedGom, $gom);
	}
	
}

?>
