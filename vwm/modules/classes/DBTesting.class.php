<?php

class DBTesting {

	private $db;
	private $idArray;
	private $accessLevelID = array(
				'company'		=> 0,
				'facility'		=> 1,
				'department'	=> 2,
				'superUser'		=> 3
			);

    function DBTesting($db) {
    	$this->db = $db;
    }
    
    public function killDB() {
//    	//$this->testCompany(84,10000);
//    	$facilityID = 162;
//    	$companyID = 189;
//    	$count = 500;
//    	//$this->testDepartment(84,$facilityID,$count,$companyID);
//    	$departmentIDarray = array('565','566','567','568','514');
//    	//foreach ($departmentIDarray as $departmentID) {
////    	$departmentID = $departmentIDarray[0];
////    		$this->testUser($this->accessLevelID['superUser'],30);
////    		$this->testUser($this->accessLevelID['company'],50,$companyID);
////    		$this->testUser($this->accessLevelID['facility'],50,$companyID,$facilityID);
////    		$this->testUser($this->accessLevelID['department'],50,$companyID,$facilityID,$departmentID);
////    		$this->testMixes(84,$departmentID,$companyID,$count);
//    	//} 
//    	$this->testProducts(124,1);
$this->getProductsToComponents();
    	echo"done!";
    	//var_dump($this->testProducts(148,1));
    	//$this->testUser($this->accessLevelID['superUser'],1);
    	//$this->testMixes(84,511,148,1);
    }
    
    private function getProductsToComponents() {
    	$this->db->query("SELECT * FROM product ");
    	$data = $this->db->fetch_all();
    	$result = array();
    	foreach($data as $record) {
    		$productDetails[$record->product_id] = $record;
    		$this->db->query("SELECT component_id, weight FROM components_group WHERE product_id = '$record->product_id'");
    		$data_comp = $this->db->fetch_all();
    		foreach ($data_comp as $comp_record) {
    			$result[$record->product_id] [$comp_record->component_id]= $comp_record->weight;
    		}
    	}
    	$equals = array(); $wasAlreadyAddesToEquals = array();
    	foreach($result as $product_id => $product) {
    		if (!in_array($product_id,$wasAlreadyAddesToEquals)) {
	    		foreach($result as $product_id2 => $product2) {
	    			if ($product == $product2 && $product_id != $product_id2) {
	    				echo "------";
	    				var_dump($product);echo"($product_id)=($product_id2)";var_dump($product2);
	    				echo "------";
	    				$equals[$product_id] []=$product_id2;
	    				if (!in_array($product_id2,$wasAlreadyAddesToEquals)) $wasAlreadyAddesToEquals []= $product_id2;
	    				if (!in_array($product_id,$wasAlreadyAddesToEquals)) $wasAlreadyAddesToEquals []= $product_id;
	    			}
	    		}
    		}
    	}
    	
    	foreach($wasAlreadyAddesToEquals as $product_id) {
    		$products .= $product_id.', ';
    	}
    	$products = substr($products,0,-2);
    	$this->db->query("SELECT company_id, product_id FROM product2company WHERE product_id IN ($products) ORDER BY product_id");
    	$product2companyData = $this->db->fetch_all();
    	$product2company = array();
    	foreach($product2companyData as $record) {
    		$product2company [$record->product_id] []= $record->company_id;
    	}
    	$toView = array();
    	foreach($equals as $product => $equalProducts) {
    		$tmp = array();
    		$productDetails[$product]->companyID = $product2company[$product];
    		$tmp []= $productDetails[$product];
    		foreach($equalProducts as $product) {
    			$productDetails[$product]->companyID = $product2company[$product];
    			$tmp []= $productDetails[$product];
    		} 
    		$toView []=$tmp;var_dump($tmp);
    	}
    	echo " **********Result:***********";
    	var_dump($toView);
    }
    
