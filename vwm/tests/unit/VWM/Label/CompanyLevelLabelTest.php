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
}

?>
