<?php

class ProductProperties {
	
	protected $productID;
	protected $productNR;
	protected $supplier;
	protected $name;
	
	
	protected $voclx;
	protected $vocwx;
	protected $density;
	protected $densityUnitID;
	
	protected $perccentVolatileWeight;
	protected $perccentVolatileVolume;
	
	protected $hazardousClass;
	
	protected $components;

    function ProductProperties() {
    	$this->hazardous = new Hazardous();
    }
    
    //		GETTERS
    public function getProductID() {
    	return $this->productID;
    }
    public function getSupplier() {
    	return $this->supplier;
    }
    public function getProductNR() {
    	return $this->productNR;
    }
    public function getName() {
    	return $this->name;
    }
    public function getVoclx() {
    	return $this->voclx;
    }
    
    public function getVocwx() {
    	return $this->vocwx;
    }
    
    public function getComponents() {
    	return $this->components;
    }
    
    public function setComponents($components) {
    	$this->components = $components;
    }
    
    public function getDensity() {
    	return $this->density;
    }
    public function getDensityUnitID() {
    	return $this->densityUnitID;
    }        
    public function setDensity($density) {
    	$this->density = $density;
    }  
    public function setDensityUnitID($densityUnitID) {
    	$this->densityUnitID = $densityUnitID;
    }         
    
    public function setProductID($productID) {
    	$this->productID = $productID;
    }
    public function setSupplier($supplier) {
    	$this->supplier = $supplier;
    }
    public function setProductNR($productNR) {
    	$this->productNR = $productNR;
    }
    public function getPercentVolatileWeigh() {
    	return $this->perccentVolatileWeight;
    }
    public function getPercentVolatileVolume() {
    	return $this->perccentVolatileVolume;
    }
    public function setName($name) {
    	$this->name = $name;
    }
}
?>