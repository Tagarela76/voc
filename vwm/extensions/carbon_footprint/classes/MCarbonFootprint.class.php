<?php

class MCarbonFootprint {

    function MCarbonFootprint() {
    }
    
    public function getNewObject($db) {
    	return new CarbonFootprint($db);
    } 
    
    public function getNewEmissionFactorObject($db, $id) {
    	return new EmissionFactor($db, $id);
    }
    
    /**
     * function prepareView($params)
     * @param array params - $db, $periodType, $period, facilityID
     * @return array for smarty
     */
    public function prepareView($params) {
    	extract($params);
    	$carbonFootprint = new CarbonFootprint($db, $facilityID);
    	$limits = $carbonFootprint->getLimits(); //TODO: how to display month and annual indicators?!
    	$usage = array();
    	if ($limits['monthly']['show']) {
    		$usage['monthly'] = $carbonFootprint->getMonthlyCurrentUsage();
    	}
    	if ($limits['annual']['show']) {
    		$usage['annual'] = $carbonFootprint->getAnnualCurrentUsage();
    	}
    	switch($periodType) {
    		case 'month':
    			$emissionsList = $carbonFootprint->getMonthlyEmissions($period['month'],$period['year']);
    			$indirectEmission = $carbonFootprint->getMonthlyIndirectEmission($period['month'],$period['year']);
    			break;
    		case 'quarter':
    			$emissionsList = $carbonFootprint->getQuarterlyEmissions($period['quarter'],$period['year']);
    			$indirectEmission = $carbonFootprint->getQuarterlyIndirectEmission($period['quarter'],$period['year']);
    			break;
    		case 'semi-year':
    			$emissionsList = $carbonFootprint->getSemiAnnualyEmissions($period['period'],$period['year']);
    			$indirectEmission = $carbonFootprint->getSemiAnnualyIndirectEmission($period['period'],$period['year']);
    			break;
    		case 'year':
    			$emissionsList = $carbonFootprint->getAnnualyEmissions($period['year']);
    			$indirectEmission = $carbonFootprint->getAnnualyIndirectEmission($period['year']);
    			break;
    	}
    	$totalDirectEmissions = 0;
    	$total = 0;
    	$unittype = new Unittype($db);
    	if ($indirectEmissions != false) {
    		$indirectEmission->unittype_id = $unittype->getNameByID($indirectEmission->unittype_id); //all unittype_id was changed on theirs names
    	}
    	foreach($emissionsList as $emission) {
    		$totalDirectEmissions += $emission->tco2;
    		if ($periodType != 'month') {
    			$emission->unittype_id = $unittype->getNameByID($emission->emissionFactor->unittype_id); //all unittype_id was changed on theirs names
    		} else {
    			$emission->unittype_id = $unittype->getNameByID($emission->unittype_id); //all unittype_id was changed on theirs names
    		}
    		$emission->quantity = round($emission->quantity,4);
    	}
    	$total = $totalDirectEmissions + $indirectEmission->tco2;;
    	$result = array(
    		'directEmissionsList' => $emissionsList,
    		'indirectEmission' => $indirectEmission,
    		'totalDirectEmissions' => $totalDirectEmissions,
    		'total' => $total,
    		'periodType' => $periodType,
    		'period' => $period,
    		'curYear' => substr(date("Y-m-d",time()),0,4),
    		'limits' => $limits,
    		'usage' => $usage
    	);
    	return $result;
    }
    
