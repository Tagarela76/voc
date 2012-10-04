<?php

use VWM\Framework\Test as Testing;

class IndustryTypeTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_INDUSTRY_TYPE, TB_PRODUCT, TB_PRODUCT2INDUSTRY_TYPE, TB_COMPANY, TB_COMPANY2INDUSTRY_TYPE
	);

	public function testIndustryType() {
		$industryType = new IndustryType($this->db, '1');
		$this->assertTrue($industryType instanceof IndustryType);
		$this->assertTrue(!is_null($industryType));
	}
	
	public function testAddIndustryType() {
		
		// add Type
		$industryType = new IndustryType($this->db);
		$industryType->type = 'test5';
		$id = $industryType->save(); 
		
		$myTesIndustryType = Phactory::get(TB_INDUSTRY_TYPE, array('type'=>"test5"));
		$this->assertTrue(isset($myTesIndustryType));
		$this->assertTrue(is_null($myTesIndustryType->parent));
		
		// add sub Type
		$industryType = new IndustryType($this->db);
		$industryType->type = 'test6';
		$industryType->parent = '1';
		$industryType->save();
		
		$myTesIndustryType = Phactory::get(TB_INDUSTRY_TYPE, array('type'=>"test6"));
		$this->assertTrue(!is_null($myTesIndustryType->parent));
		$this->assertTrue($myTesIndustryType->parent == 1);
	}
	
	public function testUpdateIndustryType() {
		
		// update Type
		$industryType = new IndustryType($this->db, 1);
		$industryType->type = 'test6';
		$industryType->save();
		
		$myTesIndustryType = Phactory::get(TB_INDUSTRY_TYPE, array('id'=>"1"));

		$this->assertTrue($myTesIndustryType->type == 'test6');
		
		// update sub Type
		$industryType = new IndustryType($this->db, 2);
		$industryType->type = 'test55';
		$industryType->parent = '4';
		$industryType->save();
		
		$myTesIndustryType = Phactory::get(TB_INDUSTRY_TYPE, array('id'=>"2"));
		$this->assertTrue($myTesIndustryType->type == 'test55');
		$this->assertTrue($myTesIndustryType->parent == '4');
	}
	
	public function testDeleteIndustryType() {
		
		$industryType = new IndustryType($this->db, '1');
		$industryType->delete();
		$deletedIndustryType = Phactory::get(TB_INDUSTRY_TYPE, array('type'=>"test1"));
		$this->assertTrue(is_null($deletedIndustryType));
	}
	
	public function testInitByArray() {

		$industryTypeOriginal = new IndustryType($this->db, '1');
		
		$sql = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE id = 1";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		
		$industryTypeChecked = new IndustryType($this->db);
		$industryTypeChecked->initByArray($row);
		$this->assertEquals($industryTypeOriginal, $industryTypeChecked);
	}
	
	public function testGetIndustryTypes() {
		
		$industryTypeManager = new IndustryTypeManager($this->db);
		$industryTypes = $industryTypeManager->getIndustryTypes();
		$this->assertTrue($industryTypes[0] instanceof IndustryType);
		$this->assertTrue(count($industryTypes) == 2);
		
		$industryTypesCount = $industryTypeManager->getIndustryTypesCount();
		$this->assertTrue($industryTypesCount == 2);
		
		$subIndustryTypes = $industryTypeManager->getSubIndustryTypes();
		$this->assertTrue($subIndustryTypes[0] instanceof IndustryType);
		$this->assertTrue(count($subIndustryTypes) == 2);
		
		$subIndustryTypesCount = $industryTypeManager->getSubIndustryTypesCount();
		$this->assertTrue($subIndustryTypesCount == 2);
	}
	
	public function testGetIndustryTypesByProductId() {
		
		$industryTypeManager = new IndustryTypeManager($this->db);
		$industryTypes = $industryTypeManager->getIndustryTypesByProductId('1');
		$this->assertTrue($industryTypes[0] instanceof IndustryType);
		$this->assertTrue(count($industryTypes) == 1);
	}
	
	public function testGetProducts() {
		
		$industryType = new IndustryType($this->db, 1);
		$products = $industryType->getProducts();
		$this->assertTrue(is_array($products));
		$this->assertTrue(count($products) == 2);
	}
	
	public function testGetCompanies() {
		
		$industryType = new IndustryType($this->db, 1);
		$companies = $industryType->getCompanies();
		$this->assertTrue(is_array($companies));
		$this->assertTrue(count($companies) == 2);
	}
	
	public function testSetCompanyToIndustryType() {
		
		$industryType = new IndustryType($this->db, 1);
		$companyId = 3;
		$industryType->setCompanyToIndustryType($companyId);
		$companies = $industryType->getCompanies();
		$this->assertTrue(count($companies) == 3);
	}
	
	public function testUnSetCompanyToIndustryType() {
		
		$industryType = new IndustryType($this->db, 1);
		$industryType->unSetCompanyFromIndustryType();

		$companies = $industryType->getCompanies();
		$this->assertTrue(count($companies) == 0);
	}
	
	public function testSetProductToIndustryType() {
		
		$industryType = new IndustryType($this->db, 1);
		$productId = 3;
		$industryType->setProductToIndustryType($productId);
		$industryTypeManager = new IndustryTypeManager($this->db);
		
		$industryTypes = $industryTypeManager->getIndustryTypesByProductId($productId);
		$this->assertTrue(count($industryTypes) == 2);
	}
	
	public function testSearchType() {
		
		$industryTypeManager = new IndustryTypeManager($this->db);
		$industryTypes = $industryTypeManager->searchType("test");
		$this->assertTrue($industryTypes[0] instanceof IndustryType);
		$this->assertTrue(count($industryTypes) == 2);
		
		$industryTypesCount = $industryTypeManager->searchTypeResultsCount("test");
		$this->assertTrue($industryTypesCount == 2);

	}
	
	public function testGetSubTypes() {
		
		$industryType = new IndustryType($this->db, 1);
		$subIndustryTypes = $industryType->getSubIndustryTypes();
		
		$this->assertTrue($subIndustryTypes[0] instanceof IndustryType);
		$this->assertTrue(count($subIndustryTypes) == 2);
	}
}