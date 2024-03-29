<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;

class FacilityTest extends DbTestCase {
	
	protected $fixtures = array(
		Company::TABLE_NAME,
		Facility::TABLE_NAME,
		Department::TABLE_NAME,
		Facility::TB_PROCESS,
		TB_DEFAULT,
		TB_TYPE,
		TB_UNITCLASS,
		TB_UNITTYPE,
	);
	
	public function testSave() {
		$facility = new Facility($this->db);
		$facility->setAddress('Zaporizhske Drive, 009');
		$facility->setCity('Dnipro');
		$facility->setClientFacilityId('RB003');
		$facility->setCompanyId('1');
		$facility->setContact('Semen');
		$facility->setCountry('2');
		$facility->setCounty('region');
		$facility->setCreaterId('1');
		$facility->setEmail('denis.foo@blah.com');			
		$facility->setEpa('124A');
		$facility->setFax('99-999');
		$facility->setGcgId('1');
		$facility->setMonthlyNoxLimit('60.00');
		$facility->setName('Zoo Facility');
		$facility->setPhone('555-55-55');
		$facility->setState('Nevada');
		$facility->setTitle('Mr');
		$facility->setVocAnnualLimit('9999.00');
		$facility->setVocLimit('677.00');
		$facility->setZip('55555');		
				
		$expectedId = 3;

		$result = $facility->save();
		$this->assertEquals($expectedId, $result);	// last id
		
		$sql = "SELECT * FROM ".TB_FACILITY." WHERE facility_id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$facilityActual = new Facility($this->db);
		$facilityActual->initByArray($row);
		$this->assertEquals($facility, $facilityActual);
		
		
		//	now test do not set client facility id
		$facilityWithoutClientId = clone $facility;
		$facilityWithoutClientId->setFacilityId('');
		$facilityWithoutClientId->setClientFacilityId('');		
		
		$newExpectedId = 4;
		
		$resultWithoutId = $facilityWithoutClientId->save();
		$this->assertEquals($newExpectedId, $resultWithoutId);
		
		$sql = "SELECT * FROM ".TB_FACILITY." WHERE facility_id = {$newExpectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$facilityWithoutClientIdActual = new Facility($this->db);
		$facilityWithoutClientIdActual->initByArray($row);
		$this->assertEquals($facilityWithoutClientId, $facilityWithoutClientIdActual);			
		
		
		//	now check update
		$newClientFacilityId = 'NEWONE';
		$facilityWithoutClientId->setClientFacilityId($newClientFacilityId);
		$id = $facilityWithoutClientId->save();
		$this->assertEquals($facilityWithoutClientId->getFacilityId(), $id);
		
		$sql = "SELECT client_facility_id FROM ".TB_FACILITY." " .
				"WHERE facility_id = {$newExpectedId} " .
				"AND client_facility_id = '{$newClientFacilityId}'";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
	
	}


	public function testGetCompany() {
		$facility = new Facility($this->db, 1);
		$this->assertEquals($facility->getCompany(), new Company($this->db, 1));
	}


	public function testGetDepartments() {
		$facility = new Facility($this->db, 1);
		$departments = $facility->getDepartments();

		$this->assertCount(2, $departments);
		$this->assertEquals(new Department($this->db, 1), $departments[0]);
	}
	
	public function testGetProcessList(){
		$facitilyId = 1;
		$facility = new Facility($this->db, $facitilyId);
		$processList = $facility->getProcessList();
		
		$sql = "SELECT * ".
				"FROM ".Facility::TB_PROCESS.
				" WHERE facility_id=".$facitilyId;
		$this->db->query($sql);
		$processTestList = $this->db->fetch_all();
		
		$count = count($processList);
		for($i=0; $i<$count; $i++){
			$this->assertEquals($processTestList[$i]->name, $processList[$i]->getName());
			$this->assertEquals($processTestList[$i]->id, $processList[$i]->getId());
			$this->assertEquals($processTestList[$i]->facility_id, $processList[$i]->getFacilityId());
		}
		
	}
	
	public function testGetUnitTypes(){
		$facilityUnitType = array(1,2,3);
		$unitTypeClass = 'USAWght';
		$categoty = 'facility';
		$facilityId = 1;
		
		$facility = new Facility($this->db, $facilityId);
		$facility->setUnitTypeClass($unitTypeClass);
		$unittype = new \Unittype($this->db);
		$unittype->setDefaultCategoryUnitTypelist($facilityUnitType, $categoty, $facilityId);
		$facilityUnitTypes = $facility->getUnitTypeList();
		
		//get unit type
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.unit_class_id, ut.system, uc.id, uc.name ucName, " .
				 "uc.description ucDescription " .
				 "FROM " . TB_UNITTYPE ." ut ".
				 "INNER JOIN " . TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = 'facility' ".
				 "AND def.id_of_object = {$this->db->sqltext($facilityId)} ".
				 "AND def.subject = 'unittype' ".
				 "ORDER BY ut.unittype_id";

				 
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch_array($i);

				$unittype = new \VWM\Apps\UnitType\Entity\UnitType();
				$unittype->initByArray($data);

				$type = new \VWM\Apps\UnitType\Entity\Type($this->db);
				$type->initByArray($data);
				$unittype->setType($type);

				$class = new \VWM\Apps\UnitType\Entity\UnitClass($this->db);
				$class->initByArray($data);
				$class->setName($data['ucName']);
				$class->setDescription($data['ucDescription']);
				$unittype->setUnitClass($class);

				$unitTypes[] = $unittype;
			}
		}
		$this->assertEquals(count($unitTypes), count($facilityUnitTypes));
		$this->assertEquals($unitTypes, $facilityUnitTypes);
	}

}

?>
