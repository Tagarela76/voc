<?php

class CAIndustrySubType extends Controller {
	
	function CAIndustrySubType($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='industrySubType';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	
	protected function actionBrowseCategory($vars) {			
		$this->bookmarkIndustrySubType($vars);
	}
	
	
	
	protected function bookmarkIndustrySubType($vars) {
		extract($vars);
		
		$productTypes = new ProductTypes($this->db);
		if (!is_null($this->getFromRequest('q'))){
			$allSubTypes = $productTypes->searchSubType($this->getFromRequest('q'));
		} else {
			$allSubTypes = $productTypes->getAllSubTypes();
		}
		$i = 0;
		foreach ($allSubTypes as $item){
			$allSubTypes[$i]['url'] = 'admin.php?action=viewDetails&category=industrySubType&id='.$item['id'];
			$i++;
		}
		$itemsCount = count($allSubTypes);
		
		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('allSubTypes', $allSubTypes);
		$this->smarty->assign('tpl', 'tpls/industrySubTypeClass.tpl');
	}
	
	private function actionViewDetails() {
		$productTypes = new ProductTypes($this->db);
		$typeDetails = $productTypes->getSubTypeDetails($this->getFromRequest('id'));
		$typeDetailsList['id'] = $this->getFromRequest('id');
		$typeDetailsList['type'] = $typeDetails[0]['type'];
		$typeDetailsList['parentType'] = $typeDetails[0]['parentType'];
		
		$this->smarty->assign('typeDetails', $typeDetailsList);
		$this->smarty->assign('tpl', 'tpls/viewIndustrySubType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$productTypes = new ProductTypes($this->db);
		if ($this->getFromPost('save') == 'Save') {	
			$data = array(
				"industrySubType_id"	=>	$id,
				"industrySubType_desc"	=>	$_POST["industrySubType_desc"],
				"industrySubType_parentID" => $_POST["industrySubType_parent"],
				"industrySubType_parent" => $productTypes->getAllTypes()
			);
			$validStatus = $productTypes->validateBeforeSaveSubType($data);	
			if ($validStatus["summary"] == "true") {
				$productTypes->setSubType($data);
				header ('Location: admin.php?action=viewDetails&category=industrySubType&id='.$id);
				die();											
			}
		} else {									
			$tmpData = $productTypes->getSubTypeDetails($id);
			$data = array (
				"industrySubType_id"	=>	$id,
				"industrySubType_desc"	=>	$tmpData[0]['type'],
				"industrySubType_parentID" => $tmpData[0]['parent'],
				"industrySubType_parent" => $productTypes->getAllTypes()
			);
		}								
		
		//	IF ERRORS OR NO POST REQUEST	
		if ($validStatus["summary"] == "false") 
		{	
			//$notify=new Notify($smarty);
			//$notify->formErrors();
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));
		}
		
		$this->smarty->assign('validStatus', $validStatus);
		$this->smarty->assign('data', $data);
		$this->smarty->assign('tpl','tpls/addIndustrySubTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		$id = $this->getFromRequest('id');
		$productTypes = new ProductTypes($this->db);
		if ($this->getFromPost('save') == 'Save') {	
			$data = array(
				"industrySubType_id"	=>	$id,
				"industrySubType_desc"	=>	$_POST["industrySubType_desc"],
				"industrySubType_parentID" => $_POST["industrySubType_parent"],
				"industrySubType_parent" => $productTypes->getAllTypes()
			);
			$validStatus = $productTypes->validateBeforeSaveSubType($data);	
			if ($validStatus["summary"] == "true") {
				$indType = $productTypes->getTypeDetails($data['industrySubType_parentID']);
				$productTypes->createNewSubType($indType['type'], $data['industrySubType_desc']);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=industrySubType');
				die();											
			}
		} else {
			$data = array (
				"industrySubType_parent" => $productTypes->getAllTypes()
			);
			//$notify=new Notify($smarty);
			//$notify->formErrors();
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));
		}
		
		$this->smarty->assign('validStatus', $validStatus);
		$this->smarty->assign('data', $data);
		$this->smarty->assign('tpl','tpls/addIndustrySubTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount = $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$productTypes = new ProductTypes($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$productSubTypeDetails = $productTypes->getSubTypeDetails($this->getFromRequest('item_'.$i));
				$item["id"]	= $productSubTypeDetails[0]['id'];
				$item["name"] = $productSubTypeDetails[0]['type'];
				$item['parentName'] = $productSubTypeDetails[0]['parentType'];
				$itemForDelete[] = $item;
			}
		}
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount = $this->getFromRequest('itemsCount');
		$productTypes = new ProductTypes($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$productTypes->deleteSubType($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>