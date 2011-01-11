<?php
	
	function loadConfig($db) {	
		$query = "SELECT * FROM ".TB_VPS_CONFIG;
		$db->query($query);
		
		if ($db->num_rows()) {
			$numRows = $db->num_rows();
			for ($i=0; $i < $numRows; $i++) {
				$data=$db->fetch($i);
				$config[$data->name] = stripslashes($data->value);							
			}
		}
		
		return $config;	
	}						
	
	$categoryID = "vps";
	
	/**
	 * Change and recalculate ballance
	 * @param $curentCurrency
	 * @param $currencySelectID
	 * @param $customerID
	 * @return unknown_type
	 */
	function btnChangeCurrencyClick($curentCurrency,$currencySelectID,$customerID)
	{
		if($curentCurrency['id'] != $currencySelectID)
		{
			global $db;
			$billing = new Billing($db);
			$newCurrency = $billing->getCurrencyDetails($currencySelectID);
		
			if($curentCurrency['iso'] != $newCurrency['iso']) /*Currency changed. Recount ballance in new currency*/
			{
				$invoice = new Invoice($db);
				$balance = $invoice->getBalance($customerID);
				
				$convertor = new CurrencyConvertor();
				
				$newBallance = $convertor->Sum(array($curentCurrency['iso'] => $balance), $newCurrency['iso']);
				
				$newBallance = round($newBallance,2);
				
				$query = "UPDATE " . TB_VPS_CUSTOMER . " SET balance = $newBallance WHERE customer_id = $customerID";
				
				$db->query($query);
				
				$convertStatus = "<br/>Your ballance has been converted from <b>{$curentCurrency['iso']} $balance</b> to <b>{$newCurrency['iso']} = $newBallance</b>";
				
				$billing->setCurrency($customerID,$_POST['currencySelect']);
				global $smarty;
				$smarty->assign('changeCurrencyStatus',$convertStatus);
			}
		}
	}
	
	switch ($_GET['vpsAction']) {
		
		case "browseCategory":	
			$xnyo->filter_get_var('itemID','text');		
			$itemID=$_GET['itemID'];
			
			$_SESSION["gobackAction"]="";
			
			$title=new Titles($smarty);
			$title->titleClassesAdmin($itemID);
			
			$smarty->assign("categoryID","tab_".$categoryID);
				
			switch ($itemID) {
				
				case "billing":
							
					$billing = new Billing($db);
					
					$currencies = $billing->getCurrenciesList();
					$smarty->assign("currencies",$currencies);
					
					$ci = $_POST['currencySelect'] ? $_POST['currencySelect'] : ($_GET['currency'] ? $_GET['currency'] : false);
					
					if($ci) {
						$curentCurrency = $billing->getCurrencyDetails($ci);
					}
					else {

						$curentCurrency = $billing->getCurrencyDetails( $currencies[0]['id']);
					}

					$smarty->assign("curentCurrency",$curentCurrency);
					
					//getting defined billing plan requests
					$requestCount = $billing->countRequests();
					$smarty->assign("unprocessedRequests",$requestCount['unprocessed']);
							
					//getting available billing plans
					$billingPlanList = $billing->getAvailablePlans($curentCurrency['id']);
					$smarty->assign("availablePlans",$billingPlanList);	
									
					//getting billing plans for modules
					$moduleBillingPlans= $billing->getModuleBillingPlans(NULL,$curentCurrency['id']);
					
					$smarty->assign("moduleBillingPlans",$moduleBillingPlans);
					
					$modules=$billing->getModules();					
					$smarty->assign("modules",$modules);
					
					//distinct months count and user count
					$months = $billing->getDistinctMonths();
					$sources = $billing->getDistinctSource($curentCurrency['id']);
					
					
					
					
					$smarty->assign("months",$months);					
					$smarty->assign("monthsCount",count($months));
					$smarty->assign("sources",$sources);
					
					
					//getting extra limits
					$extraLimits = $billing->getAvailableExtraLimits($curentCurrency['id']);				
					$smarty->assign("extraLimits",$extraLimits);
					$smarty->assign("extraLimitsSources",$extraLimits['sources']);											
					
					//getting defined plans
					
					$definedPlans = $billing->getDefinedPlans("All",$curentCurrency['id']);
					$vps2voc = new VPS2VOC($db);
					foreach ($definedPlans as $key=>$value)	{
						$customerDetails = $vps2voc->getCustomerDetails($definedPlans[$key]['customer_id']);						
						$definedPlans[$key]['customerName'] = $customerDetails['name'];
					}									
					$smarty->assign("definedPlans",$definedPlans);
					
					
					
					break;
					
				case "DBPRequests":
					$billing = new Billing($db);
					
					$requestList = $billing->getRequest();
					$vps2voc = new VPS2VOC($db);
					foreach ($requestList as $key=>$value)	{
						$customerDetails = $vps2voc->getCustomerDetails($requestList[$key]['customerID']);						
						$requestList[$key]['customerName'] = $customerDetails['name'];
					}				
					$smarty->assign("requestList",$requestList);
					break;
					
				case "discounts":
				    
				    $VPSAdmin = new VPSUser($db);
				    $Billing = new Billing($db);
				    $Invoice = new Invoice($db);
				    
				    $customerList = $VPSAdmin->getCustomerList();
				    
				    $billingPlanList = array();
				    for ($i=0; $i<count($customerList); $i++) {
				    	
				    	$billingPlan = $Billing->getCustomerPlan($customerList[$i]['id']);
				    	$allInvoices = $Invoice->getAllInvoicesList($customerList[$i]['id']);
				    	
				    	$billingPlan['total_charge'] = 0;
				    	foreach ($allInvoices as $inv)
				    		$billingPlan['total_charge'] += (float)$inv['paid'];
				    		 
				    	$billingPlanList[] = $billingPlan; 
				    	
				    }
				    
					$smarty->assign("billingPlans", $billingPlanList);
				    $smarty->assign("customers",$customerList);				    
					break;
					
				case "customers":
					$VPSUser = new VPSUser($db);
					$billing = new Billing($db);					
					
					$customerList = $VPSUser->getCustomerList();
					$trialCustomers = $VPSUser->getTrialCustomers();
					$notRegisteredAtVPS = $trialCustomers['notRegistered'];
					
					$billingPlanList = array();
					for ($i=0; $i<count($customerList); $i++) {						
						$billingPlan = $billing->getCustomerPlan($customerList[$i]['id']);
						$billingPlanList[] = $billingPlan;																		
					}
					
					$smarty->assign("billingPlans", $billingPlanList);
					$smarty->assign("customers",$customerList);
					$smarty->assign("notRegisteredCustomers",$notRegisteredAtVPS);						
					break;
					
				case "other":			   																																					
					$config = loadConfig($db);					
					$smarty->assign("config",$config);					
					break;
			}
			
			$smarty->assign("bookmarkType",$itemID);
			$smarty->display("tpls:vps.tpl");
			
			break;
		
		
		
		
		case "viewDetails":
			$xnyo->filter_get_var('itemID','text');		
			$itemID=$_GET['itemID'];
			
			switch ($itemID) {
				case "invoice":
				//	$smarty->assign("currentBookmark","viewDetails");
					
					$xnyo->filter_get_var('invoiceID', 'int');
					$invoiceID = $_GET['invoiceID'];
					
					$invoice = new Invoice($db);
					
					$invoiceDetails = $invoice->getInvoiceItemsDetails($invoiceID);	
								
					$smarty->assign("invoiceDetails", $invoiceDetails);
					
					$payment = new Payment($db);
					
					$paymentHistory = $payment->getHistory($invoiceID);		
								
					$smarty->assign("paymentHistory", $paymentHistory);
					
					$smarty->assign('action',"viewInvoice");
					$smarty->assign("bookmarkType","customers");																				
					$smarty->display("tpls:vps.tpl");	
					break;
					
					
					
				case "customer":
					$xnyo->filter_get_var('customerID','int');
					$customerID = $_GET['customerID'];	
					
					$VPSUser = new VPSUser($db);
					$billing = new Billing($db);
					$invoice = new Invoice($db);	

					$currentCurrency = $billing->getCurrencyByCustomer($customerID);
					
					if($_POST['currencySelect'])
					{
						btnChangeCurrencyClick($currentCurrency,$_POST['currencySelect'],$customerID);
						$currentCurrency = $billing->getCurrencyByCustomer($customerID);
					}
					
					
					
					/**
					 * BAD BAD BAD code style!
					 */
					/*$customerList = $VPSUser->getCustomerList();
					$billingPlanList = array();
					for ($i=0; $i<count($customerList); $i++) {
						if ($customerID == $customerList[$i]['id']) {
							$customerDetails = $customerList[$i];
							$billingPlan = $billing->getCustomerPlan($customerDetails['id']);
							
							$lastInvoice = $invoice->getLastInvoice($customerID);													
							$customerDetails['dueDate'] = ($lastInvoice['suspensionDate'] != null && $lastInvoice['suspensionDate'] == 'DUE') ? $lastInvoice['suspensionDate'] : "--";
							
							$invoices = $invoice->getAllInvoicesList($customerID);
						}											 																		
					}*/	

					// OK Style =)
					$vps2voc = new VPS2VOC($db);
					$customerDetails = $vps2voc->getCustomerDetails($customerID);

					$customerDetails['contactPerson'] = $customerDetails['contact'];
					$customerDetails['id'] = $customerDetails['customer_id'];
					
					$invoices = $invoice->getAllInvoicesList($customerID);
					
					for ($i=0; $i<count($invoices); $i++) {						
							$invoices[$i]['editable'] = 'yes';
					}
					
					$currencies = $billing->getCurrenciesList();
					
					
					
					//active modules
					$modulesView = $billing->getPurchasedPlansForCustomerView($customerID);
					$smarty->assign('modules',$modulesView['modules']);
					if (!is_null($modulesView['bonus'])) {
						$smarty->assign('bonusModules',$modulesView['bonus']);
					}

					
					$smarty->assign('currencies',$currencies);
					$smarty->assign('curentCurrency',$currentCurrency['id']);
					$smarty->assign('currencySign',$currentCurrency['sign']);
					$smarty->assign('customer',$customerDetails);
					$smarty->assign('billingPlan',$billingPlan);
					$smarty->assign('invoices',$invoices);
					$smarty->assign('action',$_GET['vpsAction']);					
					
					$smarty->assign("bookmarkType","customers");
					$smarty->display("tpls:vps.tpl");										
					break;
					
				
				
				case "notRegisteredCustomer":
					$xnyo->filter_get_var('customerID','int');
					$customerID = $_GET['customerID'];
					
					$vps2voc = new VPS2VOC($db);
					$customerDetails = $vps2voc->getCustomerDetails($customerID, $getWithNotRegistered = true);		
										
					$smarty->assign('customer',$customerDetails);
					$smarty->assign('action','notRegisteredCustomer');
					
					$smarty->assign("bookmarkType","customers");
					$smarty->display("tpls:vps.tpl");				
					break;						
								
			}						
			break;
				
				
				
				
		case "showEdit":				
			$itemID=$_GET['itemID'];
			
			switch ($itemID) {				
				case "availableBillingPlans":
					$billing = new Billing($db);
					
					$currencies = $billing->getCurrenciesList();
					$smarty->assign("currencies",$currencies);
					
					if($_GET['currency']) {
						$curentCurrency = $billing->getCurrencyDetails($_GET['currency']);
					}
					else {
						$curentCurrency = $billing->getCurrencyDetails( $currencies[0]['id']);
					}
					
					$b2cID = $_GET['b2cID'];
					$smarty->assign("b2cID",$b2cID);
					
					$smarty->assign("disableCurrencySelect",true);
					$smarty->assign("curentCurrency",$curentCurrency);
							
					//getting available billing plans
					$billingPlanList = $billing->getAvailablePlans();
					$smarty->assign("availablePlans",$billingPlanList);
					
					//distinct months count and user count
					$months = $billing->getDistinctMonths();
					$sources = $billing->getDistinctSource();
					$smarty->assign("months",$months);
					$smarty->assign("monthsCount",count($months));
					$smarty->assign("sources",$sources);
					
					//click on Billing Plan ID
				
					$editBillingPlanID = $_GET['id'];
					$smarty->assign("editBillingPlanID",$editBillingPlanID);
					
					//click on One Time Charge					
					$oneTimeCharge4bplimit = $_GET['oneTimeCharge4bplimit'];
					$smarty->assign("oneTimeCharge4bplimit",$oneTimeCharge4bplimit);
					
					$smarty->assign("edit",$itemID);
										
					$smarty->assign("bookmarkType","billing");
					$smarty->display("tpls:vps.tpl");
					break;													
				
				
				
				case "limits":
					$billing = new Billing($db);
					
					$curentCurrency = $billing->getCurrencyDetails($_GET['currencyID']);
					$smarty->assign("curentCurrency",$curentCurrency);
				
					$extraLimits = $billing->getAvailableExtraLimits();				
					$smarty->assign("extraLimits",$extraLimits);
					$smarty->assign("extraLimitsSources",$extraLimits['sources']);
					
					$xnyo->filter_get_var('subItemID','text');
					$smarty->assign("subItemID",$_GET['subItemID']);
										
					$xnyo->filter_get_var('limitPriceID','int');
					$limitPriceID = $_GET['limitPriceID'];
					$smarty->assign("limitPriceID",$limitPriceID);
					
					$smarty->assign("edit",$itemID);
										
					$smarty->assign("bookmarkType","billing");
					$smarty->display("tpls:vps.tpl");
					break;
					
					
					
				case "definedBillingPlans":
					
					$billing = new Billing($db);
					if (isset($_SESSION['definedBillingPlans'])) {						
						$definedPlans = $_SESSION['definedBillingPlans'];						
						$smarty->assign("problems",$_SESSION['problems']);
						unset($_SESSION['definedBillingPlans']);
						unset($_SESSION['problems']);
					} else {
						$xnyo->filter_get_var('customerID','int');
						$customerID = $_GET['customerID'];
					
						
						$definedPlans = $billing->getDefinedPlans($customerID);
						$vps2voc = new VPS2VOC($db);
						$customerDetails = $vps2voc->getCustomerDetails($customerID);
						$definedPlans[0]['customerName'] = $customerDetails['name'];
					}		
					
					
					$curentCurrency = $billing->getCurrencyDetails($_GET['currencyID']);
					$smarty->assign("curentCurrency",$curentCurrency);
																					
					$smarty->assign("definedPlans",$definedPlans);
					$smarty->assign("vpsAction","editItem");	
					$smarty->assign("bookmarkType","definedBillingPlan");
					$smarty->display("tpls:vps.tpl");
					break;	
					
					
					
				case "discounts":					
					/*old version
				    $company = new Company($db);
				    $customerDetails  = $company->getCompanyDetails($_GET['customerID']);
				    */
				    $Billing = new Billing($db);
				    $Invoice = new Invoice($db);
				    
				    $vps2voc = new VPS2VOC($db);
				    $customerDetails  = $vps2voc->getCustomerDetails($_GET['customerID']);
					
					//$db->select_db(DB_NAME);
					$query = "SELECT discount, TIMESTAMPDIFF(MONTH, '".$customerDetails['trial_end_date']."', CURDATE()) as time_with_us FROM ".TB_VPS_CUSTOMER." WHERE customer_id= ".$_GET['customerID'];
					$db->query($query);
					$data = $db->fetch(0);
					$customerDetails['discount'] = $data->discount;
					$customerDetails['time_with_us'] = $data->time_with_us; 
					$customerDetails['trial_end_date'] = str_replace('-', '.', $customerDetails['trial_end_date']);
					 
					$billingPlan = $Billing->getCustomerPlan($_GET['customerID']);
					$allInvoices = $Invoice->getAllInvoicesList($_GET['customerID']);
				    
				    $billingPlan['total_charge'] = 0;
				    	foreach ($allInvoices as $inv)
				    		$billingPlan['total_charge'] += (float)$inv['paid'];
				    		 
				    $customerDetails['billingPlan'] = $billingPlan;
				     
					$smarty->assign("customerDetails",$customerDetails);
					$smarty->assign("edit","showEdit");										
					$smarty->assign("bookmarkType", $itemID);
					$smarty->display("tpls:vps.tpl");
					break;
					
					
					
				case "customer":				
					$customerID = $_GET['customerID'];	
					
					$VPSUser = new VPSUser($db);
					$billing = new Billing($db);
					$invoice = new Invoice($db);					
					
					$customerList = $VPSUser->getCustomerList();
					
					$billingPlanList = array();
					for ($i=0; $i<count($customerList); $i++) {
						if ($customerID == $customerList[$i]['id']) {
							$customerDetails = $customerList[$i];
							$billingPlan = $billing->getCustomerPlan($customerDetails['id']);
							
							$lastInvoice = $invoice->getLastInvoice($customerID);						
							$customerDetails['dueDate'] = ($lastInvoice['suspensionDate'] != null && $lastInvoice['suspensionDate'] == 'DUE') ? $lastInvoice['suspensionDate'] : "--";														
						}											 																		
					}
					$availableBillingPlans = $billing->getAvailablePlans();
					
					switch ($_GET['what2edit']) {
						case "balance":
							if (isset($_SESSION['balance'])) {
								$addToBalance = $_SESSION['balance'];
								$operation = $_SESSION['operation'];
								unset($_SESSION['balance']);
								unset($_SESSION['operation']);
								$problem['balance'] = true;
								$smarty->assign('problem',$problem);
							} else {
								$addToBalance = '0.00';
								$operation = '+';
							}
							$smarty->assign('addToBalance',$addToBalance);
							$smarty->assign('operation',$operation);
							break;
						case "status":							
							if ($customerDetails['status'] == "off") {
								if (isset($_SESSION['dayShift'])) {
									$dayShift = $_SESSION['dayShift'];
									unset($_SESSION['problem']);
									unset($_SESSION['dayShift']);
									 
									$problem['dayShift'] = true;
									$smarty->assign('problem',$problem);
								} else {
									$lastDeactivation = $VPSUser->getLastDeactivation($customerID);									
									$dayShift = ($lastDeactivation['daysLeft'] < $lastDeactivation['daysPassed']) ? $lastDeactivation['daysLeft'] : $lastDeactivation['daysPassed']; 	
								}																						
								$smarty->assign('dayShift',$dayShift);								
							}							
							break;
					}
					
					$curentCurrency = $billing->getCurrencyByCustomer($customerID);
					
					$smarty->assign('curentCurrency',$curentCurrency);
					$smarty->assign('customer',$customerDetails);
					$smarty->assign('billingPlan',$billingPlan);
					$smarty->assign('availableBillingPlans',$availableBillingPlans);					
					$smarty->assign('action',$_GET['vpsAction']);
					$smarty->assign('what2edit',$_GET['what2edit']);
					
					$smarty->assign("bookmarkType","customers");
					$smarty->display("tpls:vps.tpl");
					break;
						
				case 'modules':
					//here we can edit status of active modules for customer 
					$customerID = $_GET['customerID'];
					$moduleID = $_GET['module']; 
					if (is_null($moduleID)) {
						header ('Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID='.$customerID);
						break;
					}
					$billing = new Billing($db);
					
					
					
					$modules = $billing->getPurchasedModule($customerID,$moduleID,'today&future');
					if ($_GET['status'] == 'remove_plan') {
						$planID = $_GET['plan'];
						$moduleForRemove = null;
						foreach ($modules as $module) {
							if ($module['id'] == $planID) {
								$moduleForRemove = $module;
								break;
							}
						}
						if (is_null($moduleForRemove)) {
							header ('Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID='.$customerID);
							break;
						}
						$smarty->assign('module',$moduleForRemove);
						$smarty->assign('planID',$planID);
					} else {
						$smarty->assign('module',$modules);
						$smarty->assign('moduleID',$moduleID);
					}
					
					//$module = $module[0];//TODO here should be more than 1 module plan !!!
					$vps2voc = new VPS2VOC($db);
					$customer = $vps2voc->getCustomerDetails($customerID);
					$smarty->assign('bookmarkType','areYouSure');
					$smarty->assign('itemID',$itemID);
					$smarty->assign('customer',$customer);
					$smarty->assign('customerID',$customerID);
					//$smarty->assign('module',$module);
					$smarty->assign('moduleAction',$_GET['status']);
					$smarty->display("tpls:vps.tpl");
					break;		
						
				case "other":
					//redirect from somewhere -> then take config from user 
					if (isset($_SESSION['config'])) {						
						$config = $_SESSION['config'];
						$smarty->assign("problems",$_SESSION['problems']);
						
						unset($_SESSION['config']);
						unset($_SESSION['problems']);
						
						//no redirect -> then take config from DB	
					} else {						
						$config = loadConfig($db);				
					}					
					$smarty->assign("edit","yes");										
					$smarty->assign("config",$config);
					$smarty->assign("bookmarkType",$itemID);
					$smarty->display("tpls:vps.tpl");
					break;				
			}			
			break;
			
			
			
			
		case "editItem":
			
			$itemID=$_GET['itemID'];
			
			switch ($itemID) {
				case "moduleBillingPlans":
					$billing = new Billing($db);
					
					
					$curentCurrency = $billing->getCurrencyDetails($_GET['currencyID']);
					$smarty->assign("curentCurrency",$curentCurrency);
					//$smarty->assign("currency",$curentCurrency);
					$smarty->assign("currencyID",$curentCurrency['id']);
							
					//getting billing plans for modules
					$moduleBillingPlans= $billing->getModuleBillingPlans(null,$curentCurrency['id']);
					$smarty->assign("moduleBillingPlans",$moduleBillingPlans);
					
					//distinct months count and user count
					$months = $billing->getDistinctMonths();
					$sources = $billing->getDistinctSource();
					$smarty->assign("months",$months);
					$smarty->assign("monthsCount",count($months));	
								
					//click on Billing Plan ID		
									
					$editBillingPlanID = $_GET['id'];
					
					$smarty->assign("editBillingPlanID",$editBillingPlanID);
					
					$modules=$billing->getModules();					
					$smarty->assign("modules",$modules);					
					
					$smarty->assign("edit",$itemID);										
					$smarty->assign("bookmarkType","billing");
					
					if ($_GET['Save'])
					{						
						//validation
						if (!strpos($_GET['newPrice'],'.')) {
							$_GET['newPrice'] .= ".00";
						}											
						$problem['price'] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_GET['newPrice'],$matches)) ? false : true);
						$newPrice = ($matches[1]) ? $matches[1] : $_GET['newPrice'];
	
						if (!$problem['price']) 
						{
							if (!empty($_GET['id'])) {
								$moduleBillingPlan=$billing->getModuleBillingPlans($_GET['id'], $curentCurrency['id']);
								$from =$moduleBillingPlan[0]; 
								$to = $from;						
								$to['price'] = $newPrice;							
							//one time setup charge edit 	
							} 											
							$smarty->assign("from",$from);										
							$smarty->assign("to",$to);
							
							$smarty->assign("itemCount",count($to));
						
							$smarty->assign("itemID",$itemID);
							$smarty->assign("bookmarkType","areYouSure");
							$smarty->display("tpls:vps.tpl");												
													
						} else {
							//monthly fee edit
							if (!empty($billingPlanID)) {
								header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=availableBillingPlans&id='.$billingPlanID);							
							} 
						}																	
					}
					
					$smarty->display("tpls:vps.tpl");
					break;			
				
				case "availableBillingPlans":
					$xnyo->filter_get_var('billingID','int');
					$xnyo->filter_get_var('bplimit','int');		
					$billingPlanID = $_GET['billingID'];
					$bplimit = $_GET['bplimit'];
					
					$currencyID = $_GET['currency'];
					
					$xnyo->filter_get_var('newPrice','text');					
					
					//validation
					if (!strpos($_GET['newPrice'],'.')) {
						$_GET['newPrice'] .= ".00";
					}							
					$problem['price'] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_GET['newPrice'],$matches)) ? false : true);
					$newPrice = ($matches[1]) ? $matches[1] : $_GET['newPrice'];

					if (!$problem['price']) {																		
						
						$billing = new Billing($db);
						
						$currentCurrency = $billing->getCurrencyDetails($currencyID);
						
						$smarty->assign("currentCurrency",$currentCurrency);
						
						$b2cID = $_GET['b2cID'];
						$smarty->assign("b2cID",$b2cID);
												
						//monthly fee edit
						if (!empty($billingPlanID)) {
							
							$from[] = $billing->getBillingPlanDetails($billingPlanID);
							$to = $from;						
							$to[0]['price'] = $newPrice;													
						//one time setup charge edit 	
						
						} elseif (!empty($bplimit)) {
							
							$availablePlans = $billing->getAvailablePlans();
							
							$from = $billing->getBilling2Currency($currencyID,$bplimit);
							//var_dump($from);

							/*for ($i=0;$i<count($availablePlans);$i++) {
								if ($availablePlans[$i]['bplimit'] == $bplimit ) {
									$from[] = $availablePlans[$i];
									$availablePlans[$i]['one_time_charge'] = $newPrice;
									$to[] = $availablePlans[$i];															
								}			
							}*/
							
							$len = count($from);
							$to = $from;
							for ( $i=0; $i < $len; $i++)
							{
								$to[$i]['one_time_charge'] = $newPrice;
							}
							
							//var_dump($from);
						}
						
						$smarty->assign("bpLimit",$bplimit);
						$smarty->assign("currencyID",$currencyID);
						$smarty->assign("newPrice",$newPrice);
													
						$smarty->assign("from",$from);										
						$smarty->assign("to",$to);
						
						$smarty->assign("itemCount",count($to));
					
						$smarty->assign("itemID",$itemID);
						$smarty->assign("bookmarkType","areYouSure");
						$smarty->display("tpls:vps.tpl");												
												
					} else {
						//monthly fee edit
						if (!empty($billingPlanID)) {
							header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=availableBillingPlans&id='.$billingPlanID);
						//one time setup charge edit
						} elseif (!empty($bplimit)) {
							header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=availableBillingPlans&oneTimeCharge4bplimit='.$bplimit);
						}
					}					
					break;
			
			
			
				case "limits":		
					
					$xnyo->filter_get_var('newDefaultLimit','text');
					$xnyo->filter_get_var('newIncreaseCost','text');
					$xnyo->filter_get_var('limitPriceID','int');					
					$billing = new Billing($db);
					
					$limitPriceID = $_GET['limitPriceID'];	

					$curentCurrency = $billing->getCurrencyDetails($_GET['currencyID']);
					$smarty->assign("curentCurrency",$curentCurrency);
					$smarty->assign("currencyID",$curentCurrency['id']);
					
					if (isset($_GET['newDefaultLimit'])) {
						//	validation						
						$problem['newDefaultLimit'] = ((preg_match('/^[0]*(\d+)$/',$_GET['newDefaultLimit'],$matches)) ? false : true);
						$newDefaultLimit = ($matches[1]) ? $matches[1] : $_GET['newDefaultLimit'];
						
						if (!$problem['newDefaultLimit']) {																										
													
							$extraLimits = $billing->getAvailableExtraLimits();
							for ($i=0;$i<count($extraLimits);$i++) {
								if ($extraLimits[$i]['limit_price_id'] == $limitPriceID ) {
									$from[] = $extraLimits[$i];
									$extraLimits[$i]['default_limit'] = $newDefaultLimit;
									$to[] = $extraLimits[$i];															
								}
							}							
						} else {
							$errorRedirectURL = 'admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=defaultLimit&limitPriceID='.$limitPriceID;							
						}																	
					} else {
						//	validation
						if (!strpos($_GET['newIncreaseCost'],'.')) {
							$_GET['newIncreaseCost'] .= ".00";
						}	
						$problem['newIncreaseCost'] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_GET['newIncreaseCost'],$matches)) ? false : true);
						$newIncreaseCost = ($matches[1]) ? $matches[1] : $_GET['newIncreaseCost'];
						
						if (!$problem['newIncreaseCost']) {																										
							$billing = new Billing($db);						
							$extraLimits = $billing->getAvailableExtraLimits();
							for ($i=0;$i<count($extraLimits);$i++) {
								if ($extraLimits[$i]['limit_price_id'] == $limitPriceID ) {
									$from[] = $extraLimits[$i];
									$extraLimits[$i]['increase_cost'] = $newIncreaseCost;
									$to[] = $extraLimits[$i];															
								}
							}							
						} else {
							$errorRedirectURL = 'admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=increaseCost&limitPriceID='.$limitPriceID;
						}		
					}															
													
					//redirect when error																				
					if ($problem['newDefaultLimit'] || $problem['newIncreaseCost']) {						
						header ('Location: '.$errorRedirectURL);						
					} else {						
						$smarty->assign("from",$from);										
						$smarty->assign("to",$to);						
						$smarty->assign("itemCount",count($to));						
						$smarty->assign("itemID",$itemID);
						$smarty->assign("bookmarkType","areYouSure");
						$smarty->display("tpls:vps.tpl");	
					}										
					break;
					
				
					
				case "definedBillingPlans":
					$xnyo->filter_get_var('customerID','int');
					$xnyo->filter_get_var('bplimit','int');
					$xnyo->filter_get_var('monthsCount','int');
					$xnyo->filter_get_var('oneTimeCharge','text');
					$xnyo->filter_get_var('price','text');
					$xnyo->filter_get_var('type','text');
					$xnyo->filter_get_var('MSDSDefaultLimit','text');
					$xnyo->filter_get_var('MSDSIncreaseCost','text');
					$xnyo->filter_get_var('memoryDefaultLimit','text');
					$xnyo->filter_get_var('memoryIncreaseCost','text');
					
					//echo "GET:" . count($_GET);
					
					//echo "end get";
					//validation
					$intInput = array('customerID','bplimit','monthsCount','MSDSDefaultLimit','memoryDefaultLimit','oneTimeCharge','price','MSDSIncreaseCost','memoryIncreaseCost');
					//$floatInput = array('oneTimeCharge','price','MSDSIncreaseCost','memoryIncreaseCost');
					foreach ($intInput as $value) {
						//$problem[$value] = ((preg_match('/^[0]*(\d+)$/',$_GET[$value],$matches)) ? false : true);
						if(is_numeric($_GET[$value]))
						{
							$problem[$value] = false;
							$newDefinedBillingPlan[$value] = $_GET[$value];
						}
						else
						{
							$problem[$value] = true;
						}
					}
					/*foreach ($floatInput as $value) {
						//$problem[$value] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_GET[$value],$matches)) ? false : true);
						if(is_numeric($_GET[$value]))
						{
							$problem[$value] = false;
							$newDefinedBillingPlan[$value] =  $_GET[$value];
						}
						else
						{
							$problem[$value] = true;
						}
						//$newDefinedBillingPlan[$value] = ($matches[1]) ? $matches[1] : $_GET[$value];	
					}*/
					
					
					$totalProblem = false;
					foreach ($problem as $problemWithInput) {
						if ($problemWithInput) {
							$totalProblem = true;
							break;
						}
					}
					
					$billing = new Billing($db);
					$from = $billing->getDefinedPlans($newDefinedBillingPlan['customerID']);
					
					$to = $from;
					$to[0]['bplimit'] = $newDefinedBillingPlan['bplimit'];
					$to[0]['months_count'] = $newDefinedBillingPlan['monthsCount'];
					$to[0]['limits']['MSDS']['default_limit'] = $newDefinedBillingPlan['MSDSDefaultLimit'];
					$to[0]['limits']['memory']['default_limit'] = $newDefinedBillingPlan['memoryDefaultLimit'];
					$to[0]['one_time_charge'] = $newDefinedBillingPlan['oneTimeCharge'];
					$to[0]['price'] = $newDefinedBillingPlan['price'];				 
					$to[0]['limits']['MSDS']['increase_cost'] = $newDefinedBillingPlan['MSDSIncreaseCost'];
					$to[0]['limits']['memory']['increase_cost'] = $newDefinedBillingPlan['memoryIncreaseCost'];
					
					//var_dump($from);
					//var_dump($to);
					
						
					if (!$totalProblem) {		

						$curentCurrency = $billing->getCurrencyDetails($_GET['currencyID']);
						$smarty->assign("curentCurrency",$curentCurrency);
						
						$smarty->assign("from",$from);										
						$smarty->assign("to",$to);						
						$smarty->assign("itemCount",count($to));						
						$smarty->assign("itemID",$itemID);
						$smarty->assign("bookmarkType","areYouSure");
						$smarty->display("tpls:vps.tpl");	
					} else {
						$_SESSION['problems'] = $problem;
						$_SESSION['definedBillingPlans'] = $to;
						header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=definedBillingPlans&customerID='.$_GET['customerID']);
					}										
					break;
					
					
					
				case "discounts":				
					$xnyo->filter_post_var('customerID','text');
					$xnyo->filter_post_var('discount','text');
					$newDiscount = $_POST['discount'];
					
					if (!strpos($newDiscount,'.')) {
						$newDiscount .= ".00";
					}							
					$problem = ((preg_match('/^[0]*(\d+\.\d{2})$/',$newDiscount,$matches)) ? false : true);
					$newDiscount = ($matches[1]) ? $matches[1] : $newDiscount;
					
					if (!$problem) {
						
					$VPSAdmin = new VPSUser($db);
				    $Billing = new Billing($db);
				    $Invoice = new Invoice($db);
				    
				    $customerList = $VPSAdmin->getCustomerList();
				    $customerDetails = array();
				    $from = array();
				    $to = array();
				    
				    for ($i=0; $i<count($customerList); $i++)    
				       if ((int)$customerList[$i]['id'] == (int)$_POST['customerID']) {
				    		
					    	$billingPlan = $Billing->getCustomerPlan($customerList[$i]['id']);
					    	$allInvoices = $Invoice->getAllInvoicesList($customerList[$i]['id']);
				    	
					    	$billingPlan['total_charge'] = 0;
				    		foreach ($allInvoices as $inv)
				    			$billingPlan['total_charge'] += (float)$inv['paid'];
				    			
				    		$customerDetails = $customerList[$i];
				    		$customerDetails['billingPlan'] = $billingPlan;
				    		$from = $customerDetails;
				    		$customerDetails['discount'] = $newDiscount;
				    	 	$to = $customerDetails;  
				    	}
				    
				    $smarty->assign("from",$from);
				    $smarty->assign("to",$to);
					$smarty->assign("edit","editItem");										
					$smarty->assign("bookmarkType", $itemID);
					$smarty->display("tpls:vps.tpl");
					
					} else {						
						header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=discounts&customerID='.$_POST['customerID']);						
					}
					break;	
				
				
				
				case "customer":
					$xnyo->filter_post_var('customerID','int');
					$xnyo->filter_post_var('what2edit','text');
					$customerID = $_POST['customerID'];
					$what2edit = $_POST['what2edit'];
					
					switch ($what2edit) {
						case "billing":
							$xnyo->filter_post_var('newBillingPlan','int');
							$xnyo->filter_post_var('type','text');
							
							$newBillingPlan = $_POST['newBillingPlan'];
							$type = ($_POST['type'] == 'bpEnd' || $_POST['type'] == 'asap') ? $_POST['type'] : false;
							$problem = ($type) ? false : true;
							
							//check for changes
							$billing = new Billing($db);
							$currentBillingPlan = $billing->getCustomerPlan($customerID);							
							if ($newBillingPlan == $currentBillingPlan['billingID']) {
								$nothing2do = true;
							} else {
								$nothing2do = false;
							}
							
							if ($problem) {
								//redirect
							}
							if (!$nothing2do) {
								$newBillingDetails = $billing->getBillingPlanDetails($newBillingPlan);
								
								//manage applyWhen																
								switch ($type) {
									case 'bpEnd':
										$invoice = new Invoice($db);
										$currentInvoice = $invoice->getCurrentInvoice($customerID);										//											
										if (!$currentInvoice) {
											//	*Try to take trial period end. Try do not use first invoice case 
											$firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
											if ($firstInvoice) {
												$dateWhenNewPlanWillBeImplemented = $firstInvoice['periodStartDate'];
											} else {
												//	Trial period end
												$vps2voc = new VPS2VOC($db);
												$customerDetails = $vps2voc->getCustomerDetails($customerID);												
												//$dateWhenNewPlanWillBeImplemented = $customerDetails['period_end_date'];
												$dateWhenNewPlanWillBeImplemented = $customerDetails['trial_end_date'];
											}																															
										} else {
											$dateWhenNewPlanWillBeImplemented = $currentInvoice['periodEndDate'];
										}																											
										break;
									case 'asap':										
										$dateWhenNewPlanWillBeImplemented = "ASAP";
										break;
									default:
										//redirect
									break;
								}
								$smarty->assign("customerID",$customerID);
								
								$smarty->assign("what2edit",$what2edit);
								
								$smarty->assign("applyWhen",$type);																									
								$smarty->assign("dateWhenNewPlanWillBeImplemented",$dateWhenNewPlanWillBeImplemented);
								
								$from = $currentBillingPlan;
								$to = $newBillingDetails;																																																								
															
								$smarty->assign("from",$from);										
								$smarty->assign("to",$to);
								$smarty->assign("itemID",$itemID);
								$smarty->assign("bookmarkType","areYouSure");
								$smarty->display("tpls:vps.tpl");	
							} else {
								header ('Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID='.$customerID);
							}
							break;
							
						case "balance":
							$xnyo->filter_post_var('operation','text');
							$xnyo->filter_post_var('balance','text');
														
							$operation = ($_POST['operation'] == '+' || $_POST['operation'] == '-') ? $_POST['operation'] : false;
							$balance = $_POST['balance']; 
							if (!strpos($balance,'.')) {
								$balance .= ".00";
							}							
							$problem = ((preg_match('/^[0]*(\d+\.\d{2})$/',$balance,$matches)) ? false : true);
							$balance = ($matches[1]) ? $matches[1] : $balance;
							
							if ($problem) {								
								$_SESSION['balance'] = $balance;
								$_SESSION['operation'] = $operation;
								header('Location: admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=balance&customerID='.$customerID);
							} else {
								if ($balance == "0.00") {
									header('Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID='.$customerID);
								} else {									
									$smarty->assign("customerID",$customerID);
									$smarty->assign("operation",$operation);
									$smarty->assign("what2edit",$what2edit);
									$smarty->assign("balance",$balance);
									$smarty->assign("itemID",$itemID);
									$smarty->assign("bookmarkType","areYouSure");
									$smarty->display("tpls:vps.tpl");	
								}																
							}																								
							
							break;
							
						case "status":						
							$xnyo->filter_post_var('active','text');																			
							$newStatus = $_POST['active'];																					
												
							$VPSUser = new VPSUser($db);
							$customerList = $VPSUser->getCustomerList();
					
							$billingPlanList = array();
							foreach ($customerList as $customer) {
								if ($customerID == $customer['id']) {
									$customerDetails = $customer;																							
								}											
							}
//							for ($i=0; $i<count($customerList); $i++) {
//								if ($customerID == $customerList[$i]['id']) {
//									$customerDetails = $customerList[$i];																							
//								}											 																		
//							}
							
							if ($customerDetails['status'] == 'on' && $newStatus == 'on') {
								$nothing2do = true;
							} elseif ($customerDetails['status'] == 'off' && !$newStatus) {
								$nothing2do = true;
							} else {
								$nothing2do = false;
								if ($newStatus == "on") {
									$xnyo->filter_post_var('dayShift','text');
									$problem = ((preg_match('/^[0]*(\d+)$/',$_POST['dayShift'],$matches)) ? false : true);
									$dayShift = ($matches[1]) ? $matches[1] : $_POST['dayShift'];	
								}
							}
							
							if (!$nothing2do && !$problem) {
								$smarty->assign("customerID",$customerID);
								$activate = ($newStatus == 'on') ? "Activate" : "Deactivate";
								$smarty->assign("activate",$activate);
								
								$smarty->assign("what2edit",$what2edit);
								$smarty->assign("dayShift",$dayShift);
								$smarty->assign("itemID",$itemID);
								$smarty->assign("bookmarkType","areYouSure");
								$smarty->display("tpls:vps.tpl");
							} else {
								if ($problem) {
									$_SESSION['problem'] = true;
									$_SESSION['dayShift'] = $dayShift;
									header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=status&customerID='.$customerID);									
								} else {
									header ('Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID='.$customerID);	
								}								
							}
							
							break;
					}									
					break;
					
					
					
				case "notRegisteredCustomer":
					$xnyo->filter_get_var('customerID','int');
					$xnyo->filter_get_var('what2edit','text');
					$customerID = $_GET['customerID'];
					$what2edit = $_GET['what2edit'];
					
					switch ($what2edit) {
						case 'trialPeriodEnd':
							break;
							
						case 'register':
							$vps2voc = new VPS2VOC($db);
							$customerDetails = $vps2voc->getCustomerDetails($customerID, $getWithNotRegistered = true);
							
							$smarty->assign("what2edit",$what2edit);
							$smarty->assign("customer",$customerDetails);
							$smarty->assign("itemID",$itemID);
							$smarty->assign("bookmarkType","areYouSure");
							$smarty->display("tpls:vps.tpl");
							break;
					}					
					break;
					
					
					
				case "other":
					break;
					
					
					
			}		
			break;
			
			
			
			
		case "confirmEdit":
			$xnyo->filter_get_var('itemID','text');		
			$itemID=$_GET['itemID'];
	
			switch ($itemID) {				
				case "availableBillingPlans":																				
					$xnyo->filter_post_var('itemCount','int');
					$billingPlanCount = $_POST['itemCount'];

					
					$billing = new Billing($db);
					
					$currencyID = $_POST['b2cID'];
					
					$bpLimit = $_POST['bpLimit'];
					if($bpLimit)
					{
						$currencyID = $_POST['currencyID'];
						$newPrice = $_POST['newPrice'];
						
						$billing->setOneTimeChargeToBilling2Currency($currencyID,$bpLimit,$newPrice);
					}
					else
					{
						//$db->select_db(DB_NAME);
						for ($i=0;$i<$billingPlanCount;$i++){
							$xnyo->filter_post_var('billingID_'.$i,'int');
							$xnyo->filter_post_var('one_time_charge_'.$i,'text');						
	
							$xnyo->filter_post_var('price_'.$i,'text');						
							
							$newBillingPlan['billingID'] = mysql_escape_string($_POST['billingID_'.$i]);
							$newBillingPlan['oneTimeCharge'] = mysql_escape_string($_POST['one_time_charge_'.$i]);						
							$newBillingPlan['price'] = mysql_escape_string($_POST['price_'.$i]);
							$newBillingPlan['defined'] = 0;
							$newBillingPlan['billing2currency_id'] = $currencyID;
													
							$billing->updateBillingPlan($newBillingPlan);										
						}
					}
					
					header ("Location: admin.php?action=vps&vpsAction=browseCategory&itemID=billing&currencyID=$currencyID");															
					break;
					
				case "moduleBillingPlans":
					$billing= new Billing($db);
					$moduleBillingPlanId = mysql_escape_string($_POST['billingID']);
					$newPrice = mysql_escape_string($_POST['price']);
					$currencyID = mysql_escape_string($_POST['currencyID']);
					
					$billing->updatePriceForModule($moduleBillingPlanId, $newPrice, $currencyID);
					
					header ("Location: admin.php?action=vps&vpsAction=browseCategory&itemID=billing&currency=$currencyID");
					break;
				
				case "limits":
					$xnyo->filter_post_var('itemCount','int');
					$limitCount = $_POST['itemCount'];
					$currencyID = $_POST['currencyID'];
					
					
					
					//$db->select_db(DB_NAME);
					for ($i=0;$i<$limitCount;$i++){
						$xnyo->filter_post_var('limit_price_id_'.$i,'int');
						$xnyo->filter_post_var('default_limit_'.$i,'text');						
						$xnyo->filter_post_var('increase_cost_'.$i,'text');						
						
						$limitPriceID = mysql_escape_string($_POST['limit_price_id_'.$i]);
						$defaultLimit = mysql_escape_string($_POST['default_limit_'.$i]);						
						$increaseCost = mysql_escape_string($_POST['increase_cost_'.$i]);
						
						$billing = new Billing($db);
						$billing->setPriceToLimit($limitPriceID,$currencyID,$increaseCost);
						
						
						/*$query = "UPDATE ".TB_VPS_LIMIT_PRICE." " .
								"SET default_limit = '".$defaultLimit."', " .									
									"increase_cost = '".$increaseCost."' " .
								"WHERE limit_price_id = ".$limitPriceID;
						
						$db->query($query);*/
					}
					echo "<br/><a href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing&currencyID=$currencyID'>next</a>";
					//header ("Location: admin.php?action=vps&vpsAction=browseCategory&itemID=billing&currencyID=$currencyID");
					break;
					
					
					
				case "definedBillingPlans":
					
					
					
					$xnyo->filter_post_var('billingID','int');
					$xnyo->filter_post_var('customerID','int');
					$xnyo->filter_post_var('bplimit','int');
					$xnyo->filter_post_var('monthsCount','int');
					$xnyo->filter_post_var('oneTimeCharge','text');
					$xnyo->filter_post_var('price','text');
					$xnyo->filter_post_var('type','text');
					$xnyo->filter_post_var('MSDSDefaultLimit','text');
					$xnyo->filter_post_var('MSDSIncreaseCost','text');
					$xnyo->filter_post_var('memoryDefaultLimit','text');
					$xnyo->filter_post_var('memoryIncreaseCost','text');
					
					foreach ($_POST as $varName=>$value) {
						$newDefinedBillingPlan[$varName] = mysql_escape_string($_POST[$varName]);
					}
					$newDefinedBillingPlan['defined'] = 1;
					
					
					
					$billing = new Billing($db);
					
					$currencyID = $_POST['curentCurrency'];
					
					$newDefinedBillingPlan['currencyID'] = $currencyID;
					
					
					$billing->updateBillingPlan($newDefinedBillingPlan,$currencyID);								
					
					//echo "<a href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'>next</a>";
					//exit;
					
					header ('Location: admin.php?action=vps&vpsAction=browseCategory&itemID=billing');
					break;
				
				
					
				case "discounts":				
					$xnyo->filter_post_var('discount','text');
					$xnyo->filter_post_var('customerID','text');
					$newDiscount = mysql_escape_string($_POST['discount']);
					
					//$db->select_db(DB_NAME);
					$query = "UPDATE ".TB_VPS_CUSTOMER." " .
								"SET discount = '".$newDiscount."' WHERE customer_id = ".$_POST['customerID'];
					
					$db->query($query);
					header ('Location: admin.php?action=vps&vpsAction=browseCategory&itemID=discounts');	
				    break;
				 
				 
				    
				case "customer":
					$xnyo->filter_post_var('customerID','int');
					$xnyo->filter_post_var('what2edit','text');
					$customerID = $_POST['customerID'];
					$what2edit = $_POST['what2edit'];
					
					switch ($what2edit) {
						case "billing":
							$xnyo->filter_post_var('billingID','int');
							$xnyo->filter_post_var('applyWhen', 'text');
							
							$newBillingPlan = $_POST['billingID'];
							if ($_POST['applyWhen'] == 'bpEnd' || $_POST['applyWhen'] == 'asap') {
								$applyWhen = $_POST['applyWhen'];
								//not expected variable. fraud?	
							} else {
								($_POST['applyWhen']);
								break;
							}		
							
							$billing = new Billing($db);
							$currentBillingPlan = $billing->getCustomerPlan($customerID);							
							if (!$currentBillingPlan) {								
								//	no current billing plan, probably trial period
								$billing->setCustomerPlan($customerID, $newBillingPlan);
							
								$vps2voc = new VPS2VOC($db);
								$customerDetails = $vps2voc->getCustomerDetails($customerID);					
							
								$invoice = new Invoice($db);							
								$invoice->createInvoiceForBilling($customerID, $customerDetails['trial_end_date'], $newBillingPlan);							
							} else {														
								$result = $billing->setScheduledPlan($customerID, $newBillingPlan, $applyWhen);
							}																																																								
							
							header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);		
							break;
							
						case "balance":
							$xnyo->filter_post_var('operation','text');
							$xnyo->filter_post_var('balance', 'text');
							
							$operation = $_POST['operation'];
							$balance = $_POST['balance'];
							
							$invoice = new Invoice($db);
							
							$invoice->manualBalanceChange($customerID,$operation,$balance);
							
							header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);											
							break;
							
						case "status":
							$xnyo->filter_post_var('activate','text');
							$xnyo->filter_post_var('dayShift','int');
							
							$dayShift = $_POST['dayShift'];
							
							$VPSUser = new VPSUser($db);
							if ($_POST['activate'] == 'Deactivate') {								
								$VPSUser->deactivateCustomer($customerID);
							} elseif ($_POST['activate'] == 'Activate') {																					
								$VPSUser->activateCustomer($customerID, $dayShift);
							}
							header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);														
							break; 
					}
					break;
					
					
				
				case "notRegisteredCustomer":	
					$xnyo->filter_post_var('customerID','int');
					$xnyo->filter_post_var('what2edit','text');
					$customerID = $_POST['customerID'];
					$what2edit = $_POST['what2edit'];

					switch ($what2edit) {
						case 'trialPeriodEnd':
							break;
							
						case 'register':			
								$vps2voc = new VPS2VOC($db);
								if (false !== ($userID = $vps2voc->getCompanyLevelUserByCompanyID($customerID, $getWithNotRegistered=true))) {								
									$vpsUser = new VPSUser($db);							
									$vpsUser->copyUserToVPS($userID);									
								
									$billing = new Billing($db);
									$billing->addCustomerPlan($customerID);
									
									header ('Location: admin.php?action=vps&vpsAction=browseCategory&itemID=customers');									
								} else {
									echo "No company level user =(<br>";
								}
								
							break;
					}					
					break;
				
				case "modules":
					$billing = new Billing($db);
					$customerID = $_POST['customerID'];
					if ($_POST['moduleAction'] == 'remove_plan') {
						$moduleID = $_POST['modulePlanID'];
						$billing->removeModuleBillingPlan($customerID,$moduleID);
					} elseif ($_POST['moduleAction'] == 'remove_all') {
						$moduleID = $_POST['moduleID'];
						$modules = $billing->getPurchasedModule($customerID,$moduleID,'today&future');
						foreach ($modules as $module) {
							$billing->removeModuleBillingPlan($customerID,$module['id']);
						}
					}
					header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);		
					break;	   	
					   	
				case "other":
					$xnyo->filter_post_var('paypal_merchant_email','text');
					$xnyo->filter_post_var('paypal_merchant_id','text');
					$xnyo->filter_post_var('trial_period','text');
					$xnyo->filter_post_var('vps_registration_period','text');
					$xnyo->filter_post_var('invoice_generation_period','text');
					$xnyo->filter_post_var('limit_suspension_period','text');
					$xnyo->filter_post_var('invoice_generation_email_subject','text');
					$xnyo->filter_post_var('invoice_generation_email_message','text');
					$xnyo->filter_post_var('first_notification_period','text');
					$xnyo->filter_post_var('first_notification_email_subject','text');
					$xnyo->filter_post_var('first_notification_email_message','text');
					$xnyo->filter_post_var('second_notification_period','text');
					$xnyo->filter_post_var('second_notification_email_subject','text');
					$xnyo->filter_post_var('second_notification_email_message','text');
					$xnyo->filter_post_var('deacivate_email_subject','text');
					$xnyo->filter_post_var('deacivate_email_message','text');					
					$xnyo->filter_post_var('change_customer_bp_email_subject','text');
					$xnyo->filter_post_var('change_customer_bp_email_message','text');
					$xnyo->filter_post_var('schedule_bp_email_subject','text');
					$xnyo->filter_post_var('schedule_bp_email_message','text');
					$xnyo->filter_post_var('change_customer_tariffs_email_subject','text');
					$xnyo->filter_post_var('change_customer_tariffs_email_message','text');
					$xnyo->filter_post_var('change_customer_limit_email_subject','text');
					$xnyo->filter_post_var('change_customer_limit_email_message','text');
					$xnyo->filter_post_var('new_invoice_email_subject','text');
					$xnyo->filter_post_var('new_invoice_email_message','text');									
					
					//validation
					$digitsValues = array('trial_period','vps_registration_period','invoice_generation_period','first_notification_period','second_notification_period', 'limit_suspension_period');
					foreach($digitsValues as $digitsValue) {
						$problems[$digitsValue] = ((preg_match('/^[0]*(\d+)$/',$_POST[$digitsValue],$matches) && strlen($matches[1]) < 3) ? false : true);
						$_POST[$digitsValue] = ($matches[1]) ? $matches[1] : $_POST[$digitsValue];
					}
					$validation = new Validation($db);					
					$problems['paypal_merchant_email'] = !$validation->check_email($_POST['paypal_merchant_email']);
					
					//periods conflict check
					$conflict = "";
					$conflict .= ($_POST['vps_registration_period'] > $_POST['trial_period']) ? "<div><b>Trial period</b> should be less than <b>VPS registration period</b></div>" : "";
					$conflict .= ($_POST['invoice_generation_period'] > 30) ? "<div><b>Invoice generation period</b> should be less than <b>minimal Billing period (30)</b></div>" : "";
					$conflict .= ($_POST['first_notification_period'] > $_POST['invoice_generation_period']) ? "<div><b>First notification period</b> should be less than <b>Invoice generation period</b></div>" : "";
					$conflict .= ($_POST['second_notification_period'] > $_POST['first_notification_period']) ? "<div><b>Second notification period</b> should be less than <b>First notification period</b></div>" : "";
					if (!empty($conflict)) {
						$problems['conflict'] = $conflict;						
					} 
					//--------------------- 										
					
					$totalProblem = false;
					foreach ($problems as $problem) {
						//validation failed -> redirect
						if ($problem) {							
							$totalProblem = true;								
						}
					}
					if (!$totalProblem) {
						//$db->select_db(DB_NAME);
						foreach ($_POST as $key=>$value) {
							//converting to local variables
							$config[$key] = mysql_real_escape_string($value);																	
							// saving to DB																							
							$query = "UPDATE ".TB_VPS_CONFIG. " SET value = '".$config[$key]."' WHERE name = '".$key."'";							
							$db->query($query);						
						}						
						header ('Location: admin.php?action=vps&vpsAction=browseCategory&itemID=other');
					} else {
						$_SESSION['problems'] = $problems;
						$_SESSION['config'] = $_POST;
						header ('Location: admin.php?action=vps&vpsAction=showEdit&itemID=other');
					}						
					break;
					
					
					
			}										
			break;							
		
		
		
		
		case "showAddItem":
			$xnyo->filter_get_var('itemID','text');		
			$itemID = $_GET['itemID'];
			
			switch ($itemID) {
				case "definedBillingPlans":
					if (isset($_SESSION['newDefinedBillingPlan'])) {
						$definedPlans = $_SESSION['newDefinedBillingPlan'];
						$smarty->assign("definedPlans",$definedPlans);						
						$smarty->assign("problems",$_SESSION['problems']);
						
						unset($_SESSION['newDefinedBillingPlan']);
						unset($_SESSION['problems']);
					}
					
					$xnyo->filter_get_var('requestID','int');					
					if (isset($_GET['requestID'])) {
						$billing = new Billing($db);
						$vps2voc = new VPS2VOC($db);						
						$requestDetails = $billing->getRequest($_GET['requestID']);
						$customerDetails = $vps2voc->getCustomerDetails($requestDetails[0]['customerID']);
												
						//I hope this is tmp
						$definedPlans[0]['request_id'] = $requestDetails[0]['id'];
						$definedPlans[0]['customer_id'] = $requestDetails[0]['customerID'];
						$definedPlans[0]['customerName'] = $customerDetails['name'];
						$definedPlans[0]['bplimit'] = $requestDetails[0]['bplimit'];
						$definedPlans[0]['months_count'] = $requestDetails[0]['monthsCount'];
						$definedPlans[0]['months_count'] = $requestDetails[0]['monthsCount'];
						$definedPlans[0]['type'] = $requestDetails[0]['type'];
						$definedPlans[0]['limits']['MSDS']['default_limit'] = $requestDetails[0]['MSDSLimit'];
						$definedPlans[0]['limits']['memory']['default_limit'] = $requestDetails[0]['memoryLimit'];
						$definedPlans[0]['description'] = stripslashes($requestDetails[0]['description']);
						$definedPlans[0]['date'] = $requestDetails[0]['date'];
						$definedPlans[0]['status'] = $requestDetails[0]['status'];
						 
						$smarty->assign("definedPlans",$definedPlans); 												
						
					} else {
						$vps2voc = new VPS2VOC($db);
						//--------getting customers list----------
						//$db->select_db(DB_NAME);
						
						//getting new customers
						$query = "SELECT c.customer_id " .
								"FROM ".TB_VPS_CUSTOMER." c LEFT JOIN ".TB_VPS_DEFINED_BP_REQUEST." r ON c.customer_id = r.customer_id " .
								"WHERE (r.status IS NULL or r.status = 'processed') " .
								"AND c.billing_id IS NULL";						
						$db->query($query);	
														
						if ($db->num_rows()) {
							$numRows = $db->num_rows();
							for ($i=0; $i < $numRows; $i++) {
								$data = $db->fetch($i);
								$customer = $vps2voc->getCustomerDetails($data->customer_id);								
								$customer['status'] = "new";
								$customersList[] = $customer;				
							}
						}						
						
						//getting already existing customers with standart Billing Plans
						$query = "SELECT c.customer_id FROM ".TB_VPS_CUSTOMER." c, ".TB_VPS_BILLING." b WHERE c.billing_id = b.billing_id AND b.defined = 0";						
						$db->query($query);											
						if ($db->num_rows()) {
							$numRows = $db->num_rows();
							for ($i=0; $i < $numRows; $i++) {
								$data=$db->fetch($i);
								$customer = $vps2voc->getCustomerDetails($data->customer_id);							
								$customer['status'] = "exist";
								$customersList[] = $customer;				
							}
						}
						
						$smarty->assign("customersList",$customersList);	
					}

					$smarty->assign("vpsAction","addItem");	
					$smarty->assign("bookmarkType","definedBillingPlan");
					$smarty->display("tpls:vps.tpl");
					break;	
			}
			break;
			
		case "addItem":
			$xnyo->filter_get_var('itemID','text');		
			$itemID = $_GET['itemID'];
			
			switch ($itemID) {
				case "definedBillingPlans":
					$xnyo->filter_get_var('customerID','int');
					$xnyo->filter_get_var('bplimit','int');
					$xnyo->filter_get_var('monthsCount','int');
					$xnyo->filter_get_var('oneTimeCharge','text');
					$xnyo->filter_get_var('price','text');
					$xnyo->filter_get_var('type','text');
					$xnyo->filter_get_var('MSDSDefaultLimit','text');
					$xnyo->filter_get_var('MSDSIncreaseCost','text');
					$xnyo->filter_get_var('memoryDefaultLimit','text');
					$xnyo->filter_get_var('memoryIncreaseCost','text');
					$xnyo->filter_get_var('requestID','int');
					$xnyo->filter_get_var('applyWhen','text');
					
					//validation
					$intInput = array('customerID','bplimit','monthsCount','MSDSDefaultLimit','memoryDefaultLimit');
					$floatInput = array('oneTimeCharge','price','MSDSIncreaseCost','memoryIncreaseCost');
					foreach ($intInput as $value) {
						$problem[$value] = ((preg_match('/^[0]*(\d+)$/',$_GET[$value],$matches)) ? false : true);
						$newDefinedBillingPlan[$value] = ($matches[1]) ? $matches[1] : $_GET[$value];	
					}
					foreach ($floatInput as $value) {
						if (!strpos($_GET[$value],'.')) {
							$_GET[$value] .= ".00";
						}
						$problem[$value] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_GET[$value],$matches)) ? false : true);
						$newDefinedBillingPlan[$value] = ($matches[1]) ? $matches[1] : $_GET[$value];	
					}					
					$problem['type'] = ($_GET['type'] == 'gyant' || $_GET['type'] == 'self') ? false : true;
					$newDefinedBillingPlan['type'] = $_GET['type'];
					$problem['applyWhen'] = ($_GET['applyWhen'] == 'bpEnd' || $_GET['applyWhen'] == 'asap') ? false : true;
					$newDefinedBillingPlan['applyWhen'] = $_GET['applyWhen'];															  
					
					$totalProblem = false;
					foreach ($problem as $problemWithInput) {
						if ($problemWithInput) {
							$totalProblem = true;
							break;
						}
					}
					
					if (!$totalProblem) {
						$billing = new Billing($db);						
						
						$smarty->assign("requestID",$_GET['requestID']);
						$smarty->assign("newPlan",$newDefinedBillingPlan);
						$smarty->assign("bookmarkType","areYouSureAdd");
						$smarty->display("tpls:vps.tpl");
					} else {
						$_SESSION['problems'] = $problem;
						$_SESSION['newDefinedBillingPlan'] = $newDefinedBillingPlan;
						if (isset($_GET['requestID'])) {
							header ('Location: admin.php?action=vps&vpsAction=showAddItem&itemID=definedBillingPlans&requestID='.$_GET['requestID']);
						} else {
							header ('Location: admin.php?action=vps&vpsAction=showAddItem&itemID=definedBillingPlans');	
						}						
					}	
					break;													
			}
			break;
		
		case "confirmAdd":
			$xnyo->filter_get_var('itemID','text');		
			$itemID = $_GET['itemID'];
			
			switch ($itemID) {
				case "definedBillingPlans":					
					$xnyo->filter_post_var('customerID','int');
					$xnyo->filter_post_var('bplimit','int');
					$xnyo->filter_post_var('monthsCount','int');
					$xnyo->filter_post_var('oneTimeCharge','text');
					$xnyo->filter_post_var('price','text');
					$xnyo->filter_post_var('type','text');
					$xnyo->filter_post_var('MSDSDefaultLimit','text');
					$xnyo->filter_post_var('MSDSIncreaseCost','text');
					$xnyo->filter_post_var('memoryDefaultLimit','text');
					$xnyo->filter_post_var('memoryIncreaseCost','text');
					$xnyo->filter_post_var('requestID','int');
					$xnyo->filter_post_var('applyWhen','text');					
					
					foreach ($_POST as $varName=>$value) {
						$newDefinedBillingPlan[$varName] = $db->sqltext($_POST[$varName]);
					}					
					
					$billing = new Billing($db);
					
					//$newDefinedBillingPlan['oneTimeCharge'] = $_POST['oneTimeCharge'];
					
					//$db->beginTransaction();
					
					$billing->addDefinedBillingPlan($newDefinedBillingPlan);								
												
					header ('Location: admin.php?action=vps&vpsAction=browseCategory&itemID=billing');
					break;	
			}
			break;
			
		case "manageInvoice":														
				$xnyo->filter_get_var('invoiceAction','text');
				$invoiceAction = $_GET['invoiceAction'];
				
				switch ($invoiceAction) {
					
					case "Edit":
						$xnyo->filter_get_var('customerID','int');
						$customerID = $_GET['customerID'];
						
						$invoice = new Invoice($db);
						$payment = new Payment($db);						
						$allInvoices = $invoice->getAllInvoicesList($customerID);																	
						$invoiceCount = count($allInvoices);												
						for($i=0;$i<$invoiceCount;$i++) {
							$xnyo->filter_get_var('invoice_'.$i,'int');
							if (isset($_GET['invoice_'.$i])) {
								$invoiceID = $_GET['invoice_'.$i];
								$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
								$invoiceDetails['invoiceStatusList']=$invoice->getInvoiceStatusListNew($invoiceID);								
								$invoiceDetails['lastPayment'] = $payment->getLastPayment($invoiceID);							
								if (isset($_SESSION['problem'])) {
									$invoiceDetails['due'] = $_SESSION['POST']['due_'.$i];
									$invoiceDetails['status'] = $_SESSION['POST']['status_'.$i];																		
								}												
								$invoices[] = $invoiceDetails;  	
							}							
						}
						
						if (isset($_SESSION['problem'])) {
							$smarty->assign("problem",$_SESSION['problem']);
							unset($_SESSION['POST']);
							unset($_SESSION['problem']);																
						}									
								
						if ($invoices == null) {							
							header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);														
						} else {
							
							//$invoiceStatusList = $invoice->getInvoiceStatusList();
							
							$smarty->assign("invoiceStatusList",$invoiceStatusList);
																				 
							$smarty->assign("invoices",$invoices);
							$smarty->assign("customerID", $customerID);							
							$smarty->assign("action","showEditInvoice");
							$smarty->assign("bookmarkType","customers");
							$smarty->display("tpls:vps.tpl");
						}																					
						break;
						
					case "areYouSureEdit":						
						$customerID = $_POST['customerID'];
						$vps2voc = new VPS2VOC($db);
						$customerDetails = $vps2voc->getCustomerDetails($customerID);
						
						$invoice = new Invoice($db);						
						$allInvoices = $invoice->getAllInvoicesList($customerID);																	
						$invoiceCount = count($allInvoices);
						$statusPossibleValues = array('DUE','PAID','CANCELED','DEACTIVATED');
								
						$conflict = "";
						$partOfURL = "";						
						for($i=0;$i<$invoiceCount;$i++) {						
							if (isset($_POST['invoiceID_'.$i])) {
								$invoiceID = $_POST['invoiceID_'.$i];
								
								if (isset($_POST['due_'.$i]))
								{
									if (!strpos($_POST['due_'.$i],'.')) {
										$_POST['due_'.$i] .= ".00";
									}
									$problem[$i]['due'] = (preg_match('/^[0]*(\d+\.\d{2})$/',$_POST['due_'.$i],$matches)) ? false : true;
									$newDueAmount = ($matches[1]) ? $matches[1] : $_POST['due_'.$i];
								}						
								$problem[$i]['status'] = (array_search($_POST['status_'.$i], $statusPossibleValues) == false) ? false : true;								
								
								$note = $_POST['note_'.$i]; 
								
								$invoiceStatusList = $invoice->getInvoiceStatusListNew();
								$newStatus = $invoiceStatusList[$_POST['status_'.$i]]['label'];
								//$newStatus = $_POST['status_'.$i];
								$newInvoice = array (
									'invoiceID'			=> $invoiceID,
									'due'				=> $newDueAmount,
									'status'			=> $newStatus,
									'statusID'			=> $_POST['status_'.$i],
									'paymentMethodID'	=> $invoiceStatusList[$_POST['status_'.$i]]['paymentMethodID'],
									'note'				=> $note
								);
								$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);								
								
								//MANAGE CONFLICTS								
								//check for more than one invoice for one bp conflict
								if ($newStatus != "CANCELED" && $invoiceDetails['billingInfo'] != null) {
									$invoiceForFutureBP = $invoice->getInvoiceForFuturePeriod($customerID);									
									if ($invoiceForFutureBP && $invoiceForFutureBP['invoiceID'] != $invoiceID) {
										//two invoices for one bp										
										$conflict .= "<div>Only one invoice for billing period allowed. Cancel invoice <b>".$invoiceForFutureBP['invoiceID']."</b> first.</div>";
									}										
								}																																
								$conflict .= ((float)$invoiceDetails['total'] < (float)$newDueAmount) ? "<div><b>Due amount</b> should be less than <b>total amount</b>.</div>" : "";																								
								//---------------------
								
								//redirect url preparing
								$partOfURL .= "&invoice_".$i."=".$invoiceID;
								 										
								$invoices[] = $newInvoice;
							}														
						}		
									
						
						$totalProblem = false;
						foreach ($problem as $problemWithInput) {
							if (($problemWithInput['due'] && $newStatus == "DUE") || !empty($conflict)) {
								$totalProblem = true;
								break;
							}
						}
						
						if (!$totalProblem) {						
							$smarty->assign("customerDetails", $customerDetails);							
							$smarty->assign("invoices",$invoices);
							$smarty->assign("changeInvoiceStatus","yes");
							$smarty->assign("bookmarkType","areYouSureInvoice");		
							$smarty->display("tpls:vps.tpl");	
						} else {
							//redirect							
							$_SESSION['problem'] = $problem;
							$_SESSION['POST'] = $_POST;
							header ('Location: admin.php?invoiceAction=Edit&invoiceType=custom'.$partOfURL.'&action=vps&vpsAction=manageInvoice&customerID='.$customerID);
						}
						break;
						
					case "confirmEdit":
						
						$customerID = $_POST['customerID'];
						$invoice = new Invoice($db);						
						$allInvoices = $invoice->getAllInvoicesList($customerID);																	
						$invoiceCount = count($allInvoices);
						
						for($i=0;$i<$invoiceCount;$i++) 
						{							
							if (isset($_POST['invoiceID_'.$i])) 
							{
								$invoiceID = $_POST['invoiceID_'.$i];								
								//$newStatus = $_POST['statusID_'.$i];
								$invoiceStatusList = $invoice->getInvoiceStatusListNew();
								$newStatus = $invoiceStatusList[$_POST['statusID_'.$i]]['status'];								
								$newPaymentMethod = $_POST['paymentMethodID_'.$i];							
								$newDueAmount = $_POST['due_'.$i];							
								$note = $_POST['note_'.$i];											
											
								$invoice->changeInvoiceStatus($invoiceID, $newStatus, $newPaymentMethod, $newDueAmount, $note);								
							}
						}
						header("Location: ?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);
						break;
						
					case "Add":					
						$xnyo->filter_get_var('customerID','int');
						$customerID = $_GET['customerID'];
						$vps2voc = new VPS2VOC($db);
						$customerDetails = $vps2voc->getCustomerDetails($customerID);
						
						$xnyo->filter_get_var('invoiceType','text');
						$invoiceType = $_GET['invoiceType'];
						switch ($invoiceType) {
							case "module":
								if($_SESSION['POST']['invoiceType'] == 'module') {
									$defaultSuspensionDate = $_SESSION['POST']['suspensionDate'];
									
									$smarty->assign("problem", $_SESSION['problem']);
									
									unset($_SESSION['POST']);
									unset($_SESSION['problem']); 
								} else {
									$startDate = date('Y-m-d',strtotime(date('Y-m-d')." + 5 days"));
									//$BPType = "";															
								}	
								$billing = new Billing($db);
								$modules=$billing->getModules();						
								$periodList=$billing->getDistinctMonths();
								$bp=$billing->getDefinedPlans($customerID);								
								$bp_type=$bp[0]['type'];
								
								$smarty->assign("bp_type",$bp_type);
								$smarty->assign("periodList",$periodList);																					
								$smarty->assign("modules",$modules);
								$smarty->assign("startDate", $startDate);
							break;							
							case "custom":
								if($_SESSION['POST']['invoiceType'] == 'custom') {
									$defaultSuspensionDate = $_SESSION['POST']['suspensionDate'];
									$defaultAmount = $_SESSION['POST']['amount'];
									$customInfo = $_SESSION['POST']['customInfo'];
									$suspensionDisable = isset($_SESSION['POST']['suspensionDisable']);
									
									$smarty->assign("problem", $_SESSION['problem']);
									
									unset($_SESSION['POST']);
									unset($_SESSION['problem']); 
								} else {
									$defaultSuspensionDate = date('Y-m-d',strtotime(date('Y-m-d')." + 30 days"));
									$defaultAmount = "9.99";
									$customInfo = "some reason";
									$suspensionDisable = false;	
								}																															
								$smarty->assign("defaultSuspensionDate", $defaultSuspensionDate);
								$smarty->assign("defaultAmount", $defaultAmount);
								$smarty->assign("customInfo", $customInfo);
								$smarty->assign("suspensionDisable", $suspensionDisable);
								break;
							case "limit":								
								$billing = new Billing($db);								
								$customerPlan = $billing->getCustomerPlan($customerID);
								if (!$customerPlan) {
									throw new Exception('This customer does not have billing plan yet.');
								}
								
								foreach ($customerPlan['limits'] as $limitName=>$limitInfo) {
									$limit['id'] = $limitInfo['limit_id'];
									$limit['name'] = $limitName;
									$limit['unit_type'] = $limitInfo['unit_type'];
									$limitList[] = $limit;
								}																										
								$smarty->assign("limitList",$limitList);
								
								if($_SESSION['POST']['invoiceType'] == 'limit') {									
									$limitID = $_SESSION['POST']['limitID'];
									$plusToValue = $_SESSION['POST']['plusToValue'];									
									
									$smarty->assign("problem", $_SESSION['problem']);
									
									unset($_SESSION['POST']);
									unset($_SESSION['problem']);
								} else {
									$limitID = 1;
									$plusToValue = 10;
								}
								$smarty->assign("limitID", $limitID);
								$smarty->assign("plusToValue", $plusToValue);
								break;														
							default:
								//redirect fraud
								break;
						}
						
						$smarty->assign("customerDetails", $customerDetails);
						$smarty->assign("invoiceType",$invoiceType);
						$smarty->assign("action","showAddInvoice");
						$smarty->assign("bookmarkType","customers");
						$smarty->display("tpls:vps.tpl");						
						break;											
						
					case "areYouSureAdd":
						$xnyo->filter_post_var('customerID','int');
						$customerID = $_POST['customerID'];
						$vps2voc = new VPS2VOC($db);
						$customerDetails = $vps2voc->getCustomerDetails($customerID);
						
						$xnyo->filter_post_var('invoiceType','text');
						$invoiceType = $_POST['invoiceType'];						
						switch ($invoiceType) {
							case "module":				
								$start_date = date('Y-m-d',strtotime($_POST['startDate']));			
								$smarty->assign("bp_type", $_POST['bp_type']);							
								$smarty->assign("module_name", $_POST['module_name']);								
								$smarty->assign("period", $_POST['period']);
								$smarty->assign("startDate", (($start_date > date('Y-m-d'))?$start_date:date('Y-m-d')));								
							break;
							case "custom":
								$xnyo->filter_post_var('customInfo','text');
								$xnyo->filter_post_var('amount','text');
								$xnyo->filter_post_var('suspensionDate','text');
								$xnyo->filter_post_var('suspensionDisable','text');
							
								//validation
								if (!strpos($_POST['amount'],'.')) {
									$_POST['amount'] .= ".00";
								}
								$problem['amount'] = ((preg_match('/^[0]*(\d+\.\d{2})$/',$_POST['amount'],$matches)) ? false : true);
								$amount = ($matches[1]) ? $matches[1] : $_POST['amount'];								
								$problem['suspensionDate'] = ((preg_match('/^(\d{4}\-\d{2}\-\d{2}).*$/',$_POST['suspensionDate'],$matches)) ? false : true);
								$suspensionDate = ($matches[1]) ? $matches[1] : $_POST['suspensionDate'];																				
								$suspensionDisable = (!$_POST['suspensionDisable']) ? 0 : 1;
																																	
								$smarty->assign("customInfo", $_POST['customInfo']);								
								$smarty->assign("amount", $amount);
								$smarty->assign("suspensionDate", $suspensionDate);
								$smarty->assign("suspensionDisable", $suspensionDisable);																													
								break;
								
							case "limit":
								$xnyo->filter_post_var('limitID','text');
								$xnyo->filter_post_var('plusToValue','text');
								
								$billing = new Billing($db);
								$limitDetails = $billing->getLimitDetailsByID($_POST['limitID']);
								$problem['plusToValue'] = ((preg_match('/^[0]*(\d+)$/',$_POST['plusToValue'],$matches)) ? false : true);
								$plusToValue = ($matches[1]) ? $matches[1] : $_POST['plusToValue'];
																							
								$smarty->assign("limitDetails", $limitDetails);
								$smarty->assign("plusToValue", $plusToValue);
								break;
							
							default:
								//redirect fraud
								break;
						}
						
						$totalProblem = false;
						foreach ($problem as $problemWithInput) {
							if ($problemWithInput) {
								$totalProblem = true;
								break;
							}
						}
						if (!$totalProblem) {				
							$smarty->assign("customerDetails", $customerDetails);
							$smarty->assign("invoiceType",$invoiceType);									
							$smarty->assign("bookmarkType","areYouSureInvoice");		
							$smarty->display("tpls:vps.tpl"); 
						} else {
														
							$_SESSION['problem'] = $problem;
							$_SESSION['POST'] = $_POST;							
							header ('Location: admin.php?invoiceType='.$invoiceType.'&invoiceAction=Add&action=vps&vpsAction=manageInvoice&customerID='.$customerID);							
						}																					
						break;
						
					case "confirmAdd":
						$xnyo->filter_post_var('customerID','int');
						$customerID = $_POST['customerID'];
						$vps2voc = new VPS2VOC($db);
						$customerDetails = $vps2voc->getCustomerDetails($customerID);
						
						$xnyo->filter_post_var('invoiceType','text');
						$invoiceType = $_POST['invoiceType'];						
						switch ($invoiceType) {
							case "module":
								$invoice = new Invoice($db);
								$billing = new Billing($db);
								//$billingPlanID=$billing->getBillingPlanIDWithModuleName($_POST['module_name']);											
								//$invoice->createInvoiceForModule($customerID,$_POST['startDate'],$billingPlanID);	
								$billingPlanID = $billing->getModulePlanByParams($_POST['module_name'],$_POST['bp_type'],$_POST['period']);
								
								$db->beginTransaction();
								$billing->applyModuleBillingPlan($customerID,$billingPlanID,$_POST['startDate']);
								$db->commitTransaction();	
								break;
							case "custom":						
								$customInfo = "'".$_POST['customInfo']."'";
								$amount = $_POST['amount'];
								$suspensionDate = $_POST['suspensionDate'];
								$suspensionDisable = $_POST['suspensionDisable'];								
								
								$invoice = new Invoice($db);
								
								$db->beginTransaction();
								$invoice->createCustomInvoice($customerID, $amount, $suspensionDate, $suspensionDisable, $customInfo);
								$db->commitTransaction();																
								break;
								
							case "limit":								
								$limitName = $_POST['limitName'];
								$plusToValue = $_POST['plusToValue'];
								
								$billing = new Billing($db);
								
								$db->beginTransaction();
								$currencyDetails = $billing->getCurrencyByCustomer($customerID);
								$billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue, $currencyDetails['id']);
								$db->commitTransaction();
								break;
						}
						
						header("Location: admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID=".$customerID);
						break;							
				}					
			break;
			
		case "sync"://TODO delete me if no need in bridge.xml
			$bridge = new Bridge($db);
			$bridge->CopyAllCustomersToBridge();
			$bridge->CopyAllUsersToBridge();			
			//header("Location: ?action=vps&vpsAction=browseCategory&itemID=billing");
			break;		
			
		case "ping":
			$billing = new Billing($db);
			$bridge = new Bridge($db);
			echo "Billing::getModuleBillingPlans()";
			var_dump($billing->getModuleBillingPlans());
			echo "Bridge::getModules()";
			var_dump($bridge->getModules());
			break;
	}
											
?>
