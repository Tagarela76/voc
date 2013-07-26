<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Company;

class AutomotiveWorkOrderTest extends DbTestCase
{

	protected $fixtures = array(
        Company::TABLE_NAME,
        Facility::TABLE_NAME,
        ProcessTemplate::TABLE_NAME,
		TB_WORK_ORDER
	);


	public function testSave() {
        $automotiveWO = new AutomotiveWorkOrder($this->db);
        $automotiveWO->setCustomerName("Tom Smith");
        $automotiveWO->setDescription("test wo");
        $automotiveWO->setFacilityId("1");
        $automotiveWO->setNumber("wo nubmer");
        $automotiveWO->setStatus("in progress");
        $automotiveWO->setVin("440");
        $automotiveWO->setCreationTime('1365064587');
		$r = $automotiveWO->save();
		$expectedId = 5;
		$this->assertEquals($expectedId, $r);

		$sql = "SELECT * FROM ". TB_WORK_ORDER ." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());

		$row = $this->db->fetch_array(0);
		$expectedWO = new AutomotiveWorkOrder($this->db);
		$expectedWO->initByArray($row);
		$this->assertInstanceOf('VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder', $expectedWO);
		$this->assertEquals($expectedWO, $automotiveWO);

		//UPDATE
        $automotiveWOUpdated = new AutomotiveWorkOrder($this->db, $expectedId);
		$automotiveWOUpdated->setVin("111");
		$automotiveWOUpdated->save();
        $automotiveWOUpd = new AutomotiveWorkOrder($this->db, $expectedId);
		$updatedVin = $automotiveWOUpd->getVin();
		$this->assertEquals("111", $updatedVin);
		$sql = "SELECT * FROM ".  TB_WORK_ORDER." WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$expectedWO = new AutomotiveWorkOrder($this->db);
		$expectedWO->initByArray($row);
		$this->assertEquals($expectedWO, $automotiveWOUpdated);
	}

}

?>
