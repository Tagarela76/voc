<?php

class CAApmethod extends Controller {
	
	function CAApmethod($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='apmethod';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);	
					
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkApmethod($vars) {
		extract($vars);
		$apmethod=new Apmethod($this->db);
		$pagination = new Pagination($apmethod->queryTotalCount());
		$pagination->url = "?action=browseCategory&category=tables&bookmark=apmethod";
		$apmethodList=$apmethod->getApmethodList($pagination);
		$field='apmethod_id';
		$list = $apmethodList;
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=apmethod&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/apmethodClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$apmethod=new Apmethod($this->db);
		$apmethodDetails=$apmethod->getApmethodDetails($this->getFromRequest('id'));
		$this->smarty->assign("apmethod",$apmethodDetails);
		$this->smarty->assign('tpl', 'tpls/viewApmethod.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$apmethod=new Apmethod($this->db);							
		if ($this->getFromPost('save') == 'Save') {	
			$data=array(
				"apmethod_id"	=>	$id,
				"apmethod_desc"	=>	$_POST["apmethod_desc"]
			);									
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			if (!($validate->isUniqueName("apmethod", $data["apmethod_desc"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['apmethod_desc'] = 'alredyExist';
			}								
			if ($validStatus["summary"] == "true") {
				$apmethod->setApmethodDetails($data);
				header ('Location: admin.php?action=viewDetails&category=apmethod&id='.$id);
				die();											
			}
		}
		else {									
			$data=$apmethod->getApmethodDetails($id);
		}								
		
		//	IF ERRORS OR NO POST REQUEST	
		if ($validStatus["summary"] == "false") 
		{	
			//$notify=new Notify($smarty);
			//$notify->formErrors();
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));
		}
		$this->smarty->assign('data', $data);
		$this->smarty->assign('tpl','tpls/addApmethodClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$apmethodData=array(
				"apmethod_desc"	=>	$this->getFromPost('apmethod_desc')
			);
			
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($apmethodData);
			
			if (!($validation->isUniqueName("apmethod", $apmethodData["apmethod_desc"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['apmethod_desc'] = 'alredyExist';
			}								
			if ($validStatus['summary'] == 'true') {	
				$apmethod=new Apmethod($this->db);							
				$apmethod->addNewApmethod($apmethodData);								
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=apmethod');
				die();										
			} 
			else 
			{
				//$notify=new Notify($this->smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromRequest('category'));
			}
		}
		$this->smarty->assign("data",$apmethodData);
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl',"tpls/addApmethodClass.tpl");
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$apmethod=new Apmethod($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$apmethodDetails=$apmethod->getApmethodDetails($this->getFromRequest('item_'.$i));
				$item["id"]	=$apmethodDetails["apmethod_id"];
				$item["name"]	=$apmethodDetails["apmethod_desc"];
				$itemForDelete []= $item;
			}
		}
		$this->smarty->assign("gobackAction","viewDetails");
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$apmethod=new Apmethod($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$apmethod->deleteApmethod($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>