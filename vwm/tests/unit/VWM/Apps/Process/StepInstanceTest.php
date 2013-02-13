<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class StepInstanceTest extends DbTestCase {

	const TB_STEP = 'step_instance';
	const TB_PROCESS='process_template';
	const TB_RESOURCE = 'resource_template';
	const TB_UNITTYPE = 'unittype';
	
	public $fixtures = array(
		self::TB_PROCESS, self::TB_STEP, self::TB_RESOURCE, self::TB_UNITTYPE
	);

	
	
	public function testSave(){
		$processId = 1;
		$stepNumber= 1;
		$step = new StepInstance($this->db);
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
	
}

?>
