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
		$id = $pfp->save();
		$this->assertEquals(6, $id);

		$pfp->setDescription('Updated');
		$this->assertEquals(6, $pfp->save());
	}
}

?>