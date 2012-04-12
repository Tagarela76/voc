<?php

class CASubstrate extends Controller {
	
	function CASubstrate($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='substrate';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkSubstrate($vars) {
		extract($vars);
		$substrate=new Substrate($this->db);
		$pagination = new Pagination($substrate->queryTotalCount());
		$pagination->url = "?action=browseCategory&category=tables&bookmark=substrate";
		$substrateList=$substrate->getSubstrateList($pagination);
		
		$field = 'substrate_id';
		$list = $substrateList;
		
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=substrate&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/substrateClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$substrate=new Substrate($this->db);
		$substrateDetails=$substrate->getSubstrateDetails($this->getFromRequest('id'));
		$this->smarty->assign("substrate",$substrateDetails);
		$this->smarty->assign('tpl', 'tpls/viewSubstrate.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$substrate=new Substrate($this->db);
		if ($this->getFromPost('save')=='Save') {	
			$data=array(
				"substrate_id"	=>	$id,
				"substrate_desc"	=>	$this->getFromPost("substrate_desc")
			);								
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			if (!($validate->isUniqueName("substrate", $data["substrate_desc"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = 'alredyExist';
			}																	
			if ($validStatus["summary"] == "true") {
				$substrate->setSubstrateDetails($data);
				header ('Location: admin.php?action=viewDetails&category=substrate&id='.$id);
				die();									
			}
			else
			{
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			} 
		}
		else
		{									
			$data=$substrate->getSubstrateDetails($id);
		}
		$this->smarty->assign('tpl','tpls/addSubstrateClass.tpl');
		$this->smarty->assign('data', $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$substrateData=array(
				"description"	=>	$this->getFromPost('substrate_desc')
			);
			
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($substrateData);
			
			if (!($validation->isUniqueName("substrate", $substrateData["description"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				$substrate=new Substrate($this->db);
				$substrate->addNewSubstrate($substrateData);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=substrate');
				die();
			} 
			else 
			{
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromRequest('category'));
			}
		}
		$this->smarty->assign("data",$substrateData);
		$this->smarty->assign("validStatus",$validStatus);								
		$this->smarty->assign('tpl', 'tpls/addSubstrateClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$substrate=new Substrate($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$substrateDetails	= $substrate->getSubstrateDetails($this->getFromRequest('item_'.$i));
				$item["id"]		= $substrateDetails["substrate_id"];
				$item["name"]	= $substrateDetails["substrate_desc"];
				$itemForDelete []	= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$substrate=new Substrate($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$substrate->deleteSubstrate($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>