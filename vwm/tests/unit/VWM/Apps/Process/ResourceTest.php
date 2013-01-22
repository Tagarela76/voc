<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ResourceTest extends DbTestCase {

	const TB_STEP = 'step';
	const TB_PROCESS='process';
	const TB_RESOURCE = 'resource';
	const TB_UNITTYPE = 'unittype';
	
	public $fixtures = array(
		self::TB_PROCESS, self::TB_STEP, self::TB_RESOURCE, self::TB_UNITTYPE
	);

	
	
	public function testSave(){
		$resource = new Resource($this->db);
		$resource->setDescription('description');
		$resource->setQty(1);
		$resource->setRate(10);
		$resource->setRateUnittypeId(38);
		$resource->setStepId(1);
		$resource->setUnittypeId(39);
		$resource->setRateQty(10);
		$resource->setResourceTypeId(1);
		$resourceID = $resource->save();
		
		
		$sql = "SELECT * FROM ".self::TB_RESOURCE." ".
				"WHERE id=".$resourceID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		$this->assertEquals($resource->getTotalCost(), 60);
		
		$this->assertEquals($resource->getTotalCost(), $result[0]['total_cost']);
		$this->assertEquals($resource->getQty(), $result[0]['qty']);
		$this->assertEquals($resource->getDescription(), $result[0]['description']);
		$this->assertEquals($resource->getRate(), $result[0]['rate']);
		
		
		
		//test Update
		$resource->setDescription('description2');
		$resource->setQty(2);
		$resource->setRate(10);
		$resourceID = $resource->save();
		
		$sql = "SELECT * FROM ".self::TB_RESOURCE." ".
				"WHERE id=".$resourceID;
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();
		
		$this->assertEquals($resource->getTotalCost(), $result[0]['total_cost']);
		$this->assertEquals($resource->getQty(), $result[0]['qty']);
		$this->assertEquals($resource->getDescription(), $result[0]['description']);
		$this->assertEquals($resource->getRate(), $result[0]['rate']);
		
	}

	
}

?>
