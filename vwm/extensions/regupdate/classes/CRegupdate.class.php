<?php

class CRegupdate extends Controller {

    function CRegupdate($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='regupdate';
		$this->parent_category='facility';			
	}
	
	function runAction() {
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionMarkReaded() {
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest("facilityID"));
									
		if (!$this->user->checkAccess('regupdate', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		} 
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		
		//	OK, this company has access to this module, so let's setup..
		$mRegAct = new $moduleMap['regupdate'];
		
		$userID = $this->user->xnyo->user['user_id'];
		
		$mark = (is_null($this->getFromRequest("mark")))?$this->getFromPost("mark"):$this->getFromRequest("mark");
		$params = array(
				'db' => $this->db,
				'userID' => $userID,
				'mark' => ($mark == 'all')?$this->getFromRequest("tab"):$mark
			);
		$rin = $mRegAct->prepareMarkRead($params);
		
		if (!is_null($rin)) {
			header("Location: http://www.reginfo.gov/public/do/eAgendaViewRule?RIN=$rin ");
		} else {	
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromRequest("facilityID")."&bookmark=regupdate&tab=".$this->getFromRequest("tab") . "&notify=30");
		}
		die();
	}
    
   /**
     * bookmarkRegUpdate($vars)     
     * @vars $vars array of variables: $userID, $facility, $facilityDetails, $moduleMap
     */       
	protected function bookmarkRegupdate($vars) {	
		extract($vars);
													
		$facility->initializeByID($this->getFromRequest('id'));
									
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
									
		if (!$this->user->checkAccess('regupdate', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
									
		//	OK, this company has access to this module, so let's setup..
		$mRegAct = new $moduleMap['regupdate'];
		
		$userID = $this->user->xnyo->user['user_id'];
																		
		$params = array(
			'db' => $this->db,
			'tab' => $this->getFromRequest('tab'),
			'userID' => $userID,
			'facilityID' => $this->getFromRequest('id')
		);
		$result = $mRegAct->prepareView($params);
									
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}										

		$this->smarty->assign('tpl','regupdate/design/regUpdateView.tpl');
	}
}
?>