<?php

namespace VWM\WorkOrder;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder;

class IndustrialWorkOrderTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_INDUSTRY_TYPE, TB_WORK_ORDER
	);


	public function testSave() {		
		
        $industrialWO = new IndustrialWorkOrder($this->db);
        $industrialWO->setCustomer_name("Tom Smith");
        $industrialWO->setDescription("test wo");
        $industrialWO->setFacility_id("1");
        $industrialWO->setIndustry_type("3");
        $industrialWO->setNumber("wo nubmer");
        $industrialWO->setStatus("in progress");
		$r = $industrialWO->save();
		$expectedId = 5;
		$this->assertEquals($expectedId, $r);
		
		$sql = "SELECT * FROM ". TB_WORK_ORDER ." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);
		$expectedWO = new IndustrialWorkOrder($this->db);
		$expectedWO->initByArray($row);
		$this->assertInstanceOf('VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder', $expectedWO);
		$this->assertEquals($expectedWO, $industrialWO);
		
		//UPDATE
        $industrialWOUpdated = new IndustrialWorkOrder($this->db, $expectedId);
		$industrialWOUpdated->setCustomer_name("Lukas Smith");
		$industrialWOUpdated->save();
		$updatedCustomer = $industrialWOUpdated->getCustomer_name();
		$this->assertEquals("Lukas Smith", $updatedCustomer);
		$sql = "SELECT * FROM ".  TB_WORK_ORDER." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedWO = new IndustrialWorkOrder($this->db);
		$expectedWO->initByArray($row);
		$this->assertEquals($expectedWO, $industrialWOUpdated);
	}
	
}

?>
