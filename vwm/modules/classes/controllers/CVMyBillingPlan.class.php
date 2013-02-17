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
		$customer = new VPSCustomer($this->db,$customerID);
		$currency = $customer->getCurrency();
		
        //echo $customerID;
		if ($customer->billing_id == 0){ 
			$trial = 'true';
		}else{
			$trial = 'false';
		}
		$this->smarty->assign("trial",$trial);
		if ($customer->billing_id != 0){

			$billing = new VPSBilling($this->db,$customer->billing_id, $currency);
		}
		if ($customer->next_billing_id != null){
	
			$Nextbilling = new VPSBilling($this->db,$customer->next_billing_id, $currency);
		}
		$currency = get_object_vars($currency);

        //$currency = $billing->getCurrencyByCustomer($customerID);
        //$groupedCurrencies = $billing->getCurrenciesList(true);						
        $this->smarty->assign("currentCurrency",$currency);
        $this->smarty->assign("groupedCurrencies",$groupedCurrencies);
        $this->smarty->assign("currentBookmark","MyBillingPlan");
        //$billing = new Billing($this->db);
        //$customerPlan = $billing->getCustomerPlan($customerID);
		$customerPlan = get_object_vars($billing);
		$customerPlan['total'] = $customerPlan['price'] + $customerPlan['one_time_charge']; 						
						
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
           /* $invoice = new Invoice($this->db);
	
            $currentInvoice = $invoice->getCurrentInvoice($customerID);

            if ($currentInvoice['total'] === null) {
                $lastInvoice = $invoice->getLastInvoice($customerID);
                $totalInvoice = $lastInvoice['total'];
                $totalCurrency = $billing->getCurrencyDetails($lastInvoice['currency_id']); 	
            } else {
                $totalInvoice = $currentInvoice['billing_total_price'];
                $totalCurrency = $billing->getCurrencyDetails($currentInvoice['currency_id']);
            }		
		    $discountPercent = $invoice->getDiscount($customerID);							 				
            //$customerPlan['one_time_charge'] = $currentInvoice['oneTimeCharge'] ? $currentInvoice['oneTimeCharge'] : 0;
            //$customerPlan['price'] = $currentInvoice['amount'] ? $currentInvoice['amount'] : 0;
			*/
            $this->smarty->assign("billingPlan",$customerPlan);
            $this->smarty->assign("totalInvoice", number_format($totalInvoice, 2));
            $this->smarty->assign("totalCurrency", $currency);																							

           
            

            if (!empty($discountPercent)) {
                $this->smarty->assign("discountPercent",$discountPercent);	
            }									
        }								
		//$scheduledBillingPlan = $billing->getScheduledPlanByCustomer($customerID);



/* Next billing plan */
if ($customer->next_billing_id != null){
        $dt = new DateTime();
        $dateFormat = VOCApp::getInstance()->getDateFormat();
		
		$futurePlan = get_object_vars($Nextbilling);
		$futurePlan['total'] = $customerPlan['price'] + $customerPlan['one_time_charge'];

		$invoiceManager = new VPSCustomerInvoices($this->db, $customer);
		$invoiceList[] = $invoiceManager->getNextPeriodInvoice();
		
	
		$futurePlan['generation_date'] = $invoiceList[0]->generation_date->format($dateFormat);	
		$futurePlan['suspension_date'] = $invoiceList[0]->suspension_date->format($dateFormat);
		$futurePlan['period_start_date'] = $invoiceList[0]->period_start_date->format($dateFormat);
		$futurePlan['period_end_date'] = $invoiceList[0]->period_end_date->format($dateFormat);

		$futurePlanLabel = "Following scheduled Billing Plan will be applied when first Billing Period starts <b>(".$futurePlan['suspension_date'].")</b>:";


//var_dump($billing,'======',$Nextbilling,'======',$futurePlan);
        
}
/**/		
/*		if ( $scheduledBillingPlan && ($scheduledBillingPlan['billingID'] != $customerPlan['billingID']) ) {
            $currentCurrency = $billing->getCurrencyByCustomer($customerID);									
            $futurePlan = $billing->getBillingPlanDetails($scheduledBillingPlan['billingID'], false, $currentCurrency['id']);
	           
            
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

        } 
*/		

            $this->smarty->assign("futureBillingPlan",$futurePlan);								
            $this->smarty->assign("futurePlanLabel",$futurePlanLabel);
        //modules in billing
        //$currency = $billing->getCurrencyByCustomer($customerID);
        //$modulesView = $billing->getPurchasedPlansForCustomerView($customerID,$currency['id']);





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