    private function testUser($accessLevelID, $count = 5, $companyID = null, $facilityID = null, $departmentID = null) {
    	$accessName = $accessLevelID.((!is_null($companyID))?'_'.$companyID:'').
    		((!is_null($facilityID))?'_'.$facilityID:'').((!is_null($departmentID))?'_'.$departmentID:'').'_'.time();
	    while ($count > 0) {
		    $data = array (
			    'username'			=>	$accessName,
				'accessname'		=>	$accessName,
				'password'			=>	$accessName,
				'confirm_password'	=>	$accessName,
				'phone'				=>	'0'.rand(10,99).rand(1000000,9999999),
				'mobile'			=>	'0'.rand(10,99).rand(1000000,9999999),
				'email'				=>	'mail'.rand(0,100000).'@voc.com',
				'accesslevel_id'	=>	$accessLevelID,
				'grace'				=>	14
		    );							
		    
		    $check = array (
			    'username'			=>	'ok',
				'accessname'		=>	'ok',
				'password'			=>	'ok',
				'confirm_password'	=>	'ok',
				'phone'				=>	'ok',
				'mobile'			=>	'ok',
				'email'				=>	'ok',
				'accesslevel_id'	=>	'ok',
		    );
		    
		    if ($accessLevelID!=3) {
			    $data['company_id']=$companyID;
			    $check['company_id']='ok';
			    if ($accessLevelID==1 || $accessLevelID==2) {
				    $data['facility_id']=$facilityID;
				    $check['facility_id']='ok';
			    }
			    if ($accessLevelID==2) {
				    $data['department_id']=$departmentID;
				    $check['department_id']='ok';
			    }
		    }							
		    
		    $user=new User($this->db,null,null,null);
		    
		    if (strlen(trim($data['password'])) == 0 && strlen(trim($data['confirm_password'])) == 0) {
			    $data['password'] 			= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
			    $data['confirm_password']	= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
		    }						
		    
		    //if ($user->isValidRegData($data, $check)) 
		    {							
			    $user->addUser($data);								
				$count--;							
		    } 
	    }
    }
    
    /**
     * private function testCompany() - create many companies with facilities and departments
     * @param $count - how many companies it will be
     * @param $userID - who will create them (SuperUser)
     */
    private function testCompany($userID,$count = 10000) {
		while ( $count > 0 ) {
		    $name = 'Company '.time();
		    $companyData = array(
			    "name"				=>	$name,
				"address"			=>	$name,
				"city"				=>	$name,
				"zip"				=>	rand(10000,99999),
				"county"			=>	$name,
				"state"				=>	'284',
				"country"			=>	'215',
				"phone"				=>	'0'.rand(10,99).rand(1000000,9999999),
				"fax"				=>	rand(100000000,999999999),
				"email"				=>	'mail'.rand(0,100000).'@voc.com',
				"contact"			=>	rand(0,10000),
				"title"				=>	'company '.$name,
				"creater_id"		=>	$userID,
				"voc_unittype_id"	=>  '2'
		    );
		    
		    $validation = new Validation($this->db);
		    $validStatus = $validation->validateRegData($companyData);							
		    //	check for duplicate names
		    if (!($validation->isUniqueName("company", $companyData["name"]))) {
			    $validStatus['summary'] = 'false';
			    $validStatus['name'] = 'alredyExist';
		    }							
		    
		    if ($validStatus['summary'] == 'true') {
			    $companies = new Company($this->db);																	
			    //	setter injection
			    $companies->setTrashRecord(new Trash($this->db));
			    $companyID = $companies->addNewCompany($companyData);
			    
			    if (isset($companyID)) {
				    $count--;
				    $this->testUser($this->accessLevelID['company'],rand(1,10),$companyID);//here we should add some users
				    $this->testFacility($userID,$companyID,rand(1,10));	
			    }
			    
		    }
    	}
    }
    
