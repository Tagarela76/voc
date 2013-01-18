<?php

/**
 * Class for mix table in database
 */

use VWM\Framework\Model as Model;

class MixOptimized extends Model {

	public $valid = self::MIX_IS_VALID;
	public $hoverMessage;


	public $mix_id;
	public $equipment_id;
	public $department_id;
	public $description;
	public $voc;
	public $voclx;
	public $vocwx;
	private $creation_time;
	public $rule_id;
	public $exempt_rule;
	public $apmethod_id;
	public $waste_percent;
	public $recycle_percent;
	public $notes;
		
	/**
	 * Working Order iteration number. Default is 0
	 * Common use case - append as suffix to {@link description}
	 * @var int
	 */
	public $iteration = 0;

	/**
	 * Parent {@link mix_id} of current Mix.
	 * Usefull when working order iterations are in use. By default is NULL
	 * @var int
	 */
	public $parent_id;
	
	/**
	 * Time spent on mix in minutes
	 * @var int 
	 */
	public $spent_time;

	
	public $url;
	public $rule;
	public $facility_id;
	public $exprire;
	public $expire;
	public $isPfp = 0;
	public $products = null;
	public $department;
	public $facility;
	public $equipment;
	public $company;
	public $waste;
	public $recycle;
	public $waste_json;
	public $isMWS; //Is module waste stream enabled
	public $waste_calculated; //Total Waste value voc
	private $trashRecord;
	public $dateFormatForCalendar;
	public $dateFormat;
	public $debug;
	
	/**
	 * work order id
	 * @var int 
	 */
	public $wo_id;


	/**
	 * Mixes may have sub mixes, for example WO-1234 and WO-1234-01
	 * This property says does mix have such submix
	 * @var boolean
	 */
	public $hasChild = false;
	
	
	/**	 
	 * @var RepairOrder
	 */
	private $repairOrder = false;

	const MIX_IS_VALID = 'valid';
	const MIX_IS_INVALID = 'invalid';
	const MIX_IS_EXPIRED = 'expired';
	const MIX_IS_PREEXPIRED = 'preexpired';

	public function __construct($db, $mix_id = null) {
		$this->db = $db;

		if (isset($mix_id)) {
			$this->mix_id = $mix_id;
			$this->_load();
		}
	}

	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param string $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {


		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else {
			return $this->$name;
		}
	}

