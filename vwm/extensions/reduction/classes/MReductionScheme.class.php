<?php

class MReductionScheme {

    function MReductionScheme() {
    }
    
    public function getNewObject($db) {
    	return new ReductionScheme($db);
    }
    
    /**
     * function prepareView($params) - prepare params for smarty to view reduction scheme list
     * @param array params - $db, $facilityID
     * @return array for smarty
     */
    public function prepareView($params) {
    	extract($params);
    	$reduction = new ReductionScheme($db);    	
    	$reduction->loadFactors($facilityID);
    	$facility = new Facility($db);
    	$solvent = new SolventManagement($db,$facilityID);
    	
    	$company = new Company($db);
    	$facilityDetails = $facility->getFacilityDetails($facilityID);
    	$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);
    	$reduction->unittypeID = $companyDetails['voc_unittype_id'];
    	$unittype = new Unittype($db);
    	$unittypeDescr = $unittype->getDescriptionByID($reduction->unittypeID);
    	$result = array(    		
    		'reduction' => $reduction->getReductionScheme($facilityID),
    		'mulFactorARE' => $reduction->getAREfactor(),
    		'mulFactorTargetEmission' => $reduction->getTargetEmissionFactor(),
    		'unittype' => $unittypeDescr,
    		'solventPlan' =>$solvent->getAnnualActualSolventEmissionList($facilityID)
    	);
  	
