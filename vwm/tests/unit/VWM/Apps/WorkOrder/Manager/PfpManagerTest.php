<?php

namespace VWM\Apps\WorkOrder\Manager;

use VWM\Framework\Test\DbTestCase;

class PfpManagerTest extends DbTestCase
{

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP_TYPES, TB_PFP, TB_PFP2PRODUCT,
        TB_PFP2COMPANY, TB_PRODUCT2COMPANY, TB_PFP2PFP_TYPES,
    );

    public function testFindAllAllowed()
    {
        $manager = \VOCApp::getInstance()->getService('pfp');
        $manager->setCriteria('companyId', '1');
        $list = $manager->findAllAllowed();

        $this->assertTrue(is_array($list));
        $this->assertTrue(count($list) == 2);

        //  check the product order
        //  primary should be always on top
        $productsOne = $list[0]->getProducts();
        $productsTwo = $list[1]->getProducts();
        $this->assertTrue($productsOne[0]->isPrimary() === true);
        $this->assertTrue($productsTwo[0]->isPrimary() === true);
        $this->assertTrue($productsTwo[1]->isPrimary() === false);
    }

}

?>
