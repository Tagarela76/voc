<?php

class MixValidator {

	protected $recalc;
	/**
	 * @var array [annual][facility_id][year] or [monthly][facility_id][year][month]
	 */
	protected $cachedFacilityUsage = array('annual'=>array(), 'monthly'=>array());

	/**
	 * @var array [annual][department_id][year] or [monthly][department_id][year][month]
	 */
	protected $cachedDepartmentUsage = array('annual'=>array(), 'monthly'=>array());

	function MixValidator($recalc = true) {	//	always recalc
		$this->recalc = $recalc;
	}


	//	recalc == true	- calculate isExpired
	//	recalc == false - take value from DB
	private function isExpired($mix) {
		if ($this->recalc) {
			$currentDate = new Date(time());
			$equipmentExpireDate = ($mix->getEquipment()) ? $mix->getEquipment()->getDate() : null;

			//	If Equipment has expire date - check MIX for overdue
			if ($equipmentExpireDate != null) {
				if ($currentDate->isBiggerThan($equipmentExpireDate)) {
					return true;
				} else {
					return false;
				}
			}

		} else {
			return $mix->isExpired();
		}
	}




	//	recalc == true	- calculate isPreExpired
	//	recalc == false - take value from DB
	private function isPreExpired($mix) {
		if ($this->recalc) {
			$currentDate = new Date(time());
			$equipmentExpireDate = ($mix->getEquipment()) ? $mix->getEquipment()->getDate() : null;

			//	If Equipment expire date - current date less than 5 days,
			//	change status to "preExpired"
			if ($equipmentExpireDate != null) {
				$secondsBetween = $equipmentExpireDate->getTimeStamp() - $currentDate->getTimeStamp();
				if ($secondsBetween < 60*60*24*5 && $secondsBetween > 0) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return $mix->isPreExpired();
		}
	}




	//	recalc == true	- calculate isFacilityLimitExceeded
	//	recalc == false - take value from DB
	private function isFacilityLimitExceeded($mix) {
		if ($this->recalc) {
			$equipment = $mix->getEquipment();
			if ($equipment && $equipment->isTrackedTowardsFacility()) {
				if ($mix->getFacility()->getMonthlyLimit() == 0) {
					return false;
				}

				$mixCreationMonth = substr($mix->getCreationTime(),0,2);
				$mixCreationYear = substr($mix->getCreationTime(),-4);

				//$totalFacilityUsage = $mix->getFacility()->getCurrentUsageOptimized((int)$mixCreationMonth, (int)$mixCreationYear);
				if ($this->cachedFacilityUsage['monthly'][$mix->getDepartment()->getFacilityID()][$mixCreationYear][$mixCreationMonth]) {
					$totalFacilityUsage = $this->cachedFacilityUsage['monthly'][$mix->getDepartment()->getFacilityID()][$mixCreationYear][$mixCreationMonth];
				} else {
					$totalFacilityUsage = $mix->getFacility()->getAnnualUsage((int)$mixCreationYear, (int)$mixCreationMonth);
					$this->cachedFacilityUsage['monthly'][$mix->getDepartment()->getFacilityID()][$mixCreationYear][$mixCreationMonth] = $totalFacilityUsage;
				}

				if (!$mix->isAlreadyExist()) {
					$totalFacilityUsage += $mix->getCurrentUsage();
				}

				if ((float)$totalFacilityUsage > (float)$mix->getFacility()->getMonthlyLimit()) {
					return true;
				}
			}
			return false;
		} else {
			return $mix->isFacilityLimitExceeded();
		}
	}




	//	recalc == true	- calculate isDepartmentLimitExceeded
	//	recalc == false - take value from DB
	private function isDepartmentLimitExceeded($mix) {
		if ($this->recalc) {
			$equipment = $mix->getEquipment();
			if ($equipment && $equipment->isTrackedTowardsDepartment()) {
				if ($mix->getDepartment()->getMonthlyLimit() == 0) {
					return false;
				}

				//	veeeeeeeeery slow =(
				//	$totalDepartmentUsage = $mix->getDepartment()->getCurrentUsage();

				//	optimized
				//	get mix creation month
				//$mixDetails = $mix->getMixDetails($mix->getMixID());
				//$mixCreationMonth = substr($mixDetails['creationTime'],0,2);
				//$mixCreationYear = substr($mixDetails['creationTime'],-4);
				$mixCreationMonth = substr($mix->getCreationTime(),0,2);
				$mixCreationYear = substr($mix->getCreationTime(),-4);

				//$totalDepartmentUsage = $mix->getDepartment()->getCurrentUsageOptimized((int)$mixCreationMonth, (int)$mixCreationYear);
				$totalDepartmentUsage = $mix->getDepartment()->getAnnualUsage((int)$mixCreationYear, (int)$mixCreationMonth);

				if (!$mix->isAlreadyExist()) {
					$totalDepartmentUsage += $mix->getCurrentUsage();
				}

				if ((float)$totalDepartmentUsage > (float)$mix->getDepartment()->getMonthlyLimit()) {
					return true;
				}
			}
			return false;
		} else {
			return $mix->isDepartmentLimitExceeded();
		}
	}






	private function isYearlyLimitExceeded($mix) {
		if ($mix->getEquipment()->haveYearlyLimit()) {

		}

		return false;
	}




	private function isQuarterlyLimitExceeded($mix) {
		if ($mix->getEquipment()->haveQuarterlyLimit()) {

		}

		return false;
	}




	//	recalc == true	- calculate isDailyLimitExceeded
	//	recalc == false - take value from DB
	private function isDailyLimitExceeded($mix) {
		if ($this->recalc) {
			$equipment = $mix->getEquipment();
			if ($equipment && $equipment->haveDailyLimit()) {
				if ($mix->getEquipment()->getDailyLimit() == 0) {
					return false;
				}

				//	DAILY DAILY DAILY
				//$mixDetails = $mix->getMixDetails($mix->getMixID());
				//$dailyEquipmentUsage = $mix->getEquipment()->getDailyUsage($mixDetails['creationTime']);
				$dailyEquipmentUsage = $mix->getEquipment()->getDailyUsage($mix->getCreationTime());

				//	Why?
				if (!$mix->isAlreadyExist()) {
					$dailyEquipmentUsage += $mix->getCurrentUsage();
				}

				if ((float)$dailyEquipmentUsage > (float)$mix->getEquipment()->getDailyLimit()) {
					return true;
				}
			}
			return false;
		} else {
			return $mix->isDailyLimitExceeded();
		}

	}




	private function isFacilityAnnualLimitExceeded(Mix $mix) {

		if ($this->recalc) {
			$equipment = $mix->getEquipment();
			if ($equipment && $equipment->isTrackedTowardsFacility()) {
				if ($mix->getFacility()->getAnnualLimit() == 0) {
					return false;
				}

				$mixCreationYear = substr($mix->getCreationTime(),-4);
//				if (false === ($annualUsage = $mix->getFacility()->getAnnualUsage($mixCreationYear)) ) {
//					//	facility usage for defined year is not calculated yet. So...
//					$annualUsage = $mix->getFacility()->calculateAnnualUsage($mixCreationYear);
//				}
				if ($this->cachedFacilityUsage['annual'][$mix->getDepartment()->getFacilityID()][$mixCreationYear]) {
					$annualUsage = $this->cachedFacilityUsage['annual'][$mix->getDepartment()->getFacilityID()][$mixCreationYear];
				} else {
					$annualUsage = $mix->getFacility()->getAnnualUsage((int)$mixCreationYear);
					$this->cachedFacilityUsage['annual'][$mix->getDepartment()->getFacilityID()][$mixCreationYear] = $annualUsage;
				}

				if (!$mix->isAlreadyExist()) {
					$annualUsage += $mix->getCurrentUsage();
				}

				if ((float)$annualUsage > (float)$mix->getFacility()->getAnnualLimit()) {
					return true;
				}
			}
			return false;
		} else {
			return $mix->isFacilityAnnualLimitExceeded();
		}
	}


	private function isDepartmentAnnualLimitExceeded(Mix $mix) {

		if ($this->recalc) {
			$equipment = $mix->getEquipment();
			if ($equipment && $equipment->isTrackedTowardsDepartment()) {
				if ($mix->getDepartment()->getAnnualLimit() == 0) {
					return false;
				}

				$mixCreationYear = substr($mix->getCreationTime(),-4);
//				if (false === ($annualUsage = $mix->getDepartment()->getAnnualUsage($mixCreationYear)) ) {
//					//	department usage for defined year is not calculated yet. So...
//					$annualUsage = $mix->getDepartment()->calculateAnnualUsage($mixCreationYear);
//				}
				$annualUsage = $mix->getDepartment()->getAnnualUsage((int)$mixCreationYear);

				if (!$mix->isAlreadyExist()) {
					$annualUsage += $mix->getCurrentUsage();
				}

				if ((float)$annualUsage > (float)$mix->getDepartment()->getAnnualLimit()) {
					return true;
				}
			}
			return false;
		} else {
			return $mix->isDepartmentAnnualLimitExceeded();
		}
		}




	public function isValidMix($mix) {
		$mixValidatorResponse = new MixValidatorResponse();

		//	Assign default values
		$mixValidatorResponse->setValidationStatus(true);

		//	Check if expired
		if ($this->isExpired($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setExpired(true);
		} else {
			$mixValidatorResponse->setExpired(false);
		}

		//	Check if will be expired in a nearest feature
		if ($this->isPreExpired($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setPreExpired(true);
		} else {
			$mixValidatorResponse->setPreExpired(false);
		}

		//	Check if FACILITY LIMIT is exceeded
		if ($this->isFacilityLimitExceeded($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setFacilityLimitExcess(true);
		} else {
			$mixValidatorResponse->setFacilityLimitExcess(false);
		}

		//	Check if DEPARTMENT LIMIT is exceeded
		if ($this->isDepartmentLimitExceeded($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setDepartmentLimitExcess(true);
		} else {
			$mixValidatorResponse->setDepartmentLimitExcess(false);
		}

		//	Check if DAILY LIMIT is exceeded
		if ($this->isDailyLimitExceeded($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setDailyLimitExcess(true);
		} else {
			$mixValidatorResponse->setDailyLimitExcess(false);
		}

		//	Check if FACILITY ANNUAL LIMIT is exceeded
		if ($this->isFacilityAnnualLimitExceeded($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setFacilityAnnualLimitExceeded(true);
		} else {
			$mixValidatorResponse->setFacilityAnnualLimitExceeded(false);
		}

		//	Check if DEPARTMENT ANNUAL LIMIT is exceeded
		if ($this->isDepartmentAnnualLimitExceeded($mix)) {
			$mixValidatorResponse->setValidationStatus(false);
			$mixValidatorResponse->setDepartmentAnnualLimitExceeded(true);
		} else {
			$mixValidatorResponse->setDepartmentAnnualLimitExceeded(false);
		}


				//	Check if YEARLY LIMIT is exceeded
//		if ($this->isYearlyLimitExceeded($mix)) {
//			$mixValidatorResponse->setValidationStatus(false);
//			$mixValidatorResponse->setYearlyLimitExcess(true);
//		} else {
//			$mixValidatorResponse->setYearlyLimitExcess(false);
//		}
//
//		//	Check if QUARTERLY LIMIT is exceeded
//		if ($this->isQuarterlyLimitExceeded($mix)) {
//			$mixValidatorResponse->setValidationStatus(false);
//			$mixValidatorResponse->setQuarterlyLimitExcess(true);
//		} else {
//			$mixValidatorResponse->setQuarterlyLimitExcess(false);
//		}
		return $mixValidatorResponse;
	}
}
?>