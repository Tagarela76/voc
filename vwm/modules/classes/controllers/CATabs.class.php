<?php

class CATabs extends Controller {
	
	function CATabs($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='tabs';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	
	protected function bookmarkTabs($vars) {
		extract($vars);
		
		$sl = new SL(REGION, $this->db);
		$this->smarty->assign('category', $sl->getLocaleConstants());
		$this->smarty->assign('tpl', 'tpls/tabsClass.tpl');
	}
	
	
		
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$sl = new SL(REGION, $this->db);
		$localeConstants = $sl->getLocaleConstantAsAssociativeArray();				
		
		if ($localeConstants[$id] == '') {
			throw new Exception('404');
		}
				
		$validStatus = true;
		
		if ($this->getFromPost('save') == 'Save') {
			//	POST received
			$data = array(
				'id'	=> $id,
				'string'=> trim(htmlentities($this->getFromPost('string')))
			);
			
			$validate=new Validation($this->db);
			$validStatus = $validate->check_tab_localization_string($data['string']);			
			
			if ($validStatus) {
				if (!$sl->setLocaleConstant($id, $data['string'])) {
					throw new Exception('Failed to update');
				}
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=tabs');
				die();
			} else {
				$notify=new Notify($this->smarty);
				$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}									
		}
		
		if (!isset($data)) {
			$data = array(
				'id'	=> $id,
				'string'=> $localeConstants[$id]
			);						
		}		
		
		$this->smarty->assign('validStatus',$validStatus);
		$this->smarty->assign('tpl','tpls/addTabsClass.tpl');
		$this->smarty->assign('data', $data);//var_dump($data);
		$this->smarty->display("tpls:index.tpl");
		
		die();
		$agency=new Agency($this->db);
		if ($this->getFromPost('save') == 'Save')
		{
			$nameMap = $agency->getNameMap();
			$data=array(
				"agency_id"	=>	$id,
				"name_us"	=>	$this->getFromPost("name_us"),
				"name_eu"	=>	$this->getFromPost("name_eu"),
				"name_cn"	=>	$this->getFromPost("name_cn"),
				"description"	=>	$this->getFromPost("description"),
				"country_id"		=>  $this->getFromPost("country"),
				"location"	=>	$this->getFromPost("location"),
				"contact_info"	=>	$this->getFromPost("contact_info"),
				"name"	=>	$this->getFromPost($nameMap[$agency->getRegion()])
			);
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			if ($validStatus['name'] == 'failed') {
				$validStatus[$nameMap[$agency->getRegion()]] = 'failed';
			}
			if (!($validate->isUniqueName("agency", $data["name"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus[$nameMap[$agency->getRegion()]] = 'alredyExist';
			}									
			
			if ($validStatus["summary"] == "true") {
				$agency->setAgencyDetails($data);										
				header ('Location: admin.php?action=viewDetails&category=agency&id='.$id);
				die();									
			} 
			else 
			{
				//$notify=new Notify($this->smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}
		}
		else
		{									
			$data=$agency->getAgencyDetails($id);
		}
		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		
		$this->smarty->assign("country",$countries);
		$this->smarty->assign('tpl','tpls/addAgencyClass.tpl');
		$this->smarty->assign('data', $data);//var_dump($data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$agency=new Agency($this->db);
			$nameMap = $agency->getNameMap();
			
			$agencyData=array(
				"agency_id"			=>	false,
				"name_us"			=>	$this->getFromPost('name_us'),
				"name_eu"			=>	$this->getFromPost('name_eu'),
				"name_cn"			=>	$this->getFromPost('name_cn'),
				"description"	 	=>	$this->getFromPost('description'),
				"country_id"		=>  $this->getFromPost('country'),
				"location"			=>	$this->getFromPost('location'),
				"contact_info"		=>	$this->getFromPost('contact_info'),
				"name"				=> $this->getFromPost($nameMap[$agency->getRegion()])
			);
			
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($agencyData);
			if ($validStatus['name'] == 'failed') {
				$validStatus[$nameMap[$agency->getRegion()]] = 'failed';
			}
			if (!($validation->isUniqueName("agency", $agencyData["name"]))) {
				$validStatus['summary'] = 'false';
				$validStatus[$nameMap[$agency->getRegion()]] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				
				$agency->addNewAgency($agencyData);
				
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=agency');
				die();										
			}
			else {
		//		$notify=new Notify($smarty);
		//		$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromRequest('category'));
			}
		}
		
		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		
		$this->smarty->assign("country",$countries);
		$this->smarty->assign("data",$agencyData);
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign('tpl', 'tpls/addAgencyClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$agency=new Agency($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$agencyDetails=$agency->getAgencyDetails($this->getFromRequest('item_'.$i));
				$item["id"] = $agencyDetails["agency_id"];
				$item["name"] = $agencyDetails["name"];
				$itemForDelete []= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$agency=new Agency($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$agency->deleteAgency($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>