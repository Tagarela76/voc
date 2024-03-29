<?php

class FacilityProperties {
	protected $facilityID;
	protected $currentUsage = 0;

	/**
	 *
	 * wanna usage for 2008? $facility->getAnnualUsage('2008');
	 * @var array(year=>value)
	 */
	protected $annualUsage = array();

	protected $vocLimit;
	protected $monthlyNoxLimit;
	protected $vocAnnualLimit;
	protected $companyID;

    function FacilityProperties() {
    }

    public function getMonthlyLimit() {
    	return $this->vocLimit;
    }
	
	public function getMonthlyNoxLimit() {
    	return $this->monthlyNoxLimit;
    }

    public function getCompanyID() {
    	return $this->companyID;
    }

    public function getFacilityID() {
    	return $this->facilityID;
    }

    public function getAnnualLimit() {
    	return $this->vocAnnualLimit;
    }

    /**
     *
     * wanna usage for 2008? $facility->getAnnualUsage('2008');
     * @param string $year
     * @param string $mm month
     * @return float annual usage or false if no data for such period
     */
    public function getAnnualUsage($year, $mm = null) {
    	if ($mm === null) {
    		return (isset($this->annualUsage[$year])) ? array_sum($this->annualUsage[$year]) : 0;
    	} else {
    		return (isset($this->annualUsage[$year][$mm])) ? $this->annualUsage[$year][$mm] : 0;
    	}

    }
}
?>