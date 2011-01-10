<?php

class DepartmentProperties {
	
	protected $currentUsage = 0;
	protected $departmentID;
	protected $facilityID;
	protected $vocLimit;
	protected $name;
	
	/**
	 * 
	 * wanna usage for 2008? $department->getAnnualUsage('2008');
	 * @var array(year=>value)
	 */
	protected $annualUsage = array();
	protected $vocAnnualLimit;

    function DepartmentProperties() {
    }
    
    public function getDailyLimit() {
    	return $this->vocLimit;
    }
    
    public function setFacilityID($facilityID) {
    	$this->facilityID = $facilityID;
    }
    public function getFacilityID() {
    	return $this->facilityID;
    }
    
    public function setName($name) {
    	$this->name = $name;
    }
    public function getName() {
    	return $this->name;
    }
    
    
    public function setDepartmentID($departmentID) {
    	$this->departmentID = $departmentID;
    }
    public function getDepartmentID() {
    	return $this->departmentID;
    }
    
	public function getAnnualLimit() {
    	return $this->vocAnnualLimit;
    }
 	/**
     * 
     * wanna usage for 2008? $department->getAnnualUsage('2008');
     * @param string $year
     * @param string $mm month
     * @return float annual usage or false if no data for such period
     */
    public function getAnnualUsage($year, $mm = null) {    	
    	if ($mm === null) {
    		return (isset($this->annualUsage[$year])) ? array_sum($this->annualUsage[$year]) : false;	
    	} else {    		    		
    		return (isset($this->annualUsage[$year][$mm])) ? $this->annualUsage[$year][$mm] : false;
    	}
    	 
    }
}
?>