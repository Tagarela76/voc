<?php
use VWM\Framework\Test as Testing;

class MixManagerTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_FACILITY, TB_DEPARTMENT, TB_WORK_ORDER, TB_USAGE
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
				"FROM ".TB_USAGE." m " .
				"JOIN ".TB_DEPARTMENT." d ON m.department_id = d.department_id " .
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
		$this->assertTrue(count($mixList) == 5);
		$this->assertTrue($mixList[3] instanceof MixOptimized);
		
		$this->assertEquals($mixList[3]->getRepairOrder()->customer_name, "joh smith");

		//	test search criteria
		$mixManager->searchCriteria[] = 'WO';
		$mixList = $mixManager->getMixListInFacility(1);
		$this->assertTrue(count($mixList) === 3);

	}


	public function testGetMixListInDepartment() {
		
	}

}