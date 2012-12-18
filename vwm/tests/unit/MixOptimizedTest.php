<?php

use VWM\Framework\Test as Testing;


class MixOptimizedTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_TYPE, TB_UNITTYPE, TB_DEPARTMENT, TB_SUPPLIER, TB_PRODUCT, TB_WORK_ORDER, TB_USAGE,
		TB_MIXGROUP, 'price4product'
	);


	public function testSave() {
		//	--UPDATE--
		$mixID = 1;
		$mix = new MixOptimized($this->db, $mixID);
  
		$mix->description = "WO12-020220-UPDATED";
		$mix->spent_time = 120;
		$this->assertEquals($mixID, $mix->save(false));

		$wo12Mix = Phactory::get(TB_USAGE, array('mix_id'=>$mixID)); 
		$this->assertEquals($wo12Mix->description, $mix->description);
		$this->assertEquals($mix->spent_time, $wo12Mix->spent_time);

		//	did we lost products?
		$this->assertTrue(count($mix->products) == 2);
		
		$this->assertTrue($mix->products[0] instanceof MixProduct);
		$this->assertTrue($mix->products[0]->product_nr == '470C0191');

		// --INSERT--
		$newMix = new MixOptimized($this->db);
		$newMix->facility_id = 1;
		$newMix->department_id = 1;
		$newMix->equipment_id = 666;
		$newMix->description = 'My Test Mix';
		$newMix->rule = '666';
		$newMix->rule_id = 1;
		$newMix->exempt_rule = '';
		$newMix->creation_time = '07/13/2012';
		$newMix->apmethod_id = 666;
		$newMix->notes = '';
		$newMix->valid = true;
		$newMix->iteration = 0;
		$newMix->parent_id = null;
		$newMix->spent_time = 45;
		$newMix->isMWS = false;

		$unittype = new Unittype($this->db);

		$productA = new MixProduct($this->db);
		$productA->initializeByID(1);
		$productA->quantity = 3;
		$unittypeDetails = $unittype->getUnittypeDetails(1);
		$productA->unit_type = $unittypeDetails['name'];
		$productA->unittypeDetails = $unittypeDetails;
		$productA->json = json_encode($productA);
		$productA->is_primary = 0;
		$productA->ratio_to_save = null;

		$productB = new MixProduct($this->db);
		$productB->initializeByID(2);
		$productB->quantity = 2;
		$unittypeDetails = $unittype->getUnittypeDetails(1);
		$productB->unit_type = $unittypeDetails['name'];
		$productB->unittypeDetails = $unittypeDetails;
		$productB->json = json_encode($productB);
		$productB->is_primary = 0;
		$productB->ratio_to_save = null;

		$newMix->products = array($productA, $productB);
		$newMix->getEquipment();
		$newMix->getFacility();

		$newMix->save(false);

		$myTestMix = Phactory::get(TB_USAGE, array('mix_id'=>$newMix->mix_id));
		$this->assertTrue($myTestMix->last_update_time == date(MYSQL_DATE_FORMAT. " H:i:s"));
		$this->assertEquals($myTestMix->spent_time, $newMix->spent_time);

		//	did we lost products?
		$this->assertTrue(count($newMix->products) == 2);
		$this->assertTrue($newMix->products[0] instanceof MixProduct);
		$this->assertTrue($newMix->products[0]->product_nr == '17-033-A');
	}


	public function testDelete() {
		$mixID = 1;
		$mix = new MixOptimized($this->db, $mixID);
		$mix->delete();

		$deletedMix = Phactory::get(TB_USAGE, array('mix_id'=>$mixID));
		$this->assertTrue(is_null($deletedMix));
	}

	public function testDoesProductsHaveDuplications() {
		$goodMixProduct = new MixProduct($this->db);
		$goodMixProduct->initializeByID(4);

		$badMixProduct = new MixProduct($this->db);
		$badMixProduct->initializeByID(2);

		$mix = new MixOptimized($this->db, 1);
		$mix->products[] = $goodMixProduct;
		$this->assertTrue($mix->doesProductsHaveDuplications() === false);

		$mix->products[] = $badMixProduct;
		$this->assertTrue($mix->doesProductsHaveDuplications() === true);
	}
	
	public function testGetMixPrice() {
		$mixID = '1';
		$mixOptimized = new MixOptimized($this->db, $mixID);
		$mixPrice = $mixOptimized->getMixPrice();
		$this->assertTrue(!is_null($mixPrice));
		$this->assertTrue($mixPrice == 195.74);
	}
	
	
	public function testGetRepairOrder() {
		$mixId = 1;
		$mix = new MixOptimized($this->db, $mixId);
		$wo = $mix->getRepairOrder();
		$this->assertInstanceOf('RepairOrder', $wo);
		$this->assertEquals('joh smith', $wo->customer_name);
		
		$mixIdWithoutWo = 7;
		$mixWithoutWo = new MixOptimized($this->db, $mixIdWithoutWo);
		$false = $mixWithoutWo->getRepairOrder();
		$this->assertFalse($false);
		
	}

}
