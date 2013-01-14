<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class StepTest extends DbTestCase {

	public $fixtures = array(
		TB_PROCESS, TB_STEP, TB_RESOURCE, TB_UNITTYPE
	);

	const TB_RESOURCE = 'resource';
	public function testGetResources() {
		$StepId = 1;
		$step = new Step($this->db, $StepId);
		$resources = $step->getResources();

		$sql = "SELECT * FROM " . self::TB_RESOURCE .
				" WHERE step_id = ".$StepId;
		$this->db->query($sql);

		$result = $this->db->fetch_all_array();

		$count = count($result);

		$this->assertEquals($count, count($resources));

		for ($i = 0; $i < $count; $i++) {
			$this->assertEquals($resources[$i]->getDescription(), $result[$i]['description']);
			$this->assertEquals($resources[$i]->getRate(), $result[$i]['rate']);
			$this->assertEquals($resources[$i]->getQty(), $result[$i]['qty']);
			$this->assertEquals($resources[$i]->getUnittypeId(), $result[$i]['unittype_id']);
			$this->assertEquals($resources[$i]->getResourceTypeId(), $result[$i]['resource_type_id']);
			$this->assertEquals($resources[$i]->getLaborCost(), $result[$i]['labor_cost']);
			$this->assertEquals($resources[$i]->getMaterialCost(), $result[$i]['material_cost']);
			$this->assertEquals($resources[$i]->getTotalCost(), $result[$i]['total_cost']);
			$this->assertEquals($resources[$i]->getRateUnittypeId(), $result[$i]['rate_unittype_id']);
			$this->assertEquals($resources[$i]->getRateQty(), $result[$i]['rate_qty']);
			$this->assertEquals($resources[$i]->getStepId(), $result[$i]['step_id']);
			
		}
	}
	
	public function testGetTotalSpentTime() {
		$StepId = 1;
		$step = new Step($this->db, $StepId);
		$totalSpentTime = $step->getTotalSpentTime();
		
		$sql = "SELECT qty FROM ".self::TB_RESOURCE." WHERE unittype_id IN (38,39,40)";
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		$totalTime=0;
		
		foreach ($result as $time){
			$totalTime+=$time['qty'];
		}
		$this->assertEquals($totalTime, $totalSpentTime);
		
	}

	
}

?>
