<?php

namespace VWM\Label;

use VWM\Framework\Test\DbTestCase;
	

class CompanyLevelLabelTest extends DbTestCase {
	
	public $fixtures = array(
        TB_COMPANY_LEVEL_LABEL,
        TB_INDUSTRY_TYPE,
        TB_INDUSTRY_TYPE2LABEL
	);


	public function testGetRepairOrderLabel() {
		$labelSystem = new CompanyLevelLabel($this->db);
		$label = $labelSystem->getRepairOrderLabel();
		
		$this->assertEquals('Repair Order', $label->default_label_text);
	}
	
	public function testSaveRepairOrderLabel() {
		$industryType = 1;
		$labelText = "Work Order Label";
		$labelSystem = new CompanyLabelManager($this->db, $industryType);

        $labelCompanySystem = $labelSystem->getLabel("repair_order");
		$labelCompanySystem->setLabelText($labelText);
        $labelCompanySystem->save();
        $labelCompanySystemUpdated = $labelSystem->getLabel("repair_order");
		$this->assertEquals($labelText, $labelCompanySystemUpdated->getLabelText());
	}
}

?>
