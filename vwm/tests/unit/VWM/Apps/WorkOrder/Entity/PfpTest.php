<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;

class PfpTest extends DbTestCase {

	public $fixtures = array(
		Pfp::TABLE_NAME,
	);

	public function testSave() {
		$pfp = new Pfp($this->db);		
		$pfp->setDescription('Test Pfp');
		$this->assertEquals(5, $pfp->save());

		$pfp->setDescription('Updated');
		$this->assertEquals(5, $pfp->save());
	}
}

?>
