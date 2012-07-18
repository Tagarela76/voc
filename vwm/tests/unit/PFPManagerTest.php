<?php

use VWM\Framework\Test as Testing;

class PFPManagerTest extends Testing\DbTestCase {

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP, TB_PFP2PRODUCT, TB_PFP2COMPANY
    );


    public function testGetListAll() {
        $manager = new PFPManager($this->db);
        $list = $manager->getListAllowed(1);

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
