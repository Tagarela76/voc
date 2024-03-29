<?php
require_once(site_path.'modules/phpgacl/gacl.class.php');
require_once(site_path.'modules/phpgacl/gacl_api.class.php');

class Facility extends FacilityProperties {
	//	Properties
	private $db;
	private $trashRecord;
	private $parentTrashRecord;

	
	public $searchCriteria = array();
	
	const TB_PROCESS = 'process';
	//	Methods
	
	function __construct($db) {
		$this->db=$db;
	}

	public function getRepairOrdersList($facilityId, Pagination $pagination = null) {
		
		$repairOrders = array();
		
		$sql = "SELECT * FROM ". TB_WORK_ORDER. "
				WHERE facility_id={$this->db->sqltext($facilityId)}"; 
	
		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}

		$sql .= " ORDER BY id DESC";

        if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}        
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		foreach ($rows as $row) {
			$repairOrder = new RepairOrder($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($repairOrder, $key)) {
					$repairOrder->$key = $value;
				}
			}
			$repairOrders[] = $repairOrder;
		}
		return $repairOrders;
	}

    /**
	 * Count work order in facility
	 * @param int $facilityID
	 * @return bool|int false on failure
	 */
	public function countRepairOrderInFacility($facilityID) {
		$sql = "SELECT count(id) repairOrderCount FROM ". TB_WORK_ORDER. "
				WHERE facility_id={$this->db->sqltext($facilityID)}"; 

		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}		
		$this->db->query($sql);
		if ($this->db->num_rows() > 0) {
			return (int)$this->db->fetch(0)->repairOrderCount;
		} else {
			return false;
		}
	}


	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;
	}
	public function setParentTrashRecord(iTrash $trashRecord) {
		$this->parentTrashRecord = $trashRecord;
	}




	function addNewFacility($facilityData) {

		//screening of quotation marks
	/*	foreach ($facilityData as $key=>$value)
		{
			$facilityData[$key]=$this->db->sqltext($value);
		}
*/
		//	GCG Creation
		$GCG = new GCG($this->db);
		$gcgID = $GCG->create();

		//$this->db->select_db(DB_NAME);
		$query="INSERT INTO ".TB_FACILITY." (epa, company_id, name, address, city, zip, county, state, country, phone, fax, email, contact, title, creater_id, voc_limit, voc_annual_limit, gcg_id, monthly_nox_limit) VALUES (";

		$query.="'".$this->db->sqltext($facilityData['epa'])."', ";
		$query.=$this->db->sqltext($facilityData['company_id']).", ";
		$query.="'".$this->db->sqltext($facilityData['name'])."', ";
		$query.="'".$this->db->sqltext($facilityData['address'])."', ";
		$query.="'".$this->db->sqltext($facilityData['city'])."', ";
		$query.="'".$this->db->sqltext($facilityData['zip'])."', ";
		$query.="'".$this->db->sqltext($facilityData['county'])."', ";
		$query.="'".$this->db->sqltext($facilityData['state'])."', ";
		$query.=$this->db->sqltext($facilityData['country']).", ";
		$query.="'".$this->db->sqltext($facilityData['phone'])."', ";
		$query.="'".$this->db->sqltext($facilityData['fax'])."', ";
		$query.="'".$this->db->sqltext($facilityData['email'])."', ";
		$query.="'".$this->db->sqltext($facilityData['contact'])."', ";
		$query.="'".$this->db->sqltext($facilityData['title'])."', ";
		$query.="'".$this->db->sqltext($facilityData['creater_id'])."', ";
		$query.=$this->db->sqltext($facilityData['voc_limit']).", ";
		$query.=$this->db->sqltext($facilityData['voc_annual_limit']).", ";
		$query.=$gcgID.", ";
		$query .= $this->db->sqltext($facilityData['monthly_nox_limit']).")"; 
		$this->db->query($query);


		$this->db->query("SELECT LAST_INSERT_ID() id");
		$facility_id=$this->db->fetch(0)->id;
		//----------------------------------------------------------------
		//GACL
		//----------------------------------------------------------------

		//   CREATE ACO
		$gacl_api = new gacl_api();
		$acoID = $gacl_api->add_object('access', "facility_".$facility_id, "facility_".$facility_id, 0, 0, 'ACO');
		//   CREATE ARO GROUPs
		$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
		$aro_group_facility = $gacl_api->add_group("facility_".$facility_id, "facility_".$facility_id, $giantcomliance, 'ARO');
		$aro_group_company=$gacl_api->get_group_id ("company_".$facilityData['company_id']);
		$aro_group_root=$gacl_api->get_group_id("root");
		//   CREATE ACL
		$acoArray = array("access"=>array("facility_".$facility_id));
		$facilityGroup = array($aro_group_facility);
		$companyGroup = array($aro_group_company);
		$rootGroup = array($aro_group_root);
		$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,1,1,NULL,'facility users has access to facility ACO ');
		$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company users has access to facility ACO ');
		$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to facility ACO ');


		//-----------------------------------------------------------------

		//	save to trash_bin
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$this->save2trash('C', $facility_id);
		return $facility_id;
	}

	function getFacilityDetails($facility_id, $vanilla=false) {
		$facility_id=$this->db->sqltext($facility_id);

		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_FACILITY." WHERE facility_id='".$facility_id."' ORDER BY name LIMIT 1");
		$facilityDetails=$this->db->fetch_array(0);
		/*$facilityDetails=array (
			'facility_id'	=>	$data->facility_id,
			'epa'			=>	$data->epa,
			'company_id'	=>	$data->company_id,
			'name'			=>	$data->name,
			'address'		=>	$data->address,
			'city'			=>	$data->city,
			'zip'			=>	$data->zip,
			'county'		=>	$data->county,
			'state'			=>	$data->state,
			'country'		=>	$data->country,
			'phone'			=>	$data->phone,
			'fax'			=>	$data->fax,
			'email'			=>	$data->email,
			'contact'		=>	$data->contact,
			'title'			=>	$data->title,
			'creater_id'	=>	$data->creater_id,
			'voc_limit'		=>	$data->voc_limit,
			'voc_annual_limit'	=>	$data->voc_annual_limit,
			'gcg_id'		=>	$data->gcg_id
		);*/
		//var_dump($facilityDetails);
		if (!$vanilla) {
			$reg = new Registration($this->db);

			//	Set State
			if ($reg->isOwnState($facilityDetails['country'])) {
				//	have own state list
				$facilityDetails["state"] = $reg->getState($facilityDetails['state']);
			}

			//	Set Country
			$facilityDetails["country"] = $reg->getCountry($facilityDetails['country']);
		}
		
		return $facilityDetails;
	}

	function setFacilityDetails($facilityData) {

		//screening of quotation marks
/*		foreach ($facilityData as $key=>$value)
		{
			$facilityData[$key]=$this->db->sqltext($value);
		}
*/
		//	check voc limit change
		$recalculteMixLimits = false;
		if ($this->isVocLimitChanged($facilityData["facility_id"], $facilityData["voc_limit"], $facilityData["voc_annual_limit"])) {
			$recalculteMixLimits = true;
		}

		//	save to trash
		$this->save2trash('U', $facilityData["facility_id"]);

		$query="UPDATE ".TB_FACILITY." SET ";

		$query.="epa='".		$this->db->sqltext($facilityData['epa'])."', ";
		$query.="voc_limit='".	$this->db->sqltext($facilityData['voc_limit'])."', ";
		$query.="voc_annual_limit='".	$this->db->sqltext($facilityData['voc_annual_limit'])."', ";
		$query.="name='".		$this->db->sqltext($facilityData['name'])."', ";
		$query.="address='".	$this->db->sqltext($facilityData['address'])."', ";
		$query.="city='".		$this->db->sqltext($facilityData['city'])."', ";
		$query.="zip='".		$this->db->sqltext($facilityData['zip'])."', ";
		$query.="county='".		$this->db->sqltext($facilityData['county'])."', ";
		$query.="state='".		$this->db->sqltext($facilityData['state'])."', ";
		$query.="country=".		$this->db->sqltext($facilityData['country']).", ";
		$query.="phone='".		$this->db->sqltext($facilityData['phone'])."', ";
		$query.="fax='".		$this->db->sqltext($facilityData['fax'])."', ";
		$query.="email='".		$this->db->sqltext($facilityData['email'])."', ";
		$query.="contact='".		$this->db->sqltext($facilityData['contact'])."', ";
		$query.="monthly_nox_limit='".		$this->db->sqltext($facilityData['monthly_nox_limit'])."', ";
		$query.="title='".		$this->db->sqltext($facilityData['title'])."' ";

		$query.="WHERE facility_id=".$facilityData["facility_id"];
		$this->db->query($query);

	}

	function getFacilityListByCompany($company_id) {

		$company_id=$this->db->sqltext($company_id);

		$sql = "SELECT f.facility_id id, f.name, f.address, f.contact, f.phone, s.name stateName " .
				"FROM ".TB_FACILITY." f " .
				"LEFT JOIN ".TB_STATE." s ON f.state = s.state_id " .
				"WHERE company_id=".$company_id. " ORDER BY f.name";
		$this->db->query($sql);
		
		$facilityList = null;
		if ($this->db->num_rows()) {
			$facilityList = $this->db->fetch_all_array();
			/*for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$facility=array (
					'id'	=>	$data->facility_id,
					'name'			=>	$data->name,
					'address'		=>	$data->address,
					'contact'		=>	$data->contact,
					'phone'			=>	$data->phone
				);
				$facilityList[]=$facility;
			}*/
		}

		return $facilityList;
	}

	//	NOT TESTED

	function deleteFacility($facility_id) {

		$facility_id=$this->db->sqltext($facility_id);

		//$this->db->select_db(DB_NAME);

		//	save to trash
		$this->save2trash('D', $facility_id);

		$this->db->query("SELECT * FROM ".TB_DEPARTMENT." WHERE facility_id = ".$facility_id);
		$departmentsCount = $this->db->num_rows();
		$departmentToDelete = $this->db->fetch_all();
		if ($departmentsCount > 0) {
			$department = new Department($this->db);
			$department->setParentTrashRecord($this->trashRecord);
			for ($i=0; $i<$departmentsCount; $i++) {
				$department->setTrashRecord(new Trash($this->db));
				$department->deleteDepartment($departmentToDelete[$i]->department_id);
			}
		}

		$this->db->query("SELECT id FROM ".TB_INVENTORY." WHERE facility_id = " .$facility_id);
		if ($this->db->num_rows > 0) {
			$inventoryList = $this->db->fetch_all();
			foreach ($inventoryList as $inventoryData) {
				$inventory = new Inventory($this->db, $inventoryData->id);
				$inventory->setParentTrashRecord($this->trashRecord);
				$inventory->setTrashRecord(new Trash($this->db));
				$inventory->delete();
			}
		}

		$this->db->query("DELETE FROM ".TB_FACILITY." WHERE facility_id=".$facility_id);
	}




	public function initializeByID($facilityID) {

		$facilityID=$this->db->sqltext($facilityID);

		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_FACILITY." WHERE facility_id=".$facilityID;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$facilityData = $this->db->fetch(0);
			$this->facilityID = $facilityData->facility_id;
			$this->vocLimit = $facilityData->voc_limit;
			$this->vocAnnualLimit = $facilityData->voc_annual_limit;
			$this->companyID = $facilityData->company_id;
			$this->monthlyNoxLimit = $facilityData->monthly_nox_limit;
			
			$query = 'SELECT sum(value) total_usage, yyyy, mm FROM '.TB_USAGE_STATS.' WHERE facility_id = '.$facilityID. ' GROUP BY yyyy, mm';
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




	public function getCurrentUsage() {
		$this->calculateCurrentUsage();

		return $this->currentUsage;
	}

	private function calculateCurrentUsage() {

		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE facility_id=".$this->facilityID;
		$this->db->query($query);

		$totalUsage = 0;

		if ($this->db->num_rows() > 0) {
			$departments = $this->db->fetch_all();

			foreach($departments as $department) {
				$departmentID = $department->department_id;

				$department = new Department($this->db);
				$department->initializeByID($departmentID);

				$totalUsage += $department->getCurrentUsage(false);
			}
		}

		$this->currentUsage = $totalUsage;
	}

	public function isOverLimit() {
		if ($this->getMonthlyLimit() > 0) {
			//$this->calculateCurrentUsage();
			$this->getCurrentUsageOptimized();
			return $this->currentUsage > (float)$this->getMonthlyLimit();
		}

		return false;
	}

	public function clearFacility(){
		//$this->db->select_db(DB_NAME);

    	$query = "DELETE FROM ".TB_FACILITY;
    	$this->db->query($query);
	}

	public function fillFacility(){
		$this->db->select_db(DB_IMPORT);
    	$query = "INSERT INTO ".DB_NAME.".".TB_FACILITY." SELECT * FROM ".DB_IMPORT.".".TB_FACILITY;
    	$this->db->query($query);
	}




	public function getMixList($facilityId){

		$facilityId=$this->db->sqltext($facilityId);

		//$this->db->select_db(DB_NAME);

    	$query = "SELECT department.department_id department_id, department.name department_name, mix.mix_id mix_id " .
    			 "FROM mix, department " .
    			 "WHERE mix.department_id = department.department_id " .
    			 "AND department.facility_id = ". $facilityId;
    	$this->db->query($query);

    	if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$mix = array (
					'department_id'		=> $data->department_id,
					'mix_id'			=> $data->mix_id,
					'department_name' 	=> $data->department_name
				);
				$mixList[]=$mix;
			}
		}
		return $mixList;
	}




	// getCurrentUsage method optimized version. Direct SQL query.
	public function getCurrentUsageOptimized($month = 'MONTH(CURRENT_DATE)', $year = 'YEAR(CURRENT_DATE)') {

		$month=$this->db->sqltext($month);
		$year=$this->db->sqltext($year);

		//$this->db->select_db(DB_NAME);

		//new query with time dependence
		$query = "SELECT sum( m.voc ) total_usage , f.voc_limit, MONTH(m.creation_time) creation_month " .
			"FROM ".TB_FACILITY." f, ".TB_DEPARTMENT." d, ".TB_USAGE." m " .
			"WHERE f.facility_id = d.facility_id " .
			"AND m.department_id = d.department_id " .
			"AND f.facility_id = ".$this->facilityID." " .
			"AND MONTH(m.creation_time) = ".$month." " .
			"AND YEAR(m.creation_time) = ".$year." " .
			"GROUP BY MONTH(m.creation_time), voc_limit";
		$this->db->query($query);

		$numRows = $this->db->num_rows();
		$currentDate = getdate();
		if ($numRows > 0) {
			$data = $this->db->fetch(0);
			if ((int)$currentDate['mon'] == (int)$data->creation_month) {
				$this->currentUsage = (float)$data->total_usage;
			}
			return (float)$data->total_usage;

		} else
			return false;
	}




	public function getInventoryList($sort=' ORDER BY name ') {
		$inventoryList = array(Inventory::PAINT_MATERIAL => array(), Inventory::PAINT_ACCESSORY => array());

		//	facilityid property should be set
		if (!isset($this->facilityID)) {
			return false;
		}

		$query = "SELECT id FROM ".TB_INVENTORY." WHERE facility_id = ".$this->facilityID." $sort";
		$this->db->query($query);
		//	sql should not return empty result
		if ($this->db->num_rows() == 0) {
			return false;
		}

		$dataRows = $this->db->fetch_all();
		foreach ($dataRows as $dataRow) {
			$inventory = new Inventory($this->db, $dataRow->id, $loadProducts = false);
			$inventoryList[$inventory->getType()][] = $inventory;
		}

		return $inventoryList;
	}


	public function calculateSolidsMass($facilityID = null, $dateBegin = null, $dateEnd = null) {
		if ($facilityID === null) {
			$facilityID = $this->facilityID;
		}
		$department = new Department($this->db);
		$departmentList = $department->getDepartmentListByFacility($facilityID);
		$solidsMass = 0;
		$errors = array();
		foreach($departmentList as $departmentData) {
			$result = $department->calculateSolidsMass($departmentData['id'], $dateBegin, $dateEnd);
			$solidsMass +=$result['value'];
			foreach ($result['errors'] as $error) {
				if (!in_array($error,$errors)) {
					$errors []= $error;
				}
			}
		}
		return array(
				'errors' => $errors,
				'value' => $solidsMass
			);
	}



	/**
	 *
	 * calculate annual usage for whole facility
	 * @param string $year
	 */
	public function calculateAnnualUsage($year = null) {
		//	setting default year
		if ($year === null) {
			$year = date('Y');
		}

		$query = "SELECT sum( m.voc ) total_usage " .
			"FROM ".TB_DEPARTMENT." d, ".TB_USAGE." m " .
			"WHERE m.department_id = d.department_id " .
			"AND d.facility_id = ".$this->facilityID." " .
			"AND YEAR(m.creation_time) = ".$year." ";

		$this->db->query($query);

		$this->annualUsage[$year] = $this->db->fetch(0);

		return $this->annualUsage[$year];
	}



	public function getDepartmentUsageByDays(TypeChain $beginDate, TypeChain $endDate, $facilityID) {
		$query = "SELECT sum(m.voc) as voc, d.name, m.creation_time " .
				" FROM ".TB_USAGE." m, ".TB_DEPARTMENT." d " .
				" WHERE m.department_id = d.department_id AND d.facility_id = '$facilityID' " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."' " .
				" GROUP BY m.department_id, m.creation_time " .
				" ORDER BY m.department_id ";
		$this->db->query($query);
		$departmentUsageData = $this->db->fetch_all();
		$result = array();

		//get empty template for output for each equiment
		$emptyDepartmentData = array();
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput()))/$day) + 1;
		$curDay = $beginDate->formatInput();
		for($i = 0; $i< $daysCount; $i++) {
			$emptyDepartmentData []= array(strtotime($curDay)*1000, 0);
			$curDay = date('Y-m-d',strtotime($curDay.' + 1 day'));
		}

		//get all equipments list
		$query = "SELECT name FROM ".TB_DEPARTMENT .
				" WHERE facility_id = '$facilityID' " .
				" ORDER BY department_id";
		$this->db->query($query);
		$departmentList = $this->db->fetch_all();

		//format output for all equipments
		foreach($departmentList as $data) {
			$result[$data->name] = $emptyDepartmentData;
		}

		foreach ($departmentUsageData as $data) {
			$key = round(($data->creation_time - $beginDate->getTimestamp())/$day, 2); //$key == day from the begin date
			$result[$data->name][$key][1] += $data->voc;
		}

		return $result;
	}


	public function getProductUsageByDaysByFacilities(TypeChain $beginDate, TypeChain $endDate, $category, $categoryID) {

        $categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_PRODUCT." p, ".TB_MIXGROUP." mg, ".TB_DEPARTMENT." d ";

			if ((!$_SESSION['PUF']) || ($_SESSION['PUF'] == 'all')) {
				$tables .= ", ".TB_FACILITY." f ";
				$categoryDependedSql = " m.department_id = d.department_id".
                                        " AND d.facility_id = f.facility_id".
                                        " AND f.company_id = {$categoryID}  ";
			} else {
				$categoryDependedSql = " m.department_id = d.department_id ".
                                        " AND d.facility_id =".$this->db->sqltext($_SESSION['PUF']);
			}

		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name, m.creation_time " .
				" FROM {$tables} " .
				" WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'".
				" GROUP BY mg.product_id, m.creation_time " .
				" ORDER BY p.product_id ";


		$this->db->query($query);
		$productUsageData = $this->db->fetch_all();
		$result = array();

		//get empty template for output for each product
		$emptyProductData = array();
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput()))/$day) + 1;
		$curDay = $beginDate->formatInput();
		for($i = 0; $i< $daysCount; $i++) {
			$emptyProductData []= array(strtotime($curDay)*1000, 0);
			$curDay = date('Y-m-d',strtotime($curDay.' + 1 day'));
		}

		//get all used products list
		$productList = array();
		foreach($productUsageData as $data) {
			if (!in_array($data->product_nr,$productList)) {
				$productList []= $data->product_nr;
			}
		}
		//$this->setProductNR($productList);

		if (count($productList) == 0) {
			$productList []= 'products not used!';
		}

		//format output for all products
		foreach($productList as $data) {
			$result[$data] = $emptyProductData;
		}


		foreach ($productUsageData as $data) {
			//$key = round((strtotime($data->creation_time) - strtotime($beginDate->formatInput()))/$day); //$key == day from the begin date
			//$result[$data->product_nr][$key] = array(strtotime($data->creation_time)*1000, $data->sum);
			$key = round(($data->creation_time - $beginDate->getTimestamp())/$day, 2);
			//$key = intval(date("d",$key));
			$result[$data->product_nr][$key][1] += $data->sum;
		}

		return $result;
	}

	//	check difference between $vocLimit and DB value
	private function isVocLimitChanged($facilityID, $vocLimit, $annualVocLimit) {

		$facilityID = $this->db->sqltext($facilityID);
		$vocLimit = $this->db->sqltext($vocLimit);
		$annualVocLimit = $this->db->sqltext($annualVocLimit);

		$query = "SELECT voc_limit FROM ".TB_FACILITY." WHERE facility_id = ".$facilityID." AND voc_limit = '".$vocLimit."' AND voc_annual_limit = '".$annualVocLimit."'";
		$this->db->query($query);

		return ($this->db->num_rows() > 0) ? false : true;
	}

	public function getDailyEmissionsByDays(TypeChain $beginDate, TypeChain $endDate, $category, $categoryID){

		$beginstamp = $beginDate->getTimestamp();
		$endstamp = $endDate->getTimestamp();

		$categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_EQUIPMENT." eq ";
		if ($category == "company") {
			$tables .= ", ".TB_DEPARTMENT." d, ".TB_FACILITY." f ";
			$categoryDependedSql = "eq.department_id = d.department_id
										AND d.facility_id = f.facility_id
										AND f.company_id = {$categoryID} ";
		}

		$query = "SELECT sum(m.voc) as voc, f.name, m.creation_time " .
				" FROM {$tables} " .
				" WHERE {$categoryDependedSql} " .
					"AND m.equipment_id = eq.equipment_id " .
					"AND m.creation_time BETWEEN '".$beginstamp."' AND '".$endstamp."' " .
				" GROUP BY f.name, m.creation_time " .
				" ORDER BY f.name ";

		$this->db->query($query);
		$dailyEmissionsData = $this->db->fetch_all();
		$result = array();

		//get empty template for output for each facility
		$emptyData = array();
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput()))/$day) + 1;//var_dump('day_count',$daysCount);
		$curDay = $beginDate->formatInput();
		for($i = 0; $i< $daysCount; $i++) {
			$emptyData []= array(strtotime($curDay)*1000, 0);
			$curDay = date('Y-m-d',strtotime($curDay.' + 1 day'));
		}

		$query = "SELECT f.name".
				" FROM ".TB_FACILITY." f ".
				" WHERE f.company_id = {$categoryID}";

		$this->db->query($query);
		$facilityList = $this->db->fetch_all();

		foreach($facilityList as $data) {
			$result[$data->name] = $emptyData;
		}

		foreach ($dailyEmissionsData as $data) {
			$key = round(($data->creation_time - $beginDate->getTimestamp())/$day, 2); //$key == day from the begin date
			$result[$data->name][$key][1] += $data->voc;
		}

		return $result;

	}

	//	Tracking System
	private function save2trash($CRUD, $facilityID) {
		//	protect from SQL injections
		$facilityID = $this->db->sqltext($facilityID);

		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_FACILITY, $facilityID, $CRUD, $this->parentTrashRecord);

	}
	
	public function getDepartmentList($facilityId) {

		$query = "SELECT department_id FROM ".TB_DEPARTMENT." WHERE facility_id = ". $facilityId;
		$this->db->query($query);
		//	sql should not return empty result
		if ($this->db->num_rows() == 0) {
			return false;
		}

		$dataRows = $this->db->fetch_all_array();
		$departmentIDs = array();
		foreach ($dataRows as $dataRow) {
			$departmentIDs[] = $dataRow['department_id'];
		}

		return $departmentIDs;
	}
    
    /**
     * get pfp types for facility
     * 
     * @param int $facilityId
     * @return boolean|PfpTypes 
     */
    public function getPfpTypes($facilityId) {
		
		$query = "SELECT * FROM " . TB_PFP_TYPES .
				 " WHERE facility_id={$this->db->sqltext($facilityId)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$pfpTypes = array();
		foreach ($rows as $row) {
			$pfpType = new PfpTypes($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($pfpType, $key)) {
					$pfpType->$key = $value;
				}
			}
			$pfpTypes[] = $pfpType;
		}
		return $pfpTypes;
	}
    
        
    /**
     * get pfp types count for facility
     * @param int $companyId
     * @return int 
     */
    public function getPfpTypesCount($facilityId) {

        $query = "SELECT *  FROM " . TB_PFP_TYPES . "
                  WHERE facility_id = {$this->db->sqltext($facilityId)}";
		$this->db->query($query);
		return $this->db->num_rows();
	}
	
	/**
	 *
	 * @param int $facilityId
	 * @param Pagination $pagination
	 * @return boolean|\Reminder
	 */
	public function getRemindersList($facilityId, Pagination $pagination = null) {
		
		$reminders = array();
		
		$sql = "SELECT * FROM ". TB_REMINDER. "
				WHERE facility_id={$this->db->sqltext($facilityId)}"; 
	
        if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}        
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		foreach ($rows as $row) {
			$reminder = new Reminder($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($reminder, $key)) {
					$reminder->$key = $value;
				}
			}
			$reminders[] = $reminder;
		}
		return $reminders;
	}
    
	/**
	 *
	 * @param int $facilityID
	 * @return boolean 
	 */
	public function countRemindersInFacility($facilityID) {
		$sql = "SELECT count(id) remindersCount FROM ". TB_REMINDER. "
				WHERE facility_id={$this->db->sqltext($facilityID)}"; 

		$this->db->query($sql);
		if ($this->db->num_rows() > 0) {
			return (int)$this->db->fetch(0)->remindersCount;
		} else {
			return false;
		}
	}
    
	public function updateFacilityVocLimit($facilityId, $vocLimit){
		$sql = 'UPDATE '.TB_FACILITY.
		" SET voc_limit=".$this->db->sqltext($vocLimit).
		" WHERE facility_id=".$this->db->sqltext($facilityId);
		$this->db->query($sql);
	}

	public function updateFacilityNoxLimit($facilityId, $noxLimit){
		$sql = 'UPDATE '.TB_FACILITY.
		" SET monthly_nox_limit=".$this->db->sqltext($noxLimit).
		" WHERE facility_id=".$this->db->sqltext($facilityId);
		$this->db->query($sql);
	}
	
	/**
	 * @return \VWM\Apps\Process\ProcessTemplate[]
	 */
	public function getProcessList($facilityId){
		$sql = "SELECT id ".
				"FROM ".self::TB_PROCESS.
				" WHERE facility_id=".$facilityId." AND ".
				"process_type=0";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$processListId = $this->db->fetch_all();
		foreach ($processListId as $processId){
			$processList[] = new \VWM\Apps\Process\ProcessTemplate($this->db, $processId->id);
		}
		return $processList;
	}
}


?>