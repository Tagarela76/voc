<?php
class CNox extends Controller
{	
	function Cnox($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='nox';
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
		$accessory = new Accessory($this->db); // viewDetails.tpl
								
		foreach ($this->itemID as $ID) 
		{
			//	setter injection
			$accessory->setTrashRecord(new Trash($this->db));
									
			// Delete Accessory
			$accessory->setAccessoryID($ID);
			$accessory->deleteAccessory();
		}							
		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=department&id=".$this->getFromPost('departmentID')."&bookmark=accessory&notify=40");			
	}
	
	private function actionDeleteItem()
	{
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
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=accessory");
							
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}
							
		//set permissions							
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'),array('bookmark'=>'accessory'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
							
		$this->smarty->assign('departmentID', $this->getFromRequest('departmentID'));
		$this->smarty->assign('itemID', $this->getFromRequest('departmentID'));
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}	
	
	private function actionViewDetails()
	{
		//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}					
							
		$accessory = new Accessory($this->db);
		$accessory->setAccessoryID($this->getFromRequest("id"));
		$accessoryDetails = $accessory->getAccessoryDetails();
		$accessoryUsages = $accessory->getAccessoryUsages($this->getFromRequest("id"),$this->getFromRequest("departmentID"));

		$jobberManager = new JobberManager($this->db);
		$jobberDetails = $jobberManager->getJobberDetails($accessoryDetails['jobber_id']);
		$accessoryDetails['jobber_name'] = $jobberDetails['name'];
		
		$this->smarty->assign("accessory", $accessoryDetails);
		$this->smarty->assign("accessoryUsages", $accessoryUsages);
							
		$this->setNavigationUpNew('department', $this->getFromRequest("departmentID"));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest("departmentID"),array('bookmark'=>'accessory'));
		$this->setPermissionsNew('viewData');
		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));
		
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
							
		$this->smarty->assign('dataChain', new TypeChain(null,'date',$this->db, $companyID,'company'));
		$this->smarty->assign('editUrl','?action=edit&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('addUsageUrl','?action=addUsage&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('deleteUrl','?action=deleteItem&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));	
		$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=accessory');
		$this->smarty->assign('tpl','tpls/viewAccessory.tpl');
		$this->smarty->display("tpls:index.tpl");	
	}
	
	private function actionAddItem() 
	{ 
		$request=$this->getFromRequest();
		$request['parent_category'] = 'department';
							
		//	Access control
		if (!$this->user->checkAccess('department', $request["departmentID"])) 
		{						
			throw new Exception('deny');
		}
							
		//set permissions							
		$this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark'=>'nox'));
		$this->setNavigationUpNew('department', $request['departmentID']);
		$this->setPermissionsNew('viewData');
		if($request['tab'] == 'nox'){
				$noxManager = new NoxEmissionManager($this->db);
				$burnerList = $noxManager->getBurnerListByDepartment($request['departmentID']);
				$this->smarty->assign("burners", $burnerList);		

				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));


				$this->smarty->assign('dataChain', new TypeChain(null,'date',$this->db, $companyID,'company'));
				
		}		
		
		// protecting from xss
		$post=$this->getFromPost();
		
		foreach ($post as $key=>$value)
		{				
			$post[$key]=Reform::HtmlEncode($value);										
		}		
							
		if (count($post) > 0) 
		{	
			$departmentID = $post['department_id'];			
			switch($post['tab']){
				case ('burner'):
					$burnerDetails = array(
											'burner_id'	=> $this->getFromPost('burner_id'),
											'department_id'	=> $this->getFromPost('department_id'),
											'model'	=> $this->getFromPost('model'),
											'serial'	=> $this->getFromPost('serial'),
											'manufacturer_id'	=> $this->getFromPost('manufacturer_id'),	
											'input'	=> $this->getFromPost('input'),
											'output'	=> $this->getFromPost('output'),
											'btu'	=> $this->getFromPost('btu')
											);

					$validation = new Validation($this->db);					
					$validStatus = array (
											'summary' => 'true',
											'model'	=> 'failed',
											'serial'	=> 'failed',
											'manufacturer_id'	=> 'failed',
											'input'	=> 'failed',
											'output'	=> 'failed',
											'btu'	=> 'failed'
										);
					if (!$validation->check_name($burnerDetails['model'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['model'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['serial'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['serial'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['manufacturer_id'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['manufacturer_id'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['input'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['input'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['output'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['output'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['btu'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['btu'] = 'accept';
					}					


					if ($validStatus['summary'] == 'true')
					{
						$noxBurner = new NoxBurner($this->db,$burnerDetails);
						$noxBurner->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=nox&tab={$request['tab']}&notify=41");
						die();

					} 
					else 
					{

						/*	the modern style */
						$notifyc = new Notify(null, $this->db);					
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);

						$this->smarty->assign('validStatus', $validStatus);
						$this->smarty->assign('data', $burnerDetails);
					}					
				break;	
				
				case ('nox'):
					$noxDetails = array(
											'nox_id'	=> $this->getFromPost('nox_id'),
											'department_id'	=> $this->getFromPost('department_id'),
											'description'	=> $this->getFromPost('description'),
											'gas_unit_used'	=> $this->getFromPost('gas_unit_used'),
											'start_time'	=> $this->getFromPost('start_time'),	
											'end_time'	=> $this->getFromPost('end_time'),
											'burner_id'	=> $this->getFromPost('burner_id'),
											'note'	=> $this->getFromPost('note')
											);

					$validation = new Validation($this->db);					
					$validStatus = array (
											'summary' => 'true',
											'description'	=> 'failed',
											'gas_unit_used'	=> 'failed',
											'start_time'	=> 'failed',
											'end_time'	=> 'failed',
											'burner_id'	=> 'failed'
										);

					if (!$validation->check_name($noxDetails['description'])) 
					{$validStatus['summary'] = 'false';
					}else{
						// check for duplicate names					
						if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("nox", $noxDetails['description'], $departmentID)) 
						{
							$validStatus['summary'] = 'false';
							$validStatus['description'] = 'alreadyExist';
						}else{
							$validStatus['description'] = 'accept';
						}
					}
	
					
					if (!$validation->check_name($noxDetails['gas_unit_used'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['gas_unit_used'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['start_time'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['start_time'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['end_time'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['end_time'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['burner_id'])) 
					{$validStatus['summary'] = 'false';
					}else{
						$validStatus['burner_id'] = 'accept';
					}
					
					if ($validStatus['summary'] == 'true')
					{
						$startTime = new DateTime($noxDetails['start_time']);
						$endTime = new DateTime($noxDetails['end_time']);
						
						$noxDetails['start_time'] = $startTime->getTimestamp();
						$noxDetails['end_time'] = $endTime->getTimestamp();
						$nox = new NoxEmission($this->db,$noxDetails);
						$nox->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=nox&tab={$request['tab']}&notify=45");
						die();

					} 
					else 
					{

						/*	the modern style */
						$notifyc = new Notify(null, $this->db);					
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);

						$this->smarty->assign('validStatus', $validStatus);
						$this->smarty->assign('data', $noxDetails);
					}					
				break;				
				
			}

		}
		$jsSources = array(
					'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
					'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
					'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
				);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);		
		$this->smarty->assign('request',$request);					
		$this->smarty->assign('sendFormAction', '?action=addItem&category='.$request['category'].'&departmentID='.$request['departmentID'].'&tab='.$request['tab']);
		$this->smarty->assign('tpl','tpls/addBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() 
	{
		$request=$this->getFromRequest();
		$accessory = new Accessory($this->db);
		$departmentID = $request['departmentID'];
		//var_dump($request['departmentID']);die;
							
		//	Access control
		if (!$this->user->checkAccess('department', $request['departmentID'])) {						
			throw new Exception('deny');
		}
							
		//set permissions							
		$this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark'=>'accessory'));
		$this->setNavigationUpNew('department', $request['departmentID']);
		$this->setPermissionsNew('viewData');
		
						
		$form = $this->getFromPost();
							
		if (count($form) > 0) 
		{	
			$accessoryDetails = array(
										'id'			=> $this->getFromPost('accessory_id'),
										'name'			=> $this->getFromPost('accessory_desc')
									 );
							
			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
								
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
			if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("accessory", $accessoryDetails['name'], $companyID, $accessoryDetails['id'])) 
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
				header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=accessory&notify=39");
				die();
														
			} 
			else 
			{
				
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
	
	/**
     * bookmarkDNox($vars)     
     * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
     */       
	protected function bookmarkDNox($vars)
	{			
		if(!isset($_GET['tab'])) {
			header("Location: {$_SERVER['REQUEST_URI']}&tab=nox") ;
		}
		extract($vars);

		if($tab == "burner") {
			$this->bookmarkDburner($vars);
		} else {
		$departmentID = $departmentDetails['department_id'];
		$sortStr=$this->sortList('nox',3);
		
		$noxManager = new NoxEmissionManager($this->db);

		// autocomplete
		//$accessory->accessoryAutocomplete($_GET['query'],$jobberIdList);
		
		// search
		if (!is_null($this->getFromRequest('q'))) 
		{
		/*
			$accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$accessoryList = $accessory->searchAccessory($accessoryToFind);																						
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		 */
		} 
		else 
		{
			$noxList = $noxManager->getNoxListByDepartment($departmentID, $sortStr, $pagination);
		}
		

		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "NOx Emissions of department ".$departmentDetails['name'];
			if ($this->getFromRequest('searchAction')=='search') 
			{
				$exporter->search_term = $this->getFromRequest('q');
			} 
			else 
			{
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
							'id' => '30',
							'description' => '70'											
							);
			$header = array(
							'id' => 'ID Number',
							'description' => 'Nox Emission Description',																		
							);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($noxList);
			$exporter->export();
			die();
													
		} 
		else 
		{			

			if ($noxList){
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));


				$dataChain = new TypeChain(null,'date',$this->db, $companyID,'company');				
				for ($i=0; $i<count($noxList); $i++) 
				{
					$url="?action=viewDetails&category=nox&id=".$noxList[$i]['nox_id']."&departmentID=".$this->getFromRequest('id')."&tab=".$this->getFromRequest('tab');
					$noxList[$i]['url']=$url;
					$burnerDetails = $noxManager->getBurnerDetail($noxList[$i]['burner_id']);
					$noxList[$i]['burner']= $burnerDetails;
					
					$noxList[$i]['start_time'] = date("m/d/Y g:i:s", $noxList[$i]['start_time']);					
					$noxList[$i]['end_time'] = date("m/d/Y g:i:s", $noxList[$i]['end_time']);					

					
				}
			}

			$this->smarty->assign("childCategoryItems", $noxList);

			//	set js scripts
			$jsSources = array(
								'modules/js/checkBoxes.js',										
								'modules/js/autocomplete/jquery.autocomplete.js',								
							  );
			$this->smarty->assign('jsSources', $jsSources);
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/noxList.tpl');
		}
	}	
}
	
	/**
     * bookmarkDNox($vars)     
     * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
     */       
	protected function bookmarkDburner($vars)
	{			
		extract($vars);
		$departmentID = $departmentDetails['department_id'];
		$sortStr=$this->sortList('burner',1);

		$noxManager = new NoxEmissionManager($this->db);

		// autocomplete
		//$accessory->accessoryAutocomplete($_GET['query'],$jobberIdList);
		
		// search
		if (!is_null($this->getFromRequest('q'))) 
		{
		/*
			$accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$accessoryList = $accessory->searchAccessory($accessoryToFind);																						
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		 */
		} 
		else 
		{
			$burnerList = $noxManager->getBurnerListByDepartment($departmentID, $sortStr, $pagination);
		}
		

		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Burners of department ".$departmentDetails['name'];
			if ($this->getFromRequest('searchAction')=='search') 
			{
				$exporter->search_term = $this->getFromRequest('q');
			} 
			else 
			{
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
							'id' => '30',
							'description' => '70'											
							);
			$header = array(
							'id' => 'ID Number',
							'description' => 'Burner Model',																		
							);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($burnerList);
			$exporter->export();
			die();
													
		} 
		else 
		{			

			if ($burnerList){
				for ($i=0; $i<count($burnerList); $i++) 
				{
					$url="?action=viewDetails&category=nox&id=".$burnerList[$i]['burner_id']."&departmentID=".$this->getFromRequest('id')."&tab=".$this->getFromRequest('tab');
					$burnerList[$i]['url']=$url;
				}
			}

			$this->smarty->assign("childCategoryItems", $burnerList);
			//	set js scripts
			$jsSources = array(
								'modules/js/checkBoxes.js',										
								'modules/js/autocomplete/jquery.autocomplete.js',								
							  );
			$this->smarty->assign('jsSources', $jsSources);
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/burnerList.tpl');
		}

	}

}
?>