<?php

namespace VWM\Entity\Product;

use VWM\Framework\Test\DbTestCase;
use VWM\Entity\Crib\Bin;

class GomTest extends DbTestCase {
	
	protected $fixtures = array(
/*		TB_FACILITY,
		TB_SUPPLIER,
		TB_PRODUCT,
		TB_GOM,
		Bin::TABLE_NAME,
		BinContext::TABLE_NAME,*/
	);


	public function testSave()
    {		
	    $this->markTestIncomplete();

		$gom = new Gom($this->db);
		$gom->setName("gom-test");
		$gom->setProductNr("__gom-test");
		$gom->setSupplierId('1');
		$gom->setProductPricing("2.00");
		$gom->setAddToxicCompounds('1');
		$gom->setMsdsHheet('0');
		$gom->setPackageSize('3');

		$r = $gom->save();
		$expectedId = 3;
		$this->assertEquals($expectedId, $r);
		
		$sql = "SELECT * FROM ". TB_GOM ." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);
		$expectedGom = new Gom($this->db);
		$expectedGom->initByArray($row);
		$this->assertInstanceOf('\VWM\Entity\Product\Gom', $expectedGom);
	//	$this->assertEquals($expectedGom, $gom);
		
		//UPDATE
		$gom->setProductPricing("3.00");
		$gom->save();
		$updatedPricing = $gom->getProductPricing();
		$this->assertEquals("3.00", $updatedPricing);
		$sql = "SELECT * FROM ".  TB_GOM." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedUpdatedGom = new Gom($this->db);
		$expectedUpdatedGom->initByArray($row);
	//	$this->assertEquals($expectedUpdatedGom, $gom);
	}
	
	public function testCheckAddOrUpdate() 
    {
	    $this->markTestIncomplete();

		$gom = new Gom($this->db);
		$gom->setName("gom-test");
		$gom->setProductNr("__gom-test");
		$gom->setSupplierId("1");
		$gom->setProductPricing("2.00");
		$gom->setAddToxicCompounds('1');
		$gom->setMsdsHheet('0');
		$gom->setPackageSize('3');
		
		$gom->check();
		// this product is new, so id is null
		$gomId = $gom->getId();
		$this->assertTrue(is_null($gomId));
		
		// now we add this gom
		$gom->save();
		
		// check again
		$gom->check();
		// we add this product, so gomid is not null
		$updatedGomId = $gom->getId();
		$this->assertTrue(!is_null($updatedGomId));
	}
	
}

