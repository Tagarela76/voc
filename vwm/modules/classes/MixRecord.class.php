<?php

class MixRecord extends RecordProperties {
	
	private $product;

    function MixRecord($product, $recordProperties) {
    	$this->product = $product;

    	$this->setQuantity($recordProperties->getQuantity());
    	$this->setUnitType($recordProperties->getunitType());
    }
    
    public function testMixRecord() {
    	$this->product->helloWorld();
    }
    
    public function getProduct() {
    	return $this->product;
    }
}
?>