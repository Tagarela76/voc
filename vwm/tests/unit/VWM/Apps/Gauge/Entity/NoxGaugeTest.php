<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Test\DbTestCase;
use VWM\Hierarchy\Company;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Department;

class NoxGaugeTest extends DbTestCase {

	public $fixtures = array(
		TB_UNITTYPE,
        Company::TABLE_NAME,
        Facility::TABLE_NAME,
		Department::TABLE_NAME,
        Gauge::TABLE_NAME,
	);

	public function testSave() {
		//	insert
		$noxGauge = new NoxGauge($this->db);

		$noxGauge->setFacilityId(1);
		$noxGauge->setDepartmentId(1);
		$noxGauge->setLimit(50);
		$noxGauge->setPeriod(Gauge::PERIOD_ANNUALLY);
		$noxGauge->setUnitType(1);

		$expectedId = 4;
		$this->assertEquals($expectedId, $noxGauge->save());

		$sql = "SELECT * FROM " . Gauge::TABLE_NAME . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$noxGaugeActual = new NoxGauge($this->db);
		$noxGaugeActual->initByArray($row);		
		$this->assertEquals($noxGauge, $noxGaugeActual);
		
		// update
		$noxGauge->setLimit(70);
		$this->assertEquals($expectedId, $noxGauge->save());		
		$sql = "SELECT * FROM " . Gauge::TABLE_NAME . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$noxGaugeActual = new NoxGauge($this->db);
		$noxGaugeActual->initByArray($row);		
		$this->assertEquals($noxGauge, $noxGaugeActual);

	}

	public function testGetCurrentUsage() {
		
	}
}

?>
