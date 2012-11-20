<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;


class CompanyTest extends DbTestCase {

	public function testSave() {
		$company = new Company($this->db);
		$company->save();
	}

	public function testAssignPfp() {
		$pfp = new \PFP();

		$company = new Company($this->db);
		$company->assignPfp($pfp);
	}
}

?>
