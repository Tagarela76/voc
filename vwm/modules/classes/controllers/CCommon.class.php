<?php

class CCommon extends Controller
{
    function CCommon($smarty,$xnyo,$db,$user,$action) {
    	parent::Controller($smarty,$xnyo,$db,$user,$action);    				
    }
    
    function runAction()
	{				
		try{
			
		$this->runCommon();
		}catch (Exception $e){
			throw new Exception("My Defined Exception! $e");
		}
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))
		{			
			$this->$functionName();
		}
		else 
			throw new Exception('404');
	}
	
	
	/**
	 * 
	 * Refresh usage stats. Useful when unsync happens
	 */
	private function actionPersic2()
	{
		$query = 'TRUNCATE TABLE '.TB_USAGE_STATS.'';
		$this->db->exec($query);
					
		$department = new Department($this->db);
		$query = "SELECT * FROM mix";
		$this->db->query($query);					
		$mixList = $this->db->fetch_all();
		foreach ($mixList as $mix) 
		{
			$mixCreationMonth = substr($mix->creation_time,5,2);
			$mixCreationYear = substr($mix->creation_time,0,4);						
			$department->incrementUsage($mixCreationMonth, $mixCreationYear, $mix->voc, $mix->department_id);	
		}
	}	
	
	private function actionLedokol()
	{
		$query = 	"INSERT INTO emission_factor (name, unittype_id, emission_factor) VALUES " .
					"('Aviation spirit', 5, 3128), " .
					"('Aviation turbine fuel', 5, 3150), " .
					"('Blast furnace gas', 34, 0.97), " .
					"('Burning oil/kerosene/paraffin', 4, 2.518), " .
					"('Coke oven gas', 34, 0.15), " .
					"('Coking coal', 5, 2810), " .
					"('Colliery methane', 34, 0.18), " .
					"('Diesel', 4, 2.630), " .
					"('Fuel oil', 5, 3223), " .
					"('Gas oil', 4, 2.674), " .
					"('Industrial coal', 5, 2457), " .
					"('Liquid petroleum gas (LPG)', 4, 1.495), " .
					"('Lubricants', 5, 3171), " .
					"('Waste', 5, 275), " .
					"('Naphtha', 5, 3131), " .
					"('Natural gas', 34, 0.185), " .
					"('Other petroleum gas', 34, 0.21), " .
					"('Petrol', 4, 2.315), " .
					"('Petroleum coke', 5, 3410), " .
					"('Refinery miscellaneous', 34, 0.245), " .
					"('Scrap tyres', 5, 2003), " .
					"('Solid smokeless fuel', 5, 2810), " .
					"('Sour gas', 34, 0.24), " .
					"('Waste solvents', 5, 1597), " .
					"('Electricity', 34, 0.537) ";	
										
		$this->db->exec($query);			
	}
	
	private function actionStorageDensity()
	{
		//recalc density for waste storages!!
		$query = "SELECT * FROM `storage` "."WHERE density_unit_id IS NULL ";
		$this->db->query($query);
		$data = $this->db->fetch_all();//var_dump($query);var_dump($db);
		$densityObj = new Density($this->db,1); // 1 - density_unit_id for default
		$unittype = new Unittype($this->db);
		$unittypeConverter = new UnitTypeConverter();
		$weightUnittype = $unittype->getDescriptionByID($densityObj->getNumerator());
		$volumeUnittype = $unittype->getDescriptionByID($densityObj->getDenominator());
		$done = 0; $failed = 0;
		foreach ($data as $record)
		{
			$weight = $unittypeConverter->convertFromTo($record->capacity_weight,$unittype->getDescriptionByID($record->weight_unittype),$weightUnittype);
			$volume = $unittypeConverter->convertFromTo($record->capacity_volume,$unittype->getDescriptionByID($record->volume_unittype),$volumeUnittype);
			$density = $weight/$volume;//var_dump(round($density,4));
			$query = "UPDATE `storage` SET density='".round($density,4)."', density_unit_id='1' WHERE storage_id='$record->storage_id' ";
			if($this->db->query($query)) 
			{
				$done++;
			} 
			else 
			{
				$failed++;
			}
		}
		echo "<p>Calculated density: ".$done."</p><p>Failed: ".$failed."</p>";
	}
	
	private function actionSendSubReport()
	{
		$request = $this->getFromRequest();
		$title = new TitlesNew($this->smarty, $this->db);
		
		$title->getTitle($request);	
					
		switch ($request['itemID']) 
		{
			case 'company':
				$companyID = $request['id'];
				break;
			case 'facility':
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($request['id']);
				$companyID = $facilityDetails['company_id'];
				break;
			case 'department':
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($request['id']);
				break;
		}
						
		$reportType = $request['reportType'];					
					
		if (!$this->user->checkAccess('reports', $companyID)) 
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
					
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mReport = new $moduleMap['reports'];
		$params = array(
						'db' => $this->db,								
						'xnyo' => $this->xnyo,
						'companyID' => $companyID,
						'request' => $request
						);
		$report = $mReport->prepareSendSubReport($params);
	}
		
	private function actionMsdsUploaderBasic()
	{
		//little hack
		$request = array('category'=>$this->getFromRequest('itemID'), 'id'=>$this->getFromRequest('id'));
		$this->smarty->assign('request', $request);
		$cfd = $this->noname($request, $this->user, $this->db, $this->smarty);	
					
		$step = 'assign';
										
		$msds = new MSDS($this->db);					
		$result = $msds->upload('basic');
		
		

		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);
		$request = $this->getFromRequest();
		$title->getTitle($request);	
						
		$product = new Product($this->db);
		$recognized = array();
		$unrecognized = array();
		foreach($result['msdsResult'] as $msdsResult)
		{
			if ($msdsResult['isRecognized']) 
			{
				$recognized[] = $msdsResult;							
			} 
			else 
			{
				$unrecognized[] = $msdsResult;
			}						
		}
		
		//errors
		foreach($result['filesWithError'] as $fileWithError)
		{
			$failedSheet['msdsName'] = $fileWithError['name'];
			$failedSheet['reason'] = $fileWithError['error'];										
			$failedSheets[] = $failedSheet;
		}
					
		$cntFailed = count($failedSheets);
		$this->smarty->assign('cntFailed', $cntFailed);
		$this->smarty->assign('failedSheets', $failedSheets);
					
		$productList = $product->getFormatedProductList($cfd['companyID']);
		//	NICE PRODUCT LIST 
		foreach ($productList as $oneProduct) 
		{
			$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
		}											
		//$smarty->assign('productList', $productList);
		$this->smarty->assign('productList', $productListGrouped);
					
		$cnt['recognized'] = count($recognized);
		$cnt['unrecognized'] = count($unrecognized);
		$maxCnt = max($cnt); 					
		$this->smarty->assign('cnt', $cnt);					
		$this->smarty->assign('maxCnt', $maxCnt);
		$this->smarty->assign('recognized', $recognized);
		
		$this->smarty->assign('unrecognized', $unrecognized);
					
