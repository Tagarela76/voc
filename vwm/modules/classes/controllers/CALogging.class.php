<?php

class CALogging extends Controller {

    function CALogging($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='logging';
		$this->parent_category='logging';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$request = $this->getFromRequest();
		$sortStr=$this->sortList('logging',8);		

		//	get company list
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$this->smarty->assign('companyList',$companyList);
		
		//	get user list
		$users = new User($this->db);
		$userList = $users->getUsersList(null,null,null, 'ORDER by username ASC');
		$this->smarty->assign('userList',$userList);		
		
		$userID = $request['user_id'];
		$companyID = $request['company_id'];
		$facilityID = $request['facility_id'];
		$departmentID = $request['department_id'];

		$loggingManager = new UserLoggingManager($this->db);
		$itemsCount = $loggingManager->getCountLogs($userID, $companyID, $facilityID, $departmentID);	

		$pagination = new Pagination($itemsCount);
		$pagination->url = "?action=browseCategory&category=logging";
		//$logList = $loggingManager->getAllLogs($userID,$companyID,$facilityID,$departmentID, $sortStr, $pagination);

		
		// search
		if (!is_null($this->getFromRequest('q'))) 
		{

			$logToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));		
			$logList = $loggingManager->searchLog($logToFind,$companyID, $facilityID, $departmentID,$pagination);																						
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} 
		else 
		{
			$logList = $loggingManager->getAllLogs($userID,$companyID,$facilityID,$departmentID, $sortStr, $pagination);
		}

		if($logList) {	
			$logList = $loggingManager->getLogDataReadable($logList);
		}else{
			$itemsCount = 0;
		}	


		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/registration.js','modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("logList",$logList);
		$this->smarty->assign("itemsCount",$itemsCount);
		$this->smarty->assign('tpl', 'tpls/logging.tpl');
		$this->smarty->assign('pagination', $pagination);
		$this->smarty->display("tpls:index.tpl");		

    }
	
	private function actionViewDetails() {
					
		$loggingManager = new UserLoggingManager($this->db);
		$users = new User($this->db);
		$sortStr=$this->sortList('logging',8);
		
		$logID = $this->getFromRequest('id');
		$logDetails = $loggingManager->getLogDetail($logID);
		if (!$logDetails){
			throw new Exception('404');
		}
		$userID = $logDetails['user_id'];
		$userDetail = $users->getUserDetails($userID);
		
		$itemsCount = $loggingManager->getCountLogs($userID, $companyID, $facilityID, $departmentID);	

		$pagination = new Pagination($itemsCount);
		$pagination->url = "?action=viewDetails&category=logging&id={$logID}";
		$logList = $loggingManager->getAllLogs($userID,$companyID,$facilityID,$departmentID, $sortStr, $pagination);
		if($logList) {	
			$logList = $loggingManager->getLogDataReadable($logList);
		}
		
		$this->smarty->assign("logList",$logList);
		
		$action = json_decode($logDetails['action']);
		
		//var_dump($logDetails,$userDetail,$action );

		$this->smarty->assign("log", $logDetails);
		$this->smarty->assign("user", $userDetail);
		$this->smarty->assign("action", $action);
		
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
							
		
		$this->smarty->assign('editUrl','?action=edit&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('addUsageUrl','?action=addUsage&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));
		$this->smarty->assign('deleteUrl','?action=deleteItem&category=accessory&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID"));	
		$this->smarty->assign('backUrl','?action=browseCategory&category=accessory');
		$this->smarty->assign('tpl','tpls/viewLog.tpl');
		$this->smarty->display("tpls:index.tpl");
	}	
}
?>