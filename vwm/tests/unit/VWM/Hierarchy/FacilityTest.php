<?php

use VWM\Hierarchy\Facility;
use VWM\Framework\Test\DbTestCase;

class FacilityTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_FACILITY
	);
	
	public function testSave() {
		$facility = new Facility($this->db);
		$facility->setAddress('Zaporizhske Drive, 009');
		$facility->setCity('Dnipro');
		$facility->setClientFacilityId('RB003');
		$facility->setCompanyId(1);
		$facility->setContact('Semen');
		$facility->setCountry(2);
		$facility->setCounty('region');
		$facility->setCreaterId(1);
		$facility->setEmail('denis.foo@blah.com');			
		$facility->setEpa('124A');
		$facility->setFax('99-999');
		$facility->setGcgId(1);
		$facility->setMonthlyNoxLimit(60);
		$facility->setName('Zoo Facility');
		$facility->setPhone('555-55-55');
		$facility->setState('Nevada');
		$facility->setTitle('Mr');
		$facility->setVocAnnualLimit(9999);
		$facility->setVocLimit(677);
		$facility->setZip('55555');
		
		$result = $facility->save();
		$this->assertEquals(3, $result);	// last id
	}

}

?>
