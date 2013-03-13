<?php

namespace VWM\Apps\WorkOrder\Manager;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\WorkOrder\Entity\Pfp;

class PfpManagerTest extends DbTestCase
{

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP, TB_PFP2PRODUCT,
		TB_PFP2COMPANY, TB_PRODUCT2COMPANY, TB_PFP2PFP_TYPES, TB_PFP_TYPES
    );

    public function testFindById()
    {
    	$db = \VOCApp::getInstance()->getService('db');
        $manager = \VOCApp::getInstance()->getService('pfp');
        $pfp = $manager->findById(1);

        $this->assertInstanceOf('\VWM\Apps\WorkOrder\Entity\Pfp', $pfp);
    }

    public function testFindAllAllowed()
    {
		$db = \VOCApp::getInstance()->getService('db');
        $manager = \VOCApp::getInstance()->getService('pfp');
		$pfp = new Pfp($db);
        $manager->setCriteria('companyId', '1');
        $list = $manager->findAllPfps();

        $this->assertTrue(is_array($list));
        $this->assertTrue(count($list) == 2);

        //  check the product order
        //  primary should be always on top
        $productsOne = $list[0]->getProducts();
        $productsTwo = $list[1]->getProducts();
		$this->assertTrue(count($productsOne) == 3);

        // make sure primary product is on top
        $this->assertTrue($productsOne[0]->isPrimary() === true);
        $this->assertTrue($productsOne[1]->isPrimary() === false);
        $this->assertTrue($productsOne[2]->isPrimary() === false);

		$sql = "SELECT pfp.id, pfp.description, pfp.company_id, pfp.is_proprietary ".
				"FROM preformulated_products pfp ".
				"LEFT JOIN pfp2product pfp2p ON pfp2p.preformulated_products_id = pfp.id ".
				"LEFT JOIN pfp2company pfp2c ON pfp2c.pfp_id = pfp.id ".
				"WHERE pfp2c.company_id = 1 ".
				"AND pfp.id = pfp2c.pfp_id ".
				"AND pfp2c.is_available = 1 ".
				"GROUP BY pfp.id";
		/*$sql = "SELECT * ".
				"FROM preformulated_products ".
				"WHERE company_id = 1 GROUP BY id";*/
		$db->query($sql);
		$result = $db->fetch_all_array();
		$this->assertTrue(count($list) == count($result));
		$this->assertEquals($list[0]->id, $result[0]['id']);
		$this->assertEquals($list[1]->id, $result[1]['id']);
		$this->assertEquals($list[0]->is_proprietary, $result[0]['is_proprietary']);
		$this->assertEquals($list[1]->is_proprietary, $result[1]['is_proprietary']);
		$this->assertEquals($list[1]->description, $result[1]['description']);
		$this->assertEquals($list[1]->description, $result[1]['description']);

		//test search criteria
		$search = array();
		$search[] = 'Ford';
		$manager->setCriteria('search', $search);
		$list = $manager->findAllPfps();
		$this->assertTrue(count($list) == 2);
		$this->assertEquals($list[0]->id, $result[0]['id']);
		$this->assertEquals($list[1]->id, $result[1]['id']);
		$this->assertEquals($list[0]->is_proprietary, $result[0]['is_proprietary']);
		$this->assertEquals($list[1]->is_proprietary, $result[1]['is_proprietary']);
		$this->assertEquals($list[1]->description, $result[1]['description']);
		$this->assertEquals($list[1]->description, $result[1]['description']);
    }

}

?>
