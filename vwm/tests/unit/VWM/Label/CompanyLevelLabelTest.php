<?php

namespace VWM\Label;

use VWM\Framework\Test\DbTestCase;
	

class CompanyLevelLabelTest extends DbTestCase {
	
	public $fixtures = array(
		TB_COMPANY, 
		TB_INDUSTRY_TYPE,
		TB_COMPANY2INDUSTRY_TYPE,
		CompanyLevelLabel::TABLE_NAME,
	);


	public function testGetRepairOrderLabel() {
		$companyId = 1;
		$labelSystem = new CompanyLevelLabel($this->db, $companyId);
		$label = $labelSystem->getRepairOrderLabel();
		
		$this->assertEquals('Repair Order EEE', $label);
	}
	
	public function testSaveRepairOrderLabel() {
		$industryType = 1;
		$companyId = 1;
		$labelText = "Work Order Label";
		$labelSystem = new LabelManager($this->db, $industryType);
		$labelSystem->saveRepairOrderLabel($labelText);
		$labelCompanySystem = new CompanyLevelLabel($this->db, $companyId);
		$newLabel = $labelCompanySystem->getRepairOrderLabel();
		$this->assertEquals($labelText, $newLabel);
	}
	
	public function testGetLabelList() {
		$industryType = 1;
		$labelSystem = new LabelManager($this->db, $industryType);
		$labelList = $labelSystem->getLabelList();
		$this->assertTrue(is_array($labelList));
		$this->assertEquals(1, count($labelList));
	}
}

?>
