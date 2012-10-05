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
		
        $industryTypeManager = new IndustryTypeManager($this->db);
		
		// get industry types count
		if (!is_null($this->getFromRequest('q'))){
			$itemsCount = $industryTypeManager->searchSubTypeResultsCount($this->getFromRequest('q'));
		} else {
			$itemsCount = $industryTypeManager->getSubIndustryTypesCount();
		}
		// Pagination
		$url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($itemsCount);
		$pagination->url = $url; 
		$this->smarty->assign('pagination', $pagination);
		
		if (!is_null($this->getFromRequest('q'))){
			$allTypes = $industryTypeManager->searchSubType($this->getFromRequest('q'), $pagination);
		} else {
			$allTypes = $industryTypeManager->getSubIndustryTypes($pagination);
		}

		$i = 0;
		foreach ($allTypes as $item){
			$allTypes[$i]->url = 'admin.php?action=viewDetails&category=industrySubType&id='.$item->id;
            $parenIndustryTypes = new IndustryType($this->db, $allTypes[$i]->parent);
            $allTypes[$i]->parentIndustryType = $parenIndustryTypes->type;
			$i++;
		}

		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('subIndustryTypes', $allTypes);
		$this->smarty->assign('tpl', 'tpls/industrySubTypeClass.tpl');
	}
	
	private function actionViewDetails() {

        $subIndustryType = new IndustryType($this->db, $this->getFromRequest('id')); 
        $industryType = new IndustryType($this->db, $subIndustryType->parent); 

		$this->smarty->assign('typeDetails', $subIndustryType);
		$this->smarty->assign('parentIndustryTypes', $industryType);
		$this->smarty->assign('tpl', 'tpls/viewIndustrySubType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {

		$industryType = new IndustryType($this->db, $this->getFromRequest('id')); 
        $industryTypeTypeManager = new IndustryTypeManager($this->db);
        $industryTypes = $industryTypeTypeManager->getIndustryTypes();
        $this->smarty->assign("industryTypes",$industryTypes);
		$post  = $this->getFromPost();
		if ($this->getFromPost('save') == 'Save') {	
			$industryType->type = $post["industrySubType"];
            $industryType->parent = $post["industrySubTypeParent"];
            //var_dump($industryType); die();
			$violationList = $industryType->validate(); 
			if(count($violationList) == 0) {		
				$industryType->save();
				// redirect
				header("Location: ?action=viewDetails&category=industrySubType&id=" . $this->getFromRequest('id') . "&&notify=56");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $post);
			}	
		} else {
            $this->smarty->assign('data', $industryType);
        }						
		$this->smarty->assign('tpl','tpls/addIndustrySubTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
        
        $industryType = new IndustryType($this->db);
        $industryTypeTypeManager = new IndustryTypeManager($this->db);
        $industryTypes = $industryTypeTypeManager->getIndustryTypes();
        $this->smarty->assign("industryTypes",$industryTypes);
        $post = $this->getFromPost();
        if ($this->getFromPost('save') == 'Save'){
            $industryType->type = $post["industrySubType"];
            $industryType->parent = $post["industrySubTypeParent"];
            $industryType->setValidationGroup("add");
            $violationList = $industryType->validate(); 
            if(count($violationList) == 0) {		
                $industryType->save();
                // redirect
                header("Location: ?action=browseCategory&category=tables&bookmark=industrySubType&notify=57");
            } else {						
                $notifyc = new Notify(null, $this->db);
                $notify = $notifyc->getPopUpNotifyMessage(401);
                $this->smarty->assign("notify", $notify);						
                $this->smarty->assign('violationList', $violationList);
                $this->smarty->assign('data', $post);
            }
        }
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl', 'tpls/addIndustrySubTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount = $this->getFromRequest('itemsCount');
		$itemForDelete = array();

		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
                $industrytype = new IndustryType($this->db, $this->getFromRequest('item_'.$i));
				$item["id"]	= $industrytype->id;
				$item["name"] = $industrytype->type;
				$itemForDelete[] = $item;
			}
		}
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount = $this->getFromRequest('itemsCount');

		for ($i=0; $i<$itemsCount; $i++) {
			$industrytype = new IndustryType($this->db, $this->getFromRequest('item_'.$i));
            $industrytype->delete();
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>