    /**
     * function prepareEditIndirect($params) - prepare data to edit indirect emission(electricity)
     * @param array params - $db, $quantity, $adjustment, $certificate_value, $credit_value, $month, $year, $request, $facilityID, $save = false/true
     * @return array for smarty
     */
    public function prepareEditIndirect($params) {
    	extract($params);
    	$carbonFootprint = new CarbonFootprint($db,$facilityID);
    	$carbonEmission = $carbonFootprint->getMonthlyIndirectEmission($month,$year);
    	if (!$carbonEmission) {
    		$carbonEmission = new CarbonEmissions($db);
    		$carbonEmission->emission_factor_id = EmissionFactor::ELECTRICITY_FACTOR_ID;
    		$carbonEmission->emissionFactor = new EmissionFactor($db, $carbonEmission->emission_factor_id);
    	}
    	$validation['summary'] = 'success';
    	if ($save) 
    	{
	    	if (!is_null($quantity) && $quantity != "") {
	    		$carbonEmission->quantity = $quantity;
	    	} else {
	    		$carbonEmission->quantity = 0;
	    	}
    	}
    	if ($carbonEmission->quantity < 0 || !is_numeric($carbonEmission->quantity)) {
    		$validation['summary'] = 'failed';
    		$validation['quantity'] = 'Error! Please enter valid quantity!';
    	}
    	if (!is_null($adjustment) && $adjustment != "") {
    		$carbonEmission->adjustment = $adjustment;
    		if (!is_numeric($adjustment) || $adjustment < 0) {
    			$validation['summary'] = 'failed';
    			$validation['adjustment'] = 'Error! Please enter valid adjustment value!';
    		}
    	} else {
    		if ($save)   
    		$carbonEmission->adjustment = 0;
    	}
    	if (!is_null($certificate_value) && $certificate_value != "") {
    		$carbonEmission->certificate_value = $certificate_value;
    		if (!is_numeric($certificate_value) || $certificate_value < 0) {
    			$validation['summary'] = 'failed';
    			$validation['certificate_value'] = 'Error! Please enter valid certificate value!';
    		}
    	} else {
    		if ($save)     		
    			$carbonEmission->certificate_value = 0;    		
    	}
    	if (!is_null($credit_value) && $credit_value != "") {
    		$carbonEmission->credit_value = $credit_value;
    		if (!is_numeric($credit_value) || $credit_value < 0) {
    			$validation['summary'] = 'failed';
    			$validation['credit_value'] = 'Error! Please enter valid credit value!';
    		}
    	} else {
    		if ($save)   
    			$carbonEmission->credit_value = 0;
    	}
    	$carbonEmission->unittype_id = $carbonEmission->emissionFactor->unittype_id;
    	$carbonEmission->month = $month;
    	$carbonEmission->year = $year;
    	$carbonEmission->facility_id = $facilityID;
    	if ($save && $validation['summary'] == 'success') {
    		$carbonEmission->save();
    		return true;
    	} else {
    		return array (
    			'validation' => ($save)?$validation:array('summary' => 'success'),
    			'data' => $carbonEmission,
    			'month' => $month,
    			'year' => $year
    		);
    	}
    }
    
