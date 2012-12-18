<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;

class DepartmentTest extends DbTestCase {

	public $fixtures = array(
		Company::TABLE_NAME,
		Facility::TABLE_NAME,
		Department::TABLE_NAME,
		QtyProductGauge::TABLE_NAME,
		TB_WORK_ORDER,
		TB_WO2DEPARTMENT,
	);

	public function testInitByArray() {
		$sql = "SELECT * FROM " . Department::TABLE_NAME . " WHERE facility_id = 1";
		$this->db->query($sql);

		$expectedRowCount = 2;
		$this->assertEquals($expectedRowCount, $this->db->num_rows());

		$rows = $this->db->fetch_all_array();
		foreach ($rows as $row) {
			$department = new Department($this->db);
			$department->initByArray($row);

			$this->assertEquals($row['department_id'], $department->getDepartmentId());
			$this->assertEquals($row['name'], $department->getName());
			$this->assertEquals($row['facility_id'], $department->getFacilityId());
			$this->assertEquals($row['creator_id'], $department->getCreatorId());
			$this->assertEquals($row['voc_limit'], $department->getVocLimit());
			$this->assertEquals($row['voc_annual_limit'], $department->getVocAnnualLimit());
		}
	}

	public function testGetGauge() {
		$sql = "SELECT * FROM " . QtyProductGauge::TABLE_NAME . " WHERE id=1";
		$this->db->query($sql);

		$expectedRowCount = 1;
		$this->assertEquals($expectedRowCount, $this->db->num_rows());

		$row = $this->db->fetch_array(0);

		$department = new Department($this->db, 1); // first department
		$timeGauge = $department->getGauge(Gauge::TIME_GAUGE);
		$this->assertInstanceOf('\VWM\Apps\Gauge\Entity\SpentTimeGauge', $timeGauge);
		// more assests
		$this->assertEquals($timeGauge->getGaugeType(), $row['gauge_type']);
		$this->assertEquals($timeGauge->getFacilityId(), $row['facility_id']);
		$this->assertEquals($timeGauge->getDepartmentId(), $row['department_id']);
		$this->assertEquals($timeGauge->getLimit(), $row['limit']);
	}

	public function testGetAllAvailableGauges() {
		$sql = "SELECT * FROM " . QtyProductGauge::TABLE_NAME . " WHERE department_id=1 AND `limit` <> 0";
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		$rowsCount = $this->db->num_rows();
		$gaugeTypes=array();
		
		foreach($rows as $row){
			$gaugeTypes[] = $row['gauge_type'];
		}
		
		$department = new Department($this->db, 1);
		$allGauges = $department->getAllAvailableGauges();
		$allGaugesCount = count($allGauges);
		
		$this->assertEquals($allGaugesCount, $rowsCount);
		foreach ($allGaugesCount as $key => $value){
			$this->assertTrue(in_array($value, $gaugeTypes));
		}
		
		
	}
	
	public function testCountRepairOrderInDepartment(){
		$departmentId = 1;
		$sql = "SELECT * repairOrderCount FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE department_id=1";
		$this->db->query($sql);
		$rowsCount = $this->db->num_rows();
		$department = new Department($this->db, 1);
		$countRepairOrderInDepartment = $department->countRepairOrderInDepartment();
		$this->assertEquals($countRepairOrderInDepartment, $rowsCount);
	}
	
	public function testGetRepairOrderInDepartment(){
		$departmentId = 1;
		$sql = "SELECT * repairOrderCount FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE department_id=1";
		$this->db->query($sql);
		$departmentRepairOrder = $this->db->fetch_all_array();
		$department = new Department($this->db, 1);
		
		$departmentList = $department->getRepairOrdersList();
		$this->assertEquals(count($departmentList), 1);
	}

}

?>
