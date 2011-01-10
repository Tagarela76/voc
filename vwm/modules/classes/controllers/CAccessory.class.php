<?php
class CAccessory extends Controller
{	
	function CAccessory($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='accessory';
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
		$accessory = new Accessory($this->db);
								
		foreach ($this->itemID as $ID) 
		{
			//	setter injection
			$accessory->setTrashRecord(new Trash($this->db));
									
			// Delete Accessory
			$accessory->setAccessoryID($ID);
			$accessory->deleteAccessory();
		}							
		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=department&id=".$this->getFromPost('departmentID')."&bookmark=accessory");			
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
		$this->smarty->assign("accessory", $accessoryDetails);
							
		$this->setNavigationUpNew('department', $this->getFromRequest("departmentID"));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest("departmentID"),array('bookmark'=>'accessory'));
		$this->setPermissionsNew('viewData');
							
		$this->smarty->assign('editUrl','?action=edit&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('deleteUrl','?action=deleteItem&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));	
		$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=accessory');
		$this->smarty->assign('tpl','tpls/viewAccessory.tpl');
		$this->smarty->display("tpls:index.tpl");	
	}
	
	private function actionAddItem() 
	{ 
		$request=$this->getFromRequest();
		$request['id'] = $request['departmentID'];		
		$request['parent_category'] = 'department';
							
		//	Access control
		if (!$this->user->checkAccess('department', $request["departmentID"])) 
		{						
			throw new Exception('deny');
		}
							
		//set permissions							
		$this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark'=>'accessory'));
		$this->setNavigationUpNew('department', $request['departmentID']);
		$this->setPermissionsNew('viewData');
							
		// protecting from xss
		$post=$this->getFromPost();
		
		foreach ($post as $key=>$value)
		{				
			$post[$key]=Reform::HtmlEncode($value);										
		}		
							
		if (count($post) > 0) 
		{							
			$departmentID = $post['department_id'];
			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
							
			$accessoryDetails = array(
										'id'	=> $this->getFromPost('accessory_id'),
										'name'	=> $this->getFromPost('accessory_desc')
									  );
							
			$request['id'] = $departmentID;
								
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
			if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("accessory", $accessoryDetails['name'], $companyID)) 
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
				header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=accessory");
				die();
													
			} 
			else 
			{
				//	Errors on validation of adding for a new accessory
				$notify = new Notify($this->smarty);
				$notify->formErrors();
									
				$this->smarty->assign('validStatus', $validStatus);
				$this->smarty->assign('data', $accessoryDetails);
			}
		}
		$this->smarty->assign('request',$request);					
		$this->smarty->assign('sendFormAction', '?action=addItem&category='.$request['category'].'&departmentID='.$request['departmentID']);
		$this->smarty->assign('tpl','tpls/addAccessory.tpl');
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
				header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=accessory");
				die();
														
			} 
			else 
			{
				//	Errors on validation of editing accessory
				$notify = new Notify($this->smarty);
				$notify->formErrors();
								
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
     * bookmarkDAccessory($vars)     
     * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
     */       
	protected function bookmarkDAccessory($vars)
	{			
		extract($vars);
		
		$sortStr=$this->sortList('accessory',3);
		$accessory = new Accessory($this->db);
									
		// search
		if (!is_null($this->getFromRequest('q'))) 
		{
			$accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$accessoryList = $accessory->searchAccessory($accessoryToFind, $facilityDetails['company_id']);																						
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} 
		else 
		{
			$accessoryList = $accessory->getAllAccessory($facilityDetails['company_id'],$sortStr);
		}
		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Accessories of department ".$departmentDetails['name'];
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
							'name' => '70'											
							);
			$header = array(
							'id' => 'ID Number',
							'name' => 'Accessory Name',																		
							);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($accessoryList);
			$exporter->export();
			die();
													
		} 
		else 
		{			
			$itemsCount = $accessory->queryTotalCount($facilityDetails['company_id']);			
			for ($i=0; $i<$itemsCount; $i++) 
			{
				$url="?action=viewDetails&category=accessory&id=".$accessoryList[$i]['id']."&departmentID=".$this->getFromRequest('id');
				$accessoryList[$i]['url']=$url;
			}
			$this->smarty->assign("childCategoryItems", $accessoryList);
			//	set js scripts
			$jsSources = array(
								'modules/js/checkBoxes.js',										
								'modules/js/autocomplete/jquery.autocomplete.js',								
							  );
			$this->smarty->assign('jsSources', $jsSources);
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/accessoryList.tpl');
		}
	}
}
?>