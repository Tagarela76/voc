<?php
class CSContracts extends Controller
{
	function CSContracts($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='contracts';
		$this->parent_category='contracts';
	}
	
	function runAction()
	{
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory()
	{
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mDocs = new $moduleMap['docs'];

		$params = array(
			'db' => $this->db,
			'isSales' => 'yes',
			'salesID' => '1'
		);
		$result = $mDocs->prepareView($params);
		
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		
		$itemsCount = count($result['InfoTree']);
		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl','tpls/contracts.tpl');
		$this->smarty->display("tpls:index.tpl");		
	}	
}
?>