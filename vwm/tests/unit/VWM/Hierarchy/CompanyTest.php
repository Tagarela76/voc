<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;


class CompanyTest extends DbTestCase {

    protected $fixtures = array(
		TB_COMPANY,
        TB_INDUSTRY_TYPE,
        TB_COMPANY_LEVEL_LABEL,
        TB_INDUSTRY_TYPE2LABEL,
        TB_COMPANY2INDUSTRY_TYPE,
        TB_BROWSE_CATEGORY_ENTITY,
        TB_DISPLAY_COLUMNS_SETTINGS
	);
    
	public function testSave() {
        
		$company = new Company($this->db);
        $company->setAddress("new address");
        $company->setCity("new city");
        $company->setContact("new contact");
        $company->setCountry("2");
        $company->setCreaterId("18");
        $company->setCreationDate('2011-06-26');
        $company->setDateFormatId("1");
        $company->setEmail("amn-15@mail.ru");
        $company->setFax("111");
        $company->setGcgId("1");
        $company->setName("new name");
        $company->setPhone("111");
        $company->setState("new State");
        $company->setTitle("new title");
        $company->setVocUnittypeId("2");
        $company->setZip("111");
        $company->setCounty("new county");

		$result = $company->save();
        
        // I expect that we added new company with id =4
        $expectedId = 4;
        $this->assertEquals($expectedId, $result);	// last id
        
        $sql = "SELECT * FROM ".TB_COMPANY." WHERE company_id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$companyActual = new Company($this->db);
		$companyActual->initByArray($row);
		$this->assertEquals($company, $companyActual);
        
        // Test Update
        // set company id
        $updatedCompanyId = "1";
        $company->setCompanyId($updatedCompanyId);
        // change city
        $newCityName = "new city";
        $company->setCity($newCityName);
        $result = $company->save();
        $this->assertEquals($updatedCompanyId, $result);
        
        $sql = "SELECT * FROM ".TB_COMPANY." WHERE company_id = {$updatedCompanyId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
        $companyUpdated = new Company($this->db);
		$companyUpdated->initByArray($row);
        $this->assertEquals($company, $companyUpdated);
        
        $companyAfterUpdate = new Company($this->db, $updatedCompanyId);
        $this->assertEquals($newCityName, $companyAfterUpdate->getCity());
	}

	public function testAssignPfp() {
		$pfp = new \PFP();

		$company = new Company($this->db);
		//$company->assignPfp($pfp);
	}
    
    public function testGetIndustryTypes() {
        
        $companyId = 1; 
        $company = new Company($this->db, $companyId);
        $companyIndustryTypes = $company->getIndustryTypes();
        $this->assertTrue(is_array($companyIndustryTypes));
        $this->assertTrue(count($companyIndustryTypes) == 1);
        $this->assertTrue($companyIndustryTypes[0] instanceof \IndustryType);
        $this->assertTrue($company->getIndustryType() instanceof \IndustryType);
    }
    
    public function testGetLabelManager() {
        
        $companyId = 1; 
        $company = new Company($this->db, $companyId);
        $repairOrderLabelId = "repair_order";
        $repairOrderLabel = $company->getIndustryType()->getLabelManager()
				->getLabel($repairOrderLabelId)->getLabelText();
        $this->assertTrue($repairOrderLabel == "Work Order Label");
    }
    
    public function testGetDisplayColumnsManager() {
        
        $companyId = 1; 
        $company = new Company($this->db, $companyId);
        $browseCategory = "browse_category_mix";
        $mixDisplayColumnsValue = $company->getIndustryType()->getDisplayColumnsManager()->getDisplayColumnsSettings($browseCategory)->getValue();
        $this->assertTrue($mixDisplayColumnsValue == "Product Name,Description,R/O Description,Contact,R/O VIN number,VOC,Creation Date");
    }
}

?>
