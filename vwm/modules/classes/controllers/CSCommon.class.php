<?php

class CSCommon extends Controller {

    function CSCommon($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
	
	function runAction() {
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionLogout() {
		$this->user->logout();
	} 
	
	private function actionStats() {
		if (!is_null($this->getFromPost('startDate')) && !is_null($this->getFromPost('finishDate'))) {
			//	js give us timestamp with milliseconds
			$timestampStart = floor($this->getFromPost('startDate')/1000);
			$timestampFinish = floor($this->getFromPost('finishDate')/1000);
			
			$stats = new Statistics($this->db);
			$output = $stats->show($timestampStart, $timestampFinish);
			
			echo json_encode($output);
		} else {
			$this->smarty->display("tpls:stats.tpl");	
		}
	}

    private function actionRemoteAuth() {
        //  set custom auth type
        $this->xnyo->auth_type = 'vwm_token';
        VOCApp::getInstance()->getUser()->auth->login(145,'9cd625db4c635d9596a0688e707bc202');
        var_dump($this->xnyo->user);
    }
}
?>