//		$title = new Titles($smarty);
//		$title->titleMsdsUploader($step,"Basic");
					
		$this->smarty->assign('step', $step);
		$this->smarty->display('tpls:msdsUploader.tpl');		
	}
	
	private function actionMsdsUploader()
	{
		if ($this->getFromRequest('button') != "Back") 
		{
			$step = $this->getFromRequest('step');
		} 
		else 
		{
			$step = "main";
		}																										
		//fullNavigation($_GET['itemID'], $user, $db, $smarty, $xnyo);
		//little hack
		$request = array('category'=>$this->getFromRequest('itemID'), 'id'=>$this->getFromRequest('id'));
		$this->smarty->assign('request', $request);
		$cfd = $this->noname($request);	
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);					
		$title->getTitle($this->getFromRequest());	
						
		switch ($step) 
		{					
			case "main":
				if ($this->getFromRequest('basic') == "yes") 
				{								
					$this->smarty->assign("basic","yes");											
				}
				else 
				{
					$this->smarty->assign("basic","no");
								
					//	Set company ID
					$userDetails = $this->user->getUserDetails($_SESSION['user_id'], true);					
					$companyID = ($userDetails['accesslevel_id'] != 3) ? $userDetails['company_id'] : 0;															
					$this->smarty->assign("companyID",$companyID);
								
					//	If sandbox then use special URL
					/*if (REGION !== DEFAULT_REGION) {
						$swfUrl = (ENVIRONMENT == "server") ? "modules/flash/".REGION."/uploader.swf?companyID=".$companyID
																		: "modules/flash/".REGION."/sandbox/uploader.swf?companyID=".$companyID;
					} else {
						$swfUrl = (ENVIRONMENT == "server") ? "modules/flash/uploader.swf?companyID=".$companyID
																		: "modules/flash/sandbox/uploader.swf?companyID=".$companyID;
					}*/
					$voc2vps = new VOC2VPS($this->db);
					$customerLimits = $voc2vps->getCustomerLimits($companyID);
					
					$swfUrl = "modules/flash/uploader.swf?companyID=".$companyID .
							"&memoryLimit=".$customerLimits['memory']['current_value'] .
							"&MSDSLimit=".$customerLimits['MSDS']['current_value'] .
							"&memoryMaxLimit=".$customerLimits['memory']['max_value'] .
							"&MSDSMaxLimit=".$customerLimits['MSDS']['max_value'];
								
					$this->smarty->assign("swfUrl", $swfUrl);
				}
				$this->smarty->assign("step",$step);
				$this->smarty->display("tpls:msdsUploader.tpl");
							break;
							
			case "save":
				//recognized sheets
				$cnt['recognized'] = $this->getFromRequest('sheetRecCount');						
				for ($i=0;$i<$cnt['recognized'];$i++) 
				{					
					$assignment['msdsName'] = $this->getFromRequest('sheetRec_'.$i);
					$assignment['realName'] = $this->getFromRequest('sheetRecRealName_'.$i);
					$assignment['failed'] = FALSE;							
					if(!is_null($this->getFromRequest('product2sheetRec_'.$i)))
					{
						$assignment['productID'] = $this->getFromRequest('product2sheetRec_'.$i);								
					} 
					else 
					{
						$assignment['productID'] = NULL;								
					}														
					$assignments[] = $assignment;
				}
				
				//unrecognized sheets
				$cnt['unrecognized'] = $this->getFromRequest('sheetUnrecCount');						
				for ($i=0;$i<$cnt['unrecognized'];$i++) 
				{						
					$assignment['msdsName'] = $this->getFromRequest('sheetUnrec_'.$i);
					$assignment['realName'] = $this->getFromRequest('sheetUnrecRealName_'.$i);
					$assignment['failed'] = FALSE;
					if(!is_null($this->getFromRequest('product2sheetUnrec_'.$i)))
					{
						$assignment['productID'] = $this->getFromRequest('product2sheetUnrec_'.$i);
					} 
					else 
					{
						$assignment['productID'] = NULL;					
					}								
						$assignments[] = $assignment;																		
				}
							
				//getting company/facilty/department id						
				$save['companyID'] = $cfd['companyID']; 		
				$save['facilityID'] = $cfd['facilityID'];
				$save['departmentID'] = $cfd['departmentID'];;
							
				$msds = new MSDS($this->db);
				$result = $msds->validateAssignments($assignments);						
							
				for ($i=0;$i<count($result);$i++) 
				{
					if($result[$i]['status'] == "ok") 
					{
						$msdsArray['name'] = $assignments[$i]['msdsName'];
						$msdsArray['real_name'] = $assignments[$i]['realName'];
						$msdsArray['size'] = filesize("../msds/".$msdsArray['real_name']);
						$msdsArray['productID'] = $assignments[$i]['productID'];
						$save['msds'][] = $msdsArray;																
					} 
					else 
					{								
						$failedSheet['msdsName'] = $result[$i]['msdsName'];
						switch ($result[$i]['reason'])
						{
							case "alreadyAssigned":
								$failedSheet['reason'] = "This product is already assigned to other MSDS sheet.";
								break;
							case "multiple":
								$failedSheet['reason'] = "More than one sheet is assigned to one product.";
								break;
						}								
						$assignments[$i]['failed'] = TRUE;
						$failedSheets[] = $failedSheet;
					}
				}
							
				if ($failedSheets) 
				{	//back to assign step	
					$step = "assign";											
					foreach($assignments as $assignment) 
					{
						if (!empty($assignment['productID']))
						{
							$sheet['name'] = $assignment['msdsName'];
							$sheet['real_name'] = $assignment['realName'];
							$sheet['product_id'] = $assignment['productID'];
							$sheet['failed'] = $assignment['failed'];
							$recognized[] = $sheet;
						} 
						else 
						{
							$sheet['name'] = $assignment['msdsName'];
							$sheet['real_name'] = $assignment['realName'];									
							$unrecognized[] = $sheet;
						}
					}
					$this->smarty->assign('recognized',$recognized);
					$this->smarty->assign('unrecognized',$unrecognized);						
								
					$cnt['recognized'] = count($recognized);
					$cnt['unrecognized'] = count($unrecognized);
					$maxCnt = max($cnt); 					
					$this->smarty->assign('cnt', $cnt);					
					$this->smarty->assign('maxCnt', $maxCnt);
								
					$product = new Product($this->db);																		
					$productList = $product->getFormatedProductList($cfd['companyID']);	
					//	NICE PRODUCT LIST 
					foreach ($productList as $oneProduct) 
					{
						$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
					}							
					//$smarty->assign('productList', $productList);				
					$this->smarty->assign('productList', $productListGrouped);
								
					$cntFailed = count($failedSheets);
					$this->smarty->assign('cntFailed',$cntFailed);
					$this->smarty->assign('failedSheets',$failedSheets);
								
//					$title = new Titles($smarty);
//					$title->titleMsdsUploader($step,"Basic");
								
					$this->smarty->assign('step', 'assign');
					$this->smarty->display('tpls:msdsUploader.tpl');
				} 
				else 
				{ // finish upload
					$msds->addSheets($save);
								
					//save vps limits
					$userDetails = $this->user->getUserDetails($_SESSION['user_id'], true);
					$companyID = ($userDetails['accesslevel_id'] != 3) ? 0 : $userDetails['company_id'];										
					if ($userDetails['accesslevel_id'] != 3) 
					{
						$voc2vps = new VOC2VPS($this->db);
						$customerLimits = $voc2vps->getCustomerLimits($userDetails['company_id']);
									
						$MSDSLimit = array (
											'limit_id' 		=> 1,
											'current_value' => $customerLimits['MSDS']['current_value']+count($save['msds']),
											'max_value' 	=> $customerLimits['MSDS']['max_value']
											);
						$voc2vps->setCustomerLimitByID($userDetails['company_id'], $MSDSLimit);
									
						$totalSize = 0;
						foreach ($save['msds'] as $file) 
						{
							$totalSize += $file['size'];
						}								
						$sizeMb = round($totalSize/1024/1024,2);								
						$memoryLimit = array (
												'limit_id' => 2,
												'current_value' => $customerLimits['memory']['current_value']+$sizeMb,
												'max_value' => $customerLimits['memory']['max_value']
											);
						$voc2vps->setCustomerLimitByID($userDetails['company_id'], $memoryLimit);									
					}							
								
					//going back						
					if (!empty($save['departmentID'])) 
					{						
						header("Location: ?action=browseCategory&category=department&id=".$save['departmentID']."&bookmark=mix");
					} 
					elseif (!empty($save['facilityID'])) 
					{		
						header("Location: ?action=browseCategory&category=facility&id=".$save['facilityID']."&bookmark=department");
					} 
					elseif (!empty($save['companyID'])) 
					{	
						header("Location: ?action=browseCategory&category=company&id=".$save['companyID']."");
					} 
					elseif ($this->getFromRequest('category') == 'root') 
					{	
						header("Location: ?action=browseCategory&category=root");
					}							
				}																											
				break;
						
			case "edit":
				$productID = $this->getFromRequest('productID');
							
				$product = new Product($this->db);
				$productDetails = $product->getProductDetails($productID);
				$this->smarty->assign('productDetails',$productDetails);						
				$msds = new MSDS($this->db);
				$unlinkedMsdsSheets = $msds->getUnlinkedMsdsSheets();
				$this->smarty->assign('unlinkedMsdsSheets',$unlinkedMsdsSheets);
							
//				$title = new Titles($smarty);
//				$title->titleEditItem("MSDS Sheets");
							
				$this->smarty->assign('step', 'edit');
				$this->smarty->display('tpls:msdsUploader.tpl');
				break;
							
			case "saveEdit":							
				$selectedSheetID = $this->getFromRequest('selectedSheet');
				$productID = $this->getFromRequest('productID');
							
				$msds = new MSDS($this->db);
				$msds->linkSheetToProduct($selectedSheetID, $productID);
							
				$product = new Product($this->db);
				$productDetails = $product->getProductDetails($productID);
							
				$notify=new Notify($this->smarty);
				$notify->successEdited("product", $productDetails['product_nr']);
							
				//showCategory("product", $_GET['id'], $db, $xnyo, $smarty, $user);
				header("Location: ?action=browseCategory&category=department&id=".$this->getFromRequest('id')."&bookmark=product");
				break;
		}											
	}
	
	private function actionLogout() {
		$this->user->logout();
	}	
}
?>
