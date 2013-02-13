<?php

namespace VWM\Apps\Process\ProcessTemplate;

use VWM\Framework\Test\DbTestCase;
use \VWM\Apps\Process\ProcessTemplate;
use \VWM\Apps\Process\ProcessInstance;

class ProcessInstanceTest extends DbTestCase {
	
	const TB_STEP = 'step_instance';
	const TB_PROCESS='process_instance';
	
	public $fixtures = array(
		self::TB_PROCESS,
		self::TB_STEP
	);
	
	public function testSave(){
		$facilityId = 100;
		$workOrderId= 1;
		$process = new ProcessInstance($this->db);
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
	
	public function testGetSteps() {

	  $woProcessId = 2;
	  $process = new ProcessInstance($this->db, $woProcessId);
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
	
	
	
}
?>
