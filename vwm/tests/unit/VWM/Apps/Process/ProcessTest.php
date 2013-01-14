<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ProcessTest extends DbTestCase {
	
	const TB_STEP = 'step';
	const TB_PROCESS='process';
	
	public $fixtures = array(
		self::TB_PROCESS,
		self::TB_STEP
	);
	
	
	public function testGetSteps() {
		
		$woProcessId = 2;
		$process = new Process($this->db, $woProcessId);
		$steps = $process->getSteps();
		
		$sql = "SELECT * FROM " .  self::TB_STEP .
				" WHERE process_id = 2";
		$this->db->query($sql);
		
		$result = $this->db->fetch_all_array();
		
		$count = count($result);
		
		$this->assertEquals($count, count($steps));
		
		for($i=0;$i<$count;$i++){
			$this->assertEquals($steps[$i]->getNumber(), $result[$i]['number']);
			$this->assertEquals($steps[$i]->getProcessId(), $result[$i]['process_id']);
			$this->assertEquals($steps[$i]->getId(), $result[$i]['id']);
		} 
		//$this->assertEquals($process->getWorkOrderId(), $row['work_order_id']);
	
	}
	
	public function testCurrentStep(){
		$woProcessId = 2;
		$stepNumber = 1;
		$process = new Process($this->db, $woProcessId);
		$process->setCurrentStepNumber($stepNumber);
		$step = $process->getCurrentStep();
		
		$sql = "SELECT * FROM " . self::TB_STEP .
				" WHERE process_id = ".$woProcessId." AND number = ".$stepNumber." LIMIT 1";
		$this->db->query($sql);
		$result = $this->db->fetch(0);
		
		$this->assertEquals($step->getId(), $result->id);
		$this->assertEquals($step->getNumber(), $result->number);
		$this->assertEquals($step->getProcessId(), $result->process_id);
	}
	
	public function testSave(){
		$facilityId = 100;
		$workOrderId= 1;
		$process = new Process($this->db);
		$process->setFacilityId($facilityId);
		$process->setName('newTestProcess');
		$process->setWorkOrderId($workOrderId);
		$processID = $process->save();
		
		$sql = "SELECT * FROM ".self::TB_PROCESS." ".
				"WHERE id=".$processID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		$this->assertEquals($process->getId(), $result[0]['id']);
		$this->assertEquals($process->getName(), $result[0]['name']);
		$this->assertEquals($process->getFacilityId(), $result[0]['facility_id']);
		$this->assertEquals($process->getWorkOrderId(), $result[0]['work_order_id']);
		
		//test Update
		$newFacilityId = 200;
		$newWorkOrderId= 2;
		$process->setFacilityId($newFacilityId);
		$process->setWorkOrderId($newWorkOrderId);
		$process->setName('newName');
		$process->save();
		
		$sql = "SELECT * FROM ".self::TB_PROCESS." ".
				"WHERE id=".$processID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		
		$this->assertEquals($process->getId(), $result[0]['id']);
		$this->assertEquals($process->getName(), $result[0]['name']);
		$this->assertEquals($process->getFacilityId(), $result[0]['facility_id']);
		$this->assertEquals($process->getWorkOrderId(), $result[0]['work_order_id']);
	}
	
	
}
?>
