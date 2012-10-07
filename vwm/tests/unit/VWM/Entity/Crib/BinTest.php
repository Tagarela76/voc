<?php

namespace VWM\Entity\Crib;

use VWM\Framework\Test\DbTestCase;

class BinTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_FACILITY,
		Crib::TABLE_NAME,
		Bin::TABLE_NAME,
	);


	public function testSave() {
		$bin = new Bin($this->db);
		$bin->setNumber('3');
		$bin->setCribId('2');
		$bin->setCapacity('1');
		$bin->setSize('3');
		$bin->setType('3');
		
		$r = $bin->save();
		$expectedBinId = 3;
		
		$this->assertEquals($expectedBinId, $r);
		
		$sql = "SELECT * FROM ".Bin::TABLE_NAME." WHERE id = {$expectedBinId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);
		$expectedBin = new Bin($this->db);
		$expectedBin->initByArray($row);
		$this->assertEquals($expectedBin, $bin);
		
		//UPDATE
		$bin->setSize(9);
		$bin->save();
		
		$sql = "SELECT * FROM ".  Bin::TABLE_NAME." WHERE id = {$expectedBinId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedUpdatedBin = new Bin($this->db);
		$expectedUpdatedBin->initByArray($row);
		$this->assertEquals($expectedUpdatedBin, $bin);
	}
	
	public function testGetCrib() {
		$binId = 1;
		$bin = new Bin($this->db, $binId);
		$crib = $bin->getCrib();
		$this->assertInstanceOf('\VWM\Entity\Crib\Crib', $crib);	
		
		$expectedCrib = new Crib($this->db, 1);
		$this->assertEquals($expectedCrib, $crib);
	}
}

?>
