<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Gauge\Entity\Gauge;

class DepartmentTest extends DbTestCase {

	public $fixtures = array(
		Company::TABLE_NAME,
		Facility::TABLE_NAME,
		Department::TABLE_NAME,
	);

	public function testInitByArray() {
		$sql = "SELECT * FROM ".Department::TABLE_NAME." WHERE facility_id = 1";
		$this->db->query($sql);

		$expectedRowCount = 2;
		$this->assertEquals($expectedRowCount, $this->db->num_rows());

		$rows = $this->db->fetch_all_array();
		foreach ($rows as $row) {
			$department = new Department($this->db);
			$department->initByArray($row);
			
			$this->assertEquals($row['department_id'], 
					$department->getDepartmentId());
			$this->assertEquals($row['name'], $department->getName());
			$this->assertEquals($row['facility_id'], $department->getFacilityId());
			$this->assertEquals($row['creator_id'], $department->getCreatorId());
			$this->assertEquals($row['voc_limit'], $department->getVocLimit());
			$this->assertEquals($row['voc_annual_limit'],
					$department->getVocAnnualLimit());
		}		
	}


	public function testGetGauge() {
		$department = new Department($this->db, 1);// first department
		$timeGauge = $department->getGauge(Gauge::TIME_GAUGE);
		$this->assertInstanceOf('VWM\Apps\Gauge\Entity\Gauge\TimeGauge',
				$timeGauge);
		// more assests
	}


	public function testGetAllAvailableGauges() {
		
	}
}

?>
