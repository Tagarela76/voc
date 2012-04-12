<?php
class CSDashboard extends Controller
{
	function CSDashboard($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='dashboard';
		$this->parent_category='dashboard';
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
		$jsSources = array();
		array_push($jsSources, 'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign("tpl", "tpls/dashboard.tpl");		
		$this->smarty->display("tpls:index.tpl");		
	}	
}
?>