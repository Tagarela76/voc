<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVInvoices
 *
 * @author ilya.iz@kttsoft.com
 */
class CVModules extends Controller {

    function CVModules($smarty,$xnyo,$db,$user,$action) {
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
        
        $customerID = $_SESSION['customerID'];
        $billing = new Billing($this->db);
								
        $subCategory = $_GET['subCategory'];
        
        $currentCurrency = $billing->getCurrencyByCustomer($customerID);
        $this->smarty->assign("currentCurrency",$currentCurrency);
        $dateFormat = VOCApp::get_instance()->getDateFormat();

        if (isset($_GET['total']) && ($_GET['total'] == 'delete')) {
            //Delete Module
            $moduleID = $_GET['module'];
            $status = $_GET['status'];



            if ($status == 'delete_all') {
                $plans = $billing->getPurchasedModule($customerID,$moduleID,'today&future','today',$currentCurrency['id']);
                $this->smarty->assign("plans",$plans);
                $this->smarty->assign("moduleID",$moduleID);
            } elseif ($status == 'delete_plan') {
                $planID = $_GET['plan'];
                $plans = $billing->getPurchasedModule($customerID,$moduleID,'today&future','today',$currentCurrency['id']);
                foreach ($plans as $module_plan) {
                    if ($module_plan['id'] == $planID) {
                        $plan = $module_plan;
                    }
                }
                $this->smarty->assign("plan",$plan); //TODO: it can be more than 1 plans
            }
            $this->smarty->assign("status",$status);
            $this->smarty->assign("areYouSureAction","remove module plan");
        } else {
            //edit of MBP
            $vps2voc = new VPS2VOC($this->db);
            $modules = $vps2voc->getModules();
            $no_changes = true;
            $plans = array();
            $oldModulePlans = array();
            $ids = array();
            
            $plan_start  = DateTime::createFromFormat($dateFormat, html_entity_decode($_GET['startDate']));
            $now = new DateTime("now"); 
            if($now->diff($plan_start)->invert){
                $plan_start = new DateTime("now");
            }
            
            
            /*$plan_start = date('Y-m-d',strtotime(html_entity_decode($_GET['startDate'])));
                    if ($plan_start < date('Y-m-d')) {
                        $plan_start = date('Y-m-d');
                    }*/
                    
            foreach($modules as $key => $value) {
                if (!is_null($_GET['selectedModulePlan_'.$key])) {
                    $plan = $billing->getModuleBillingPlans($_GET['selectedModulePlan_'.$key],$currentCurrency['id']);
                    $plan = $plan[0];
                    $plan['start'] = $plan_start;
                    
                    //$plan['end'] = date('Y-m-d', strtotime($plan['start'].'+'.$plan['month_count'].' month - 1 day'));
                    $end = clone $plan['start'];
                    $plus_month_count = (int) $plan['month_count'];
                    
                    $end->add(new DateInterval("P{$plus_month_count}M"));
                    $end->sub(new DateInterval("P1D"));
                    
                    //var_dump($plan['start']);
                    //var_dump($end);
                    //exit;
                    $plan['end'] = $end;
                    
                    
                    

                    $oldPlans = $billing->getPurchasedModule($customerID,$plan['module_id'],'today&future',array($plan['start'],$plan['end']),$currentCurrency['id']);

                   
                    
                    if ($oldPlans) {
                        foreach($oldPlans as $oldPlan) {
                            if ($plan['id'] != $oldPlan['id']) {
                                $no_changes = false;
                                if (!in_array($plan['id'],$ids)) {
                                    $ids []= $plan['id'];
                                    $plans [$key]= $plan;
                                }
                                $oldModulePlans [$key] []= $oldPlan;
                            }
                        }
                    } else {
                        $no_changes = false;
                        if (!in_array($plan['id'],$ids)) {
                            $ids []= $plan['id'];
                            $plans [$key]= $plan;
                        }
                    }
                }
            }
            
            if ($no_changes) {
                header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");
                break;
            }
            
            //var_dump($oldModulePlans);
            
            //foreach($oldModulePlans as $key => $val) {
            //    var_dump($oldModulePlans[$key]);
            //}
            
            foreach($plans as $key => $val) {
                $plans[$key]['start']  = $plans[$key]['start']->format($dateFormat);
                $plans[$key]['end']  = $plans[$key]['end']->format($dateFormat);
            }

            $this->smarty->assign("plans",$plans);
            $this->smarty->assign("oldPlans",$oldModulePlans);
            $this->smarty->assign("plan_ids",json_encode($ids));
            $this->smarty->assign("start",$plan_start->format(VOCApp::get_instance()->getDateFormat()));
            $this->smarty->assign("areYouSureAction","apply module plan");


            
        }
        
        $this->smarty->assign("category","billing");
        $this->smarty->assign("subCategory",$subCategory);
        $this->smarty->assign("currentBookmark",$_GET["action"]);
        $this->smarty->display("tpls:vps.tpl");
    }
    
    public function actionConfirmEdit() {
        $customerID = $_SESSION['customerID'];
        $subCategory = $_GET['subCategory'];
        
        //$this->db->beginTransaction();
        
        $this->smarty->assign("currentCurrency",$currentCurrency);
        
        $billing = new Billing($this->db);
        if (isset($_POST['total']) && $_POST['total'] == 'delete') {
            if ($_POST['status'] == 'delete_all') {
                $plans = $billing->getPurchasedModule($customerID,$_POST['module_id'],'today&future');
                //echo "to delete:";
                //var_dump($plans);
                
                foreach ($plans as $plan) {
                    $billing->removeModuleBillingPlan($customerID,$plan['id']);
                }
            } elseif ($_POST['status'] == 'delete_plan') {
                $billing->removeModuleBillingPlan($customerID,$_POST['plan_id']);
            }
        } else {
            $plans = json_decode($_POST['changeTo']);
            $start_date = $_POST['startDate'];
            $start_date_dt = DateTime::createFromFormat(VOCApp::get_instance()->getDateFormat(), $start_date);
            //var_dump($start_date_dt);
            //exit;
            
            
            $billing->applyModuleBillingPlan($customerID,$plans,$start_date_dt); //plans is array of module plans id!
        }
       // echo "<a href='vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan' target='_blank'>next</a>";
        header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
    }
}
?>
