<?php

namespace VWM\Entity\Crib;

use VWM\Framework\Test\DbTestCase;

class CribTest extends DbTestCase {
			
	protected $fixtures = array(
//		TB_FACILITY,
//		Crib::TABLE_NAME,
//		Bin::TABLE_NAME,
	);

	
	public function testSave()
    {					
        $this->markTestIncomplete();

		$crib = new Crib($this->db);
		$crib->setFacilityId('1');
		$crib->setSerialNumber('Blah!');
		$r = $crib->save();
		
		$expectedCribId = 5;
		$this->assertEquals($expectedCribId, $r);
		
		$sql = "SELECT * FROM ".  Crib::TABLE_NAME." WHERE id = {$expectedCribId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);
		$expectedCrib = new Crib($this->db);
		$expectedCrib->initByArray($row);
	//	$this->assertEquals($expectedCrib, $crib);
		
		
		//	UPDATE
		$crib->setSerialNumber('Hmm');
		$crib->save();
		
		$sql = "SELECT * FROM ".  Crib::TABLE_NAME." WHERE id = {$expectedCribId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedUpdatedCrib = new Crib($this->db);
		$expectedUpdatedCrib->initByArray($row);
//		$this->assertEquals($expectedUpdatedCrib, $crib);				
	}	
	
	
	public function testGetBins() 
    {
        $this->markTestIncomplete();

		$cribId = 1;
		$crib = new Crib($this->db, $cribId);
		$bins = $crib->getBins();
		
		$this->assertTrue(is_array($bins));
		$this->assertEquals(2, count($bins));
		
		$firstBin = new Bin($this->db, 1);
		$secondBin = new Bin($this->db, 2);
		$this->assertEquals($firstBin, $bins[0]);
		$this->assertEquals($secondBin, $bins[1]);
		
		$cribWithoutBins = new Crib($this->db, 2);
		$this->assertEquals(array(), $cribWithoutBins->getBins());
		
	}
	
	public function testCheckAddOrUpdate()
    {
	    $this->markTestIncomplete();

		$crib = new Crib($this->db);
		$crib->setFacilityId('1');
		$crib->setSerialNumber('Blah!'); // unique
		
		$crib->check();
		// this crib is new, so id is null
		$cribId = $crib->getId();
		$this->assertTrue(is_null($cribId));
		
		// now we add this crib
		$crib->save();
		
		// check again
		$crib->check();
		// we add this bin, so id is not null
		$updatedCribId = $crib->getId();
		$this->assertTrue(!is_null($updatedCribId));
	}
}