	/**
	 * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	 * @param string $name - name of property
	 * @param mixed $value - value to set
	 */
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/*
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
		 * it will craete dynamic property, like usually does PHP
		 */
		else if (!property_exists($this, $name)) {
			$this->$name = $value;
		}
		/*
		 * property exists and private or protected, do not touch. Keep OOP
		 */
		else {
			//Do nothing
		}
	}

	private function get_creation_time() {
		if (!isset($this->dateFormat) && isset($this->department_id)) {
			$this->iniDateFormat();
		} else if (!isset($this->dateFormat) and !isset($this->department_id)) {
			throw new Exception("Date format does not exists! And department_id is not set!");
		}

		$date = date($this->dateFormat, $this->creation_time);
		return $date;
	}

	private function set_creation_time($value) {
		if (!isset($this->dateFormat)) {
			$this->iniDateFormat();
			if (!isset($this->dateFormat)) {
				return;
			}
		} else if (!isset($value)) {
			throw new Exception("\$value is not set!");
		}

		/*
		 * If value is already timestamp  - just set value
		 */
		if (strlen($value) == 10 and is_numeric($value)) {
			$this->creation_time = $value;
		} else {

			$date = DateTime::createFromFormat($this->dateFormat, $value);
			if (!$date) {
				return;
			}
			$timestamp = $date->getTimestamp();
			$this->creation_time = $timestamp;
		}
	}

	public function setDepartment(Department $department) {
		$this->department = $department;
	}

	public function setFacility(Facility $facility) {
		$this->facility = $facility;
	}

	public function setEquipment(Equipment $equipment) {
		$this->equipment = $equipment;
	}

	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;
	}
	
	public function setWoId($wo_id) {
		$this->wo_id = $wo_id;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	

	public function getCompany() {
		if (!isset($this->company)) {
			$this->loadCompany();
		}
		return $this->company;
	}

	public function getDepartment() {
		if (!isset($this->department)) {
			$this->loadDepartment();
		}
		return $this->department;
	}
	
	public function getWoId() {
		return $this->wo_id;
	}
	
	public function getDescription() {
		return $this->description;
	}

	public function getFacility() {
		if (!isset($this->facility)) {
			$this->loadFacility();
		}
		return $this->facility;
	}

	public function getFacilityIdbyDepartment() {

	}

	public function getEquipment() {
		if (!isset($this->equipment)) {
			$this->loadEquipment();
			return $this->equipment;
		} else {
			return $this->equipment;
		}
	}

	public function getWaste() {
		if (!isset($this->waste)) {
			$this->iniWaste();
		}
		return $this->waste;
	}

	public function getCreationTime() {
		return date($this->dateFormat, strtotime($this->creation_time));
	}

	public function getRule() {
		if (isset($this->rule_id)) {
			$this->db->query("select * from rule where rule_id = " . $this->rule_id);
			$a = $this->db->fetch_array(0);
			$this->rule = $a;
		}
	}

	public function getDepartmentId() {
		return $this->department_id;
	}

	public function setDepartmentId($department_id) {
		$this->department_id = $department_id;
	}

		/**
	 * Add or Edit this mix
	 *
	 */
	public function save($isMWS = false, $mix = null) {

		//check mix products for duplication
		if($this->doesProductsHaveDuplications()) {			
			return false;
		}

		if (!isset($this->mix_id)) {
			$mixID = $this->addNewMix();
		} else {
			$mixID = $this->updateMix($isMWS, $mix);
		}

		if(!$mixID) {
			//	failed to save mix
			return false;
		}
		//	save waste data (If module 'Waste Stream' is disabled)
		if (!$isMWS and isset($this->waste) and ($this->waste->value) and $this->waste->value != "" and $this->waste->value != "0.00") {
			$this->saveWaste($mixID, $this->waste->value, $this->waste->unittype);
		}

		//	save recycle data
		if (isset($this->recycle)) {
			$this->saveRecycle($mixID, $this->recycle->value, $this->recycle->unittype);
		}

		return $mixID;
	}


	/**
	 * Update mix
	 * @param boolean $isMWS
	 * @param type $mix - not in use???
	 * @return int|boolean mix id or false on failure
	 */
	private function updateMix($isMWS, $mix) { 
		$this->db->beginTransaction();

		//	save to trash_bin
		$this->save2trash('U', $this->mix_id);

		$department = $this->getDepartment();

		if (!isset($this->facility_id)) {
			$this->facility_id = $department->getFacilityID();
			unset($this->facility);
		}

		$facility = $this->getFacility();
		/* if company use module ReductionScheme we should check and correct solvent outputs */
		$user = new User($this->db);
		if ($user->checkAccess('reduction', $facility->getCompanyID())) {
			$this->correctSolventOutputInReductionScheme();
		}

		//TODO:	save waste data first (If module Waste Stream switched off)?
		if (!$isMWS) {
		}

		$updateMixQuery = $this->getUpdateMixQuery();
		$deleteProductsQuery = $this->getDeleteProductsQuery();

		if ($this->products && is_array($this->products) && count($this->products) > 0) {			
			$insertProductsQuery = $this->getInsertProductsQuery($this->mix_id);			
		} else {
			// we can update mix without do it if this mix is work order
			if (!isset($this->wo_id) || $this->iteration != 0) { 
				//	no sense t save mix without products
				$this->db->rollbackTransaction();
				return false;
			}
		}		

		if(!$this->db->query($updateMixQuery)) {
			$this->db->rollbackTransaction();
			return false;
		}
		if (!is_null($deleteProductsQuery)) {
			if(!$this->db->query($deleteProductsQuery)) {
				$this->db->rollbackTransaction();
				return false;
			}
		}
		  
		if (!is_null($insertProductsQuery)) {
			if(!$this->db->query($insertProductsQuery)) {
				$this->db->rollbackTransaction();
				return false;
			}
		}
		

		$this->db->commitTransaction();
		return $this->mix_id;
	}

	/**
	 * Build SQL query for delete mix products
	 * @return string
	 */
	private function getDeleteProductsQuery() {
		$query = "DELETE FROM " . TB_MIXGROUP . " WHERE mix_id = " . $this->db->sqltext($this->mix_id);
		return $query;
	}


	/**
	 * Build SQL query for mix update
	 * @return string
	 */
	private function getUpdateMixQuery() {
		$spentTime = (!empty($this->spent_time)) 
				? $this->db->sqltext($this->spent_time) 
				: "NULL";
		
		$query = "UPDATE " . TB_USAGE . " SET ";
		$query .= "equipment_id={$this->db->sqltext($this->equipment_id)}, ";
		$query .= "apmethod_id=" . ((empty($this->apmethod_id)) ? "NULL" : "{$this->db->sqltext($this->apmethod_id)}") . ", ";
		$query .= "voc={$this->db->sqltext($this->voc)}, ";
		$query .= "voclx={$this->db->sqltext($this->voclx)}, ";
		$query .= "vocwx={$this->db->sqltext($this->vocwx)}, ";
		$query .= "waste_percent=" . ((empty($this->waste_percent)) ? "NULL" : "{$this->db->sqltext($this->waste_percent)}") . ", ";
		$query .= "recycle_percent=" . ((empty($this->recycle_percent)) ? "NULL" : "{$this->db->sqltext($this->recycle_percent)}") . ", ";
		$query .= "description='{$this->db->sqltext($this->description)}', ";
		$query .= "rule_id={$this->db->sqltext($this->rule_id)}, ";
		$query .= "exempt_rule = ". ((empty($this->exempt_rule)) ? "NULL" : "'{$this->db->sqltext($this->exempt_rule)}'") . ", ";
		$query .= "notes = ". ((empty($this->notes)) ? "NULL" : "'{$this->db->sqltext($this->notes)}'") . ", ";
		$query .= "creation_time = {$this->db->sqltext($this->creation_time)}, ";
		$query .= "spent_time = {$spentTime}, ";
		$query .= "iteration = {$this->db->sqltext($this->iteration)}, ";
		$query .= "parent_id = " . ((empty($this->parent_id)) ? "NULL" : $this->db->sqltext($this->parent_id)) . ", ";
		$query .= "last_update_time = NOW() ";
		$query .= " WHERE mix_id ={$this->db->sqltext($this->mix_id)}";
		return $query;
	}


	/**
	 * Adds new mix to databse
	 * @return int|boolean $mixID on success, false on failure
	 */
	private function addNewMix() {
		$this->db->beginTransaction();

		$department = $this->getDepartment();

		if (!isset($this->facility_id)) {
			$this->facility_id = $department->getFacilityID();
			unset($this->facility);
		}

		$facility = $this->getFacility();
		$user = new User($this->db);

		/* if company use module ReductionScheme we should check and correct solvent outputs */
		if ($user->checkAccess('reduction', $facility->getCompanyID())) {
			$this->correctSolventOutputInReductionScheme();
		}

		$insertQuery = $this->getInsertMixQuery();
		if(!$this->db->query($insertQuery)) {
			$this->db->rollbackTransaction();
			return false;
		}
		$mixID = $this->db->getLastInsertedID();

		//	now we are saving mix products
		// we can save mix without do it if this mix is work order
		if (!isset($this->wo_id) || $this->iteration != 0) { 
			if ($this->products && is_array($this->products) && count($this->products) > 0) {
				$insertProductsQuery = $this->getInsertProductsQuery($mixID);
				if (!$this->db->query($insertProductsQuery)) {
					$this->db->rollbackTransaction();
					return false;
				}
			} else {
				//	we should not save mix without products
				$this->db->rollbackTransaction();
				return false;
			}
		}
		
		$this->db->commitTransaction();

		$this->mix_id = $mixID;
		//	save to trash_bin
		$this->save2trash('C', $this->mix_id);
		return $mixID;
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

	private function saveRecycle($mixID, $value, $unittype) {
		//	form & escape input data
		$recycleData = $this->formAndEscapeRecycleData($mixID, $value, $unittype);

		//	if recycle already saved at DB - update, else - insert
		if ($this->checkIfRecycleExist($recycleData['mixID'])) {
			$this->updateRecycle($recycleData);
		} else {
			$this->insertRecycle($recycleData);
		}
	}

	private function updateRecycle($recycleData) {

		//screening of quotation marks
		foreach ($recycleData as $key => $value) {
			$recycleData[$key] = mysql_escape_string($value);
		}

		$query = "UPDATE recycle SET " .
				"method = '" . $recycleData['method'] . "', " .
				"unittype_id = " . $recycleData['unittypeID'] . ", " .
				"value = '" . $recycleData['value'] . "' " .
				"WHERE mix_id = " . $recycleData['mixID'];
		$this->db->query($query);
	}

	private function updateWaste($wasteData) {

		//screening of quotation marks
		foreach ($wasteData as $key => $value) {
			$wasteData[$key] = mysql_escape_string($value);
		}

		$query = "UPDATE waste SET " .
				"method = '" . $wasteData['method'] . "', " .
				"unittype_id = " . $wasteData['unittypeID'] . ", " .
				"value = '" . $wasteData['value'] . "' " .
				"WHERE mix_id = " . $wasteData['mixID'];
		$this->db->query($query);
	}

	private function insertWaste($wasteData) {

		//screening of quotation marks
		foreach ($wasteData as $key => $value) {
			$wasteData[$key] = mysql_escape_string($value);
		}

		$query = "INSERT INTO waste (mix_id, method, unittype_id, value) VALUES (" .
				"" . $wasteData['mixID'] . ", " .
				"'" . $wasteData['method'] . "', " .
				"" . $wasteData['unittypeID'] . ", " .
				"'" . $wasteData['value'] . "' )";
		$this->db->query($query);
	}

	private function insertRecycle($recycleData) {

		//screening of quotation marks
		foreach ($recycleData as $key => $value) {
			$recycleData[$key] = mysql_escape_string($value);
		}

		$query = "INSERT INTO recycle (mix_id, method, unittype_id, value) VALUES (" .
				"" . $recycleData['mixID'] . ", " .
				"'" . $recycleData['method'] . "', " .
				"" . $recycleData['unittypeID'] . ", " .
				"'" . $recycleData['value'] . "' )";
		$this->db->query($query);
	}

	private function formAndEscapeWasteData($mixID, $value, $unittype) {
		$wasteData = array(
			'mixID' => $this->db->sqltext($mixID),
			'method' => (!$unittype) ? 'percent' : 'weight', //	method is "percent" or "weight" only.  Weight means that waste set in some unittype.
			'unittypeID' => (!$unittype) ? 'null' : $this->db->sqltext($unittype), //	if method is "percent" then save unittype as null to DB
			'value' => $this->db->sqltext($value)
		);
		return $wasteData;
	}

	private function formAndEscapeRecycleData($mixID, $value, $unittype = null) {
		$recycleData = array(
			'mixID' => $this->db->sqltext($mixID),
			'method' => (!$unittype) ? 'percent' : 'weight', //	method is "percent" or "weight" only.  Weight means that waste set in some unittype.
			'unittypeID' => (!$unittype) ? 'null' : $this->db->sqltext($unittype), //	if method is "percent" then save unittype as null to DB
			'value' => $this->db->sqltext($value)
		);
		return $recycleData;
	}

	private function checkIfWasteExist($mixID) {
		settype($mixID, "integer");

		$query = "SELECT id FROM waste WHERE mix_id = " . $mixID;
		$this->db->query($query);
		return ($this->db->num_rows()) ? true : false;
	}

	private function checkIfRecycleExist($mixID) {
		settype($mixID, "integer");

		$query = "SELECT id FROM recycle WHERE mix_id = " . $mixID;
		$this->db->query($query);
		return ($this->db->num_rows()) ? true : false;
	}

	/**
	 * Get raw sql for adding products to mix
	 * @param int $mixID
	 * @return string|boolean SQL string to add products to mix or false on failure
	 */
	private function getInsertProductsQuery($mixID) {
		if (!count($this->products)) {
			return false;
		}

		$unitTypeConverter = new UnitTypeConverter('lb');
		$unittype = new Unittype($this->db);

		//	we'll put SQL for each product here
		$queryProducts = array();
		foreach ($this->products as $product) {			
			$type = $unittype->isWeightOrVolume($product->unittypeDetails['unittype_id']);
			if ($type == 'weight') {
				/* Get value by weight */
				$value = $this->db->sqltext($unitTypeConverter->convertToDefault(
						$product->quantity, $product->unittypeDetails['description']));
			} else {
				/* Get value by density */
				$densityObj = new Density($this->db, $product->density_unit_id);
				$densityType = array(
					'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
					'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				);
				$value = $unitTypeConverter->convertToDefault($product->quantity, $product->unittypeDetails['description'], $product->density, $densityType);
				if (empty($product->density) || $product->density == '0.00') {
					$value = 'NULL';
				} else {
					$value = "'" . $this->db->sqltext($value) . "'";
				}
			}
			
			$ratio = (isset($product->ratio_to_save)) ? $this->db->sqltext($product->ratio_to_save) : 'NULL';
			$queryProducts[] = " ({$this->db->sqltext($mixID)}, " .
								"{$this->db->sqltext($product->product_id)}, " .
								"{$this->db->sqltext($product->quantity)}, " .
								"{$this->db->sqltext($product->unittypeDetails['unittype_id'])}, " .
								"{$value}, " .
								"{$product->is_primary}, " .
								"{$ratio}) ";
		}

		$query = "INSERT INTO " . TB_MIXGROUP . " (mix_id, product_id, quantity, unit_type, quantity_lbs, is_primary, ratio) VALUES ";
		$query .= implode(' , ', $queryProducts);
		return $query;
	}

	/**
	 * Create query for INSERT Mix (Usage)
	 *
	 */
	private function getInsertMixQuery() {

		/* Prepare data to insert into mysql */
		$this->equipment_id = isset($this->equipment_id) ? $this->equipment_id : "0";
		$this->department_id = isset($this->department_id) ? $this->department_id : "0";
		$this->voc = isset($this->voc) ? $this->voc : "0.00";
		$this->voclx = isset($this->voclx) ? $this->voclx : "0.00";
		$this->vocwx = isset($this->vocwx) ? $this->vocwx : "0.00";
		$this->rule_id = isset($this->rule_id) ? $this->rule_id : "0";

		$creation_time = isset($this->creation_time) 
				? $this->db->sqltext($this->creation_time) 
				: time();
		
		$spentTime = (!empty($this->spent_time)) 
				? $this->db->sqltext($this->spent_time) 
				: "NULL";

		$apmethod_id = isset($this->apmethod_id) 
				? "{$this->db->sqltext($this->apmethod_id)}" 
				: "NULL";
		$exempt_rule = !empty($this->exempt_rule) 
				? "'{$this->db->sqltext($this->exempt_rule)}'" 
				: "NULL";
		$waste_percent = isset($this->waste_percent) 
				? "{$this->db->sqltext($this->waste_percent)}" 
				: "NULL";
		$recycle_percent = isset($this->recycle_percent) 
				? "{$this->db->sqltext($this->recycle_percent)}" 
				: "NULL";
		$notes = !empty($this->notes) 
				? "'{$this->db->sqltext($this->notes)}'" 
				: "NULL";
		$parentID = ($this->parent_id !== null) 
				? $this->db->sqltext($this->parent_id) 
				: "NULL";
		$repairOrderId = ($this->wo_id !== null) 
				? $this->db->sqltext($this->wo_id) 
				: "NULL";

		$query = "INSERT INTO " . TB_USAGE . " (equipment_id, department_id, " .
					"description, voc, voclx, vocwx, creation_time, spent_time, " .
					"rule_id, apmethod_id, exempt_rule, notes, waste_percent, " .
					"recycle_percent, iteration, parent_id, last_update_time, wo_id ) VALUES (" .
						"{$this->db->sqltext($this->equipment_id)}, " .
						"{$this->db->sqltext($this->department_id)}, " .
						"'{$this->db->sqltext($this->description)}', " .
						"{$this->db->sqltext($this->voc)}, " .
						"{$this->db->sqltext($this->voclx)}, " .
						"{$this->db->sqltext($this->vocwx)}, " .
						"{$creation_time}, " .
						"{$spentTime}, " .
						"{$this->db->sqltext($this->rule_id)}, " .
						"{$apmethod_id}, " .
						"{$exempt_rule}, " .
						"{$notes}, " .
						"{$waste_percent}, " .
						"{$recycle_percent}, " .
						"{$this->db->sqltext($this->iteration)}, " .
						"{$parentID}, " .
						" NOW(), " .
						" {$repairOrderId} " .		
						") "; 

		return $query;
	}

	private function correctSolventOutputInReductionScheme() {
		//if company use module ReductionScheme we should check and correct solvent outputs

		$facility = $this->getFacility();

		if ($this->creation_time) {

			$currentTimeStamp = strtotime("now");
			$creationTimeStamp = strtotime($this->creation_time);

			if ($creationTimeStamp < $currentTimeStamp) {

				$ms = new ModuleSystem($this->db);
				$moduleMap = $ms->getModulesMap();
				$mReduction = new $moduleMap['reduction'];
				$params = array(
					'db' => $this->db,
					'facilityID' => $facility->getFacilityID(),
					'month' => date("mm", $currentTimeStamp),
					'year' => date("yyyy", $currentTimeStamp),
					'oldVOC' => 0,
					'newVOC' => $this->voc
				);
				$mReduction->prepareMixSave($params);
			}
		}
	}

	private function _load() {
		if (!isset($this->mix_id)) {
			return false;
		}

		$query = "SELECT m.*, d.facility_id " .
				"FROM " . TB_USAGE . " m " .
				"JOIN " . TB_DEPARTMENT. " d ON  d.department_id = m.department_id " .
				"WHERE mix_id = {$this->db->sqltext($this->mix_id)}";
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}

		$mixData = $this->db->fetch(0);

		foreach ($mixData as $property => $value) {
			if (property_exists($this, $property)) {
				$this->$property = $mixData->$property;
			}
		}

		$this->iniDateFormat();

		//TODO: move this to Equipment.class.php
		$query = "SELECT expire FROM " . TB_EQUIPMENT . " WHERE equipment_id=" . $this->equipment_id;
		$this->db->query($query);
		$exp = $this->db->fetch(0)->expire;
		$DateType = new DateTypeConverter($this->db);
		$this->expire = date($DateType->getDatetypebyID($this->equipment_id), $exp);

		$this->getProducts();
	}

	/**
	 * Mix products getter
	 * @return array
	 */
	public function getProducts() {
		if(!is_array($this->products) && count($this->products) == 0) {
			$this->loadProducts();
		}

		return $this->products;
	}

	private function iniDateFormat($departmentID = null) {

		$dID = $departmentID ? $departmentID : $this->department_id;

		if (!$dID or $dID == NULL or !isset($dID)) {
			throw new Exception("Cannot get date format for mix, because deparment id is not set!");
		}

		$chain = new TypeChain(null, 'Date', $this->db, $dID, 'department');
		$this->dateFormatForCalendar = $chain->getFromTypeController('getFormatForCalendar');

		$this->dateFormat = $chain->getFromTypeController('getFormat');
	}

	private function loadCompany() {

		if (!isset($this->department)) {
			$this->loadDepartment();
		}

		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->department_id);
		$ccompany = $company->getCompanyDetails($companyID);

		foreach ($ccompany as $key => $value) {
			$this->company->$key = $value;
		}
	}

	private function loadDepartment() {

		if (!isset($this->department_id)) {
			//throw new Exception("No department id");
			//return;
		}
		$department = new Department($this->db);

		$department->initializeByID($this->department_id);
		$this->department = $department;
	}

	private function loadFacility() {
		$facility = new Facility($this->db);
		$facility->initializeByID($this->facility_id);
		$this->facility = $facility;
	}

	private function loadEquipment() {
		$equipment = new Equipment($this->db);
		$equipment->initializeByID($this->equipment_id);

		$this->expire = $equipment->expire;
		$this->equipment = $equipment;
	}


	/**
	 * Load mix products from database
	 * @return bool|null
	 */
	public function loadProducts() {
		if (!isset($this->mix_id)) {
			return false;
		}

		$this->products = array();
		$query = "SELECT mg.*, sup.*, p.product_id, p.product_nr, p.name, p.paint_chemical, coat.coat_desc as coatDesc " .
					"FROM " . TB_MIXGROUP . " mg " .
					"JOIN " . TB_PRODUCT . " p ON p.product_id = mg.product_id " .
					"JOIN " . TB_SUPPLIER . " sup ON p.supplier_id = sup.supplier_id " .
					"JOIN " . TB_COAT . " coat ON coat.coat_id = coating_id " .
					"WHERE mg.mix_id = {$this->db->sqltext($this->mix_id)}";

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}

		$productsData = $this->db->fetch_all();

		$unittype = new Unittype($this->db);
		foreach ($productsData as $productData) {
			$mixProduct = new MixProduct($this->db);
			foreach ($productData as $property => $value) {
				if (property_exists($mixProduct, $property)) {
					$mixProduct->$property = $productData->$property;
				}
			}
			//	TODO: add userfriendly records to product properties
			$mixProduct->initializeByID($mixProduct->product_id);

			//	if there is a primary product then this is an pfp-based mix
			if ($mixProduct->is_primary) {
				$this->isPfp = true;
			}

			if ($productData->ratio) {
				$mixProduct->ratio_to_save = $productData->ratio;
			}

			$mixProduct->unittypeDetails = $unittype->getUnittypeDetails($mixProduct->unit_type);
			$unittypeClass = $unittype->getUnittypeClass($mixProduct->unit_type);

			$mixProduct->unittypeDetails['unittypeClass'] = $unittypeClass;
			$mixProduct->initUnittypeList($unittype);

			$mixProduct->json = json_encode($mixProduct);

			//	push to mix products
			array_push($this->products, $mixProduct);
		}

		return $this->products;
	}

	public function isAlreadyExist() {
		return (!$this->mix_id) ? false : true;
	}

	public function iniRecycle($isMWS, $unittypeListDefault = false) {
		$unittype = new Unittype($this->db);

		$recycleFromDB = $this->selectRecycle($isMWS);

		if (!$recycleFromDB) {
			//	default values
			if (!$unittypeListDefault) { //if unittypeList is empty than set default
				$this->recycle = array(
					'mixID' => $mixID,
					'value' => "0.00",
					'unittypeClass' => 'USAWght',
					'unitTypeList' => $unittype->getUnittypeListDefault()
				);
			} else { //else get unittypelist
				$this->recycle = array(
					'mixID' => $mixID,
					'value' => "0.00",
					'unittypeClass' => 'USAWght',
					'unitTypeList' => $unittypeListDefault
				);
			}
		} else {
			foreach ($recycleFromDB as $r) {
				if (!isset($r->pollution_id)) {
					$recycle['id'] = $r->id;
					$recycle['value'] = $r->value;
				} else {

				}

				$recycle['id'] = $r->id;
				$recycle['mixID'] = $r->mix_id;
				$recycle['value'] = $r->value;
				$recycle['unitttypeID'] = $r->unittype_id;
				$recycle['unittypeClass'] = ($r->method == 'percent') ? "%" : $unittype->getUnittypeClass($r->unittype_id);
				$recycle['storage_id'] = $r->storage_id;
				$recycle['unitTypeList'] = (is_null($r->unittype_id)) ? false : $unittype->getUnittypeListDefault($r->method);

				$this->recycle = $recycle;
			}
		}

		return $this->recycle;
	}

	/**
	 * Init Waste
	 * @param boolean $isMWS if Module 'Wase Streams' Enabled
	 */
	public function iniWaste($isMWS, $unittypeListDefault = false) { //Is Module Wase Streams Enabled
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter();

		$wastesFromDB = $this->selectWaste($isMWS);
		//	get mix products
		if (is_null($this->products)) {
			$this->loadProducts();
		}

		if (!$wastesFromDB) {
			//	default values
			if (!$unittypeListDefault) { //if unittypeList is empty than set default
				$this->waste = array(
					'mixID' => $mixID,
					'value' => "0.00",
					'unittypeClass' => 'USAWght',
					'unitTypeList' => $unittype->getUnittypeListDefault()
				);
			} else { //else get unittypelist
				$this->waste = array(
					'mixID' => $mixID,
					'value' => "0.00",
					'unittypeClass' => 'USAWght',
					'unitTypeList' => $unittypeListDefault
				);
			}
		} else {
			foreach ($wastesFromDB as $w) {
				if (!isset($w->pollution_id)) {
					$waste['id'] = $w->id;
					$waste['value'] = $w->value;
				} else {

				}

				$waste['id'] = $w->id;
				$waste['mixID'] = $w->mix_id;
				$waste['value'] = $w->value;
				$waste['unitttypeID'] = $w->unittype_id;
				$waste['unittypeClass'] = ($w->method == 'percent') ? "%" : $unittype->getUnittypeClass($w->unittype_id);
				$waste['storage_id'] = $w->storage_id;
				$waste['unitTypeList'] = (is_null($w->unittype_id)) ? false : $unittype->getUnittypeListDefault($w->method);

				if ($isMWS) {
					$this->waste[] = $waste;
				} else {
					$this->waste = $waste;
				}
			}
		}

		//	sum total quantity
		$quantitySum = 0;
		foreach ($this->products as $product) {
			$densityObj = new Density($this->db, $product->getDensityUnitID());
			$densityType = array(
				'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
				'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
			);

			$unitTypeDetails = $unittype->getUnittypeDetails($product->unit_type);

			$quantitySum += $unitTypeConverter->convertToDefault($product->quantity, $unitTypeDetails['description'], $product->getDensity(), $densityType);
		}
		// sum total waste
		if ($isMWS) {
			$totalWasteValue = 0;
			foreach ($this->waste as $w) {
				$totalWasteValue += $w['value'];
			}
		} else {
			$totalWasteValue = $this->waste['value'];
		}
		// sum total waste
		if ($isMWS) {
			$totalWasteValue = 0;
			foreach ($this->waste as $w) {

				$totalWasteValue += $w['value'];
			}
		} else {
			$totalWasteValue = $this->waste['value'];
		}

		if ($isMWS) {
			foreach ($this->waste as $w) {
				$this->waste_calculated += $this->calculateWaste($w->unittypeID, $w->value, $quantitySum); //	waste in weight unit type same as VOC
			}
			$this->waste_json = json_encode($this->waste);
		} else {
			$this->waste_calculated += $this->calculateWaste($this->waste['unittypeID'], $w->value, $quantitySum); //	waste in weight unit type same as VOC
		}

		if ($isMWS) {
			$ws = new WasteStreams($this->db);
			$wastesFromDB = $ws->getWasteStreamsFromMix($this->mix_id);
			$this->waste = $wastesFromDB;

			$this->waste_json = json_encode($this->waste);
		}

		return $this->waste;
	}

	public function recalcAndSaveWastePersent() {
		if (!isset($this->mix_id))
			return false;
		$mixID = mysql_escape_string($this->mix_id);

		$this->calculateCurrentUsage();
		$this->waste_percent = round($this->waste_percent, 2);

		$query = "UPDATE " . TB_USAGE . " SET waste_percent='$this->waste_percent.' WHERE mix_id='" . $mixID . "'";
		$this->db->exec($query);

		return $this->waste_percent;
	}

	public function calculateCurrentUsage() {
		if ($this->products === null)
			return false;

		if (!isset($this->waste)) {
			//	TODO:
			$this->iniWaste($isMWS);
		}

		$errors = array(
			'isDensityToVolumeError' => false,
			'isDensityToWeightError' => false,
			'isWasteCalculatedError' => false,
			'isWastePercentAbove100' => false
		);

		$isThereProductWithoutDensity = false;
		$company = new Company($this->db);
		$unittype = new Unittype($this->db);

		$wasteUnitDetails = $unittype->getUnittypeDetails($this->waste['unittypeID']);
		$companyDetails = $company->getCompanyDetails($this->facility->getCompanyID());

		//	default unit type = company's voc unit
		$defaultType = $unittype->getDescriptionByID($companyDetails['voc_unittype_id']);

		$unitTypeConverter = new UnitTypeConverter($defaultType);
		$quantitiWeightSum = 0;
		$quantitiVolumeSum = 0;

		foreach ($this->products as $key => $product) {
			$errors['isVocwxOrPercentWarning'][$key] = 'false';

			$densityObj = new Density($this->db, $product->density_unit_id);

			//	check density
			if (empty($product->density) || $product->density == '0.00') {
				$product->density = false;
				$isThereProductWithoutDensity = true;
			}

			// get Density Type
			$densityType = array(
				'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
				'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
			);

			$quantitiWeightSum += $unitTypeConverter->convertFromTo($product->quantity, $product->unittypeDetails['description'], "lb", $product->density, $densityType); //	in weight
			//quantity array in gallon
			$quantitiVolumeSum += $unitTypeConverter->convertFromTo($product->quantity, $product->unittypeDetails['description'], "us gallon", $product->density, $densityType); //	in volume ;
			//check, is vocwx or percentVolatileWeight or  voclx
			$isVocwx = true;
			$isVoclx = true;
			$isPercentVolatileWeight = true;
			if (empty($product->vocwx) || $product->vocwx == '0.00') {
				$isVocwx = false;
			}
			if (empty($product->voclx) || $product->voclx == '0.00') {
				$isVoclx = false;
			}

			$percentVolatileWeight = $product->getPercentVolatileWeight();
			$percentVolatileVolume = $product->getPercentVolatileVolume();

			if (empty($percentVolatileWeight) || $percentVolatileWeight == '0.00') {
				$isPercentVolatileWeight = false;
				if (empty($percentVolatileWeight)) {
					$w = $percentVolatileWeight;
				}
			}

			if ($isPercentVolatileWeight || $isVocwx) {
				$errors['isVocwxOrPercentWarning'][$key] = false;
				switch ($unittype->isWeightOrVolume($product->unittypeDetails['unittype_id'])) {
					case 'weight':
						if ($isPercentVolatileWeight) {
							$percentAndWeight = array(
								'weight' => $unitTypeConverter->convertToDefault($product->quantity, $product->unittypeDetails['description']),
								'percent' => $product->getPercentVolatileWeight()
							);
							$ArrayWeight[] = $percentAndWeight;
						} else {
							if ($product->density) {
								$galQty = $unitTypeConverter->convertFromTo($product->quantity, $product->unittypeDetails['description'], "us gallon", $product->density, $densityType); //	in volume
								$vocwxAndVolumeAndvoclx = array(
									'volume' => $galQty,
									'vocwx' => $product->vocwx,
									'voclx' => $product->voclx
								);
								$ArrayVolume[] = $vocwxAndVolumeAndvoclx;
							} else {
								$errors['isDensityToVolumeError'] = true;
							}
						}
						break;

					case 'volume':
						if ($isVocwx) {
							$galQty = $unitTypeConverter->convertFromTo($product->quantity, $product->unittypeDetails['description'], "us gallon"); //	in volume
							$vocwxAndVolumeAndvoclx = array(
								'volume' => $galQty,
								'vocwx' => $product->vocwx,
								'voclx' => $product->voclx
							);
							$ArrayVolume[] = $vocwxAndVolumeAndvoclx;
						} else {
							if ($product->density) {
								$percentAndWeight = array(
									'weight' => $unitTypeConverter->convertToDefault($product->quantity, $product->unittypeDetails['description'], $product->density, $densityType),
									'percent' => $product->getPercentVolatileWeight()
								);
								$ArrayWeight[] = $percentAndWeight;
							} else {
								$errors['isDensityToWeightError'] = true;
							}
						}
						break;
				}
			}
		}

		$wasteResult = $this->calculateWastePercent($this->waste['unitttypeID'], $this->waste['value'], $unitTypeConverter, $quantitiWeightSum, $quantitiVolumeSum);
		$recycleResult = $this->calculateRecyclePercent($this->recycle['unitttypeID'], $this->recycle['value'], $unitTypeConverter, $quantitiWeightSum, $quantitiVolumeSum);

		$calculator = new Calculator();
		$this->voc = $calculator->calculateVocNew($ArrayVolume, $ArrayWeight, $defaultType, $wasteResult, $recycleResult);
		$this->voclx = $calculator->calculateVoclx($ArrayVolume, $ArrayWeight, $defaultType, $wasteResult, $recycleResult);
		$this->vocwx = $calculator->calculateVocwx($ArrayVolume, $ArrayWeight, $defaultType, $wasteResult, $recycleResult);

		if ($this->debug) {
			echo "<h1>Waste Percent: {}</h1>";
			var_dump($wasteResult);
			echo "<h1>recycle Percent: {}</h1>";
			var_dump($recycleResult);
			echo "<h1>waste result: </h1>";
			var_dump($wasteResult);
			echo "<h1>recycle result: </h1>";
			var_dump($recycleResult);
		}

		$this->waste_percent = $wasteResult['wastePercent'];
		$this->recycle_percent = $recycleResult['recyclePercent'];
		$this->currentUsage = $this->voc;
		$this->wastePercent = $wasteResult['wastePercent'];
		$this->recyclePercent = $recycleResult['recyclePercent'];
		$errors['isWastePercentAbove100'] = $wasteResult['isWastePercentAbove100'];
		$errors['isRecyclePercentAbove100'] = $recycleResult['isRecyclePercentAbove100'];

		return $errors;
	}

	public function getCurrentUsage() {
		if (!isset($this->voc)) {
			$this->calculateCurrentUsage();
		}
		return $this->voc;
	}

	private function selectWaste($isMWS) {
		if (!isset($this->mix_id))
			return false;
		$mixID = mysql_escape_string($this->mix_id);

		$query = "SELECT * FROM waste WHERE mix_id = " . $mixID;
		if (!$this->isMWS) { // If module MWS disabled - get one waste.
			$query .= " AND waste_stream_id IS NULL";
		}

		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			if (!$isMWS) {
		//		return $this->db->fetch(0); // If module MWS disabled - get one waste.
				return $this->db->fetch_all(); // we should return an array
			} else {
				return $this->db->fetch_all(); // Else get all wastes
			}
		} else {
			return false;
		}
	}

	// TODO: check this
	private function selectRecycle($isMWS) {
		if (!isset($this->mix_id))
			return false;
		$mixID = mysql_escape_string($this->mix_id);

		$query = "SELECT * FROM recycle WHERE mix_id = " . $mixID;
		if (!$this->isMWS) { // If module MWS disabled - get one waste.
			$query .= " AND recycle_stream_id IS NULL";
		}

		$this->db->query($query);

		if ($this->db->num_rows() > 0) {

			if (!$isMWS) {
				return $this->db->fetch(0); // If module MWS disabled - get one waste.
			} else {
				return $this->db->fetch_all(); // Else get all wastes
			}
		} else {
			return false;
		}

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
			$waste = $unitTypeConverter->convertToDefault($value, $unitTypeDetails["description"], $mixDensity);
		}
		$waste = round($waste, 2);

		return $waste;
	}

	private function calculateWastePercent($unittypeID, $value, UnitTypeConverter $unitTypeConverter, $quantityWeightSum = 0, $quantityVolumeSum = 0) {
		if ($this->debug) {
			echo "<p>" . __FUNCTION__ . "</p>";
		}

		$result = array(
			'wastePercent' => 0,
			'isWasteError' => false,
			'isWastePercentAbove100' => false
		);
		$unittype = new Unittype($this->db);
		$uid = $this->waste['unittypeID'] ? $this->waste['unittypeID'] : $this->waste['unitttypeID'];

		$wasteUnitDetails = $unittype->getUnittypeDetails($uid);

		if ($this->debug) {
			var_dump('************', $wasteUnitDetails, $uid, '$unitTypeConverter', $unitTypeConverter, '$quantityWeightSum', $quantityWeightSum, '$quantityVolumeSum', $quantityVolumeSum);
		}

		if (empty($uid)) {
			//	percent
			$result['wastePercent'] = $value;
		} else {
			switch ($unittype->isWeightOrVolume($uid)) {
				case 'volume':
					if ($this->debug) {
						echo "volume";
						echo "value: $value, description: {$wasteUnitDetails["description"]}";
					}
					$wasteVolume = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], 'us gallon');
					$result['wastePercent'] = $wasteVolume / $quantityVolumeSum * 100;
					break;

				case 'weight':
					if ($this->debug) {
						echo "weight";
						echo "value: $value, description: {$wasteUnitDetails["description"]}";
					}
					$wasteWeight = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], "lb");
					$result['wastePercent'] = $wasteWeight / $quantityWeightSum * 100;
					break;

				case false:
					$result['isWasteError'] = true;
					break;
			}
		}
		if ($this->debug) {
			var_dump('$wasteVolume', $wasteVolume, '$wasteWeight', $wasteWeight, '$wasteWeight/$quantityWeightSum*100', $wasteWeight / $quantityWeightSum * 100, '$wasteVolume/$quantityVolumeSum*100', $wasteVolume / $quantityVolumeSum * 100, $result);
		}
		if ($result['wastePercent'] > 100) {
			$result['wastePercent'] = 0;
			$result['isWastePercentAbove100'] = true;
		}

		$result['wastePercent'] = round($result['wastePercent'], 2);
		return $result;
	}

	private function calculateRecyclePercent($unittypeID, $value, UnitTypeConverter $unitTypeConverter, $quantityWeightSum = 0, $quantityVolumeSum = 0) {
		if ($this->debug) {
			echo "<p>" . __FUNCTION__ . "</p>";
			echo $this->debug;
		}
		$result = array(
			'recyclePercent' => 0,
			'isRecycleError' => false,
			'isRecyclePercentAbove100' => false
		);
		$unittype = new Unittype($this->db);
		$uid = $this->recycle['unittypeID'] ? $this->recycle['unittypeID'] : $this->recycle['unitttypeID'];

		$recycleUnitDetails = $unittype->getUnittypeDetails($uid);

		if (empty($uid)) {
			//	percent
			$result['recyclePercent'] = $value;
		} else {
			switch ($unittype->isWeightOrVolume($uid)) {
				case 'volume':
					$recycleVolume = $unitTypeConverter->convertFromTo($value, $recycleUnitDetails["description"], 'us gallon');
					$result['recyclePercent'] = $recycleVolume / $quantityVolumeSum * 100;
					break;

				case 'weight':
					$recycleWeight = $unitTypeConverter->convertFromTo($value, $recycleUnitDetails["description"], "lb");
					$result['recyclePercent'] = $recycleWeight / $quantityWeightSum * 100;
					break;

				case false:
					$result['isRecycleError'] = true;
					break;
			}
		}
		if ($this->debug) {
			echo'<h1>calculateRecyclePercent</h1>';
			var_dump($unittypeID, $value, 'UnitDetails', $recycleUnitDetails, '$quantityWeightSum', $quantityWeightSum, '$quantityVolumeSum', $quantityVolumeSum, '$recycleVolume', $recycleVolume, '$recycleWeight', $recycleWeight);
			echo'<h1>$result</h1>';
			var_dump(' $recycleVolume/$quantityVolumeSum*100', $recycleVolume / $quantityVolumeSum * 100, ' $recycleVolume/$quantityVolumeSum*100', $recycleWeight / $quantityWeightSum * 100, $result);
		}
		if ($result['recyclePercent'] > 100) {
			$result['recyclePercent'] = 0;
			$result['isRecyclePercentAbove100'] = true;
		}
		if ($this->debug) {
			var_dump($result);
		}
		$result['recyclePercent'] = round($result['recyclePercent'], 2);

		return $result;
	}


	/**
	 * Generate description for next mix. For example, mix A has description
	 * WO-1234. Child mix B should have description WO-1234-01
	 * @return boolean|string
	 */
	public function generateNextIterationDescription() {
		if(!$this->mix_id) {
			return false;
		}

		$delimeter = "-";
		$nextIteration = $this->iteration+1;

		$description = $this->description;
		if($this->iteration > 0) {
			//	3 last symbols are suffix by design
			$description = substr($description, 0, -3);
		}
		$description .= $delimeter.sprintf("%02d",$nextIteration);

		return $description;
	}

	//	Tracking System
	private function save2trash($CRUD, $id) {
		//	protect from SQL injections
		$id = mysql_escape_string($id);
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_USAGE, $id, $CRUD, $this->parentTrashRecord);
	}

	/**
	 * Check does mix has child mixes
	 * @return boolean
	 */
	public function getHasChild() {
		$sql = "SELECT * FROM " . TB_USAGE . " WHERE parent_id = " . $this->db->sqltext($this->mix_id);
		$this->db->query($sql);
		if ($this->db->num_rows() > 0) {
			$this->hasChild = true;
		} else {
			$this->hasChild = false;
		}

		return $this->hasChild;
	}


	/**
	 * Remove mix from DB
	 */
	public function delete() {
		$sql = "DELETE FROM ".TB_USAGE. " WHERE mix_id = {$this->db->sqltext($this->mix_id)}";
		$this->db->exec($sql);

		//	remove everything from cache
		$cache = VOCApp::get_instance()->getCache();
		if ($cache) {
			$cache->flush();
		}
	}


	/**
	 * Does Products Have Duplications ?
	 * @return bool true if mix have duplicated products, false if not
	 */
	public function doesProductsHaveDuplications() {
		$productIDs = array();
		foreach ($this->products as $product) {
			$productIDs[] = $product->product_id;
		}

		$uniqueProductIDs = array_unique($productIDs);
		return (count($productIDs) != count($uniqueProductIDs));
	}
	
	public function getMixPrice() {
		
		$inventoryManager = new InventoryManager($this->db);
		$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, pp .jobber_id, pp .unittype, pp .price, p.product_pricing as price_by_manufacturer, p.price_unit_type as unit_type_by_manufacturer
				FROM mix m, mixgroup mg, department d, product p
				LEFT JOIN price4product pp ON(pp.product_id=p.product_id)
				WHERE m.mix_id = {$this->db->sqltext($this->mix_id)}
				AND mg.product_id = p.product_id
				AND (pp.jobber_id != 0 OR pp.jobber_id IS NULL)
				AND d.department_id = m.department_id
				AND mg.mix_id = m.mix_id";
				
		$this->db->query($query);		
		$resultData = $this->db->fetch_all();		
		$price = 0;
		foreach ($resultData as $data) {  
			// if supllier doesn't set price we get manufacturer price
			if ( is_null($data->price) || $data->price == '0.00') {
				$data->price = $data->price_by_manufacturer;
			} 
			// if supllier doesn't set unit type we get manufacturer unit type
			if ( is_null($data->unittype) ) {
				$data->unittype = $data->unit_type_by_manufacturer;
			}
			$unittype2price = $inventoryManager->convertUnitTypeFromTo($data); 

			if ($unittype2price){
				$price += $data->quantity_lbs * ( $data->price / $unittype2price['usage'] ); // qty (always in lbsp * price for one product unit / price for one lbsp
			}
		}
		$mixPrice = number_format($price, 2, '.', '');
		
		return $mixPrice;
	}
	
	
	/**
	 * Get mix's Work Order
	 * @return boolean|RepairOrder
	 */
	public function getRepairOrder() {
		if(!$this->wo_id) {
			//	this mix does not have a Work Order at all
			return false;
		}
		
		if(!$this->repairOrder) {
			$this->repairOrder = new RepairOrder($this->db, $this->wo_id);			
		}
		
		return $this->repairOrder;
	}
	
	
	public function setRepairOrder(RepairOrder $repairOrder) {
		$this->repairOrder = $repairOrder;
	}

}