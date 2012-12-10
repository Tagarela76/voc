<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Test\DbTestCase;

class SpentTimeGaugeTest extends DbTestCase {

	protected $fixtures = array(
		TB_DEPARTMENT,
		TB_USAGE,
		
	);

	public function testGetCurrentUsage() {
		$productTime = new SpentTimeGauge($this->db);
		$productTime->setFacilityId("2");
		$productTime->setDepartmentId("2");
		$productTime->load();
		//$productTime->setGaugeType(Gauge::TIME_GAUGE);
		
		$expectedTime=10;
		$result = $productTime->getCurrentUsage();
		$this->assertEquals($expectedTime, $result);
	}

}

?>