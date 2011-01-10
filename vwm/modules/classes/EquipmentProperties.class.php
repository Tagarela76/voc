<?php

class EquipmentProperties {
	
	protected $date;
	protected $trackTowardsDepartment;
	protected $trackTowardsFacility;
	
	protected $yearlyLimit;
	protected $quarterlyLimit;
	protected $dailyLimit;
	
	protected $currentUsage;
	
	protected $equipment_id;
	
    function EquipmentProperties() {
    }
    
    public function setTrackTowardsDepartment($trackTowardsDepartment) {
    	$this->trackTowardsDepartment = $trackTowardsDepartment;
    }
    
    public function setTrackTowardsFacility($trackTowardsFacility) {
    	$this->trackTowardsFacility = $trackTowardsFacility;
    }
    
    public function setYearlyLimit($yearlyLimit) {
    	$this->yearlyLimit = $yearlyLimit;
    }
    
    public function setQuarterlyLimit($quarterlyLimit) {
    	$this->quarterlyLimit = $quarterlyLimit;
    }
    
    public function setDailyLimit($dailyLimit) {
    	$this->dailyLimit = $dailyLimit;
    }
    
    //		GETTERS
    public function getYearlyLimit() {
    	return $this->yearlyLimit;
    }
    
    public function getQuarterlyLimit() {
    	return $this->quarterlyLimit;
    }
    
    public function getDailyLimit() {
    	return $this->dailyLimit;
    }
    
    public function getDate() {
		return $this->date;
	}
		
	public function isTrackedTowardsDepartment() {
		return $this->trackTowardsDepartment;
	}
	
	public function isTrackedTowardsFacility() {
		return $this->trackTowardsFacility;
	}
	
	public function haveYearlyLimit() {
		if ($this->yearlyLimit > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function haveQuarterlyLimit() {
		if ($this->quarterlyLimit > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function haveDailyLimit() {
		if ($this->dailyLimit > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function isExpired() {
		$currentDate = new Date(time());
		$equipmentExpireDate = $this->getDate();
		
//		echo "Current: ".$currentDate->getTimeStamp()." \t Equipment: ".$equipmentExpireDate."<br>";
		
		if ($equipmentExpireDate != null) {
			if ($currentDate->isBiggerThan($equipmentExpireDate)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function isPreExpired() {
		$currentDate = new Date(time());
		$equipmentExpireDate = $this->getDate();
		
//		echo "Current: ".$currentDate->getTimeStamp()." \t Equipment: ".$equipmentExpireDate."<br>";
		
		
		if ($equipmentExpireDate != null) {
			$secondsBetween = $equipmentExpireDate->getTimeStamp() - $currentDate->getTimeStamp();
			if ($secondsBetween < 60*60*24*5 && $secondsBetween > 0) {
				return true;
			} else {
				return false;
			}
		}

	}
	
}
?>