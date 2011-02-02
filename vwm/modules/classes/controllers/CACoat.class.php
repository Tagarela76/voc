<?php

class CACoat extends Controller {
	
	function CACoat($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='coat';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkCoat($vars) {
		extract($vars);
		$coat=new Coat($this->db);
		
		$pagination = new Pagination($coat->queryTotalCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=coat".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 
		
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY coat_desc  ";
		}
		$coatList=$coat->getCoatList($pagination,$filterStr,$sortStr);
		$itemsCount=count($coatList);
		$field='coat_id';
		$list=$coatList;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=coat&id=".$coatList[$i]['coat_id'];
			$coatList[$i]['url']=$url;
		}
		
		//	$smarty->assign("bookmarkType","coat");
		//	$smarty->assign("categoryType","class");
		$this->smarty->assign("category",$coatList);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/coatClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$coat=new Coat($this->db);
		$coatDetails=$coat->getCoatDetails($this->getFromRequest('id'));
		$this->smarty->assign("coat",$coatDetails);
		$this->smarty->assign('tpl', 'tpls/viewCoat.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$coat=new Coat($this->db);
		if ($this->getFromPost('save')=='Save') {
			$data=array(
				"coat_id"	=>	$id,
				"coat_desc"	=>	strtoupper($this->getFromPost("coat_desc"))
			);									
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			if (!($validate->isUniqueName("coat", $data["coat_desc"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['coat_desc'] = 'alredyExist';
			}								
			
			if ($validStatus["summary"] == "true") {
				$coat->setCoatDetails($data);
				header ('Location: admin.php?action=viewDetails&category=coat&id='.$id);
				die();										
			} 									
		}
		else
		{									
			$data=$coat->getCoatDetails($id);
		}								
		
		//	IF ERRORS OR NO POST REQUEST
		if ($validStatus["summary"] == "false") 
		{
			//$notify=new Notify($smarty);
			//$notify->formErrors();
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));
		}
		$this->smarty->assign('tpl','tpls/addCoatClass.tpl');
		$this->smarty->assign("data",$data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$coatData=array(
				"coat_desc"	=>	strtoupper($this->getFromPost('coat_desc'))
			);
			
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($coatData);
			
			if (!($validation->isUniqueName("coat", $coatData["coat_desc"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['coat_desc'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				$coat=new Coat($this->db);
				$coat->addNewCoat($coatData);									
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=coat');
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
		$this->smarty->assign("data",$coatData);
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl',"tpls/addCoatClass.tpl");
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$coat=new Coat($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$coatDetails=$coat->getCoatDetails($this->getFromRequest('item_'.$i));
				$item["id"]	=$coatDetails["coat_id"];
				$item["name"] =$coatDetails["coat_desc"];
				$itemForDelete []= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$coat=new Coat($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$coat->deleteCoat($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>