<?php

class CAdditionalEmailAccounts extends Controller {

    function CAdditionalEmailAccounts($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'AdditionalEmailAccounts';
        $this->parent_category = 'facility';
    }

    function runAction() {

        $this->runCommon();
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }
	
    private function actionDeleteItem() {
		//	Access control
		if (!$this->user->isHaveAccessTo('view', 'company')) {
			throw new Exception('deny');
		}
        $addEmailAccountsIds = $this->getFromRequest('id');
		$companyId = $this->getFromRequest('companyId');
        if (!is_array($addEmailAccountsIds))
            $addEmailAccountsIds = array($addEmailAccountsIds);

        if (!is_null($this->getFromRequest('id'))) {
            foreach ($addEmailAccountsIds as $addEmailAccountsId) {
                $additionalEmailAccounts = new AdditionalEmailAccounts($this->db, $addEmailAccountsId);
				$additionalEmailAccounts->delete();
            }
        }	
		// we need return a new refresh email accounts list
		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccountsList = $additionalEmailAccounts->getAdditionalEmailAccountsByCompany($companyId);
		$this->smarty->assign('additionalEmailAccountsList', $additionalEmailAccountsList);
		$result = $this->smarty->fetch("tpls/additionalEmailAccounts.tpl");
		
		echo $result;
		
    }
	
    private function actionAddItem() {
		//	Access control
		if (!$this->user->isHaveAccessTo('view', 'company')) {
			throw new Exception('deny');
		}
        $request = $this->getFromRequest();
		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccounts->username = $request["emailAccountUserName"];
		$additionalEmailAccounts->email = $request['emailAccountUserEmail']; 
		$additionalEmailAccounts->company_id = $request['companyId']; 
		$additionalEmailAccountsId = $additionalEmailAccounts->save();
		if (!$additionalEmailAccountsId) {
			echo "error";
			return false;
		}
        // we need return a new refresh email accounts list
		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccountsList = $additionalEmailAccounts->getAdditionalEmailAccountsByCompany($request['companyId']);
		$this->smarty->assign('additionalEmailAccountsList', $additionalEmailAccountsList);
		$result = $this->smarty->fetch("tpls/additionalEmailAccounts.tpl");
		
		echo $result;
    }

}

?>