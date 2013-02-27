<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Test\DbTestCase;

class SpentTimeGaugeTest extends DbTestCase {

	protected $fixtures = array(
        TB_UNITTYPE,
        TB_FACILITY,
		TB_DEPARTMENT,
		TB_USAGE,
        Gauge::TABLE_NAME,
	);

	public function testGetCurrentUsage() {
		$productTime = new SpentTimeGauge($this->db);
		$productTime->setFacilityId(9999);
		$productTime->setDepartmentId(999);
		$this->assertFalse($productTime->load());

        $realTimeGauge = new SpentTimeGauge($this->db);
        $realTimeGauge->setDepartmentId(1);
        $realTimeGauge->setFacilityId(1);
        $this->assertTrue($realTimeGauge->load());

        // 10 minutes is 0.17 of an hour
        $expectedTime = 0.17;
		$result = $realTimeGauge->getCurrentUsage();
		$this->assertEquals($expectedTime, $result);

        $annualTimeGauge = new SpentTimeGauge($this->db);
        $annualTimeGauge->setDepartmentId(2);
        $annualTimeGauge->setFacilityId(2);
        $this->assertTrue($annualTimeGauge->load());

        $annualUsage = $annualTimeGauge->getCurrentUsage();
        
	}

}

?>