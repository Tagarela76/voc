<?php

use VWM\Label\LabelManager;
use VWM\Label\CompanyLevelLabel;

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

		$industryTypeManager = new IndustryTypeManager($this->db);
		
		// get industry types count
		if (!is_null($this->getFromRequest('q'))){
			$itemsCount = $industryTypeManager->searchTypeResultsCount($this->getFromRequest('q'));
		} else {
			$itemsCount = $industryTypeManager->getIndustryTypesCount();
		}
		// Pagination
		$url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($itemsCount);
		$pagination->url = $url; 
		$this->smarty->assign('pagination', $pagination);
		
		if (!is_null($this->getFromRequest('q'))){
			$allTypes = $industryTypeManager->searchType($this->getFromRequest('q'), $pagination);
		} else {
			$allTypes = $industryTypeManager->getIndustryTypes($pagination);
		}

		$i = 0;
		foreach ($allTypes as $item){
			$allTypes[$i]->url = 'admin.php?action=viewDetails&category=industryType&id='.$item->id;
			$i++;
		}

		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('allTypes', $allTypes);
		$this->smarty->assign('tpl', 'tpls/industryTypeClass.tpl');
	}
	
	private function actionViewDetails() {
		
		$industryType = new IndustryType($this->db, $this->getFromRequest('id')); 
		$subIndustryTypes = $industryType->getSubIndustryTypes();
		// get industry type label list
		$labelSystem = new LabelManager($this->db, $this->getFromRequest('id'));
		$labelList = $labelSystem->getLabelList();	
		$this->smarty->assign('industryLabelList', $labelList);
		$this->smarty->assign('typeDetails', $industryType);
		$this->smarty->assign('subIndustryTypes', $subIndustryTypes);
		$this->smarty->assign('tpl', 'tpls/viewIndustryType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {

		$industryType = new IndustryType($this->db, $this->getFromRequest('id')); 
		$post  = $this->getFromPost();
		// get industry type label list
		$labelManager = new LabelManager($this->db, $this->getFromRequest('id'));
		$labelList = $labelManager->getLabelList();	
		$this->smarty->assign('industryLabelList', $labelList);
		if ($this->getFromPost('save') == 'Save') {	
			$industryType->type = $post["type"];
			$violationList = $industryType->validate(); 
			if(count($violationList) == 0) {		
				$industryType->save();
				if ($post["repair_order"] == "") {					
					$notifyc = new Notify(null, $this->db);
					$notify = $notifyc->getPopUpNotifyMessage(401);
					$this->smarty->assign("repairOrderError", 'true');
					$this->smarty->assign("notify", $notify);						
					$this->smarty->assign('data', $industryType);
				} else {
					// save repair_order label
					$labelManager->saveRepairOrderLabel($post["repair_order"]);
					// redirect
					header("Location: ?action=viewDetails&category=industryType&id=" . $this->getFromRequest('id') . "&&notify=54");
				}
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
		$this->smarty->assign("currentOperation","edit");
		$this->smarty->assign('tpl','tpls/addIndustryTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
        
        $industryType = new IndustryType($this->db);
        $post = $this->getFromPost();
        if ($this->getFromPost('save') == 'Save'){
            $industryType->type = $post["type"];
            $industryType->setValidationGroup("add");
            $violationList = $industryType->validate(); 
            if(count($violationList) == 0) {		
                $industryType->save();
                // redirect
                header("Location: ?action=browseCategory&category=tables&bookmark=industryType&notify=55");
            } else {						
                $notifyc = new Notify(null, $this->db);
                $notify = $notifyc->getPopUpNotifyMessage(401);
                $this->smarty->assign("notify", $notify);						
                $this->smarty->assign('violationList', $violationList);
                $this->smarty->assign('data', $post);
            }
        }
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl', 'tpls/addIndustryTypeClass.tpl');
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