<?php

class MixValidatorResponse {
	
	private $validationStatus = true;
	
	private $expired;
	private $preExpired;
	
	private $facilityLimitExcess;
	private $departmentLimitExcess;
	private $facilityAnnualLimitExcess;
	private $departmentAnnualLimitExcess;
	private $dailyLimitExcess;

	
	private $yearLimitExcess;
	private $quarterlyLimitExcess;

	
	function MixValidatorResponse() {
		//    	$this->validationStatus = true;
	}
	
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	
	public function setPreExpired($preExpired) {
		$this->preExpired = $preExpired;
	}
	
	public function setFacilityLimitExcess($facilityLimitExcess) {
		$this->facilityLimitExcess = $facilityLimitExcess;
	}
	
	public function setDepartmentLimitExcess($departmentLimitExcess) {
		$this->departmentLimitExcess = $departmentLimitExcess;
	}
	
	public function setYearlyLimitExcess($yearlyLimitExcess) {
		$this->yearlyLimitExcess = $yearlyLimitExcess;
	}
	
	public function setQuarterlyLimitExcess($quarterlyLimitExcess) {
		$this->quarterlyLimitExcess = $quarterlyLimitExcess;
	}
	
	public function setDailyLimitExcess($dailyLimitExcess) {
		$this->dailyLimitExcess = $dailyLimitExcess;
	}
	
	public function setValidationStatus($validationStatus) {
		$this->validationStatus = $validationStatus;
	}
	
	public function isValid() {
		return $this->validationStatus;
	}
	
	public function isExpired() {
		return $this->expired;
	}
	
	public function isPreExpired() {
		return $this->preExpired;
	}
	
	public function isSomeLimitExceeded() {
		return	$this->isDailyLimitExceeded()
			or	$this->isDepartmentLimitExceeded()
			or	$this->isFacilityLimitExceeded()
			or 	$this->getFacilityAnnualLimitExceeded()
			or	$this->getDepartmentAnnualLimitExceeded();
	}
	
	public function isFacilityLimitExceeded() {
		return $this->facilityLimitExcess;
	}
	
	public function isDepartmentLimitExceeded() {		
		return $this->departmentLimitExcess;
	}
	
	public function isYearlyLimitExceeded() {
		return $this->yearlyLimitExcess;
	}
	
	public function isQuarterlyLimitExceeded() {
		return $this->quarterlyLimitExcess;
	}
	
	public function isDailyLimitExceeded() {
		return $this->dailyLimitExcess;
	}
	
	public function setFacilityAnnualLimitExceeded($facilityAnnualLimitExceeded) {
		$this->facilityAnnualLimitExcess = $facilityAnnualLimitExceeded;
	}
	public function setDepartmentAnnualLimitExceeded($departmentAnnualLimitExceeded) {
		$this->departmentAnnualLimitExcess = $departmentAnnualLimitExceeded;
	}
	
	public function getFacilityAnnualLimitExceeded() {
		return $this->facilityAnnualLimitExcess;
	}
	public function getDepartmentAnnualLimitExceeded() {
		return $this->departmentAnnualLimitExcess;
	}
	
	
	
	public function printResult() {
		echo "<br>===Result of MIX validation===<br>";
		echo "Status: ";
		if ($this->isValid()) {
			echo "valid";
		} else {
			echo "failed";
		}
		echo "<br>";
		
		echo "Department limit: ";
		if ($this->isDepartmentLimitExceeded()) {
			echo "exceeded";
		} else {
			echo "not exceeded";
		}
		echo "<br>";
		
		
		echo "Facility limit: ";
		if ($this->isFacilityLimitExceeded()) {
			echo "exceeded";
		} else {
			echo "not exceeded";
		}
		echo "<br>";
		
		echo "Daily limit: ";
		if ($this->isDailyLimitExceeded()) {
			echo "exceeded";
		} else {
			echo "not exceeded";
		}
		echo "<br>";
		
		echo "Is expired: ";
		if ($this->isExpired()) {
			echo "yes";
		} else {
			echo "no";
		}
		echo "<br>";
		
		echo "Is pre expired: ";
		if ($this->isPreExpired()) {
			echo "yes";
		} else {
			echo "no";
		}
		echo "<br><br>";
	}
	
}
?>