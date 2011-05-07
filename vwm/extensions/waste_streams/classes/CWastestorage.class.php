<?php
class CWastestorage extends Controller
{	
	function CWastestorage($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='wastestorage';
		$this->parent_category='facility';			
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
		if ($this->getFromPost('error') != 'date') 
		{
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromPost('facilityID'));
			//delete of direct carbon footprint!!
			$facility->initializeByID($this->getFromPost('facilityID'));
										
			if (!$this->user->checkAccess('waste_streams', $facilityDetails['company_id'])) 
			{
				throw new Exception('deny');
			}
			//	OK, this company has access to this module, so let's setup..
			$ms = new ModuleSystem($this->db);	//	TODO: show?
			$moduleMap = $ms->getModulesMap();
			$mWasteStreams = new $moduleMap['waste_streams'];		
										
			$params = array(
							'db' => $this->db,
							'facilityID' => $this->getFromPost('facilityID'),
							'confirmed' => true,
							'idArray' => $this->itemID,
							'method' => $this->getFromPost('info'),
							'date' => date('Y-m-d',strtotime($this->getFromPost('dateEmpty')))
						   );
			$mWasteStreams->prepareDeleteStorage($params);
		}
		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromPost('facilityID')."&bookmark=wastestorage&tab=active&notify=29");	
	}
	
	private function actionDeleteItem()
	{			
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		if (!$this->user->checkAccess('waste_streams', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//delete of direct carbon footprint!!
		$facility->initializeByID($this->getFromRequest('facilityID'));						
		
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mWasteStreams = new $moduleMap['waste_streams'];						
							
		if (!is_null($this->getFromRequest('id'))) {
			$idArray = array($this->getFromRequest('id'));
		} else {
			$idArray = $this->getFromPost('checkWastestorage');
		}
			
		if (!is_null($this->getFromPost('delete')) || !is_null($this->getFromRequest('delete'))) 
		{
			if(!is_null($this->getFromRequest('delete')))
			{
				$info = array(
								'method' => 'delete',
								'date' => $this->getFromRequest('dateDeleted')
							);
			}
			if(!is_null($this->getFromPost('delete')))
			{
				$info = array(
								'method' => 'delete',
								'date' => $this->getFromPost('dateDeleted')
							 );
			}
		} 
		elseif (!is_null($this->getFromPost('restore')) || !is_null($this->getFromRequest('restore'))) 
		{
			if(!is_null($this->getFromPost('restore')))
			{
				$info = array(
								'method' => 'restore',
								'date' => $this->getFromPost('dateDeleted')
							);
			}
			if(!is_null($this->getFromRequest('restore')))
			{
				$info = array(
								'method' => 'restore',
								'date' => $this->getFromRequest('dateDeleted')
							);
			}			
		}
		elseif (!is_null($this->getFromPost('empty')) || !is_null($this->getFromRequest('empty'))) 
		{
			if(!is_null($this->getFromPost('empty')))
			{
				$info = array(
								'method' => 'empty',
								'date' => $this->getFromPost('dateEmpty')
							 );
			}
			if(!is_null($this->getFromRequest('empty')))
			{
				$info = array(
								'method' => 'empty',
								'date' => $this->getFromRequest('dateEmpty')
							 );
			}	
			
		} 
		else 
		{
			throw new Exception('Action for choosen files are lost!');
		}
		
		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('facilityID'),
						'confirmed' => false,
						'idArray' => $idArray,
						'method' => $info['method'],
						'date' => $info['date']
						);
		extract($mWasteStreams->prepareDeleteStorage($params));
							
		if ($error == 'date') 
		{
			$info['error'] = 'date';
			$this->smarty->assign('error', 'date');
		}
							
		if (count($itemForDelete) == 1) 
		{
			$notify = new Notify($this->smarty);
			$notify->warnDelete($this->getFromRequest('category'),$itemForDelete[0]["name"],false, 1, $info);
		}
							
		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'),array('bookmark'=>'wastestorage','tab'=>'active'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('facility');
							
		$this->smarty->assign('facilityID', $this->getFromRequest('facilityID'));
		$this->smarty->assign('cancelUrl', "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=wastestorage&tab=active");
		$this->smarty->assign('info',$info);		
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}
	
	private function actionAddItem()
	{
		$request=$this->getFromRequest();
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);
		//add of direct carbon footprint!!
		$facility->initializeByID($request['facilityID']);
							
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();							
		foreach($moduleMap as $key=>$module) 
		{
			$showModules[$key] = $this->user->checkAccess($key,$facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);
							
		$this->setListCategoriesLeftNew('facility', $request["facilityID"],array('bookmark'=>'wastestorage', 'tab'=>'active'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('viewFacility');
							
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
							
		if (!$showModules['waste_streams']) 
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mWasteStream = new $moduleMap['waste_streams'];							
							
		$params = array(
						'db' => $this->db,
						'facilityID' => $request['facilityID'],
						'save' => (!is_null($this->getFromPost('save')))?true:false,
						'capacity_volume' => str_replace(',','.',$this->getFromPost('capacity_volume')),
						'capacity_weight' => str_replace(',','.',$this->getFromPost('capacity_weight')),
						'density' => $this->getFromPost('density'),
						'density_unit_id' => $this->getFromPost('selectDensityType'),								
						'max_period' => $this->getFromPost('max_period'),
						'name' =>  $this->getFromPost('name'),
						'selectSuitability' =>  $this->getFromPost('selectSuitability'),
						'companyID' =>$facilityDetails['company_id'],
						'action'=>$request['action'],
						'volume_unittype'=>$this->getFromPost('selectVolumeUnittype'),
						'weight_unittype'=>$this->getFromPost('selectWeightUnittype'),
						'document_id'=>$this->getFromPost('documentID'),
						'isDocs'=>$showModules['docs']						
						);							
							
	 	$result = $mWasteStream->prepareAddStorage($params);
		if ($result === true) 
		{
			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=wastestorage&tab=active&notify=27");
			die();
		}
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);
		}
		//prepare docs to display if its need
		if ($showModules['docs']) 
		{
			$params = array(
							'db' => $this->db,
							'facilityID' => $request['facilityID']
							);
			$mDocs = new $moduleMap['docs'];
			$result = $mDocs->prepareStorageAdd($params);
			foreach($result as $key => $data) 
			{
				$this->smarty->assign($key,$data);
			}
		}							
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->assign('tpl','waste_streams/design/editStorage.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit()
	{		
		$request=$this->getFromRequest();
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);						
		$facility->initializeByID($request['facilityID']);

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();							
		foreach($moduleMap as $key=>$module) 
		{
			$showModules[$key] = $this->user->checkAccess($key,$facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);
								
		$this->setListCategoriesLeftNew('facility', $request["facilityID"], array('bookmark'=>'wastestorage','tab'=>'active'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('viewFacility');
								
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
								
		if (!$showModules['waste_streams']) 
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$mWasteStream = new $moduleMap['waste_streams'];
						
		$params = array(							
						'db' => $this->db,
						'facilityID' => $request['facilityID'],
						'save' => (!is_null($this->getFromPost('save')))?true:false,
						'capacity_volume' => str_replace(',','.',$this->getFromPost('capacity_volume')),
						'capacity_weight' => str_replace(',','.',$this->getFromPost('capacity_weight')),	
						'density' => $this->getFromPost('density'),
						'density_unit_id' =>$this->getFromPost('selectDensityType'),							
						'max_period' => $this->getFromPost('max_period'),
						'name' =>  $this->getFromPost('name'),
						'selectSuitability' =>  $this->getFromPost('selectSuitability'),
						'companyID' =>$facilityDetails['company_id'],
						'action'=>$request['action'],
						'volume_unittype'=>$this->getFromPost('selectVolumeUnittype'),
						'weight_unittype'=>$this->getFromPost('selectWeightUnittype'),
						'storage_id'=>$request['id'],
						'document_id'=>$this->getFromPost('documentID'),
						'isDocs'=>$showModules['docs']						
						);
		$result = $mWasteStream->prepareAddStorage($params);
		if ($result === true) 
		{			
 			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=wastestorage&tab=active&notify=28");
			die();
		}
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);
		}
							
		//prepare docs to display if its need
		if ($showModules['docs']) 
		{
			$params = array(
							'db' => $this->db,
							'facilityID' => $request['facilityID']
						   );
			$mDocs = new $moduleMap['docs'];
			$result = $mDocs->prepareStorageAdd($params);
			foreach($result as $key => $data) 
			{
				$this->smarty->assign($key,$data);
			}
		}		
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->assign('tpl','waste_streams/design/editStorage.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionViewDetails()
	{
		$facility = new Facility($this->db);
		$facility->initializeByID($this->getFromRequest('facilityID'));
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
							
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
	
		$this->setPermissionsNew('viewFacility');
							
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
							
		if (!$this->user->checkAccess('waste_streams', $facilityDetails['company_id'])) 
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
							
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();							
		foreach($moduleMap as $key=>$module) 
		
		{
			$showModules[$key] = $this->user->checkAccess($key,$facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);
		$mWasteStreams = new $moduleMap['waste_streams'];
		$params = array(
						'db' => $this->db,								
						'storage_id' => $this->getFromRequest('id'),
						'isDoc'=>$showModules['docs']
						);
		$result = $mWasteStreams->prepareViewStorage($params);
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);												
		}
		//prepare docs to display if its need
		if ($showModules['docs']) 
		{
			$params = array(
							'db' => $this->db,
							'facilityID' => $this->getFromRequest('facilityID'),
							'id' => $result['data']->document_id
							);
			$mDocs = new $moduleMap['docs'];
			
			$result_doc = $mDocs->prepareStorageView($params);
			foreach($result_doc as $key => $data) 
			{
				$this->smarty->assign($key,$data);
			}
		}	
		
		$this->smarty->assign('editUrl',"?action=edit&category=wastestorage&facilityID=".$this->getFromRequest('facilityID')."&id=".$this->getFromRequest('id')."");
		$this->smarty->assign('deleteUrl',"?action=deleteItem&category=wastestorage&facilityID=".$this->getFromRequest('facilityID')."&id=".$this->getFromRequest('id')."&delete=1");
		$this->smarty->assign('emptyUrl',"?action=deleteItem&category=wastestorage&facilityID=".$this->getFromRequest('facilityID')."&id=".$this->getFromRequest('id')."&empty=1");
		$this->smarty->assign('restoreUrl',"?action=deleteItem&category=wastestorage&facilityID=".$this->getFromRequest('facilityID')."&id=".$this->getFromRequest('id')."&restore=1");
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('notViewChildCategory', true);
		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'wastestorage','tab'=>(($result['data']->active ==1)?'active':'removed')));
		$this->smarty->assign('backUrl','?action=browseCategory&category=facility&id='.$this->getFromRequest('facilityID').'&bookmark=wastestorage&tab='.(($result['data']->active !=0)?'active':'removed'));
		$this->smarty->assign('tpl','waste_streams/design/viewWasteStorageDetails.tpl');
		$this->smarty->display("tpls:index.tpl");	
	}				
	
	/**
     * bookmarkLogbook($vars)     
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */       
	protected function bookmarkWastestorage($vars)
	{			
		extract($vars);		
		$sortStr=$this->sortList('wasteStorage',1);								
		$this->smarty->assign('tab',$this->getFromRequest('tab'));
		$facility->initializeByID($this->getFromRequest('id'));
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
									
		if (!$this->user->checkAccess('waste_streams', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}									
		//	OK, this company has access to this module, so let's setup..
									
		$mWastestreams = new $moduleMap['waste_streams'];
									
		$status = $this->getFromRequest('tab');
									
		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('id'),
						'status'=>$status,
						'page'=>$this->getFromRequest('page'),
						'sort'=>$sortStr,
						'isDocs'=>$this->user->checkAccess('docs', $facilityDetails['company_id'])
						);								
									
		$result = $mWastestreams-> prepareBrowseStorage($params);
		
		

		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		//prepare docs to display if its need
		if ($showModules['docs']) 
		{
			$params = array(
							'db' => $this->db,
							'idArray' => $result['idArray']
							);
			$mDocs = new $moduleMap['docs'];
			$result = $mDocs->prepareStorageBrowse($params);
			foreach($result as $key => $data) {
				$this->smarty->assign($key,$data);
			}
		}										
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->assign('tpl','waste_streams/design/wasteStorageView.tpl');				
	}
}
?>