    /**
     * function prepareAddEditDirect($params) - prepare data to add/edit direct emission
     * @param array params - $db, $emission_factor_id = null, $description = null, $adjustment = null,
     *  $quantity = null, unittype_id = null, $request, $facilityID, $save = false/true, $month, $year
     * @return array for smarty
     */
    public function prepareAddEditDirect($params) {
    	extract($params);
    	$facility = new Facility($db);
    	$facilityDetails = $facility->getFacilityDetails($facilityID);
    	$unittype = new Unittype($db);
    	$validation['summary'] = 'success';
    	$categories = array('weight', 'volume', 'energy');
    	foreach ($categories as $category) {
    		$unittypeList[$category] = $unittype->getUnittypeListByCategory($category,$facilityDetails['company_id']);
    	}	
    	if (isset($request['id'])) {
	    	$carbonEmission = new CarbonEmissions($db,$request['id']);
	    	$type = $unittype->isWeightOrVolume($carbonEmission->unittype_id);
	    	$unittypeList = $unittypeList[$type];
	    	$month = $carbonEmission->month;
	    	$year = $carbonEmission->year;
	    	if ($unittype_id != null) {
	    		$carbonEmission->unittype_id = $unittype_id;
	    	}
    	} else {
    		$carbonEmission = new CarbonEmissions($db);
    		$carbonEmission->facility_id = $facilityID;
    		$carbonFootprint = new CarbonFootprint($db,$facilityID);
    		$directEmissionsListFull = $carbonFootprint->getAllDirectEmissionFactors();
    		$idsExist = $carbonFootprint->getEmissionFactorIdArrayExist($month,$year);
    		$directEmissionsList = array();
    		foreach($directEmissionsListFull as $emissionFactor) {
    			if (!in_array($emissionFactor->id,$idsExist)) {
    				$directEmissionsList []= $emissionFactor;
    			}
    		}
    		if ($emission_factor_id != null) {
    			$carbonEmission->emission_factor_id = $emission_factor_id;
    			$carbonEmission->emissionFactor = new EmissionFactor($db, $emission_factor_id);
    		} else {
    			$validation['factor_id'] = 'Error! Please select factor!';
    			$validation['summary'] = 'failed';
    		}
    		$carbonEmission->unittype_id = $unittype_id;
    	}
    	if ($description != null) {
    		$carbonEmission->description = $description;
    	} else {
    		$validation['summary'] = 'failed';
    		$validation['description'] = 'Error! Please enter description!';
    	}
    	  	
    	if ($save) {
    		if (is_null($adjustment) || $adjustment == "") {
		    	$carbonEmission->adjustment = 0;
	    	} else {
		    	$carbonEmission->adjustment = $adjustment;
	    	}
    		if (!is_numeric($carbonEmission->adjustment) || $carbonEmission->adjustment < 0) {
    			$validation['summary'] = 'failed';
    			$validation['adjustment'] = 'Error! Please enter valid adjustment value!';
    		}
    		if (is_null($quantity) || $quantity == "") {
		    	$carbonEmission->quantity = 0;
	    	} else {
		    	$carbonEmission->quantity = $quantity;
	    	}    	
	    	if ( $carbonEmission->quantity <= 0 || !is_numeric($carbonEmission->quantity)) {
	    		$validation['summary'] = 'failed';
	    		$validation['quantity'] = 'Error! Please enter valid quantity!';
	    	} 
    	}
    	
    	//TODO: при edit нужно сохранять старые month, year
    	$carbonEmission->month = $month;
    	$carbonEmission->year = $year;
    	
    	if ($save && $validation['summary'] == 'success') {
    		$carbonEmission->save();
    		return true;
    	} else {
    		return array(
    			'validation' => ($save)?$validation:array('summary' => 'success'),
    			'data' => $carbonEmission,
    			'month' => $month,
    			'year' => $year,
    			'unittypeList' =>(isset($request['id']))?$unittypeList:json_encode($unittypeList),
    			'directEmissionsList' =>$directEmissionsList
    		);
    	}
    }
    
    /**
     * function prepareDelete($params) - prepare data to delete selected emissions
     * @param array params - $db, $idArray, $confirmed = false/true
     * @return array for smarty/true
     */
    public function prepareDelete($params) {
    	extract($params);
    	if (!$confirmed) {
    		$arrayForDelete = array();
    		foreach($idArray as $id) {
    			$emission= new CarbonEmissions($db, $id);
    			$arrayForDelete []= array(
    				'id' => $emission->id,
    				'name' => "Factor: ".$emission->emissionFactor->name.". Period: $emission->month/$emission->year"
    			);
    		}
    		return array('itemForDelete' => $arrayForDelete);
    	} else {
    		$carbonEmission = new CarbonEmissions($db);
    		foreach($idArray as $id) {
    			$carbonEmission->delete($id);
    		}
    		return true;
    	}
    }
    
