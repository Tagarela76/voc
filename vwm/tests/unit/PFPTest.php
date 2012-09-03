<?php

use VWM\Framework\Test as Testing;

class PFPTest extends Testing\DbTestCase {

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP, TB_PFP2PRODUCT, TB_PFP2COMPANY, TB_PFP_TYPES, TB_FACILITY
    );

    public function testGetProducts() {
        $manager = new PFPManager($this->db);
        $pfp = $manager->getPFP(1);

        $products = $pfp->getProducts();
        $this->assertTrue(is_array($products));
        $this->assertTrue(count($products) == 3);

        //  check the product order
        //  primary should be always on top
        $this->assertTrue($products[0]->product_nr == '470C0191');
    }

    public function testGetRatio() {
        $manager = new PFPManager($this->db);
        $pfp = $manager->getPFP(1);

        $ratio = $pfp->getRatio();
        $this->assertTrue($ratio == "<b>1</b>:3:4");

        $ratio = $pfp->getRatio(false);
        $this->assertTrue($ratio == "1:3:4");
    }
    
    public function testAssignPFP2Type() {
        
        $manager = new PFPManager($this->db);
        $pfpID = 4;
        $pfpTypeid = 2;
        
        $manager->assignPFP2Type($pfpID, $pfpTypeid);
        $pfpTypes = new PfpTypes($this->db);
        $pfpTypesCount = $pfpTypes->getPfpProductsByTypeId('2');
        $this->assertTrue(count($pfpTypesCount) == 2);
        
    }
    
    public function testUnassignPFP2Type() {
        
        $manager = new PFPManager($this->db);
        $pfpID = 4;

        $manager->unassignPFP2Type($pfpID);
        $pfpTypes = new PfpTypes($this->db);
        $pfpTypesCount = $pfpTypes->getPfpProductsByTypeId('4'); 
        $this->assertTrue(!$pfpTypesCount);
        
    }

}
