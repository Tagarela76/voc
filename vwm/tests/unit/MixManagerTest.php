<?php

class MixManagerTest extends DbTestCase {

	protected $fixtures = array(
		'department', 'mix'
	);

	public function testCountMixes() {
		$mixManager = new MixManager($this->db);
		$mixCount = $mixManager->countMixes();

		//	we did not set departmentID
		$this->assertTrue($mixCount === false);

		$mixManager->departmentID = 1;
		$mixCount = $mixManager->countMixes();
		$this->assertTrue($mixCount === 4);

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
		$mixManager = new MixManager($this->db);
		$mixCount = $mixManager->countMixesInFacility(1);
		$this->assertTrue($mixCount === 5);

		//	now let's test filter
		$filter = ' description LIKE \'%WO12%\' ';
		$mixCount = $mixManager->countMixesInFacility(1, $filter);
		$this->assertTrue($mixCount === 3);

		//	test search criteria
		$mixManager->searchCriteria[] = 'WO';
		$mixCount = $mixManager->countMixesInFacility(1);
		$this->assertTrue($mixCount === 3);
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

		//	test search criteria
		$mixManager->searchCriteria[] = 'WO';
		$mixList = $mixManager->getMixListInFacility(1);
		$this->assertTrue(count($mixList) === 3);

	}

}