<?php
 
require_once('modules/phpgacl/gacl.class.php');
require_once('modules/phpgacl/gacl_api.class.php');

class Department extends DepartmentProperties {
	//	Properties
	private $db;	
	private $trashRecord;	//	tracking system trash obj
	private $parentTrashRecord;
	
	//	Methods
	
	function Department($db) {
		$this->db=$db;
	}
	
		
	function addNewDepartment($departmentData) {
		//$this->db->select_db(DB_NAME);
		
		//screening of quotation marks
		foreach ($departmentData as $key=>$value)
		{
			$departmentData[$key]=mysql_escape_string($value);
		}
		
		$query="INSERT INTO ".TB_DEPARTMENT." (facility_id, name, creater_id, voc_limit, voc_annual_limit) VALUES (";
		
		$query.=$departmentData['facility_id'].", ";
		$query.="'".$departmentData['name']."', ";
		$query.="'".$departmentData['creater_id']."', ";
		$query.=$departmentData['voc_limit'].", ";
		$query.=$departmentData['voc_annual_limit'].")";
		$this->db->query($query);
										
		//	save to trash_bin
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$department_id=$this->db->fetch(0)->id;
		
		//----------------------------------------------------------------
		//GACL
		//----------------------------------------------------------------
		$query= "SELECT * FROM ".TB_FACILITY." WHERE facility_id = ".$departmentData['facility_id'];
		$this->db->query($query);
		$data=$this->db->fetch(0);
		$company_id=$data->company_id;
				
		//   CREATE ACO
		$gacl_api = new gacl_api();
		$acoID = $gacl_api->add_object('access', "department_".$department_id, "department_".$department_id, 0, 0, 'ACO');
		//   CREATE ARO GROUPs
		$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
		$aro_group_department = $gacl_api->add_group("department_".$department_id, "department_".$department_id, $giantcomliance, 'ARO');
		$aro_group_facility=$gacl_api->get_group_id ("facility_".$departmentData['facility_id']);
		$aro_group_company=$gacl_api->get_group_id ("company_".$company_id);
		$aro_group_root=$gacl_api->get_group_id("root");
		//   CREATE ACL
		$acoArray = array('access'=>array("department_".$department_id));		
		$departmentGroup = array($aro_group_department);
		$facilityGroup = array($aro_group_facility);
		$companyGroup = array($aro_group_company);
		$rootGroup = array($aro_group_root);	
		$gacl_api->add_acl($acoArray,NULL,$departmentGroup,NULL,NULL,1,1,NULL,'department users has access to department ACO ');	
		$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,1,1,NULL,'facility users has access to department ACO ');
		$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company users has access to department ACO ');
		$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to department ACO ');
		
		//-----------------------------------------------------------------
		
