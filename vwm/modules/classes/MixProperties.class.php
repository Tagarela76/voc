<?php

class MixProperties {
	
	protected $db;
	
	protected $equipment;
	protected $department;
	protected $date;
		
	protected $voc;
	protected $voclx;
	protected $vocwx;
	protected $wastePercent;
		
	protected $alreadyExist = false;	
	protected $mixID;
	protected $waste;
	
	protected $creationTime;	//	mm-dd-yyyy
	
	//	limits
	protected $expired;
	protected $preExpired;	
	protected $facilityLimitExcess;
	protected $departmentLimitExcess;	
	protected $facilityAnnualLimitExcess;
	protected $departmentAnnualLimitExcess;
	protected $dailyLimitExcess;
	
	
    function MixProperties($equipment, $department, $waste) {
    	$this->equipment = $equipment;
    	$this->department = $department;
    	$this->date = new Date(time());    	
    	$this->waste = $waste;    	
    }
    
    public function setEquipment($equipment) {
    	$this->equipment = $equipment;
    }
    
    public function getEquipment() {
    	return $this->equipment;
    }
    
    public function setDepartment($department) {
    	$this->department = $department;
    }
    
    public function getDepartment() {
    	return $this->department;
    }
    
    public function setDate($date) {
    	$this->date = $date;
    }
    
    public function getDate() {
    	return $this->date;
    }
    
    public function getVoc() {
    	return $this->voc;
    }
    
    public function getVoclx() {
    	return $this->voclx;
    }
    
    public function getVocwx() {
    	return $this->vocwx;
    }
    
    public function getWastePercent() {
    	return $this->wastePercent;
    }
    
    public function isAlreadyExist() {
    	return $this->alreadyExist;
    }
    
    public function setDB($db) {    	
    	$this->db = $db;
    }
    
    public function getMixID() {
    	return $this->mixID;
    }
    
    public function getWaste() {    	
    	return $this->waste;
    }
    
    
    //	limits
    public function isExpired() {
		return $this->expired;
	}
	
	public function isPreExpired() {
		return $this->preExpired;
	}
	
	public function isFacilityLimitExceeded() {				
		return $this->facilityLimitExcess;
	}
	
	public function isDepartmentLimitExceeded() {
		return $this->departmentLimitExcess;
	}			
	
	public function isFacilityAnnualLimitExceeded() {				
		return $this->facilityAnnualLimitExcess;
	}	
	public function isDepartmentAnnualLimitExceeded() {
		return $this->departmentAnnualLimitExcess;
	}
				
	public function isDailyLimitExceeded() {
		return $this->dailyLimitExcess;
	}
	
	public function setMixID($mixID) {
    	$this->mixID = $mixID;
    }
    
    
    public function setCreationTime($creationTime) {
    	$this->creationTime = $creationTime;
    }
    public function getCreationTime() {
    	return $this->creationTime;
    }
}
?>