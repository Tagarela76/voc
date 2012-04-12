<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVMyinfo
 *
 * @author ilya.iz@kttsoft.com
 */
class CVMyInfo extends Controller {

    function CVMyinfo($smarty,$xnyo,$db,$user,$action) {
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
    
    private function actionViewDetails() {
        
        $userID = $_SESSION['userID'];
        
        $userData = $this->user->getUserDetails($userID);
        
		$vps2voc = new VPS2VOC($this->db);
		   //$bridge->CopyAllCustomersToBridge();
		   //$bridge->CopyAllUsersToBridge();		 
		$customerDetails = $vps2voc->getCustomerDetails($userData['company_id'],true);
		
		$this->smarty->assign("companyName",$customerDetails['name']);				
		
		//getting state list ////////need to add to Billing or smth else getStateList() and getCountryList() functions and add to db states and countries  
		$state = new State($this->db);
		$stateList = $state->getStateList();
		$this->smarty->assign("states",$stateList);
		
		//getting country lists
		$country = new Country($this->db);
		$countryList = $country->getCountryList();
		$this->smarty->assign("countries",$countryList);

		if ($userData["showAddUser"]) {
			$this->smarty->assign("action","addUser");	
		} else {
			$this->smarty->assign("action","editCategory");	
		}
		
		$billing = new Billing($this->db);		
		$currenciesList = $billing->getCurrenciesList();
		$this->smarty->assign("currenciesList",$currenciesList);
		if (isset($customerDetails['currency_id'])) {
			$userData['currency_id'] = $customerDetails['currency_id'];
		}		
				
		$title = "My info";
		
        $this->smarty->assign("title",$title);
						
		$this->smarty->assign("userData",$userData);
		$this->smarty->assign("category","myInfo");
		$this->smarty->display("tpls:vps.tpl");	
    }
    
    private function actionEditCategory() {
        
        //$this->getFromPost($key);('firstName', 'text');
        /*$this->xnyo->filter_post_var('lastName', 'text');
        $this->xnyo->filter_post_var('secondaryContact', 'text');
        $this->xnyo->filter_post_var('email', 'text');
        $this->xnyo->filter_post_var('secondaryEmail', 'text');
        $this->xnyo->filter_post_var('companyID', 'int');
        $this->xnyo->filter_post_var('address1', 'text');
        $this->xnyo->filter_post_var('address2', 'text');
        $this->xnyo->filter_post_var('city', 'text');
        $this->xnyo->filter_post_var('state', 'int');
        $this->xnyo->filter_post_var('zip', 'text');
        $this->xnyo->filter_post_var('country', 'int');
        $this->xnyo->filter_post_var('phone', 'text');
        $this->xnyo->filter_post_var('fax', 'text');*/

        $userID = $_SESSION['userID'];

        $userDetails = array (
            'user_id'			=> $userID,							
            'firstName'			=> $this->getFromPost("firstName"),
            'lastName' 			=> $this->getFromPost("lastName"),
            'currency_id'		 => $this->getFromPost("currency_id"),
            'secondary_contact' => $this->getFromPost("secondaryContact"),
            'email'				=> $this->getFromPost("email"),
            'company_id' 		=> $this->getFromPost("companyID"),
            'facility_id' 		=> "NULL",
            'department_id' 	=> "NULL",					
            'address1' 			=> $this->getFromPost("address1"),
            'address2'			=> $this->getFromPost("address2"),
            'city'				=> $this->getFromPost("city"),
            'state_id'			=> $this->getFromPost("state"),
            'zip'				=> $this->getFromPost("zip"),
            'country_id'		=> $this->getFromPost("country"),
            'phone'				=> $this->getFromPost("phone"),
            'fax'				=> $this->getFromPost("fax")										
        );

        $billing = new Billing($this->db);

        $this->db->beginTransaction();

        $curentCurrency = $billing->getCurrencyByCustomer($userDetails['company_id']);
        $newCurrency = $billing->getCurrencyDetails($userDetails['currency_id']);

        if($curentCurrency['iso'] != $newCurrency['iso']) /*Currency changed. Recount ballance in new currency*/
        {
            $invoice = new Invoice($this->db);
            $balance = $invoice->getBalance($userDetails['company_id']);

            $convertor = new CurrencyConvertor();

            $newBallance = $convertor->Sum(array($curentCurrency['iso'] => $balance), $newCurrency['iso']);

            $newBallance = round($newBallance,2);

            $convertStatus = "<br/>Your ballance has been converted from <b>{$curentCurrency['iso']} $balance</b> to <b>{$newCurrency['iso']} = $newBallance</b>";

            $query = "UPDATE " . TB_VPS_CUSTOMER . " SET balance = $newBallance WHERE customer_id = {$userDetails['company_id']}";
            $this->db->query($query);
        }

        $this->user->setUserDetails($userDetails);
        

        $billing->setCurrency($userDetails['company_id'], $userDetails['currency_id']);



        $this->db->commitTransaction();

        $this->smarty->assign("message","Your user information is successfully edited." . $convertStatus);
        header("Location: vps.php?action=viewDetails&category=myInfo");
    }
    
    /**
     * Add user step first
     */
    private function actionFirst() {
        /*$xnyo->filter_get_var('accessname', 'text');
        $xnyo->filter_get_var('password', 'text');
        $xnyo->filter_get_var('accessLevelID', 'int');				
        $xnyo->filter_get_var('firstName', 'text');
        $xnyo->filter_get_var('lastName', 'text');
        $xnyo->filter_get_var('secondaryContact', 'text');
        $xnyo->filter_get_var('email', 'text');
        $xnyo->filter_get_var('secondaryEmail', 'text');
        $xnyo->filter_get_var('companyID', 'int');
        $xnyo->filter_get_var('address1', 'text');
        $xnyo->filter_get_var('address2', 'text');
        $xnyo->filter_get_var('city', 'text');
        $xnyo->filter_get_var('state', 'int');
        $xnyo->filter_get_var('zip', 'text');
        $xnyo->filter_get_var('country', 'int');
        $xnyo->filter_get_var('phone', 'text');
        $xnyo->filter_get_var('fax', 'text');*/

       // $this->db->beginTransaction();
        
        
        $userDetails = array (
            'accessname' 		=> $_POST['accessname'],
            'password' 			=> $_POST['password'],
            'accesslevel_id' 	=> $_POST['accessLevelID'],
            'firstName'			=> $_POST['firstName'],
            'lastName' 			=> $_POST['lastName'],
            'secondary_contact' => $_POST['secondaryContact'],
            'email'				=> $_POST['email'],
            'company_id' 		=> $_POST['companyID'],
            'facility_id' 		=> "NULL",
            'department_id' 	=> "NULL",					
            'address1' 			=> $_POST['address1'],
            'address2'			=> $_POST['address2'],
            'city'				=> $_POST['city'],
            'state_id'			=> $_POST['state'],
            'zip'				=> $_POST['zip'],
            'country_id'		=> $_POST['country'],
            'phone'				=> $_POST['phone'],
            'fax'				=> $_POST['fax'],
            'currency_id'		=> $_POST['currency_id']												
        );

        //check if company already added
        if ($this->user->ifCustomerExist($userDetails['company_id'])) {
            $userDetails['accesslevel_id'] = 1;

            $this->user->addUser($userDetails);

            $this->smarty->assign("status", "userAdded");
            $this->smarty->display("tpls:authorization.tpl");

        } else {
            if (isset($_POST['accessname'])) {
                //	refresh user data
                //echo "refresh user data";
                //var_dump($userDetails);
                $_SESSION['userDetails'] = $userDetails;	
            }

            //new user registration flag
            $newUserRegistration = true;
            $this->smarty->assign("newUserRegistration",$newUserRegistration);									

            $this->showAvailableBillingPlans($_POST['currency_id']);

        }
    }
    
    private function actionSecond() {
        
        
        
		if (empty($_GET['selectedBillingPlan'])) {
            //new user registration flag
            $newUserRegistration = true;
            $this->smarty->assign("newUserRegistration",$newUserRegistration);									

            $this->showAvailableBillingPlans( );

        } else {
            $selectedBillingPlanID = intval($_GET['selectedBillingPlan']);
            

            $billing = new Billing($this->db);
            $customerPlan = $billing->getBillingPlanDetails($selectedBillingPlanID, false, $_SESSION['userDetails']['currency_id']);
            
            //var_dump($_SESSION);
            //var_dump($customerPlan);

            foreach ($customerPlan['limits'] as $limit=>$value) {
                if ($value['default_limit'] == $value['max_value']) {
                    $customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type']." (free)"; 
                } else {
                    $customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type'];
                }		
            }								
            $invoice = new Invoice($this->db);

            $totalInvoiceSimple = $invoice->calculateTotal($customerPlan['one_time_charge'], $customerPlan['price']);
            $totalInvoice = number_format($totalInvoiceSimple,2);


            $this->smarty->assign("billingPlan",$customerPlan);
            $this->smarty->assign("totalInvoice", $totalInvoice);	
            $this->smarty->assign("totalInvoiceSimple",$totalInvoiceSimple);
            $_SESSION['selectedBillingPlan'] = $customerPlan['billingID'];

            //new user registration flag
            $newUserRegistration = true;
            $this->smarty->assign("newUserRegistration",$newUserRegistration);

            //Apply to smarty Selected Modules
            $plans = json_decode($_POST['changeTo']);

            $selectedModules = $this->getSelectedModulesfromGET(intval($_GET['currencyID']));

            $totalPrice = 0;
            foreach($selectedModules as $m) // Count total price
            {
                $totalPrice += $m['price'];
            }
            $this->smarty->assign('totalModulesPrice',$totalPrice);
            $this->smarty->assign('totalModulesPriceFormat',number_format($totalPrice,2));
            $this->smarty->assign('totalInvoiceForAllFormat',number_format($totalPrice + $totalInvoiceSimple,2));

            $_SESSION['selectedModules'] = $selectedModules;
            $this->smarty->assign('appliedModules',$selectedModules);
            $this->smarty->assign('isRegistration',true);

            $currencyDetails = $billing->getCurrencyDetails($_SESSION['userDetails']['currency_id']);							
            $this->smarty->assign("currentCurrency", $currencyDetails);

            $title = "Please confirm";
            $this->smarty->assign("title",$title);

            $this->smarty->assign("currentBookmark","MyBillingPlan");	
            $this->smarty->assign("category","billing");
            $this->smarty->display("tpls:vps.tpl");							
        }
						
    }
    
    private function actionThird() {
        if ($_POST['registrationAction'] == "Save") {
							
        /**
         * REGISTER USER
         */
        $selectedBillingPlanID = $_SESSION['selectedBillingPlan'];
        $selectedModules = $_SESSION['selectedModules'];	

        $currencyID = $_POST['currencyID'];

        $multiInvoiceData = $this->prepareModulesForMultiInvoice($selectedModules,$selectedBillingPlanID,$this->db,$currencyID);

        $userDetails = $_SESSION['userDetails'];														
        if(!$userDetails) {
            throw new Exception('User data lost during registration. Please try to register one more time.');
        }


        //	START TRANSACTION
        $this->db->beginTransaction();

        $userID = $this->user->addUser($userDetails);		

        if(!$userID or $userID == 0) {
            //addUser fail
            throw new Exception('User::addUser(); failed. Please try to register one more time.');
        }	 		

        $billing = new Billing($this->db);																					
        $billing->addCustomerPlan($userDetails['company_id'], $selectedBillingPlanID);							
        $billing->setCurrency($userDetails['company_id'], $userDetails['currency_id']);

        $vps2voc = new VPS2VOC($this->db);
        $customerDetails = $vps2voc->getCustomerDetails($userDetails['company_id']);					

        $invoice = new Invoice($this->db);

        var_dump($customerDetails);
        $dt = new DateTime();
        $dt->setTimestamp( intval($customerDetails['trial_end_date']));
        //$invoice->createInvoiceForBilling($userDetails['company_id'], $customerDetails['trial_end_date'], $selectedBillingPlanID);
        $invoice->createMultiInvoiceForNewCustomer($userDetails['company_id'],
                                                $dt,
                                                $selectedBillingPlanID,$multiInvoiceData); // Create Multy invoice

        //	COMMIT TRANSACTION			
        //exit;
        $this->db->commitTransaction();


        unset($_SESSION['userDetails']);
        unset($_SESSION['registration']);
        unset($_SESSION['selectedBillingPlan']);

        $authResult = $this->user->authorize($userDetails['accessname'], $_SESSION['originalPassword']);
        if ($authResult) {
            unset($_SESSION['originalPassword']);

            //	Redirect user to dashboard
            session_start();	

            $_SESSION['userID']=$this->user->getUserIDbyAccessname($userDetails['accessname']);
            $_SESSION['accessname']=$userDetails['accessname'];					
            $accessLevel=$this->user->getUserAccessLevel($_SESSION['userID']);
            $_SESSION['accessLevel']=$accessLevel;
            $_SESSION['customerID'] = $this->user->getCustomerIDbyUserID($_SESSION['userID']);

            header("Location: vps.php?action=viewDetails&category=dashboard");
            //echo "<br/><a href='vps.php?action=viewDetails&category=dashboard' target='_blank'>redirect</a>";							
        } else {							
            header ('Location: vps.php');
        }
    } elseif ($_POST['registrationAction'] == "Cancel") {
        header ('Location: vps.php');
    }
    }
    
    function prepareModulesForMultiInvoice($modules,$billingID,$db,$currencyID)
	{
		$billing = new Billing($db);
		$billingDetails = $billing->getBillingPlanDetails($billingID,false,$currencyID);

		foreach($modules as $m)// Convert from [0] array to indexed (index is month count) array [month][module_arr]
		{
			$moduleMonthed[$m['month_count']][] = $m;
		}

		$invoiceData['billingID'] = $billingID;
		$invoiceData['appliedModules'] = $moduleMonthed[$billingDetails['months_count']]; //Insert to invoiceData modules, that approach by date period (in month)
		unset($moduleMonthed[$billingDetails['months_count']]); //Delete this module ↑
		
		foreach($moduleMonthed as $mM) // Insert all another modules
		{
			foreach($mM as $m)
			{
				$invoiceData['not_approach_modules'][] = $m;
			}
		}
		
		return $invoiceData;
	}
    
    function showAvailableBillingPlans($currencyID) {
		
        
		$this->smarty->assign("currentBookmark","AvailableBillingPlans");
		$billing = new Billing($this->db);
				
		//getting available billing plans
		$billingPlanList = $billing->getAvailablePlans($currencyID);
		$this->smarty->assign("availablePlans",$billingPlanList);
		
		//distinct months count and user count
		$months = $billing->getDistinctMonths();
		$sources = $billing->getDistinctSource();
		$this->smarty->assign("months",$months);
		$this->smarty->assign("monthsCount",count($months));
		$this->smarty->assign("sources",$sources);
		
	
		
		$title = "Available Billing Plans";
		$this->smarty->assign("title",$title);
		
		//Create data for modules		
		$vps2voc = new VPS2VOC($this->db);
		
		$modules = $billing->getModuleBillingPlans(null, $currencyID);
		
		$this->smarty->assign("allModules",$modules);
		
		$moduleBPsheet = array();//grouped by modules and monthes
		foreach ($modules as $plan) {
			$moduleBPsheet[$plan['module_id']][$plan['type']][$plan['month_count']] = array(
					'id' => $plan['id'],
					'price' => $plan['price']
				);
			$moduleBPsheet[$plan['module_id']]['name'] = $plan['module_name'];
			$moduleBPsheet[$plan['module_id']]['applied'] = ((isset($howApplied[$plan['module_id']]))?$howApplied[$plan['module_id']]:false);
		}
		$this->smarty->assign("allModules", $moduleBPsheet);
		$ids_names = $vps2voc->getModules();
		$ids = array();
		foreach($ids_names as $id => $key) {
			$ids []= $id;
		}
        
		$this->smarty->assign("ids",json_encode($ids));
        $dt = new DateTime();
		$this->smarty->assign('date',$dt->format(VOCApp::get_instance()->getDateFormat()));
        $this->smarty->assign('jsdateformat',VOCApp::get_instance()->getDateFormat_JS());
		$this->smarty->assign('newUserRegistration',true);
		/////////////////////////
		
		$currencyDetails = false;
		if (isset($_SESSION['userDetails'])) {
			$currencyDetails = $billing->getCurrencyDetails($_SESSION['userDetails']['currency_id']);							
		}else {
            $currencyDetails = $currencyID ;
        }
		$this->smarty->assign("currentCurrency",$currencyDetails);
				
		$this->smarty->assign("category","billing");					
		$this->smarty->display("tpls:vps.tpl");
			
	}
    
    function getSelectedModulesFromGET($currencyID)
	{
		$billing = new Billing($this->db);
		$modulesBillingPlans = $billing->getModuleBillingPlans(null,$currencyID);
		
		$modules = $billing->getModules();
		
		if(!isset($_GET['selectedBillingPlan']) or !Is_Numeric($_GET['selectedBillingPlan']))//check selectedBillingPlan for validity
		{
			return false;
		}
		
		$billingDetails = $billing->getBillingPlanDetails($_GET['selectedBillingPlan'],false,$currencyID);
		
		$selectedModules = array();
		
		foreach($modules as $m)//Looking for selected modules in GET params
		{
			$tmpVarName = 'selectedModulePlan_'.$m['id'];
			if(isset($_GET[$tmpVarName]))
			{
				$selectedModules[] = $_GET[$tmpVarName];
			}
		}
		
		$modulesBillingPlansIndexed = array();
		foreach($modulesBillingPlans as $m) //Convert array from [0] => ... to [id] => ...
		{
			$modulesBillingPlansIndexed[$m['id']] = $m;
		}
		
		

		$len = count($selectedModules);
		if($len == 0)
		{
			$selectedModules = array();
		}
		for($i=0; $i < $len; $i++)//Check billing type == module type (self == self, or gyant == gyant)
		{
			$m = $selectedModules[$i];
			if($modulesBillingPlansIndexed[$m]['type'] != $billingDetails['type']) // WRONG module (billing type != module type) has been founded!! Removing it." (Hackerzzz does not sleep! =) But who's carres..) 
			{
				unset($selectedModules[$i]);
			}
		}
		
		$moduleDetails = $billing->getModuleBillingPlans($selectedModules,$currencyID);
		
		
		return $moduleDetails;
	}
}

?>
