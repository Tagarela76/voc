<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;

class PfpProductTest extends DbTestCase {

	public $fixtures = array(
        TB_SUPPLIER,
        TB_PRODUCT,
        Pfp::TABLE_NAME,
		PfpProduct::TABLE_NAME,
	);

	public function testSave() {
        $ratio = 12;
        $isPrimary = 1;
        $productId = 1;
        $preformulatedProductsId = 1;
        
		$pfpProduct = new PfpProduct();
        $pfpProduct->setName('testProduct');
        $pfpProduct->setProductId($productId);
        $pfpProduct->setIsPrimary($isPrimary);
        $pfpProduct->setPreformulatedProductsId($preformulatedProductsId);
        $pfpProduct->setRatio($ratio);
        $id = $pfpProduct->save();
        
        $sql = "SELECT * FROM ".PfpProduct::TABLE_NAME." ".
                "WHERE id=".$id;
        $this->db->query($sql);

		$result = $this->db->fetch_all_array();
        $this->assertEquals($pfpProduct->getRatio(), $result[0]['ratio']);
        $this->assertEquals($pfpProduct->getProductId(), $result[0]['product_id']);
        $this->assertEquals($pfpProduct->getPreformulatedProductsId(), $result[0]['preformulated_products_id']);
        $this->assertEquals($pfpProduct->getIsPrimary(), $result[0]['isPrimary']);
	}
    
}

?>
