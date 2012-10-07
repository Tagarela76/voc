<?php

namespace VWM\Entity\Product;

use VWM\Framework\Test\DbTestCase;

class PaintProductTest extends DbTestCase {
	
	public function testSave() {
		$paintProduct = new PaintProduct($this->db);
		$paintProduct->setAerosol('no');
		$paintProduct->setBoilingRangeFrom('0.00');
		$paintProduct->setBoilingRangeTo('5.95');
		$paintProduct->setClosed('NO');
		$paintProduct->setCoatingId('4');
		$paintProduct->setDensity('4.34');
		$paintProduct->setDensityUnitId('1');
		$paintProduct->setDiscontinued('0');
		$paintProduct->setFlashPoint('0');
		$paintProduct->setName('SUPER TEST');
		$paintProduct->setPercentVolatileVolume('54.4');
		$paintProduct->setPercentVolatileWeight('64.34');
		$paintProduct->setPriceUnitType('1');
		$paintProduct->setProductAmount('25');
		$paintProduct->setProductInstock('50');
		$paintProduct->setProductLimit('5');
		$paintProduct->setProductNr('TEST-007');
		$paintProduct->setProductPricing('5.99');
		$paintProduct->setProductStocktype('99');
		$paintProduct->setSpecialtyCoating('no');
		$paintProduct->setSpecificGravity('4.78');
		$paintProduct->setSpecificGravityUnitId('1');
		$paintProduct->setSupplierId('1');
		$paintProduct->setVoclx('7.45');
		$paintProduct->setVocwx('7.64');
		
		$r = $paintProduct->save();		
	}
	
	public function testGetFacilityContext() {				
		// Case: get total product qty at the facility
		/*$product = new PaintProduct($this->db, 1);
		$facilityContext = $product->getFacilityContext($facilityId);		
		$facilityContext->getCurrentQty();*/
	}
	
	public function testGetCribContext() {
		// Case: get total product qty at the crib
		/*$product = new PaintProduct($this->db, 1);
		$cribContext = $product->getCribContext($cribId);		
		$cribContext->getCurrentQty();*/
	}
	
	public function testGetBinContext() {
		// Case: qty decreased at the bin		
		$productId = 1;
		$product = new PaintProduct($this->db, $productId);
		
		$binId = 1;
		$binContext = $product->getBinContext($binId);	
		$this->assertInstanceOf('VWM\Entity\Product\BinContext', $binContext);
		$this->assertEquals(60, $binContext->getCurrentQty());
	}
}

?>
