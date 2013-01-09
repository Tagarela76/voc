<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ProcessInstanceTest extends DbTestCase {
	
	public $fixtures = array(
		TB_PROCESS
	);
	
	public function testSave() {
		//test insert
		$process = new ProcessInstance($this->db);
		$process->setName('testProcess');
		$process->setFacilityId(100);
		$process->setWorkOrderId(1);
		$id = $process->save();
		$date =date(MYSQL_DATETIME_FORMAT);
		$sql = "SELECT * FROM ".TB_PROCESS. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($process->getName(), $row['name']);
		$this->assertEquals(1, $row['process_type']);
		$this->assertEquals($process->getFacilityId(), $row['facility_id']);
		$this->assertEquals($process->getWorkOrderId(), $row['work_order_id']);
		
		//test update
		$processUpdateTest = new ProcessInstance($this->db, $id);
		$processUpdateTest->setFacilityId(200);
		$processUpdateTest->setName('newTest');
		$processUpdateTest->setWorkOrderId(2);
		$processUpdateTest->save();
		
		$sql = "SELECT * FROM ".TB_PROCESS. " WHERE id=".$id." AND work_order_id IS NOT NULL";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals('newTest', $row['name']);
		$this->assertEquals(200, $row['facility_id']);
		$this->assertEquals(2, $row['work_order_id']);
	}
	
	
}
?>
