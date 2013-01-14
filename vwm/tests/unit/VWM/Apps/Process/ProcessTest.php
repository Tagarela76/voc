<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ProcessTest extends DbTestCase {
	
	public $fixtures = array(
		TB_PROCESS, TB_STEP
	);
	
	const TB_STEP = 'step';
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
				" WHERE process_id = 2 AND number = ".$stepNumber." LIMIT 1";
		$this->db->query($sql);
		$result = $this->db->fetch(0);
		
		$this->assertEquals($step->getId(), $result->id);
		$this->assertEquals($step->getNumber(), $result->number);
		$this->assertEquals($step->getProcessId(), $result->process_id);
	}
	
	
}
?>
