<?php

class TypeChain {
	private $value;//we should keep its in main format))
	private $mainValue;
	private $type;
	
	private $typeC;
	
	private $db;
	private $companyID;
	
	private $warnings;

    function TypeChain($value = null, $type = 'Date', $db = null, $categoryID = null, $category = 'company') {
    	$this->value = $value;
    	$this->type = $type;
    	$this->db = $db;
    	if (!is_null($categoryID)) {
	    	if ($category == 'company') {
	    		$this->companyID = $categoryID;
	    	} elseif($category == 'facility') {
	    		$facility = new Facility($this->db);
	    		$facilityDetails = $facility->getFacilityDetails($categoryID);
	    		$this->companyID = $facilityDetails['company_id'];
	    	} elseif($category == 'department') {
	    		$company = new Company($this->db);
	    		$this->companyID = $company->getCompanyIDbyDepartmentID($categoryID);
	    	}
    	}
    	$typeC_className = 'CT'.ucfirst($type);
    	if (class_exists($typeC_className)) {
    		
    		$this->typeC = new $typeC_className($db, $this->companyID);
    	}
    	
    	if (!is_null($this->typeC) && !is_null($this->value)) {
    		//we should check it for its format and try to convert to main Format
    		$this->mainValue = $this->typeC->convert($this->value,true);
    	}
    }
    
    public function setValue($value) {
    	$this->value = $value;
    	$this->mainValue = $this->typeC->convert($this->value,true);
    }
    
    public function formatInput() {
    	if (!is_null($this->value)) {
    	$result = $this->mainValue;//it was already converted
    	$warnings = $this->typeC->getErrorsForConvertedValue($this->value);//var_dump($result,$warnings,$this);
    	return $result;
    	} else {
    		return false;
    	}
    }
    
    public function formatOutput() {
    	if (!is_null($this->value)) {
    		return $this->typeC->convert($this->mainValue);
    	} else {
    		return '';
    	}
    }
    
    public function getTimestamp() {
    	$c = false;
    	try{
    		$c = $this->typeC->getLastStamp(); 
    	} catch(Exception $e) {}
    	return $c;
    }
    
    public function getWarnings() {
    	return $this->warnings;
    }
    
    public function getFormatInfo() {
    	return $this->typeC->getOutputFormat();
    }
    
    public function getFromTypeController($method) {
    	
    	if (method_exists($this->typeC,$method)) {
    		return $this->typeC->$method();
    	} else {
    		return false;
    	}
    }
}
?>