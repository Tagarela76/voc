<?php

class Equipment extends EquipmentProperties {

	/**	 
	 * @var db
	 */
	private $db;
	private $trashRecord;
	private $parentTrashRecord;

	function Equipment($db) {
		$this->db = $db;
	}


	
	/**
	 * get equipment filters List for equipment
	 * @param int equipemnt id
	 * @return array|bool array of EquipmentFilter or false on failure
	 */
	public function getEquipmentFiltersList($equipmentId) {
		
		$filters = array();
		
		$sql = "SELECT * FROM ". TB_EQUIPMENT_FILTER. "
				WHERE equipment_id={$this->db->sqltext($equipmentId)}"; 
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		foreach ($rows as $row) {
			$filter = new EquipmentFilter($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($filter, $key)) {
					$filter->$key = $value;
				}
			}
			$filters[] = $filter;
		}
		return $filters;
	}
	
	/**
	 * get equipment lighting list for equipment
	 * @param int equipemnt id
	 * @return array|bool array of EquipmentLighting or false on failure
	 */
	public function getEquipmentLightingList($equipmentId) {
		
		$lightings = array();
		
		$sql = "SELECT * FROM ". TB_EQUIPMENT_LIGHTING. "
				WHERE equipment_id={$this->db->sqltext($equipmentId)}";  
		$this->db->query($sql); 
		$rows = $this->db->fetch_all_array();
		
		if($this->db->num_rows() == 0) {
			return false;
		}
		foreach ($rows as $row) {
			$lighting = new EquipmentLighting($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($lighting, $key)) {
					$lighting->$key = $value;
				}
			}
			$lightings[] = $lighting;
		} 
		return $lightings;
	}

	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;
	}
	public function setParentTrashRecord(iTrash $trashRecord) {
		$this->parentTrashRecord = $trashRecord;
	}

	public function queryTotalCount($departmentID) {
		$query = "SELECT COUNT(*) cnt FROM ".TB_EQUIPMENT." WHERE department_id = $departmentID";
		$this->db->query($query);
		return $this->db->fetch(0)->cnt;
	}

	public function getEquipmentList($departmentID,$sort=' ORDER BY equip_desc ') {

		$departmentID=mysql_escape_string($departmentID);

		//$this->db->select_db(DB_NAME);

		$this->db->query("SELECT equipment_id,equip_desc FROM ".TB_EQUIPMENT." WHERE department_id = $departmentID  $sort");
		if ($this->db->num_rows())
		{
			return $this->db->fetch_all_array();
		}
		else
			return false;
	}

	public function deleteEquipment($equipmentID){

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);

		//	save to trash_bin
		$this->save2trash('D', $equipmentID);

		if ($this->isInUseList($equipmentID)) {
			$usage = new Mix($this->db);
			$usage->setTrashRecord(new Trash($this->db));
			$usage->setParentTrashRecord($this->trashRecord);

			$usage->deleteUsage($equipmentID, "equipment_id");
		}
		$this->db->query("DELETE FROM ".TB_EQUIPMENT." WHERE equipment_id=".$equipmentID);
	}




	public function clearEquipment(){
		//$this->db->select_db(DB_NAME);

    	$query = "DELETE FROM ".TB_EQUIPMENT;
    	$this->db->query($query);
	}

	public function fillEquipment(){
		$this->db->select_db(DB_IMPORT);
    	$query = "INSERT INTO ".DB_NAME.".".TB_EQUIPMENT." SELECT * FROM ".DB_IMPORT.".".TB_EQUIPMENT;
    	$this->db->query($query);
	}


	public function getEquipmentDetails($equipmentID, $vanilla=false){

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_EQUIPMENT." WHERE equipment_id=".$equipmentID);
		$equipmentDetails=$this->db->fetch_array(0);

		$equipmentDetails['expire'] = new TypeChain(date('Y-m-d',$equipmentDetails['expire']),'date',$this->db,$equipmentDetails['department_id'],'department');
		if (!$vanilla){
			$this->db->query("SELECT * FROM ".TB_DEPARTMENT." WHERE department_id=".$equipmentDetails['department_id']);
			$data2=$this->db->fetch(0);
			$equipmentDetails['department_id']=$data2->name;

			$this->db->query("SELECT * FROM ".TB_INVENTORY." WHERE id = ".$equipmentDetails['inventory_id']);
			$data2=$this->db->fetch(0);
			$equipmentDetails['inventory_id']=$data2->name;
		}

		return $equipmentDetails;
	}


	public function addNewEquipment($equipmentData) {

		//screening of quotation marks
		foreach ($equipmentData as $key=>$value)
		{
			$equipmentData[$key]=($key != "expire")?mysql_escape_string($value):$value;
		}


		//$this->db->select_db(DB_NAME);

		$query = "INSERT INTO ".TB_EQUIPMENT." (department_id, equip_desc, inventory_id, permit, expire, daily, dept_track, facility_track, model_number, serial_number, creater_id) VALUES (";

		$query .= "'".$this->db->sqltext($equipmentData["department_id"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["equip_desc"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["inventory_id"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["permit"])."', ";
		$query .= "'".strtotime($equipmentData["expire"]->formatInput())."', ";
		$query .= "'".$this->db->sqltext($equipmentData["daily"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["dept_track"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["facility_track"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["model_number"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["serial_number"])."', ";
		$query .= "'".$this->db->sqltext($equipmentData["creater_id"])."'";
		$query .= ")";

		$this->db->query($query);
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$equipmentID = $this->db->fetch(0)->id;

		$this->setEquipmentNR();

		//	save to trash_bin
		$this->save2trash('C', $equipmentID);
//
		return $equipmentID;
	}


	function setEquipmentNR() {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT equipment_id FROM ".TB_EQUIPMENT. " WHERE equipment_nr = 0";
		$this->db->query($query);
		$rowNum = $this->db->num_rows();
		$data = $this->db->fetch_all();
		for ($i=0; $i<$rowNum; $i++) {
			$query = "UPDATE ".TB_EQUIPMENT." SET equipment_nr = ".$data[$i]->equipment_id." WHERE equipment_id = ".$data[$i]->equipment_id;
			$this->db->query($query);
		}
	}


	public function setEquipmentDetails($equipmentData){

		//screening of quotation marks
		foreach ($equipmentData as $key=>$value)
		{
			$equipmentData[$key]=($key != 'expire')?mysql_escape_string($value):$value;
		}

		//$this->db->select_db(DB_NAME);

		//	save to trash_bin
		$this->save2trash('U', $equipmentData['equipment_id']);
//
		//	check expire date change
		$recalculteMixLimits = false;
		if ($this->isExpireDateChanged($equipmentData["equipment_id"], $equipmentData["expire"])) {
			$recalculteMixLimits = true;
		}

		//	check daily limit change
		if ($this->isDailyLimitChanged($equipmentData["equipment_id"], $equipmentData["daily"])) {
			$recalculteMixLimits = true;
		}

		$query="UPDATE ".TB_EQUIPMENT." SET ";

		$query.="department_id='".$this->db->sqltext($equipmentData["department_id"])."', ";
		$query.="equip_desc='".$this->db->sqltext($equipmentData["equip_desc"])."', ";
		$query.="inventory_id='".$this->db->sqltext($equipmentData["inventory_id"])."', ";
		$query.="permit='".$this->db->sqltext($equipmentData["permit"])."', ";
		$query.="expire='".strtotime($equipmentData["expire"]->formatInput())."', ";
		$query.="daily='".$this->db->sqltext($equipmentData["daily"])."', ";
		$query.="dept_track='".$this->db->sqltext($equipmentData["dept_track"])."', ";
		$query.="facility_track='".$this->db->sqltext($equipmentData["facility_track"])."', ";
		$query .= "model_number='". $this->db->sqltext($equipmentData["model_number"])."', ";
		$query .= "serial_number='".$this->db->sqltext($equipmentData["serial_number"])."', ";
		$query.="creater_id='".$this->db->sqltext($equipmentData["creater_id"])."'";

		$query.=" WHERE equipment_id=".$equipmentData['equipment_id'];

		$this->db->query($query);


	}

	public function deleteEquipmentOnly($equipmentID){

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_EQUIPMENT." WHERE equipment_id=".$equipmentID);
	}

	public function getCurrentUsage() {
		$this->calculateCurrentUsage();

		return $this->currentUsage;
	}





	private function getEquipmentMixes() {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE equipment_id=".$this->equipment_id;
		$this->db->query($query);

		$data = $this->db->fetch_all_array();

		foreach($data as $mixData) {
			$mix = new Mix($this->db);
			$mix->initializeByID($mixData->mix_id);
			$mixes[] = $mix;
		}
		return $mixes;
	}




	private function calculateCurrentUsage() {
		$equipmentMixes = $this->getEquipmentMixes();

		$currentUsage = 0;

		foreach($equipmentMixes as $mix) {
			$currentUsage += $mix->getCurrentUsage();
		}

		$this->currentUsage = $currentUsage;
	}

	public function initializeByID($equipmentID) {

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_EQUIPMENT." WHERE equipment_id=".$equipmentID;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$equipmentData = $this->db->fetch(0);

			$this->equipment_id = $equipmentID;


			$this->date = new Date($equipmentData->expire);

			$DateType = new DateTypeConverter($this->db);
			$this->expire = date($DateType->getDatetypebyID($this->equipment_id), $equipmentData->expire);

			$this->dailyLimit = $equipmentData->daily;

			if ($equipmentData->dept_track == "yes") {
				$this->trackTowardsDepartment = true;
			} else {
				$this->trackTowardsDepartment = false;
			}

			if ($equipmentData->facility_track == "yes") {
				$this->trackTowardsFacility = true;
			} else {
				$this->trackTowardsFacility = false;
			}


		} else {
			return false;
		}

		return true;
	}

	public function delete($equipmentID, $forceDelete = false) {

		$equipmentID=mysql_escape_string($equipmentID);

		$deleteResponse = new DeleteResponse();

		//	Check if there are some dependencies
		if (!$forceDelete) {
			//	Warn if there are some dependencies
			//	Check if there are MIXes that depends on this Equipment
			$query = "SELECT mix_id FROM ".TB_USAGE." WHERE equipment_id=".$equipmentID;
			$this->db->query($query);



			$mixes = $this->db->fetch_all_array();


			foreach ($this->db->fetch_all() as $mixID) {
				$mixID = $mixID->mix_id;

				echo "mix_id=".$mixID."<br>";
			}
		}

		//	Delete Equipment and all dependecies automaticaly
		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE equipment_id=".$equipmentID;
		$this->db->query($query);

		$mix = new Mix($this->db);

		foreach ($this->db->fetch_all() as $mixID) {
			$mixID = $mixID->mix_id;

			$mix->delete($mixID);
		}
	}

	public function isInUseList($equipmentID) {

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);

		$query = "SELECT * FROM ".TB_USAGE." WHERE equipment_id=".$equipmentID;
		$this->db->query($query);

		$usageList = $this->db->fetch_all();
		foreach($usageList as $data)
		{
			$usage=array(
		    	'id' => $data->mix_id,
		    	'desc' => $data->description
		    );
		    $usages[]=$usage;
		}


		return $usages;
	}

	public function getRuleList($equipmentID) {

		$equipmentID=mysql_escape_string($equipmentID);

		//$this->db->select_db(DB_NAME);
		$rule = new Rule($this->db);
		$rule_nr_byRegion = $rule->ruleNrMap[$rule->getRegion()];

		$query = "SELECT distinct r.$rule_nr_byRegion rule " .
				 "FROM equipment e, productgroup pg, components_group cg, rule r " .
				 "WHERE e.inventory_id = pg.inventory_id " .
				 "AND pg.product_id = cg.product_id " .
				 "AND cg.rule_id = r.rule_id " .
				 "AND e.equipment_id = ".$equipmentID;
		$this->db->query($query);

		$n=$this->db->num_rows();
		$data=$this->db->fetch_all();
		for ($i=0; $i<$n; $i++) {
			$rule = $data[$i]->rule;
			$rules_tmp[]=$rule;
		}

		$query = "SELECT distinct r.$rule_nr_byRegion rule " .
				 "FROM product p, components_group cg, rule r " .
				 "WHERE p.product_id = cg.product_id " .
				 "AND cg.rule_id = r.rule_id " .
				 "AND p.inventory_id = 0";
		$this->db->query($query);

		$n=$this->db->num_rows();
		$data=$this->db->fetch_all();
		for ($i=0; $i<$n; $i++) {
			$rule = $data[$i]->rule;
			$rules_tmp[]=$rule;
		}

		$rules_tmp = array_unique($rules_tmp);
		for ($i=0; $i < count($rules_tmp); $i++) {
			if ( !empty($rules_tmp[$i]) ){
				$rules[] = $rules_tmp[$i];
			}
		}

		return $rules;
	}




	//	Calculate equipment's daily usage
	public function getDailyUsage($day = 'today') {

		$day=mysql_escape_string($day);

		$mixCreationTime = ($day == 'today') ? date('m-d-Y') : $day;

		//$this->db->select_db(DB_NAME);

		$query = "SELECT sum(m.voc) dailyUsage " .
				"FROM ".TB_EQUIPMENT." e, ".TB_USAGE." m " .
				"WHERE m.equipment_id = e.equipment_id " .
				"AND e.equipment_id = ".$this->equipment_id." " .
				"AND m.creation_time = STR_TO_DATE('".$mixCreationTime."', '%m-%d-%Y')";
		$this->db->query($query);

		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->dailyUsage : false;
	}


	//Calculate equipment count for billing limit tracking(used only for not registered in vps customers)
	public function getEquipmentCountForCompany($companyID) {
		$query = "SELECT count(equipment_id) as count " .
				"FROM ".TB_EQUIPMENT." WHERE department_id IN " .
						"(SELECT d.department_id " .
						"FROM ".TB_DEPARTMENT." d , ".TB_FACILITY." f " .
						"WHERE f.company_id = '$companyID' " .
						"AND f.facility_id = d.facility_id ) " .
				" LIMIT 1";
		$this->db->query($query);
		return $this->db->fetch(0)->count;
	}


	public function getDailyEmissionsByDays(TypeChain $beginDate, TypeChain $endDate, $category, $categoryID) {

		$beginstamp = $beginDate->getTimestamp();
		$endstamp = $endDate->getTimestamp();

		$categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_EQUIPMENT." eq ";
		switch ($category) {
			case "company":
				$tables .= ", ".TB_DEPARTMENT." d, ".TB_FACILITY." f ";
				$categoryDependedSql = "eq.department_id = d.department_id
										AND d.facility_id = f.facility_id
										AND f.company_id = {$categoryID} ";
				break;
			case "facility":
				$tables .= ", ".TB_DEPARTMENT." d ";
				$categoryDependedSql = "eq.department_id = d.department_id AND d.facility_id = {$categoryID} ";
				break;
			case "department":
				$categoryDependedSql = "eq.department_id = {$categoryID} ";
				break;
			default :
				throw new Exception('Unknown category for DailyEmissions');
				break;
		}

		$query = "SELECT sum(m.voc) as voc, eq.equip_desc, m.creation_time " .
				" FROM {$tables} " .
				" WHERE {$categoryDependedSql} " .
					"AND m.equipment_id = eq.equipment_id " .
					"AND m.creation_time BETWEEN '".$beginstamp."' AND '".$endstamp."' " .
				" GROUP BY m.equipment_id, m.creation_time " .
				" ORDER BY m.equipment_id ";

		$this->db->query($query);
		$dailyEmissionsData = $this->db->fetch_all();
		$result = array();

		//get empty template for output for each equiment
		$emptyEquipmentData = array();
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput()))/$day) + 1;//var_dump('day_count',$daysCount);
		$curDay = $beginDate->formatInput();
		for($i = 0; $i< $daysCount; $i++) {
			$emptyEquipmentData []= array(strtotime($curDay)*1000, 0);
			$curDay = date('Y-m-d',strtotime($curDay.' + 1 day'));
		}


		$query = "SELECT eq.equip_desc".
				" FROM {$tables} " .
				" WHERE {$categoryDependedSql} ";
		$this->db->query($query);
		$equipmentList = $this->db->fetch_all();

		//format output for all equipments
		foreach($equipmentList as $data) {
			$result[$data->equip_desc] = $emptyEquipmentData;
		}


		foreach ($dailyEmissionsData as $data) {

			$key = round(($data->creation_time - $beginDate->getTimestamp())/$day, 2); //$key == day from the begin date
			//$result[$data->equip_desc][$key] = array(strtotime($data->creation_time)*1000, $data->voc);
			//$result[$data->equip_desc][$key][1] = array($data->creation_time*1000, $data->voc);
			$result[$data->equip_desc][$key][1] += $data->voc;
		}

		return $result;
	}


	//	check difference between $expireTimestamp and DB value
	private function isExpireDateChanged($equipmentID, $expireTimestamp) {

		$equipmentID=mysql_escape_string($equipmentID);
		$expireTimestamp=mysql_escape_string($expireTimestamp);

		//$this->db->select_db(DB_NAME);

		$query = "SELECT expire FROM ".TB_EQUIPMENT." WHERE equipment_id = ".$equipmentID." AND expire = ".$expireTimestamp;
		$this->db->query($query);

		return ($this->db->num_rows() > 0) ? false : true;
	}




	//	check difference between $dailyLimit and DB value
	private function isDailyLimitChanged($equipmentID, $dailyLimit) {

		$equipmentID=mysql_escape_string($equipmentID);
		$dailyLimit=mysql_escape_string($dailyLimit);

		//$this->db->select_db(DB_NAME);

		$query = "SELECT daily FROM ".TB_EQUIPMENT." WHERE equipment_id = ".$equipmentID." AND daily = ".$dailyLimit;
		$this->db->query($query);

		return ($this->db->num_rows() > 0) ? false : true;
	}




	//	Tracking System
	private function save2trash($CRUD, $equipmentID) {
		//	protect from SQL injections
		$equipmentID = mysql_escape_string($equipmentID);

		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_EQUIPMENT, $equipmentID, $CRUD, $this->parentTrashRecord);
	}

}
?>