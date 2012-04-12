<?php

class RecordProperties {
	
	private $quantity;
	private $unitType;

    function RecordProperties() {
    }
    
    //		GETTERS
    public function getUnitType() {
    	return $this->unitType;
    }
    
    public function getQuantity() {
    	return $this->quantity;
    }
    
    //		SETTERS
    public function setUnitType($unitType) {
    	$this->unitType = $unitType;
    }
    
    public function setQuantity($quantity) {
    	$this->quantity = $quantity;    
    }
    
}
?>