    /**
     * function prepareSetLimits($params) - prepare data to set monthly and annual carbon footprint limits
     * @param array params - $db, $facilityID, $monthlyLimit, $annualLimit, $monthlyShow = 0/1, $annualShow = 0/1, $save = false/true
     */
    public function prepareSetLimits($params) {
    	extract($params);
    	$carbonFootprint = new CarbonFootprint($db,$facilityID);
    	$limits = $carbonFootprint->getLimits();
    	$validation['summary'] = 'success';
    	if ($save) {
    		if ($monthlyLimit == null || !is_numeric($monthlyLimit) || $monthlyLimit <= 0) {
    			$validation['summary'] = 'failed';
    			$validation['month_value'] = 'Error! Please enter valid month limit value!';
    		}
    		if ($annualLimit == null || !is_numeric($annualLimit) || $annualLimit <= 0) {
    			$validation['summary'] = 'failed';
    			$validation['annual_value'] = 'Error! Please enter valid year limit value!';
    		}
    		if ($validation['summary'] == 'success') {
    			$carbonFootprint->setLimit(CarbonFootprint::MONTHLY,$monthlyLimit,$monthlyShow);
    			$carbonFootprint->setLimit(CarbonFootprint::ANNUAL,$annualLimit,$annualShow);
    			return true;
    		} else {
    			$limits = array (
					'monthly' => array('value'=>$monthlyLimit, 
										'show'=>($monthlyShow == 0) ? false : true),
					'annual' => array('value'=>$annualLimit, 
										'show'=>($annualShow == 0)  ? false : true)
				);
    		}
    	}
    	return array(
    		'validation' => $validation,
    		'limits' => $limits
    	);
    }
    
    public function prepareAdminView($params) {
    	extract($params);
    	$carbonFootprint = new CarbonFootprint($db);
    	$pagination = new Pagination($carbonFootprint->queryTotalEmissionCount());
		$pagination->url = '?action=browseCategory&category=tables&bookmark=emissionFactor';
    	
    	$emissionFactors = $carbonFootprint->getAllDirectEmissionFactors($pagination);						
			//	and electricity)
		array_push($emissionFactors, new EmissionFactor($db, EmissionFactor::ELECTRICITY_FACTOR_ID));
		
		$result['pagination'] = $pagination;
		$result['emissionFactors'] = $emissionFactors;
		$result['tpl'] = 'carbon_footprint/design/emissionFactor.tpl';
		return $result;
    }
    
    public function prepareAdminEdit($params) {
    	extract($params);
    	$emissionFactor = new EmissionFactor($db, $id); //in caae it add action id=null so all ok!
    	if ($_POST['save'] == 'Save')
    	{													
	    	$emissionFactor->name = $_POST['name'];
	    	$emissionFactor->unittype_id = $_POST['unittypeID'];
	    	$emissionFactor->emission_factor = $_POST['emissionFactor'];
	    	
	    	$validation = new Validation($db);
	    	$validStatus = $validation->validateRegDataAdminClasses($_POST);
	    	
	    	if ($validStatus['summary'] != 'false') {
		    	//	save
		    	$emissionFactor->save();
		    	header ('Location: admin.php?action=browseCategory&category=tables&bookmark=emissionFactor');
		    	die();	
	    	} 
    	}
    	
    	$unittype = new Unittype($db);								
    	$weightUnits = $unittype->getUnittypeListByCategory('weight');
    	$volumeUnits = $unittype->getUnittypeListByCategory('volume');
    	$energyUnits = $unittype->getUnittypeListByCategory('energy');
    	
    	$result['volumeUnittypes'] = $volumeUnits;
    	$result['weightUnittypes'] = $weightUnits;
    	$result['energyUnittypes'] = $energyUnits;									
    	
    	$result['emissionFactor'] = $emissionFactor;
    	$result["validStatus"] = $validStatus;
    	$result['tpl'] = 'carbon_footprint/design/addEmissionFactorClass.tpl';
    	return $result;
    }
}
?>