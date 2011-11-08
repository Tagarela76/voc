<?php
class CSUserRequest extends Controller
{
	function CSUserRequest($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='userRequest';
		$this->parent_category='forms';
	}
	
	function runAction()
	{
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function actionBrowseCategory($vars) {
		$this->bookmarkUserRequest($vars);
	}
	
	protected function bookmarkUserRequest($vars)
	{
		extract($vars);
		$cUserRequest = new UserRequest($this->db);
		if ($_POST['submitForm'] == "Submit"){
			if ($_POST['new_accessname'] == '' || $_POST['new_username'] == '' || $_POST['email'] == '' || $_POST['company'] == ''){
				$error = "Incorrect data!";
			} else {
				$this->db->query("SELECT company_id FROM ".TB_COMPANY." WHERE name='".$_POST['company']."'");
				if ($this->db->num_rows() > 0){
					$companyID = $this->db->fetch(0)->company_id;
					$cUserRequest->setALL('add', 'NULL', 'NULL', $_POST['new_username'], $_POST['new_accessname'], $_POST['email'], $_POST['phone'], $_POST['mobile'], 'company', $companyID);
					$cUserRequest->setCreaterID($_SESSION['user_id']);
					$errorSave = $cUserRequest->save();
					if ($errorSave == ''){
						$cUserRequest->sendMail('Please, create new user.');
						header("Location: sales.php?action=browseCategory&category=dashboard");
					} else {
						$error = $errorSave;
					}
				} else {
					$cUserRequest->setALL('add', 'NULL', 'NULL', $_POST['new_username'], $_POST['new_accessname'], $_POST['email'], $_POST['phone'], $_POST['mobile'], 'company', $companyID);
					$cUserRequest->setCreaterID($_SESSION['user_id']);
					$error = "Incorrect Company Name!";
				}
			}
		}
		
		$this->smarty->assign('userRequest', $cUserRequest);
		
		$this->smarty->assign('error', $error);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl','tpls/userRequestForm.tpl');
	}	
}
?>