    	return $result;
    }
    /**
     * function prepareSaveFactors($params) -save factors
     * @param array params - $db, $facilityID,$AREfactor,$TEfactor
     */
    public function prepareSaveFactors($params) {
    	extract($params);
    	$reduction = new ReductionScheme($db);
    	$reduction->setFactors($facilityID,$AREfactor,$TEfactor);
    }
    
    /**
     * function prepareViewSolventPlan($params) - prepare params for smarty to view solvent plan outputs list
     * @param array params - $db, $facilityID, $period, $periodType, $companyID
     */
    public function prepareViewSolventPlan($params) {
    	extract($params);
    	$sm = new SolventManagement ($db, $facilityID);
    	$sm->iniOutputNames();
    	switch ( $periodType ) {
			case 'month':
				if ($period['month'] === null) {
					$period = $sm->getLastUseDate($facilityID);
				}
				$sm->getMonthlyOutputs($period['month'],$period['year']);
					if (!$sm->isAlreadyExist) {
						header ("Location: ?action=edit&category=solventplan&facilityID=$facilityID&mm=".$period['month']."&yyyy=".$period['year']."");
					}
				break;
			case 'quarter':
				$sm->getQuarterlyOutputs($period['quarter'], $period['year']);
				break;
			case 'semi-year':
				$sm->getSemiAnnualyOutputs($period['period'],$period['year']);
				break;
			case 'year':
				$sm->getAnnualyOutputs($period['year']);
				break;	
			default:
				break;
		}
		$company = new Company($db);
		$companyDetails = $company->getCompanyDetails($companyID);
		$unittype = new Unittype($db);
		$unittypeDescr = $unittype->getNameByID($companyDetails['voc_unittype_id']);
		$fields = $this->getFields();
		$currentDate = getdate();
		return array(
			'period' => $period,
			'periodType' => $periodType,
			'curYear' => $currentDate['year'],
			'data' => $sm,
			'fields' => $fields,
			'unittype' => $unittypeDescr
		);
    }
    
    /**
     * function prepareAddSolventPlan($params) - prepare params to add/edit solvent plan outputs
     * @param array params - $db, $facilityID, $month, $year, $request, $save = true/false, $outputs
     */
    public function prepareAddSolventPlan($params) {
    	extract($params);
    	$fields = $this->getFields();
    	$sm = new SolventManagement($db,$facilityID);
    	$facility = new Facility($db);
    	$facility->initializeByID($facilityID);
    	$currentDate = getdate();
    	if (($year > $currentDate['year']) || ($year == $currentDate['year'] && $month >= $currentDate['mon'])) {
    		$year = $currentDate['year'];
    		$month = $currentDate['mon']-1;
    		if ($month == 0) {
    			$year--;
    			$month = 12;
    		}
    	}
    	$input = $facility->getCurrentUsageOptimized($month,$year);
    	if (!$input) {
    		$input = 0;
    	}
    	$noOutputs = false;
    	$sm->getMonthlyOutputs($month,$year);
    	if (!$sm->isAlreadyExist) {
    		foreach ($fields as $field) {
    			$sm->$field = 0;
    		}
    		$noOutputs = true;
    	}
    	$sm->iniOutputNames();
    	$validation['summary'] = 'success';
    	if ($save) {
    		$sumOut = 0;
    		foreach($outputs as $output_id => $value) {
    			$sm->$output_id = $value;
    			if (!is_numeric($value) || $value < 0 ) {
    				$validation['summary'] = 'failed';
    				$validation[$output_id] = 'Error! Please enter valid value!';
    			} else {
    				$sumOut += $value;
    			}
    		}
    		if ((string)$input != (string)$sumOut) {
    			$validation['summary'] = 'failed';
    			$validation['input'] = 'Error! Input does not equal output!';
    		}
    		if ($validation['summary'] == 'success') {
    			$sm->save();
    			return true;
    		}
    	}
    	$company = new Company($db);
		$companyDetails = $company->getCompanyDetails($facility->getCompanyID());
		$unittype = new Unittype($db);
		$unittypeDescr = $unittype->getNameByID($companyDetails['voc_unittype_id']);
		$currentDate = getdate();
    	return array(
    		'data' => $sm,
    		'validation' => $validation,
    		'input' => $input,
    		'month' => $month,
    		'year' => $year,
			'fields' => $fields,
			'unittype' => $unittypeDescr,
    		'curYear'	=> $currentDate['year'],
    		'noOutputs'	=> $noOutputs
    	);
    }
    
    /**
     * function prepareMixSave($params) - correct outputs if mixes from past was changed
     * @param array params - $db, $facilityID, $monthOld, $yearOld, $month, $year, $newVOC, $oldVOC
     */
    public function prepareMixSave($params) {
    	extract($params);
		if (($month == $monthOld && $year == $yearOld) || ($oldVOC == 0)) {
			$voc = $newVOC - $oldVOC;
			$paramsForSave = array(
				'db' => $db,
				'month' => $month,
				'year' => $year,
				'facilityID' => $facilityID,
				'voc' => $voc
			);
			
			$this->saveOutputsForMix($paramsForSave);
		} else {
			
			$paramsForSave = array(
				'db' => $db,
				'month' => $monthOld,
				'year' => $yearOld,
				'facilityID' => $facilityID,
				'voc' => -$oldVOC
			);
			$this->saveOutputsForMix($paramsForSave);
			$paramsForSave = array(
				'db' => $db,
				'month' => $month,
				'year' => $year,
				'facilityID' => $facilityID,
				'voc' => $newVOC
			);
			$this->saveOutputsForMix($paramsForSave); 
		}
    }
    
    /**
     * function prepareMixRollback($params) - correct outputs in case mix rollback 
     * @param array $params - $db, $data, $crud = C/U/D
     */
    public function prepareMixRollback($params) {
    	extract($params);//TODO: should we use trackong trash system for solventManagement or all ok with only correcting outputs?!!
    	$mix = new Mix($db);
    	if ($crud == 'C') {
    		$vocNew = -$data->voc;
    		$vocOld = 0;
    	} else {
	    	$isMixExist = $mix->initializeByID($data->mix_id); 
	    	if ($isMixExist === false) {
	    		$vocOld = 0;
	    	} else {
		    	$dateOld['mon'] = substr($mix->getCreationTime(),0,2); //her creation time in mm-dd-yyyy format
		    	$dateOld['year'] = substr($mix->getCreationTime(),-4,4);
		    	$vocOld = $mix->getVoc();
	    	}
	    	$vocNew = $data->voc;
    	}
    	$dateNew = getdate(strtotime($data->creation_time)); //for rollback new date would be from trash
    	$department = new Department($db);
	    $department->initializeByID($data->department_id);
    	$paramsForSave = array(
	    	'db' => $db,
			'facilityID' => $department->getFacilityID(),
			'month' => $dateNew['mon'],
			'year' => $dateNew['year'],
			'monthOld' => $dateOld['mon'],
			'yearOld' => $dateOld['year'],
			'oldVOC' => $vocOld,
			'newVOC' => $vocNew
    	);
    	$this->prepareMixSave($paramsForSave);
    }
    
    /**
     * function saveOutputsForMix($params) - correct outputs if mixes from past was changed
     * @param array params - $db, $facilityID, $month, $year, $voc(it can be negative in case voc is old)
     */    
    private function saveOutputsForMix($params) {
    	
    	extract($params);
    	$lastSolvents = new SolventManagement($db, $facilityID);
    	$currentDate = getdate();
    	if ($year > $currentDate['year'] || ($year == $currentDate['year'] && $month >=$currentDate['mon'])) return; // we dont save outputs for current and future monthes!
		$last = $lastSolvents->getMonthlyOutputs($month, $year);
		$fields = $this->getFields();
		
		if (!$last) {
			//there are no monthly outputs
			//in that case we should ignore changes of voc... it all'll be set on edit solvent plan
			return;
		}
		
		$value = $voc;
		foreach($fields as $field) {
			$lastSolvents->$field = $lastSolvents->$field + $value;
			if ($lastSolvents->$field >= 0 ) {
				$value = 0;
				break;
			} else {
				$value = $lastSolvents->$field;
				$lastSolvents->$field = 0;
			}
		}
		$lastSolvents->save();
    }
    
    public function getFields() {
    	return array('o1','o2','o3','o4','o5','o6','o7','o8','o9');
    }
}
?>