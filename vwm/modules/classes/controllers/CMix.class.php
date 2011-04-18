<?php
class CMix extends Controller
{	
	function CMix($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='mix';
		$this->parent_category='department';			
	}
	
	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionConfirmDelete()
	{		
		$usage = new Mix($this->db, $trashRecord);
																				
		foreach ($this->itemID as $ID)
		{									
			//	setter injection
			$usage->setTrashRecord(new Trash($this->db));
			$usageDetails=$usage->getMixDetails($ID);
			$itemForDeleteName[]=	$usageDetails["description"];
			$usage->deleteUsage($ID);
		}		
		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=department&id=".$usageDetails['department_id']."&bookmark=mix&notify=" . (count($this->itemID) > 1 ? "32" : "33" ));										
	}
	
	private function actionDeleteItem()
	{
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);
			
		$usage = new Mix($this->db);
		if (!is_null($this->getFromRequest('id'))) {
		foreach ($req_id as $mixID) {
				$usageDetails = $usage->getMixDetails($mixID);
				$delete["id"] =	$usageDetails["mix_id"];
				$delete["description"] = $usageDetails["description"];								
				$itemForDelete[] = $delete;
			}
		}
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=mix");
							
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}
							
		//set permissions							
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');		
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}
	
	private function actionViewDetails()
	{
		$usage = new Mix($this->db);						
		$usageDetails = $usage->getMixDetails($this->getFromRequest('id'));
		
		
		$mixID = $this->getFromRequest('id');
		$mixOptimized = new MixOptimized($this->db, $mixID);
		
		//var_dump($mixOptimized);
		
		//if (!isset($this->getFromRequest('departmentID')) || empty($request['departmentID'])) {
		//	$request['departmentID'] = $usageDetails['department_id'];
		//}				
							//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}				

		$mixOptimized->rule = $ruleDetails["rule_nr"];
//																		
//		$usage = new Mix($db);						
//		$usageDetails = $usage->getMixDetails($request["id"]);
		/*$rule = new Rule($this->db);
		$ruleDetails = $rule->getRuleDetails($usageDetails["rule"]);
		$usageDetails["rule"] = $ruleDetails["rule_nr"];
		
		
		
		

		if ($usageDetails['waste_percent'] == null) 
		{
			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
			if ($this->user->checkAccess('waste_streams', $companyID)) 
			{
				//	OK, this company has access to waste streams module, so let's setup..
				$ms = new ModuleSystem($this->db);
				$moduleMap = $ms->getModulesMap();
				$mWasteStreams = new $moduleMap['waste_streams'];
				$result = $mWasteStreams->prepareViewMix(array('id' => $this->getFromRequest('id'), 'db' => $this->db));
				extract($result); //here extracted $wasteData 
				$usageDetails['waste_percent'] = $usage->recalcAndSaveWastePersent($this->getFromRequest('id'),$wasteData);
			} 
			else 
			{
				$usageDetails['waste_percent'] = $usage->recalcAndSaveWastePersent($this->getFromRequest('id'));
			}
		}*/
		
		//echo "<h1>Usage Details product1:</h1>";
		//var_dump($usageDetails['products'][0]);
		//echo "<h1>Mix Optimized product1:</h1>";
		//var_dump($mixOptimized->products[0]);
		
		$this->smarty->assign("usage", $mixOptimized);

		$apMethodObject = new Apmethod($this->db);
		$apMethodDetails =$apMethodObject->getApmethodDetails($mixOptimized->apmethod_id);
		$this->smarty->assign('apMethodDetails',$apMethodDetails);											
		//$usageDetails = $usage->getMixDetails($this->getFromRequest('id'), true);
		//$this->smarty->assign("productCount", count($usageDetails["products"]));				
							
		$unittype = new Unittype($this->db);
		//	TOD O: что за хрень с рулами?																				
		$k = 0; 
		//var_dump($mixOptimized->products);
		//exit;
		for ($i = 0; $i < count($mixOptimized->products); $i++) 
		{
			$product = $mixOptimized->products[$i];
			//$productMix = new Product($this->db);
			//$productMix->initializeByID($product['product_id']);							
			$unittypeDetails[$i] = $unittype->getUnittypeDetails($product->unit_type);
			$productDetails[$i] = $product->getProductDetails($product->product_id);
			for ($j = 0;$j < count($productDetails[$i]['components']); $j++)
			{
				if (!empty($productDetails[$i]['components'][$j]['rule']))
				{
					$rules[$k] = $productDetails[$i]['components'][$j]['rule'];
					$k++;									
				}								
			}																																		
		}	
		$rules = array_keys(array_count_values($rules));
		$rulesCount = count($rules);
		$this->smarty->assign('rules',$rules);
		$this->smarty->assign('rulesCount',$rulesCount);
							
		$this->smarty->assign('unitTypeName',$unittypeDetails);
							
		//$mix = new Mix($this->db);
		//$mix->initializeByID($this->getFromRequest('id'));
							
		//$mixValidator=new MixValidator();
		//$mixValidatorResponse=$mixValidator->isValidMix($mix);
		//var_dump($mixValidatorResponse);	
		
		
		
		$mixValidatorOptimized = new MixValidatorOptimized();
		$mixOptimizedValidatorResponce = $mixValidatorOptimized->isValidMix($mixOptimized);
		
		//var_dump($mixOptimizedValidatorResponce);
							
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		$companyDetails = $company->getCompanyDetails($companyID);
		
		
		$this->smarty->assign('companyDetails',$companyDetails);					
							
		$this->smarty->assign('unittypeObj',$unittype);

		$this->smarty->assign('dailyLimitExceeded', $mixOptimizedValidatorResponce->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixOptimizedValidatorResponce->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixOptimizedValidatorResponce->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixOptimizedValidatorResponce->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixOptimizedValidatorResponce->getFacilityAnnualLimitExceeded());
		$this->smarty->assign('expired', $mixOptimizedValidatorResponce->isExpired());
		$this->smarty->assign('preExpired', $mixOptimizedValidatorResponce->isPreExpired());
				
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
							
		$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=mix');
		$this->smarty->assign('tpl', 'tpls/viewUsage.tpl');			
		$this->smarty->display("tpls:index.tpl");	
	}

	/**
     * bookmarkDMix($vars)     
     * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
     */       
	protected function bookmarkDMix($vars)
	{			
		extract($vars);
		
		$sortStr=$this->sortList('mix',2);
		$filterStr=$this->filterList('mix');
		
		$usages = new Mix($this->db);								
									
		//	search??									
		if ($this->getFromRequest('searchAction')=='search') 
		{
			$mixesToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			if (!is_null($this->getFromRequest('export'))) 
			{
				$pagination = null;											
			} 
			else 
			{									
				$searchedMixesCount = $usages->countSearchedMixes($mixesToFind, 'description', $this->getFromRequest('id'));
				$pagination = new Pagination($searchedMixesCount);
				$pagination->url = "?q=".urlencode($this->getFromRequest('q'))."&action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id')."&bookmark=".$this->getFromRequest('bookmark')."&searchAction=search";
			}																						
			$usageList = $usages->searchMixes($mixesToFind, 'description', $this->getFromRequest('id'), $pagination);
																																	
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			$this->smarty->assign('pagination',$pagination);
		} 
		else 
		{
			if ($this->getFromRequest('export')) 
			{
				$pagination = null;											
			} 
			else 
			{	
				$pagination = new Pagination((int)$usages->countMixes($this->getFromRequest('id'),$filterStr));
				$pagination->url = "?action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id')."&bookmark=".$this->getFromRequest('bookmark').
					(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
					(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
					(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
					(isset($filterData['filterField'])?"&searchAction=filter":"");
			}
			$usageList = $usages->getMixList($this->getFromRequest('id'), $pagination,$filterStr,$sortStr);										
			$this->smarty->assign('pagination',$pagination);
		}				

		
		if (!is_null($this->getFromRequest('export'))) 
		{
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Mixes of department ".$departmentDetails['name'];
			if ($this->getFromRequest('searchAction')=='search') 
			{
				$exporter->search_term = $this->getFromRequest('q');
			} 
			else 
			{
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}
			$widths = array(
							'mix_id' => '10',
							'description' => '60',	
							'voc' => '10',
							'creation_time' => '20'										
							);										
			$header = array(
							'mix_id' => 'ID Number',
							'description' => 'Description',	
							'voc' => 'VOC',
							'creation_time' => 'Creation Date'																	
							);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);										
			$exporter->setTbody($usageList);
			$exporter->export();
			die();
									
		} 
		else 
		{										
			//================	Begin MIX'es Highliting
			$mixValidator = new MixValidator();
			$mixHover = new Hover();
			
			
			$departmentID = $this->getFromRequest('id');
			
			//$start = xdebug_time_index();
			//echo "<h1>MixManager start:$start</h1>";
			$mixOptimized = new MixManager($this->db, $departmentID);
			$mixList = $mixOptimized->getMixList($pagination);
			//var_dump($mixList[0]);
			//$end = xdebug_time_index();
			//$optimizedDiff = $end - $start;
			//echo "<h1>MixManager end $end. Difference: $optimizedDiff</h1>";
			

			/*$start = xdebug_time_index();
			echo "<h1>Old variant start: $start</h1>";
			for ($i=0; $i<count($usageList); $i++) 
			{
				$url="?action=viewDetails&category=mix&id=".$usageList[$i]['mix_id']."&departmentID=".$this->getFromRequest('id');
				$usageList[$i]['url'] = $url;
				$mix = new Mix($this->db);
				$mix->initializeByID($usageList[$i]["mix_id"]);
				$validatorResponse = $mixValidator->isValidMix($mix);

				if ($validatorResponse->isValid()) 
				{
					$usageList[$i]["valid"] = "valid";
					$usageList[$i]["hoverMessage"] = $mixHover->mixValid();
				}
				else 
				{
					if ($validatorResponse->isPreExpired()) 
					{
						$usageList[$i]["valid"] = "preexpired";
						$usageList[$i]["hoverMessage"] = $mixHover->mixPreExpired();
					}
													
					if ($validatorResponse->isSomeLimitExceeded() or $validatorResponse->isExpired())
					{
						$usageList[$i]["valid"] = "invalid";
						$usageList[$i]["hoverMessage"] = $mixHover->mixInvalid();
					}
				}
			}
			
			$end = xdebug_time_index();
			$oldDiff = $end - $start;
			echo "<h1>Old variant end $end. Difference: $oldDiff</h1>";
			
			
			$totalDiffPercent = $oldDiff / $optimizedDiff;
			echo "<h1>Different between optimized and old variant: $totalDiffPercent</h1>";
			var_dump($usageList);
			var_dump($mixList);
			*/								
			//================	Finish MIX'es highlitings
			//var_dump($usageList[0]);
			//var_dump($mixList[0]);
			//exit;
											
			$this->smarty->assign('childCategoryItems', $mixList);
											
			//	set js scripts
			$jsSources = array  (
								'modules/js/checkBoxes.js',										
								'modules/js/autocomplete/jquery.autocomplete.js',
								);
			$this->smarty->assign('jsSources', $jsSources);
											
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/mixListNew.tpl');
		}			
	}
	
	private function  actionEditItemAjax() {
		if($_REQUEST['debug']) {
			$debug = true;
		}
		if($debug) {
			var_dump($_REQUEST);
		}
		
		$form = $_REQUEST;
		
		if(!isset($form['id'])) {
			echo json_encode(array('mix_error' => 'No mix ID!!'));
			exit;
		}
		
		$mix = new MixOptimized($this->db, $form['id']);
		if($debug) {
			var_dump($mix);
		}
		
		$departmentID = $mix->department_id;		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];
		
		//	Extractt from json
		$jmix = json_decode($form['mix']);
		$wastes = json_decode($form['wasteJson']);
		
		//	Start processing waste						
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		$isMWS = false;
		
		if ($showModules['waste_streams']) {				
			//	OK, this company has access to waste streams module, so let's setup..
			$mWasteStreams = new $moduleMap['waste_streams'];
			$isMWS = true; //shows that we can access module Waste Streams
			if($debug) {
				echo "<h1>Wastes:</h1>";
				var_dump($wastes);
			}
			$params = array(
				'db' 		=> $this->db,
				'xnyo' 		=> $this->xnyo,
				'isForm' 	=> (count($form)>0),
				'facilityID'=> $facilityID,
				'companyID' => $companyID,
				'jmix'		=> $jmix,
				'wastes'	=> $wastes			
			);
			
			$result = $mWasteStreams->prepare4mixAdd($params);
			foreach ($result as $key=>$value) {										
				$this->smarty->assign($key,$value);
			}
			
			if (isset($result['storageError']) || ($result['isDeletedStorageError']=='true')) {
				$storagesFailed = true;
			}								
			$wasteArr = $mWasteStreams->resultParams['waste'];
			if($debug) {
				echo "<h1>mWasteStreams->resultParams</h1>";
				var_dump($mWasteStreams->resultParams['waste']);
			}
			//								$ws = $mWasteStreams->getNewObject($this->db);
				
			
			//...........
			
			//	OK, this company has access to waste streams module, so let's setup..
			//$mWasteStreams = new MWasteStreams();
			
			$result = $mWasteStreams->validateWastes($this->db, $this->xnyo, $facilityID, $companyID, '03-29-2011' , $wastes);	
			if($debug) {
				echo "<h1>validateWastes</h1>";
				var_Dump($mWasteStreams);
				//var_dump($wastes);
			}			
			if($result != false){
				echo json_encode($result);
				exit;
			} else {
				//echo "<p>Waste stream validation failed</p>";
			}
		} 
				
		if(!isset($form['mix']) or !$this->validateInputMix($form['mix'])) {			
			echo json_encode(array('mix_error' => 'Mix error!'));
			exit;
		}
		
		if(!isset($form['products'])) {			
			echo json_encode(array('products_error' => 'Products are not set!'));
			exit;
		}
		
		$jproducts = json_decode($form['products']);
		if($debug){
			var_dump($jproducts);
			
		}
		
		$valProductsRes = $this->validateProducts2($jproducts);
		
		if($valProductsRes === true) {
			if($debug) {
				echo "<p>Products are OK</p>";
			}
		} else {
			if($debug) {
				echo "<p>Products Error</p>";
				var_dump($valProductsRes);
			}		
			echo json_encode(array('products_error' => 'Products error!'));
			exit;
		}
		
		//	Start to ini onbjects				
		//$mix = $this->buildMix($jmix);
		$this->updateMixByForm($mix,$jmix);
		//$mix->facility_id = $facilityID;
		$mix->isMWS = $isMWS;				
				
		$mix->products = $this->buildProducts($jproducts);
		/*if($debug){
			echo("<h1>PRODUCTS:</h1>");
			var_Dump($mix->products);
		}*/
		//$mix->waste = $wastes;
		//$mix->waste = json_encode($wasteArr);				
		$mix->getEquipment();
		$mix->getFacility();	
		//$mix->waste = $wastes;

		
		
		$this->AddOrEditAjax($facilityID, $companyID, $isMWS, $mix, $mWasteStreams, $wastes, $debug);
	}
	
	private function actionAddItemAjax() {
		
		//$form = $this->getFromPost();
		$form = $_REQUEST;
		
		if($form['debug']) {			
			$debug = true;
			echo "add mix";
			var_dump($form);
		}				
		
		$departmentID = $form['departmentID'];		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];
		
		//	Extractt from json
		$jmix = json_decode($form['mix']);
		$wastes = json_decode($form['wasteJson']);
		
		//	Start processing waste						
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		$isMWS = false;
		
		if ($showModules['waste_streams']) {	
			if($debug) {
				echo "<h1>MWS MODULE</h1>";
			}			
			//	OK, this company has access to waste streams module, so let's setup..
			$mWasteStreams = new $moduleMap['waste_streams'];
			$isMWS = true; //shows that we can access module Waste Streams
			$params = array(
				'db' 		=> $this->db,
				'xnyo' 		=> $this->xnyo,
				'isForm' 	=> (count($form)>0),
				'facilityID'=> $facilityID,
				'companyID' => $companyID,
				'jmix'		=> $jmix,
				'wastes'	=> $wastes			
			);
			
			$result = $mWasteStreams->prepare4mixAdd($params);
			foreach ($result as $key=>$value) {										
				$this->smarty->assign($key,$value);
			}
			
			if (isset($result['storageError']) || ($result['isDeletedStorageError']=='true')) {
				$storagesFailed = true;
			}		
			if($debug) {
				echo "<h1>OLOLO</h1>";		
				var_dump($mWasteStreams->resultParams['waste']);				
				$wasteArr = $mWasteStreams->resultParams['waste'];
			}
			//								$ws = $mWasteStreams->getNewObject($this->db);
				
			
			//...........
			
			//	OK, this company has access to waste streams module, so let's setup..
			//$mWasteStreams = new MWasteStreams();
			if($debug)
				echo "<h1>new MWasteStreams</h1>";
			
			$result = $mWasteStreams->validateWastes($this->db, $this->xnyo, $facilityID, $companyID, '03-29-2011' , $wastes);
			if($debug) {
				echo "<h1>validateWastes</h1>";
				var_Dump($mWasteStreams);
			}			
			if($result != false){
				echo json_encode($result);
				exit;
			} else {
				//echo "<p>Waste stream validation failed</p>";
			}
		} else {
			if($debug) {
				echo "<h1>NO MWS MODULE</h1>";
			}
		}
				
		if(!isset($form['mix']) or !$this->validateInputMix($form['mix'])) {			
			echo json_encode(array('mix_error' => 'Mix error!'));
			exit;
		}
		
		if(!isset($form['products'])) {			
			echo json_encode(array('products_error' => 'Products are not set!'));
			exit;
		}
		
		$jproducts = json_decode($form['products']);
		if($debug){
			var_dump($jproducts);
			
		}
		
		$valProductsRes = $this->validateProducts2($jproducts);
		
		if($valProductsRes === true) {
			if($debug) {
				echo "<p>Products are OK</p>";
			}
		} else {
			if($debug) {
				echo "<p>Products Error</p>";
				var_dump($valProductsRes);
			}		
			echo json_encode(array('products_error' => 'Products error!'));
			exit;
		}
		
		//	Start to ini onbjects				
		$mix = $this->buildMix($jmix);
		$mix->facility_id = $facilityID;
		$mix->isMWS = $isMWS;				
				
		$mix->products = $this->buildProducts($jproducts);
		//$mix->waste = $wastes;
		//$mix->waste = json_encode($wasteArr);				
		$mix->getEquipment();
		$mix->getFacility();	
		//$mix->waste = $wastes;

		
		
		$this->AddOrEditAjax($facilityID, $companyID, $isMWS, $mix, $mWasteStreams, $wastes, $debug);
		//if($validationRes['summary'] != 'false')  // Add Mix - No validating errors 
			
		//var_dump($mix);
		
		//header("Location: /vwm/?action=browseCategory&category=department&id={$mix->department_id}&bookmark=mix");
		////var_dump($mix);
		//$mix->save();
	}
	
	private function AddOrEditAjax($facilityID, $companyID, $isMWS, MixOptimized $mix, MWasteStreams $mWasteStreams, $jwaste ,$debug = false) {
		
		if ($isMWS) {
			//here we calculate total waste for voc calculations
			$params = array (
				'products' => $mix->products,
				'db' => $this->db
			);
			if($debug) {
				echo "<h3>calculateWaste</h3>";
				var_dump($mWasteStreams);
			}
			$result = $mWasteStreams->calculateWaste($params);			
			extract($result); //here extracted $wasteData, $wasteArr and $ws_error
			if ($ws_error) {
//				$validStatus['summary'] = 'false';
//				if ($ws_error !== true) {
//					//$ws->error not 'true' its a error message!
//					$validStatus['waste']['error'] = $ws_error;
//				}
			}			
			$mix->waste = $wasteData;
			
		}		
		
		/*$mix->wasteArray = $wastes;
		if($debug) {		
			echo "waste:";
			var_dump($mix->wasteArray);
		}*/
		
		$mixValidator = new MixValidatorOptimized();
		//TODO: stopped here Denis April 4, 2011  -->	
		$mix->calculateCurrentUsage();
		$mixValidatorResponse = $mixValidator->isValidMix($mix);
		
		if($debug) {
			var_dump($mixValidatorResponse,$validationRes);
			echo "<h2>VOCs:</h2>";
			var_dump($mix->voc, $mix->voclx, $mix->vocwx);
		}
		
		if($debug) {
			echo "MWasteStreams:";
			var_dump($mWasteStreams->resultParams['waste']);
		}

		//exit;
		if($debug) {
			$this->db->beginTransaction();
		}
		
		//$mix->waste
		$mix->waste = $jwaste;
		$mix->debug = $debug;
		$newMixID = $mix->save($isMWS,$optMix);
		if($debug) {
			echo "<h1>mix #$newMixID saved!</h1>";
		}
		//If module 'Waste Streams' is disabled, waste is already saved in mix->save func
		if($isMWS) {
			//echo "prepareSaveWasteStreams";
			$mWasteStreams->prepareSaveWasteStreams(array('id' => $newMixID, 'db' => $this->db));
			if($debug) {
				echo "<h1>Waste Streams saved to mix #$newMixID!</h1>";
			}
		}
		if($debug) {
			echo "<h1>DONE!</h1>";
		}
		echo "DONE";
		exit;
	}
	
	private function buildProducts($ps) {
		
		$products = array();
		foreach($ps as $p) {
			
			$product = new MixProduct($this->db);
			
			
			$product->initializeByID($p->productID);
			$product->quantity = $p->quantity;
			
			$unittype = new Unittype($this->db);
			$unittypeDetails = $unittype->getUnittypeDetails($p->selectUnittype);
			//var_dump($unittypeDetails);
			$product->unit_type = $unittypeDetails['name'];
			$product->unittypeDetails = $unittypeDetails;
			
			$product->json = json_encode($product);
			
			
			$products[] = $product;
		}
		
		return $products;
	}
	
	private function buildMix($m) {
		
		$optMix = new MixOptimized($this->db);
		
		$optMix->department_id 	= $_REQUEST['departmentID'];
		$optMix->equipment_id 	= $m->equipment;		
		$optMix->description	= $m->description;
		$optMix->rule			= $m->rule;
		$optMix->rule_id		= $m->rule;
		$optMix->exempt_rule	= $m->excemptRule;
		$optMix->creation_time	= $m->creationTime;
		$optMix->apmethod_id	= $m->APMethod;
		$optMix->valid			= true;
		$optMix->unittypeClass	= $m->selectUnittypeClass;
		
		return $optMix;
	}
	
	private function updateMixByForm($basemix,$formMix) {
		
		$basemix->equipment_id 	= $formMix->equipment;		
		$basemix->description	= $formMix->description;
		$basemix->rule			= $formMix->rule;
		$basemix->rule_id		= $formMix->rule;
		$basemix->exempt_rule	= $formMix->excemptRule;
		$basemix->creation_time	= $formMix->creationTime;
		$basemix->apmethod_id	= $formMix->APMethod;
		$basemix->valid			= true;
		$basemix->unittypeClass	= $formMix->selectUnittypeClass;
	}
	
	private function validateInputMix($m) {
		
		if(empty($m['description']) or empty($m['creation_time'])) {
			return false;
		}
		
		return true;
	}
	
	private function validateProducts2($products) {
		
		$validation = new Validation($this->db);
		$productConflitcs = array();
		
		foreach($products as $product) {
		$isProdConflict = $validation->checkWeight2Volume($product->productID, $product->selectUnittype);
		
			if ($isProdConflict !== true) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = $isProdConflict;
				$productConflitcs[$product->productID] = $product->selectUnittype;
			}
		}
		
		return empty ($productConflitcs) ? true : $productConflitcs;
	}
	
		
	private function actionAddItem() {
		
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}			
			
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		
		
		
		if(isset($_POST['save'])) {
			$action = $_POST['save'] == "Add product to list" ? "addItem" : "saveMix";	
		} else {
			$action = "addItem";
		}
		
		$this->addEdit($action, $this->getFromRequest('departmentID'));
	}
	
	private function actionEdit() {

		$mix = new Mix($this->db);
		$departmentID = $mix->getMixDepartment($this->getFromRequest('id'));
		//	Access control
		if (!$this->user->checkAccess('department', $departmentID)) {						
			throw new Exception('deny');
		}
		
		$this->setListCategoriesLeftNew('department', $departmentID);
		$this->setNavigationUpNew('department', $departmentID);
		$this->setPermissionsNew('viewData');
		
		if(isset($_POST['save'])) {
			$action = $_POST['save'] == "Add product to list" ? "EditAddItem" : "edit";	
		} else {
			$action = "edit";
		}
		
		$this->addEdit($action, $departmentID);
	}
	
	private function addEdit($action, $departmentID) {
		
		/*
		echo "<div style='width:100%; background-color:silver;border:1px black solid;'>";
		echo "<center><h1>POST</h1></center>";
		var_dump($_POST,$action);
		echo "</div>";
		*/
		
		
		$form = $this->getFromPost();
		
		/** protecting from xss **/
		foreach ($form as $key=>$value)
		{								
			$form[$key]=Reform::HtmlEncode($value);
		}
		
		/** show modules **/
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		//var_dump($showModules);
		
		if( $this->isModuleWasteStream($companyID)) { //	OK, this company has access to waste streams module, so let's setup..
				$isMWS = true;
			} else {
				$isMWS = false;
		}
		
		/** Mix need to create only if edit. when add new mix or product - mix is not created yet **/
		if($action == "edit" or $action == "EditAddItem") { 
			$mixID = $this->getFromRequest("id");
			$optMix = new MixOptimized($this->db, $mixID);
			//var_dump($optMix->products[0]->unitTypeList);
			$optMix->isMWS = $isMWS;
			$optMix->getDepartment();
			
			/** Initialize facility, company and equipment */
			$optMix->getFacility();
			$optMix->getEquipment();
			$optMix->getCompany();
			
			if($isMWS === true) {
				$result = $this->prepareShowWasteForSmarty((count($form) > 0), $optMix->facility_id, $optMix->company->company_id, $mixID);
				
				//var_dump(json_decode($result['waste_streams']['wasteStreamsWithPollutions']));
				
				foreach ($result['waste_streams'] as $key=>$value) {				//Assign to smarty: storageOverflow,deletedStorageValidation,isDeletedStorageError,wasteStreamsList,wasteStreamsWithPollutions,storages
					$this->smarty->assign($key,$value);
					//echo "<b>assign:</b> $key => value: ";
					//var_dump($value);
				}
				
				if (isset($result['waste_streams']['storageError']) || ($result['waste_streams']['isDeletedStorageError']=='true')) {
					$storagesFailed = true;
				}								
				$wasteArr = $result['waste_arr'];
			} 
			
			/** Initialize waste **/
			//echo "<h1>Ini Waste</h1>";
			$optMix->iniWaste($isMWS); // TODO: Доделать если MWS выключен
			
			//var_dump($optMix);
			
		}
		//Init all wastes list for smarty
		if($isMWS) {
			$result = $this->prepare4MixAdd( (count($form) > 0) /*IsForm*/, $facilityID, $companyID);
			foreach ($result as $key=>$value) {	
				$this->smarty->assign($key,$value);
			}
			
			if (isset($result['storageError']) || ($result['isDeletedStorageError']=='true')) {
				$storagesFailed = true;
			}	
		}
		//var_dump("show:",$this->smarty->get_template_vars('show'));
		
		

		if(count($form) > 0) {
			
			switch ($action) {
				case "edit":
						$this->confirmSaveMix($form,$storagesFailed,$isMWS, $optMix, $checkIsUnique = false);
					break;
				case "addItem":
						$this->confirmAdd($form,$isMWS);
					break;
				case "saveMix":
						$this->confirmSaveMix($form,$storagesFailed,$isMWS);
					break;
				case "EditAddItem":
						$this->editMix_AddProduct($form,$optMix);
					break;
			}
			
		} else {
			
			switch ($action) {
				case "edit":
						$this->showEdit($optMix,$isMWS);
					break;
				case "addItem":
						$this->showAdd($departmentID);
					break;
			}
		}
		
		$jsSources = array (										
		    'modules/js/flot/jquery.flot.js',
			'modules/js/mixValidator.js',
			'modules/js/productObj.js',
			'modules/js/productCollection.js',
			'modules/js/mixObj.js',
			'modules/js/addUsage.js',
	    	'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
	    );
	    $this->smarty->assign('jsSources',$jsSources);
	    
	    $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
			
		/*$jsSources = array(									
			'modules/js/addUsage.js'							
		);							
		
		$this->smarty->assign('jsSources', $jsSources);		*/	
		if( $action == "EditAddItem") {
			$action = "edit";
		}	
		else if($action == "saveMix") {
			$action = "addItem";
		}
		
		if($_GET['debug']) {
			$this->smarty->assign('debug',true);
		}
		
		$this->smarty->assign('sendFormAction', '?action='.$action.'&category='.$this->category.(($action == 'addItem')?'&departmentID='.$departmentID:'&id='.$this->getFromRequest('id')));
		$this->smarty->assign('tpl', 'tpls/addUsageNew.tpl');	
		$this->smarty->display("tpls:index.tpl");
	}
	
	
	
	/**
	 * 
	 * 
	 * @param unknown_type $form
	 * @param unknown_type $storagesFailed
	 * @param unknown_type $isMWS
	 * @param unknown_type $optMix = null. If not null - edit mix. Is null - add new mix
	 */
	private function confirmSaveMix($form,$storagesFailed,$isMWS, $optMix = null, $checkIsUnique = true) {
		
		// valide reg data
		$mix = $this->getMixByPOSTData($form);
		
		if(!isset($mix->department_id)) {
			$mix->department_id = $optMix->department_id;
			echo "<h1>Set department_id: {$optMix->department_id}</h1>";
		}
		
		
		
		if(isset($optMix)) {
			$mix->mix_id = $optMix->mix_id;
		}
		
		

		$validationRes['summary'] = true;
		$validation = new Validation($this->db);
		$dataForValidate = $this->prepareDataForValidation($mix);
		$validationRes = $validation->validateRegData($dataForValidate);
		
		
		
		//var_dump($mix);
		if($storagesFailed === true) {
			$validationRes['summary'] = 'false';
		}
		
		if($checkIsUnique) {
			if (!$validation->isUniqueUsage( Array("description" => $mix->description, "department_id" => $mix->department_id)) ) {
				$validationRes['summary'] = 'false';
				$validationRes['description'] = 'alredyExist';
			}
		}
		
		if ($mix->equipment_id == null) {
			$validationRes['summary'] = 'false';
			$validationRes['equipment'] = 'noEquipment';
		}
		
		$products = $this->getProductsByPOST();
		
		if(!$products) {
			$products = null;
		}
		
		$validProductsResult = $this->validateProducts($products);

		if(!$validProductsResult) {
			//products error!! 
			$validationRes['summary'] = 'false';
			//$validationRes['products'] = 'noProducts';
		}
		
		if ($products == null or $products === false or count($products) == 0 or !$validProductsResult) {
			$validationRes['summary'] = 'false';
			$validationRes['products'] = 'noProducts';
		}	
		
		//var_dump($validationRes);
		//echo "<h1>VOC before calc {$mix->voc}</h1>";
		
		$count = count($products);
		echo "<h1>count $count</h1>";
		
		for($i=0; $i<$count; $i++) {
			$products[$i]->initializeByID($products[$i]->product_id);
		}
		
		$mix->products = $products;
		
		if($isMWS) {
				$mWasteStreams = new MWasteStreams();
				
				$params = array(
					'db' => $this->db,
					'xnyo' => $this->xnyo,
					'isForm' => true,
					'facilityID' => $mix->getDepartment()->getFacilityID(),
					'companyID' => $mix->getCompany()->company_id	//DIFFERENCE!!											
				);
				
				//var_dump($params['isForm']);
				//echo "POST: ";
			//var_dump($_POST);
				$result = $mWasteStreams->prepare4mixAdd($params);
				//echo "resultparams:";
				//var_dump($mWasteStreams->resultParams['waste']);
				//echo "result:";
				//var_dump($result);
				
				$wastesFromPost = $this->getWastesFromPost($form);
				echo "<h1>Wastes from post:</h1>";
				var_dump($wastesFromPost);
				echo "<h1>Wastes from MWasteStreams->prepare4mixAdd:</h1>";
				var_dump($mWasteStreams->resultParams['waste']);
				//exit;
				//$mix->waste = $mWasteStreams->resultParams['waste'];
				$mix->waste_json = json_encode($mWasteStreams->resultParams['waste']);
				echo "<h1>WASTE JSON</h1>";
				var_dump($mix->waste_json );
		} else {
			$wasteFromPost = $this->getSingleWasteFromPost($form);
			$mix->waste = $wasteFromPost;
		}
		
		$mixCalcError = $mix->calculateCurrentUsage($isMWS);
		//echo "<h1>VOC after calc {$mix->voc}</h1>";
		//exit;
		
		if ($mixCalcError != null) {
			
			if ($mixCalcError['isDensityToVolumeError']) {
				$validationRes['conflict'] = 'density2volume';
				$validationRes['summary'] = 'false';
			}
			if ($mixCalcError['isDensityToWeightError']) {
				$validationRes['conflict'] = 'density2weight';
				$validationRes['summary'] = 'false';
			}
			$validationRes['warning'] = false;
			foreach ($mixCalcError['isVocwxOrPercentWarning'] as $productID => $productWarning) {
				if ($productWarning) {
					$validationRes['warning'] = true;
				}
			}
			if ($validationRes['warning']) {
				$validationRes['warnings2products'] = $mixCalcError['isVocwxOrPercentWarning'];
				
			}
			if ($mixCalcError['isWastePercentAbove100']) {
				$validationRes['summary'] = 'false';
				$validationRes['waste']['percent'] = 'failed'; 
			}
		}
		
		$mixValidator = new MixValidatorOptimized();
		$mixValidatorResponse = $mixValidator->isValidMix($mix);
		
		//var_dump($mixValidatorResponse,$validationRes);
		//exit;
		
		if($validationRes['summary'] != 'false') { // Add Mix - No validating errors 
			
			//var_dump($mix);
			

			//exit;
			//$this->db->beginTransaction();
			$newMixID = $mix->save($isMWS,$optMix);
			echo "<h1>mix #$newMixID saved!</h1>";
			//If module 'Waste Streams' is disabled, waste is already saved in mix->save func
			if($isMWS) {
				echo "<h1>Waste Streams saved to mix #$newMixID!</h1>";
				$mWasteStreams->prepareSaveWasteStreams(array('id' => $newMixID, 'db' => $this->db));
			}
			
			header("Location: /vwm/?action=browseCategory&category=department&id={$mix->department_id}&bookmark=mix");
			//var_dump($mix);
			//$mix->save();
			
		} else {
			/*Validation erors*/
			echo "<h1>Validation errors {$mix->department_id}</h1>";
			//Display all again with validation errors
			
			
			
			/** Load equipment list, cause confirmAdd doesnot do that */
			$equipment = new Equipment($this->db);
			
			var_dump($optMix);
			$equipmentList = $equipment->getEquipmentList($mix->department_id);
			var_dump($equipmentList);
			
			$this->confirmAdd($form);
			
			$this->smarty->assign('equipment',$equipmentList);
		}
		
		$this->smarty->assign('validStatus',$validationRes);
		
	}
	
	private function prepareDataForValidation($mix) {
		
		$data = Array (
			'voc'	=>	$mix->voc,
			'voclx'	=>	$mix->voclx,
			'vocwx'	=>	$mix->vocwx,
			'description'	=> $mix->description,
			'creationTime'	=> $mix->creation_time
		);
		return $data;
	}
	
	private function showAdd($departmentID) {
		
		$request = $this->getFromRequest();
		$request['id'] = $departmentID;
		$request['parent_category'] = $this->parent_category;
		
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($departmentID);						
		$this->smarty->assign('equipment', $equipmentList);
		
		$apmethodObject = new Apmethod($this->db);
		$APMethod=$apmethodObject->getDefaultApmethodDescriptions($companyID);
		if(!isset($APMethod) or empty($APMethod)) {
			$APMethod = $apmethodObject->getApmethodList(null);
		}
		
		//	Get rule list
		$rule = new Rule($this->db);						
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $companyID, $departmentDetails['facility_id'], $departmentID);							
		$this->smarty->assign('rules', $customizedRuleList);
		
		
		
		//	Getting Product list
		
		$productsListGrouped = $this->getProductsListGrouped($companyID);
		
		$this->smarty->assign('products', $productsListGrouped);
		
		$product = new Product($this->db);
		$productInfo = $product->getProductInfoInMixes($productList[0]['product_id']);
		
		$res = $this->getDefaultTypesAndUnitTypes($companyID);		
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];
		
		
		
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('companyID', $companyID);
		
		
		$this->smarty->assign('APMethod',$APMethod);
		$this->smarty->assign('defaultAPMethod',$defaultAPMethod);
		
		$data = $this->getClearDataForAddItem();
		
		$data->product_desc = $productInfo['desc'];
		$data->coating = $productInfo['coat'];														
		
		$unittype = new Unittype($this->db);
		//var_dump("unittype_id",$unitTypeEx[0]['unittype_id']);
		$unitTypeClass = $unittype->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		//var_dump($companyID, $unitTypeClass);
		$unittypeListDefault = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
		//var_dump($unittypeListDefault);
		
		if (empty($unittypeListDefault)) {
			$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
		}
		//	var_dump($unittypeListDefault);
		$data->unitTypeClass = $unitTypeClass;
		
		//if($isMWS) { //IF MWS disabled
			$mix = new MixOptimized($this->db);
			$mix->iniWaste(false);
		//}
		
		$data->waste = $mix->waste;
		
		$this->smarty->assign('data',$data);

		//var_dump("unittype",$unittypeListDefault);
		$this->smarty->assign('unittype', $unittypeListDefault);
		
	}
	
	private function getClearDataForAddItem() {
		$data->voc				= '0.00';
		$data->voclx			= '0.00';
		$data->vocwx			= '0.00';
		$data->creation_time 	= date("m-d-Y");
		$data->waste			= false;
		return $data;
	}
	
	private function showEdit($optMix,$isMWS) {
		
		
		$optMix->setTrashRecord(new Trash($this->db));
		//echo "<h1>Creation date: {$optMix->creation_time}</h1>";
		//	Get rule list
		$rule = new Rule($this->db);
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $optMix->company->company_id, $optMix->facility_id, $optMix->department_id);
		$this->smarty->assign('rules', $customizedRuleList);
		//var_dump($customizedRuleList); echo($query
		
		/** Collect unittypeDetails for smarty **/
		
		foreach($optMix->products as $p) {
			
			$unittypeDetails2[] = $p->unittypeDetails;
			
		}
		
		
		$this->smarty->assign('unitTypeName',$unittypeDetails2);
		
		$mixCalcError = $optMix->calculateCurrentUsage();
		//var_dump($mixCalcError);
		//var_dump($optMix);
		
		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($optMix->department_id);
		
		
		$productsListGrouped = $this->getProductsListGrouped($optMix->company->company_id);
		
		$unittypeList = $this->getUnitTypeList($optMix->company->company_id);
		
		$APMethod = $this->getDefaultApMethod($optMix->company->company_id);
		
		$res = $this->getDefaultTypesAndUnitTypes($optMix->company->company_id);		
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];
		
		//var_dump($optMix->waste);
		
		//$this->addEditOld('edit', $optMix->department_id);
		//exit;
		
		//var_dump($optMix->products);
		
		//echo "Waste:";
		//var_dump(json_decode($optMix));
		
		//var_dump($optMix->waste[0]);
		
		/*TEST*/
		//$optMix->waste_json = '[{"0":{"id":"1","value":"1.00","unittypeClass":"USAWght","unittypeID":"12","validation":"success"},"1":{"id":"2","value":"2.00","unittypeClass":"USAWght","unittypeID":"12","validation":"success"},"storage_id":"58","count":2,"id":1},{"id":"3","value":"3.00","unittypeClass":"USAWght","unittypeID":"12","storage_id":"11","validation":"success"}]';
		
		$this->smarty->assign('data', $optMix);
		$this->smarty->assign('equipment', $equipmentList);
		$this->smarty->assign('products', $productsListGrouped);
		$this->smarty->assign('unittype', $unittypeList);
		$this->smarty->assign('productCount',count($optMix->products));						
		$this->smarty->assign('productsAdded',$optMix->products);
		$this->smarty->assign('APMethod',$APMethod);
		//exit;
		
		//var_dump($optMix->products);
		//var_dump($productsListGrouped);
		//exit;
		
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('companyID', $optMix->company->company_id);
	}
	
	private  function editMix_AddProduct($form,$optMix) {
		
		$this->confirmAdd($form);
		
		$res = $this->getDefaultTypesAndUnitTypes($optMix->company->company_id);		
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];
		
		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($optMix->department_id);
		
		$this->smarty->assign('equipment', $equipmentList);
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		//var_dump($optMix);
		echo "CompanyID: {$optMix->company->company_id}";
		$this->smarty->assign('companyID', $optMix->company->company_id);
	}
	
	private function confirmAdd($form,$isMWS) {
		
		//echo "get wastes from post:";
		
		//var_dump($wastesFromPost);
		
		
		
		$optMix = $this->getMixByPOSTData($form);
		
		//var_dump($isMWS);
		
		if($isMWS) {
			$wastesFromPost = $this->getWastesFromPost($form);
			$optMix->waste_json = json_encode($wastesFromPost);
			
		} else {
			$wasteFromPost = $this->getSingleWasteFromPost($form);
			//var_dump($wasteFromPost);
			$optMix->waste = $wasteFromPost;
		}
		
		
		
		$validResult = $this->validateProductByForm($form);
		
		
		$this->smarty->assign('validStatus', $validResult);
		
		//var_dump("valid:",$validResult);
		
		$this->showAdd($form['department_id']);
		
		$data = $this->smarty->get_template_vars('data');
		$data->description = $_POST['description'];
		$data->exempt_rule = $_POST['exemptRule'];
		
		//echo "<h1>Data</h1>";
		//var_dump($data);
		
		$products = $this->getProductsByPOST();
		
		//var_dump($products);
		
		if(!$products) {
			$products = Array();
		}
		//echo "valid result:";
		//var_dump($validResult);
		//echo "product count: " . count($products);
		//echo "<h2>Products from post:</h2>";
		//var_dump($products);
		if($validResult['summary'] != 'false') { // If adding product valid, add to cart
			$product = new MixProduct($this->db);
			
			
			$product->initializeByID($form['selectProduct']);
			$product->quantity = $form['quantity'];
			
			$unittype = new Unittype($this->db);
			$unittypeDetails = $unittype->getUnittypeDetails($form['selectUnittype']);
			//var_dump($unittypeDetails);
			$product->unit_type = $unittypeDetails['name'];
			$product->unittypeDetails = $unittypeDetails;
			
			$product->json = json_encode($product);
			//echo "product added";
			
			$products[] = $product;
		}
		
		//var_dump($products);
		
		$optMix->products = $products;
		
		$unittype = new Unittype($this->db);
		
		$count = count($optMix->products);
		for($i=0; $i<$count; $i++) {
			$optMix->products[$i]->initializeByID($optMix->products[$i]->product_id);
			$optMix->products[$i]->initUnittypeList($unittype);
			
		}
		
		$calcMixResult = $optMix->calculateCurrentUsage($isMWS);
		//var_dump($calcMixResult);
		/** From Post**/
		$optMix->description = $_POST['description'];
		$optMix->exempt_rule = $_POST['exemptRule'];
		
		$mixValidatorOptimized = new MixValidatorOptimized(true);
		$mixValidatorResponse = $mixValidatorOptimized->isValidMix($optMix);
		
		$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
		
		//var_dump($mixValidatorResponse);
		//var_dump($optMix);
		//exit;
		
		
		
		//$data = $this->smarty->get_template_vars('data');
		//var_dump($data);
		//$data->products = $product;
		//$product->set
		//var_dump($product);
		//exit;
		//$products = $this->initProductsByForm($form);
		
		//var_dump($products);
		
		
		$this->smarty->assign('data',$optMix);
		$this->smarty->assign('productCount',count($products));
		$this->smarty->assign('productsAdded',$products);
		
	}
	
	private function getSingleWasteFromPost($form) {
		$waste['value'] = $form['wasteValue'];
		$waste['unittypeClass'] = $form['selectWasteUnittypeClass'];
		$waste['unittypeID'] = $form['selectWasteUnittype'];
		
		$unittype = new Unittype($this->db);
		$waste['unitTypeList'] = $unittype->getUnittypeListDefault($waste['unittypeClass']);
		return $waste;
	}
	
	private function getWastesFromPost($form) {
		
		$wasteCount = $_POST['wasteStreamCount'];
		$wastes = Array();
		
		//echo "<h1>Waste Count: $wasteCount</h1>";
		
		for($i = 0; $i < $wasteCount; $i++) {
			
			$waste['id'] = $_POST["wasteStreamSelect_$i"];
			$waste['storage_id'] = $_POST["selectStorage_$i"];
			
			$pollutionCount = $_POST["pollutionCount_$i"];
			$waste['count'] = $pollutionCount;
			//echo "<h3> pollutionCount $pollutionCount</h3>";
			
			$quantityWithoutPollutions = $_POST["quantityWithoutPollutions_$i"];
			if(isset($quantityWithoutPollutions)) {
				
				$waste['value'] = $quantityWithoutPollutions;
				$waste['unittypeClass'] = $_POST["selectWasteUnittypeClassWithoutPollutions_$i"];
				$waste['unittypeID']	= $_POST["selectWasteUnittypeWithoutPollutions_$i"];
			} 
			else {
				$pollutions = Array();
				for($j = 0; $j<$pollutionCount; $j++) { /*Adding pollutions*/
					$pollution['id'] = $_POST["selectPollution_{$i}_{$j}"];
					//echo "<p>selectPollution_{$i}_{$j}</p>";
					$pollution['unittypeClass'] = $_POST["selectWasteUnittypeClass_{$i}_{$j}"];
					$pollution['unittypeID'] = $_POST["selectWasteUnittype_{$i}_{$j}"];
					$pollution['value'] = $_POST["quantity_{$i}_{$j}"];
					
					//$pollutions[] = $pollution;
					$waste[$j] = $pollution;
				}
				
				
				//$waste[] = $pollutions;
			}
			
			$wastes[] = $waste;
			unset($waste);
		}
		return $wastes;
	}
	
	private function prepare4MixAdd($isForm, $facilityID, $companyID) { 
		//echo "prepare4MixAdd!!";
		//var_dump($isForm, $facilityID, $companyID);
		$mWasteStreams = new MWasteStreams();
		$params = array(
			'db' => $this->db,
			'xnyo' => $this->xnyo,
			'isForm' => $isForm,
			'facilityID' => $facilityID,
			'companyID' => $companyID		//DIFFERENCE!!											
		);
		if (isset($_GET['id'])) {
			$params['id'] = $this->getFromRequest('id');
		}
		//var_dump($params);
		$result = $mWasteStreams->prepare4mixAdd($params);
		//var_dump($result);
		return $result;
		
	}
	
	public function actionValidateProductAjax() {
		
		$unittypeID = $_REQUEST['unittypeID'];
		$productID = $_REQUEST['productID'];
		
		if($_REQUEST['debug']) {
			var_dump($product);
		}
		
		$validation = new Validation($this->db);
		$productConflitcs = array();
		
		
		$isProdConflict = $validation->checkWeight2Volume($productID, $unittypeID);
		if ($isProdConflict !== true) {
			$validStatus['summary'] = 'false';
			$validStatus['description'] = $isProdConflict;
		} else {
			$validStatus['summary'] = 'true';
		}
		
		echo json_encode($validStatus);
		exit;
	}
	
	private function validateProductByForm($form) {
		
		$validation = new Validation($this->db);
		
		$validStatus = $validation->validateRegData(Array("quantity" => $form['quantity']));
		// we have new validation in Mix->calculateCurrentUsage 
		//but we still need those - to disallow product adding
		
		$isProdConflict = $validation->checkWeight2Volume($form['selectProduct'], $form['selectUnittype']);
		if ($isProdConflict !== true) {
			$validStatus['summary'] = 'false';
			$validStatus['description'] = $isProdConflict;
		}
		//echo "validate product:"
		//var_dump("");
		
		return $validStatus;
	}
	
	private function validateProducts($products) {
		
		$validation = new Validation($this->db);
		$count = count($products);
		
		$validSummary = true;
		
		for($i = 0; $i < $count; $i++) {
			
			$products[$i]->valid = true;
			
			$validStatus = $validation->validateRegData(Array("quantity" => $products[$i]->quantity));
			//var_dump($validStatus);
			if($validStatus['summary'] != 'true') {
				$products[$i]->valid = false;
				$validSummary = false;
			}
			// we have new validation in Mix->calculateCurrentUsage 
			//but we still need those - to disallow product adding
			$isProdConflict = $validation->checkWeight2Volume($products[$i]->product_id, $products[$i]->unittypeDetails->unittype_id);
			
			if ($isProdConflict) {
				$products[$i]->valid = false;
				$validSummary = false;
			}
			
		}
		return $validSummary;
	}
	
	private function getProductsByPOST() {
		
		if($_POST['addingProductsArr']) {
			
			foreach($_POST['addingProductsArr'] as $jsProduct) {
				
				//var_dump($jsProduct);
				$product = json_decode($jsProduct);
				$product->json = $jsProduct;
				
				//Convert std class (product) to MixProduct ($mixProduct)
				$mixProduct = new MixProduct($this->db);
				// set public properties
				foreach($product as $key => $value) {
					
					//echo "$key and $value <br/>";
					
					if(property_exists ( $mixProduct , $key ) ) {
						try {// Property may be not public
							$mixProduct->$key = $value;
						} catch (Exception $e) {}
					}
				}
				
				$mixProduct->json = $product->json;
				
				/** Convert unittypeDetails to array, cause of json_decode make it object =( **/
				$mixProduct->unittypeDetails = Array ( 
					"unittype_id"	=> $product->unittypeDetails->unittype_id,
					"name"			=> $product->unittypeDetails->name,
					"description"	=> $product->unittypeDetails->description,
					"formula"		=> $product->unittypeDetails->formula,
					"type_id"		=> $product->unittypeDetails->type_id,
					"type"			=> $product->unittypeDetails->type
				) ;
				
				$products[] = $mixProduct;
			} 
			
			return $products;
		} else {
			return false;
		}
	}
	
	private function getMixByPOSTData($form) {
		$optMix = new MixOptimized($this->db);
		
		$optMix->department_id 	= $form['department_id'];
		$optMix->equipment_id 	= $form['selectEquipment'];
		$optMix->voc			= $form['voc'] 		? $form['voc'] 		: "0.00";
		$optMix->voclx			= $form['voclx'] 	? $form['voclx'] 	: "0.00";
		$optMix->vocwx			= $form['vocwx'] 	? $form['vocwx'] 	: "0.00";
		$optMix->description	= $form['description'];
		$optMix->unittype		= $form['selectUnittype']; // not exists
		$optMix->rule			= $form['rule'];
		$optMix->user_id		= 0; //not exists
		$optMix->exempt_rule	= $form['exemptRule'];
		$optMix->creation_time	= $form['creationTime'];
		$optMix->apmethod_id	= $form['selectAPMethod'];
		$optMix->valid			= true;
		$optMix->unittypeClass	= $form['selectUnittypeClass'];
		
		return $optMix;
	}
	
	
	
	private function getDefaultTypesAndUnitTypes($companyID) {
		
		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		
		$companyEx = 1;
		if (!$unitTypeEx) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
			$companyEx = 0;
		}
		
		
		
		$k = 1;
		$count = 1;
		$flag = 1;
		$typeEx = Array();
		$typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		
		
		while ($unitTypeEx[$k]){
			$idn = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$k]['unittype_id']);
			
			for($j=0; $j < $count; $j++) {
				if ($idn == $typeEx[$j] ) {
					$flag=0;
					break;
				}
			}
			if ($flag) {
				$typeEx[$count] = $idn;
				$count++;
			}
			$k++;
			$flag = 1;
		}
		
		return Array("typeEx" => $typeEx, "companyEx" => $companyEx, "unitTypeEx" => $unitTypeEx);
	}
	
	private function getDefaultApMethod($companyID) {
		
		$apmethodObject = new Apmethod($this->db);
		$APMethod = $apmethodObject->getDefaultApmethodDescriptions($companyID);
		return $APMethod;
	}
	
	private function getProductsListGrouped($companyID) {
		
	// get product list
		$product = new Product($this->db);						
		$productList = $product->getFormatedProductList($companyID);

		//	NICE PRODUCT LIST 
		foreach ($productList as $oneProduct) {
			$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
		}
		return $productListGrouped;
	}
	
	private function getUnitTypeList($companyID) {
		
		$unittype = new Unittype($this->db);
		$cUnitTypeEx = new Unittype($this->db);

		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		if ($unitTypeEx === null) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
		}
		$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);

		$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
		
		return $unittypeList;
	}
	
	private function prepareShowWasteForSmarty($isForm,$facilityID,$companyID,$mixID) {//shows that we can access madule Waste Streams
		
			$mWasteStreams = new MWasteStreams();
			$params = array(
				'db' 		=> $this->db,
				'xnyo' 		=> $this->xnyo,
				'isForm' 	=> $isForm,
				'facilityID'=> $facilityID,
				'companyID' => $companyID,
				'id'		=> $mixID											
			);
			$result['waste_streams'] =	$mWasteStreams->prepare4mixAdd($params);
			$result['waste_arr']	=	$mWasteStreams->resultParams['waste'];
			return $result;
	}
	
	private function isModuleWasteStream($companyID) {
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		$isMWS = false;
		if ($showModules['waste_streams']) {
			return $showModules['waste_streams'];
		} else {
			return false;
		}
	}
	
	
	
	private function addEditOld($action, $departmentID) {
//same=>
		$form = $this->getFromPost();	
		
		// protecting from xss
		foreach ($form as $key=>$value)
		{								
			$form[$key]=Reform::HtmlEncode($value);
		}
		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		$isMWS = false;
		if ($showModules['waste_streams']) {
			//	OK, this company has access to waste streams module, so let's setup..
			$mWasteStreams = new $moduleMap['waste_streams'];
			$isMWS = true; //shows that we can access madule Waste Streams
			$params = array(
				'db' => $this->db,
				'xnyo' => $this->xnyo,
				'isForm' => (count($form)>0),
				'facilityID' => $facilityID,
				'companyID' => $companyID		//DIFFERENCE!!											
			);
			if ($action == 'edit') {
				$params['id'] = $this->getFromRequest('id');
			}
			$result = $mWasteStreams->prepare4mixAdd($params);
			foreach ($result as $key=>$value) {										
				$this->smarty->assign($key,$value);
			}
			
			if (isset($result['storageError']) || ($result['isDeletedStorageError']=='true')) {
				$storagesFailed = true;
			}								
			$wasteArr = $mWasteStreams->resultParams['waste'];
			//								$ws = $mWasteStreams->getNewObject($this->db);//query failed, no unittype with received ID
		}
		if (count($form) > 0) {
			
			//dIRRERS
			if ($action == 'edit') {
				$usages = new Mix($this->db);
				//	setter injection								
				$usages->setTrashRecord(new Trash($this->db));		
				
				$mixDetails = $usages->getMixDetails($this->getFromRequest('id'),false,$isMWS);
			}
			//eND OF DIFFERS
			$usageData = array (
				"department_id"			=>	$departmentID,
				"equipment_id"			=>	$form["selectEquipment"],
				"voc"					=>	$form["voc"],
				"voclx"					=>	$form["voclx"],
				"vocwx"					=>	$form["vocwx"],
				"description"			=>	$form["description"],
				"unittype"				=>	$form["selectUnittype"],
				"rule"					=>	$form["rule"],
				"user_id"				=>	0,
				"exemptRule"			=>	$form["exemptRule"],
				"creationTime"			=>	$form["creationTime"],
				"apmethod_id"			=>  $form["selectAPMethod"],
				"validResult"			=>  true,	//	DIFFERS?
				"unitTypeClass"			=>	$form["selectUnittypeClass"]										
			);	
			//DIFFERS
			if ($action == 'edit') {
				$usageData["mix_id"] = $this->getFromRequest('id');
			}
			//+ACCESS CONTROL WAS EARLIER
			//END OF DIFFERS					
			
			//	waste system - when part of mix is not used												
			//  wasteUnittypeClass can be "percent" or unittype class from DB for by "weight" method  													 																
			
			$unittype = new Unittype($this->db);
			if (!$isMWS) {
				$wasteData = array (						
					"value"			=> $form["wasteValue"],
					"unittypeClass"	=> $form["selectWasteUnittypeClass"],
					"unittypeID"	=> (isset($form["selectWasteUnittype"])) ? $form["selectWasteUnittype"] : false
				);
				//DIFFERS
				$wasteData["mixID"] = $this->getFromRequest('id');
				//END OF DIFFERS
				$wasteData["unitTypeList"] = ($wasteData["unittypeID"]) ? $unittype->getUnittypeListDefault($wasteData["unittypeClass"]) : false;															
				$usageData['waste'] = $wasteData;				
			} 
			//DIFFERS? only in edit
			$regData['name'] = $usageData['description'];
			//END OF DIFFERS			
			if ($usageData['voc']=="") {
				$usageData['voc']="0.00";
			}
			if ($usageData['voclx']=="") {
				$usageData['voclx']="0.00";
			}
			if ($usageData['vocwx']=="") {
				$usageData['vocwx']="0.00";
			}		
			
			$validation = new Validation($this->db);
			$productInfo = new Product($this->db);
			
			if ($form['save'] == "Save") {								
				$validStatus = $validation->validateRegData($usageData);	
				if ($storagesFailed === true) {
					$validStatus['summary'] = 'false';
				}						
				if (!$validation->isUniqueUsage($usageData)) {
					$validStatus['summary'] = 'false';
					$validStatus['description'] = 'alredyExist';
				}
				
				if ($form['selectEquipment'] == null) {
					$validStatus['summary'] = 'false';
					$validStatus['equipment'] = 'noEquipment';
				}
				
				$productCount = ($form['productCount'] == "") ? 0 : $form['productCount'];
				
				for ($i=0;$i<$productCount;$i++) {
					//DIFFERS
					if (!is_null($this->getFromPost('product_'.$i))) {
					//END OF DIFFERS
					$data = $productInfo->getProductDetails($this->getFromPost('product_'.$i));
					$product = array(
						"product_id"	=>	$this->getFromPost('product_'.$i),
						"product_nr"	=>	$data['product_nr'],
						"quantity"		=>	$this->getFromPost('quantity_'.$i),
						"unittype"		=>	$this->getFromPost('unittype_'.$i),
						"supplier"		=>	$data["supplier"],
						"description"	=>	$data["name"],
						"components"	=>	"This is hack by Denis, cuz ugly validation" // TODO: fix me
					);
					//DIFFERS IN EDIT
					$product['unitTypeList'] = $unittype->getUnittypeListDefault($this->getFromPost('selectUnittypeClass_'.$i));
					$product['unittypeClass'] = $this->getFromPost('selectUnittypeClass_'.$i);
					//END OF DIFFERS
					
					//product quantity validation
					$productValidation = $validation->validateRegDataProduct($product);	
					//DIFFERS IN EDIT
					$isProdConflict = $validation->checkWeight2Volume($product['product_id'], $product['unittype']);
					if ($isProdConflict !== true) {
						$productValidation['summary'] = 'false';
						$productValidation['description'] = $isProdConflict;
					}
					//END OF DIFFERS						
					if ($productValidation['summary'] == 'false') {
						$validStatus['summary'] = 'false';																						
					}
					
					if($product['product_id'] != '') {
						if(!$isMWS) {
							$productMix = new Product($this->db);
							$productMix->initializeByID($product['product_id']);
							$recordProperties = new RecordProperties();
							$recordProperties->setQuantity($product['quantity']);
							$recordProperties->setUnitType($product['unittype']);
							$mixRecord = new MixRecord($productMix, $recordProperties);
							//waste here detected!	=>												
							if (!$validation->checkWaste($mixRecord,$wasteData['unittypeID']) && $wasteData['value'] != 0) {
								$validStatus['summary'] = 'false';
								$validStatus['waste']['convert'] = 'failed';
								$usageData['waste']['value'] = 0;
								$wasteData['value'] = 0;
							} 	
							$products[]=$product;
							
						} else {
							$products []=$product;
						}
					}
					
					$validStatus['product'][] = $productValidation;	
					//DIFFERS
					}		
					//END PF DIFFERS					
				}
				$productCount = ($products)?count($products):0;
				if ($productCount == 0 && $validStatus['summary']=='true') {
					$validStatus['summary'] = 'false';
					$validStatus['products'] = 'noProducts';
				}							
				
				$unittype = new Unittype($this->db);								
				for ($i=0; $i < $productCount; $i++) {
					$productMix = new Product($this->db);
					$productMix->initializeByID($products[$i]['product_id']);
					//DIFFERS
					$unittypeDetails[$i]=$unittype->getUnittypeDetails($products[$i]['unittype']);
					//END OF DIFFERS
					$recordProperties=new RecordProperties();
					$recordProperties->setQuantity($products[$i]['quantity']);																								
					$recordProperties->setUnitType($products[$i]['unittype']);
					$mixRecord=new MixRecord($productMix, $recordProperties);
					
					$mixRecords[]=$mixRecord;
					
				}
				//DIFFERS
				$this->smarty->assign('unitTypeName',$unittypeDetails);
				
				
				
				//END OF DIFFERS
				$equipmentMix=new Equipment($this->db);
				$equipmentMix->initializeByID($usageData['equipment_id']);
				$departmentMix=new Department($this->db);
				$departmentMix->initializeByID($usageData['department_id']);
				
				if ($isMWS) {
					//here we calculate total waste for voc calculations
					$params = array (
						'products' => $products,
						'db' => $this->db
					);
					$result = $mWasteStreams->calculateWaste($params);
					extract($result); //here extracted $wasteData, $wasteArr and $ws_error
					if ($ws_error) {
						$validStatus['summary'] = 'false';
						if ($ws_error !== true) {
							//$ws->error not 'true' its a error message!
							$validStatus['waste']['error'] = $ws_error;
						}
					}
					
					$usageData['waste'] = json_encode($wasteArr); //$wasteArr was modified(now its with validation) and extracted from $result
				}
				
				$mixProperties=new MixProperties($equipmentMix, $departmentMix, $wasteData);
				
				$mix=new Mix($mixRecords, $mixProperties);
				$mix->setDB($this->db);
				//DIFFERS IN ADD
				$mix->setCreationTime($usageData['creationTime']);	//	for MixValidator
				//END OF DIFFERS
				$mixCalcError = $mix->calculateCurrentUsage();
				
				if ($mixCalcError != null) {
					if ($mixCalcError['isDensityToVolumeError']) {
						$validStatus['conflict'] = 'density2volume';
						$validStatus['summary'] = 'false';
					}
					if ($mixCalcError['isDensityToWeightError']) {
						$validStatus['conflict'] = 'density2weight';
						$validStatus['summary'] = 'false';
					}
					$validStatus['warning'] = false;
					foreach ($mixCalcError['isVocwxOrPercentWarning'] as $productID => $productWarning) {
						if ($productWarning) {
							$validStatus['warning'] = true;
						}
					}
					if ($validStatus['warning']) {
						$validStatus['warnings2products'] = $mixCalcError['isVocwxOrPercentWarning'];
						$this->smarty->assign('validStatus',$validStatus);
					}
					if ($mixCalcError['isWastePercentAbove100']) {
						$validStatus['summary'] = 'false';
						$validStatus['waste']['percent'] = 'failed'; 
						if (!$isMWS) {
							$usageData['waste']['value'] = 0;
						}
					}
				}
				$usageData['voc'] = round($mix->getVoc(),2);
				$usageData['voclx'] = round($mix->getVoclx(),2);
				$usageData['vocwx'] = round($mix->getVocwx(),2);
				$usageData['waste_percent'] = round($mix->getWastePercent(),2);	
				
				if ($usageData['waste_percent']==0 || $usageData['waste_percent']=="") {
					$usageData['waste_percent']="0.00";
				}
				if ($usageData['voc']==0 || $usageData['voc']=="") {
					$usageData['voc']="0.00";
				}
				if ($usageData['voclx']==0 || $usageData['voclx']=="") {
					$usageData['voclx']="0.00";
				}
				if ($usageData['vocwx']==0 || $usageData['vocwx']=="") {
					$usageData['vocwx']="0.00";
				}
				//DIFFERS IN EDIT
				// check voc validation
				if (!$validation->check_vocwx($usageData['voc'])) {										
					$validStatus['summary'] = 'false';
				}
				//END OF DIFFERS
				//DIFFERS IN ADD
				$mixValidator = new MixValidator($recalc = true);
				$mixValidatorResponse=$mixValidator->isValidMix($mix);
				//END OF DIFFERS
				$productCount = ($products)?count($products):0;
				if ($validStatus['summary'] == 'true') {
					foreach($products as $product) {
						if($product['product_id'] != '') {						
							$validProducts[]=$product;
						}
					}
					$usageData['products']=$validProducts;
					
					//checkWaste to add mix		
					//dIFFERS								
					if ($action == 'addItem') {
						//	setter injection								
						$mix->setTrashRecord(new Trash($this->db));		//NEEDED?
						
						$newMixId = $mix->addNewMix($usageData,$isMWS);
						if ($isMWS) {
							$mWasteStreams->prepareSaveWasteStreams(array('id' => $newMixId, 'db' => $this->db));
							//TODO: here we should add a storage
						}
					} elseif ($action == 'edit') {
						$usages->setMixDetails($usageData,$isMWS);											
						if ($isMWS) {
							$mWasteStreams->prepareSaveWasteStreams(array('id' => $usageData['mix_id'], 'db' => $this->db));
							//TODO: herewe should add a storage
						}
						
						$mixValidator = new MixValidator($recalc = true);							
						$mixValidatorResponse = $mixValidator->isValidMix($mix);
						
					}
					//eND OF DIFFERS
					//now lets check if we need to send notifies
					$emailNotifications = new EmailNotifications($this->db);
					$emailNotifications->checkLimits($mixValidatorResponse,'department',$departmentID);
					//	redirect
					header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=mix&notify=31&notify=34");
					die();										
				} else {
					//Set Notify
						
					/* old school style */
					//$notify = new Notify($this->smarty);
					//$notify->formErrors();

					/*	the modern style */
					$notifyc = new Notify(null, $this->db);
					$notify = $notifyc->getPopUpNotifyMessage(401);
					$this->smarty->assign("notify", $notify);
					//DIFFERS
					if ($action == 'addItem') {
						$this->smarty->assign('productsAdded', $products);									
						
						$usageData['equipment_id'] = $this->getFromPost('selectEquipment');								
						$usageData['product_id'] = $this->getFromPost('selectProduct');
						
						//$usageData['validResult']=false;
						
						$usageData['quantity'] = $this->getFromPost('quantity');
						
						
						if ($productCount > 0) {
							$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
							$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
							$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
							$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
							$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
						}
						$this->smarty->assign('productCount', $productCount);
						$this->smarty->assign('currentOperation', 'addItem');
						$this->smarty->assign('validStatus', $validStatus);
						//here we send waste in dataUsage to smarty in error case										
						$this->smarty->assign('data', $usageData);
					} elseif ($action == 'edit') {
						$this->smarty->assign('validStatus', $validStatus);									
						$usageData["products"] = $products;
						$usageData['product_id'] = $this->getFromPost('selectProduct');
						$usageData['description'] = $this->getFromPost('description');
						$usageData['quantity'] = $this->getFromPost('quantity');
						$usageData['unittype'] = $this->getFromPost('selectUnittype');
						$usageData['unitTypeClass']	= $this->getFromPost('selectUnittypeClass');
						
					}
					//END OF DIFFERS
					$isDataAssigned = true;															
				}
			} else {
				
				// ------------------------A D D  P R O D U C T---------------------------------------------------------								
				
				$quantityForCheck['quantity'] = $form['quantity'];
				$validStatus = $validation->validateRegData($quantityForCheck);
				//Ksenya: we have new validation in Mix->calculateCurrentUsage 
				//but we still need those - to disallow product adding
				$isProdConflict = $validation->checkWeight2Volume($form['selectProduct'], $form['selectUnittype']);
				if ($isProdConflict !== true) {
					$validStatus['summary'] = 'false';
					$validStatus['description'] = $isProdConflict;
				}
				
				$productCount = ($form['productCount'] == "") ? 0 : $form['productCount'];
				
				$unittype=new Unittype($this->db);																
				for ($i=0;$i<$productCount;$i++) {
					if (!is_null($this->getFromPost('product_'.$i)) && trim($this->getFromPost('product_'.$i)) != "") {
						$data=$productInfo->getProductDetails($this->getFromPost('product_'.$i));
						$product=array(
							"product_id"	=>	$this->getFromPost('product_'.$i),
							"product_nr"	=>	$data['product_nr'],
							"quantity"		=>	$this->getFromPost('quantity_'.$i),
							"unittype"		=>	$this->getFromPost('unittype_'.$i),
							"unittypeClass"	=>	$this->getFromPost('selectUnittypeClass_'.$i), //diff - only in edit
							"supplier"		=>	$data["supplier"],
							"description"	=>	$data["name"]
						);
						if ($product['product_id'] != '') {	
							if(!$isMWS) {
								$productMix = new Product($this->db);
								$productMix->initializeByID($product['product_id']);
								$recordProperties = new RecordProperties();
								$recordProperties->setQuantity($product['quantity']);
								$recordProperties->setUnitType($product['unittype']);
								$mixRecord = new MixRecord($productMix, $recordProperties);
								//waste here detected!	=>												
								if (!$validation->checkWaste($mixRecord,$wasteData['unittypeID']) && $wasteData['value'] != 0) {
									$validStatus['summary'] = 'false';
									$validStatus['waste']['convert'] = 'failed';
									$usageData['waste']['value'] = 0;
									$wasteData['value'] = 0;
								} 	
								$products[]=$product;
								
							} else {
								$products []=$product;
							}				
						}					
					}
				}
				$productCount = ($products)?count($products):0;								
				
				if ($validStatus['summary'] == 'true') {
					//	selectProduct == 0 when no products at the company
					if (!is_null($this->getFromPost('selectProduct')) && ($this->getFromPost('selectProduct') != 0) ) {
						$data = $productInfo->getProductDetails($this->getFromPost('selectProduct'));										
						$product = array(
							"product_id"	=>	$this->getFromPost('selectProduct'),
							"product_nr"	=>	$data['product_nr'],
							"quantity"		=>	$this->getFromPost('quantity'),										
							"unittype"		=>	$this->getFromPost('selectUnittype'),
							"unittypeClass"	=> 	$this->getFromPost('selectUnittypeClass'), //only in edit
							"supplier"		=>	$data["supplier"],
							"description"	=> $data["name"]
						);
						$product['unitTypeList'] = $unittype->getUnittypeListDefault($this->getFromPost('selectUnittypeClass'));
						if ($product['product_id'] != '') {										
							if(!$isMWS) {
								$productMix = new Product($this->db);
								$productMix->initializeByID($product['product_id']);
								$recordProperties = new RecordProperties();
								$recordProperties->setQuantity($product['quantity']);
								$recordProperties->setUnitType($product['unittype']);
								$mixRecord = new MixRecord($productMix, $recordProperties);
								//waste here detected!	=>												
								if (!$validation->checkWaste($mixRecord,$wasteData['unittypeID']) && $wasteData['value'] != 0) {
									$validStatus['summary'] = 'false';
									$validStatus['description'] = 'wasteCalc';
									//differs in edit
									if ($action == 'edit') {
										$this->smarty->assign('validStatus', $validStatus);									
										$usageData["products"] = $products;
										$usageData['product_id'] = $_POST['selectProduct'];
										$usageData['description'] = $_POST['description'];
										$usageData['quantity'] = $_POST['quantity'];
										$usageData['unittype'] = $_POST['selectUnittype'];
										$usageData['unitTypeClass']	= $_POST['selectUnittypeClass'];
										$isDataAssigned = true;	
									}
									//end of differs
								} else {	
									$products[]=$product;
								}
							} else {
								$products []=$product;
							}
						}
						$productCount = ($products)?count($products):0;
						
					} else {
						$productCount = ($products)?count($products):0;
					}
					$usageData["products"] = $products;	 //DIFFERS IN EDIT
				} elseif($action == 'edit') {
					//Set Notify																		
					$notify = new Notify($this->smarty);
					$notify->formErrors();
					
					$this->smarty->assign('validStatus', $validStatus);									
					$usageData["products"] = $products;
					$usageData['product_id'] = $this->getFromPost('selectProduct');
					$usageData['description'] = $this->getFromPost('description');
					$usageData['quantity'] = $this->getFromPost('quantity');
					$usageData['unittype'] = $this->getFromPost('selectUnittype');
					$usageData['unitTypeClass']	= $this->getFromPost('selectUnittypeClass');
					$isDataAssigned = true;		
				}
				//DIFFERS IN ADD
			if ($action == 'addItem') {	
				for ($i=0; $i < $productCount; $i++) {
					$productMix = new Product($this->db);
					$productMix->initializeByID($products[$i]['product_id']);
					
					$recordProperties=new RecordProperties();
					$recordProperties->setQuantity($products[$i]['quantity']);							
					$recordProperties->setUnitType($products[$i]['unittype']);								
					$mixRecord=new MixRecord($productMix, $recordProperties);								
					
					$mixRecords[]=$mixRecord;
					
				}
				
				$equipmentMix = new Equipment($this->db);
				$equipmentMix->initializeByID($usageData['equipment_id']);
				$departmentMix = new Department($this->db);
				$departmentMix->initializeByID($usageData['department_id']);
				//here we add $wasteData to mixProperties 
				if ($isMWS) {
					//here we calculate total waste for voc calculations
					$params = array (
						'products' => $products,
						'db' => $this->db
					);
					$result = $mWasteStreams->calculateWaste($params);
					extract($result); //here extracted $wasteData, $wasteArr and $ws_error
					if ($ws_error) {
						$validStatus['summary'] = 'false';
						if ($ws_error !== true) {
							//$ws->error not 'true' its a error message!
							$validStatus['waste']['error'] = $ws_error;
						}
					}
					$usageData['waste'] = json_encode($wasteArr); //$wasteArr was modified(now its with validation) and extracted from $result
				}
				
				$mixProperties = new MixProperties($equipmentMix, $departmentMix, $wasteData);
				
				$mix = new Mix($mixRecords, $mixProperties);
				$mix->setDB($this->db);
				$mix->setCreationTime($usageData['creationTime']);	//	for MixValidator
				$mixCalcError = $mix->calculateCurrentUsage();
				
				if ($mixCalcError != null) {
					if ($mixCalcError['isDensityToVolumeError']) {
						$validStatus['conflict'] = 'density2volume';
						$validStatus['summary'] = 'false';
					}
					if ($mixCalcError['isDensityToWeightError']) {
						$validStatus['conflict'] = 'density2weight';
						$validStatus['summary'] = 'false';
					}
					$validStatus['warning'] = 'false';
					foreach ($mixCalcError['isVocwxOrPercentWarning'] as $id => $productWarning) {
						if ($productWarning == 'true') {
							$validStatus['warning'] = 'true';
							break;
						}
					}
					if ($validStatus['warning'] == 'true') {
						$validStatus['warnings2products'] = $mixCalcError['isVocwxOrPercentWarning'];
						$this->smarty->assign('validStatus',$validStatus);
					}
					if ($mixCalcError['isWastePercentAbove100']) {
						$validStatus['summary'] = 'false';
						$validStatus['waste']['percent'] = 'failed'; 
						if (!$isMWS) {
							$usageData['waste']['value'] = 0;
						}
					}
				}
				$usageData['voc']=$mix->getVoc();
				$usageData['voclx']=$mix->getVoclx();
				$usageData['vocwx']=$mix->getVocwx();
				$usageData['waste_percent'] = $mix->getWastePercent();	
				
				if ($usageData['waste_percent']==0 || $usageData['waste_percent']=="") {
					$usageData['waste_percent']="0.00";
				}
				if ($usageData['voc']==0 || $usageData['voc']=="") {
					$usageData['voc']="0.00";
				}
				if ($usageData['voclx']==0 || $usageData['voclx']=="") {
					$usageData['voclx']="0.00";
				}
				if ($usageData['vocwx']==0 || $usageData['vocwx']=="") {
					$usageData['vocwx']="0.00";
				}
				
				$mixValidator=new MixValidator($recalc = true);
				$mixValidatorResponse=$mixValidator->isValidMix($mix);
				
				$this->smarty->assign('productsAdded', $products);
				$this->smarty->assign('currentOperation', 'addItem');
				
				$unittype=new Unittype($this->db);
				//Ksenya:---------
				if (!$isDataAssigned) {
					$cUnitTypeEx = new Unittype($this->db);
					$unitTypeClass = $this->getFromPost('selectUnittypeClass');
					$company = new Company($this->db);
					$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
					$unittypeListDefault = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
					if (empty($unittypeListDefault)) {
						$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
					}
					$data['unitTypeClass'] = $unitTypeClass;
					$this->smarty->assign('data',$data);
				} else {
					$unitTypeClass = $cUnitTypeEx->getUnittypeClass($usageData['unittype']);
					$unittypeListDefault = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);									
					if (empty($unittypeListDefault)) {
						$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
					}
				}
				$this->smarty->assign('unittype', $unittypeListDefault);
				//---------
				
				$this->smarty->assign('unitTypeClass',$this->getFromPost('selectUnittypeClass'));
				$unittypeAssigned = true;
				
				for ($i=0; $i < count($products); $i++) {							
					$unittypeDetails[$i]=$unittype->getUnittypeDetails($products[$i]["unittype"]);															
				}
				$this->smarty->assign('unitTypeName',$unittypeDetails);												
				
				//Set Title
				//$title=new Titles($this->smarty);
				//$title->titleAddItem("usage");
				
				$productDetails = $productInfo->getProductDetails($this->getFromPost('selectProduct'));
				$usageData['product_nr']=$productDetails['product_nr'];
				$usageData['equipment_id']=$this->getFromPost('selectEquipment');							
				if ($validStatus['summary'] == 'true') {
					$usageData['description']=$this->getFromPost('description');
					$this->smarty->assign('data', $usageData);
					$this->smarty->assign('productCount', $productCount);
					$isDataAssigned = true;	
				} else {
					$usageData['product_id']=$this->getFromPost('selectProduct');
					$usageData['description']=$this->getFromPost('description');
					$usageData['quantity']=$this->getFromPost('quantity');
					$this->smarty->assign('data', $usageData);
					$this->smarty->assign('validStatus', $validStatus);
					$this->smarty->assign('productCount', $productCount);
					$isDataAssigned = true;	
				}
				
				if ($productCount > 0) {
					$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
					$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
					$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
					$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
					$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
				}
			}	
			//END OF DIFFERS							
			}					
		} 
		
		//	IF ERRORS OR NO POST REQUEST
		
		//BEGIN OF BIG DIFFERS!
		if ($action == 'addItem') {
		$request = $this->getFromRequest();
		$request['id'] = $departmentID;
		$request['parent_category'] = $this->parent_category;
		
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($departmentID);						
		$this->smarty->assign('equipment', $equipmentList);
		
		$apmethodObject = new Apmethod($this->db);
		$APMethod=$apmethodObject->getDefaultApmethodDescriptions($companyID);
		
		
		//	Get rule list
		$rule = new Rule($this->db);						
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $companyID, $departmentDetails['facility_id'], $departmentID);							
		$this->smarty->assign('rules', $customizedRuleList);
		
		//	Getting Product list
		$product = new Product($this->db);
		$productList = $product->getFormatedProductList($companyID);
		//$smarty->assign('products', $productList);
		
		//	NICE PRODUCT LIST 
		foreach ($productList as $oneProduct) {
			$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
		}
		
		$this->smarty->assign('products', $productListGrouped);
		
		$productInfo = $product->getProductInfoInMixes($productList[0]['product_id']);
		} elseif ($action == 'edit') {
															
							if (!isset($usageData)) {
								$usage = new Mix($this->db);
								$usageData = $usage->getMixDetails($this->getFromRequest('id'), true, $isMWS);
															
								//	Access control
								if (!$this->user->checkAccess('department', $usageData["department_id"])) {						
									throw new Exception('deny');
								}
							}
							
							$optMix = new MixOptimized($this->db,$this->getFromRequest("id"));
							
							//echo "<h1>Ini waste (old func)</h1>";
							//var_dump($isMWS);
							$optMix->iniWaste($isMWS);
							//var_dump($optMix);
							//exit;
							
							//echo "<h1>usageData:</h1>";
							//var_dump($usageData);
							//exit;
							
							//echo "<h1>optMix:</h1>";
							//var_dump($optMix);
							//exit;
							
							$dep = $optMix->getDepartment();
							//echo "<h1>Department Optimized:</h1>";
							//var_dump($dep);
							
							$department = new Department($this->db);
							$departmentDetails = $department->getDepartmentDetails($usageData['department_id'], true);
							
							//echo "<h1>Department</h1>";
							//var_dump($departmentDetails);
							
							
							$company = new Company($this->db);
							$companyID = $company->getCompanyIDbyDepartmentID($usageData['department_id']);						
							
							$apmethodObject = new Apmethod($this->db);
							$APMethod=$apmethodObject->getDefaultApmethodDescriptions($companyID);
							
							
							if ($productList) {
								$seleceteProductID = (isset($usageData['product_id'])) ? $usageData['product_id'] : $productList[0]['product_id'];
								$productInfo = $product->getProductInfoInMixes($seleceteProductID);
								$usageData['product_desc'] = $productInfo['desc'];
								$usageData['coating'] = $productInfo['coat'];
							}						
							
							$equipment = new Equipment($this->db);
							$equipmentList = $equipment->getEquipmentList($usageData['department_id']);
							
							// get product list
							$product = new Product($this->db);						
							$productList = $product->getFormatedProductList($companyID);

							//	NICE PRODUCT LIST 
							foreach ($productList as $oneProduct) {
								$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
							}
							
							//	Get rule list
							$rule = new Rule($this->db);
							$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $companyID, $departmentDetails['facility_id'], $usageData['department_id']);

							//echo "<h2>rules:</h2>";
							//var_dump($customizedRuleList);
							//$this->smarty->assign('rules', $customizedRuleList);
							
							$unittype = new Unittype($this->db);						
							if (isset($_POST['selectUnittypeClass'])) {
								$unittypeList = $unittype->getUnittypeListDefault($_POST['selectUnittypeClass']);	
							} else {
								$unittypeList = $unittype->getUnittypeListDefault();
							}						
							//Ksenya:---------
							if (!$isDataAssigned) {
								
								//$company = new Company($this->db);
								//$companyID = $company->getCompanyIDbyDepartmentID($request['departmentID']);								
								$cUnitTypeEx = new Unittype($this->db);
								if (isset($_POST['selectUnittypeClass'])) {
								$unitTypeClass = $_POST['selectUnittypeClass'];
								} else {
								$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
								if ($unitTypeEx === null) {
									$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
								}
								$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
								}

								$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
								
								$data['unitTypeClass'] = $unitTypeClass;
								//echo "<h2>data:</h2>";
								//var_dump($data);
								//var_dump($data);
								$this->smarty->assign('data',$data);
							} else {
								$cUnitTypeEx = new Unittype($this->db);
								if (isset($usageData['unittype'])) {
								$unitTypeClass = $cUnitTypeEx->getUnittypeClass($usageData['unittype']);
								} else {
									$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
									$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);								
								}
								$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);									
							}
							//---------
							//$_SESSION['equipmentIDWhereProductsFrom']=$categoryDetails['equipment_id'];
							
							//exit;
							for ($i=0; $i < count($usageData["products"]); $i++) {
								$product = $usageData["products"][$i];
								$unittypeDetails[$i] = $unittype->getUnittypeDetails($product["unit_type"]);
								$usageData["products"][$i]['unittypeClass'] = $unittype->getUnittypeClass($product["unittype"]);
								$usageData["products"][$i]['unitTypeList'] = $unittype->getUnittypeListDefaultByCompanyID($companyID, $usageData["products"][$i]['unittypeClass']);
								
								$productMix = new Product($this->db);
								$productMix->initializeByID($product['product_id']);
								$recordProperties = new RecordProperties();
								$recordProperties->setQuantity($product['quantity']);
								$recordProperties->setUnitType($product['unittype']);
								$mixRecord = new MixRecord($productMix, $recordProperties);

								$mixRecords[]=$mixRecord;																						
							}
							

							//echo "<h2>unitTypeDetails:</h2>";
							//var_dump($unittypeDetails);
							
							$mixManager = new MixManager($this->db);
							$mixManager->fillProductsUnitTypes($optMix->products);
							
							//var_dump($optMix->products);
							
							/** Collect unittypeDetails for smarty **/
							foreach($optMix->products as $p) {
								
								$unittypeDetails2[] = $p->unittypeDetails;
							}
							//var_dump($unittypeDetails2);
							//exit;
							
							$this->smarty->assign('unitTypeName',$unittypeDetails2);																											
							
							$equipmentMix = new Equipment($this->db);
							$equipmentMix->initializeByID($usageData['equipment_id']);
							$departmentMix = new Department($this->db);
							$departmentMix->initializeByID($usageData['department_id']);

