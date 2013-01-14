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
		$process = new ProcessTemplate($this->db);
		$process->setName('testProcess');
		$process->setFacilityId(100);
		$id = $process->save();
		
		
		$date =date(MYSQL_DATETIME_FORMAT);
		$sql = "SELECT * FROM ".Process::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals(0, $row['process_type']);
		$this->assertEquals($process->getName(), $row['name']);
		$this->assertEquals($process->getFacilityId(), $row['facility_id']);
		
		//test update
		$processUpdateTest = new ProcessTemplate($this->db, $id);
		$processUpdateTest->setFacilityId(200);
		$processUpdateTest->setName($processName);
		$processUpdateTest->save();
		
		$sql = "SELECT * FROM ".Process::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($processName, $row['name']);
		$this->assertEquals(200, $row['facility_id']);
	}
	
	
}
?>
