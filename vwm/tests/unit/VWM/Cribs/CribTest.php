<?php

namespace VWM\Cribs;

use VWM\Framework\Test\DbTestCase;

class CribTest extends DbTestCase {
			
	protected $fixtures = array(
		TB_FACILITY,
		Crib::TABLE_NAME,
	);
	
	
	public function testSave() {		
		$crib = new Crib($this->db);
		$crib->setFacilityId(1);
		$crib->setSerialNumber('Blah!');
		$r = $crib->save();
		
		$this->assertEquals(5, $r);
	}	
}
?>
