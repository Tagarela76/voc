<?php

use VWM\Framework\Test\DbTestCase;

class NewProductRequestTest extends DbTestCase {
	
	public $fixtures = array(
		TB_NEW_PRODUCT_REQUEST,
	);


	public function testSave() {		
		$request = new NewProductRequest($this->db);		
		$request->setSupplier('Denis The Supplier');
		$request->setProductId('wewe');
		$request->setName('THIS-IS-NAME');
		$request->setDescription('HAHAAHAAA');
		$request->setMsdsId('0');						
		$request->setUserId('1');
		$request->setStatus(NewProductRequest::STATUS_NEW);
				
		$exptectedId = 14;
		$id = $request->save();
		
		$this->assertEquals($exptectedId, $id);
		
		$sql = "SELECT * FROM ".TB_NEW_PRODUCT_REQUEST." WHERE id = {$exptectedId}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);		
		$expectedRequest = new NewProductRequest($this->db);
		$expectedRequest->initByArray($row);		
		$this->assertEquals($expectedRequest, $request);
		
		// UPDATE
		$request->setStatus(NewProductRequest::STATUS_ACCEPT);
		$updateResult = $request->save();
		$this->assertEquals($exptectedId, $updateResult);
		
		$sql = "SELECT * FROM ".TB_NEW_PRODUCT_REQUEST." WHERE id = {$updateResult}";
		$this->db->query($sql);
		$this->assertEquals(1, $this->db->num_rows());
		
		$row = $this->db->fetch_array(0);		
		$expectedRequestAfterUpdate = new NewProductRequest($this->db);
		$expectedRequestAfterUpdate->initByArray($row);		
		$this->assertEquals($expectedRequestAfterUpdate, $request);
		
	}
}

?>
