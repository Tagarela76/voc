<?php
class CSForms extends Controller
{
	function CSForms($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='forms';
		$this->parent_category='forms';
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
		$bookmark = $this->getFromRequest('bookmark');
		$jsSources = array();
		array_push($jsSources, 'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'sales');
		$this->smarty->display("tpls:index.tpl");		
	}	
}
?>