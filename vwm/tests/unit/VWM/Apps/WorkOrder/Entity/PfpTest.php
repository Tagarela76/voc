<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;

class PfpTest extends DbTestCase {

	public $fixtures = array(
		Pfp::TABLE_NAME,
	);

	public function testSave() {
		$pfp = new Pfp();
		$pfp->setDescription('Test Pfp');
		$id = $pfp->save();
		$this->assertEquals(6, $id);

		$pfp->setDescription('Updated');
		$this->assertEquals(6, $pfp->save());
	}
   
    public function testGetPfpIdByDescription(){
        $description = "Ford Explorer Basecoat";
        $pfp = new Pfp();
        $pfp->setDescription($description);
        $this->assertEquals(1, $pfp->getPfpIdByDescription());
    }
    
}

?>