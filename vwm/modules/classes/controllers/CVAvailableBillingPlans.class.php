<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVAvailableBillingPlans
 *
 * @author developer
 */
class CVAvailableBillingPlans extends Controller {

    function CVAvailableBillingPlans($smarty,$xnyo,$db,$user,$action) {
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
    
    public function actionViewDetails() {
        
        $billing = new Billing($this->db);
        $customerID = $_SESSION['customerID'];
        
        $currency = $billing->getCurrencyByCustomer($customerID);
        $groupedCurrencies = $billing->getCurrenciesList(true);	
        
        
        $vps2voc = new VPS2VOC($this->db);
        $VPSUser = new VPSUser($this->db);

        $curentCurrency = $billing->getCurrencyByCustomer($customerID);

        $customerLimits = $VPSUser->getCustomerLimits($customerID); 	



        //getting current billing plan 
        $customerPlan = $billing->getCustomerPlan($customerID);



        //defined plan is not processed yet by admin
        if (!$customerPlan) {
            header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
        }

        $this->smarty->assign("billingPlan",$customerPlan);

        //getting available billing plans
        $billingPlanList = $billing->getAvailablePlans($currency['id']);								
        $this->smarty->assign("availablePlans",$billingPlanList);


        //distinct months count and user count
        $months = $billing->getDistinctMonths();
        $sources = $billing->getDistinctSource($currency['id']);

        //remove options with less emission sources than current are 
        //for($i=0;$i<count($sources);$i++) {
        //	if ($sources[$i]['Source count'] < $customerLimits['Source count']['current_value']) {
        //		array_splice($sources,$i,1);								
        //	}
        //}



        $this->smarty->assign("months",$months);
        $this->smarty->assign("monthsCount",count($months));
        $this->smarty->assign("sources",$sources);

        //limits list
        foreach ($customerPlan['limits'] as $limit=>$value) {
            $list[$limit][0] = $value['increase_step'];
            for ($i=1;$i<10;$i++) {
                $list[$limit][$i] = $list[$limit][$i-1] + $value['increase_step'];	
            }																					
        }										
        $this->smarty->assign("list",$list);

        //modules in billing
    //	$smarty->assign("appliedModules",$billing->($customerID));

        $appliedModules = $billing->getPurchasedModule($customerID,null,'today&future');

        $howApplied = array();
        foreach ($appliedModules as $module) {
            $howApplied[$module['module_id']] = $module['id'];
        }
        $modulesPlans = $billing->getModuleBillingPlans(null,$currency['id']);
        $this->smarty->assign("allModules",$modulesPlans);

        
        $moduleBPsheet = array();//grouped by modules and monthes
        foreach ($modulesPlans as $plan) {
            $moduleBPsheet[$plan['module_id']][$plan['type']][$plan['month_count']] = array(
                    'id' => $plan['id'],
                    'price' => $plan['price']
                );
            $moduleBPsheet[$plan['module_id']]['name'] = $plan['module_name'];
            $moduleBPsheet[$plan['module_id']]['applied'] = ((isset($howApplied[$plan['module_id']]))?$howApplied[$plan['module_id']]:false);
        }
        
        
        $this->setBookmarks($category);
						
        
        $subCategory = $this->getFromRequest('subCategory');
        $this->smarty->assign("currentBookmark",$subCategory);

        
        					
        $this->smarty->assign("currentCurrency",$currency);
        $this->smarty->assign("groupedCurrencies",$groupedCurrencies);
                        
                        
        $this->smarty->assign("allModules", $moduleBPsheet);
        $ids_names = $vps2voc->getModules();
        $ids = array();
        foreach($ids_names as $id => $key) {
            $ids []= $id;
        }
        $this->smarty->assign("ids",json_encode($ids));
        
        
        $this->smarty->assign('date',date(VOCApp::getInstance()->getDateFormat()));

        $title = "Available Billing Plans";
        $this->smarty->assign("title", $title);	
        $this->smarty->assign("category","billing");
        
        $dateFormatJS = VOCApp::getInstance()->getDateFormat_JS();
        
        
        $this->smarty->assign("dateFormatJS",$dateFormatJS);
        $this->smarty->display("tpls:vps.tpl");
    }
}

?>
