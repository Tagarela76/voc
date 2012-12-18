<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;

class DepartmentTest extends DbTestCase {

	public $fixtures = array(
		TB_UNITTYPE,
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
			$this->assertEquals($row['creater_id'], $department->getCreaterId());
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
		$sql = "SELECT * FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE dw.department_id=1";
		$this->db->query($sql);
		$rowsCount = $this->db->num_rows();
		$department = new Department($this->db, 1);
		
		$countRepairOrderInDepartment = $department->countRepairOrderInDepartment();
		$this->assertEquals($countRepairOrderInDepartment, $rowsCount);
	}
	
	public function testGetRepairOrderInDepartment(){
		$departmentId = 1;
		$sql = "SELECT * FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE dw.department_id=1";
		$this->db->query($sql);
		$departmentRepairOrder = $this->db->fetch_all();
		$department = new Department($this->db, 1);
		
		$departmentList = $department->getRepairOrdersList();
		$this->assertEquals(count($departmentList), 1);
		//$this->assertEquals($departmentList,  $departmentRepairOrder);
		
	}

	public function testGetFacility() {
		$departmentId = 1;
		$department = new Department($this->db, $departmentId);
		$facilityActual = $department->getFacility();
		$facilityExpected = new Facility($this->db, 1);

		$this->assertEquals($facilityExpected, $facilityActual);
	}

	public function testSave(){
		$department = new Department($this->db);
		$department->setFacilityId('1');
		$department->setName('Test Name');
		$department->setShareWo('1');
		$department->setVocLimit('100.00');
		$department->setVocAnnualLimit('100.00');
		$department->setCreaterId('1');
		
		
		$expectedId = 4;
		$result = $department->save();
		$this->assertEquals($expectedId, $result);
		
		$sql = "SELECT * FROM ".TB_DEPARTMENT." WHERE department_id = {$result}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$departmentActual = new Department($this->db);
		$departmentActual->initByArray($row);
		$this->assertEquals($department, $departmentActual);
		
		
		//check update
		
		$department->setCreaterId('2');
		$department->setShareWo('0');
		$result=$department->save();
		
		$newDepartment = new Department($this->db, 4);
		$this->assertEquals($newDepartment->getShareWo(), 0);
		$this->assertEquals($newDepartment->getCreaterId(), 2);
		
	}
	
	public function testGetMixList(){
		$departmentId =1;
		$query = "SELECT * FROM ". TB_USAGE ." m ".
				"LEFT JOIN ". TB_WO2DEPARTMENT ." j ON m.wo_id=j.wo_id ".
				"WHERE m.department_id =".$departmentId." ".
				"OR j.department_id=".$departmentId." ".
				"GROUP BY mix_id";
		$rows = $this->db->fetch_all_array();
		$this->db->query($query);
		$department = new Department($this->db, 1);
		$mixList = $department->getMixList();
		$this->assertEquals($mixList, $rows);
	}
}

?>
