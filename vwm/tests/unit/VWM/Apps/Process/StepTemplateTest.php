<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ProcessTemplateTest extends DbTestCase {
	
	public $fixtures = array(
		TB_PROCESS
	);
	
	public function testSave() {
		//test insert
		$processName = 'newTest';
		$step = new StepTemplate($this->db);
		$step->setNumber(1);
		$step->setProcessTemplateId(1);
		$id = $step->save();
		$date =date(MYSQL_DATETIME_FORMAT);
		$sql = "SELECT * FROM ".  Step::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($step->getNumber(), $row['number']);
		$this->assertEquals($step->getProcessTemplateId(), $row['process_template_id']);
		
		//test update
		$stepUpdateTest = new StepTemplate($this->db, $id);
		$stepUpdateTest->setNumber(2);
		$stepUpdateTest->setProcessTemplateId(2);
		$id = $stepUpdateTest->save();
		
		$sql = "SELECT * FROM ".Step::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($stepUpdateTest->getNumber(), $row['number']);
		$this->assertEquals($stepUpdateTest->getProcessTemplateId(), $row['process_template_id']);
	}
	
	
}
?>
