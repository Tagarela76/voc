<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVMSDSLimit
 *
 * @author ilya.iz@kttsoft.com
 */
class CVMSDSLimit extends Controller {

    function CVMSDSLimit($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
    
    public function runAction() {
		$this->runCommon('vps');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
    
    public function actionEditCategory() {
        
        $category = $this->getFromRequest('category');
        $subCategory = $this->getFromRequest('subCategory');
        
        $customerID = intval($_SESSION['customerID']);
        
        $plusToValue = intval($_GET['plusTo']);
        $this->smarty->assign("plusTo",$plusToValue);

        $billing = new Billing($this->db);	

        $currency = $billing->getCurrencyByCustomer($customerID);
        $this->smarty->assign("curentCurrency",$currency);

        $currentBillingPlan = $billing->getCustomerPlan($customerID);

        $areYouSureAction = "change MSDS limit";
        $from =	$currentBillingPlan;

        $to = $from;						
        $to['limits']['MSDS']['max_value'] += $plusToValue;

        $increaseCost = $to['limits']['MSDS']['increase_cost'] * ($plusToValue/$to['limits']['MSDS']['increase_step']);	//убого
        $this->smarty->assign("increaseCost",$increaseCost);

        $this->smarty->assign("areYouSureAction",$areYouSureAction);
        $this->smarty->assign("from",$from);
        $this->smarty->assign("to",$to);

        $title = "Increase MSDS limit";
        $this->smarty->assign("title",$title);	

        $this->smarty->assign("category","billing");
        $this->smarty->assign("subCategory",$subCategory);
        $this->smarty->assign("currentBookmark",$_GET["action"]);
        $this->smarty->display("tpls:vps.tpl");
    }
    
    public function actionConfirmEdit() {
        $plusToValue = intval($_POST['plusTo']);
		$customerID = intval($_SESSION['customerID']);
        $category = $this->getFromRequest('category');
        $subCategory = $this->getFromRequest('subCategory');
        $billing = new Billing($this->db);

        $currency = $billing->getCurrencyByCustomer($customerID);

        $limitName = 'MSDS';
        $billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue,$currency['id']);

        header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
    }
}

?>
