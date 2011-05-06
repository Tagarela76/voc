<?php
class CEquipment extends Controller
{	
	function CEquipment($smarty,$xnyo,$db,$user,$action)
		{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='equipment';
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
		$equipment=new Equipment($this->db);
		//save to bridge
		//$companyFacilityDepartment = getCompanyFacilityDepartment($db);							
		$voc2vps = new VOC2VPS($this->db);							
		$company = new Company($this->db);
		
		foreach ($this->itemID as $ID) 
		{								
			$equipmentDetails=$equipment->getEquipmentDetails($ID, true);								
			$companyID = $company->getCompanyIDbyDepartmentID($equipmentDetails['department_id']);
			
			$customerLimits = $voc2vps->getCustomerLimits($companyID);
			$limit = array (
				'limit_id' => 3,
				'current_value' => $customerLimits['Source count']['current_value']-1,
				'max_value' => $customerLimits['Source count']['max_value']
			);								
			$voc2vps->setCustomerLimitByID($companyID, $limit);
			
			$itemForDeleteName[]=$equipmentDetails["equip_desc"];
			
			//	setter injection								
			$equipment->setTrashRecord(new Trash($this->db));																									
			$equipment->deleteEquipment($ID);													
		}
		
		//post redirect			
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=department&id=".$equipmentDetails['department_id']."&bookmark=equipment&notify=36");										
		}
	
	private function actionDeleteItem()
		{		
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);
		
		$equipment=new Equipment($this->db);
		if(!is_null($this->getFromRequest('id'))) {
			foreach ($req_id as $equipmentID) 
			{								
				$equipmentDetails 	= $equipment->getEquipmentDetails($equipmentID, true);
				$delete["id"]		=	$equipmentDetails["equipment_id"];
				$delete["name"]		=	$equipmentDetails["equip_desc"];
				// dependencies
				$delete["linkedItem"] = "Mix";
				$delete["inUseList"] = $equipment->isInUseList($delete["id"]);
				$delete["linkedItemCount"] = count($delete["inUseList"]);
				$itemForDelete[] 	= $delete;
			}
		}						
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=equipment");
		
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}
		
		//set permissions							
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'), array('bookmark'=>'equipment'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');								
		$this->smarty->assign('linkedItemCount', count());
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
		}
	
	private function actionViewDetails()
		{
		//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}
		
		$equipment = new Equipment($this->db);
		$equipmentDetails = $equipment->getEquipmentDetails($this->getFromRequest("id"));
		
		//if(is_null($this->getFromRequest('departmentID')) || empty($this->getFromRequest('departmentID'))) {
		//	$this->getFromRequest('departmentID') = $equipmentDetails['departmentID'];
		//}				
		
		//	<Check for inventory module>
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();							
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);		
						
		
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'), array('bookmark'=>'equipment'));
		$this->setPermissionsNew('viewEquipment');																		
		
		//	Check if Expired/Pre Expired
		$equipment->initializeByID($this->getFromRequest("id"));
		
		if ($equipment->isPreExpired()) {
			$equipmentDetails["status"] = "Pre Expired";
		} elseif ($equipment->isExpired()) {
			$equipmentDetails["status"] = "Expired";
		} else {
			$equipmentDetails["status"] = "Not Expired";
		}											
		$this->smarty->assign("equipment", $equipmentDetails);
		$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=equipment');
		$this->smarty->assign('tpl', 'tpls/viewEquipment.tpl');
		$this->smarty->display("tpls:index.tpl");	
		}
	
	private function actionAddItem() {
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {						
			throw new Exception('deny');
		}
		
		$department = new Department($this->db);
		$department->initializeByID($this->getFromRequest('departmentID'));
		
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		
		// Check: can this company add more equipments?
		$voc2vps = new VOC2VPS($this->db);
		$limits = $voc2vps->getCustomerLimits($companyID);
		if ($limits['Source count']['current_value'] >= $limits['Source count']['max_value']) {
			header ('Location: ?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=equipment&notify=35');
			die();
		}
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);
		
		if ($showModules['inventory']) {
			//	OK, this company has access to inventory module, so let's setup..
			$mInventory = new $moduleMap['inventory'];
			
			$params = array(
				'department' => $department,													
			);
			$result = $mInventory->prepare4equipmentAdd($params);
			foreach ($result as $key=>$value) {										
				$this->smarty->assign($key,$value);
			}
		}																						
		
		$DateType = new DateTypeConverter($this->db);
		$categoryDetails['date_type'] = $DateType->getDatetype($department->getFacilityID()); 	
		
		$categoryDetails['expire'] = new TypeChain(null,'date',$this->db,$this->getFromRequest('departmentID'),'department');						
		$this->smarty->assign('data', $categoryDetails);
		
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'), array('bookmark'=>'equipment'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewEquipment');
		
		$this->smarty->assign('pleaseWaitReason', "Recalculating mixes by equipment.");
		
		//	set js scripts				
		$jsSources = array(								
			'modules/js/getInventoryShortInfo.js',								
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/saveItem.js',
			'modules/js/PopupWindow.js'
		);							
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		
		$this->smarty->assign('tpl','tpls/addEquipment.tpl');
		$this->smarty->display("tpls:index.tpl");
	}		
	
	private function actionEdit() {
		$equipment = new Equipment($this->db);
		$equipmentDetails = $equipment->getEquipmentDetails($this->getFromRequest('id'), true);						
		$equipmentDetails["expire_date"] = $equipmentDetails["expire"];	

		$equipmentDetails["expire"]->getFromTypeController('getFormatForCalendar');
		
		//	Access control
		if (!$this->user->checkAccess('department', $equipmentDetails['department_id'])) {						
			throw new Exception('deny');
		}
		
		$department = new Department($this->db);														
		$department->initializeByID($equipmentDetails['department_id']);
		
		//	Module system
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($equipmentDetails['department_id']);
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);		
		
		if ($showModules['inventory']) {
			//	OK, this company has access to inventory module, so let's setup..
			$mInventory = new $moduleMap['inventory'];
			
			$params = array(
				'department' => $department,			
				'db'		=> $this->db,
				'inventoryID'=> $equipmentDetails['inventory_id']									
			);
			$result = $mInventory->prepare4equipmentEdit($params);
			
			foreach ($result as $key=>$value) {																	
				$this->smarty->assign($key,$value);
			}
		}																		
