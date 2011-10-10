<?php

class CAIndustryType extends Controller {
	
	function CAIndustryType($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='industryType';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	
	protected function actionBrowseCategory($vars) {			
		$this->bookmarkIndustryType($vars);
	}
	
	
	
	protected function bookmarkIndustryType($vars) {
		extract($vars);
		
		$productTypes = new ProductTypes($this->db);
		$allTypes = $productTypes->getAllTypes();
		$i = 0;
		foreach ($allTypes as $item){
			$allTypes[$i]['url'] = 'admin.php?action=viewDetails&category=industryType&id='.$item['id'];
			$i++;
		}
		$itemsCount = count($allTypes);
		
		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('allTypes', $allTypes);
		$this->smarty->assign('tpl', 'tpls/industryTypeClass.tpl');
	}
	
	private function actionViewDetails() {
		$productTypes = new ProductTypes($this->db);
		$typeDetails = $productTypes->getTypeDetails($this->getFromRequest('id'));
		$typeDetailsList['id'] = $this->getFromRequest('id');
		$typeDetailsList['type'] = $typeDetails['type'];
		$typeDetailsList['subTypes'] = $productTypes->getSubTypesByTypeID($this->getFromRequest('id'));
		
		$this->smarty->assign('typeDetails', $typeDetailsList);
		$this->smarty->assign('tpl', 'tpls/viewIndustryType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$productTypes = new ProductTypes($this->db);
		if ($this->getFromPost('save') == 'Save') {	
			$data = array(
				"industryType_id"	=>	$id,
				"industryType_desc"	=>	$_POST["industryType_desc"]
			);	
			$validStatus = $productTypes->validateBeforeSaveType($data);	
			if ($validStatus["summary"] == "true") {
				$productTypes->setType($data);
				header ('Location: admin.php?action=viewDetails&category=industryType&id='.$id);
				die();											
			}
		} else {									
			$tmpData = $productTypes->getTypeDetails($id);
			$data = array (
				"industryType_id"	=>	$id,
				"industryType_desc"	=>	$tmpData['type']
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
		$this->smarty->assign('tpl','tpls/addIndustryTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		$productTypes = new ProductTypes($this->db);
		if ($this->getFromPost('save') == 'Save'){
			$data = array(
				"industryType_desc"	=>	$_POST["industryType_desc"]
			);	
			$validStatus = $productTypes->validateBeforeSaveType($data);	
			if ($validStatus["summary"] == "true") {
				$productTypes->createNewType($data['industryType_desc']);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=industryType');
				die();											
			}
		} else {
			//$notify=new Notify($smarty);
			//$notify->formErrors();
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));
		}
		
		$this->smarty->assign('data', $data);
		$this->smarty->assign('validStatus', $validStatus);
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl', 'tpls/addIndustryTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount = $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$productTypes = new ProductTypes($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$productTypeDetails = $productTypes->getTypeDetails($this->getFromRequest('item_'.$i));
				$item["id"]	= $productTypeDetails['id'];
				$item["name"] = $productTypeDetails['type'];
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
			$productTypes->deleteType($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>