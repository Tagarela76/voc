<?php
class CSupRoot extends Controller
{
	function CSupRoot($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='root';

	}
	
	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory(){

		$request = $this->getFromRequest();
		$jobberManager = new JobberManager($this->db);
		
		$inventoryManager = new InventoryManager($this->db);
				
		$jobberList = $jobberManager->getJobberList();
		if ($jobberList){
			foreach($jobberList as $jobber){
				$supplierIDS = $inventoryManager->getSuppliersByJobberID($jobber->jobber_id);
				$jobber->url = "?action=browseCategory&category=sales&bookmark=clients&jobberID={$jobber->jobber_id}&supplierID={$supplierIDS[0]['supplier_id']}";
				$arr[] = $jobber;
			}
			$jobberList = $arr;
		}
		
		$urlRoot = "?action=browseCategory&category=root";
		$this->smarty->assign('urlRoot', $urlRoot);		
		
		$itemsCount = $jobberManager->getJobberCount();
		$this->smarty->assign('itemsCount', $itemsCount);

	

		$this->smarty->assign('childCategory', 'sales');
		
							
		//	set js
		$jsSources = array('modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('childCategoryItems', $jobberList);
		$this->smarty->assign('supplierID', $supplierIDS[0]['supplier_id']);
	//	$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'supplier');
		$this->smarty->assign('tpl','tpls/root.tpl');
		$this->smarty->display("tpls:index.tpl");	

	}
}
?>