<?php

class CAAccessory extends Controller {
	
	function CAAccessory($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='accessory';
		$this->parent_category='accessory';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	private function actionBrowseCategory() {
	
		$sortStr=$this->sortList('accessory',3);		
		$accessory = new Accessory($this->db);
		$product = new Product($this->db);
		$itemsCount = $accessory->queryTotalCount();	
		
		$pagination = new Pagination($itemsCount);
		$pagination->url = "?action=browseCategory&category=accessory";

		$subaction = $this->getFromRequest('subaction');
		$jobberID = $this->getFromRequest('jobberID');	
		$jobberID = (is_null($jobberID) || $jobberID == 'All jobbers')?0:$jobberID;
		
		// search
		if (!is_null($this->getFromRequest('q'))) 
		{
			$accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$accessoryList = $accessory->searchAccessory($accessoryToFind,$jobberID,$pagination);																						
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} 
		else 
		{
			$accessoryList = $accessory->getAllAccessory($jobberID,$sortStr,$pagination);
		}

		if($accessoryList) {	
			for ($i=0; $i<count($accessoryList); $i++) 
				{
					$url="?action=viewDetails&category=accessory&id=".$accessoryList[$i]['id'];
					$accessoryList[$i]['url']=$url;
				}
		}else{
			$itemsCount = 0;
		}	

		$this->smarty->assign('currentJobber',$jobberID);
		
		$jobberManager = new JobberManager($this->db);
		$jobbersList = $jobberManager->getJobberList();
		$this->smarty->assign("jobbers",$jobbersList);
	
		if (!is_null($subaction) && $jobberID != 0 && $subaction != 'Filter') {
			
			$id = $this->getFromRequest('id');
			for ($i=0;$i<count($id);$i++) {										
				if (!is_null($id[$i])) {
					$GOMID = $id[$i];
					if ($subaction == "Assign to jobber") {
						$product->assignGOM2Jobber($GOMID, $jobberID);	
					} elseif ($subaction == "Unassign GOM(s)") {
						$product->unassignGOMFromJobber($GOMID, $jobberID);
					}											
				}	
			}
			// redirect
			header("Location: ?action=browseCategory&category=accessory&subaction=Filter&jobberID={$jobberID}");
			die();			
		}

		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("childCategoryItems",$accessoryList);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/accessoryClass.tpl');
		$this->smarty->assign('pagination', $pagination);
		
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionViewDetails() {
				
							
		$accessory = new Accessory($this->db);
		$accessory->setAccessoryID($this->getFromRequest("id"));
		$accessoryDetails = $accessory->getAccessoryDetails();
		$accessoryUsages = $accessory->getAccessoryUsages($this->getFromRequest("id"));
		
		$this->smarty->assign("accessory", $accessoryDetails);
		$this->smarty->assign("accessoryUsages", $accessoryUsages);
							
		$this->setNavigationUpNew('department', $this->getFromRequest("departmentID"));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest("departmentID"),array('bookmark'=>'accessory'));
		$this->setPermissionsNew('viewData');

		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
							
		
		$this->smarty->assign('editUrl','?action=edit&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('addUsageUrl','?action=addUsage&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('deleteUrl','?action=deleteItem&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));	
		$this->smarty->assign('backUrl','?action=browseCategory&category=accessory');
		$this->smarty->assign('tpl','tpls/viewAccessory.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$request=$this->getFromRequest();
		$accessory = new Accessory($this->db);

		
						
		$form = $this->getFromPost();
							
		if (count($form) > 0) 
		{	
			$accessoryDetails = array(
										'id'			=> $this->getFromPost('accessory_id'),
										'name'			=> $this->getFromPost('accessory_desc')
									 );
							
			$validation = new Validation($this->db);					
			$validStatus = array (
									'summary'		=> 'true',
									'name'	=> 'failed'
								 );
			if (!$validation->check_name($accessoryDetails['name'])) 
			{
				$validStatus['summary'] = 'false';
			}
								
			// check for duplicate names					
			if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("accessory", $accessoryDetails['name'], null, $accessoryDetails['id'])) 
			{
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alreadyExist';
			}
								
			if ($validStatus['summary'] == 'true') 
			{
				//	setter injection
				$accessory->setTrashRecord(new Trash($this->db));
									
				// Editing accessory			
				$accessory->setAccessoryID($accessoryDetails['id']);
				$accessory->setAccessoryName($accessoryDetails['name']);
				$accessory->updateAccessory();
								
				// redirect
				header("Location: ?action=viewDetails&category=accessory&id=".$accessoryDetails['id']."&notify=39");
				die();
														
			} 
			else 
			{
				//	Errors on validation of editing accessory
				/* old school style */
				//$notify = new Notify($this->smarty);
				//$notify->formErrors();
				
				/*	the modern style */
				$notifyc = new Notify(null, $this->db);					
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);
								
				$this->smarty->assign('validStatus', $validStatus);
			}
		} 
		else 
		{
			$accessory->setAccessoryID($request['id']);
			$accessoryDetails = $accessory->getAccessoryDetails(); 
		}
			
		$this->smarty->assign('sendFormAction', '?action=edit&category='.$request['category'].'&departmentID='.$departmentID);
		$this->smarty->assign('data', $accessoryDetails);							
		$this->smarty->assign('tpl','tpls/addAccessory.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		$request=$this->getFromRequest();
							
		// protecting from xss
		$post=$this->getFromPost();
		
		foreach ($post as $key=>$value)
		{				
			$post[$key]=Reform::HtmlEncode($value);										
		}		
							
		if (count($post) > 0) 
		{							
			$companyID = 0; // KOSTYL' while not deleted company_id in TB_accessory
							
			$accessoryDetails = array(
										'id'	=> $this->getFromPost('accessory_id'),
										'name'	=> $this->getFromPost('accessory_desc')
									  );

								
			$accessory = new Accessory($this->db);
			$validation = new Validation($this->db);					
			$validStatus = array (
									'summary'		=> 'true',
									'name'	=> 'failed'
								 );
			if (!$validation->check_name($accessoryDetails['name'])) 
			{
				$validStatus['summary'] = 'false';
			}
							
			// check for duplicate names					
			if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("accessory", $accessoryDetails['name'])) 
			{
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alreadyExist';
			}
							
			if ($validStatus['summary'] == 'true')
			{
				//	setter injection
				$accessory->setTrashRecord(new Trash($this->db));
											
				// Adding for a new accessory			
				$accessory->setAccessoryName($accessoryDetails['name']);
				$accessory->insertAccessory($companyID);
								
				// redirect
				header("Location: ?action=browseCategory&category=accessory&notify=38");
				die();
													
			} 
			else 
			{
				//	Errors on validation of adding for a new accessory
				/* old school style */
				//$notify = new Notify($this->smarty);
				//$notify->formErrors();
				
				/*	the modern style */
				$notifyc = new Notify(null, $this->db);					
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);
									
				$this->smarty->assign('validStatus', $validStatus);
				$this->smarty->assign('data', $accessoryDetails);
			}
		}
		$this->smarty->assign('request',$request);					
		$this->smarty->assign('sendFormAction', '?action=addItem&category='.$request['category'].'&departmentID='.$request['departmentID']);
		$this->smarty->assign('tpl','tpls/addAccessory.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);
		
		$accessory=new Accessory($this->db);
		if (!is_null($this->getFromRequest('id'))) {					
			foreach ($req_id as $accessoryID) {
				$accessory->setAccessoryID($accessoryID);
				$accessoryDetails = $accessory->getAccessoryDetails();
									
				$delete["id"]		=	$accessoryDetails["id"];
				$delete["name"]		=	$accessoryDetails["name"];
				$itemForDelete[] 	= $delete;
			}
		}
		$request=$this->getFromRequest();
		$this->smarty->assign('request',$request);	
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}
			
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$accessory=new Accessory($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			
			//	setter injection
			$accessory->setTrashRecord(new Trash($this->db));
									
			// Delete Accessory
			$accessory->setAccessoryID($id);
			$accessory->deleteAccessory();
		}
		header ('Location: admin.php?action=browseCategory&category=accessory&page='.$this->getFromRequest("page"));
		die();
	}
	
		private function actionUploadOneMsds() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		$this->smarty->assign('page', $this->getFromRequest('page'));
		$this->smarty->assign('letterpage', $this->getFromRequest('letterpage'));
		//var_dump($productDetails['product_id']);
		//var_dump($_POST); die();
		if ($_POST['fileType'][0] == 'msds'){
		$success = true;
		if (count($_FILES) > 0) {			
			$msds = new MSDS($this->db);
			$msdsUploadResult = $msds->upload('basic');
			if (isset($msdsUploadResult['filesWithError'][0])) {
				$success = false;
				$error = $msdsUploadResult['filesWithError'][0]['error'];
			} else {				
				if ($msdsUploadResult['msdsResult']) {
					$msdsUploadResult['msdsResult'][0]['productID'] = $productDetails['product_id'];
					$input = array(
						'msds' => $msdsUploadResult['msdsResult']
					);					
					$msds->addSheets($input);					
					header('Location: ?action=viewDetails&category=product&id='.$productDetails['product_id'].'&letterpage='.$this->getFromRequest('letterpage').'&page='.$this->getFromRequest('page'));
				} else {
					$success = false;	
					$error = 'msdsResult is not set';
				}				
			}
						
		}
		} elseif ($_POST['fileType'][0] == 'techsheet') {
		$success = true;
		if (count($_FILES) > 0) {			
			$techSheet = new TechSheet($this->db);
			$techSheetUploadResult = $techSheet->upload('basic');
			//var_dump($techSheetUploadResult);
			if (isset($techSheetUploadResult['filesWithError'][0])) {
				$success = false;
				$error = $techSheetUploadResult['filesWithError'][0]['error'];
			} else {
				if ($techSheetUploadResult['techSheetResult']) {
					$techSheetUploadResult['techSheetResult'][0]['productID'] = $productDetails['product_id'];
					$input = array(
						'techSheets' => $techSheetUploadResult['techSheetResult']
					);	
					//var_dump($input);
					$techSheet->addSheets($input);					
					header('Location: ?action=viewDetails&category=product&id='.$productDetails['product_id'].'&letterpage='.$this->getFromRequest('letterpage').'&page='.$this->getFromRequest('page'));
				} else {
					$success = false;	
					$error = 'techSheetResult is not set';
				}				
			}
						
		}	
		}
		if (!$success) {
			$this->smarty->assign("error", $error);	
		}
		
		$this->smarty->assign("productDetails",$productDetails);
		$this->smarty->assign("tpl","tpls/uploadOneMsds.tpl");
		$this->smarty->display("tpls:index.tpl");
	}
	
	
	private function actionUnlinkMsds() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		
		$msds = new MSDS($this->db);
		$sheet = $msds->getSheetByProduct($this->getFromRequest('productID'));
		if (!$sheet) {
			throw new Exception('This product does not have MSDS');
		}
		
		$msds->unlinkMsdsSheet($sheet['id']);
		header('Location: ?action=viewDetails&category=product&id='.$this->getFromRequest('productID'));
	}
	
	private function actionUnlinkTechSheet() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		
		$techSheet = new TechSheet($this->db);
		$sheet = $techSheet->getSheetByProduct($this->getFromRequest('productID'));
		if (!$sheet) {
			throw new Exception('This product does not have Tech Sheet');
		}
		
		$techSheet->unlinkTechSheet($sheet['id']);
		header('Location: ?action=viewDetails&category=product&id='.$this->getFromRequest('productID'));
	}
}
?>