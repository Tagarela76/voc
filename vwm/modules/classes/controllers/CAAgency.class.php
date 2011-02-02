<?php

class CAAgency extends Controller {
	
	function CAAgency($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='agency';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkAgency($vars) {
		extract($vars);
		$agency=new Agency($this->db);
		
		$pagination = new Pagination($agency->getAgencyCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=agency".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 
		
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY a.name_us ";
		}
		$agencyList=$agency->getAgencyList('', $pagination,$filterStr,$sortStr);						
		$field='agency_id';
		$list=$agencyList;
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=agency&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/agencyClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$agency=new Agency($this->db);
		$agencyDetails=$agency->getAgencyDetails($this->getFromRequest('id'));
		$country = new Country($this->db);
		$countryData=$country->getCountryDetails($agencyDetails['country_id']);
		$agencyDetails['country']=$countryData['country_name'];
		$this->smarty->assign("agency", $agencyDetails);
		$this->smarty->assign('tpl', 'tpls/viewAgency.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
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