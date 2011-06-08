<?php

/**
 * Description of CVBilling
 *
 * @author ilya.iz@kttsoft.com
 */
class CVBilling extends Controller {

    function CVBilling($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
    
    public function runAction() {
		$this->runCommon('vps');
        
        if($this->getFromRequest("subCategory")) {
            $category = $this->getFromRequest("category");
            $this->setBookmarks($category);

            $controller = $this->getFromRequest("subCategory");
            $function = "action".ucfirst($this->getFromRequest("action"));
            //echo "billing forvard $controller :: $function";
            $this->forvard($controller, $function, null, 'vps');
        }
        else {
            $functionName='action'.ucfirst($this->action);				
            if (method_exists($this,$functionName))			
                $this->$functionName();		
        }
	}
    
    public function actionConfirmEdit() {
        $customerID = $_SESSION['customerID'];
        $subCategory = $this->getFromPost('subCategory');
        $applyWhen = $this->getFromPost('applyWhen');
        

        if ($applyWhen == 'bpEnd' || $applyWhen == 'asap') {
            //$applyWhen = $_POST['applyWhen'];
            //not expected variable. fraud?	
        } else {
            die("no applyWhen");
            return;
        }
        $newBillingPlanID = $this->getFromPost('changeTo');

        $billing = new Billing($this->db);

        $currentCurrency = $billing->getCurrencyByCustomer($customerID);
        $newBillingDetails = $billing->getBillingPlanDetails($newBillingPlanID, false, $currentCurrency['id']);
        $currentBillingPlan = $billing->getCustomerPlan($customerID);	
        //var_dump($currentBillingPlan);
        //exit;
        $this->db->beginTransaction();

        if($newBillingDetails['type'] != $currentBillingPlan['type'] and $applyWhen == "asap")
        {
            $appliedModules = $billing->getPurchasedModule($customerID,null,'today&future','today',$currentCurrency['id']);
            //echo "CANCEL ALL MODULES";
            //var_dump($appliedModules);
            foreach($appliedModules as $module)
            {
                $billing->removeModuleBillingPlan($customerID,$module['id']);
                //echo "<br/>module {$module['id']} deleted<br/>";
            }
        }


        
        
        $result = $billing->setScheduledPlan($customerID, $newBillingPlanID, $applyWhen);

        //echo "<a href='vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan'>next</a>";
        //exit;
        $this->db->commitTransaction();
        header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
    }
    
    public function actionEditCategory() {
        
        //$this->db->beginTransaction();
        $customerID = $_SESSION['customerID'];
        $selectedBillingPlanID = $this->getFromRequest('selectedBillingPlan');

        $billing = new Billing($this->db);

        $currentCurrency = $billing->getCurrencyByCustomer($customerID);
        $this->smarty->assign("currentCurrency",$currentCurrency);

        $newBillingDetails = $billing->getBillingPlanDetails($selectedBillingPlanID, false, $currentCurrency['id']);
        $currentBillingPlan = $billing->getCustomerPlan($customerID);																

        //check current and new plan. if equal than go to billing plan page
        if ($currentBillingPlan['billingID'] == $newBillingDetails['billingID']) {
            header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");
            return;
        }


        //manage applyWhen
        
        $applyWhen = $this->getFromRequest('applyWhen');
        switch ($applyWhen) {
            case 'bpEnd':
                $invoice = new Invoice($this->db);
                $currentInvoice = $invoice->getCurrentInvoice($customerID);
                if ($currentInvoice['periodEndDate'] == NULL) {
                    $firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
                    $dateWhenNewPlanWillBeImplemented = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($firstInvoice['periodStartDate'], false) ;											
                } else {
                    $dateWhenNewPlanWillBeImplemented = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($currentInvoice['periodEndDate'], false) ;	;
                }																	
                //$dateWhenNewPlanWillBeImplemented = substr($currentInvoice['periodEndDate'],0,10);	//	maybe +1 day?										
                break;
            case 'asap':										
                $dateWhenNewPlanWillBeImplemented = "ASAP";
                break;
            default:
                header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");
                return;
            break;
        }			

        if($currentBillingPlan['type'] != $newBillingDetails['type'] and $applyWhen == "asap")
        {
            $notification = "New type of billing plan from <b>{$currentBillingPlan['type']}</b> to <b>{$newBillingDetails['type']}</b> as soon as posible (ASAP). <b>All current modules will be canceled!</b>";
        }

        $this->smarty->assign("notification",$notification);
        $this->smarty->assign("applyWhen",$applyWhen);																	
        $this->smarty->assign("dateWhenNewPlanWillBeImplemented",$dateWhenNewPlanWillBeImplemented);

        $areYouSureAction = "change billing plan";
        $from = $currentBillingPlan;
        $to = $newBillingDetails;

        $title = "Change billing plan";
        $this->smarty->assign("title",$title);																							

        $this->smarty->assign("areYouSureAction",$areYouSureAction);

        $this->smarty->assign("from",$from);
        $this->smarty->assign("to",$to);																		

        $this->smarty->assign("category","billing");
        $this->smarty->assign("currentBookmark",$_GET["action"]);
        $this->smarty->display("tpls:vps.tpl");
    }
}

?>