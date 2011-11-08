<?php
class CSCompanyRequest extends Controller
{
	function CSCompanyRequest($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='companyRequest';
		$this->parent_category='forms';
	}
	
	function runAction()
	{
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function actionBrowseCategory($vars)
	{
		$this->bookmarkCompanyRequest($vars);
	}	
	
	protected function bookmarkCompanyRequest($vars){
		extract($vars);
		
		$cemail = new EMail();
		$cSetupRequest = new SetupRequest($this->db);
		
		$to = array ("denis.nt@kttsoft.com", "dmitry.vd@kttsoft.com", "jgypsyn@gyantgroup.com ");
		//$to = "dmitry.ds@kttsoft.com";
		
		//$from = "authentification@vocwebmanager.com";
		$from = AUTH_SENDER."@".DOMAIN;
		$theme = "Company setup request";
		
		$message .= "Company Name: ".$_POST['name']."\r\n\r\n";
		$message .= "Email:".$_POST['email']."\r\n\r\n";
		
		if ($_POST["submitForm"] == "Submit") {
			$cSetupRequest->setName($_POST['name']);
			$cSetupRequest->setAddress($_POST['address']);
			$cSetupRequest->setCity($_POST['city']);
			$countryID = $_POST['country'];
			$cSetupRequest->setCountryID($countryID);
			if ($countryID == '215'){
				$this->db->query("SELECT name FROM ".TB_STATE." WHERE state_id=".$_POST['stateSelect']);
				$cSetupRequest->setState($this->db->fetch(0)->name);
				$cSetupRequest->setStateID($_POST['stateSelect']);
			} else {
				$cSetupRequest->setState($_POST['stateText']);
				$cSetupRequest->setStateID('NULL');
			}
			$cSetupRequest->setCounty('NULL');
			$cSetupRequest->setParentID('NULL');
			$cSetupRequest->setZipCode($_POST['zip']);
			$cSetupRequest->setPhone($_POST['phone']);
			$cSetupRequest->setContact($_POST['contact']);
			$cSetupRequest->setEmail($_POST['email']);
			$cSetupRequest->setFax($_POST['fax']);
			$cSetupRequest->setTitle($_POST['title']);
			$cSetupRequest->setCreaterID($_SESSION['user_id']);
			$errorSave = $cSetupRequest->save('company');
			if ($errorSave == ''){
				$cemail->sendMail($from, $to, $theme, $message);
				header("Location: sales.php?action=browseCategory&category=dashboard");
			} else {
				$error = $errorSave;
			}
		}
		$cCountry = new Country($this->db);
		$countryList = $cCountry->getCountryList();
		$this->smarty->assign('countryList', $countryList);
		
		$cState = new State($this->db);
		$stateList = $cState->getStateList();
		$this->smarty->assign('stateList', $stateList);
		
		$this->smarty->assign('setupRequest', $cSetupRequest);
		
		$this->smarty->assign('error', $error);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl','tpls/companyRequestForm.tpl');
	}
}
?>