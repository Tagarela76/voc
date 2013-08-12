<?php

use VWM\Framework\Test as Testing;
use VWM\Apps\Process\ProcessTemplate;

class MixManagerTest extends Testing\DbTestCase
{

	protected $fixtures = array(
		TB_COMPANY,
        TB_FACILITY,
        TB_DEPARTMENT,
        ProcessTemplate::TABLE_NAME,
        TB_WORK_ORDER,
        TB_USAGE, TB_WO2DEPARTMENT
	);

	public function testCountMixes() {
		$mixManager = new MixManager($this->db);
		$mixCount = $mixManager->countMixes();

		//	we did not set departmentID
		$this->assertTrue($mixCount === false);

		$mixManager->departmentID = 1;
		$mixCount = $mixManager->countMixes();
		$this->assertEquals(4, $mixCount);

		//	now let's test filter
		$filter = ' description LIKE \'%WO12%\' ';
		$mixCount = $mixManager->countMixes($filter);
		$this->assertTrue($mixCount === 2);

		//	test search criteria
		$mixManager->searchCriteria[] = '124';
		$mixCount = $mixManager->countMixes();
		$this->assertTrue($mixCount === 1);
	}

	public function testCountMixesInFacility() {

		$facilityId = 1;
		$sql = "SELECT COUNT(*) cnt " .
				"FROM " . TB_USAGE . " m " .
				"JOIN " . TB_DEPARTMENT . " d ON m.department_id = d.department_id " .
				"WHERE d.facility_id = {$facilityId}";
		$this->db->query($sql);
		$expectedCount = $this->db->fetch(0)->cnt;
		$mixManager = new MixManager($this->db);
		$mixCount = $mixManager->countMixesInFacility($facilityId);
		$this->assertEquals($expectedCount, $mixCount);

		//	now let's test filter
		$filter = ' description LIKE \'%WO12%\' ';
		$mixCount = $mixManager->countMixesInFacility($facilityId, $filter);
		$this->assertEquals(4, $mixCount);

		//	test search criteria
		$mixManager->searchCriteria[] = 'WO';
		$mixCount = $mixManager->countMixesInFacility($facilityId);
		$this->assertEquals(4, $mixCount);
	}

	public function testGetMixList() {
		$mixManager = new MixManager($this->db);
		$mixList = $mixManager->getMixList();
		//	we did not set departmentID
		$this->assertTrue($mixList === false);

		$mixManager->departmentID = 666;
		$mixList = $mixManager->getMixList();
		//	no mixes for this department
		$this->assertTrue($mixList === false);

		$mixManager->departmentID = 1;
		$mixList = $mixManager->getMixList();
		$this->assertTrue(is_array($mixList));
		$this->assertTrue(count($mixList) == 4);
		$this->assertTrue($mixList[3] instanceof MixOptimized);
	}

	public function testGetMixListInFacility() {
		$mixManager = new MixManager($this->db);
		$mixList = $mixManager->getMixListInFacility(1);
		$this->assertTrue(is_array($mixList));

		$this->assertEquals(count($mixList), 6);
		$this->assertTrue($mixList[3] instanceof MixOptimized);

		$this->assertEquals($mixList[3]->getRepairOrder()->customer_name, "joh smith");

		//	test search criteria
		$mixManager->searchCriteria[] = 'WO';
		$mixList = $mixManager->getMixListInFacility(1);
		$this->assertEquals(count($mixList), 4);
	}

	public function testGetMixListInDepartment() {
		$departmentId = 1;
		$sql = "SELECT * FROM ".TB_USAGE.
			" WHERE `department_id` =".$departmentId.
			" OR  `wo_id` IN (SELECT  `wo_id` FROM " .TB_WO2DEPARTMENT ." WHERE `department_id` =".$departmentId.")";

		$this->db->query($sql);
		$expectedMixList = $this->db->fetch_all_array();

		$mixManager = new MixManager($this->db);
		$mixList = $mixManager->getMixListInDepartment($departmentId);

		$this->assertEquals(count($mixList), count($expectedMixList));
		$this->assertEquals($mixList[0]['mix_id'], $expectedMixList[0]['mix_id']);

	}

}