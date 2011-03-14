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
		//if (!isset($this->getFromRequest('departmentID')) || empty($request['departmentID'])) {
		//	$request['departmentID'] = $usageDetails['department_id'];
		//}				
							//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}					
//																				
//		$usage = new Mix($db);						
//		$usageDetails = $usage->getMixDetails($request["id"]);
		$rule = new Rule($this->db);
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
		}
		$this->smarty->assign("usage", $usageDetails);

		$apMethodObject = new Apmethod($this->db);
		$apMethodDetails =$apMethodObject->getApmethodDetails($usageDetails["apmethod_id"]);
		$this->smarty->assign('apMethodDetails',$apMethodDetails);											
		$usageDetails = $usage->getMixDetails($this->getFromRequest('id'), true);
		$this->smarty->assign("productCount", count($usageDetails["products"]));				
							
		$unittype = new Unittype($this->db);
		//	TODO: что за хрень с рулами?																				
		$k = 0; 
		for ($i = 0; $i < count($usageDetails["products"]); $i++) 
		{
			$product = $usageDetails["products"][$i];
			$productMix = new Product($this->db);
			$productMix->initializeByID($product['product_id']);							
			$unittypeDetails[$i] = $unittype->getUnittypeDetails($usageDetails["products"][$i]["unittype"]);
			$productDetails[$i] = $productMix->getProductDetails($product['product_id']);
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
							
		$mix = new Mix($this->db);
		$mix->initializeByID($this->getFromRequest('id'));
							
		$mixValidator=new MixValidator();
		$mixValidatorResponse=$mixValidator->isValidMix($mix);	
							
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		$companyDetails = $company->getCompanyDetails($companyID);							
		$this->smarty->assign('companyDetails',$companyDetails);					
							
		$this->smarty->assign('unittypeObj',$unittype);

		$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
		$this->smarty->assign('expired', $mixValidatorResponse->isExpired());
		$this->smarty->assign('preExpired', $mixValidatorResponse->isPreExpired());
				
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
											
			//================	Finish MIX'es highlitings
											
			$this->smarty->assign('childCategoryItems', $usageList);
											
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
	
		
	private function actionAddItem() {
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}			
			
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		
		$this->addEdit('addItem', $this->getFromRequest('departmentID'));
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
		
		$this->addEdit('edit', $departmentID);
	}
	
	private function addEdit($action, $departmentID) {
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
			//								$ws = $mWasteStreams->getNewObject($this->db);
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
							$department = new Department($this->db);
							$departmentDetails = $department->getDepartmentDetails($usageData['department_id'], true);
							
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
							$this->smarty->assign('rules', $customizedRuleList);
							
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
							
							for ($i=0; $i < count($usageData["products"]); $i++) {
								$product = $usageData["products"][$i];
								$unittypeDetails[$i] = $unittype->getUnittypeDetails($product["unittype"]);
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

							$this->smarty->assign('unitTypeName',$unittypeDetails);																											
							
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
									extract($result); //here extracted $wasteData, $wasteArr and $ws_error
									if ($ws_error) {
										$validStatus['summary'] = 'false';
										if ($ws_error !== true) {
											//$ws->error not 'true' its a error message!
											$validStatus['waste']['error'] = $ws_error;
										}
									}
									
									$usageData['waste'] = json_encode($wasteArr); //$wasteArr was modified(now its with validation) and extracted from $result
									var_dump($usageData['waste']);
									var_dump($wasteArr);
								} else {
									$wasteData = $usageData['waste'];
								}
								$mixProperties = new MixProperties($equipmentMix, $departmentMix, $wasteData);
							
	
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
									$this->smarty->assign('validStatus',$validStatus);
								}							
							}							
							//***End of VOC Calculations****
							
							$mixValidator = new MixValidator($recalc = true);							
							$mixValidatorResponse = $mixValidator->isValidMix($mix);
							
							$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
							$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
							$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
							$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
							$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
							
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
							
							
							$this->smarty->assign('data', $usageData);
							$isDataAssigned = true;
							$this->smarty->assign('equipment', $equipmentList);
							$this->smarty->assign('products', $productListGrouped);
							$this->smarty->assign('unittype', $unittypeList);
							$this->smarty->assign('productCount',count($usageData['products']));						
							$this->smarty->assign('productsAdded',$usageData['products']);
							$this->smarty->assign('APMethod',$APMethod);
							//$this->smarty->assign('defaultAPMethod',$defaultAPMethod);
	}
		//END OF BIG DIFFERS!!
		
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