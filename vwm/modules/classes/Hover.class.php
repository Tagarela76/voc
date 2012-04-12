<?php

class Hover {
	private $mixValid = "All ok";
	private $mixPreExpired = "Will expire";
	private $mixInvalid = "Problems with MIX";

	private $equipmentValid = "All ok";
	private $equipmentPreExpired = "Will expire";
	private $equipmentExpired = "Equipment is expired";
	
    function Hover() {
    }
    
    public function mixValid() {
    	return $this->mixValid;
    }
    
    public function mixPreExpired() {
    	return $this->mixPreExpired;
    }
    
    public function mixInvalid() {
    	return $this->mixInvalid;
    }
    
    public function equipmentValid() {
    	return $this->equipmentValid;
    }
    
    public function equipmentPreExpired() {
    	return $this->equipmentPreExpired;
    }
    
    public function equipmentExpired() {
    	return $this->equipmentExpired;
    }
}
?>