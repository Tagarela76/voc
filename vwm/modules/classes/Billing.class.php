<?php

class Billing {
	public $currentPlan;
	
	private $db;	
	public $currentDate;

    function Billing($db) {
    	$this->db = $db;
    	$this->currentDate = date("Y-m-d");
    }
            
    
    //	get customer's billing plan by customer's ID
    //	output: billing plan details ARRAY or FALSE if billing plan or customer does not exist    
    public function getCustomerPlan($customerID) {
    
    	$query = "SELECT billing_id 
    				FROM ".TB_VPS_CUSTOMER." 
    				WHERE customer_id = $customerID AND billing_id IS NOT NULL";
    	
		$this->db->query($query);
		      
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);			
			$currencyDetails = $this->getCurrencyByCustomer($customerID);
			//	return billing plan details				 				
			return $this->getBillingPlanDetails($data->billing_id, $customerID, $currencyDetails['id']);									
		} else {
			//	no such customer or billing not set			
			return false;
		}		
    }
    
    
    /**      
     * Inserts billing plan to VPS_CUSTOMER table and insert customer's limits to VPS_CUSTOMER_LIMIT and Bridge if billing plan defined
     * @param int $customerID
     * @param int $billingID
     */    
    public function addCustomerPlan($customerID, $billingID = "NULL") {
    	
    	//	insert to VPS_CUSTOMER
    	$query = "INSERT INTO ".TB_VPS_CUSTOMER." (customer_id, billing_id) VALUES (".$customerID.", ".$billingID.")";    	    			     			
    	$this->db->query($query);
    	
//    	$vps2voc = new VPS2VOC($this->db);    		
//   		//	change customer's status at Bridge to "on" 
//   		$vps2voc->changeCustomerStatus($customerID, "on");    		
    	    	    	
    	if ($billingID != "NULL") {
    		
    		$this->addCustomerLimit($customerID, $billingID);  

//	   		//	save customer's limits to Bridge
//	   		$vps2voc->setCustomerLimits($customerID, $billingPlanDetails['limits']);
    	}    	
    }
    
    /**
     * insert customer's limits info 
     * @param $customerID
     * @param $billingPlanDetails
     * @return unknown_type
     */
    public function addCustomerLimit($customerID, $billingID)
    {
    	$billingPlanDetails = $this->getBillingPlanDetails($billingID);
    	
    	$query = "INSERT INTO ".TB_VPS_CUSTOMER_LIMIT." (customer_id, limit_price_id, current_value, max_value) VALUES (" .
    				"".$customerID.", " .
    				"".$billingPlanDetails['limits']['MSDS']['limit_price_id'].", " .
    				"0, " .
    				"".$billingPlanDetails['limits']['MSDS']['default_limit'].") , (" .
    				"".$customerID.", " .
    				"".$billingPlanDetails['limits']['memory']['limit_price_id'].", " .
    				"0, " .
    				"".$billingPlanDetails['limits']['memory']['default_limit'].")";    			
    		$this->db->query($query); 
    }
    
    
    /**
     * updates customer's billing plan at VPS_CUSTOMER
     * @param int $customerID
     * @param int $billingID
     */	
    public function setCustomerPlan($customerID, $billingID) {
	    	
    	$scheduledBillingPlan = $this->getScheduledPlanByCustomer($customerID);
    
    	//	we should recalculate limit values according to new Billing Plan
    	//	and limit amount ($customerPaidForValue) that customer already spend for limit increasing 
    	if ($scheduledBillingPlan['billingID'] == $billingID) {
			
			//	get current limit values
    		$currentLimits = $this->getLimits($customerID);
    		//	now we can delete them
    		$this->db->query("DELETE FROM ".TB_VPS_CUSTOMER_LIMIT." WHERE customer_id = ".$customerID);
    		
    		foreach ($scheduledBillingPlan['limits'] as $limitPriceID) {
    			//	calculate new value    			 
    			$limitPriceDetails = $this->getLimitPriceDetails($limitPriceID);
    			if ($limitPriceDetails['limit_id'] == 1) {	//	MSDS
    				$customerPaidForValue = $currentLimits['MSDS']['max_value'] - $currentLimits['MSDS']['default_limit'];	
    			} elseif ($limitPriceDetails['limit_id'] == 2) {	//	memory
    				$customerPaidForValue = $currentLimits['memory']['max_value'] - $currentLimits['memory']['default_limit'];
    			}    		
    			$newValue = $limitPriceDetails['default_limit'] + $customerPaidForValue;
    			
    			//	save customer's limits    			
    			$this->setCustomerLimit($customerID, $limitPriceID, $newValue, true);	
    		}    		
    	}
    	
    	//	update VPS_CUSTOMER    	
    	$query = "UPDATE ".TB_VPS_CUSTOMER." " .
    			 "SET billing_id = ".$billingID." " .
    			 "WHERE customer_id = ".$customerID;    			
    	$this->db->query($query);
    }
    
    
    /**
     * 
     * @param $billingID
     * @param $customerID default <b>false</b>
     * @param $currencyID currency id - <b>1 (USD) is default</b>
     * @return unknown_type
     */
    public function getBillingPlanDetails($billingID, $customerID = false, $currencyID = 1) {    	
		//$this->db->select_db(DB_NAME);
		$query = "SELECT bil.billing_id as 'billingID', bil.name, bil.description, b2c.one_time_charge, bil.bplimit, bil.months_count, b2c.price, bil.type, bil.defined 
					FROM ".TB_VPS_BILLING." bil, " . TB_VPS_BILLING2CURRENCY . " b2c 
					WHERE 	bil.billing_id = $billingID
					AND 	b2c.billing_id = bil.billing_id
					AND		b2c.currency_id = $currencyID";
		
		
		$this->db->query($query);

		$billingPlan = $this->db->fetch_array(0);
		
		//get limits details
		if ($customerID) {			
			$billingPlan['customerID'] = $customerID;			
			$billingPlan['limits'] = $this->getLimits($billingPlan['customerID'],$currencyID);
		//default limit	
		} else {
			$billingPlan['limits'] = $this->getDefaultLimit($billingID,$currencyID);// TODO:проверить бы		
		}   
		
		return $billingPlan;
    }
    

    /**
     * 
     * @param $customerID
     * @return array of $scheduledPlan or false if there is no scheduled plan for $customerID  
     */
	public function getScheduledPlanByCustomer($customerID) {
    																											
    	//$this->db->select_db(DB_NAME);
    	$query = "SELECT scp.id, scp.billing_id, scp.type, sl.limit_price_id " .
    			"FROM ".TB_VPS_SCHEDULE_CUSTOMER_PLAN." scp, ".TB_VPS_SCHEDULE_LIMIT." sl " .
    			"WHERE scp.customer_id = sl.customer_id " .
    			"AND scp.customer_id = ".$customerID;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$scheduledPlan = array (
					'id'		=> $data->id,
					'billingID'	=> $data->billing_id,
					'type'		=> $data->type
				);
				//	get scheduled limits				
				$limits[] = $data->limit_price_id;
			}
			$scheduledPlan['limits'] = $limits;						
			return $scheduledPlan;
		} else {			
			return false;
		}
	}
    
    public function setScheduledPlan($customerID, $billingID, $type, $limitPrice = false) {
    	//$this->db->select_db(DB_NAME);    	    	
    	//echo "setScheduledPlan function<br/>";
    	$scheduledBillingPlan = $this->getScheduledPlanByCustomer($customerID);	
    	//echo "scheduledBillingPlan $scheduledBillingPlan<br/>";
    	
		if (!$scheduledBillingPlan) {
			//	no scheduled billing plans, so add new plan to schedule
			//echo "no scheduled billing plans, so add new plan to schedule<br/>";
			$query = "INSERT INTO ".TB_VPS_SCHEDULE_CUSTOMER_PLAN." ( customer_id, billing_id, type) VALUES ( " .
					 "".$customerID.", " .
					 "".$billingID.", " .
					 "'".$type."' )";
			$action = "add";	
																														
		} else {
			//	update scheduled billing plan to new
			//echo "update scheduled billing plan to new<br/>";
			$query = "UPDATE ".TB_VPS_SCHEDULE_CUSTOMER_PLAN." " .
					 "SET billing_id = ".$billingID."," .
					 	"type = '".$type."' " .
					 "WHERE customer_id = ".$customerID;									 									
			$action = "update";	 					 				
		}
		
		//echo "query: $query<br/>";
		$this->db->query($query);
		
		//set scheduled limits
		$this->db->query("DELETE FROM ".TB_VPS_SCHEDULE_LIMIT." WHERE customer_id = ".$customerID);		
		if ($limitPrice) {						
			$this->getLimits($billingID);
			$query = "INSERT INTO ".TB_VPS_SCHEDULE_LIMIT." (customer_id, limit_price_id, type) VALUES " .									
				"(".$customerID.", " .
				"".$limitPrice[0].", " .
				"'".$type."'), " .
				"(".$customerID.", " .
				"".$limitPrice[1].", " .
				"'".$type."')"; 			
		} else {
			$limits = $this->getDefaultLimit($billingID);
			$query = "INSERT INTO ".TB_VPS_SCHEDULE_LIMIT." (customer_id, limit_price_id, type) VALUES " .									
				"(".$customerID.", " .
				"".$limits['MSDS']['limit_price_id'].", " .
				"'".$type."'), " .
				"(".$customerID.", " .
				"".$limits['memory']['limit_price_id'].", " .
				"'".$type."')";
			
		}	
		//echo "query $query<br/>";					 
		$this->db->query($query);		
				
		//create Invoice
		//echo "create Invoice<br/>";
		$invoice = new Invoice($this->db);
		$invoice->currentDate = $this->currentDate;
		
		if ($type == "bpEnd") {
			$currentInvoice = $invoice->getCurrentInvoice($customerID);					
			if ($currentInvoice) {
				$periodStartDate = $currentInvoice['periodEndDate'];
			} else {
				//	*Try to take trial period end. Try do not use first invoice case 
				$firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
				if ($firstInvoice) {
					$periodStartDate = $firstInvoice['periodStartDate'];
				} else {
					//	Trial period end
					$vps2voc = new VPS2VOC($this->db);
					$customerDetails = $vps2voc->getCustomerDetails($customerID);
					
					$periodStartDate = $customerDetails['trial_end_date'];
				}	
				//$firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
				//$periodStartDate = ($firstInvoice) ? $firstInvoice['periodStartDate'] : null;	
			}									
			$asap = false;								
		} else {
			$periodStartDate = $this->currentDate;			
			$asap = true;
		}
		echo "createInvoiceForBilling<br/>";
		echo " ($customerID, $periodStartDate, $billingID, $asap)";
		$invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);
		//echo "createdInvocieForBilling<br/>";
		return $action;
				
    }
    
    /**
     * 
     * @param $currencyID = 1
     * @return unknown_type
     */
    public function getAvailablePlans($currencyID = 1) { //Default 1 == USD

		$query = "SELECT bil.billing_id as billingID, b2c.one_time_charge AS one_time_charge, bil.bplimit, bil.months_count, b2c.price AS price, bil.type, b2c.currency_id, b2c.id as 'b2c_id'
					FROM ".TB_VPS_BILLING." bil, ".TB_VPS_BILLING2CURRENCY." b2c
					WHERE bil.defined = 0
					AND b2c.billing_id = bil.billing_id
					AND b2c.currency_id = $currencyID
					GROUP BY bil.bplimit, bil.months_count, bil.type
					ORDER BY bil.type DESC , bil.bplimit, bil.months_count
		";
		
		$this->db->query($query);

		$billingPlans = $this->db->fetch_all_array();
		return $billingPlans;
    }
    
    /**
     * 
     * @param $currencyID (vps_currency.id)
     * @param $bpLimit (vps_billing.bplimit)
     * @return billing an billing2customer row data
     */
    public function getBilling2Currency($currencyID,$bpLimit)
    {
    	$query = "SELECT b2c.*, bil.*
    				FROM vps_billing2currency b2c, vps_billing bil
    				WHERE	b2c.billing_id = bil.billing_id
    				AND		bil.bplimit = $bpLimit
    				AND		b2c.currency_id = $currencyID
    				AND		bil.defined = 0";
    	$this->db->query($query);

		return $this->db->fetch_all_array();
    }
    
    /**
     * 
     * @param $currencyID
     * @param $bpLimit
     * @param $newPrice
     * @return unknown_type
     */
    public function setOneTimeChargeToBilling2Currency($currencyID,$bpLimit,$newPrice)
    {
    	$query = "UPDATE vps_billing2currency b2c, vps_billing bil
    				SET b2c.one_time_charge = $newPrice
    				WHERE	b2c.billing_id = bil.billing_id
    				AND		bil.bplimit = $bpLimit
    				AND		b2c.currency_id = $currencyID
    				AND		bil.defined = 0";
    	$this->db->query($query);
    }
    
    public function getModules()
    {
    	$query = "SELECT * FROM ".TB_MODULE." ";
    	$this->db->query($query);
    	return $this->db->fetch_all_array();		
    }
    
    /**
     * To get list call this method without parameters
     * To get info about 1 method billing plan just use $moduleBillingPlanID (int param)
     * To get list of specific items, get id's array param $moduleBillingPlanID
     * @param [int $moduleBillingPlanID] - module billing plan ID 
     * @param $currencyID int, <b>Default: 1 (USD)</b>
     * @return array 'id', 'price', 'module_id', 'type', 'month_count' or false if nothing found    
     */
    public function getModuleBillingPlans($moduleBillingPlanID = null,$currencyID = 1) { // TODO: переделать под мультикаренс

    
    	$query = "SELECT m.id, m.month_count, m.module_id, m.type, m2c.price FROM ".TB_VPS_MODULE_BILLING." m, " . TB_VPS_MODULE2CURRENCY . " m2c 
    					WHERE 
    						m2c.module_billing_id = m.id AND m2c.currency_id = $currencyID ";
    	/*
    	 * Check, if moduleBillingPlanID is null - no where in query. if it is numeric - than add where id = num. if it's array - than add id = .. or id = .. etc.
    	 * */	
    	if (!is_null($moduleBillingPlanID) and !is_array($moduleBillingPlanID) and is_numeric($moduleBillingPlanID)) {    	//Hard check for number :)
    		$safeModuleBillingPlanID = mysql_escape_string($moduleBillingPlanID);

    		$query .= " AND	m.id = $safeModuleBillingPlanID";
    	} 
    	else if(is_array($moduleBillingPlanID))
    	{
    		$len = count($moduleBillingPlanID);
    		
    		if($len == 0)
    		{
    			return false;
    		}
			for($i = 0; $i < $len; $i++) // Hard check for array of numbers. No frivolity and freedom in our VOC project! Give strict code! =)
			{
				$id = $moduleBillingPlanID[$i];
				if(is_numeric($id)) 
    			{
    				if($i == 0)
    				{
    					$query .= " AND m.id IN ( $id";
    				}
    				
    				
    				
    				if($i < $len)
					{
						$query .= " ,$id ";
					}
					
					if($i == $len-1)
					{
						$query .= ") ";
					}
    			}
			}
    	} 	
    	else 
    	{
    		/* Nothing to do, just for view*/
    	}
    	$this->db->query($query);
    	$modulesCnt = $this->db->num_rows(); 
    	if ($modulesCnt > 0) {
    		$modules = $this->db->fetch_all_array();    		
    		$vps2voc = new VPS2VOC($this->db);
    		$allAvailableModules = $vps2voc->getModules();
    		for ($i=0;$i<$modulesCnt;$i++) {    			
    			$modules[$i]['module_name'] = $allAvailableModules[$modules[$i]['module_id']];    			
    		}
    		return $modules;    		
    	} else {
    		return false;
    	}
    }
    
    //it's no need - wrong(( right func is getModulePlanByParams
    public function getBillingPlanIDWithModuleName($name)
    {    	
    	$query="SELECT mb.id AS id FROM ".TB_VPS_MODULE_BILLING." mb ,".TB_MODULE." m WHERE m.name='$name' AND mb.module_id=m.id";    	
    	$this->db->query($query);    
    		
    	return $this->db->fetch_array(0)->id;
    }
    
    public function getModulePlanByParams($name,$type,$period) {
    	$vps2voc = new VPS2VOC($this->db);
    	$allAvailableModules = $vps2voc->getModules();
    	$module_id = null;
    	foreach ($allAvailableModules as $id => $module_name) {
    		if ($module_name == $name) {
    			$module_id = $id;
    			break;
    		}
    	}
    	if (is_null($module_id)) {
    		return null;
    	}
    	$query = "SELECT id FROM `".TB_VPS_MODULE_BILLING."` WHERE `month_count` ='$period' AND `type` = '$type' AND `module_id` = '$module_id' LIMIT 1";
    	$this->db->query($query);
    	return $this->db->fetch(0)->id;
    }
    
    /**
     * 
     * @param $moduleBillingPlanID
     * @param $price
     * @param $currencyID <b>Default: 1 (USD)</b>
     * @return unknown_type
     */
    public function updatePriceForModule($moduleBillingPlanID,$price,$currencyID = 1) 
    {    	
    	//$query = "UPDATE ".TB_VPS_MODULE_BILLING." SET price='$price' WHERE id=$moduleBillingPlanID";  

    	$query = "UPDATE " . TB_VPS_MODULE2CURRENCY . " SET price = $price 
    				WHERE module_billing_id = $moduleBillingPlanID
    				AND currency_id = $currencyID";
    	
    	$this->db->exec($query);    	
    }
    
    /**
     * Updates increase_coast
     * @param $limitPriceID
     * @param $currencyID
     * @param $increaseCost
     * @return nothing
     */
    public function setPriceToLimit($limitPriceID,$currencyID,$increaseCost)
    {
    	$query = "UPDATE ".TB_VPS_LIMIT_PRICE2CURRENCY." p2c
					SET		p2c.increase_cost = $increaseCost 
					WHERE 	p2c.vps_limit_price_id = $limitPriceID
					AND		p2c.currency_id = $currencyID";
    	$this->db->exec($query);
    	echo $query;
    }
    
	/**
	 * 
	 * Apply module billing plan to customer
	 * @param int $customerID
	 * @param int $moduleBillingPlanIDs - array of billing plan ids for module
	 * @param string $startDate Y-m-d
	 */
    public function applyModuleBillingPlan($customerID, $moduleBillingPlanIDs, $startDate = null) {
    	//	default start date is today
    	if (is_null($startDate)) {
    		$startDate = date('Y-m-d');
    	}    	    	
    	
    	//	if exist already exist? - ?? if we want to add it for future it's a fail - we can't!
    	$saveModuleBillingPlanIDs = array();
    	
    	//	sometimes $moduleBillingPlanIDs is array, sometimes as int. 
    	//	here we always convert $moduleBillingPlanIDs to array
    	$moduleBillingPlanIDs = (is_array($moduleBillingPlanIDs) ? $moduleBillingPlanIDs : array($moduleBillingPlanIDs));
    	
    	foreach($moduleBillingPlanIDs as $moduleBillingPlanID) { 
    		
	    	if (!$this->_isModule2CustomerLink($customerID, $moduleBillingPlanID)) {
	    		//it's did not exist yet
	    		$saveModuleBillingPlanIDs []= $moduleBillingPlanID;
	    	}
    	}
    	if(empty($saveModuleBillingPlanIDs)) {
    		return false;//no need in apply
    	}

    	//now remove old conflicted modules plans and group all ids by month_count
    	$moduleBPIDsByMonthCount = array();
		foreach ($saveModuleBillingPlanIDs as $moduleBillingPlanID) {
	    	$moduleBillingDetails = $this->getModuleBillingPlans($moduleBillingPlanID);
	    	$moduleBPIDsByMonthCount [$moduleBillingDetails[0]['month_count']][]=$moduleBillingPlanID;
	    	
	    	$purchasedModule = $this->getPurchasedModule($customerID, $moduleBillingDetails[0]['module_id'], 'todayOnly', array($startDate,date('Y-m-d',strtotime($startDate.' + '.$moduleBillingDetails[0]['month_count'].' months - 1 day'))));
	    	if ($purchasedModule) {    		
	    		foreach ($purchasedModule as $module) {
	    			$this->removeModuleBillingPlan($customerID, $module['id']);//if we get here customer accepted to delete all conflicted modules: we can delete them all!
	    		}
	    	}
		}
    	    	   	   	    	
    	//	and now create invoice
    	foreach($moduleBPIDsByMonthCount as $moduleBillingPlanIDs) {
	    	$invoice = new Invoice($this->db);    	
	    	if ($invoice->createInvoiceForModule($customerID, $startDate, $moduleBillingPlanIDs)) {
	    		//	and now insert links module 2 customer
	    		$this->insertModuleBillingPlan($customerID, $startDate, $moduleBillingPlanIDs);
	    	} 
    	}
    }
    
    public function insertModuleBillingPlan($customerID, $startDate, $arrayBillingPlanIDs) {
    	$query = "INSERT INTO ".TB_VPS_MODULE2CUSTOMER." (customer_id, module_billing_id, start_date) VALUES ";
    	foreach ($arrayBillingPlanIDs as $moduleID) {
    		$query .= "( ".$customerID.", ".$moduleID.", '".$startDate."'), ";  
    	}
    	$query = substr($query,0,-2);
    	
    	$this->db->exec($query);
    }
    /**
     * 
     * @param $modulesIDs
     * @param $currencyID default <b>1</b>
     * @return unknown_type
     */
    public function getModulesBillingByIDs($modulesIDs,$currencyID = 1)
    {
    	$count = count($modulesIDs);
    	 if($count == 0)
    	 {
    	 	return;
    	 }
    	 
    	 $q = "SELECT mb.id, mb.month_count, mb.module_id, mb.type, m.name, m2c.price
			FROM vps_module_billing mb, module m, vps_module2currency m2c
			WHERE m.id = mb.module_id
			AND m2c.module_billing_id = mb.id
			AND m2c.currency_id = $currencyID ";
    	 
     	$q .= " AND mb.id IN( ";
     	foreach($modulesIDs as $id)
    	{
    		 $q .= $id . ",";
    	}
    	
    	$q = substr_replace($q,"",strlen($q)-1,1); // Delete last symbol ','
    	$q .= ")";
    	
    	$this->db->query($q);
    	p("query",$q);
    	$modules = $this->db->fetch_all_array();
    	
    	return $modules;
    }
    
    private function deleteModule2Customer($customerID,$moduleBillingPlanID)
    {
    	$query = "DELETE FROM ".TB_VPS_MODULE2CUSTOMER." WHERE customer_id = ".$customerID." AND module_billing_id = ".$moduleBillingPlanID;  	
		$this->db->exec($query);
		$this->db->query("SELECT ROW_COUNT() as 'count'");
		$ar = $this->db->fetch_all_array();
		$deleteRowsCount = $ar[0];
		return $deleteRowsCount;
    }
    
    
    /**     
     * Method removes Module Billing plan for company
     * @param int $customerID
     * @param int $moduleBillingPlanID
     */
    public function removeModuleBillingPlan($customerID,$moduleBillingPlanID) {    	
    	$safeCustomerID = mysql_escape_string($customerID);
    	$safeModuleBillingPlanID = mysql_escape_string($moduleBillingPlanID);  
    	$test = array("a","b","c");  	    	 
    	    	
    	//	if exist already exist?
    	if (!$this->_isModule2CustomerLink($customerID, $moduleBillingPlanID)) {
    		//	nothing to delete
    		return false;
    	}
    	
    	//проверить, является ли модуль частью мульти-инвойса
    	//если да - создать новый мульти инвойс, без удаляемого модуля
    	//если нет - просто отменить инвойс
    	
    	//$this->db->beginTransaction();
    	
    	$invoice = new Invoice($this->db);
    	$result = $invoice->isModuleInMultiinvoice($customerID,$safeModuleBillingPlanID);
    	
    	
    	$invoiceDetails = $invoice->getInvoiceDetailsByBillingModuleID($customerID,$safeModuleBillingPlanID);
    	
    	$currency = $this->getCurrencyByCustomer($customerID);
    	
    	//var_dump($moduleBillingPlanID);
    	//p("invoiceDetails");
    	//var_dump($invoiceDetails);
    	//var_dump($invoiceDetails);
    	
    	//p("is multi?");
    	//var_dump($result);
    
    	//exit;
    	
    	
    	if($result) /**MULTI INVOICE*/
    	{
	    		 $billingID = $invoiceDetails['customerDetails']['billing_id'];
	    		 $trialEndDate = $invoiceDetails['customerDetails']['trial_end_date'];
	    		 $customerID = $invoiceDetails['customerDetails']['company_id'];
	    		 $startDate = $invoiceDetails['periodStartDate'];
	    		 
    			 /** Get back money to customer if status == PAID*/	
	    		 //$mID = $invoice->getModuleIDByBillingModuleID($moduleBillingPlanID);
	    		 //$deletingModuleInfo = $this->getModulesBillingByIDs(array($mID));
	    		 //$deletingModuleInfo = $deletingModuleInfo[0];
	    		 
	    		 /*$query = "SELECT * from " . TB_VPS_MODULE_BILLING . " where id = $moduleBillingPlanID";
	    		 $this->db->query($query);
	    		 $deletingModuleInfo = $this->db->fetch_array(0);*/
	    		 
	    		 $di = $this->getModuleBillingPlans($moduleBillingPlanID,$currency['id']);
	    		 $deletingModuleInfo = $di[0];
	    		 
	    		 
	    		 //var_dump($deletingModuleInfo);
		    		  
	    		 if(strtoupper($invoiceDetails['status'])  == "PAID")
	    		 {
	    		 	 $data['total'] = $deletingModuleInfo['price'];
		    		 $data['daysCountAtBP'] = $invoiceDetails['daysCountAtBP'];
		    		 $data['daysLeft2BPEnd'] = $invoiceDetails['daysLeft2BPEnd'];
		    		 $data['periodStartDate'] = $invoiceDetails['periodStartDate'];
		    		 
		    		 $periodStartDate = strtotime($invoiceDetails['periodStartDate']);
		    		 $curDate = mktime();
		    		 
		    		 if($periodStartDate < $curDate) //If module is not active, back to customer full price, else - refund money
		    		 {
		    		 	$backToCustomer = $invoice->partialRefund($data);
		    		 }
		    		 else
		    		 {
		    		 	$backToCustomer = $data['total'];
		    		 }
		    		 
		    		 /*Commented cause of double back money to customer (createCustomInvoice)*/
		    		 //$invoice->manualBalanceChange($customerID,'+',$backToCustomer); 
	    		 }
	    		 
	    		 
	    		 
	    		 /*Create Custom invoice for module */
	    		 if(!$backToCustomer)
	    		 {
	    		 	$backToCustomer = 0.0;
	    		 }
	    		 $customInfo = "Canceled module <b>{$deletingModuleInfo['name']}</b>,
	    		 month - <b>{$deletingModuleInfo['month_count']}</b>, 
	    		 type - <b>{$deletingModuleInfo['type']}</b>,
	    		 back to customer - <b>$ $backToCustomer</b>
	    		 <a href='/voc_src/vwm/vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceDetails['invoiceID']}'>Original Invoice</a>";
	    		 
	    		 $customInfo = mysql_escape_string($customInfo);
	    		 //echo "<h2>Back to customer: $backToCustomer</h2>";
	    		 //echo("<br/>Balance: " . $invoice->getBalance($customerID) . "<br/>");
	    		 $invoice->createCustomInvoice($customerID, 0, $invoiceDetails['suspensionDate'], 0, $customInfo,'CANCELED');
	    		 //echo("<br/>Balance: " . $invoice->getBalance($customerID) . " <b>createCustomInvoice</b><br/>"); 
	    		 //exit;
	    		 
	    		 //-------------------------------------------------------------------------------------------------------------------------------------------
	    		 ///Remove invoice_item from main multiInvoice
	    		 /*$query = "SELECT item.id 
					FROM  vps_invoice inv, vps_invoice_item item
					WHERE inv.invoice_id = item.invoice_id and inv.customer_id = $customerID AND item.module_id = $mID";
	    		 
	    		 $this->db->query($query);
	    		 $removingInvoiceItemId = $this->db->fetch(0)->id;
	    		 
	    		 if($removingInvoiceItemId)
	    		 {
	    		 	$query = "DELETE FROM vps_invoice_item where id = $removingInvoiceItemId";
	    		 }
	    		 else
	    		 {
	    		 	die("$query <h1>returns null!</h1>");
	    		 }*/
	    		 /*$invoiceItemsDetails = $invoice->getInvoiceItemsDetails($invoiceDetails['invoiceID']);
	    		 
	    		 $invoiceItems = $invoiceItemsDetails['invoice_items'];
	    		 
	    		 $moduleIDs = array();
	    		 
	    		 
	    		 // Prepare data to create new multi invoice without deleting module
	    		 $test = array();
	    		 $mID = $invoice->getModuleIDByBillingModuleID($moduleBillingPlanID);
	    		 
	    		 
	    		 
	    		 foreach($invoiceItems as $i)
	    		 {
	    		 	//p($mID . " == " . $i['moduleID']);
	    		 	
	    		 	if($i['moduleID'] and $i['moduleID'] != $mID)
	    		 	{
	    		 		$moduleIDs[] = $i['moduleID'];	
	    		 		$test[] = $i['moduleID'];
	    		 	}
	    		 	else
	    		 	{
	    		 		$deletingModuleID = $i['moduleID'];
	    		 	}
	    		 }
	    		 
	    		 //p("invoiceItems:");
	    		 //var_dump($invoiceItems);
	    		 
	    		 //p("moduleIDs");
	    		 //var_dump($moduleIDs);
	    		 
	    		 $moduleBillingIDList = array();
	    		 
	    		 //$purcashedModules = $this->getPurchasedModule($customerID, null, 'todayOnly', 'today', $currency['id']);
	    		 
	    		 /*getPurchasedModule без всякой фигни*/
	    		 /*$query = "SELECT  mb.id, mb.month_count, mb2c.price, mb.module_id, mb.type, m2c.start_date " .
    			 "FROM ".TB_VPS_MODULE2CUSTOMER." m2c, ".TB_VPS_MODULE_BILLING." mb, " . TB_VPS_MODULE2CURRENCY . " mb2c 
    			 WHERE mb.id = m2c.module_billing_id
    			AND mb2c.module_billing_id = mb.id
    			AND m2c.customer_id = $customerID 
    			 AND mb2c.currency_id = {$currency['id']} ";
    			 $this->db->query($query);
    			 
    			 echo $query;
    			
    			 $purcashedModules = $this->db->fetch_all_array();
	    		 
	    		 //p("purcashedModules");
	    		 //var_dump($purcashedModules);
	    		 
	    		 //p("change moduleIDs from ");
	    		 //var_dump($moduleIDs);
	    		 //p("to");
	    		 $moduleIDs = array();
	    		 foreach($purcashedModules as $pm)
	    		 {
	    		 	if($pm['module_id'] != $deletingModuleID)
	    		 	{
	    		 		$moduleIDs[] = $pm['id'];
	    		 	}
	    		 }
	    		 //var_dump($moduleIDs);
	    		 //p("modules");
	    		 $modules = $this->getModulesBillingByIDs($moduleIDs,$currency['id']);
	    		 var_dump($modules);*/
	    		 
	    		 //echo "<h1>Normal modules ;)</h1>";
	    		 $modules = array(); //clear modules
	    		 foreach($invoiceDetails['modules'] as $m)
	    		 {
	    		 	if($m['id'] != $moduleBillingPlanID)
	    		 	{
	    		 		$modules[] = $m;
	    		 	}
	    		 }
	    		 //var_dump($modules);
	    		 //exit;
	    		 
	    		// p("deleting module id:",$deletingModuleID);
	    		 
	    		 //$deletingModuleInfo = $this->getModulesBillingByIDs(array($deletingModuleID),$currency['id']);
	    		 //$deletingModuleInfo = $deletingModuleInfo[0];
	    		 //echo "deletingModuleInfo:";
	    		 //echo "<br/>";
	    		 //var_dump($deletingModuleInfo);
	    		 
	    		 
	    		 $multiInvoiceData = array();
	    		 $multiInvoiceData['billingID'] = $billingID;
	    		 $multiInvoiceData['appliedModules'] = $modules;

	    		 //Cancel Invoice 
	    		 $invoice->cancelInvoice($invoiceDetails['invoiceID']);
	    		 //echo("<br/>Balance: " . $invoice->getBalance($customerID) . "<b>cancelInvoice</b><br/>");
 				 //p("canceledInvoice id:",$invoiceDetails['invoiceID']);
	    		 // Create New Multi Invoice 
	    		 
	    		 //echo "<br/>create new multi invoice<br/>";
	    		 //p("multiInvoiceData");
	    		 //var_dump($multiInvoiceData);
	    		 //p("delete applied modules for test..");
	    		 
	    		 if($invoiceDetails['billingInfo']) // If multiinvoice is included billing
	    		 {
	    		 	//p("multiinvoice is included billing");
	    		 	$insertedInvoiceID = $invoice->createMultiInvoiceForNewCustomer($customerID,$trialEndDate,$billingID,$multiInvoiceData);
	    		 	//var_dump($multiInvoiceData);
    			 }
    			 else // If multiinvoice contains just modules
    			 {
    			 	//p("multiinvoice contains just modules");
    			 	$billing = new Billing($this->db);
    			 	
    			 	$billingModulesIDs = array();
    			 	foreach($modules as $m)
    			 	{
    			 		$billingModulesIDs = $m['id'];
    			 	}
    			 	//var_dump($billingModulesIDs);
    			 	$billing->applyModuleBillingPlan($customerID,$billingModulesIDs,$invoiceDetails['periodStartDate']);
    			 }
	    		 
	    		 
	    		 //exit;
	    		 //echo("<br/>Balance: " . $invoice->getBalance($customerID) . " <b>createMultiInvoiceForNewCustomer</b><br/>");
	    		 
	    		 //p("billingID",$billingID);
	    		 //p("trialEndDate",$trialEndDate);
	    		 
	    		 
	    		 
	    		 //$this->db->rollbackTransaction();*/
	    		 //exit;
	    		 
    	}
    	else /** Invoice with one item */
    	{
    		/*
    		 * Cancel invoice
    		 */
    		 
    		 if($invoiceDetails)
    		 {
    		 	if (strtolower($invoiceDetails['status']) == 'due') 
    		 	{
    				$invoice->cancelInvoice($invoiceDetails['invoiceID']);
    				//p("cancel single Invoice");
    				//exit;
	    		} 
	    		else 
	    		{
	    			//echo("<br/>Balance: " . $invoice->getBalance($customerID) . " <b>Begin</b><br/>");
	    			$backToCustomer = $invoice->partialRefund($invoiceDetails);
	    			//$invoice->manualBalanceChange($customerID,'+',$backToCustomer);
	    			$deletingModuleInfo = $invoiceDetails['modules'][0];
	    			
	    			$customInfo = "Canceled module <b>{$deletingModuleInfo['name']}</b>,
		    		month - <b>{$deletingModuleInfo['month_count']}</b>, 
		    		type - <b>{$deletingModuleInfo['type']}</b>,
		    		back to customer - <b>$ $backToCustomer</b>
		    		<a href='/voc_src/vwm/vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceDetails['invoiceID']}'>Original Invoice</a>";
	    		 	$customInfo = mysql_escape_string($customInfo);
	    		 	$invoice->createCustomInvoice($customerID, $backToCustomer * -1, $invoiceDetails['suspensionDate'], 0, $customInfo,'CANCELED');
	    		 	//echo("<br/>Balance: " . $invoice->getBalance($customerID) . " <b>createCustomInvoice</b><br/>");
	    		 	$query = "UPDATE ".TB_VPS_INVOICE. " " .
						"SET status = 'CANCELED' " .
						"WHERE invoice_id = ".$invoiceDetails['invoiceID'];
					$this->db->query($query);							
					
					$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);		
					$payment->cancelInvoicePayment($invoiceID, 0,$type); 
					//echo("<br/>Balance: " . $invoice->getBalance($customerID) . " <b>cancelPayment</b><br/>");
					//p("delete module from gacl");
					//var_dump($deletingModuleInfo);
					//Delete module from gacl
					
					$ms = new ModuleSystem($this->db);
					$vps2voc = new VPS2VOC($this->db);
					$moduleName = $vps2voc->getModuleNameByID($deletingModuleInfo['module_id']);
			    	//p("moduleName",$moduleName);
					$ms->setModule2company($moduleName,0,$customerID);
					
					/*Delete module2customer*/
	   				$rowsDeleted = $this->deleteModule2Customer($safeCustomerID,$safeModuleBillingPlanID);
				    
	    			//p("cancel PAID single Invoice");
	    			//$invoice->cancelInvoice($invoiceDetails['invoiceID']);
	    			
	    			//exit;
	    		}
    		 } 
    	}
    	
		
	
    	//switching off the module
    	/*
    	if ($invoiceDetails['periodStartDate'] <= date('Y-m-d') && $invoiceDetails['periodEndDate'] >= date('Y-m-d')) {
    		p("cancelModule2company");
	    	$ms = new ModuleSystem($this->db);
	    	$vps2voc = new VPS2VOC($this->db);
	    	$moduleName = $vps2voc->getModuleNameByID($invoiceDetails['moduleID']);
	    	p("moduleName",$moduleName);
			$ms->setModule2company($moduleName,0,$customerID);
    	}*/
    	
    	//exit;
    	return true;
    }
    
    
    /**
     * 
     * @param $customerID
     * @param $moduleID = null
     * @param $invoiceType = 'todayOnly'
     * @param $period = 'today'
     * @param $currencyID = null
     * @return unknown_type
     */
    public function getPurchasedModule($customerID, $moduleID = null, $invoiceType = 'todayOnly', $period = 'today', $currencyID = null) {
    	$safeCustomerID = mysql_escape_string($customerID);
    	$safeModuleID = mysql_escape_string($moduleID);
    	
    	$query = "SELECT  mb.id, mb.month_count, mb2c.price, mb.module_id, mb.type, m2c.start_date " .
    			"FROM ".TB_VPS_MODULE2CUSTOMER." m2c, ".TB_VPS_MODULE_BILLING." mb, " . TB_VPS_MODULE2CURRENCY . " mb2c 
    			WHERE mb.id = m2c.module_billing_id
    			AND mb2c.module_billing_id = mb.id
    			AND m2c.customer_id = $safeCustomerID ";
    	if(!is_null($currencyID))
    	{
    		$query .= " AND mb2c.currency_id = $currencyID ";
    	}
    	
    	if (!is_null($moduleID)) {
    		$query .= "AND mb.module_id = ".$safeModuleID;
    	}
    	
    	$this->db->query($query);
    	
    	
    	$modulesCnt = $this->db->num_rows();
    	if ($modulesCnt > 0) {
    		$modulesFetched = $this->db->fetch_all_array();    		
    		$vps2voc = new VPS2VOC($this->db);
    		$invoiceObj = new Invoice($this->db);
    		$allAvailableModules = $vps2voc->getModules();    
    		$modulesExists = array();	//needed only if $period[1] is null
    		$modules = array();
    		if ($period == 'today') {
    			$period = array(date('Y-m-d'), null);
    		} elseif (!is_array($period)) {
    			$period = array(date('Y-m-d', strtotime($period)), null);
    		} else {
    			foreach ($period as $key => $value) {
    				$period[$key] = date('Y-m-d', strtotime($value));
    			}
    		}    		
    		foreach($modulesFetched as  $module) {
    			if (is_null($period[1])) {
    				if ($module['start_date'] > $period[0] && $invoiceType == 'todayOnly') {
    					continue;	//start_date for this module in future but  now we look for todayOnly
    				}
    				//if $invoiceType == 'todayOnly' we should take only 1 current modulePlan
    				if (!is_null($modulesExists[$module['module_id']]) && $invoiceType == 'todayOnly') {
    					$id = $modulesExists[$module['module_id']];
    					if ($modules[$id]['start_date'] > $module['start_date']) {
    						continue;	//start_date of this module early than previous found for it, maybe its from the past so we dont need it \NEW: WE NEED IT ALL!
    					}
    				} else {
    					$id = count($modules);
    					$modulesExists [$module['module_id']]= $id;
    				}	
    			} else {
    				$month_count = ' + '.$module['month_count'].(($module['month_count'] == 1)?' month':' months').' - 1 day';
    				if (($module['start_date'] >= $period[0] && $module['start_date'] <= $period[1]) || 
    				 (date('Y-m-d',strtotime($module['start_date'].$month_count)) > $period[0] && 
    				  date('Y-m-d',strtotime($module['start_date'].$month_count)) < $period[1]) || 
    				 (date('Y-m-d',strtotime($module['start_date'].$month_count)) > $period[1] &&
    				  $module['start_date'] < $period[0])) {
    				 	$id = count($modules);	
    				 } else {
    				 	continue;	//period of this module does not conflict with chosen period  
    				 }
    			}
    			//if we get here we have $id in $modules and we should add current $module to $modules[$id]
    			$modules[$id] = $module;
    			$modules[$id]['module_name'] = $allAvailableModules[$modules[$id]['module_id']];
    			
    			$modules[$id]['currentInvoice'] = $invoiceObj->getInvoiceForModuleByStartDate($customerID,$modules[$id]['id'],$modules[$id]['start_date']);//($invoiceType == 'todayOnly')?($invoiceObj->getCurrentInvoiceForModule($customerID, $modules[$id]['id'], date('Y-m-d',strtotime($modules[$id]['start_date'].' + 1 day')))):($invoiceObj->getCurrentOrFutureInvoiceForModule($customerID, $modules[$id]['id'], date('Y-m-d',strtotime($modules[$id]['start_date'].' + 1 day'))));
    		}
    		return $modules;
    	} else {
    		return false;
    	}
    }
    
    public function printArray( $a ){
  
		   static $count; 
		  $count = (isset($count)) ? ++$count : 0;
		  $colors = array('#FFCB72', '#FFB072', '#FFE972', '#F1FF72', '#92FF69', '#6EF6DA', '#72D9FE', '#77FFFF', '#FF77FF');
		  if ($count > count($colors)) {
		   $count--;
		   return;
		  }
		
		  if (!is_array($a)) {
		   echo "Passed argument is not an array!<p>";
		   return; 
		  }
		
		  echo "<table border=1 cellpadding=0 cellspacing=0 bgcolor=$colors[$count]>";
		
		  while(list($k, $v) = each($a)) {
		   echo "<tr><td style='padding:1em'>$k</td><td style='padding:1em'>$v</td></tr>\n";
		   if (is_array($v)) {
		    echo "<tr><td> </td><td>";
		    self::printArray($v);
		    echo "</td></tr>\n";
		   }
		  }
		  echo "</table>";
		  $count--;
		 }
    
    /**
     * 
     * @param $customerID
     * @param $currencyID <b>default 1</b>
     * @return unknown_type
     */
    public function getPurchasedPlansForCustomerView($customerID,$currencyID = 1) {
	    $vps2voc = new VPS2VOC($this->db);
	    $purchasedModules = $this->getPurchasedModule($customerID,null,'today&future','today',$currencyID);
	    
	    
	    
	    $allModules = $vps2voc->getModules();
	    
	    $user = new User($this->db, null, null, null);
	    $activatedModules = array();
	    foreach ($allModules as $key => $value) {
		    $activatedModules[$key] = $user->checkAccess($value,$customerID);
	    }
	    
	    $appliedModules = array();
	    foreach ($purchasedModules as $key => $module) {
		    $module_plan = array(
			    'id' => $module['id'],
				'type' => $module['type'],
				'period' => $module['month_count'].(($module['month_count'] == 1)?'Month':'Months'),
				'start' => $module['currentInvoice']['periodStartDate'],
				'end' => $module['currentInvoice']['periodEndDate'],
				'status' => $module['currentInvoice']['status'],
				'price' => $module['price'],
		    	'currency_id'	=>  $module['currentInvoice']['currency_id'],
		    );
		    if (!is_null($appliedModules[$module['module_id']])) {
			    $appliedModules[$module['module_id']]['plans'] []= $module_plan;
		    } else {
			    $appliedModules[$module['module_id']] = array(
				    'module_id' => $module['module_id'],
					'module_name' => $module['module_name'],
					'plans' => array($module_plan)
			    );
			    
			    //lets set status for module
			    if ($activatedModules[$module['module_id']]) {
				    $appliedModules[$module['module_id']]['status'] = 'active';
				    $activatedModules[$module['module_id']] = false;
			    } else {
				    $appliedModules[$module['module_id']]['status'] = 'unactive';
			    }
		    }
	    }
	    
	    $bonus = 0;
	    foreach ($activatedModules as $module => $activated) {
		    if ($activated) {
			    $activatedModules [$module] = $allModules[$module]; //if module activated but not purchased we should write its as bonus activated
			    $bonus = 1;
		    }	
	    }
	    return array (
		    'modules' => $appliedModules,
			'bonus' => ($bonus == 1)?$activatedModules:null
	    );
	}
    
	public function deletePlanFromSchedule($scheduleID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT customer_id FROM ".TB_VPS_SCHEDULE_CUSTOMER_PLAN." WHERE id = ".$scheduleID);
		$data = $this->db->fetch(0);		
		$this->db->query("DELETE FROM ".TB_VPS_SCHEDULE_CUSTOMER_PLAN." WHERE id = ".$scheduleID);		
		$this->db->query("DELETE FROM ".TB_VPS_SCHEDULE_LIMIT." WHERE customer_id = ".$data->customer_id);
    }
    
    
    
	public function getDistinctMonths() {
    	//$this->db->select_db(DB_NAME);
				
		$this->db->query("SELECT distinct months_count  FROM ".TB_VPS_BILLING." WHERE defined = 0 ORDER BY months_count");
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);							
				$months[] = $data->months_count;
			}
    	}    	    	
		
		return $months;
    }
    
    public function getDistinctSource($currencyID = 1) { //Default 1 == USD
    	//$this->db->select_db(DB_NAME);
    	
    	//$query = "SELECT distinct bplimit, one_time_charge  FROM ".TB_VPS_BILLING." WHERE defined = 0 ORDER BY bplimit";
    	
    	$query = "SELECT distinct bil.bplimit, cur.one_time_charge
    				FROM vps_billing bil, vps_billing2currency cur 
    				WHERE	cur.billing_id = bil.billing_id
    				AND		bil.defined = 0
    				AND		cur.currency_id = $currencyID
    				ORDER BY bil.bplimit";
				
		$this->db->query($query);
		
		$sources = $this->db->fetch_all_array();
		
		/*if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);							
				$source = array (
					'bplimit' 			=> $data->bplimit,
					'one_time_charge'	=> $data->one_time_charge					 
				);
				$sources[] = $source;
			}
    	}    */	    	
		
		return $sources;
    }
    
    public function getAvailableExtraLimits($currencyID = 1) {
    	
    	//$this->db->select_db(DB_NAME);
    	    	

    	$query = "SELECT limit_price_id, lp.limit_id, bplimit, default_limit, l2c.increase_cost, type, l.name, l.increase_step, l.unit_type " .
    			"FROM ".TB_VPS_LIMIT_PRICE." lp, ".TB_VPS_LIMIT." l, " . TB_VPS_LIMIT_PRICE2CURRENCY . " l2c 
    			WHERE l.limit_id = lp.limit_id
    			AND defined = 0 
    			AND l2c.currency_id = $currencyID
    			AND l2c.vps_limit_price_id = lp.limit_price_id
    			GROUP BY bplimit, l.name, type
    			ORDER BY type DESC, bplimit, l.name DESC"; 	
  	
    	$this->db->query($query);

    	$extraLimits = $this->db->fetch_all_array();
    	
    	
    	
    	//var_dump($extraLimits);
    	
    	
    	/*if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$extraLimit = array (
					'limit_price_id'	=> $data->limit_price_id,
					'name'				=> $data->name,
					'increase_step'		=> $data->increase_step,
					'unit_type'			=> $data->unit_type,
					'bplimit'			=> $data->bplimit,					
					'default_limit'		=> $data->default_limit,
					'increase_cost'		=> $data->increase_cost,
					'type'				=> $data->type					
				);
				
				$extraLimits[] = $extraLimit;
			}
    	}*/
    	    	
    	$this->db->query("SELECT distinct bplimit FROM ".TB_VPS_LIMIT_PRICE." WHERE defined = 0");
    	if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);							
				$sources[] = $data->bplimit;
			}
    	}
    	$extraLimits['sources'] = $sources;
    	
    	$this->db->query("SELECT * FROM ".TB_VPS_LIMIT."");
    	if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$limitInfo = array (
					'limit_id'		=>	$data->limit_id,					
					'increase_step'	=>	$data->increase_step,
					'unit_type'		=>	$data->unit_type
				); 							
				$limitsInfo[$data->name] = $limitInfo;
			}
			$extraLimits['info'] = $limitsInfo;
    	}    	 
    	    	
    	return $extraLimits;    	    	
    }
    
    /**
     * 
     * @param $customerID default <b>All</b>
     * @param $currencyID default <b>1 (USD)</b>
     * @return unknown_type
     */
    public function getDefinedPlans($customerID = "All",$currencyID = 1) {
    	//$this->db->select_db(DB_NAME);    	
    	/*if ($customerID == "All") {
	    	$query = "SELECT b.billing_id, c.customer_id, b.bplimit, b.months_count, b.one_time_charge, b.price, b.type " .
		    	"FROM ".TB_VPS_BILLING." b, ".TB_VPS_CUSTOMER." c " .
				"WHERE b.billing_id = c.billing_id " .				
				"AND b.defined = 1 ";						
    	} else {
    		$query = "SELECT b.billing_id, c.customer_id, b.bplimit, b.months_count, b.one_time_charge, b.price, b.type " .
		    	"FROM ".TB_VPS_BILLING." b, ".TB_VPS_CUSTOMER." c " .
				"WHERE b.billing_id = c.billing_id " .				
				"AND c.customer_id = ".$customerID;
    	}*/
    	
    	$query = "SELECT b.billing_id as 'billingID', c.customer_id, b.bplimit, b.months_count, b2c.one_time_charge, b2c.price, b.type 
		    	FROM ".TB_VPS_BILLING." b, ".TB_VPS_CUSTOMER." c, " . TB_VPS_BILLING2CURRENCY . " b2c
				WHERE b.billing_id = c.billing_id				
    			AND b2c.billing_id = b.billing_id
    			AND b2c.currency_id = $currencyID";
    	if($customerID != "All") {
				$query .= " AND c.customer_id = ".$customerID;
    	}
    	
    							
		$this->db->query($query);
		
		$billingPlans = $this->db->fetch_all_array();
		
		/*$numRows = $this->db->num_rows();
		if ($numRows) {
			for ($i=0; $i < $numRows; $i++) {
				$data=$this->db->fetch($i);
				
				$billingPlan = array (
					'billingID'			=> $data->billing_id,
					'customer_id'		=> $data->customer_id,
					'bplimit'			=> $data->bplimit,					
					'months_count'		=> $data->months_count,					
					'one_time_charge'	=> $data->one_time_charge,					
					'price'				=> $data->price,
					'type'				=> $data->type												
				);																																			
				$billingPlans[] = $billingPlan;
			}
			
			
    	}*/
    	$numRows = count($billingPlans);
    	for ($i=0; $i<$numRows;$i++) {
			// Adding extra limits
			
			$limits = $this->getLimits($billingPlans[$i]['customer_id']); // with included Bridge functionality
			$billingPlans[$i]['limits'] = $limits;
		}
    	
    	return $billingPlans;  
    }
    
    
    public function updateBillingPlan ($newBillingPlan,$currencyID = 1) {    	
    	/* ---------I N P U T---------------
    	 * defined = 1 (defined Billing Plan)
    	 * $newBillingPlan = array (
    	 *		'billingID',
		 *		'customerID',
		 *		'bplimit',
		 *		'monthsCount',
		 *		'oneTimeCharge',
		 *		'price',
		 *		'type',
		 *		'MSDSDefaultLimit',
		 *		'MSDSIncreaseCost',
		 *		'memoryDefaultLimit',
		 *		'memoryIncreaseCost',
		 *		'defined' = 1 				
    	 * );
    	 * 
    	 * defined = 0 (standart Billing Plan)
    	 * $newBillingPlan = array (
    	 *		'billingID',
		 *		'oneTimeCharge',
		 *		'price',	
		 *		'defined' = 0 				
    	 * );
    	 * */
    	 
    	 
    	//$this->db->select_db(DB_NAME);
    	/**
    	 * 					"one_time_charge = '".$newBillingPlan['oneTimeCharge'] ."', " .
					"price = '".$newBillingPlan['price'] ."', " .
    	 */
    	
    	//defined Billing Plan
    	if ($newBillingPlan['defined'] == 1) {
    		
    		/*Update price*/
    		$query = "UPDATE " . TB_VPS_BILLING2CURRENCY . " 
			SET 
			price = {$newBillingPlan['price']},
			one_time_charge = {$newBillingPlan['oneTimeCharge']}
			WHERE billing_id = {$newBillingPlan['billingID']}
			AND currency_id = $currencyID";
			$this->db->query($query);	
		
    		
    		$query = "UPDATE ".TB_VPS_BILLING." " .
    			"SET bplimit = ".$newBillingPlan['bplimit'] .", " .
					"months_count = ".$newBillingPlan['monthsCount'] .", " .

					"type = '".$newBillingPlan['type'] ."' " .
				"WHERE billing_id = ".$newBillingPlan['billingID'];
    		
			$this->db->query($query);
			
			$query = "SELECT cl.limit_price_id, lp.limit_id, l.name " .
					"FROM ".TB_VPS_CUSTOMER_LIMIT." cl, ".TB_VPS_LIMIT_PRICE." lp, ".TB_VPS_LIMIT." l " .
					"WHERE cl.limit_price_id = lp.limit_price_id " .
					"AND cl.customer_id = ".$newBillingPlan['customerID'].
					" AND lp.limit_id = l.limit_id ";
					
			$this->db->query($query);
			
			$numRows = $this->db->num_rows();
			if ($numRows) {
		    	for ($i=0; $i < $numRows; $i++) {
					$data = $this->db->fetch($i);
										
					$limitPrice = array (
						'limitPriceID'	=> $data->limit_price_id,
						'name' 			=> $data->name
					);
					$limitPrices[] = $limitPrice;
		    	}
		    	
		    	// very very bad variables((
		    	foreach ($limitPrices as $limitPrice) {
		    		if ($limitPrice['name'] == "MSDS") {
						$query = "UPDATE ".TB_VPS_LIMIT_PRICE." " .
								"SET default_limit = ".$newBillingPlan['MSDSDefaultLimit'].", " .
									"increase_cost = ".$newBillingPlan['MSDSIncreaseCost']." " .
								"WHERE limit_price_id = ".$limitPrice['limitPriceID'];
							
					} elseif ($limitPrice['name'] == "memory") {
						$query = "UPDATE ".TB_VPS_LIMIT_PRICE." " .
								"SET default_limit = ".$newBillingPlan['memoryDefaultLimit'].", " .
									"increase_cost = ".$newBillingPlan['memoryIncreaseCost']." " .
								"WHERE limit_price_id = ".$limitPrice['limitPriceID'];
					}
					$this->db->query($query);			    														   
		    	}		   
		    }
		 
		//standart Billing Plan    		    
    	} else {
			
			$query = "UPDATE " . TB_VPS_BILLING2CURRENCY . " 
			SET 
			price = {$newBillingPlan['price']}
			WHERE id = {$newBillingPlan['billing2currency_id']}";
			$this->db->query($query);
			
    	}   	
    }
    
    public function addDefinedBillingPlan($newBillingPlan) {
    
    	/* ---------I N P U T---------------
    	 * 
    	 * $newBillingPlan = array (
    	 *		'customerID',
		 *		'bplimit',
		 *		'monthsCount',
		 *		'oneTimeCharge',
		 *		'price',
		 *		'type',
		 *		'MSDSDefaultLimit',
		 *		'MSDSIncreaseCost',
		 *		'memoryDefaultLimit',
		 *		'memoryIncreaseCost',
		 *		'requestID' - if user send a defined Billing Plan requst. Should change request status to 'processed',
		 *		'applyWhen' - bpEnd | asap 				
    	 * );
    	 * 
    	 */	
    	 
    	 //insert into vps_billing
    	 //$this->db->select_db(DB_NAME);
    	 $query = "INSERT INTO ".TB_VPS_BILLING." (bplimit, months_count, one_time_charge, price, type, defined) VALUES (" .
    	 		$newBillingPlan['bplimit'].", " .
    	 		$newBillingPlan['monthsCount'].", " .
    	 		"'".$newBillingPlan['oneTimeCharge']."', " .
    	 		"'".$newBillingPlan['price']."', " .
    	 		"'".$newBillingPlan['type']."', " .
    	 		"1 )";    	 		
    	 $this->db->query($query);  
    	 
    	 
    	 
    	 //echo "query:   	$query<br/>"; 
    	 //$insertedBillingPlanID = mysql_insert_id(); OLD
    	 $insertedBillingPlanID = $this->db->getLastInsertedID();
    	 
    	 
    	 $currency = $this->GetCurrencyByCustomer($newBillingPlan['customerID']);
    	 
    	 
    	 /*Insert into billing2currency */
    	 $query = "INSERT INTO " . TB_VPS_BILLING2CURRENCY . " (billing_id, 	currency_id, 	price, 	one_time_charge) VALUES
    	 			($insertedBillingPlanID, {$currency['id']}, {$newBillingPlan['price']}, {$newBillingPlan['oneTimeCharge']})";
    	 
    	 $this->db->query($query);  
    	 
    	 //echo "insertedBillingPlanID: $insertedBillingPlanID <br/>";
    	 //insert into vps_limit_price
    	 $query = "INSERT INTO ".TB_VPS_LIMIT_PRICE." (limit_id, bplimit, default_limit, increase_cost, type, defined) VALUES (" .
    	 		"1, " .	// 1-MSDS, 2-memory
    	 		$newBillingPlan['bplimit'].", " .
    	 		$newBillingPlan['MSDSDefaultLimit'].", " .
    	 		"'".$newBillingPlan['MSDSIncreaseCost']."', " .
    	 		"'".$newBillingPlan['type']."', " .
    	 		"1 )";
    	 $this->db->query($query);
    	 //echo "query:   	$query<br/>";
    	 $insertedLimitPriceID[] = $this->db->getLastInsertedID();//mysql_insert_id(); OLD
    	 		    	 		 
    	 $query = "INSERT INTO ".TB_VPS_LIMIT_PRICE." (limit_id, bplimit, default_limit, increase_cost, type, defined) VALUES (" .		
    	 		"2, " .	// 1-MSDS, 2-memory
    	 		$newBillingPlan['bplimit'].", " .
    	 		$newBillingPlan['memoryDefaultLimit'].", " .
    	 		"'".$newBillingPlan['memoryIncreaseCost']."', " .
    	 		"'".$newBillingPlan['type']."', " .
    	 		"1 )";
    	 $this->db->query($query);
    	 //echo "query:   	$query<br/>";
    	 $insertedLimitPriceID[] = $this->db->getLastInsertedID();//mysql_insert_id(); OLD
    	 //echo "insertedLimitPriceID";
    	 //var_dump($insertedLimitPriceID);
    	 //	if customer already has plan, so add to schedule
    	 if ($this->getCustomerPlan($newBillingPlan['customerID'])) {
    	 	echo "if customer already has plan, so add to schedule<br/>";
    	 	echo "<br/>insertedLimitPriceID $insertedLimitPriceID";
    	 	$this->setScheduledPlan($newBillingPlan['customerID'], $insertedBillingPlanID, $newBillingPlan['applyWhen'], $insertedLimitPriceID);
    	 		
    	 //	else first time. set customer plan and invoice
    	 } else {
    	 	echo "else first time. set customer plan and invoice<br/>";
			$this->setCustomerPlan($newBillingPlan['customerID'], $insertedBillingPlanID);
			$this->setCustomerLimit($newBillingPlan['customerID'], $insertedLimitPriceID[0], $newBillingPlan['MSDSDefaultLimit'], true);
			$this->setCustomerLimit($newBillingPlan['customerID'], $insertedLimitPriceID[1], $newBillingPlan['memoryDefaultLimit'], true);
			
			//Bridge
			$vps2voc = new VPS2VOC($this->db);						
			
//			//	save BP limit to bridge
//			$customerLimits = $vps2voc->getCustomerLimits($newBillingPlan['customerID']);
//			$customerLimits['Source count']['limit_id'] = 3;	//	3 - bplimit
//			$customerLimits['Source count']['max_value'] = $newBillingPlan['bplimit'];
//			$inputLimit['Source count'] = $customerLimits['Source count'];			
//			$vps2voc->setCustomerLimits($newBillingPlan['customerID'], $inputLimit); 	
			
			$customerDetails = $vps2voc->getCustomerDetails($newBillingPlan['customerID']);						    	 	
			$invoice = new Invoice($this->db);		
			
			$invoice->createInvoiceForBilling($newBillingPlan['customerID'], $customerDetails["trial_end_date"], $insertedBillingPlanID); //Bridge trial_end_date
			//echo "end<br/>";
    	 }
    	 
    	 
    	 $query = "UPDATE ".TB_VPS_DEFINED_BP_REQUEST." " .
    	 			"SET status = 'processed' " .
    	 			"WHERE id = ".$newBillingPlan['requestID'];   	 
    	 $this->db->query($query);
    	 //echo "query:   	$query<br/>";
    	 //echo "end of addDefinedBillingPlan";
    }
    
    //	create invoice when customer orders limit increase
    //	we calculate increase cost and call invoice->createInvoiceForLimit
    /**
     * 
     * @param $limitName
     * @param $customerID
     * @param $plusToValue
     * @param $currencyID <b>1 default (USD)</b>
     * @return unknown_type
     */
    public function invoiceIncreaseLimit($limitName, $customerID, $plusToValue,$currencyID = 1) {
    	
    	/* ---------I N P U T---------------
    	 * 
    	 * $limitName	- 'MSDS' or 'memory'
    	 * $customerID
    	 * $plusToValue	- add this var to max limit value (maxValue += plusToValue)    	  
    	 */	
    	 
    	//$this->db->select_db(DB_NAME);
    	
    	$limits = $this->getLimits($customerID,$currencyID); //with Bridge functionality
		$newValue = $limits[$limitName]['max_value'] += $plusToValue;		
				
		$invoice = new Invoice($this->db);				
		$increaseCost = $limits[$limitName]['increase_cost'] * ($plusToValue/$limits[$limitName]['increase_step']);			
		$limitInfo = "'Increase ".$limitName. " + ".$plusToValue." ".$limits[$limitName]['unit_type']."'";

		$invoice->createInvoiceForLimit($customerID, $increaseCost, $limitInfo);
		//echo 	$limits[$limitName]['max_value'] . " + " . 		$plusToValue;		
    }
    
    
    public function increaseLimit($invoiceID) {
    	
    	// increase limit after user payed invoice that was generated by invoiceIncreaseLimit()
    	 
    	$invoice = new Invoice($this->db);
    	$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
    	preg_match("/Increase\s(.*)\s\+\s(\d*)\s/",$invoiceDetails['limitInfo'], $matches);
    	$limitName = $matches[1];
    	$plusToValue = $matches[2];
    	
    	$limits = $this->getLimits($invoiceDetails['customerID']);
		$newValue = $limits[$limitName]['max_value'] + $plusToValue;
		
		$this->setCustomerLimit($invoiceDetails['customerID'], $limits[$limitName]['limit_price_id'], $newValue); //with Bridge functionality
    }
    
    
    public function saveDefinedBillingPlanRequest($request) {
    	
    	/*	----INPUT----
    	 * $request = array(
    	 * 	customerID
	    	bplimit
	    	monthsCount
	    	type
	    	MSDSDefaultLimit
	    	memoryDefaultLimit
	    	description
	    	date
    	 * )
    	 */
    	
    	foreach($request as $key=>$value) {
    		$request[$key] = mysql_escape_string($value);
    	}
    	
    	//$this->db->select_db(DB_NAME);    	   				
	    $query = "INSERT INTO ".TB_VPS_DEFINED_BP_REQUEST." (customer_id, bplimit, months_count, type, MSDS_limit, memory_limit, description, date) VALUES (" .
	    		"".$request['customerID'].", " .
	    		"".$request['bplimit'].", " .
	    		"".$request['monthsCount'].", " .
	    		"'".$request['type']."', " .
	    		"".$request['MSDSDefaultLimit'].", " .
	    		"".$request['memoryDefaultLimit'].", " .
	    		"'".$request['description']."', " .
	    		"'".$request['date']."')";	    
	    $this->db->query($query);    	
    }
    
    
    public function getRequest($id = "All") {
    	//$this->db->select_db(DB_NAME);
    	
    	if ($id == "All") {
    		$query = "SELECT * " .
	    		"FROM ".TB_VPS_DEFINED_BP_REQUEST."";	
    	} else {    		
    		$query = "SELECT * " .
	    		"FROM ".TB_VPS_DEFINED_BP_REQUEST." " .
	    		"WHERE id = ".$id;
    	}    	   				
	    	    			    
	    $this->db->query($query);
	    
		if ($this->db->num_rows()) {
		    for ($i=0; $i < $this->db->num_rows(); $i++) {
			    $data = $this->db->fetch($i);									    
			    $request = array (
			    	'id' 			=> $data->id,
			    	'customerID' 	=> $data->customer_id,
			    	'bplimit' 		=> $data->bplimit,
			    	'monthsCount' 	=> $data->months_count,
			    	'type'			=> $data->type,
			    	'MSDSLimit'		=> $data->MSDS_limit,
			    	'memoryLimit'	=> $data->memory_limit,
			    	'description'	=> $data->description,
			    	'date'			=> $data->date,
			    	'status'		=> $data->status
			    );
			    $requests[] = $request;
		    }
		    return $requests;
		} else {
			return false;
		}
    }
    
    public function countRequests() {
    	//$this->db->select_db(DB_NAME);    	   				
	    $query = "SELECT status, count(*) requestCount " .
	    		"FROM ".TB_VPS_DEFINED_BP_REQUEST." " .
	    		"GROUP BY status";	    			    
	    $this->db->query($query);
	    
		if ($this->db->num_rows()) {
		    for ($i=0; $i < $this->db->num_rows(); $i++) {
			    $data = $this->db->fetch($i);									    
			    $requestCount[$data->status] = $data->requestCount;
		    }
		    return $requestCount;
		} else {
			return false;
		}		   	   	
	}		
    
    /**
     * 
     * @param $customerID
     * @param $currencyID <b>1 by default (USD)</b>
     * @return unknown_type
     */
    private function getLimits($customerID,$currencyID = 1) { 
    	 
    	//$this->db->select_db(DB_NAME);
    	/*old version    	   				
	    $query = "SELECT lp.limit_price_id, lp.limit_id, l.name, lp.default_limit, lp.increase_cost, cl.max_value, l.increase_step, l.unit_type " .
		    "FROM ".TB_VPS_LIMIT_PRICE." lp, ".TB_VPS_CUSTOMER_LIMIT." cl, ".TB_VPS_LIMIT." l " .
			"WHERE cl.limit_price_id = lp.limit_price_id " .
			"AND l.limit_id = lp.limit_id " .
			"AND cl.customer_id = ".$customerID;	    
	    $this->db->query($query);
	    */
	    
	    
	    $query = "SELECT lp.limit_price_id, lp.limit_id, lp.default_limit, lp2c.increase_cost, cl.max_value, l.increase_step, l.unit_type, l.name " .
		    "FROM ".TB_VPS_LIMIT_PRICE." lp, ".TB_VPS_CUSTOMER_LIMIT." cl, ".TB_VPS_LIMIT." l, " . TB_VPS_LIMIT_PRICE2CURRENCY . " lp2c " .
			"WHERE cl.limit_price_id = lp.limit_price_id 
	    	AND lp2c.vps_limit_price_id = lp.limit_price_id 
	    	AND lp2c.currency_id = $currencyID
			AND cl.customer_id = $customerID.
			 AND lp.limit_id = l.limit_id";	
	    
	    $this->db->query($query);
	    
	    if ($this->db->num_rows()) {
		    for ($i=0; $i < $this->db->num_rows(); $i++) {
			    $limitsData = $this->db->fetch($i);
			    
			    $limit = array(
			    	'limit_price_id'=> $limitsData->limit_price_id,
			    	'limit_id'		=> $limitsData->limit_id,
				    'default_limit'	=> $limitsData->default_limit,
					'increase_cost'	=> $limitsData->increase_cost,
					'max_value' 	=> $limitsData->max_value,
					'increase_step'	=> $limitsData->increase_step,
					'unit_type' 	=> $limitsData->unit_type
			    );
			    $limits[$limitsData->name] = $limit;
		    }		    
		   return $limits;
	    } else {
	    	return false;
	    }
    }
    
    
    private function setCustomerLimit($customerID, $limitPriceID, $value, $setLimitPrice = false) {
    	//$this->db->select_db(DB_NAME);
    	
    	if ($setLimitPrice) {
    			    	
    		$query = "INSERT INTO ".TB_VPS_CUSTOMER_LIMIT." (customer_id, limit_price_id, current_value, max_value) VALUES (".$customerID.", ".$limitPriceID.", 0, ".$value.")";
			$this->db->query($query);	
			
		} else {
    		
    		$query = "UPDATE ".TB_VPS_CUSTOMER_LIMIT." " .
		    	"SET max_value = ".$value." " .
				"WHERE limit_price_id = ".$limitPriceID." " .
				"AND customer_id = ".$customerID;
			$this->db->query($query);    		
    	}
    	
//    		//Bridge
//    		$limit_query = "SELECT limit_id FROM ".TB_VPS_LIMIT_PRICE." WHERE limit_price_id =".(int)$limitPriceID;
//    		$this->db->query($limit_query);
//    		$data = $this->db->fetch(0);
//    		$vps2voc = new VPS2VOC($this->db);
//    		$limit = array(
//    				'limit_id' => $data->limit_id,
//    				'max_value' => $value	
//    			);
//    		$vps2voc->setCustomerLimitByID($customerID, $limit);
//    		//end of bridge    			 	
    }
    
    /**
     * 
     * @param $billingID
     * @param $currencyID <b>1 by default (USD)
     * @return unknown_type
     */
    private function getDefaultLimit($billingID,$currencyID = 1) {
    	
    	//$this->db->select_db(DB_NAME);
	    $query = "SELECT lp.limit_price_id, lp.limit_id, lp.default_limit, lp2c.increase_cost, l.increase_step, l.unit_type, l.name " .
	    		"FROM ".TB_VPS_LIMIT_PRICE." lp, ".TB_VPS_BILLING." b, ".TB_VPS_LIMIT." l, " . TB_VPS_LIMIT_PRICE2CURRENCY . " lp2c 
	    		WHERE lp.bplimit = b.bplimit 
	    		AND lp2c.vps_limit_price_id = lp.limit_price_id
	    		AND lp2c.currency_id = $currencyID
	    		AND lp.type = b.type 
	    		AND lp.defined = 0 
	    		AND b.billing_id = $billingID.
	    		AND lp.limit_id = l.limit_id ";

	    $this->db->query($query);
	    if ($this->db->num_rows()) {
	    	$limitsFromDB = $this->db->fetch_all();
	    	foreach($limitsFromDB as $limitsData) {	    		 
			    $limit = array(
				    'limit_price_id'=> $limitsData->limit_price_id,
				    'limit_id'		=> $limitsData->limit_id,
					'default_limit'	=> $limitsData->default_limit,
					'increase_cost'	=> $limitsData->increase_cost,
					'max_value'		=> $limitsData->default_limit,
					'increase_step'	=> $limitsData->increase_step,
					'unit_type' 	=> $limitsData->unit_type
			    );
			    $limits[$limitsData->name] = $limit;
	    	}		    
		    return $limits;
	    } else {
	    	return false;
	    } 	    
    }
    
    private function getLimitPriceDetails($limitPriceID) {
    	//$this->db->select_db(DB_NAME);
	    $query = "SELECT * FROM ".TB_VPS_LIMIT_PRICE." WHERE limit_price_id = ".$limitPriceID;			
	    
	    $this->db->query($query);
	    if ($this->db->num_rows()) {		    
		    $limit = $this->db->fetch_array(0);						
		    /*$limit = array(
			    'limit_price_id'=> $limitsData->limit_price_id,
			    'limit_id'		=> $limitsData->limit_id,
			    'bplimit'		=> $limitsData->bplimit,
				'default_limit'	=> $limitsData->default_limit,
				'increase_cost'	=> $limitsData->increase_cost,
				'type'			=> $limitsData->type,
				'defined'		=> $limitsData->defined
		    );*/		    		    
		    return $limit;
	    } else {
	    	return false;
	    }    	
    }
    
    public function getLimitDetailsByID($limitID) {
	    $query = "SELECT * FROM ".TB_VPS_LIMIT." WHERE limit_id = ".$limitID;			
	    
	    $this->db->query($query);
	    if ($this->db->num_rows()) {		    
		    $limit = $this->db->fetch_array(0);		    		    
		    return $limit;
	    } else {
	    	return false;
	    }    	
    }
    
    public function getTrialLimitPriceDetails($limit_id) {
    	
    	//$this->db->select_db(DB_NAME);
	    $query = "SELECT * FROM ".TB_VPS_LIMIT_PRICE." WHERE limit_id = ".(int)$limit_id." order by bplimit Limit 1 ";			

	    $this->db->query($query);
	    if ($this->db->num_rows()) {		    
		    $limit = $this->db->fetch_array(0);						
		    /*$limit = array(
			    
			    'limit_id'		=> $limitsData->limit_id,
			    'bplimit'		=> $limitsData->bplimit,
				'default_limit'	=> $limitsData->default_limit,
				'type'			=> $limitsData->type,
				
		    );*/		    		    
		    return $limit;
	    } else {
	    	return false;
	    }    	
    }
    
    public function getMinBPLimitCount() {
    	
    	//$this->db->select_db(DB_NAME);
	    $query = "SELECT bplimit FROM ".TB_VPS_LIMIT_PRICE." order by bplimit Limit 1 ";			
	    
	    $this->db->query($query);
	    if ($this->db->num_rows()) {		    
		    $data = $this->db->fetch(0);						
		    return $data->bplimit;
	    } else {
	    	return 0;
	    }    	
    }
    
    /**
     * get all currency
     * @param $group = group array by currency.id
     * @return array (id,iso,sign,description). name of currency is (USD, EUR etc)
     */
    public function getCurrenciesList($group = false) {
    	
    	$query = "SELECT * FROM " . TB_VPS_CURRENCY;
    	$this->db->query($query);
    	$res = $this->db->fetch_all_array();
    	
    	if($group and $res) //group result by id
    	{
    		$curs = array();
    		foreach($res as $c)
    		{
    			$curs[$c['id']] = $c;
    		}
    		return $curs;
    	}
    	else
    	{
    		return $res;
    	}
    }
    
    /**
     * set currency to customer
     * @param $customerID
     * @param $currencyID
     * @return boolean true if ok or false if is not ok
     */
    public function setCurrency($customerID,$currencyID) {
    	
    	if(is_numeric($customerID) and is_numeric($currencyID)) {
    		
    		$query = "UPDATE " . TB_VPS_CUSTOMER . " SET currency_id = $currencyID WHERE customer_id = $customerID";
    		$this->db->query($query);
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public function getCurrencyByCustomer($customerID) {
    	if(is_numeric($customerID)) {
    		
    		$query = "SELECT cur.* FROM " . TB_VPS_CUSTOMER . " cus, " . TB_VPS_CURRENCY . " cur
    		 WHERE cus.currency_id = cur.id AND cus.customer_id = $customerID";
    		$this->db->query($query);
    		return $this->db->fetch_array(0);
   		}
    	else {
    		return false;
    	}
    }
    
    
    /**
     * get currency details
     * @param $currencyID
     * @return return array(id,iso,sign,description) or FALSE
     */
    public function getCurrencyDetails($currencyID) {

    	if(is_numeric($currencyID)) {
    		
    		$query = "SELECT * FROM " . TB_VPS_CURRENCY . " WHERE id = $currencyID";
    		$this->db->query($query);
    		$res = $this->db->fetch_array(0);
    		
    		return $res;
    	}
    	else {
    		return false;
    	}
    }
    
     private function emailNotification($customers, $action) {
    	$email = new EMail();
    	$from = VPS_SENDER_EMAIL;
    	
    	$config = $this->loadConfig();
    	
	    switch ($action) {
		    case 'change':			    			    			    			    
			    $subject = $config['change_customer_bp_email_subject'];			    
			    $message = $config['change_customer_bp_email_message']; 					    
			    foreach ($customers as $customer) {
					$to = $this->getCustomerEmail($customer);
			    //	$email->sendMail($from, $to, $subject, $message);	
			    }			    
			    break;
			case 'schedule':
				$subject = $config['schedule_bp_email_subject'];			    
			    $message = $config['schedule_bp_email_message'];
			    foreach ($customers as $customer) {
					$to = $this->getCustomerEmail($customer);
			    //	$email->sendMail($from, $to, $subject, $message);	
			    }			    
			    break;
			case 'tariffs':
				$subject = $config['change_customer_tariffs_email_subject'];			    
			    $message = $config['change_customer_tariffs_email_message'];			    			   
			    foreach ($customers as $customer) {
					$to = $this->getCustomerEmail($customer);
			    //	$email->sendMail($from, $to, $subject, $message);	
			    }			    
			    break;
			case 'limits':
			    $subject = $config['change_customer_limit_email_subject'];			    
			    $message = $config['change_customer_limit_email_message'];	    
			    foreach ($customers as $customer) {
					$to = $this->getCustomerEmail($customer);
			    //	$email->sendMail($from, $to, $subject, $message);	
			    }			    
			    break; 
	    }
	    
    }
    
    private function loadConfig() {
		//$this->db->select_db(DB_NAME);				
		$query = "SELECT * FROM ".TB_VPS_CONFIG;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			$numRows = $this->db->num_rows();
			for ($i=0; $i < $numRows; $i++) {
				$data=$this->db->fetch($i);
				$config[$data->name] = stripslashes($data->value);							
			}
		}
		
		return $config;	
	}
    
    function getCustomerEmail($customerID) {
    			
		$vps2voc = new VPS2VOC($this->db);
		$customerDetails = $vps2voc->getCustomerDetails($customerID);		
		return $customerDetails["email"];
		
	}
	
	
	private function _isModule2CustomerLink($customerID, $moduleBillingPlanID) {
		$safeCustomerID = mysql_escape_string($customerID);
    	$safeModuleBillingPlanID = mysql_escape_string($moduleBillingPlanID);    	    	 
    	
    	$query = "SELECT id FROM ".TB_VPS_MODULE2CUSTOMER." WHERE customer_id = ".$safeCustomerID." AND module_billing_id = ".$safeModuleBillingPlanID;
    	$this->db->query($query);
    	if ($this->db->num_rows() > 0) {
    		return true;    		
    	} else {
    		return false;
    	}    	    	
		
	} 
}
?>