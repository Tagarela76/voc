<?php
class CSTutorials extends Controller
{
	function CSTutorials($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='tutorials';
		$this->parent_category='tutorials';
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
		$trainingParts = array('login' => 'How to Login',
							   'overview' => 'Overview', 
							   'report' => 'Create Report', 
							   'graph' => 'Company at a Glance Graphs', 
							   'payment' => 'Payment Process',
							   'training' => 'See Entire Video',
							   'npvideo' => 'New Product Video');
		
		$this->smarty->assign('trainingParts', $trainingParts);
		$this->smarty->assign('category', 'company');
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl','tpls/videoTutorials.tpl');
		$this->smarty->display("tpls:index.tpl");		
	}	
}
?>