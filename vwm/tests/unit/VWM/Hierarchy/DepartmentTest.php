<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;
use VWM\Apps\Process\ProcessTemplate;

class DepartmentTest extends DbTestCase {

	public $fixtures = array(
		TB_UNITTYPE,
		Company::TABLE_NAME,
		Facility::TABLE_NAME,
		Department::TABLE_NAME,
		QtyProductGauge::TABLE_NAME,
        ProcessTemplate::TABLE_NAME,
		TB_WORK_ORDER,
		TB_WO2DEPARTMENT,
		TB_PFP,
		TB_PFP_TYPES,
		\PfpTypes::TB_PFP_2_DEPARTMENT,
        TB_PFP2COMPANY,
        TB_SUPPLIER,
        TB_PRODUCT,
        TB_PFP2PRODUCT,
		TB_DEFAULT,
		TB_TYPE,
		TB_UNITCLASS,
        TB_USAGE,

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

    /**
     * Gyant Pre Formulated Products
     */
    public function testGetPfpsGyant()
    {
        $department = new Department($this->db, 1);
        $pfps = $department->getPfpsGyant();

        // company 1 has 2 available pfps
        $this->assertCount(2, $pfps);
        $newPfp = new \VWM\Apps\WorkOrder\Entity\Pfp();
        $newPfp->setId(1);
        $newPfp->load();
        $this->assertEquals($pfps[0]->getId(), $newPfp->getId());
        $this->assertEquals($pfps[0]->getDescription(), $newPfp->getDescription());
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
	public function testGetCountMix(){
		$department = new Department($this->db, 1);
		$mixCount = $department->getCountMix();
		$this->assertEquals($mixCount, 10);
	}
	public function testGetMixList(){
		$departmentId =1;

        $query = "SELECT m.*, wo.id, wo.customer_name, wo.description woDescription, wo.vin ".
                 "FROM ".TB_USAGE." m  ".
                 "LEFT JOIN ".TB_WORK_ORDER." wo ON m.wo_id = wo.id ".
                 "LEFT JOIN ".TB_WO2DEPARTMENT." j ON m.wo_id=j.wo_id ".
                 "WHERE (m.department_id ={$departmentId} OR j.department_id={$departmentId}) ".
                 "AND  TRUE   GROUP BY m.mix_id ORDER BY m.mix_id DESC ";
        $this->db->query($query);
		$rows = $this->db->fetch_all_array();
		$department = new Department($this->db, $departmentId);
		$mixList = $department->getMixList();
		$this->assertEquals($mixList[0]->getDepartmentId(), $rows[0]['department_id']);
        $this->assertEquals($mixList[0]->getDescription(), $rows[0]['description']);

	}


	public function testGetPfpTypes() {
		$departmentId = 1;
		$department = new Department($this->db, $departmentId);

		$pfpTypes = $department->getPfpTypes();
		$this->assertTrue(is_array($pfpTypes));

		$sql = "SELECT * FROM ".\PfpTypes::TB_PFP_2_DEPARTMENT." " .
				"WHERE department_id = {$departmentId}";
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		$expectedPfpTypes = array();
		foreach ($rows as $row) {
			$expectedPfpTypes[] = new \PfpTypes($this->db, $row['pfp_type_id']);
		}

		$this->assertEquals($expectedPfpTypes[0], $pfpTypes[0]);
	}

	public function testGetUnitTypes(){
		// test gettint department Unit type
		$departmentUnitType = array(1,2,3);
		$unitTypeClass = 'USAWght';
		$categoty = 'department';
		$departmentId = 1;

		$department = new Department($this->db, $departmentId);
		$department->setUnitTypeClass($unitTypeClass);
		$unittype = new \Unittype($this->db);
		$unittype->setDefaultCategoryUnitTypelist($departmentUnitType, $categoty, $departmentId);
		$departmentUnitTypes = $department->getUnitTypeList();

		//get unit type
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				"ut.unittype_desc, ut.system " .
				"FROM " . TB_UNITTYPE . " ut " .
				"INNER JOIN " . TB_TYPE . " t " .
				"ON ut.type_id = t.type_id " .
				"INNER JOIN " . TB_DEFAULT . " def " .
				"ON ut.unittype_id = def.id_of_subject " .
				"INNER JOIN " . TB_UNITCLASS . " uc " .
				"ON ut.unit_class_id = uc.id " .
				"WHERE def.object = 'department' " .
				"AND def.id_of_object = {$this->db->sqltext($departmentId)} " .
				"AND def.subject = 'unittype' ".
				"ORDER BY ut.unittype_id";

		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unitType = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system
				);
				$unittypes[] = $unitType;
			}
		}
		$this->assertEquals(count($unittypes), count($departmentUnitTypes));
		$this->assertEquals($unittypes[0]['type_id'], $departmentUnitTypes[0]->getTypeId());
        $this->assertEquals($unittypes[0]['unittype_id'], $departmentUnitTypes[0]->getUnitTypeId());
        $this->assertEquals($unittypes[0]['unittype_desc'], $departmentUnitTypes[0]->getUnitTypeDesc());
        $this->assertEquals($unittypes[0]['system'], $departmentUnitTypes[0]->getSystem());

