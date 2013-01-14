<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ProcessInstanceTest extends DbTestCase {
	
	public $fixtures = array(
		TB_PROCESS
	);
	
	public function testSave() {
		//test insert
		$processName = 'newTest';
		$step = new StepInstance($this->db);
		$step->setNumber(1);
		$step->setProcessId(1);
		$id = $step->save();
		$date =date(MYSQL_DATETIME_FORMAT);
		$sql = "SELECT * FROM ".  Step::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($step->getNumber(), $row['number']);
		$this->assertEquals($step->getProcessId(), $row['process_id']);
		
		//test update
		$stepUpdateTest = new StepInstance($this->db, $id);
		$stepUpdateTest->setNumber(2);
		$stepUpdateTest->setProcessId(2);
		$id = $stepUpdateTest->save();
		
		$sql = "SELECT * FROM ".Step::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($stepUpdateTest->getNumber(), $row['number']);
		$this->assertEquals($stepUpdateTest->getProcessId(), $row['process_id']);
	}
	
	
}
?>
