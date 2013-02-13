<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ResourceTemplateTest extends DbTestCase {

	const TB_STEP = 'step_template';
	const TB_PROCESS='process_template';
	const TB_RESOURCE = 'resource_template';
	const TB_UNITTYPE = 'unittype';
	const TB_RESOURCE_INSTANCE = 'resource_instance';
	
	public $fixtures = array(
		self::TB_PROCESS, self::TB_STEP, self::TB_RESOURCE, self::TB_UNITTYPE
	);

	
	
	public function testSave(){
		$resource = new ResourceTemplate($this->db);
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
	
	public function testCreateInstanceResource(){
		$resourceTemplateId = 1;
		$resourceTemplate = new ResourceTemplate($this->db, $resourceTemplateId);
		$resourceInstanse = $resourceTemplate->createInstanceResource();

		$sql = "SELECT * FROM " . self::TB_RESOURCE_INSTANCE . " " .
				"WHERE id=".$resourceInstanse->getId();
		$this->db->query($sql);
		$result = $this->db->fetch_all_array();

		$this->assertEquals($resourceInstanse->getId(), $result[0]['id']);
		$this->assertEquals($resourceInstanse->getLaborCost(), $result[0]['labor_cost']);
		$this->assertEquals($resourceInstanse->getMaterialCost(), $result[0]['material_cost']);
		$this->assertEquals($resourceInstanse->getStepId(), $result[0]['step_id']);
		$this->assertEquals($resourceInstanse->getTotalCost(), $result[0]['total_cost']);
		$this->assertEquals($resourceInstanse->getQty(), $result[0]['qty']);
		$this->assertEquals($resourceInstanse->getDescription(), $result[0]['description']);
		$this->assertEquals($resourceInstanse->getRate(), $result[0]['rate']);
		
	}

	
}

?>