		$this->save2trash('C', $department_id);	
		return $department_id;		
	}
	
	
	
	
	function getDepartmentDetails($department_id) {
		
		$department_id=mysql_real_escape_string($department_id);
		
		//$this->db->select_db(DB_NAME);
		
		$this->db->query("SELECT * FROM ".TB_DEPARTMENT." WHERE department_id='".$department_id."' ORDER BY name");
		
		$departmentDetails=$this->db->fetch_array(0);
		/*$departmentDetails=array (
			'facility_id'		=>	$data->facility_id,
			'department_id'		=>	$data->department_id,
			'name'				=>	$data->name,
			'creater_id'		=>	$data->creater_id,
			'voc_limit'			=>	$data->voc_limit,
			'voc_annual_limit'	=>	$data->voc_annual_limit			
		);*/
		return $departmentDetails;
	}
	
	function setDepartmentDetails($departmentData) {
		
		//screening of quotation marks
		foreach ($departmentData as $key=>$value)
		{
			$departmentData[$key]=mysql_real_escape_string($value);
		}
		
		//$this->db->select_db(DB_NAME);
		
		//	check voc limit change
		$recalculteMixLimits = false;
		if ($this->isVocLimitChanged($departmentData["department_id"], $departmentData["voc_limit"], $departmentData["voc_annual_limit"])) {
			$recalculteMixLimits = true;			
		}
		
		//	save to trash_bin
		$this->save2trash('U', $departmentData["department_id"]);		
		
		$query="UPDATE ".TB_DEPARTMENT." SET ";
		$query.="name='".$departmentData['name']."', ";
		$query.="voc_limit='".$departmentData['voc_limit']."', ";
		$query.="voc_annual_limit='".$departmentData['voc_annual_limit']."' ";
		
		$query.="WHERE department_id=".$departmentData["department_id"];
		
		$this->db->query($query);
		
		//DEPRECATED
		//	VOC limit was changed. We should recalculte all mix limits
		//	It can take some minutes. Please wait...
//		ini_set("max_execution_time","180"); 
//		if ($recalculteMixLimits) {			
//			$query = "SELECT mix_id FROM ".TB_USAGE." WHERE department_id = ".$departmentData["department_id"];		
//			$this->db->query($query);
//		
//			$mixesData = $this->db->fetch_all();		
//			$mix = new Mix($this->db);		
//			foreach($mixesData as $mixData) {							
//				$mix->calculateAndSaveMixLimits($mixData->mix_id);
//			}
//		}
	}	
	
	function getDepartmentListByFacility($facility_id, Pagination $pagination = null, $filter='TRUE',$sort="ORDER BY name") {
		
		$facility_id=mysql_escape_string($facility_id);		
		
		$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE facility_id=".$facility_id. " AND $filter $sort";
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}			
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				
				$department=array (
					'id'	=>	$data->department_id,
					'name'	=>	$data->name
				);
				
				$departmentList[]=$department;
			}
		}
		
		return $departmentList;
	}	
	
		
	function deleteDepartment($departmentID) {
		
		$departmentID=mysql_real_escape_string($departmentID);
		
		//$this->db->select_db(DB_NAME);
		
		//	save to trash_bin
		$this->save2trash('D', $departmentID);		
		
		$this->db->query("SELECT * FROM ".TB_EQUIPMENT." WHERE department_id = " .$departmentID);
		$equipmentCount = $this->db->num_rows();
		$equipmentToDelete = $this->db->fetch_all();
		if ($equipmentCount > 0) {
			$equipment = new Equipment($this->db);
			$equipment->setParentTrashRecord($this->trashRecord);			
			for ($i=0; $i<$equipmentCount; $i++) {
				$equipment->setTrashRecord(new Trash($this->db));							
				$equipment->deleteEquipment($equipmentToDelete[$i]->equipment_id);				
			}
		}					
		
		//	remove use_location at material inventory
		//$this->db->query("DELETE FROM use_location2material WHERE department_id = ".$departmentID."");
		
		$this->db->query("DELETE FROM ".TB_DEPARTMENT." WHERE department_id = ".$departmentID);
	}
	
	
	
	
	public function initializeByID($departmentID) {
		
		$departmentID=mysql_real_escape_string($departmentID);
		
		$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE department_id=".$departmentID;
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {
			$departmentData = $this->db->fetch(0);
			
			$this->departmentID		=	$departmentData->department_id;
			$this->facilityID		=	$departmentData->facility_id;
			$this->name				=	$departmentData->name;
			$this->vocLimit			=	$departmentData->voc_limit;
			$this->vocAnnualLimit	=	$departmentData->voc_annual_limit;
			
			$query = 'SELECT sum(value) total_usage, yyyy, mm FROM '.TB_USAGE_STATS.' WHERE department_id = '.$departmentID. ' GROUP BY yyyy, mm';			
			$this->db->query($query);
			if ($this->db->num_rows() > 0) {
				$usageData = $this->db->fetch_all();
				$annualUsage = array();
				foreach ($usageData as $usage) {
					$annualUsage[$usage->yyyy][$usage->mm] = $usage->total_usage;
				}
				$this->annualUsage = $annualUsage;				
			}
					
		} else {
			return false;
		}
		
		return true;
	}
	
	private function calculateCurrentUsage($forDepartment = true) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_USAGE." WHERE department_id=".$this->departmentID;
		$this->db->query($query);
		
		$totalUsage = 0;
		
		if ($this->db->num_rows() > 0) {
			$mixes = $this->db->fetch_all();
			
			foreach ($mixes as $mix) {
				$mix_id = $mix->mix_id;
				
				$mix = new Mix($this->db);
				$mix->initializeByID($mix_id);
				
				if ($forDepartment) {
					//	If tracker for Department
					if ($mix->getEquipment()->isTrackedTowardsDepartment()) {
						$totalUsage += $mix->getCurrentUsage();
					} 
				} else {
					//	If tracked for Facility
					if ($mix->getEquipment()->isTrackedTowardsFacility()) {
						$totalUsage += $mix->getCurrentUsage();						
					}
				}
			}
		}
		
		$this->currentUsage = $totalUsage;
	}
	
	public function getCurrentUsage() {
		//$this->calculateCurrentUsage();
		$this->getCurrentUsageOptimized();
		return $this->currentUsage;
	}
	
	public function getFacility() {
		$facility = new Facility($this->db);
		$facility->initializeByID($this->facilityID);
		
		return $facility;
	}
	
	
	
	public function isOverLimit() {
		
		if ($this->getDailyLimit() > 0) {		
			return $this->getCurrentUsage() > $this->getDailyLimit();
		}
		
		return false;
	}
	
	public function clearDepartment(){		
		//$this->db->select_db(DB_NAME);
    	
    	$query = "DELETE FROM ".TB_DEPARTMENT;
    	$this->db->query($query);	
	}
	
	public function fillDepartment(){
		$this->db->select_db(DB_IMPORT);    	
    	$query = "INSERT INTO ".DB_NAME.".".TB_DEPARTMENT." SELECT * FROM ".DB_IMPORT.".".TB_DEPARTMENT;
    	$this->db->query($query);    	
	}
		
		
	public function getCurrentUsageOptimized($month = 'MONTH(CURRENT_DATE)', $year = 'YEAR(CURRENT_DATE)', $forDepartment = true) {
		
		$month=mysql_real_escape_string($month);
		$year=mysql_real_escape_string($year);
		
		//$this->db->select_db(DB_NAME);
		
		//this query calculates total usage [total_usage] and gets voc limit for department [voc_limit]
		if ($forDepartment) {
			$query = "SELECT sum( m.voc ) total_usage , d.voc_limit,  MONTH( FROM_UNIXTIME(m.creation_time) ) creation_month " .
				 "FROM ".TB_DEPARTMENT." d, ".TB_USAGE." m , ".TB_EQUIPMENT." e " .
				 "WHERE m.department_id = d.department_id " .
				 "AND e.equipment_id = m.equipment_id " .
				 "AND e.dept_track = 'yes' " .
				 "AND MONTH(FROM_UNIXTIME(m.creation_time)) = ".$month." " .
				 "AND YEAR(FROM_UNIXTIME(m.creation_time)) = ".$year." " .
				 "AND d.department_id = ".$this->departmentID." ".
				 "GROUP BY voc_limit";	
		} else {
			$query = "SELECT sum( m.voc ) total_usage , d.voc_limit, MONTH(FROM_UNIXTIME(m.creation_time)) creation_month " .
				 "FROM ".TB_DEPARTMENT." d, ".TB_USAGE." m , ".TB_EQUIPMENT." e " .
				 "WHERE m.department_id = d.department_id " .
				 "AND e.equipment_id = m.equipment_id " .
				 "AND e.facility_track = 'yes' " .
				 "AND MONTH(FROM_UNIXTIME(m.creation_time)) = ".$month." " .
				 "AND YEAR(FROM_UNIXTIME(m.creation_time)) = ".$year." " .
				 "AND d.department_id = ".$this->departmentID." ".
				 "GROUP BY voc_limit";
		}	
		
		$this->db->query($query);		
				
		$numRows = $this->db->num_rows();
		$currentDate = getdate();
		if ($numRows > 0) {				
			$data = $this->db->fetch(0);
			if ((int)$currentDate['mon'] == (int)$data->creation_month) {
				$this->currentUsage = (float)$data->total_usage;		
			}			
			
			return (float)$data->total_usage;									
		} else {
			$this->currentUsage = 0;
			return $this->currentUsage;
		} 
			
	}
	
	
	
	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection	
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;		
	}
	public function setParentTrashRecord(iTrash $trashRecord) {
		$this->parentTrashRecord = $trashRecord;		
	}	
	
	
	
	public function getInventoryList($sort=' ORDER BY i.name ') {		
		$inventoryList = array(Inventory::PAINT_MATERIAL => array(), Inventory::PAINT_ACCESSORY => array());
		
		//	facilityid property should be set 
		if (!isset($this->departmentID)) {			
			return false;
		}
				
		$query = "SELECT DISTINCT(i.id) id " .
				"FROM ".TB_INVENTORY." i, ".TB_MATERIAL2INVENTORY." m2i, ".TB_USE_LOCATION2MATERIAL." ul2m " .
				"WHERE i.id = m2i.inventory_id AND " .
				"m2i.id = ul2m.material2inventory_id AND " .
				"ul2m.department_id = ".$this->departmentID." $sort";
		$this->db->query($query);		
		//	sql should not return empty result
		if ($this->db->num_rows() == 0) {			
			return false;
		}				
		
		$dataRows = $this->db->fetch_all();
		foreach ($dataRows as $dataRow) {
			$inventory = new Inventory($this->db, $dataRow->id, $loadProducts = false);
			//$inventory->xnyo2properties($dataRow);			
			
			$inventoryList[$inventory->getType()][] = $inventory;						
		}
		
		return $inventoryList;		
	}
	
	
	
	
	public function getAvailableInventoryList() {		
		$inventoryList = array(Inventory::PAINT_MATERIAL => array(), Inventory::PAINT_ACCESSORY => array());
		
		//	facilityid property should be set 
		if (!isset($this->departmentID)) {			
			return false;
		}
				
		$query = "SELECT DISTINCT(id) id " .
				"FROM ".TB_INVENTORY." " .
				"WHERE facility_id = ".$this->facilityID." AND " .
				"id NOT IN " .
					"(SELECT DISTINCT(i.id) FROM ".TB_INVENTORY." i, ".TB_MATERIAL2INVENTORY." m2i, ".TB_USE_LOCATION2MATERIAL." ul2m " .
						"WHERE i.id = m2i.inventory_id AND " .
						"m2i.id = ul2m.material2inventory_id AND " .
						"ul2m.department_id = ".$this->departmentID.")";
		$this->db->query($query);
				
		//	sql should not return empty result
		if ($this->db->num_rows() == 0) {			
			return false;
		}				
		
		$dataRows = $this->db->fetch_all();
		foreach ($dataRows as $dataRow) {
			$inventory = new Inventory($this->db, $dataRow->id, $loadProducts = false);			
			$availableInventoryList[$inventory->getType()][] = $inventory;						
		}
		
		return $availableInventoryList;		
	}
	

	public function calculateSolidsMass($departmentID = null, $dateBegin = null, $dateEnd = null) {
		if ($departmentID === null) {
			$departmentID = $this->departmentID;
		}
		if ($dateBegin === null && $dateEnd === null) {
			$dateEnd = date("Y-m-d",time());
			$year = substr($dateEnd,0,4);
			$year--;
			$dateBegin = $year.substr($dateEnd,4);
		} else {
			if ($dateBegin === null) {
				$year = substr($dateEnd,0,4);
				$year--;
				$dateBegin = $year.substr($dateEnd,4);
			} elseif ($dateEnd === null) {
				$year = substr($dateBegin,0,4);
				$year++;
				$dateEnd = $year.substr($dateBegin,4);		
			}
		}
		
		//---------------------------
		
		$query = "SELECT mg.product_id AS id, mg.quantity_lbs AS quantity, p.vocwx AS vocwx, p.density AS density, p.percent_volatile_weight AS percent FROM ".TB_MIXGROUP." mg, ".TB_PRODUCT." p WHERE mg.mix_id IN (".
			"SELECT mix_id FROM ".TB_USAGE." WHERE department_id = '".$departmentID."' ".
			"AND creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
			"AND DATE_FORMAT('" . date("Y-m-d", strtotime($dateEnd)). "','%Y-%m-%d') " .
			") AND p.product_id = mg.product_id";
		$this->db->query($query);
		$data = $this->db->fetch_all_array();
		$productError = array();
		$solidSumm = 0;
		foreach($data as $info) {
			
			if ($info['percent'] === false && ($info['vocwx'] === false || $info['density'] === false)) {
				$productError []= $info['id'];
			} elseif ($info['percent'] !== false) {
				$solidSumm += $info['quantity'] * (1 - $info['percent']/100);
			} else {
				$solidSumm += $info['quantity'] * (1 - $info['vocwx']/$info['density']);
			}
		}
			//----------------------
			
//		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE department_id = '".$departmentID."' ".
//			"AND creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
//			"AND DATE_FORMAT('" . date("Y-m-d", strtotime($dateEnd)). "','%Y-%m-%d') " ." ";
//		$this->db->query($query);
//		$mixIds = $this->db->fetch_all();
//		$solidSumm = 0;
//		$productError = array();
//		$productInfo = array();
//		foreach($mixIds as $mixId) {
//			$query = "SELECT product_id, quantity_lbs FROM ".TB_MIXGROUP." WHERE mix_id = '".$mixId->mix_id."' ";
//			$this->db->query($query);
//			$data = $this->db->fetch_all();
//			foreach ($data as $productData) {
//				if ($productData->quantity_lbs === null) {
//					$productError []= $productData->product_id;
//				} else {
//					if (!isset($productInfo[$productData->product_id])) {
//						$query = "SELECT vocwx, density, percent_volatile_weight FROM ".TB_PRODUCT." WHERE product_id = '$productData->product_id' LIMIT 0,1 ";
//						$this->db->query($query);
//						$pData = $this->db->fetch(0);
//						$productInfo[$productData->product_id] = array(
//							'vocwx' => (empty($pData->vocwx) || $pData->vocwx == '0.00')?'false':$pData->vocwx,
//							'density' => (empty($pData->density) || $pData->density == '0.00')?'false':$pData->density,
//							'percent' => (empty($pData->percent_volatile_weight) || $pData->percent_volatile_weight == '0.00')?'false':$pData->percent_volatile_weight
//						);
//					}
//					if ($productInfo[$productData->product_id]['percent'] != 'false') {
//						$forSumm = $productData->quantity_lbs * (1 - $productInfo[$productData->product_id]['percent']/100);
//					} elseif($productInfo[$productData->product_id]['vocwx'] != 'false' && $productInfo[$productData->product_id]['density'] != 'false') {
//						$forSumm = $productData->quantity_lbs * (1 - $productInfo[$productData->product_id]['vocwx']/$productInfo[$productData->product_id]['density']);
//					} else {
//						$productError []= $productData->product_id;
//						$forSumm = 0;
//					}
//					
//					$solidSumm += $forSumm;
//				}
//			}
//		} 
		$errorArr = array();
		foreach ($productError as $error) {
			if(!in_array($error,$errorArr)) {
				$errorArr []= $error;
			}
		}
		$result = array(
			'errors' => $errorArr,
			'value' => $solidSumm
		);
		return $result;
	}
	
	
	
	/**
	 * 
	 * calculate annual usage for whole department
	 * @param string $year
	 */
	public function calculateAnnualUsage($year = '') {
		//	setting default year
		if ($year === '') {
			$year = date('Y');
		}
		
		$query = "SELECT sum( voc ) total_usage " .
			"FROM ".TB_USAGE." " .
			"WHERE department_id = ".$this->departmentID." " .			
			"AND YEAR(creation_time) = ".$year." ";
		
		$this->db->query($query);

		$this->annualUsage[$year] = $this->db->fetch(0);
		
		return $this->annualUsage[$year]; 
	}
	
	
		
	public function incrementUsage($mm, $yyyy, $value, $departmentID = null) {
		$input = $this->_prepareUsageStatsEditing($mm, $yyyy, $value, $departmentID);
		
		if (false === ($currentValue = $this->_isUsageStatExist($input['mm'], $input['yyyy'], $input['departmentID']))) {
			//	insert
			$query = 'INSERT INTO '.TB_USAGE_STATS.' (mm, yyyy, value, department_id, facility_id) VALUES (' .
					''.$input['mm'].', '.
					''.$input['yyyy'].', ' .
					''.$input['value'].', ' .
					''.$input['departmentID'].', ' .
					''.$input['facilityID'].')';			
			
			//echo "<br/>add Usage stats $query";
			$this->db->exec($query);			
			return $value;
			
		} else {			
			//	update
			$newValue = $currentValue + $value;
			$query = 'UPDATE '.TB_USAGE_STATS.' SET ' .
					 'value = '.$newValue.' ' .
					 'WHERE mm = '.$input['mm'].' AND yyyy = '.$input['yyyy'].' AND department_id = '.$input['departmentID'].'';
			$this->db->exec($query);
			//echo "<br/>add new Usage stats $query";
			return $newValue;
		}	
	}
	
	public function decrementUsage($mm, $yyyy, $value, $departmentID = null) {			
		$input = $this->_prepareUsageStatsEditing($mm, $yyyy, $value, $departmentID);
		
		$currentValue = $this->_isUsageStatExist($mm, $yyyy, $departmentID);
		
		if (false === $currentValue) {
			//	decrement unexisted usage. wtf?
			throw new Exception('Something wrong. I cannot reduce usage for Department ID '.$departmentID.' ( period: '.$yyyy.'-'.$mm.') ');
			
		} else {			
			//	update
			$newValue = $currentValue - $value;
			
			if ($newValue < 0) {
				//	new usage is less 0. wtf?
				throw new Exception('Something wrong. New usage is less than 0. I cannot reduce usage for Department ID '.$departmentID.' ( period: '.$yyyy.'-'.$mm.') ');
			}
			
			$query = 'UPDATE '.TB_USAGE_STATS.' SET ' .
					 'value = '.$newValue.' ' .
					 'WHERE mm = '.$input['mm'].' AND yyyy = '.$input['yyyy'].' AND department_id = '.$input['departmentID'].'';

			$this->db->exec($query);
			
			return $newValue;
		}	
	}
	
	
		
	public function countDepartments($facilityID,$filter='TRUE') {
		
		$facilityID = mysql_escape_string($facilityID);		
		$query = "SELECT count(department_id) departmentCount FROM ".TB_DEPARTMENT." WHERE facility_id = $facilityID AND $filter";
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->departmentCount;
		} else 
			return false;
	}
	
	
	private function _prepareUsageStatsEditing($mm, $yyyy, $value, $departmentID = null) {		
		//	get departmentID & facilityID
		if ($departmentID === null) {
			$departmentID = $this->departmentID;
			$facilityID = $this->getFacilityID();
		} else {
			//	если вызвали через жопу, тогда +1 лишний вызов getDepartmentDetails()
			//	а что делать..
			$departmentDetails = $this->getDepartmentDetails($departmentID);
			$facilityID = $departmentDetails['facility_id'];			
		}
		
		//	escape before using at SQL
		$departmentID = mysql_real_escape_string($departmentID); 
		$mm = mysql_real_escape_string($mm);
		$yyyy = mysql_real_escape_string($yyyy);		 
		$value = mysql_real_escape_string($value);
		
		return array('departmentID'=>$departmentID, 
					 'facilityID'=>$facilityID,
					 'mm'=>$mm,
					 'yyyy'=>$yyyy,
					 'value'=>$value
				);
	}
	
	
	
	private function _isUsageStatExist($mm, $yyyy, $departmentID) {
		$query = 'SELECT value FROM '.TB_USAGE_STATS.' WHERE mm = '.$mm.' AND yyyy = '.$yyyy.' AND department_id = '.$departmentID.'';
		$this->db->query($query);		
		
		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->value : false;
	}  
	
	
	//	check difference between $vocLimit and DB value
	private function isVocLimitChanged($departmentID, $vocLimit, $vocAnnualLimit) {
		
		$departmentID=mysql_real_escape_string($departmentID); 
		$vocLimit=mysql_real_escape_string($vocLimit);
		$vocAnnualLimit=mysql_real_escape_string($vocAnnualLimit);		 
		
		$query = "SELECT voc_limit FROM ".TB_DEPARTMENT." WHERE facility_id = ".$departmentID." AND voc_limit = ".$vocLimit." AND voc_annual_limit = ".$vocAnnualLimit;
		$this->db->query($query);
		
		return ($this->db->num_rows() > 0) ? false : true;
	}
	
	
	
	//	Tracking System
	private function save2trash($CRUD, $departmentID) {

		//	protect from SQL injections
		$departmentID = mysql_real_escape_string($departmentID);
		
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_DEPARTMENT, $departmentID, $CRUD, $this->parentTrashRecord);

		
		//	DEPRECATED July 16, 2010

		
//		$departmentID=mysql_real_escape_string($departmentID);
//		
//		if (isset($this->trashRecord)) {	
//			$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE department_id = ".$departmentID;
//			$this->db->query($query);
//			$dataRows = $this->db->fetch_all();
//			
//			foreach ($dataRows as $dataRow) {				
//				$parentID = (isset($this->parentTrashRecord)) ? $this->parentTrashRecord->getID() : null;																												
//				
//				$departmentRecords = TrackingSystem::properties2array($dataRow);		
//				$this->trashRecord->setTable(TB_DEPARTMENT);		
//				$this->trashRecord->setData(json_encode($departmentRecords[0]));
//				$this->trashRecord->setUserID($_SESSION['user_id']);
//				$this->trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//				$this->trashRecord->setDate(time());	//	current time
//				$this->trashRecord->setReferrer($parentID);				
//				$this->trashRecord->save();
//				
//			}					
//			//	load and save dependencies
//			if ($CRUD != 'D') {					
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
