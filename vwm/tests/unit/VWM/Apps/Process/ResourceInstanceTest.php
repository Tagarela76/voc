<?php

namespace VWM\Apps\Process;

use VWM\Framework\Test\DbTestCase;

class ResourceInstanceTest extends DbTestCase {
	
	public $fixtures = array(
		TB_PROCESS,TB_TYPE, TB_UNITTYPE
	);
	
	public function testSave() {
		//test insert
		$processName = 'newTest';
		$resource = new ResourceInstance($this->db);
		
		
		$resource->setDescription('resource description');
		$resource->setQty(100);
		$resource->setUnittypeId(39);
		$resource->setResourceTypeId(1);
		$resource->setRate(15);
		$resource->setRateUnittypeId(38);
		$resource->setRateQty(1);
		$resource->setStepId(1);
		
		$id = $resource->save();
		
		
		$date =date(MYSQL_DATETIME_FORMAT);
		$sql = "SELECT * FROM ". Resource::TABLE_NAME. " WHERE id=".$id;
		//var_dump($sql);die();
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($resource->getDescription(), $row['description']);
		$this->assertEquals($resource->getQty(), $row['qty']);
		$this->assertEquals($resource->getUnittypeId(), $row['unittype_id']);
		$this->assertEquals($resource->getResourceTypeId(), $row['resource_type_id']);
		$this->assertEquals($resource->getLaborCost(), $row['labor_cost']);
		$this->assertEquals($resource->getMaterialCost(), $row['material_cost']);
		$this->assertEquals($resource->getTotalCost(), $row['total_cost']);
		$this->assertEquals($resource->getRate(), $row['rate']);
		$this->assertEquals($resource->getRateUnittypeId(), $row['rate_unittype_id']);
		$this->assertEquals($resource->getRateQty(), $row['rate_qty']);
		$this->assertEquals($resource->getStepId(), $row['step_id']);
		
		
		//test update
		$resourceUpdateTest = new ResourceInstance($this->db, $id);
		$resourceUpdateTest->setDescription('new resource description');
		$resourceUpdateTest->setQty(200);
		$resourceUpdateTest->setUnittypeId(2);
		$resourceUpdateTest->setResourceTypeId(2);
		$resourceUpdateTest->setLaborCost(20);
		$resourceUpdateTest->setMaterialCost(30);
		$resourceUpdateTest->calculateTotalCost();
		$resourceUpdateTest->setRate(30);
		$resourceUpdateTest->setRateUnittypeId(2);
		$resourceUpdateTest->setRateQty(2);
		$resourceUpdateTest->setStepId(2);
		$id = $resourceUpdateTest->save();
		
		$sql = "SELECT * FROM ".Resource::TABLE_NAME. " WHERE id=".$id;
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$this->assertEquals($resourceUpdateTest->getDescription(), $row['description']);
		$this->assertEquals($resourceUpdateTest->getQty(), $row['qty']);
		$this->assertEquals($resourceUpdateTest->getUnittypeId(), $row['unittype_id']);
		$this->assertEquals($resourceUpdateTest->getResourceTypeId(), $row['resource_type_id']);
		$this->assertEquals($resourceUpdateTest->getLaborCost(), $row['labor_cost']);
		$this->assertEquals($resourceUpdateTest->getMaterialCost(), $row['material_cost']);
		$this->assertEquals($resourceUpdateTest->getTotalCost(), $row['total_cost']);
		$this->assertEquals($resourceUpdateTest->getRate(), $row['rate']);
		$this->assertEquals($resourceUpdateTest->getRateUnittypeId(), $row['rate_unittype_id']);
		$this->assertEquals($resourceUpdateTest->getRateQty(), $row['rate_qty']);
		$this->assertEquals($resourceUpdateTest->getStepId(), $row['step_id']);
		$this->assertEquals($resourceUpdateTest->getTotalCost(), $row['total_cost']);
	}
	
	
	public function testCalculateTotalCost() {
		
		//Time
		$resource = new ResourceInstance($this->db);
		$resource->setQty(30);
		$resource->setUnittypeId(38);
		$resource->setResourceTypeId(Resource::TIME);
		$resource->setRate(20);
		$resource->setRateUnittypeId(39);
		$resource->setRateQty(2);
		
		
		$resource->calculateTotalCost();
		$totalCost = $resource->getTotalCost();
		
		$this->assertEquals($resource->getTotalCost(), 5);
		
		//VOLUME
		$resource = new ResourceInstance($this->db);
		$resource->setQty(10);
		$resource->setUnittypeId(7);
		$resource->setResourceTypeId(2);
		$resource->setRate(20);
		$resource->setRateUnittypeId(1);
		$resource->setRateQty(1);
		
		$resource->countCost();
		$resource->calculateTotalCost();
		$totalCost = $resource->getTotalCost();
		
		$this->assertEquals($resource->getTotalCost(), 200);
		
		//COUNT
		$resource = new ResourceInstance($this->db);
		$resource->setQty(10);
		$resource->setUnittypeId(41);
		$resource->setResourceTypeId(3);
		$resource->setRate(100);
		$resource->setRateUnittypeId(37);
		$resource->setRateQty(1);
		
		$resource->countCost();
		$resource->calculateTotalCost();
		$totalCost = $resource->getTotalCost();
		
		$this->assertEquals($totalCost, 10);
	}
	
}
?>
