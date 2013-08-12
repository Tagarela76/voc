<?php
namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Company;

class IndustrialWorkOrderTest extends DbTestCase
{

	const TB_PROCESS_INSTANCE = 'process_instance';
    
	protected $fixtures = array(
        Company::TABLE_NAME,
        Facility::TABLE_NAME,
        ProcessTemplate::TABLE_NAME,
		TB_WORK_ORDER,
		self::TB_PROCESS_INSTANCE
	);


	public function testSave() {
        $industrialWO = new IndustrialWorkOrder($this->db);
        $industrialWO->setCustomerName("Tom Smith");
        $industrialWO->setDescription("test wo");
        $industrialWO->setFacilityId("1");
        $industrialWO->setNumber("wo nubmer");
        $industrialWO->setStatus("in progress");
        $industrialWO->setCreationTime('1361603686');
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
		$industrialWOUpdated->setCustomerName("Lukas Smith");
		$industrialWOUpdated->save();
		$updatedCustomer = $industrialWOUpdated->getCustomerName();
		$this->assertEquals("Lukas Smith", $updatedCustomer);
		$sql = "SELECT * FROM ".  TB_WORK_ORDER." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedWO = new IndustrialWorkOrder($this->db);
		$expectedWO->initByArray($row);
		$this->assertEquals($expectedWO, $industrialWOUpdated);
	}

	public function testGetProcessInstance(){
		$woId = 1;
		$industrialWOUpdated = new IndustrialWorkOrder($this->db, $woId);
		$processInstance = $industrialWOUpdated->getProcessInstance();

		$sql = "SELECT * FROM ".self::TB_PROCESS_INSTANCE." ".
			   "WHERE work_order_id = ".$woId;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		$this->assertEquals($processInstance->getId(), $result[0]['id']);
		$this->assertEquals($processInstance->getName(), $result[0]['name']);
		$this->assertEquals($processInstance->getFacilityId(), $result[0]['facility_id']);
		$this->assertEquals($processInstance->getWorkOrderId(), $result[0]['work_order_id']);

	}

}

?>
