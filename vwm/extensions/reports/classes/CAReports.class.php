<?php

class CAReports extends Controller {

    function CAReports($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='reports';
		$this->parent_category='reports';		
	}
	
	function runAction() {
		
		$this->runCommon('admin');
		
		$functionName='action'.ucfirst($this->action);	
					
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$save=false;									
		if ($_POST['reportButton']=='save') 
		{		
			$save=true;			
			//header("Location: ?action=browseCategory&categoryID=reports");
			//die();																			
		}	
		if (class_exists('MReports')) {				
			$mReports= new MReports();
			$params = array(
				'db' => $this->db,
				'save'=>$save,
				'setCheckboxes'=>$this->getFromPost('reportID')								
			);
			$result = $mReports->prepareDoAdmin($params);
			
			foreach($result as $key => $data) 
			{
				$this->smarty->assign($key,$data);
			}							
			
			$jsSources = array(
				'modules/js/checkBoxes.js'									
			);
			
			$this->smarty->assign('jsSources', $jsSources);
		} else {
			throw new Exception('Deny');
		}
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->display("tpls:index.tpl");
		//header("Location: ?action=browseCategory&categoryID=reports");
		//die();	
	}
}
?>