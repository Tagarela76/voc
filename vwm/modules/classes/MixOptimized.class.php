<?php
	
	class MixOptimized {
		
		private $db;
		
		public $valid = self::MIX_IS_VALID;
		public $hoverMessage;
		
		public $mix_id;
		public $equipment_id;
		public $department_id;
		public $description;
		public $voc;
		public $voclx;
		public $vocwx;
		public $creation_time;
		public $rule_id;
		public $exempt_rule;
		public $apmethod_id;
		public $waste_percent;
		
		public $url;
		public $rule;
		public $facility_id;
		public $exprire;
		public $expire;

		
		public $products = null;
		
		public $department;
		public $facility;
		public $equipment;
		public $company;
		
		
		public $waste;
		public $waste_json;
		public $isMWS; //Is module waste stream enabled
		public $waste_calculated; //Total Waste value voc
		
		private $trashRecord;
		 
		
		const MIX_IS_VALID = 'valid';
		const MIX_IS_INVALID = 'invalid';
		const MIX_IS_EXPIRED = 'expired';
		const MIX_IS_PREEXPIRED = 'preexpired';
				
		public $debug;
		
		public function __construct($db, $mix_id = null) {
			$this->db = $db;
			if (isset($mix_id)) {
				$this->mix_id = $mix_id;	
				$this->_load();			
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
		
		public function getCompany() {
			
			if(!isset($this->company)) {
				$this->loadCompany();
			}
			return $this->company;
		}
		
		public function getDepartment() {
			
			if(!isset($this->department)) {
				$this->loadDepartment();
			}
			return $this->department;
		}
		public function getFacility() {
			
			if(!isset($this->facility)) {
				$this->loadFacility();
			}
			return $this->facility;
		}
		
		public function getFacilityIdbyDepartment() {
			
			
		}
		public function getEquipment() {
			
			if(!isset($this->equipment)) {
				$this->loadEquipment();
				return $this->equipment;	
			} else {
				return $this->equipment;
			}
		}
		
		public function getWaste() {
			
			if(!isset($this->waste)) {
				$this->iniWaste();
			}
			return $this->waste;
		}
		
		public function getCreationTime() {
			//TODO: dates
			return date('m-d-Y', strtotime($this->creation_time));
		}
		
		
		/**
		 * Add or Edit this mix
		 * 
		 */
		public function save($isMWS,$mix = null) {
			
			if(!isset($this->mix_id)) {
				$mixID = $this->addNewMix($isMWS);
			} else {
				$mixID = $this->updateMix($isMWS, $mix);
			}
			
			//	save waste data (If module 'Waste Stream' is disabled)
			//echo "<h1>Waste:</h1>";
			//var_dump($this->waste);
			
			if(!$isMWS and isset($this->waste) and ($this->waste->value) and $this->waste->value != "" and $this->vaste->value != "0.00") {
				//echo "<h1>Save single waste</h1>";
				//var_dump($mixID, $this->waste['value'], $this->waste['unittypeID']);
				$this->saveWaste($mixID, $this->waste->value, $this->waste->unittype);
				//echo "Only waste saved {$this->waste->value} {$this->waste->unittype}";
			} else {
				//echo "not MWS!!"; 
			}
			
			return $mixID;
		}
		

		private function updateMix($isMWS, $mix) {
			
			$query = 'SELECT voc, department_id, creation_time FROM '.TB_USAGE.' WHERE mix_id = '. $this->mix_id;
			
			$this->db->query($query);
			if ($this->db->num_rows() > 0) {
				$record = $this->db->fetch(0);
				$oldVoc = $record->voc;
				$departmentID = $record->department_id;
				$oldTime = $record->creation_time;
			}
			
			//	save to trash_bin
			//$this->save2trash('U', $this->mix_id);
			
			$department = $this->getDepartment();
			
			if(!isset($this->facility_id)) {
				$this->facility_id = $department->getFacilityID();
				unset($this->facility);
			}
			
			$facility = $this->getFacility();
			//echo "<h2>Facility:</h2>";
			//var_dump($facility);
			
			/** if company use module ReductionScheme we should check and correct solvent outputs */
			$user = new User($this->db);
			if($user->checkAccess('reduction', $facility->getCompanyID())) {
				$this->correctSolventOutputInReductionScheme();
			}
			
			//TODO:	save waste data first (If module Waste Stream switched off)
			if (!$isMWS) {
				//$wasteData = $usageData['waste'];
				//$this->saveWaste($wasteData['mixID'], $wasteData['value'], $wasteData['unittypeID']);																		
			}
			if($this->debug) {
				var_dump($this);
				var_dump($mix);
			}
			
			$updateMixQuery = $this->getUpdateMixQuery();
			
			if($this->debug) {
				echo "<h1>UpdateMixQuery:</h1>";
				echo "<h3>$updateMixQuery</h3>";
			}
			
			$deleteProductsQuery = $this->getDeleteProductsQuery();
			
			if($this->products and is_array($this->products) and count($this->products) > 0) {
				$insertProductsQuery = $this->getInsertProductsQuery($this->mix_id);
			
				if($this->debug) {
					echo "<h2>insertProductsQuery:</h2>";
					echo($insertProductsQuery);
				}
			}
			
			$this->db->query($updateMixQuery);
			$this->db->query($deleteProductsQuery);
			$this->db->query($insertProductsQuery);
			
			return $this->mix_id;
		}

		
		private  function getDeleteProductsQuery() {
			$query = "DELETE FROM ".TB_MIXGROUP." WHERE mix_id = ".$this->mix_id;
			return $query;
		}
		
		private function getUpdateMixQuery() {
			
			if($this->debug) {
				//echo "<h1>Edited Mix:</h1>";
				//var_Dump($editedMix);
			}
			
			$query = "UPDATE ".TB_USAGE." SET ";		
			$query .= "equipment_id='{$this->equipment_id}', ";
			$query .= "apmethod_id=".((empty($this->apmethod_id))?"NULL":"'{$this->apmethod_id}'").",";
			$query .= "voc='{$this->voc}', ";
			$query .= "voclx='{$this->voclx}', ";
			$query .= "vocwx='{$this->vocwx}', ";
			$query .= "waste_percent=".((empty($this->waste_percent))?"NULL":"'".$this->waste_percent."'").", ";
			$query .= "description='{$this->description}', ";
			$query .= "rule_id={$this->rule_id}, ";
			$query .= (empty($this->exempt_rule)) ? "exempt_rule = NULL, " : "exempt_rule ='".$this->exempt_rule."', ";			
			if (empty($this->creation_time)) {
				$query .= "creation_time = '".date('Y-m-d')."' ";
			} else {
				$query .= "creation_time = STR_TO_DATE('{$this->creation_time}', '%m-%d-%Y') ";
			}		
			$query .= " WHERE mix_id =".$this->mix_id;
			
			return $query;
		}
		
		private function addNewMix($isMWS) {
			//echo "<h1>addNewMix</h1>";
			//screening of quotation marks
			/*foreach ($this as $key=>$value) {
				switch ($key) {
					case 'waste': break; 
					case 'products': break;
					default: 
					{
						$this->$key = mysql_escape_string($value);
						break;
					}
				}
			}*/
			
			$department = $this->getDepartment();
			
			
			if(!isset($this->facility_id)) {
				$this->facility_id = $department->getFacilityID();
				unset($this->facility);
			}
			
			$facility = $this->getFacility();
			//echo "<h2>Facility:</h2>";
			//var_dump($facility);
			
			$user = new User($this->db);
			
			/** if company use module ReductionScheme we should check and correct solvent outputs */
			if($user->checkAccess('reduction', $facility->getCompanyID())) {
				$this->correctSolventOutputInReductionScheme();
			}
			
			$insertQuery = $this->getInsertMixQuery();
			//echo "<h2>insert mix query:</h2>";
			//var_dump($insertQuery);
			
			
			$this->db->query($insertQuery);
			//echo "<h1>$insertQuery</h1>";
			$mixID = $this->db->getLastInsertedID();
			
			//var_dump($this->products);
			if($this->products and is_array($this->products) and count($this->products) > 0) {
				$insertProductsQuery = $this->getInsertProductsQuery($mixID);
			
				//echo "<h2>insertProductsQuery:</h2>";
				//echo($insertProductsQuery);
				$this->db->query($insertProductsQuery);
			}
			
			
			return $mixID;
		}
		
		private function saveWaste($mixID, $value, $unittype) {
			//	form & escape input data
			$wasteData = $this->formAndEscapeWasteData($mixID, $value, $unittype);				
									
			//	if waste already saved at DB - update, else - insert
			if ($this->checkIfWasteExist($wasteData['mixID'])) {
				//echo "<h1>Update waste</h1>";
				$this->updateWaste($wasteData);
			} else {
				//echo "<h1>Insert waste</h1>";
				$this->insertWaste($wasteData);
			}
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
			//echo $query;
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
		
		private function getInsertWasteQuery($mixID) {
			
			var_dump($this->waste_json);
		}
		
		private function getInsertProductsQuery($mixID) {
			
			if(!count($this->products)) {
				return;
			}
			
			$query = "INSERT INTO ".TB_MIXGROUP." (mix_id, product_id, quantity, unit_type, quantity_lbs) VALUES ";
			
			$unitTypeConverter = new UnitTypeConverter('lb');
			$unittype = new Unittype($this->db);
			
			foreach($this->products as $product) {
				
				$type = $unittype->isWeightOrVolume($product->unittypeDetails['unittype_id']);
				//echo "<h1>$type</h1>";
				if ($type == 'weight') {
					/** Get value by weight */
					$value = $unitTypeConverter->convertToDefault($product->quantity,$product->unittypeDetails['description']);
				} else {
					/** Get value by density */
					//echo "<h1>Product #</h1>";
					$densityObj = new Density($this->db,$product->density_unit_id);
					$densityType = array (
					    'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
						'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				    );
				    $value = $unitTypeConverter->convertToDefault($product->quantity,$product->unittypeDetails['description'],$product->density,$densityType);
				    if (empty($product->density) || $product->density == '0.00') {
				    	$value = 'NULL';
				    } else {
				    	$value = "'".$value."'";
				    }
				}
			    $query .= " ($mixID, 
			    			{$product->product_id}, 
			    			{$product->quantity}, 
			    			{$product->unittypeDetails['unittype_id']}, 
			    			$value
			    			) ,";
			}
			
			$query = substr_replace($query, " ", strlen($query)-1, 1); // Remove last symbol ','
			//echo "<h1>$query</h1>";
			return $query;
		}
		
		/**
		 * Create query for INSERT Mix (Usage)
		 * 
		 */
		private function getInsertMixQuery() {
			
			/** Prepare data to insert into mysql*/
			$this->equipment_id = isset($this->equipment_id) ? $this->equipment_id : "0";
			$this->department_id = isset($this->department_id) ? $this->department_id : "0";
			$this->voc = isset($this->voc) ? $this->voc : "0.00";
			$this->voclx = isset($this->voclx) ? $this->voclx : "0.00";
			$this->vocwx = isset($this->vocwx) ? $this->vocwx : "0.00";
			$creation_time = isset($this->creation_time) ? " STR_TO_DATE('{$this->creation_time}', '%m-%d-%Y') " : "'".date('Y-m-d')."'";//Warning: quotes!
			$this->rule_id = isset($this->rule_id) ? $this->rule_id : "0";
			$this->apmethod_id = isset($this->apmethod_id) ? "'{$this->apmethod_id}'" : "NULL";//Warning: quotes!
			$this->exempt_rule = isset($this->exempt_rule) ? "'{$this->exempt_rule}'" : "NULL";//Warning: quotes!
			$this->waste_percent = isset($this->waste_percent) ? "'{$this->waste_percent}'" : "NULL";//Warning: quotes!
			
			//var_dump($this->exemptRule);
			
			$query = "INSERT INTO ".TB_USAGE." (equipment_id, department_id, description, voc, voclx, vocwx, creation_time, rule_id,apmethod_id, exempt_rule, waste_percent ) VALUES (
						'{$this->equipment_id}',
						'{$this->department_id}',
						'{$this->description}',
						'{$this->voc}',
						'{$this->voclx}',
						'{$this->vocwx}',
						 $creation_time,
						 '{$this->rule_id}',
						 {$this->apmethod_id},
						 {$this->exempt_rule},
						 {$this->waste_percent}
						 ) ";			
			/*$query .= (empty($usageData['equipment_id'])) ? " '0', " : "'".$usageData['equipment_id']."', ";
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
			$query .= ")";*/
	
			return $query;
		}
		
		private function correctSolventOutputInReductionScheme() {
			//if company use module ReductionScheme we should check and correct solvent outputs
			//$this->department = new Department($this->db);
			//$this->department->initializeByID($usageData['department_id']);
			//$user = new User($this->db);
			$facility = $this->getFacility();
			//$facilityDetails = $facility->getFacilityDetails($this->department->getFacilityID());
				
			if($this->creation_time) {
				//date in format mm-dd-yyyy
				//$mm = substr($usageData['creationTime'],0,2); TODO: индуский код детектед
				//$yyyy = substr($usageData['creationTime'],-4);
				
				$currentTimeStamp = strtotime("now");
				$creationTimeStamp = strtotime($this->creation_time);
				
				//if ($yyyy < substr(date('Y-m-d'),0,4) || $mm < substr(date('m-d-Y'),0,2) ) {
				if($creationTimeStamp < $currentTimeStamp) {
					
					$ms = new ModuleSystem($this->db);
					$moduleMap = $ms->getModulesMap();
					$mReduction = new $moduleMap['reduction'];
					$params = array(
						'db' => $this->db,
						'facilityID' => $facility->getFacilityID(),
						'month' 	=> date("mm",$currentTimeStamp),
						'year' 		=> date("yyyy",$currentTimeStamp),
						'oldVOC' => 0,
						'newVOC' => $this->voc
					);
					$mReduction->prepareMixSave($params);
				}
			}
		}
		
		
		private function _load() {
			if (!isset($this->mix_id)) return false;
			
			$mixID = mysql_escape_string($this->mix_id);
			
			$query = 'SELECT mix.*, facility_id FROM '.TB_USAGE.' , ' . TB_DEPARTMENT . '  WHERE mix_id = '.$mixID.' AND  department.department_id = mix.department_id';
			$this->db->query($query);

			if ($this->db->num_rows() == 0) return false;
			
			$mixData = $this->db->fetch(0);
			
			
			foreach ($mixData as $property =>$value) {
				if (property_exists($this,$property)) {
					$this->$property = $mixData->$property;
				}
			}
			
			$query = "SELECT expire FROM ".TB_EQUIPMENT." WHERE equipment_id=".$this->equipment_id;
		
			$this->db->query($query);
			$exp =  $this->db->fetch(0)->expire;
			$DateType = new DateTypeConverter($this->db);
			
			$this->expire = date($DateType->getDatetypebyID($this->equipment_id), $exp);
			
			$timestamp = strtotime($this->creation_time);
			//echo "<h2>Load: {$this->creation_time}</h2>";
			$this->creation_time = date("m-d-Y",$timestamp);
			//echo "<h2>Load: {$this->creation_time}</h2>";

			$this->loadProducts();
		}
		
		private function loadCompany() {
			
			if(!isset($this->department)) {
				$this->loadDepartment();
			}
			$company = new Company($this->db);
			
			$companyID = $company->getCompanyIDbyDepartmentID($this->department_id);
			
			$ccompany = $company->getCompanyDetails($companyID);
			
			foreach($ccompany as $key => $value) {
				
				$this->company->$key = $value;
			}
		}
		
		private function loadDepartment() {
			
			if(!isset($this->department_id)) {
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
		
		private function  loadEquipment() {
			
			$equipment = new Equipment($this->db);
			$equipment->initializeByID($this->equipment_id);
			
			$this->expire = $equipment->expire;
			
			$this->equipment = $equipment;
		}
		

		public function loadProducts() {
									
			if (!isset($this->mix_id)) return false;
			
			$this->products = array();
			
			$mixID = mysql_escape_string($this->mix_id);
			
			/*$query = 'SELECT * FROM '.TB_MIXGROUP.' WHERE mix_id = '.$mixID.'';
			$this->db->query($query);
			
			if ($this->db->num_rows() == 0) return false;
			
			$productsData = $this->db->fetch_all();*/
			//var_dump($productsData);
			$query = "SELECT mg.*, sup.*, p.*, coat.coat_desc as coatDesc FROM ".TB_MIXGROUP." mg, ".TB_PRODUCT." p, ". TB_SUPPLIER ." sup, " . TB_COAT . "  
							WHERE mg.mix_id=".$mixID." 
							AND mg.product_id = p.product_id 
							AND p.supplier_id = sup.supplier_id
							AND coat.coat_id = coating_id";
			
			$this->db->query($query);
			
			if ($this->db->num_rows() == 0) return false;
			
			$productsData = $this->db->fetch_all();
			//$this->waste =
			
			
			foreach ($productsData as $productData) {
				
				$mixProduct = new MixProduct($this->db,$productData->mixgroup_id);
				foreach ($productData as $property =>$value) {
					if (property_exists($mixProduct,$property)) {
						$mixProduct->$property = $productData->$property;
					}
				}
				//	TODO: add userfriendly records to product properties
				$mixProduct->initializeByID($mixProduct->product_id);
				array_push($this->products, $mixProduct);								
			}
			
			$unittype = new Unittype($this->db);
			$count = count($this->products);
			for($i = 0; $i < $count; $i++) {
				$this->products[$i]->unittypeDetails = $unittype->getUnittypeDetails($this->products[$i]->unit_type);
				$unittypeClass = $unittype->getUnittypeClass($this->products[$i]->unit_type);
				$unitTypeList = $unittype->getUnittypeListDefaultByCompanyID($this->company->company_id, $unittypeClass);
				
				$this->products[$i]->unittypeDetails['unittypeClass'] = $unittypeClass;
				$this->products[$i]->initUnittypeList($unittype);
				//$this->products[$i]->unittypeDetails['unitTypeList'] = $unitTypeList;
			}
			
			for($i = 0; $i < $count; $i++) {
				$this->products[$i]->json = json_encode($this->products[$i]);
				
				//echo "<p>Load unittypeList</p>";
				//echo $mixProduct->unittypeDetails['unittypeClass'];
				
				//var_dump($mixProduct->unitTypeList);
			}
			
			return $this->products;
		}
		
		
		
		public function isAlreadyExist() {
			return (!$this->mix_id) ? false : true; 
		}
		
		
		/**
		 * <h1>Init Waste</h1>
		 * 
		 * @param boolean $isMWS if Module 'Wase Streams' Enabled
		 */
		public function iniWaste($isMWS) { //Is Module Wase Streams Enabled
			//if (!isset($this->mix_id)) return false;
			
			//$isMWS = $this->isMWS;
			
			$unittype = new Unittype($this->db);
			$unitTypeConverter = new UnitTypeConverter();
			
			//$wastesFromDB = $this->selectWaste($isMWS);
			/*if($isMWS) {
				$ws = new WasteStreams($this->db);
				$wastesFromDB = $ws->getWasteStreamsFromMix($this->mix_id);
			} else {
				$wastesFromDB = $this->selectWaste($isMWS);
			}*/
			
			$wastesFromDB = $this->selectWaste($isMWS);
			//echo "<h1>Waste from DB</h1>";
			//var_dump($wastesFromDB);
			
			//	get mix products
			if (is_null($this->products)) {
				$this->loadProducts();
			}
			
			//$this->isMWS = $isMWS;
					

			if (!$wastesFromDB) {
				//	default values
				$this->waste = array (
					'mixID'			=> $mixID,
					'value'			=> "0.00",
					'unittypeClass'	=> 'percent'								
				);
			} else {				
				foreach($wastesFromDB as $w) {
						
					/*$waste = array();
					$waste['id']	= $w->id;
					$waste['mixID'] = $w->mix_id;
					$waste['value'] = $w->value;
					$waste['unitttypeID'] 	= $w->unittype_id;
					$waste['unittypeClass'] = ($w->method == 'percent') ? "%" : $unittype->getUnittypeClass($w->unittype_id);
					$waste['storage_id'] 	= $w->storage_id;
					$waste['unitTypeList'] = (is_null($w->unittype_id)) ? false : $unittype->getUnittypeListDefault($w->method); */
					
					if(!isset($w->pollution_id)) {
						$waste['id']	= $w->id;
						$waste['value'] = $w->value;
						
					} else {
						
					}
					
					$waste['id']	= $w->id;
					$waste['mixID'] = $w->mix_id;
					$waste['value'] = $w->value;
					$waste['unitttypeID'] 	= $w->unittype_id;
					$waste['unittypeClass'] = ($w->method == 'percent') ? "%" : $unittype->getUnittypeClass($w->unittype_id);
					$waste['storage_id'] 	= $w->storage_id;
					$waste['unitTypeList'] = (is_null($w->unittype_id)) ? false : $unittype->getUnittypeListDefault($w->method);
					
					if($isMWS) {
						//echo "<h1>Add waste</h1>";
						$this->waste[] = $waste;
						
						//echo "<h1>Waste:</h1>";
						//var_dump($this->waste[count($this->waste)-1]);
					} else {
						$this->waste = $waste;
					}
					
					
				}	

				
				
				//exit;	
			}
			
			//echo "<h1>Completed Waste:</h1>";
			//var_dump($this->waste);
			
			//echo "<h1>Waste 222</h1>";
			//var_dump($this);

			//	sum total quantity
			$quantitySum = 0;
			foreach ($this->products as $product) {				
				$densityObj = new Density($this->db, $product->getDensityUnitID());
				$densityType = array (
					'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
					'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				);
				
				$unitTypeDetails = $unittype->getUnittypeDetails($product->unit_type);
				
				$quantitySum += $unitTypeConverter->convertToDefault($product->quantity, $unitTypeDetails['description'], $product->getDensity(), $densityType);
			}			
			// sum total waste
			if($isMWS) {
				$totalWasteValue = 0;
				foreach ($this->waste as $w) {					
					$totalWasteValue += $w['value'];
				}
			} else {
				$totalWasteValue = $this->waste['value'];
			}
			// sum total waste
			if($isMWS) {
				$totalWasteValue = 0;
				foreach ($this->waste as $w) {
					
					$totalWasteValue += $w['value'];
				}
			} else {
				$totalWasteValue = $this->waste['value'];
			}

			// TODO: !!! Как правильно посчитать, так или
			//$this->waste_calculated = $this->calculateWaste($this->waste['unittypeID'], $totalWasteValue, $quantitySum);	//	waste in weight unit type same as VOC
			// TODO: !!! или так:
			//echo "<h1>Waste:</h1>";
			//var_dump($this->waste);
				
			if($isMWS) {
				
				foreach ($this->waste as $w) {
					$this->waste_calculated += $this->calculateWaste($w->unittypeID, $w->value, $quantitySum);	//	waste in weight unit type same as VOC
				}
				//echo "<h1>Waste:</h1>";
				//var_dump($this->waste);
				$this->waste_json = json_encode($this->waste);
			} else {
				$this->waste_calculated += $this->calculateWaste($this->waste['unittypeID'], $w->value, $quantitySum);	//	waste in weight unit type same as VOC
			}
			
			//echo "<h1>WASTE:</h1>";
			//var_dump($this->waste);
			
			//echo "<h1>WASTE переделанное с pollutions:</h1>";
			if($isMWS) {
				$ws = new WasteStreams($this->db);
				$wastesFromDB = $ws->getWasteStreamsFromMix($this->mix_id);
				//var_dump($wastesFromDB);
				$this->waste = $wastesFromDB;
				
				$this->waste_json = json_encode($this->waste);
			}
						
			return $this->waste;
		}
		
		
		public function recalcAndSaveWastePersent() {
			if (!isset($this->mix_id)) return false;
			$mixID = mysql_escape_string($this->mix_id);
			
			$errors = $this->calculateCurrentUsage();
			$this->waste_percent = round($this->waste_percent,2);
			
			$query= "UPDATE ".TB_USAGE." SET waste_percent='$this->waste_percent.' WHERE mix_id='".$mixID."'";
			$this->db->exec($query);
			
			return $this->waste_percent;
		}
	
		

		public function calculateCurrentUsage() {	
			//echo "<h3>calculateCurrentUsage</h3>";	
			if ($this->products === null) return false; 
								
			if(!isset($this->waste)) {
				
				$this->iniWaste($isMWS);
			}
			
			$errors = array(
			'isDensityToVolumeError'=>false,
			'isDensityToWeightError'=>false,
			'isWasteCalculatedError'=>false,
			'isWastePercentAbove100'=>false			
			);

			$isThereProductWithoutDensity = false;
			$company = new Company($this->db);
			$unittype = new Unittype($this->db);
			//echo "<h1>WASTE:</h1>";
			//var_dump($this->waste);
			$wasteUnitDetails = $unittype->getUnittypeDetails($this->waste['unittypeID']);			
							
			$companyDetails = $company->getCompanyDetails($this->facility->getCompanyID());

			//	default unit type = company's voc unit
			$defaultType = $unittype->getDescriptionByID($companyDetails['voc_unittype_id']);
					
			$unitTypeConverter = new UnitTypeConverter($defaultType);
			
			//echo "<h3>Products: </h3>";
			//var_dump($this->products);
			//echo "<h1>Calculate Current Usage!</h1>";
			foreach ($this->products as $key=>$product) {	
				//echo "<h2>Product: </h2>";
				//var_dump($product);
				//echo "$key => "; var_dump($product);				
				$errors['isVocwxOrPercentWarning'][$key]='false';
				
				$densityObj = new Density($this->db, $product->density_unit_id);
				
				//	check density
				if (empty($product->density) || $product->density == '0.00') {
					$product->density = false;
					$isThereProductWithoutDensity = true;
				}
								
				// get Density Type
				$densityType = array (
    			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
    			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				);
				
				$quantitiWeightSum += $unitTypeConverter->convertFromTo($product->quantity, 
																		$product->unittypeDetails['description'], 
																		"lb", 
																		$product->density, 
																		$densityType);//	in weight
				//quantity array in gallon
				$quantitiVolumeSum += $unitTypeConverter->convertFromTo($product->quantity, 
																		$product->unittypeDetails['description'], 
																		"us gallon", 
																		$product->density, 
																		$densityType);//	in volume ;
																		
				//echo "<p>quantitiWeightSum: $quantitiWeightSum, quantitiVolumeSum: $quantitiVolumeSum</p>";
																		
			
				//check, is vocwx or percentVolatileWeight
				$isVocwx = true;
				$isPercentVolatileWeight = true;
				if (empty($product->vocwx) || $product->vocwx == '0.00') {
					$isVocwx = false;	
				}			
				
				$percentVolatileWeight = $product->getPercentVolatileWeight();			
				$percentVolatileVolume = $product->getPercentVolatileVolume();
								
				if (empty($percentVolatileWeight) || $percentVolatileWeight == '0.00') {		
					$isPercentVolatileWeight = false;	
					//echo "<p>set isPercentVolatileWeight to false</p>";
					
					if(empty($percentVolatileWeight)) {
						$w = $percentVolatileWeight;
						//echo "<p>product->perccentVolatileWeight is empty: $w</p>";
					}
				}
				//echo "<h1>isPercentVolatileWeight $isPercentVolatileWeight, isVocwx $isVocwx</h1>";
				if ($isPercentVolatileWeight || $isVocwx) {				
					//echo "<p>isPercentVolatileWeight || isVocwx</p>";					
					$errors['isVocwxOrPercentWarning'][$key] = false;					
					switch ($unittype->isWeightOrVolume($product->unittypeDetails['unittype_id'])) {
						case 'weight':
							//echo "<p>weight</p>";
							if ($isPercentVolatileWeight) {														
								$percentAndWeight = array(
									'weight'	=> $unitTypeConverter->convertToDefault($product->quantity, $product->unittypeDetails['description']),
									'percent'	=> $product->getPercentVolatileWeight()
								);
								//echo "percentAndWeight: ";
								//var_Dump($percentAndWeight);
								$ArrayWeight[] =$percentAndWeight; 
							} else  {								
								if ($product->density) {
									$galQty = $unitTypeConverter->convertFromTo($product->quantity, $product->unittypeDetails['description'], "us gallon", $product->density, $densityType);//	in volume
									$vocwxAndVolume = array (
										'volume'	=> $galQty,
										'vocwx'		=> $product->vocwx
									);
									$ArrayVolume[] = $vocwxAndVolume;
								} else {
									$errors['isDensityToVolumeError'] = true;
								}
							}						
							break;
						
						case 'volume':		
							//echo "<p>volume</p>";					
							if ($isVocwx) {			
								//echo "<p>isVocwx</p>";				
								$galQty = $unitTypeConverter->convertFromTo($product->quantity,  $product->unittypeDetails['description'], "us gallon");//	in volume								
								$vocwxAndVolume = array(
									'volume'=>$galQty,
									'vocwx'=>$product->vocwx
								);			
								//echo "<p>vocwxAndVolume: $vocwxAndVolume</p>";					
								$ArrayVolume[] = $vocwxAndVolume;
							} else  {								
								if ($product->density) {
									//echo "<p>density</p>";
									$percentAndWeight = array(
										'weight'=>$unitTypeConverter->convertToDefault($product->quantity,  $product->unittypeDetails['description'], $product->density, $densityType),
										'percent'=> $product->getPercentVolatileWeight()
									);
									$ArrayWeight[] =$percentAndWeight;
								} else {
									//echo "<p>not density</p>";
									$errors['isDensityToWeightError']=true;
								}
							}
							break;					
					}
				}
			}
			
			//var_dump($this->waste);
			//echo "---------";
			/*var_dump($this->waste['unitttypeID'], 
														$this->waste['value'],
														$unitTypeConverter,
														$quantitiWeightSum,
														$quantitiVolumeSum); */
			
			$wasteResult = $this->calculateWastePercent($this->waste['unitttypeID'], 
														$this->waste['value'],
														$unitTypeConverter,
														$quantitiWeightSum,
														$quantitiVolumeSum);
														
			//echo "waste result: ";
			//var_dump($wasteResult);
														
			$calculator = new Calculator();		
			$this->voc = $calculator->calculateVocNew ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult);
			if($this->debug) {
				echo "<h1>Waste Percent: {$wasteResult['wastePercent']}</h1>";
			}
			
			$this->waste_percent = $wasteResult['wastePercent'];
			$this->currentUsage = $this->voc;
			$this->wastePercent = $wasteResult['wastePercent'];
			$errors['isWastePercentAbove100'] = $wasteResult['isWastePercentAbove100'];
			
			return $errors;			
		}
		
		public function getCurrentUsage() {
			if(!isset($this->voc)) {
				$this->calculateCurrentUsage();
			}
			return $this->voc;
		}
		
		private function selectWaste($isMWS) {
			if (!isset($this->mix_id)) return false;
			$mixID = mysql_escape_string($this->mix_id);

			$query = "SELECT * FROM waste WHERE mix_id = ".$mixID;			
			if(!$this->isMWS) { // If module MWS disabled - get one waste.
				
				$query .= " AND waste_stream_id IS NULL";
			}
			
			$this->db->query($query);
			
			if ($this->db->num_rows() > 0) {

				if(!isMWS) {
					return $this->db->fetch(0);// If module MWS disabled - get one waste.
				} else {
					return $this->db->fetch_all();// Else get all wastes
				}
			} else
				return false;
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
			$waste = round($waste, 2); 
			return $waste;
		}	
		
		
		
		private function calculateWastePercent($unittypeID, $value, UnitTypeConverter $unitTypeConverter, $quantityWeightSum = 0, $quantityVolumeSum = 0) {
			$result = array(
						'wastePercent' => 0,
						'isWasteError' => false,
						'isWastePercentAbove100' => false
					);
			$unittype = new Unittype($this->db);
			$uid = $this->waste['unittypeID'] ? $this->waste['unittypeID'] : $this->waste['unitttypeID'];
			
			$wasteUnitDetails = $unittype->getUnittypeDetails($uid);

			
			if (empty($unittypeID)) {
				//	percent
				$result['wastePercent']= $value;
			} else {
				
				
				switch ($unittype->isWeightOrVolume($uid)) {
					
					case 'volume':
						
						$weistVolume = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], 'us gallon');
						$result['wastePercent'] = $weistVolume/$quantityVolumeSum*100;							
						break;
						
					case 'weight':
						//echo "weight";						
						//echo "value: $value, description: {$wasteUnitDetails["description"]}";
						$weistWeight = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], "lb");
						$result['wastePercent'] = $weistWeight/$quantityWeightSum*100;						
						break;
						
					case false:
						$result['isWasteError'] = true;
						break;
				}
			}
			
			if ($result['wastePercent']>100) {
				$result['wastePercent'] = 0;
				$result['isWastePercentAbove100'] = true;
			}
			
			return $result;
		}
		
		//	Tracking System
		private function save2trash($CRUD, $id) {
			//	protect from SQL injections
			$id = mysql_escape_string($id);
			
			$tm = new TrackManager($this->db);
			$this->trashRecord = $tm->save2trash(TB_USAGE, $id, $CRUD, $this->parentTrashRecord);
		}
	}