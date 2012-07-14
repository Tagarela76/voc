<?php


class MixOptimizedTest extends DbTestCase {

	protected $fixtures = array(
		TB_DEPARTMENT, TB_SUPPLIER, TB_PRODUCT, TB_USAGE, TB_MIXGROUP,
	);


	public function testSave() {
		//	--UPDATE--
		$mixID = 1;
		$mix = new MixOptimized($this->db, $mixID);

		$mix->description = "WO12-020220-UPDATED";
		$mix->save(false);

		$wo12Mix = Phactory::get(TB_USAGE, array('mix_id'=>$mixID));
		$this->assertTrue($wo12Mix->description == $mix->description);

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

		//	did we lost products?
		$this->assertTrue(count($newMix->products) == 2);
		$this->assertTrue($newMix->products[0] instanceof MixProduct);
		$this->assertTrue($newMix->products[0]->product_nr == '17-033-A');

	}

}