		//test getting facility unittype
		$facilityUnitType = array(4,9);
		$categoty = 'facility';
		$facilityId = 1;
		$departmentId = 2;
		$unitTypeClass = 'MetricVlm';
		$department = new Department($this->db, $departmentId);
		$department->setUnitTypeClass($unitTypeClass);

		$unittype->setDefaultCategoryUnitTypelist($facilityUnitType, $categoty, $facilityId);
		$department->setFacilityId($facilityId);
		$departmentUnitTypes = $department->getUnitTypeList();

		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				"ut.unittype_desc, ut.system " .
				"FROM " . TB_UNITTYPE . " ut " .
				"INNER JOIN " . TB_TYPE . " t " .
				"ON ut.type_id = t.type_id " .
				"INNER JOIN " . TB_DEFAULT . " def " .
				"ON ut.unittype_id = def.id_of_subject " .
				"INNER JOIN " . TB_UNITCLASS . " uc " .
				"ON ut.unit_class_id = uc.id " .
				"WHERE def.object = 'facility' " .
				"AND def.id_of_object = {$this->db->sqltext($facilityId)} " .
				"AND def.subject = 'unittype' ".
				"ORDER BY ut.unittype_id";

		$this->db->query($query);
		$unittypes = array();
		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unitType = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system
				);
				$unittypes[] = $unitType;
			}
		}
		$this->assertEquals(count($unittypes), count($departmentUnitTypes));
		$this->assertEquals($unittypes[0]['type_id'], $departmentUnitTypes[0]->getTypeId());
        $this->assertEquals($unittypes[0]['unittype_id'], $departmentUnitTypes[0]->getUnitTypeId());
        $this->assertEquals($unittypes[0]['unittype_desc'], $departmentUnitTypes[0]->getUnitTypeDesc());
        $this->assertEquals($unittypes[0]['system'], $departmentUnitTypes[0]->getSystem());

		//test getting company unittype
		$companyUnitType = array(38,39);
		$categoty = 'company';
		$unitTypeClass = 'Time';
		$departmentId = 3;
		$facilityId = 2;
		$companyId = 1;

		$department = new Department($this->db, $departmentId);
		$department->setUnitTypeClass($unitTypeClass);

		$unittype->setDefaultCategoryUnitTypelist($companyUnitType, $categoty, $companyId);
		$department->setFacilityId($facilityId);
		$department->getFacility()->setCompanyId($companyId);
		$companyUnitTypes = $department->getUnitTypeList();

		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				"ut.unittype_desc, ut.system " .
				"FROM " . TB_UNITTYPE . " ut " .
				"INNER JOIN " . TB_TYPE . " t " .
				"ON ut.type_id = t.type_id " .
				"INNER JOIN " . TB_DEFAULT . " def " .
				"ON ut.unittype_id = def.id_of_subject " .
				"INNER JOIN " . TB_UNITCLASS . " uc " .
				"ON ut.unit_class_id = uc.id " .
				"WHERE def.object = 'company' " .
				"AND def.id_of_object = {$this->db->sqltext($companyId)} " .
				"AND def.subject = 'unittype' ".
				"ORDER BY ut.unittype_id";

		$this->db->query($query);
		$unittypes = array();
		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unitType = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system
				);
				$unittypes[] = $unitType;
			}
		}
		$this->assertEquals(count($unittypes), count($companyUnitTypes));
		$this->assertEquals($unittypes[0]['type_id'], $companyUnitTypes[0]->getTypeId());
        $this->assertEquals($unittypes[0]['unittype_id'], $companyUnitTypes[0]->getUnitTypeId());
        $this->assertEquals($unittypes[0]['unittype_desc'], $companyUnitTypes[0]->getUnitTypeDesc());
        $this->assertEquals($unittypes[0]['system'], $companyUnitTypes[0]->getSystem());

	}
}

?>
