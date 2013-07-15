<?php

use VWM\Framework\Test as Testing;

class PFPManagerTest extends Testing\DbTestCase {

    protected $fixtures = array(
        TB_COMPANY, TB_SUPPLIER, TB_PRODUCT, TB_PFP_TYPES, TB_PFP, TB_PFP2PRODUCT, 
        TB_PFP2COMPANY, TB_PRODUCT2COMPANY, TB_PFP2PFP_TYPES,
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
	
	public function testDeletePFP() {
        $this->markTestIncomplete();

		$manager = new PFPManager($this->db);
		$pfp = $manager->getPFP('1');
		$this->assertInstanceOf('VWM\Apps\WorkOrder\Entity\Pfp', $pfp);
		
		$manager->unassignPFPFromCompanies('1');
		$manager->remove($pfp);
		
		$pfp = $manager->getPFP('1');
		$this->assertNull($pfp->getId());
	}
	
	public function testIsPFPsProductsAssign2Company() {
		
		$manager = new PFPManager($this->db);
		$pfpId = '1';
		$companyId = '1';
		$isPFPsProductsIsAssign2Company = $manager->isPFPsProductsAssign2Company($pfpId, $companyId);
		$this->assertTrue($isPFPsProductsIsAssign2Company == true);
	}
	
	
	public function testAssignPFP2Type() {
        $this->markTestIncomplete();

        $manager = new PFPManager($this->db);
        $pfpID = 4;
        $pfpTypeid = 2;
        
        $manager->assignPFP2Type($pfpID, $pfpTypeid);
        $sql = "SELECT * FROM ".TB_PFP2PFP_TYPES." " .
				"WHERE pfp_id = {$pfpID} AND pfp_type_id = {$pfpTypeid}";
		$this->db->query($sql);	
		$this->assertEquals($this->db->num_rows(), 1);
		$row = $this->db->fetch(0);
		$this->assertEquals($row->pfp_id, $pfpID);
		$this->assertEquals($row->pfp_type_id, $pfpTypeid);
        
    }
    
    public function testUnAssignPFP2Type() {
        $this->markTestIncomplete();

        $manager = new PFPManager($this->db);
        $pfpID = 2;
		$pfpTypeID = 2;

        $manager->unassignPFP2Type($pfpID, $pfpTypeID);
		$sql = "SELECT * FROM ".TB_PFP2PFP_TYPES." " .
				"WHERE pfp_type_id = {$pfpTypeID}";
		$this->db->query($sql);	
		$this->assertEquals($this->db->num_rows(), 1);	//	was 2 before unassign
		$row = $this->db->fetch(0);
		$this->assertEquals($row->pfp_id, 1);
		
    }
	
	
	public function testGetUnAssignPFP2Type() {
		$manager = new PFPManager($this->db);
		$manager->getUnAssignPFP2Type($companyId);
	}

}