    private function testFacility($userID,$companyID,$count = 5) {
    	while ( $count > 0 ) {
    		$limit = rand(1000,9999);
    		$name = 'Facility'.time();
			$facilityData = array (
				"voc_limit"		=>	$limit,
				"voc_annual_limit"		=>	$limit*12,
				"epa"			=>	'EPA'.time(),
				"company_id"	=>	$companyID,
				"name"			=>	$name,
				"address"		=>	$name,
				"city"			=>	$name,
				"zip"			=>	rand(10000,99999),
				"county"		=>	$name,
				"state"			=>	'284',
				"country"		=>	'215',
				"phone"			=>	'0'.rand(10,99).rand(1000000,9999999),
				"fax"			=>	rand(100000000,999999999),
				"email"			=>	'mail'.rand(0,100000).'@voc.com',
				"contact"		=>	rand(0,10000),
				"title"			=>	'facility '.$name,
				"creater_id"	=>	$userID
			);
			
			
			$validation = new Validation($this->db);
			$validStatus = $validation->validateRegData($facilityData);
			
			if (!$validation->isUniqueName("facility", $facilityData["name"], $facilityData['company_id'])) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				$facility = new Facility($this->db);
				//	setter injection
				$facility->setTrashRecord(new Trash($this->db));	
				$facilityID = $facility->addNewFacility($facilityData);
				if (isset($facilityID)) {
					$count--;
					$this->testUser($this->accessLevelID['facility'],rand(1,10),$companyID,$facilityID);
					$this->testDepartment($userID,$facilityID,rand(1,10),$companyID);
				}
			}
    	}	
    }
    
    private function testDepartment($userID,$facilityID,$count = 5, $companyID = null) {
    	while ( $count > 0 ) {
    		$limit = rand(100,999);
    		$name = 'Department'.time();

			$departments = new Department($this->db);			
			
			$departmentData = array (
				"facility_id"	=>	$facilityID,
				"name"			=>	$name,
				"voc_limit"		=>	$limit,
				"voc_annual_limit"		=>	$limit*12,
				"creater_id"	=>	$userID
			);
			
			$validation = new Validation($this->db);
			$validStatus = $validation->validateRegData($departmentData);
			
			if (!$validation->isUniqueName("department", $departmentData['name'], $departmentData['facility_id'])) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {					
				//	setter injection
				$departments->setTrashRecord(new Trash($this->db));

				$departmentID = $departments->addNewDepartment($departmentData);
								
				if (isset($departmentID)) {
					$count--;
					$this->testUser($this->accessLevelID['department'],rand(1,10),$companyID,$facilityID,$departmentID);
					$this->testMixes($userID,$departmentID,$companyID,rand(1,10));
				}
			}
    	}	
    }
    
    private function testMixes($userID, $departmentID, $companyID, $count = 50) {
	    while ($count > 0) {
		    $equipmentID = $this->testEquipment($departmentID);
		    $name = 'Mix_'.$departmentID.'_'.time();
		    $usageData = array (
			    "department_id"			=>	$departmentID,
				"equipment_id"			=>	$equipmentID,
				"voc"					=>	0,
				"voclx"					=>	'0.00',
				"vocwx"					=>	'0.00',
				"description"			=>	$name,
				"unittype"				=>	1,
				"rule"					=>	'0',
				"user_id"				=>	$userID,
				"exemptRule"			=>	null,
				"creationTime"			=>	date('m-d-Y', time()-60*60*24*1000*rand(0,100)),
				"apmethod_id"			=>  null,
				"validResult"			=>  true,
				"unitTypeClass"			=>	null										
		    );						
		    		
		    //	waste system - when part of mix is not used												
		    //  wasteUnittypeClass can be "percent" or unittype class from DB for by "weight" method  													 																
		    
		    
			    $wasteData = array (						
				    "value"			=> rand(0,100),
					"unittypeClass"	=> 'percent',
					"unittypeID"	=> false
			    );						
			    $unittype = new Unittype($this->db);
			    $wasteData["unitTypeList"] = false;															
			    $usageData['waste'] = $wasteData;
		    
		    $validation = new Validation($this->db);
		    $productInfo = new Product($this->db);
		    //TODO pust here some products add
		    							
			    $validStatus = $validation->validateRegData($usageData);	
			  						
			    if (!$validation->isUniqueUsage($usageData)) {
				    $validStatus['summary'] = 'false';
				    $validStatus['description'] = 'alredyExist';
			    }
			    $products = $this->testProducts($companyID,rand(1,10));
											
			    for ($i=0; $i < count($products); $i++) {
				    $productMix = new Product($this->db);
				    $productMix->initializeByID($products[$i]['product_id']);
				    $unittypeDetails[$i]=$unittype->getUnittypeDetails($products[$i]['unittype']);
				    $recordProperties=new RecordProperties();
				    $recordProperties->setQuantity($products[$i]['quantity']);																								
				    $recordProperties->setUnitType($products[$i]['unittype']);
				    $mixRecord=new MixRecord($productMix, $recordProperties);
				    
				    $mixRecords[]=$mixRecord;
				    
			    }
			    
			    $equipmentMix=new Equipment($this->db);
			    $equipmentMix->initializeByID($equipmentID);
			    $departmentMix=new Department($this->db);
			    $departmentMix->initializeByID($departmentID);
			    
			    $mixProperties=new MixProperties($equipmentMix, $departmentMix, $wasteData);
			    
			    $mix=new Mix($mixRecords, $mixProperties);
			    $mix->setDB($this->db);
			    $mix->setCreationTime($usageData['creationTime']);	//	for MixValidator
			    $mixCalcError = $mix->calculateCurrentUsage();
			   
			    $usageData['voc']=$mix->getVoc();
			    $usageData['voclx']='0.00';
			    $usageData['vocwx']='0.00';
			    $usageData['rule']='0';
			    $usageData['waste_percent'] = $mix->getWastePercent();	
			    
			    $mixValidator = new MixValidator($recalc = true);
			    $mixValidatorResponse=$mixValidator->isValidMix($mix);
			    
			    $productCount = ($products)?count($products):0;
			    if ($validStatus['summary'] == 'true') {
				    foreach($products as $product) {
					    if($product['product_id'] != '') {						
						    $validProducts[]=$product;
					    }
				    }
				    $usageData['products']=$validProducts;
				    //checkWaste to add mix										
				    //	setter injection								
				    $mix->setTrashRecord(new Trash($this->db));					
				    $newMixId = $mix->addNewMix($usageData,false);
				    $count--;
			    }
	    }				
    }
    
    private function testProducts($companyID, $count = 5) {
    	while ($count > 0) {
	    $name = 'PR_NR_'.$count.time();
	    $productData = array (
		    "product_nr"		=>	$name,
			"name"				=>	$name,
			"component_id"		=>	1,
			"density"			=>	rand(1,5),
			"density_unit_id"	=>  1,
			"inventory_id"		=>	0,
			"coating_id"		=>	0,
			"specialty_coating"	=>	'no',
			"aerosol"			=>	'no',
			"specific_gravity"	=>	0,
			"supplier_id"		=>	1,
			"vocwx"				=>	rand(1,10),
			"voclx"				=>	rand(1,10),
			"boiling_range_from"=>	rand(1,10),
			"boiling_range_to"	=>	rand(10,30),
			"percent_volatile_weight"=>	rand(1,100),
			"percent_volatile_volume"	=>	rand(1,100),
			"creator_id"			=>	18
	    );
	    
	    //	process hazardous (chemical) classes
	    $hazardous = new Hazardous($this->db);
	    $productData['chemicalClasses'] = null;
	    
	    //	process components
	    $componentCount = rand(1,10);
	    for ($i=0;$i<$componentCount;$i++) {
		    //add some components for product
		    $cas = 'CAS_'.$i.'_'.time();
		    $data=array(
			    "cas"	=>	$cas,
				"description"	=>	'DESC_'.$cas
		    );
		    $agency=new Agency($this->db);
		    $agencyCount=$agency->getAgencyCount();
		    $componentsObj=new Component($this->db);
		    $data['agencies']=$componentsObj->getComponentAgencies("");
		    
		    for ($i=0; $i < $agencyCount; $i++) {
			    $data['agencies'][$i]['control']='no';
		    }
		    
		    $validate=new Validation($this->db);
		    $validStatus=$validate->validateRegDataAdminClasses($data);
		    
		    if (!($validate->isUniqueName("component", $data['cas']))) {
			    $validStatus['summary'] = 'false';
			    $validStatus['cas'] = 'alredyExist';
		    }
		    
		    if ($validStatus["summary"] == "true") {
			    $componentID = $componentsObj->addNewComponent($data);
		    }
		    //end of components add
		    //add substrate
		    $substrateData=array(
				"description"	=>	'SUBSTRATE_'.time()
			);
		    $substrate=new Substrate($this->db);
			$substrate->addNewSubstrate($substrateData);
			$this->db->query("SELECT LAST_INSERT_ID() id");
			$substrateID = $this->db->fetch(0)->id;
			//end of substrate add
		    $component = array (
			    "component_id"	=>	$componentID,
				"comp_cas"		=>	$cas,
				"temp_vp"		=>	rand(1,100),
				"substrate_id"		=>	$substrateID,
				"rule_id"		=>	'0',
				"mm_hg"			=>	rand(1,100),
				"weight"		=>	rand(1,100),
				"type"			=>	'VOC'
		    );
		    $components[] = $component;
	    }
	    
	    $productData['components'] = $components;
	    $validation = new Validation($this->db);
	    
//	    
//	    $validStatus = $validation->validateRegDataProduct($productData);										
//	    //check for duplicate names
//	    if($productData['supplier_id']==null)
//	    {
//		    $validStatus['summary'] = 'false';
//		    $validStatus['supplier_id'] = 'failed';
//	    }
//	    if($productData['coating_id']==null)
//	    {
//		    $validStatus['summary'] = 'false';
//		    $validStatus['coating_id'] = 'failed';
//	    }
//	    if (!($validation->isUniqueName("product", $productData["product_nr"]))) {
//		    $validStatus['summary'] = 'false';
//		    $validStatus['product_nr'] = 'alredyExist';
//	    }
	    $product = new Product($this->db);
	    
//	    if ($validStatus['summary'] == 'true') 
	    {
		    $productID = $product->addNewProduct($productData, $companyID);	
		   // if (!empty($productID)) 
		    {						
			    $products []= array(
				    "product_id"	=>	$productID,
					"product_nr"	=>	$name,
					"quantity"		=>	rand(1,10),
					"unittype"		=>	2,
					"supplier"		=>	1,
					"description"	=>	$name
			    );
			    $count--;
		    }
	    }
    }
	    return $products;
    }
    
    private function testEquipment($departmentID) {
    	$regData = array(
				"equipment_id"	=>	null,
				"name"			=>	'NAME_'.time(),
				"department_id"	=>	$departmentID,
				"equip_desc"	=>	'DESC_'.time(),
				"inventory_id"	=>	0,
				"permit"		=>	rand(0,10),
				"expire_date"	=>	date('Y-m-d', time()*2),				
				"daily"			=>	rand(1,100),
				"dept_track"	=>	'yes',
				"facility_track"=>	'yes',
				"creater_id"	=>	18
			);

			
			$validation = new Validation($this->db);
			$validateStatus = $validation->validateRegDataEquipment($regData);			
		
			if ($validateStatus["summary"] == "true") {		
				//	convert date to timestamp							
				$regData["expire"] = time();
							
				$equipment = new Equipment($this->db);
				//	setter injection								
				$equipment->setTrashRecord(new Trash($this->db));														
				$equipmentID = $equipment->addNewEquipment($regData);
			} 
			return $equipmentID;
    }
}
?>