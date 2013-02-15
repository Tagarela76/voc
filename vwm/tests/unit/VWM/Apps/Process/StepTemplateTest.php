<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class StepTemplateTest extends DbTestCase {

	const TB_STEP = 'step_template';
	const TB_PROCESS='process_template';
	const TB_RESOURCE = 'resource_template';
	const TB_UNITTYPE = 'unittype';
	const TB_STEP_INSTANCE = 'step_instance';
	
	public $fixtures = array(
		self::TB_PROCESS, self::TB_STEP, self::TB_RESOURCE, self::TB_UNITTYPE
	);

	
	public function testGetResources() {
		$stepId = 1;
		$step = new StepTemplate($this->db, $stepId);
		$resources = $step->getResources();

		$sql = "SELECT * FROM " . self::TB_RESOURCE .
				" WHERE step_id = ".$stepId;
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
		$stepId = 1;
		$step = new StepTemplate($this->db, $stepId);
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
	
	public function testSave(){
		$processId = 1;
		$stepNumber= 1;
		$step = new StepTemplate($this->db);
		$step->setProcessId($processId);
		$step->setNumber($stepNumber);
		$step->setDescription('description');
		
		$stepID = $step->save();
		
		$sql = "SELECT * FROM ".self::TB_STEP." ".
				"WHERE id=".$stepID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		$this->assertEquals($step->getId(), $result[0]['id']);
		$this->assertEquals($step->getNumber(), $result[0]['number']);
		$this->assertEquals($step->getProcessId(), $result[0]['process_id']);
		
		
		//test Update
		$newProcessId = 2;
		$newNumber= 2;
		$step->setProcessId($newProcessId);
		$step->setNumber($newNumber);
		$step->save();
		
		$sql = "SELECT * FROM ".self::TB_STEP." ".
				"WHERE id=".$stepID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		
		$this->assertEquals($step->getId(), $result[0]['id']);
		$this->assertEquals($step->getNumber(), $result[0]['number']);
		$this->assertEquals($step->getProcessId(), $result[0]['process_id']);
	}
	
	public function testCreateInstanceStep(){
		$stepTemplateId = 1;
		//$stepProcessInstance = 1;
		$stepTemplate = new StepTemplate($this->db, $stepTemplateId);
		$stepInstanse = $stepTemplate->createInstanceStep($stepTemplate->getProcessId());
		$stepInstanse->save();
		$sql = "SELECT * FROM " . self::TB_STEP_INSTANCE . " " .
				"WHERE id=".$stepInstanse->getId();
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();

		$this->assertEquals($stepInstanse->getId(), $result[0]['id']);
		$this->assertEquals($stepInstanse->getNumber(), $result[0]['number']);
		$this->assertEquals($stepInstanse->getDescription(), $result[0]['description']);
		$this->assertEquals($stepInstanse->getOptional(), $result[0]['optional']);
		$this->assertEquals($stepInstanse->getProcessId(), $result[0]['process_id']);
		
	}
	
}

?>
