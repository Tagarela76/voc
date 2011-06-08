<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVMyBillingPlan
 *
 * @author ilya.iz@kttsoft.com
 */
class CVMyBillingPlan extends Controller {

    function CVMyBillingPlan($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
    
    protected function actionViewDetails() {
        
        $customerID = $_SESSION['customerID'];
        //echo $customerID;
        $billing = new Billing($this->db);
        $currency = $billing->getCurrencyByCustomer($customerID);
        $groupedCurrencies = $billing->getCurrenciesList(true);						
        $this->smarty->assign("currentCurrency",$currency);
        $this->smarty->assign("groupedCurrencies",$groupedCurrencies);
        $this->smarty->assign("currentBookmark","MyBillingPlan");
        
        $customerPlan = $billing->getCustomerPlan($customerID);
								
								
        //defined plan is not processed yet by admin
        if (!$customerPlan) {
            $pleaseWait = "VOC WEB MANAGER's Administrator is working with your query. Please wait for some time. If have questions <a href=''>contact Administrator</a>.";
            $this->smarty->assign("pleaseWait",$pleaseWait);
        } else {
            foreach ($customerPlan['limits'] as $limit=>$value) {
                if ($value['default_limit'] == $value['max_value']) {
                    $customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type']." (free)"; 
                } else {
                    $customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type'];
                }		
            }//insert customer's limits info
            $invoice = new Invoice($this->db);

            $currentInvoice = $invoice->getCurrentInvoice($customerID);

            if ($currentInvoice['total'] === null) {
                $lastInvoice = $invoice->getLastInvoice($customerID);
                $totalInvoice = $lastInvoice['total'];
                $totalCurrency = $billing->getCurrencyDetails($lastInvoice['currency_id']); 	
            } else {
                $totalInvoice = $currentInvoice['billing_total_price'];
                $totalCurrency = $billing->getCurrencyDetails($currentInvoice['currency_id']);
            }									 				
            $customerPlan['one_time_charge'] = $currentInvoice['oneTimeCharge'] ? $currentInvoice['oneTimeCharge'] : 0;
            $customerPlan['price'] = $currentInvoice['amount'] ? $currentInvoice['amount'] : 0;

            $this->smarty->assign("billingPlan",$customerPlan);
            $this->smarty->assign("totalInvoice", number_format($totalInvoice, 2));
            $this->smarty->assign("totalCurrency", $totalCurrency);																							

            $invoice = new Invoice($this->db);
            $discountPercent = $invoice->getDiscount($customerID);

            if (!empty($discountPercent)) {
                $this->smarty->assign("discountPercent",$discountPercent);	
            }									
        }								
        $scheduledBillingPlan = $billing->getScheduledPlanByCustomer($customerID);

        $dt = new DateTime();
        $dateFormat = VOCApp::get_instance()->getDateFormat();

        if ( $scheduledBillingPlan && ($scheduledBillingPlan['billingID'] != $customerPlan['billingID']) ) {
            $currentCurrency = $billing->getCurrencyByCustomer($customerID);									
            $futurePlan = $billing->getBillingPlanDetails($scheduledBillingPlan['billingID'], false, $currentCurrency['id']);
            
            $invoice = new Invoice($this->db);
            $currentInvoice = $invoice->getCurrentInvoice($customerID);								

            if ($scheduledBillingPlan['type'] == "bpEnd") {																				
                //trial period
                if ($currentInvoice['periodEndDate'] == NULL) {
                    $firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
                    $dt->setTimestamp($firstInvoice['periodStartDate']);
                    
                    $futurePlanLabel = "Following scheduled Billing Plan will be applied when first Billing Period starts <b>(".$dt->format($dateFormat).")</b>:";											
                } else {
                    $dt->setTimestamp($currentInvoice['periodEndDate']);
                    $futurePlanLabel = "Following scheduled Billing Plan will be applied after current Billing Period end <b>(".$dt->format($dateFormat).")</b>:";
                }
            } elseif ($scheduledBillingPlan['type'] == "asap") {
                //trial period
                if ($currentInvoice['periodEndDate'] == NULL) {
                    $firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);	
                    $futurePlanLabel = "Following scheduled Billing Plan will be applied when invoice  <a href='vps.php?action=viewList&category=invoices&subCategory=Due'><b>ID ".$firstInvoice['invoiceID']."</b></a> will be paid:";											
                } else {
                    $invoiceForFututeBP = $invoice->getInvoiceForFuturePeriod($customerID);																						
                    $futurePlanLabel = "Following scheduled Billing Plan will be applied when invoice  <a href='vps.php?action=viewList&category=invoices&subCategory=Due'><b>ID ".$invoiceForFututeBP['invoiceID']."</b></a> will be paid:";
                }

            }

            $this->smarty->assign("futureBillingPlan",$futurePlan);								
            $this->smarty->assign("futurePlanLabel",$futurePlanLabel);

        }

        //modules in billing
        $currency = $billing->getCurrencyByCustomer($customerID);
        $modulesView = $billing->getPurchasedPlansForCustomerView($customerID,$currency['id']);


        //var_dump($modulesView['modules'][5]['plans'][0]);
        
        /*foreach($modulesView['modules'] as $module) {
            
            foreach($module['plans'] as $plan) {
                $dt->setTimestamp($plan['start']);
                $plan['start'] = 'ololo';
            }
        }*/


        $this->smarty->assign("appliedModules",$modulesView['modules']);
        if (!is_null($modulesView['bonus'])) {
            $this->smarty->assign("bonusModules",$modulesView['bonus']);
        }
        $title = "My Billing Plan";
        $this->smarty->assign("title",$title);																		
        $this->smarty->assign("category","billing");
        
		$this->smarty->display("tpls:vps.tpl");
    }
}

?>