<?php
class CSupSales extends Controller
{
	function CSupSales($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='sales';
		$this->parent_category='sales';
	}
	
	function runAction()
	{
		$this->runCommon('supplier');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory(){
		
		$request = $this->getFromRequest();
		$bookmark = $this->getFromRequest('bookmark');
		$jsSources = array();
		array_push($jsSources, 'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		
		$this->smarty->assign('request', $request);				
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'supplier');
		$this->smarty->display("tpls:index.tpl");		
	}
}
?>