//waste here detected!	=>	|
							//***VOC Calculations***
							if ($form['save'] != "Save") {//if ($form['save'] == "Save") we already calculate voc
								if ($isMWS) {
									//here we calculate total waste for voc calculations
									$params = array (
										'products' => $usageData["products"],
										'db' => $this->db
									);
									
									$result = $mWasteStreams->calculateWaste($params);
									var_dump($result);
									//exit;
									extract($result); //here extracted $wasteData, $wasteArr and $ws_error
									if ($ws_error) {
										$validStatus['summary'] = 'false';
										if ($ws_error !== true) {
											//$ws->error not 'true' its a error message!
											$validStatus['waste']['error'] = $ws_error;
										}
									}
									//echo "<h1>Waste Result:</h1>"; //TODO :Waste json
									//var_dump($result);
									
									$usageData['waste'] = json_encode($wasteArr); //$wasteArr was modified(now its with validation) and extracted from $result
									//var_dump($usageData['waste']);
									//exit;
								} else {
									$wasteData = $usageData['waste'];
								}
								$mixProperties = new MixProperties($equipmentMix, $departmentMix, $wasteData);
							
								$mixID = $_GET['id'];
								$mix = new MixOptimized($this->db,$mixID);
								
								$mixCalcError = $mix->calculateCurrentUsage();
								
								
								
								//var_dump($mixCalcError);
								//var_dump($mixRecords);
								//var_dump($mixProperties);
	
								$mix = new Mix($mixRecords, $mixProperties);
								$mix->setDB($this->db);
								$mix->setCreationTime($usageData['creationTime']);
								
								
								
								$mixCalcError = $mix->calculateCurrentUsage();
								
								if ($mixCalcError != null) {
									if (!isset($validStatus['summary'])) {
										$validStatus['summary'] = 'success';
									}
									if ($mixCalcError['isDensityToVolumeError']) {
										$validStatus['conflict'] = 'density2volume';
										$validStatus['summary'] = 'false';
									}
									if ($mixCalcError['isDensityToWeightError']) {
										$validStatus['conflict'] = 'density2weight';
										$validStatus['summary'] = 'false';
									}
									$validStatus['warning'] = false;
									foreach ($mixCalcError['isVocwxOrPercentWarning'] as $productID => $productWarning) {
										if ($productWarning) {
											$validStatus['warning'] = true;
										}
									}
									if ($validStatus['warning']) {
										$validStatus['warnings2products'] = $mixCalcError['isVocwxOrPercentWarning'];
									}
									if ($mixCalcError['isWastePercentAbove100']) {
										$validStatus['summary'] = 'false';
										$validStatus['waste']['percent'] = 'failed'; 
										if (!$isMWS) {
											$usageData['waste']['value'] = 0;
										}
									}
									//echo "<h2>validStatus:</h2>";
									//var_dump($validStatus);
									$this->smarty->assign('validStatus',$validStatus);
								}							
							}							
							//***End of VOC Calculations****
							
							//$mixValidator = new MixValidator($recalc = true);							
							//$mixValidatorResponse = $mixValidator->isValidMix($mix);
							
							$mixValidatorOpt = new MixValidatorOptimized($recalc = true);
							$mixValidatorResponseOpt = $mixValidatorOpt->isValidMix($optMix);
							
							//echo "<h2>validator responce:</h2>";
							//var_dump($mixValidatorResponse);
							
							//echo "<h2>validator responce OPT:</h2>";
							//var_dump($mixValidatorResponseOpt);
							
							$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponseOpt->isDailyLimitExceeded());
							$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponseOpt->isDepartmentLimitExceeded());
							$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponseOpt->isFacilityLimitExceeded());
							$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponseOpt->getDepartmentAnnualLimitExceeded());
							$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponseOpt->getFacilityAnnualLimitExceeded());
							
							$usageData['voc'] = round($mix->getVoc(),2);
							$usageData['voclx'] = round($mix->getVoclx(),2);
							$usageData['vocwx'] = round($mix->getVocwx(),2);
							$usageData['waste_percent'] = round($mix->getWastePercent(),2);	
							
							if ($usageData['waste_percent']==0 || $usageData['waste_percent']=="") {
								$usageData['waste_percent']="0.00";
							}							
							if ($usageData['voc']==0 || $usageData['voc']=="") {
								$usageData['voc']="0.00";
							}
							if ($usageData['voclx']==0 || $usageData['voclx']=="") {
								$usageData['voclx']="0.00";
							}
							if ($usageData['vocwx']==0 || $usageData['vocwx']=="") {
								$usageData['vocwx']="0.00";
							}							
							
							//echo "<h2>usageData----:</h2>";
							//var_dump($usageData);
							
							//echo "<h2>mixOpt:</h2>";
							//var_dump($optMix);
							//exit;
							
							//echo "<h2>equipment:</h2>";
							//var_dump($equipmentList);
							
							//echo "<h2>products:</h2>";
							//var_dump($data);
							
							//echo "<h2>unittype:</h2>";
							//var_dump($unittypeList);
							
							//echo "<h2>productCount:</h2>";
							//var_dump(count($usageData['products']));
							
							//echo "<h2>productsAdded:</h2>";
							//var_dump($usageData['products']);
							
							//echo "<h2>APMethod:</h2>";
							//var_dump($APMethod);
							
							
							//exit;
							
							
							
							$this->smarty->assign('data', $optMix);
							$isDataAssigned = true;
							$this->smarty->assign('equipment', $equipmentList);
							
							$this->smarty->assign('products', $productListGrouped);
							
							$this->smarty->assign('unittype', $unittypeList);
							$this->smarty->assign('productCount',count($usageData['products']));
							//var_dump($usageData['products'][0]);
							//exit;						
							$this->smarty->assign('productsAdded',$usageData['products']);
							
							
							$this->smarty->assign('APMethod',$APMethod);
							
							
							//$this->smarty->assign('defaultAPMethod',$defaultAPMethod);
	}
		//END OF BIG DIFFERS!!
		echo "<h1>END OF BIG DIFFERS!!</h1>";
		// get Default Types and UnitTypes
		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		$companyEx = 1;
		if (!$unitTypeEx) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
			$companyEx = 0;
		}
		
		$k = 1;
		$count = 1;
		$flag = 1;
		$typeEx = Array();
		$typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		
		while ($unitTypeEx[$k]){
			$idn = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$k]['unittype_id']);
			
			for($j=0; $j < $count; $j++) {
				if ($idn == $typeEx[$j] ) {
					$flag=0;
					break;
				}
			}
			if ($flag) {
				$typeEx[$count] = $idn;
				$count++;
			}
			$k++;
			$flag = 1;
		}
		//var_dump($companyEx);
		//exit;
		
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('companyID', $companyID);
	//DIFFERS IN ADD
	if ($action == 'addItem') {
		$this->smarty->assign('APMethod',$APMethod);
		$this->smarty->assign('defaultAPMethod',$defaultAPMethod);
		if ($this->smarty->_tpl_vars['data']['waste'] === null) {
			$data['waste'] = 'false';
			$this->smarty->assign('data',$data);
		}					
		if (!isset($usageData)) {
			$data['product_desc'] = $productInfo['desc'];
			$data['coating'] = $productInfo['coat'];
			
			$data['voc']			= '0.00';
			$data['voclx']			= '0.00';
			$data['vocwx']			= '0.00';
			$data['creationTime'] 	= date("m-d-Y");
			
			$usage = new Mix($this->db);
			if (!$isMWS) {
				$data['waste'] = $usage->getWasteDetails();
				var_dump($data['waste']);
			}
			$this->smarty->assign('data', $data);																
		}
		
		if (!$unittypeAssigned) {
			$unittype = new Unittype($this->db);
			//								$unittypeList = $unittype->getUnittypeListDefault();
			//Ksenya:---------
			if (!$isDataAssigned) {
				$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
				$unittypeListDefault = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
				if (empty($unittypeListDefault)) {
					$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
				}
				//	var_dump($unittypeList);
				$data['unitTypeClass'] = $unitTypeClass;
				$this->smarty->assign('data',$data);
			} else {
				$unitTypeClass = $cUnitTypeEx->getUnittypeClass($usageData['unittype']);
				$unittypeListDefault = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);									
				if (empty($unittypeListDefault)) {
					$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
				}
			}
			$this->smarty->assign('unittype', $unittypeListDefault);
			//---------
		}									
	}
		//END OF DIFFERS

		//	set js scripts			
		$jsSources = array (										
		    'modules/js/flot/jquery.flot.js',
			'modules/js/addUsage.js',
	    	'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'							
	    );
	    $this->smarty->assign('jsSources',$jsSources);
	    
	    $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
			
		/*$jsSources = array(									
			'modules/js/addUsage.js'							
		);							
		
		$this->smarty->assign('jsSources', $jsSources);		*/				
		$this->smarty->assign('sendFormAction', '?action='.$action.'&category='.$this->category.(($action == 'addItem')?'&departmentID='.$departmentID:'&id='.$this->getFromRequest('id')));
		$this->smarty->assign('tpl', 'tpls/addUsageNew.tpl');	
		$this->smarty->display("tpls:index.tpl");
	}
}
?>