//		var_dump($equipmentDetails['expire']);
		$this->smarty->assign('data', $equipmentDetails);						
		
		$this->setNavigationUpNew('department', $equipmentDetails['department_id']);
		$this->setListCategoriesLeftNew('department',$equipmentDetails['department_id'], array('bookmark'=>'equipment'));
		$this->setPermissionsNew('viewEquipment');
		
		//	set js scripts				
		$jsSources = array(									
			'modules/js/getInventoryShortInfo.js',								
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/PopupWindow.js',							
			'modules/js/saveItem.js',								
		);
		$this->smarty->assign('jsSources', $jsSources);							
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
		
		$this->smarty->assign('pleaseWaitReason', "Recalculating mixes by equipment.");
		$this->smarty->assign('tpl', 'tpls/addEquipment.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	/**
	 * bookmarkDEquipment($vars)     
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */       
	protected function bookmarkDEquipment($vars)
	{			
		extract($vars);
		
		$sortStr=$this->sortList('equipment',3);
		$equipments = new Equipment($this->db);
		$equipmentList = $equipments->getEquipmentList($this->getFromRequest('id'),$sortStr);	
		
		
				
		if (!is_null($this->getFromRequest('export'))) 
		{
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Equipments of department ".$departmentDetails['name'];
			if ($this->getFromRequest('searchAction')=='search') {
				$exporter->search_term = $this->getFromRequest('q');
			} 
			else 
			{
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
				'equipment_id' => 30,
				'equip_desc' => 70,											
			);
			$header = array(
				'equipment_id' => 'ID Number',
				'equip_desc' => 'Equipemnt Name',																													
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($equipmentList);
			$exporter->export();
			die();
			
		} 
		else 
		{
			$equipmentHover = new Hover();	
			$countEquipment=$equipments->queryTotalCount($this->getFromRequest('id'));	
										
			for ($i=0; $i<$countEquipment; $i++) 
			{
				$url="?action=viewDetails&category=equipment&id=".$equipmentList[$i]['equipment_id']."&departmentID=".$this->getFromRequest('id');
				$equipmentList[$i]['url']=$url;
				
				//	Check if expired or pre expired
				$equipment = new Equipment($this->db);
				$equipment->initializeByID($equipmentList[$i]['equipment_id']);
				
				if ($equipment->isPreExpired()) 
				{
					$equipmentList[$i]["valid"] = "preexpired";
					$equipmentList[$i]["hoverMessage"] = $equipmentHover->equipmentPreExpired();
				} 
				else if ($equipment->isExpired()) 
				{
					$equipmentList[$i]["valid"] = "expired";
					$equipmentList[$i]["hoverMessage"] = $equipmentHover->equipmentExpired();
				} 
				else 
				{
					$equipmentList[$i]["valid"] = "valid";
					$equipmentList[$i]["hoverMessage"] = $equipmentHover->equipmentValid();
				}
			}
				
			
			$this->smarty->assign('childCategoryItems',$equipmentList);
			
			
			//vps part
			$vpsSaysNo = false;
			$voc2vps = new VOC2VPS($this->db);
			$customerLimits = $voc2vps->getCustomerLimits($companyDetails['company_id']);
			
			if ($customerLimits['Source count']['current_value'] >= $customerLimits['Source count']['max_value']) 
			{
				//disable add button
				$vpsSaysNo = true;
			}
			$this->smarty->assign('vpsSaysNo',$vpsSaysNo);
			
			//Set Notify
			$notify=new Notify($this->smarty);
			if ($this->user->isHaveAccessTo('add', 'equipment') && $vpsSaysNo) 
			{
				$notify->billingPlanLimitations('equipment');
			} 
			else 
			{
				if (count($equipmentList)==0) 
				{
					$notify->emptyCategory("insideDepartment",$this->getFromRequest('categoryID'));
				}
			}
			
			//	set js scripts
			$jsSources = array(
				'modules/js/checkBoxes.js',									
			);
			$this->smarty->assign('jsSources', $jsSources);
			
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/equipmentListNew.tpl');
		}
	}
}
?>