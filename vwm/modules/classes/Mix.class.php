<?php

class Mix extends MixProperties {
	
	private $mixRecords;
	
	private $currentUsage;
	private $waste_percent;
	
	private $wasteData;
	
	private $trashRecord;	//	tracking system trash obj
	private $parentTrashRecord;
	
	
	function Mix($mixRecords, $mixProperties = false) {
		if (!$mixProperties) {
			$db = $mixRecords;
			$this->db = $db;
		} else {			
			$this->mixRecords = $mixRecords;
			$this->setEquipment($mixProperties->getEquipment());
			$this->department = $mixProperties->getDepartment();
								
			$this->wasteData = $mixProperties->getWaste();			
			
			//================================
			//$this->calculateCurrentUsage();
			//================================
		}
	}
	
	
	
	
	public function getMixListOld($departmentID, $from = 0, $sortBy = "mixID") {
		
		$departmentID=mysql_escape_string($departmentID);
				
		//	convert $sortBy to DB fields
		switch ($sortBy) {							
			case "description":
				$orderBy = "description";
				break;
			case "mixID":				
			default:
				$orderBy = "mix_id";
		}

		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT mix_id, description FROM ".TB_USAGE." WHERE department_id = ".$departmentID." ORDER BY ".$orderBy." LIMIT ".$from.", ".ROW_COUNT;				
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);				
				$usage = array (
					'mix_id'		=>	$data->mix_id,
					'description'	=>	$data->description
				);				
				$usageList[] = $usage;
			}			
			
		} //else 
			//return false;
			return $usageList;						
	}
	
	
	public function getMixList($departmentID, Pagination $pagination = null,$filter=' TRUE ',$sort=' ORDER BY mix_id DESC ') {
		
		$departmentID=mysql_escape_string($departmentID);
		
		$query = "SELECT mix_id, description, voc, creation_time FROM ".TB_USAGE." WHERE department_id = ".$departmentID." AND $filter $sort ";
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}		
		 						
		$this->db->query($query);		
		if ($this->db->num_rows() > 0) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);				
				$usage = array (
					'mix_id'		=>	$data->mix_id,
					'description'	=>	$data->description,
					'voc'			=>	$data->voc,
					'creation_time'	=>	$data->creation_time
				);				
				$usageList[] = $usage;
			}			
			
		} //else 
			//return false;			
			return $usageList;									
	}
	
	
	
	
	public function addNewMix($usageData, $isMWS = false) {
		
		
		//screening of quotation marks
		foreach ($usageData as $key=>$value)
		{
			switch ($key)
			{
				case 'waste': break; 
				case 'products': break;
				default: 
				{
					$usageData[$key]=mysql_escape_string($value);
					break;
				}
			}
		}
		
		//if company use module ReductionScheme we should check and correct solvent outputs
		$this->department = new Department($this->db);
		$this->department->initializeByID($usageData['department_id']);
		$user = new User($this->db);
		$facility = $this->getFacility();
		$facilityDetails = $facility->getFacilityDetails($this->department->getFacilityID());

		if($user->checkAccess('reduction', $facilityDetails['company_id'])) {
			if(!empty($usageData['creationTime'])) {
				//date in format mm-dd-yyyy
				$mm = substr($usageData['creationTime'],0,2);
				$yyyy = substr($usageData['creationTime'],-4);
				if ($yyyy < substr(date('Y-m-d'),0,4) || $mm < substr(date('m-d-Y'),0,2) ) {
					$ms = new ModuleSystem($this->db);
					$moduleMap = $ms->getModulesMap();
					$mRedaction = new $moduleMap['reduction'];
					$params = array(
						'db' => $this->db,
						'facilityID' => $this->department->getFacilityID(),
						'month' => $mm,
						'year' => $yyyy,
						'oldVOC' => 0,
						'newVOC' => $usageData['voc']
					);
					$mRedaction->prepareMixSave($params);
				}
			}
		}

		//$this->db->select_db(DB_NAME);
		//		Create Usage
		$mixNR = $this->getMaxMixNR()+1;

		$query = "INSERT INTO ".TB_USAGE." (equipment_id, department_id, description, voc, voclx, vocwx, creation_time, rule_id,apmethod_id, exempt_rule, waste_percent ) VALUES (".			
			$query .= (empty($usageData['equipment_id'])) ? " '0', " : "'".$usageData['equipment_id']."', ";
			$query .= (empty($usageData['department_id'])) ? " '0', " : "'".$usageData['department_id']."', ";
			$query .= "'".$usageData['description']."', ";		
			$query .= (empty($usageData['voc'])) ? " '0.00', " : "'".$usageData['voc']."', ";
			$query .= (empty($usageData['voclx'])) ? " '0.00', " : "'".$usageData['voclx']."', ";
			$query .= (empty($usageData['vocwx'])) ? " '0.00', " : "'".$usageData['vocwx']."', ";  						
			$query .= (empty($usageData['creationTime'])) ? "'".date('Y-m-d')."', " : "STR_TO_DATE('".$usageData['creationTime']."', '%m-%d-%Y'), ";
			$query .= (empty($usageData["rule"])) ? " '0', " : "'".$usageData['rule']."', ";
			$query .= (empty($usageData['apmethod_id'])) ? "NULL, " : "'".$usageData['apmethod_id']."', ";
			$query .= (empty($usageData['exemptRule'])) ? "NULL, " : "'".$usageData['exemptRule']."', ";	
			$query .= (empty($usageData['waste_percent'])) ? "NULL, " : "'".$usageData['waste_percent']."'";		
		$query .= ")";

		$this->db->query($query);
		
		//		Add products to Usage
		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE description='".$usageData['description']."' AND department_id = '".$usageData['department_id']."'";
		$this->db->query($query);
		$mixID = $this->db->fetch(0)->mix_id;

		$products = $usageData['products'];
		for ($i = 0; $i < count($products); $i++) {
			$unitTypeConverter = new UnitTypeConverter('lb');
			$unittype = new Unittype($this->db);
			$type = $unittype->isWeightOrVolume($products[$i]['unittype']);
			if ($type == 'weight') {
				$value = $unitTypeConverter->convertToDefault($products[$i]['quantity'],$unittype->getDescriptionByID($products[$i]['unittype']));
			} else {
				$product = new Product($this->db);
				$product->initializeByID($products[$i]['product_id']);
				$density = $product->getDensity();
				$densityUnitID = $product->getDensityUnitID();
				$densityObj = new Density($this->db,$densityUnitID);
			    $densityType = array (
				    'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
					'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
			    );
			    $value = $unitTypeConverter->convertToDefault($products[$i]['quantity'],$unittype->getDescriptionByID($products[$i]['unittype']),$density,$densityType);
			    if (empty($density) || $density == '0.00') {
			    	$value = 'NULL';
			    } else {
			    	$value = "'".$value."'";
			    }
			}
			$query = "INSERT INTO ".TB_MIXGROUP." (mix_id, product_id, quantity, unit_type, quantity_lbs) VALUES (";			
				$query .= "'".$mixID."', ";
				$query .= "'".$products[$i]['product_id']."', ";
				$query .= "'".$products[$i]['quantity']."', ";
				$query .= "'".$products[$i]['unittype']."', ";	
				$query .= $value;		
			$query .= " )";
			
			$this->db->query($query);
		}
		
		//	save waste data
		if (!$isMWS) {
			$wasteData = $usageData['waste'];
			$this->saveWaste($mixID, $wasteData['value'], $wasteData['unittypeID']);
		}
		
		//	add usage stats
		$creationMonth = substr($usageData['creationTime'],0,2);
		$creationYear = substr($usageData['creationTime'],-4);		
		$department = new Department($this->db);		
		$department->incrementUsage($creationMonth, $creationYear, $usageData['voc'], $usageData['department_id']);
		
		//	DEPRECATED
		//	Calculate and save mix limits		
		//$this->_recalcLimits($usageData['creationTime'], $usageData['department_id']);
				
		//	save to trash_bin
		$this->save2trash('C', $mixID);	
		
		return $mixID;						
	}
	
	public function getMixDepartment($usageID) {
		$query = "SELECT department_id FROM ".TB_USAGE." WHERE mix_id=".$usageID." LIMIT 1";
		$this->db->query($query);
		return $this->db->fetch(0)->department_id;
	}
	
	
	public function getMixDetails($usageID, $vanilla = false, $isMWS = false) {
		
		$usageID=mysql_escape_string($usageID);		
					
		//$this->db->select_db(DB_NAME);
		
		
		$query = "SELECT *,rule_id as 'rule', creation_time as 'creationTime' FROM ".TB_USAGE." WHERE mix_id=".$usageID;
		$this->db->query($query);
				
		$usageDetails = $this->db->fetch_array(0);
		$usageDetails['creationTime'] = date('m-d-Y', strtotime($usageDetails['creationTime']));
		
		/*$usageDetails = array (
			'mix_id'			=>	$data->mix_id,
			'equipment_id'		=>	$data->equipment_id,
			'department_id'		=>	$data->department_id,
			'apmethod_id'		=>  $data->apmethod_id,
			'description'		=>	$data->description,
			'voc'				=>	$data->voc,
			'voclx'				=>	$data->voclx,
			'vocwx'				=>	$data->vocwx,
			'rule'				=>	$data->rule_id,
			'exemptRule'		=>	$data->exempt_rule,
			'creationTime'		=>	date('m-d-Y', strtotime($data->creation_time)),
			'waste_percent'		=>	$data->waste_percent
		);*/		
		
		$query = "SELECT expire FROM ".TB_EQUIPMENT." WHERE equipment_id=".$usageDetails['equipment_id'];
		
			$this->db->query($query);
			$exp =  $this->db->fetch(0)->expire;
			$DateType = new DateTypeConverter($this->db);
			$usageDetails['expire'] = date($DateType->getDatetypebyID($usageDetails['equipment_id']), $exp);
			 
		if (!$vanilla) {
			$query="SELECT equip_desc FROM ".TB_EQUIPMENT." WHERE equipment_id=".$usageDetails['equipment_id'];
			$this->db->query($query);
			$usageDetails['equipment_id']=$this->db->fetch(0)->equip_desc;		
		}
		
		//	get products
		$usageDetails['products'] = $this->getMixProducts($usageDetails['mix_id']);
				
		//	get waste
		if (!$isMWS) {						
			$wasteDetails = $this->getWasteDetails($usageID);
			$usageDetails['waste'] = $wasteDetails;
		}		
		return $usageDetails; 
	}
	
	
	
	
	public function setMixDetails($usageData, $isMWS = false) {
		
		//screening of quotation marks
		foreach ($usageData as $key=>$value)
		{
			switch ($key)
			{
				case 'waste': break; 
				case 'products': break;
				default: 
				{
					$usageData[$key]=mysql_escape_string($value);
					break;
				}
			}
		}
		
		$query = 'SELECT voc, department_id, creation_time FROM '.TB_USAGE.' WHERE mix_id = '. $usageData['mix_id'];
		
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$record = $this->db->fetch(0);
			$oldVoc = $record->voc;
			$departmentID = $record->department_id;
			$oldTime = $record->creation_time;
		}
		
		//	save to trash_bin
		$this->save2trash('U', $usageData['mix_id']);
		//$this->parentTrashRecord = $this->trashRecord;		
		
		//if company use module ReductionScheme we should check and correct solvent outputs
		$this->department = new Department($this->db);
		$this->department->initializeByID($usageData['department_id']);
		$user = new User($this->db);
		$facility = $this->getFacility();
		$facilityDetails = $facility->getFacilityDetails($this->department->getFacilityID());

		if($user->checkAccess('reduction', $facilityDetails['company_id'])) {
			if(!empty($usageData['creationTime'])) {
				//date in format mm-dd-yyyy
				$mm = substr($usageData['creationTime'],0,2);
				$yyyy = substr($usageData['creationTime'],-4);
				$oldDate = getdate(strtotime($oldTime));
				if ($yyyy < substr(date('Y-m-d'),0,4) || ($mm < substr(date('m-d-Y'),0,2) && $yyyy == substr(date('Y-m-d'),0,4)) ||
					$oldDate['year'] < substr(date('Y-m-d'),0,4) || ($oldDate['mon'] < substr(date('m-d-Y'),0,2) && $oldDate['year'] == substr(date('Y-m-d'),0,4)) ) {
					$ms = new ModuleSystem($this->db);
					$moduleMap = $ms->getModulesMap();
					$mRedaction = new $moduleMap['reduction'];
					$params = array(
						'db' => $this->db,
						'facilityID' => $this->department->getFacilityID(),
						'month' => $mm,
						'year' => $yyyy,
						'monthOld' => $oldDate['mon'],
						'yearOld' => $oldDate['year'],
						'oldVOC' => $oldVoc,
						'newVOC' => $usageData['voc']
					);
					$mRedaction->prepareMixSave($params);
				}
			}
		}

		
		//	save waste data first
		if (!$isMWS) {
			$wasteData = $usageData['waste'];
			$this->saveWaste($wasteData['mixID'], $wasteData['value'], $wasteData['unittypeID']);																		
		}
		
		//		Update product's details
		$query = "UPDATE ".TB_USAGE." SET ";		
			$query .= "equipment_id='".$usageData['equipment_id']."', ";
			$query .= "apmethod_id=".((empty($usageData['apmethod_id']))?"NULL":"'".$usageData['apmethod_id']."'").",";
			$query .= "voc='".$usageData['voc']."', ";
			$query .= "voclx='".$usageData['voclx']."', ";
			$query .= "vocwx='".$usageData['vocwx']."', ";
			$query .= "waste_percent=".((empty($usageData['waste_percent']))?"NULL":"'".$usageData['waste_percent']."'").", ";
			$query .= "description='".$usageData['description']."', ";
			$query .= "rule_id=".$usageData['rule'].", ";
			$query .= (empty($usageData['exemptRule'])) ? "exempt_rule = NULL, " : "exempt_rule ='".$usageData['exemptRule']."', ";			
			if (empty($usageData["creationTime"])) {
				$query .= "creation_time = '".date('Y-m-d')."' ";
			} else {
				$query .= "creation_time = STR_TO_DATE('".$usageData['creationTime']."', '%m-%d-%Y') ";
			}		
		$query .= " WHERE mix_id =".$usageData['mix_id'];
		
		$this->db->query($query);
		
		//	Delete previous products		
		$query = "DELETE FROM ".TB_MIXGROUP." WHERE mix_id = ".$usageData['mix_id'];
		$this->db->query($query);
	
		//	Set new data
		if (count($usageData['products']) > 0) {
			$products = $usageData['products'];			
			for ($i=0; $i<count($products); $i++) {
				$unitTypeConverter = new UnitTypeConverter('lb');
				$unittype = new Unittype($this->db);
				$type = $unittype->isWeightOrVolume($products[$i]['unittype']);
				if ($type == 'weight') {
					$value = $unitTypeConverter->convertToDefault($products[$i]['quantity'],$unittype->getDescriptionByID($products[$i]['unittype']));
				} else {
					$product = new Product($this->db);
					$product->initializeByID($products[$i]['product_id']);
					$density = $product->getDensity();
					$densityUnitID = $product->getDensityUnitID();
					$densityObj = new Density($this->db,$densityUnitID);
					$densityType = array (
						'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
						'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
					);
					$value = $unitTypeConverter->convertToDefault($products[$i]['quantity'],$unittype->getDescriptionByID($products[$i]['unittype']),$density,$densityType);
					if (empty($density) || $density == '0.00') {
						$value = 'NULL';
					} else {
						$value = "'".$value."'";
					}
				}
				$query = "INSERT INTO ".TB_MIXGROUP." (mix_id, product_id, quantity, unit_type, quantity_lbs) VALUES (";
				
				$query.="'".$usageData['mix_id']."', ";
				$query.="'".$products[$i]['product_id']."', ";
				$query.="'".$products[$i]['quantity']."', ";
				$query.="'".$products[$i]['unittype']."', ";
				$query .= $value;
				$query .= ")";
				
				$this->db->query($query);
			}
		}
		
		
		//	add usage stats
		if (isset($oldVoc) && (float)$oldVoc !== (float)$usageData['voc']) {			
			$creationMonthOld = substr($oldTime,5,2);
			$creationYearOld = substr($oldTime,0,4);
			$creationMonth = substr($usageData['creationTime'],0,2);
			$creationYear = substr($usageData['creationTime'],-4);		
			$department = new Department($this->db);
				
			//if ($oldVoc > $usageData['voc']) {				
				$department->decrementUsage($creationMonthOld, $creationYearOld, $oldVoc, $departmentID);	//	- erase old mix value.		
			//} else {				
				$department->incrementUsage($creationMonth, $creationYear, $usageData['voc'], $departmentID);
			//}		
		}
		
		
		//	DEPRECATED
		//	recalculate and save mix limits
		//$this->_recalcLimits($usageData['creationTime'], $usageData['department_id']);
	}
	
	
	
	
	public function deleteUsage($id, $byField = "mix_id") {
				
		$id=mysql_escape_string($id);		
		
		switch ($byField) {
			case "equipment_id":
				$query = "SELECT * FROM ".TB_USAGE." WHERE equipment_id = ".$id;
				$this->db->query($query);
				if ($this->db->num_rows() > 0) {
					$mixesData = $this->db->fetch_all();
					$department = new Department($this->db);
					foreach ($mixesData as $mixData) {
						//	save to trash		
						$this->save2trash('D', $mixData->mix_id);

						//	add usage stats
						$usageData = $this->getMixDetails($mixData->mix_id);					
						$mixCreationMonth = substr($usageData['creationTime'],0,2);
						$mixCreationYear = substr($usageData['creationTime'],-4);						
						$department->decrementUsage($mixCreationMonth, $mixCreationYear, $usageData['voc'],  $usageData['department_id']);
						
						//if company use module ReductionScheme we should check and correct solvent outputs
						$this->department = new Department($this->db);
						$department->initializeByID($mixData->department_id);
						$user = new User($this->db);
						$facility = $department->getFacility();
						$facilityDetails = $facility->getFacilityDetails($department->getFacilityID());
						
						if($user->checkAccess('reduction', $facilityDetails['company_id'])) {
								if ($mixCreationYear < substr(date('Y-m-d'),0,4) || $mixCreationMonth < substr(date('m-d-Y'),0,2) ) {
									$ms = new ModuleSystem($this->db);
									$moduleMap = $ms->getModulesMap();
									$mRedaction = new $moduleMap['reduction'];
									$params = array(
										'db' => $this->db,
										'facilityID' => $department->getFacilityID(),
										'month' => $mixCreationMonth,
										'year' => $mixCreationYear,
										'newVOC' => -$mixData->voc
									);
									$mRedaction->prepareMixSave($params);
								}	
						}
					}
					
					//	Delete usage's details									
					$query = "DELETE FROM ".TB_USAGE." WHERE equipment_id = ".$id;
					$this->db->query($query);										
					
					//	Calculate and save mix limits
//					$query = "SELECT d.facility_id FROM ".TB_EQUIPMENT." e, ".TB_DEPARTMENT." d " .
//							"WHERE e.department_id = d.department_id AND " .
//							"e.equipment_id = ".$id;
//					$this->db->query($query);					
//					$query = "SELECT mix_id FROM ".TB_USAGE." m, ".TB_DEPARTMENT." d " .
//							"WHERE m.department_id = d.department_id AND " .
//							"d.facility_id = ".$this->db->fetch(0)->facility_id;	//	надоело. Пересчитываем весь facility			
//									
//					$this->db->query($query);
//					if ($this->db->num_rows() > 0) {
//						$mixesData = $this->db->fetch_all();		
//						$mix = new Mix($this->db);		
//						foreach($mixesData as $mixData) {							
//							$mix->calculateAndSaveMixLimits($mixData->mix_id);
//						}
//					}		
				}
				break;
			case "mix_id":
			default:
				$usageData = $this->getMixDetails($id);	
			
				//if company use module ReductionScheme we should check and correct solvent outputs
				$this->department = new Department($this->db);
				$this->department->initializeByID($usageData['department_id']);
				$user = new User($this->db);
				$facility = $this->getFacility();
				$facilityDetails = $facility->getFacilityDetails($this->department->getFacilityID());
				
				if($user->checkAccess('reduction', $facilityDetails['company_id'])) {
					if(!empty($usageData['creationTime'])) {
						//date in format mm-dd-yyyy
						$mm = substr($usageData['creationTime'],0,2);
						$yyyy = substr($usageData['creationTime'],-4);
						if ($yyyy < substr(date('Y-m-d'),0,4) || $mm < substr(date('m-d-Y'),0,2) ) {
							$ms = new ModuleSystem($this->db);
							$moduleMap = $ms->getModulesMap();
							$mRedaction = new $moduleMap['reduction'];
							$params = array(
								'db' => $this->db,
								'facilityID' => $this->department->getFacilityID(),
								'month' => $mm,
								'year' => $yyyy,
								'newVOC' => -$usageData['voc']
							);
							$mRedaction->prepareMixSave($params);
						}
					}
				}
				
				//	Delete usage's details
				$this->save2trash('D', $id);									
				$query = "DELETE FROM ".TB_USAGE." WHERE mix_id = ".$id;
				$this->db->query($query);					
			
				//	add usage stats
				$creationMonth = substr($usageData['creationTime'],0,2);
				$creationYear = substr($usageData['creationTime'],-4);
				$department = new Department($this->db);
			
				$department->decrementUsage($creationMonth, $creationYear, $usageData['voc'], $usageData['department_id']);
				//	Calculate and save mix limits		
				//$this->_recalcLimits($usageData['creationTime'], $usageData['department_id']);		
			break;
		}		
	}																										
	
	
	
	
	//******************************
	//*			FUNCTIONS
	//******************************
	
	function getMixNRByUsage($usageID) {
		
		$usageID=mysql_escape_string($usageID);		
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT mix_nr FROM ".TB_USAGE." WHERE usage_id=".$usageID;
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			return $this->db->fetch(0)->mix_nr;
		}
	}
	
	
	
	
	function getMaxMixNR() {
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT mix_nr FROM ".TB_USAGE." ORDER BY mix_nr";
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			return $this->db->fetch($this->db->num_rows() - 1)->mix_nr;
		}
		
		return 0;
	}
	
	
	
	
	public function testMix() {
		$this->mixRecords[0]->testMixRecord();
	}
	
	
	
	
	public function getCurrentUsage() {
		$this->calculateCurrentUsage();
		
		return $this->currentUsage;
	}
	
	
	public function calculateCurrentUsage() {	
		
		$errors = array(
			'isDensityToVolumeError'=>false,
			'isDensityToWeightError'=>false,
			'isWasteCalculatedError'=>false,
			'isWastePercentAbove100'=>false			
		);
		
		$isThereProductWithoutDensity = false;
		$company = new Company($this->db);
		$unittype = new Unittype($this->db);
		$wasteUnitDetails = $unittype->getUnittypeDetails($this->wasteData['unittypeID']);
		
		$companyID = $company->getCompanyIDbyDepartmentID($this->getDepartment()->getDepartmentID());
		$companyDetails = $company->getCompanyDetails($companyID);
		//	default unit type = company's voc unit
		$defaultType=$unittype->getDescriptionByID($companyDetails['voc_unittype_id']);
			
		$unitTypeConverter = new UnitTypeConverter($defaultType);
		
		foreach ($this->mixRecords as $key=>$mixRecord) {
							
			$voclx = $mixRecord->getProduct()->getVoclx();			
			$vocwx = $mixRecord->getProduct()->getVocwx();
			$percentVolatileWeight = $mixRecord->getProduct()->getPercentVolatileWeigh();			
			$percentVolatileVolume = $mixRecord->getProduct()->getPercentVolatileVolume();
			$errors['isVocwxOrPercentWarning'][$key]='false';
			
			$density = $mixRecord->getProduct()->getDensity();
			$densityObj = new Density($this->db, $mixRecord->getProduct()->getDensityUnitID());
			
			//	check density
			if (empty($density) || $density == '0.00') {				
				$density = false;
				$isThereProductWithoutDensity = true;				
			}
			
			//$voclxArray[] = $voclx;
			//$vocwxArray[] = $vocwx;
			//$percentVolatileWeightArray[] = $percentVolatileWeight;
			//$percentVolatileVolumeArray[] = $percentVolatileVolume;
			
			$quantity = $mixRecord->getQuantity();			
			$unitTypeId = $mixRecord->getUnitType();
											
			$unitTypeDetails = $unittype->getUnittypeDetails($unitTypeId);
			
			
												
			// get Density Type	    	
    		$densityType = array (
    			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
    			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
    		);																										
			
			//quantity array in lbs															
			//$quantityArrayWeight[] = $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "lb", $density, $densityType);//	in weight
			$quantitiWeightSum+=$unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "lb", $density, $densityType);//	in weight
			//quantity array in gallon
			$quantitiVolumeSum+=$unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon", $density, $densityType);//	in volume ;	
			//$quantityArrayVolume[] =$unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon", $density, $densityType);//	in volume ;			
				
			
			//check, is vocwx or percentVolatileWeight
			$isVocwx=true;
			$isPercentVolatileWeight=true;
			if (empty($vocwx) || $vocwx == '0.00')			
				$isVocwx = false;				
			if (empty($percentVolatileWeight) || $percentVolatileWeight == '0.00')			
				$isPercentVolatileWeight = false;					
			if ($isPercentVolatileWeight||$isVocwx)
			{
				$errors['isVocwxOrPercentWarning'][$key]=false;
				switch ($unittype->isWeightOrVolume($unitTypeId))
				{
					case 'weight':
						if ($isPercentVolatileWeight)
						{							
							$percentAndWeight= array(
								'weight'=>$unitTypeConverter->convertToDefault($quantity, $unitTypeDetails["description"]),
								'percent'=> $percentVolatileWeight
							);
							$ArrayWeight[] =$percentAndWeight; 
						}
						else 
						{
							if ($density)
							{
								$galQty = $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon", $density, $densityType);//	in volume
								$vocwxAndVolume=array(
									'volume'=>$galQty,
									'vocwx'=>$vocwx
								);
								$ArrayVolume[] =$vocwxAndVolume;
							}
							else 
							{
								$errors['isDensityToVolumeError']=true;
							}
						}						
						break;
					case 'volume':
						if ($isVocwx)
						{
							$galQty = $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon");//	in volume
							$vocwxAndVolume=array(
								'volume'=>$galQty,
								'vocwx'=>$vocwx
							);
							$ArrayVolume[] =$vocwxAndVolume;
						}
						else 
						{
							if ($density)
							{
								$percentAndWeight= array(
									'weight'=>$unitTypeConverter->convertToDefault($quantity, $unitTypeDetails["description"], $density, $densityType),
									'percent'=> $percentVolatileWeight
								);
								$ArrayWeight[] =$percentAndWeight;
							}
							else
							{
								$errors['isDensityToWeightError']=true;
							}
						}
						break;
				}
								
			}
			else  			
				$errors['isVocwxOrPercentWarning'][$key]='true';								
		}				
		//$quantitiWeightSum=array_sum($quantityArrayWeight);		
		//$quantitiVolumeSum=array_sum($quantityArrayVolume);		
		$wasteResult=$this->calculateWastePercent($this->wasteData['unittypeID'], $this->wasteData['value'],$unitTypeConverter,$quantitiWeightSum,$quantitiVolumeSum);
		//$errors['isWasteCalculatedError']=$wasteResult[];
		$calculator = new Calculator();
		
		$this->voc=$calculator->calculateVocNew ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult);
		//$this->voc = $calculator->calculateVoc($percentVolatileWeightArray, $quantityArray, $wasteCalculated);
		//	I don't know what about Europe			
		//$this->voclx = $calculator->calculateVoclx($voclxArray, $quantityArray, $wasteCalculated);
		//$this->vocwx = $calculator->calculateVocwx($vocwxArray, $quantityArray, $wasteCalculated);
		
		$this->waste_percent = $wasteResult['wastePercent'];
		$this->currentUsage = $this->voc;
		$this->wastePercent = $wasteResult['wastePercent'];
		$errors['isWastePercentAbove100']=$wasteResult['isWastePercentAbove100'];
		return $errors;
		
	}
	
	/*public function calculateCurrentUsage() {
		
		$isThereProductWithoutDensity = false;
				
		$unittype = new Unittype($this->db);
		$wasteUnitDetails = $unittype->getUnittypeDetails($this->wasteData['unittypeID']);
		
		foreach ($this->mixRecords as $mixRecord) {
							
			$voclx = $mixRecord->getProduct()->getVoclx();			
			$vocwx = $mixRecord->getProduct()->getVocwx();
			$density = $mixRecord->getProduct()->getDensity();
			$densityObj = new Density($this->db, $mixRecord->getProduct()->getDensityUnitID());
			
			//	check density
			if (empty($density) || $density == '0.00') {
				$density = false;
				$isThereProductWithoutDensity = true;
			}
			
			$voclxArray[] = $voclx;
			$vocwxArray[] = $vocwx;
			
			$quantity = $mixRecord->getQuantity();			
			$unitTypeId = $mixRecord->getUnitType();
			
			//$unittype = new Unittype($this->db);
			$unitTypeDetails = $unittype->getUnittypeDetails($unitTypeId);
			
			$unitTypeConverter = new UnitTypeConverter();								
				
			// get Density Type
	    	$densityType = array (
    			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
    			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
    		);		
									
			$quantityArray[] = $unitTypeConverter->convertToDefault($quantity, $unitTypeDetails["description"], $density, $densityType);	//	in volume
			if ($wasteUnitDetails['type'] == 'Weight') {	//	TODO: review
				$lbsQty = $unitTypeConverter->toDefault($quantity, $unitTypeDetails["description"]);//	in weight
				$quantityArrayWeight[] = $lbsQty['value'];
			}												
		}
		
		if ($wasteUnitDetails['type'] == 'Weight') {
			if (!$isThereProductWithoutDensity) {
				$mixDensity = array_sum($quantityArrayWeight)/array_sum($quantityArray);
				$wasteCalculated = $this->calculateWaste($this->wasteData['unittypeID'], $this->wasteData['value'], array_sum($quantityArray), $mixDensity);
			} else {
				$wasteCalculated = 0;
			}
		} else {
			$wasteCalculated = $this->calculateWaste($this->wasteData['unittypeID'], $this->wasteData['value'], array_sum($quantityArray));
		}			
		//$wasteCalculated = $this->calculateWaste($this->wasteData['unittypeID'], $this->wasteData['value'], array_sum($quantityArray));
			
		$calculator = new Calculator();

		$this->voc = $calculator->calculateVoc($vocwxArray, $quantityArray, $wasteCalculated);			
		$this->voclx = $calculator->calculateVoclx($voclxArray, $quantityArray, $wasteCalculated);
		$this->vocwx = $calculator->calculateVocwx($vocwxArray, $quantityArray, $wasteCalculated);
		
		$this->currentUsage = $this->voc;
	}
	
	*/
	
	
	public function initializeByID($mixID) {
		
		$mixID=mysql_escape_string($mixID);				
		
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_USAGE." WHERE mix_id=".$mixID;
		$this->db->query($query);		
		
		if ($this->db->num_rows() > 0) {
			$mixData = $this->db->fetch(0);
			
			//		Initialize MIX' properties
			//		Set Equipment
			$equipmentID = $mixData->equipment_id;
			
			$equipment = new Equipment($this->db);
			if ($equipment->initializeByID($equipmentID)) {
				$this->equipment = $equipment;				
			} else {				
				return false;
			}
			
			//		Set Department
			$department = new Department($this->db);
			
			if ($department->initializeByID($mixData->department_id)) {
				$this->department = $department;
			} else {
				return false;
			}
			
			$this->mixID = $mixID;
			
			$this->date = new Date($mixData->timestamp);
			$this->voc = $mixData->voc;
			$this->voclx = $mixData->voclx;
			$this->vocwx = $mixData->vocwx;
			$this->creationTime = date('m-d-Y', strtotime($mixData->creation_time));
			
			$this->alreadyExist = true;
			
			//		Initialize MIX' records
			$query = "SELECT mixgroup_id FROM ".TB_MIXGROUP." WHERE mix_id=".$mixID;
			$this->db->query($query);
						
			if ($this->db->num_rows() > 0) {
				$builder = new Builder($this->db);
								
				$mixRecordsData = $this->db->fetch_all();

				foreach($mixRecordsData as $mixRecordData) {
					$mixRecord = $builder->buildMixRecord($mixRecordData->mixgroup_id);
					
					if ($mixRecord) {
						$mixRecords[] = $mixRecord;	
					} else {
						return false;
					}				
				}
				
				$this->mixRecords = $mixRecords;
			}
			
			//	DEPRECATED Denis, July 20, 2010
//			//	Initialize mix limits
//			$query = "SELECT m2ml.mix_id, ml.name, m2ml.yesNo FROM mix2mix_limit m2ml, mix_limit ml " .
//					"WHERE ml.mix_limit_id = m2ml.mix_limit_id " .
//					"AND m2ml.mix_id = ".$mixID;
//			$this->db->query($query);
//			if ($this->db->num_rows() > 0) {				
//				$mixLimitsData = $this->db->fetch_all();
//				foreach($mixLimitsData as $mixLimitData) {					
//					//	check if such limit exists at class properties
//				 	if (property_exists('Mix', $mixLimitData->name)) {
//				 		$limitName = $mixLimitData->name;							 	
//				 		$this->$limitName = ($mixLimitData->yesNo == 'yes') ? true : false;				 						 				 		
//				 	}											
//				}				
//			} else {
//				return false;
//			}
			
			
		} else {
			return false;
		}
		
		return true;
	}
	
	
	
	
	public function getFacility() {
		return $this->department->getFacility();					
	}
	
	
	
	
	public function getWasteDetails($mixID = 0) {						
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter();
		
		//	escape input data		
		$mixID = $this->db->sqltext($mixID);
		
		$wasteFromDB = $this->selectWaste($mixID);
		
		if (!$wasteFromDB) {			
			//	default values
			$wasteDetails = array (
				'mixID'			=> $mixID,
				'value'			=> 0.00,
				'unittypeClass'	=> 'percent'								
			); 			
		} else {
			$wasteDetails = array (
				'mixID'			=> $wasteFromDB['mixID'],
				'value'			=> $wasteFromDB['value']
			);
						
			$wasteDetails['unittypeClass'] = ($wasteFromDB['method'] == 'percent') ? $wasteFromDB['method'] : $unittype->getUnittypeClass($wasteFromDB['unittypeID']);
			$wasteDetails['unittypeID'] = (is_null($wasteFromDB['unittypeID'])) ? '' : $wasteFromDB['unittypeID'];
			$wasteDetails['unitTypeList'] = (is_null($wasteFromDB['unittypeID'])) ? false : $unittype->getUnittypeListDefault($wasteDetails['unittypeClass']);
		}		
		
		//	get mix products				
		$products = $this->getMixProducts($mixID);
																
		//	sum total quantity
		$quantitySum = 0;
		foreach ($products as $product) {		
			$densityObj = new Density($this->db, $product['densityUnitID']);							
			$densityType = array (
				'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
				'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
			);						
			$unitTypeDetails = $unittype->getUnittypeDetails($product['unittype']);
			$quantitySum += $unitTypeConverter->convertToDefault($product['quantity'], $unitTypeDetails['description'], $product['density'], $densityType);	
		}
				
		$wasteDetails['calculated'] = $this->calculateWaste($wasteDetails['unittypeID'], $wasteDetails['value'], $quantitySum);	//	waste in weight unit type same as VOC		
			
		return $wasteDetails;
	}
	
	
	
	
	public function countMixes($departmentID,$filter=' TRUE ') {
		
		$departmentID=mysql_escape_string($departmentID);		
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT count(mix_id) mixCount FROM ".TB_USAGE." WHERE department_id = $departmentID AND $filter";
		$this->db->query($query);	
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->mixCount;
		} else 
			return false;
	}
	
	
	/**	 
	 * Search mixes
	 * @param mixed $mixes - value of field to search, array or string
	 * @param string $byField - field name 
	 * @param int $departmentID 
	 */	
	public function countSearchedMixes($mixes, $byField, $departmentID) {
		
		$departmentID=mysql_escape_string($departmentID);		
		$query = "SELECT  count(mix_id) mixCount FROM ".TB_USAGE." WHERE department_id = ".$departmentID." AND (";
		
		if (!is_array($mixes)) {
			$mixes = array($mixes);
		}
		
		$sqlParts = array();
		foreach ($mixes as $mix) {
			$sqlParts[] = $byField." LIKE '%".$mix."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.")";		
		
		$this->db->query($query);	
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->mixCount;
		} else 
			return false;
	}
	
	
	
	public function mixAutocomplete($occurrence, $departmentID) {
		
		$departmentID=mysql_escape_string($departmentID);		
		$occurrence=mysql_escape_string($occurrence);
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT mix_id, description, LOCATE('".$occurrence."', description) occurrence FROM ".TB_USAGE.
			" WHERE department_id = ".$departmentID." AND LOCATE('".$occurrence."', description)>0 LIMIT ".AUTOCOMPLETE_LIMIT;
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			$mixes = $this->db->fetch_all();
//			for ($i = 0; $i < count($mixes); $i++) {
//				if ($mixes[$i]->occurrence) {
			foreach ($mixes as $mix) {
				if($mix->occurrence) {
//					$mix = array (
//						"mixID"			=>	$mixes[$i]->mixID,
//						"description"	=>	$mixes[$i]->description,
//						"occurrence"	=>	$mixes[$i]->occurrence
//					);
					$results[] = $mix->description;
				}				
			}
			return (isset($results)) ? $results : false;								
		} else 
			return false;
	}
	
	
	/**	 
	 * Search mixes
	 * @param mixed $mixes - value of field to search, array or string
	 * @param string $byField - field name 
	 * @param int $departmentID 
	 */
	public function searchMixes($mixes, $byField, $departmentID, Pagination $pagination = null) {
		$departmentID=mysql_escape_string($departmentID);		
		$query = "SELECT  mix_id, description, voc, creation_time FROM ".TB_USAGE." WHERE department_id = ".$departmentID." AND (";		
		
		if (!is_array($mixes)) {
			$mixes = array($mixes);
		}
		
		$sqlParts = array();
		foreach ($mixes as $mix) {
			$mix=mysql_escape_string($mix);
			$sqlParts[] = $byField." LIKE '%".$mix."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.")";		
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}		
		
		$this->db->query($query);	
		if ($this->db->num_rows() > 0) {
				
			$searchedMixes = $this->db->fetch_all_array();
		}
		return (isset($searchedMixes)) ? $searchedMixes : null;		
	}
	
	
	
	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection	
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;		
	}
	public function setParentTrashRecord(iTrash $trashRecord) {
		$this->parentTrashRecord = $trashRecord;		
	}	
	
	
	private function selectWaste($mixID) {
		
		$mixID=mysql_escape_string($mixID);		
		//$this->db->select_db(DB_NAME);
		$query = "SELECT id,mix_id as 'mixID',method,unittype_id as 'unittypeID',value FROM waste WHERE mix_id = ".$mixID;		
		$this->db->query($query);
		$numRows = $this->db->num_rows(); 
		if ($numRows) {			
			$wasteDetails = $this->db->fetch_array(0);
			/*$wasteDetails = array(
				'id'			=>	$wasteData->id,
				'mixID'			=>	$wasteData->mix_id,
				'method'		=>	$wasteData->method,
				'unittypeID'	=>	$wasteData->unittype_id,
				'value'			=>	$wasteData->value					
			);*/
			return $wasteDetails; 
		} else 
			return false;								
	}
	
	
	
	
	private function saveWaste($mixID, $value, $unittype) {
		//	form & escape input data
		$wasteData = $this->formAndEscapeWasteData($mixID, $value, $unittype);				
								
		//	if waste already saved at DB - update, else - insert
		if ($this->checkIfWasteExist($wasteData['mixID'])) {
			$this->updateWaste($wasteData);
		} else {
			$this->insertWaste($wasteData);
		}
	}	
	
	
	
	
	private function formAndEscapeWasteData($mixID, $value, $unittype) {
		$wasteData = array(
			'mixID'		=> $this->db->sqltext($mixID),
			'method'	=> (!$unittype) ? 'percent' : 'weight',						//	method is "percent" or "weight" only.  Weight means that waste set in some unittype.
			'unittypeID'=> (!$unittype) ? 'null' : $this->db->sqltext($unittype),	//	if method is "percent" then save unittype as null to DB
			'value'		=> $this->db->sqltext($value)
		);
		return $wasteData; 
	}
	
	
	
	
	private function checkIfWasteExist($mixID) {
		
		//$mixID=mysql_escape_string($mixID);
		settype($mixID,"integer");
		
		//$this->db->select_db(DB_NAME);		
		$query = "SELECT id FROM waste WHERE mix_id = ".$mixID;		
		$this->db->query($query);
		return ($this->db->num_rows()) ? true : false;
	}		
	
	
	
	
	private function insertWaste($wasteData) {
		
		//screening of quotation marks
		foreach ($wasteData as $key=>$value)
		{
			$wasteData[$key]=mysql_escape_string($value);
		}
		
		//$this->db->select_db(DB_NAME);		
		$query = "INSERT INTO waste (mix_id, method, unittype_id, value) VALUES (" .
					"".$wasteData['mixID'].", " .
					"'".$wasteData['method']."', " .
					"".$wasteData['unittypeID'].", " .
					"'".$wasteData['value']."' )";		
		$this->db->query($query);						
	}
	
	
	
	
	private function updateWaste($wasteData) {
		
		//screening of quotation marks
		foreach ($wasteData as $key=>$value)
		{
			$wasteData[$key]=mysql_escape_string($value);
		}
		
		//$this->db->select_db(DB_NAME);		
		$query = "UPDATE waste SET " .
					"method = '".$wasteData['method']."', " .
					"unittype_id = ".$wasteData['unittypeID'].", " .
					"value = '".$wasteData['value']."' " .
				"WHERE mix_id = ".$wasteData['mixID'];		
		$this->db->query($query);		
	}
	
	
	private function calculateWastePercent($unittypeID, $value, $unitTypeConverter, $quantityWeightSum=0, $quantityVolumeSum = 0)
	{		
		$result=array(	'wastePercent'=>0,
						'isWasteError'=>false,
						'isWastePercentAbove100'=>false);
		$unittype = new Unittype($this->db);
		$wasteUnitDetails = $unittype->getUnittypeDetails($this->wasteData['unittypeID']);
		
	/*	$densityType = array (
    			'numerator' => 'lb',
    			'denominator' => 'us gallon'
    		);		*/	
		
		if (empty($unittypeID)) 
		{
			//	percent								
			$result['wastePercent']= $value;				
		} 
		else 
		{					
			switch ($unittype->isWeightOrVolume($this->wasteData['unittypeID']))
			{
				case 'volume':				
					$weistVolume=$unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], 'us gallon');
					$result['wastePercent']=$weistVolume/$quantityVolumeSum*100;
					
					break;
				case 'weight':
					{						 
						$weistWeight=$unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], "lb");
						$result['wastePercent']= $weistWeight/$quantityWeightSum*100;
					}
					break;
				case false:
					$result['isWasteError']=true;
					break;
			}						
		}		
		if ($result['wastePercent']>100)
		{
			$result['wastePercent']=0;
			$result['isWastePercentAbove100']=true;
		}						
		return $result;
	}
	
	private function calculateWaste($unittypeID, $value, $quantitySum = 0, $mixDensity = false) {
					
		$waste = 0;
		
		$unitTypeConverter = new UnitTypeConverter();
		$unittype = new Unittype($this->db);

		if (empty($unittypeID)) {
			//	percent								
			$waste = ($value * $quantitySum) / 100;	
				
		} else {
			//	weight
					
			$unitTypeDetails = $unittype->getUnittypeDetails($unittypeID);			
			$waste = $unitTypeConverter->convertToDefault($value, $unitTypeDetails["description"],$mixDensity);
		}
		
		return round($waste, 2);	
	}	
	
	
	
	
	public function getMixProducts($mixID) {
		
		$mixID=mysql_escape_string($mixID);		
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT * FROM ".TB_MIXGROUP." mg, ".TB_PRODUCT." p WHERE mg.mix_id=".$mixID." AND mg.product_id = p.product_id ";
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			$products = $this->db->fetch_all_array();			
			
			foreach($products as $product) {
				
				//	Getting Supplier Name
				$supplier = new Supplier($this->db);
				$supplierDetails = $supplier->getSupplierDetails($product['supplier_id']);
				$product["supplier"] = $supplierDetails["supplier_desc"];
				
				//	Getting coating
				$coat = new Coat($this->db);
				$coatingDetails = $coat->getCoatDetails($product['coating_id']);
				$product["coatDesc"] = $coatingDetails["coat_desc"];
				
				//	sorry for this
				$product["description"] = $product['name'];
				$product["unittype"] = $product['unit_type'];
								
				$productData[]=$product;
			}			
			return $productData;
		} else 
			return false;		
	}
	
	
	
	
	//	DEPRECATED
	public function calculateAndSaveMixLimits($mixID) {
		
		$mixID=mysql_escape_string($mixID);			
		
		//$this->db->select_db(DB_NAME);
		
		//	if not already saved to trash at $this->setMixDetails();
	//	if (isset($this->parentTrashRecord)) {
	//		$this->save2trash('U', $mixID);	
	//	}		
		
		$recalc = true;	//	calculate mix limits, not take from DB
		$mixValidator = new MixValidator($recalc);																	
		
		$allMixLimits = $this->selectMixLimits();
		
		$this->initializeByID($mixID);							
		$validatorResponse = $mixValidator->isValidMix($this);
		
		$query = "SELECT mix_id FROM mix2mix_limit WHERE mix_id = ".$mixID;				
		$this->db->query($query);

		//	insert
		if ($this->db->num_rows() == 0) {			
			//	insert expired
			$mixLimitID = array_search('expired', $allMixLimits);
			if ($validatorResponse->isExpired()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
						
			//	insert preExpired
			$mixLimitID = array_search('preExpired', $allMixLimits);			
			if ($validatorResponse->isPreExpired()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	insert facilityLimitExcess
			$mixLimitID = array_search('facilityLimitExcess', $allMixLimits);
			if ($validatorResponse->isFacilityLimitExceeded()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	insert departmentLimitExcess
			$mixLimitID = array_search('departmentLimitExcess', $allMixLimits);
			if ($validatorResponse->isDepartmentLimitExceeded()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	insert dailyLimitExcess
			$mixLimitID = array_search('dailyLimitExcess', $allMixLimits);
			if ($validatorResponse->isDailyLimitExceeded()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	insert facilityAnnualLimitExcess
			$mixLimitID = array_search('facilityAnnualLimitExcess', $allMixLimits);
			if ($validatorResponse->getFacilityAnnualLimitExceeded()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	insert departmentAnnualLimitExcess
			$mixLimitID = array_search('departmentAnnualLimitExcess', $allMixLimits);
			if ($validatorResponse->getDepartmentAnnualLimitExceeded()) {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->insertRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
		//	update	
		} else {		
			
			//	update expired
			$mixLimitID = array_search('expired', $allMixLimits);			
			if ($validatorResponse->isExpired()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
						
			//	update preExpired
			$mixLimitID = array_search('preExpired', $allMixLimits);			
			if ($validatorResponse->isPreExpired()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	update facilityLimitExcess
			$mixLimitID = array_search('facilityLimitExcess', $allMixLimits);
			if ($validatorResponse->isFacilityLimitExceeded()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	update departmentLimitExcess
			$mixLimitID = array_search('departmentLimitExcess', $allMixLimits);
			if ($validatorResponse->isDepartmentLimitExceeded()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	update dailyLimitExcess
			$mixLimitID = array_search('dailyLimitExcess', $allMixLimits);
			if ($validatorResponse->isDailyLimitExceeded()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	update facilityLimitExcess
			$mixLimitID = array_search('facilityAnnualLimitExcess', $allMixLimits);
			if ($validatorResponse->getFacilityAnnualLimitExceeded()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
			
			//	update departmentLimitExcess
			$mixLimitID = array_search('departmentAnnualLimitExcess', $allMixLimits);
			if ($validatorResponse->getDepartmentAnnualLimitExceeded()) {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'yes');				
			} else {
				$this->updateRowMix2MixLimit($mixID, $mixLimitID, 'no');
			}
		}		
	}
	
	
	
	
	//	get all available mix limits 
	private function selectMixLimits() {			
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM mix_limit";				
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			$mixLimitsData = $this->db->fetch_all();
			foreach ($mixLimitsData as $mixLimitData) {				
				$mixLimits[$mixLimitData->mix_limit_id] = $mixLimitData->name;
			}
			return $mixLimits;
		} else {
			return false;
		}
	}
	
	
	
	
	//	insert row to mix2mix_limit
	private function insertRowMix2MixLimit ($mixID, $mixLimitID, $yesNo) {
		
		
		$mixID=mysql_escape_string($mixID);				
		$mixLimitID=mysql_escape_string($mixLimitID);
		$yesNo=mysql_escape_string($yesNo);
		
		//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO mix2mix_limit (mix_id, mix_limit_id, yesNo) VALUES (".$mixID.", ".$mixLimitID.", '".$yesNo."')";			
		$this->db->query($query);
	}
	
	
	
	
	//	update row to mix2mix_limit
	private function updateRowMix2MixLimit ($mixID, $mixLimitID, $yesNo) {
		
		
		$mixID=mysql_escape_string($mixID);		
		$mixLimitID=mysql_escape_string($mixLimitID);
		$yesNo=mysql_escape_string($yesNo);
		
		//$this->db->select_db(DB_NAME);
		$query = "UPDATE mix2mix_limit SET yesNo = '".$yesNo."' WHERE mix_id = ".$mixID." AND mix_limit_id = ".$mixLimitID;			
		$this->db->query($query);
	}
	

	public function recalcAllMixesProductsInLbs() {
		$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_MIXGROUP." ";
		$this->db->query($query);
		$data = $this->db->fetch_all();
		$unitTypeConverter = new UnitTypeConverter('lb');$count = 0;$count_not = 0;$count_yes = 0;
		foreach($data as $dbRecord) {
			$unittype = new Unittype($this->db);
			$type = $unittype->isWeightOrVolume($dbRecord->unit_type);
			if ($type == 'weight') {
				$value = $unitTypeConverter->convertToDefault($dbRecord->quantity,$unittype->getDescriptionByID($dbRecord->unit_type));
			} else {
				$product = new Product($this->db);
				$product->initializeByID($dbRecord->product_id);
				$density = $product->getDensity();
				$densityUnitID = $product->getDensityUnitID();
				$densityObj = new Density($this->db,$densityUnitID);
			    $densityType = array (
				    'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
					'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
			    );
			    $value = $unitTypeConverter->convertToDefault($dbRecord->quantity,$unittype->getDescriptionByID($dbRecord->unit_type),$density,$densityType);
			    if (empty($density) || $density == '0.00') {
			    	$value = null;
			    }
			}
			if ($value !== null) {
				$query = "UPDATE ".TB_MIXGROUP." SET `quantity_lbs` = ".$value." WHERE `mixgroup_id` = ".$dbRecord->mixgroup_id." ";
				$this->db->query($query);
				$count_yes++;
				if ($value < 0.01) {
					$count++;
				}
			} else {
				$count_not++;
			}
		}
		echo "<p> Count of elements with product = 0 in base(theirs value < 0.01): ".$count."</p>";
		echo "<p> Count of elements with product quantities can't be calculated in lbs: ".$count_not."</p>";
		echo "<p> Count of elements with product quantities calculated in lbs: ".$count_yes."</p>";
	}

	

//	public function recalcAllWastePercent($begin = 0,$end_final = 1000) {
//		$query_select = "SELECT mix_id, voc FROM ".TB_USAGE;
//		$end = $begin+100;
//		$this->db->query($query_select." LIMIT $begin, $end");
//		$begin += 100;
//		$end += 100;
//		$unitTypeConverter = new UnitTypeConverter();
//		//$mix = new Mix($this->db);
//		while($this->db->num_rows()>0 && $end<$end_final) {
//			$mixResult = $this->db->fetch_all();
//			foreach($mixResult as $mixID){
//				//$this->wastePercent = null;
//				$this->recalcAndSaveWastePersent($mixID->mix_id);
//			}
//			$this->db->query($query_select." LIMIT $begin, $end");
//			$begin +=100;
//			$end +=100;
//		}
//	}

	
	public function recalcAndSaveWastePersent($mixID, $wasteDetails = null) {
		$res = $this->initializeByID($mixID);
		if ($wasteDetails == null) {
		$this->wasteData = $this->getWasteDetails($mixID);
		} else {
			$this->wasteData = $wasteDetails;
		}
		$errors = $this->calculateCurrentUsage();
		$waste_Percent = round($this->getWastePercent(),2);
		$query= "UPDATE ".TB_USAGE." SET waste_percent='$waste_Percent' WHERE mix_id='".$mixID."'";
		$this->db->query($query);	
		return $waste_Percent;	
	}
	//	DEPRECATED
	private function _recalcLimits($creationTime, $departmentID) {
		
		$departmentID=mysql_escape_string($departmentID);
				
		$creationMonth = (empty($creationTime)) ? date('m') : substr($creationTime,0,2);
		$creationYear = (empty($creationTime)) ? date('Y') : substr($creationTime,-4);
		
		$query = "SELECT facility_id FROM ".TB_DEPARTMENT." WHERE department_id = ".$departmentID."";
		$this->db->query($query);
		$facilityID = $this->db->fetch(0)->facility_id;
				
		$query = "SELECT m.mix_id " .
				"FROM ".TB_USAGE." m, ".TB_DEPARTMENT." d " .
				"WHERE MONTH(m.creation_time) = ".$creationMonth." AND " .
				"YEAR(m.creation_time) = ".$creationYear." AND " .				
				"d.department_id = m.department_id AND " .
				"d.facility_id = ".$facilityID."";						
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			$mixesData = $this->db->fetch_all();
			foreach ($mixesData as $mixData) {				
				$this->calculateAndSaveMixLimits($mixData->mix_id);		
			}	
		}
	}
	
	
	
	//	Tracking System
	private function save2trash($CRUD, $id) {
		//	protect from SQL injections
		$id = mysql_escape_string($id);
		
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_USAGE, $id, $CRUD, $this->parentTrashRecord);
		
		//	DEPRECATED July 16, 2010
//		$id=mysql_escape_string($id);		
//		
//		if (isset($this->trashRecord)) {	
//			$query = "SELECT * FROM ".TB_USAGE." WHERE mix_id = ".$id;
//			$this->db->query($query);
//			$dataRows = $this->db->fetch_all();
//			
//			foreach ($dataRows as $dataRow) {				
//				$parentID = (isset($this->parentTrashRecord)) ? $this->parentTrashRecord->getID() : null;														
//										
//				$records = TrackingSystem::properties2array($dataRow);				
//				$this->trashRecord->setTable(TB_USAGE);		
//				$this->trashRecord->setData(json_encode($records[0]));
//				$this->trashRecord->setUserID($_SESSION['user_id']);
//				$this->trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//				$this->trashRecord->setDate(time());	//	current time
//				$this->trashRecord->setReferrer($parentID);				
//				$this->trashRecord->save();
//			}
//			
//			//	Why delete dependencies if we potentially will rollback them?
//			//	So if D then we do not save to trash cuz we do not delete mix_group etc 
//			if ($CRUD != 'D') {
//				//	load and save dependencies
//				if (false !== ($dependencies = $this->trashRecord->getDependencies(TrackingSystem::HIDDEN_DEPENDENCIES))) {							
//					foreach ($dependencies as $dependency) {
//						$parentID = ($dependency->getParentObj() !== null) ? $dependency->getParentObj()->getID() : null;
//						$dependency->setUserID($_SESSION['user_id']);
//						$dependency->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//						$dependency->setDate(time());	//	current time					
//						$dependency->setReferrer($parentID);
//						$dependency->save();									
//					}
//				}
//			}								
//		}		
	}	
}
?>