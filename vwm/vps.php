<?php
	define ("USERS_TABLE", "vps_user");	

	function println($str) {
		echo $str."<br>";
	}
	
	require('config/constants.php');
	
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	
	$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 

	define ('site_path', $site_path);
	
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php');
	
	//	Start Smarty templates engine
	require ('modules/xnyo/smarty/startSmartyVPS.php');
	
	//	deny access to system while updating jobs
	if (MAINTENANCE) {
		$smarty->display('tpls:errors/maintenance.tpl');
		die();
	}

	$db->select_db(DB_NAME);
	
	$xnyo->load_plugin('auth');
	$xnyo->logout_redirect_url="vps.php";
	
	$xnyo->filter_get_var('action', 'text');
	$xnyo->filter_post_var('action', 'text');
	
	
		
	function p($name,$var = NULL)
	{
		echo "<br/>" . $name;
		if($var)
		{
			echo " = " . $var . "<br/>";	
		}
		else
		{
			echo "<br/>";
		}
	}

	function showMyInfo($db,$smarty,$userData) {
		$vps2voc = new VPS2VOC($db);
		   //$bridge->CopyAllCustomersToBridge();
		   //$bridge->CopyAllUsersToBridge();		 
		$customerDetails = $vps2voc->getCustomerDetails($userData['company_id'],true);
		
		$smarty->assign("companyName",$customerDetails['name']);				
		
		//getting state list ////////need to add to Billing or smth else getStateList() and getCountryList() functions and add to db states and countries  
		$state = new State($db);
		$stateList = $state->getStateList();
		$smarty->assign("states",$stateList);
		
		//getting country lists
		$country = new Country($db);
		$countryList = $country->getCountryList();
		$smarty->assign("countries",$countryList);

		if ($userData["showAddUser"]) {
			$smarty->assign("action","addUser");	
		} else {
			$smarty->assign("action","editCategory");	
		}
		
		$billing = new Billing($db);		
		$currenciesList = $billing->getCurrenciesList();
		$smarty->assign("currenciesList",$currenciesList);
		if (isset($customerDetails['currency_id'])) {
			$userData['currency_id'] = $customerDetails['currency_id'];
		}		
				
		$title = "My info";
		setTitle($title, $smarty);		
						
		$smarty->assign("userData",$userData);
		$smarty->assign("category","myInfo");
		$smarty->display("tpls:vps.tpl");	
	}
	
	
	
	function setBookmarks($category, $smarty) {
		switch ($category) {
			case "invoices":
			
				$subCategoryList = array('All','Paid','Canceled','Due');
				foreach ($subCategoryList as $subCategoryName) {
					$bookmark['label'] = $subCategoryName;
					$bookmark['name'] = $subCategoryName;
					$bookmark['url'] = "vps.php?action=viewList&category=invoices&subCategory=".$subCategoryName;
					$bookmarks[] = $bookmark;						
				}						
				$smarty->assign("bookmark",$bookmarks);
				
				break;
				
			case "billing":
			
				$subCategoryList = array('My Billing Plan','Available Billing Plans');
					foreach ($subCategoryList as $subCategoryName) {
						$bookmark['label'] = $subCategoryName;
						$bookmark['name'] = str_replace(" ","",$subCategoryName);
						$bookmark['url'] = "vps.php?action=viewDetails&category=billing&subCategory=".$bookmark['name'];
						$bookmarks[] = $bookmark;						
					}						
				$smarty->assign("bookmark",$bookmarks);
			
				break;	
		}		
	}
	
	
	
	function showAvailableBillingPlans($db, $smarty, $currencyID) {
		
		$smarty->assign("currentBookmark","AvailableBillingPlans");
		$billing = new Billing($db);
				
		//getting available billing plans
		$billingPlanList = $billing->getAvailablePlans($currencyID);
		$smarty->assign("availablePlans",$billingPlanList);
		
		//distinct months count and user count
		$months = $billing->getDistinctMonths();
		$sources = $billing->getDistinctSource();
		$smarty->assign("months",$months);
		$smarty->assign("monthsCount",count($months));
		$smarty->assign("sources",$sources);
		
		//echo "availablePlans:<br/>";
		//var_dump($billingPlanList);	
		
		$title = "Available Billing Plans";
		setTitle($title, $smarty);
		
		//Create data for modules		
		$vps2voc = new VPS2VOC($db);
		
		$modules = $billing->getModuleBillingPlans(null, $currencyID);
		
		$smarty->assign("allModules",$modules);
		
		$moduleBPsheet = array();//grouped by modules and monthes
		foreach ($modules as $plan) {
			$moduleBPsheet[$plan['module_id']][$plan['type']][$plan['month_count']] = array(
					'id' => $plan['id'],
					'price' => $plan['price']
				);
			$moduleBPsheet[$plan['module_id']]['name'] = $plan['module_name'];
			$moduleBPsheet[$plan['module_id']]['applied'] = ((isset($howApplied[$plan['module_id']]))?$howApplied[$plan['module_id']]:false);
		}
		$smarty->assign("allModules", $moduleBPsheet);
		$ids_names = $vps2voc->getModules();
		$ids = array();
		foreach($ids_names as $id => $key) {
			$ids []= $id;
		}
		$smarty->assign("ids",json_encode($ids));
		$smarty->assign('date',date('Y-m-d'));
		$smarty->assign('newUserRegistration',true);
		/////////////////////////
		
		$currencyDetails = false;
		if (isset($_SESSION['userDetails'])) {
			$currencyDetails = $billing->getCurrencyDetails($_SESSION['userDetails']['currency_id']);							
		}		
		$smarty->assign("currentCurrency",$currencyDetails);
				
		$smarty->assign("category","billing");					
		$smarty->display("tpls:vps.tpl");
			
	}
	
	
	function setTitle($title, $smarty) {
		$title = "VOC Payment System: ".$title;
		$smarty->assign("title", $title);
	}
	
	
	function loadConfig($db) {
		//$db->select_db(DB_NAME);				
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
	
	function getSelectedModulesFromGET($db,$currencyID)
	{
		$billing = new Billing($db);
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
	
	function calculateInvoicesSum($invoices,$currencyISO)
	{
		if(empty($invoices) or !is_array($invoices)){
			return 0;
		}
		global $db;
		
		$billing = new Billing($db);
		$currencies = $billing->getCurrenciesList(true);
		
		
		
		$invoice2currency = array();
		
		foreach( $invoices as $i ) {
			
			$iso = $currencies[$i['currency_id']];
			$invoice2currency[$iso['iso']] += $i['total'] < 0 ? $i['total'] * -1 : $i['total']; //If total < 0 - make it positive
			
		}
		
		//var_dump($invoice2currency);
		try
		{
			$currencyConvertor = new CurrencyConvertor();
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
			//global $smarty;
			//$smarty->assign("message",$msg);
			//$smarty->display('tpls:errors/other.tpl');
			return "[error]";
		}
		
		$sum = $currencyConvertor->Sum($invoice2currency,$currencyISO);
		$sum = round($sum,2);
		
		if(!$sum or $sum == 0)
		{
			$sum = 0;
		}
		//echo "$currencyISO = $sum";
		return $sum;
		//$billing->getCurrencyDetails($currencyID)
	}
	
	
	$user = new VPSUser($db, $auth, $access, $xnyo);
	
	
	if (!isset($_GET["action"])) {
		
		if (isset($_POST["action"]) && $_POST["action"]=="auth") {
			//	Try to authorize
			$xnyo->filter_post_var("username", "text");
			$xnyo->filter_post_var("password", "text");
			
			$username = $_POST["username"];
			$password = $_POST["password"];
			
			
			if ($authResult = $user->authorize($username, $password)) {
	
				if ($authResult["showAddUser"]) {
					
					//new user registration flag
					$newUserRegistration = true;
					$smarty->assign("newUserRegistration",$newUserRegistration);
					
					$_SESSION['registration'] = true;
					$_SESSION['originalPassword'] = $password;
					showMyInfo($db,$smarty,$authResult);									
				
				} else {
					//	Redirect user to dashboard
					session_start();	
						
					$_SESSION['userID']=$user->getUserIDbyAccessname($username);
					$_SESSION['accessname']=$username;					
					$accessLevel=$user->getUserAccessLevel($_SESSION['userID']);
					$_SESSION['accessLevel']=$accessLevel;
					$_SESSION['customerID'] = $user->getCustomerIDbyUserID($_SESSION['userID']);
					
					if($_POST['backUrl'])
					{
						$backUrl =  $_POST['backUrl'];
					}
					else
					{
						$backUrl = "vps.php?action=viewDetails&category=dashboard";
					}
					header("Location: $backUrl");
				}
			} else {
				$smarty->assign("status", "fail");
				
				$smarty->display("tpls:authorization.tpl");
			}
		} else {
			
			//	Show login page
			if(isset($_GET['backUrl']))
			{
				$smarty->assign("backUrl",urldecode($_GET['backUrl']));
			}
			$smarty->display("tpls:authorization.tpl");
		}
		
	} else {
		$userID = $_SESSION['userID'];
		$customerID = $_SESSION['customerID'];
		
		if (empty($userID) && !$_SESSION['registration']) {

			$backUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$backUrl = urlencode($backUrl);
			header("Location: vps.php?backUrl=$backUrl");
		}
		
		if (!$_SESSION['registration']) {
			$vps2voc = new VPS2VOC($db);
			$customerDetails = $vps2voc->getCustomerDetails($customerID);
			$isTrial = (strtotime($customerDetails['trial_end_date']) >= strtotime(date('Y-m-d'))) ? true : false;				
			$smarty->assign("isTrial", $isTrial);
		}		
		$smarty->assign("accessname",strtoupper($_SESSION['accessname']));				
		
		//	Parse action
		switch ($_GET["action"]) {
			
			case "payInvoice":
				$invoice = new Invoice($db);
				$details = $invoice->getInvoiceDetails($_GET['invoiceID']);
				$balance = $invoice->getBalance($details['customerID']);
				
				if($balance - $details['total'] <= 0) /*Если денег недостаточно для оплаты инвойса - кнопочка неактивная*/
				{
					$smarty->assign("enablePayButton",false);
				}
				else
				{
					$smarty->assign("enablePayButton",true);
				}
				
				$smarty->assign("invoice",$details);
				
				$title = "Pay Invoice";
				setTitle($title, $smarty);
				
				
				$smarty->assign();
				$smarty->assign("areYouSureAction","pay for invoice");
				$smarty->assign("category","invoices");
				$smarty->assign("subCategory",$subCategory);
				$smarty->assign("currentBookmark",$_GET["action"]);
				$smarty->display("tpls:vps.tpl");
				
			break;	
			
			case "viewList":
				$xnyo->filter_get_var('category', 'text');
				$category = $_GET['category'];
												
				switch ($category) {
					case "invoices":
						setBookmarks($category, $smarty);
						
						$xnyo->filter_get_var('subCategory', 'text');
						$subCategory = $_GET['subCategory'];																								
						$smarty->assign("currentBookmark",$subCategory);
						$invoice = new Invoice($db);
						switch ($subCategory) {
							case "All":
								$invoiceList = $invoice->getAllInvoicesList($customerID);						
								break;
							case "Paid":
								$invoiceList = $invoice->getPaidInvoicesList($customerID);						
								break;
							case "Due":

								$invoiceList = $invoice->getDueInvoicesList($customerID);	
								
								$size = count($invoiceList);
								for($i=0; $i< $size; $i++)
								{
									if(($invoiceList[$i]['customer_balance'] - $invoiceList[$i]['total']) < 0) //Если денег недостаточно для оплаты инвойса - кнопочка неактивная
									{
										$invoiceList[$i]['enablePayButton'] = "disabled";
									}
									else
									{
										$invoiceList[$i]['enablePayButton'] = "enabled";
									}
								}
																							
								break;
							case "Canceled":							
								$invoiceList = $invoice->getCanceledInvoicesList($customerID);																
								break;
							
						}	//subCategory switch ending						
							
						$smarty->assign("invoiceList",$invoiceList);
						$smarty->assign("invoiceListCount",count($invoiceList));
						
						$smarty->assign("userID",$userID);
						
						$viewListURL = "vps.php?action=viewList&category=invoices";
						$smarty->assign("viewListURL",$viewListURL);
						
						$billing = new Billing($db);
						$groupedCurrencies = $billing->getCurrenciesList(true);	//	grouped						
						$smarty->assign('currencies', $groupedCurrencies);
						
						$title = $subCategory." invoices";
						setTitle($title, $smarty);
												
						$smarty->assign("category","invoices");
						$smarty->display("tpls:vps.tpl");
						break;  // invoices category break
										
				}	//category switch ending
				
				break;
				
			case "viewDetails":
				$xnyo->filter_get_var('category', 'text');
				$category = $_GET['category'];
								
				switch ($category) {
					
					case "dashboard":							
						$billing = new Billing($db);
						$currency = $billing->getCurrencyByCustomer($customerID);						
						
						$invoice = new Invoice($db);
						$allInvoices = $invoice->getAllInvoicesList($customerID);	
						
						$curentCurrency = $billing->getCurrencyByCustomer($customerID);
						
						$paidInvoices = $invoice->getPaidInvoicesList($customerID);
						$paidInvoicesSum = calculateInvoicesSum($paidInvoices,$curentCurrency['iso']);
						
						
						
						$dueInvoices = $invoice->getDueInvoicesList($customerID);
						
						$dueInvoicesSum = calculateInvoicesSum($dueInvoices,$curentCurrency['iso']);
						
						
						$canceledInvoices = $invoice->getCanceledInvoicesList($customerID);
						$canceledInvoicesSum = calculateInvoicesSum($canceledInvoices,$curentCurrency['iso']);
						
						$balance = $currency['sign'].' '.number_format($invoice->getBalance($customerID),2);
						
						$all['count'] = count($allInvoices);
						
						$paid['count'] = count($paidInvoices);
						$paid['total'] = $paidInvoicesSum;//number_format($invoice->totalPaid,2);
						
						//count total paid without custom invoices
						/*foreach($paidInvoices as $i)
						{
							if()
							$paid['total'] +=
						}*/
						
						$due['count'] = count($dueInvoices);
						$due['total'] = $dueInvoicesSum;//number_format($invoice->totalDue,2);
						
						
						$canceled['count'] = count($canceledInvoices);
						$canceled['total'] = $canceledInvoicesSum;//number_format($invoice->totalCanceled,2);
						
						$config = loadConfig($db);						 
						$lastInvoice = $invoice->getLastInvoice($customerID);
						$nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice['periodEndDate']." - ".$config['invoice_generation_period']." days"));	

						
						$smarty->assign("currency",$currency);
						$smarty->assign("allInvoices",$all);
						
						$smarty->assign("paidInvoices",$paid);
						$smarty->assign("dueInvoices",$due);
						$smarty->assign("canceledInvoices",$canceled);
						$smarty->assign("balance",$balance);
						$smarty->assign("nextInvoiceDate",$nextInvoiceDate);
						$smarty->assign("currentCurrency",$currency);
						
						$viewListURL = "vps.php?action=viewList&category=invoices";
						$smarty->assign("viewListURL",$viewListURL);
						
						$title = "Dashboard";
						setTitle($title, $smarty);
						
						$smarty->assign("category","dashboard");				
						$smarty->display("tpls:vps.tpl");
						break;
				
					case "billing":
											
						setBookmarks($category, $smarty);
						
						$xnyo->filter_get_var('subCategory', 'text');
						$subCategory = $_GET['subCategory'];
						$smarty->assign("currentBookmark",$subCategory);
						
						$billing = new Billing($db);
						$currency = $billing->getCurrencyByCustomer($customerID);
						$groupedCurrencies = $billing->getCurrenciesList(true);						
						$smarty->assign("currentCurrency",$currency);
						$smarty->assign("groupedCurrencies",$groupedCurrencies);
						
						switch ($subCategory) {
							case "MyBillingPlan":
													
								$customerPlan = $billing->getCustomerPlan($customerID);
								
								
								//defined plan is not processed yet by admin
								if (!$customerPlan) {
									$pleaseWait = "VOC WEB MANAGER's Administrator is working with your query. Please wait for some time. If have questions <a href=''>contact Administrator</a>.";
									$smarty->assign("pleaseWait",$pleaseWait);
								} else {
									foreach ($customerPlan['limits'] as $limit=>$value) {
										if ($value['default_limit'] == $value['max_value']) {
											$customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type']." (free)"; 
										} else {
											$customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type'];
										}		
									}//insert customer's limits info
									$invoice = new Invoice($db);
									
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
									
									$smarty->assign("billingPlan",$customerPlan);
									$smarty->assign("totalInvoice", number_format($totalInvoice, 2));
									$smarty->assign("totalCurrency", $totalCurrency);																							
									
									$invoice = new Invoice($db);
									$discountPercent = $invoice->getDiscount($customerID);
									
									if (!empty($discountPercent)) {
										$smarty->assign("discountPercent",$discountPercent);	
									}									
								}								
								$scheduledBillingPlan = $billing->getScheduledPlanByCustomer($customerID);
									
								
								if ( $scheduledBillingPlan && ($scheduledBillingPlan['billingID'] != $customerPlan['billingID']) ) {
									$currentCurrency = $billing->getCurrencyByCustomer($customerID);									
									$futurePlan = $billing->getBillingPlanDetails($scheduledBillingPlan['billingID'], false, $currentCurrency['id']);
									
									$invoice = new Invoice($db);
									$currentInvoice = $invoice->getCurrentInvoice($customerID);								
									
									if ($scheduledBillingPlan['type'] == "bpEnd") {																				
										//trial period
										if ($currentInvoice['periodEndDate'] == NULL) {
											$firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
											$futurePlanLabel = "Following scheduled Billing Plan will be applied when first Billing Period starts <b>(".$firstInvoice['periodStartDate'].")</b>:";											
										} else {
											$futurePlanLabel = "Following scheduled Billing Plan will be applied after current Billing Period end <b>(".$currentInvoice['periodEndDate'].")</b>:";
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
									
									$smarty->assign("futureBillingPlan",$futurePlan);								
									$smarty->assign("futurePlanLabel",$futurePlanLabel);
																		
								}
								
								//modules in billing
								$currency = $billing->getCurrencyByCustomer($customerID);
								$modulesView = $billing->getPurchasedPlansForCustomerView($customerID,$currency['id']);
								
								
								
								
								$smarty->assign("appliedModules",$modulesView['modules']);
								if (!is_null($modulesView['bonus'])) {
									$smarty->assign("bonusModules",$modulesView['bonus']);
								}
								$title = "My Billing Plan";
								setTitle($title, $smarty);																			
								break;
								
							case "AvailableBillingPlans":	
								//getting customer info
								$vps2voc = new VPS2VOC($db);
								$VPSUser = new VPSUser($db);
								
								$curentCurrency = $billing->getCurrencyByCustomer($customerID);
								
								$customerLimits = $VPSUser->getCustomerLimits($customerID); 	

								
																														
								//getting current billing plan 
								$customerPlan = $billing->getCustomerPlan($customerID);
								
								
								
								//defined plan is not processed yet by admin
								if (!$customerPlan) {
									header("Location: /voc_src/vwm/vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
								}
								
								$smarty->assign("billingPlan",$customerPlan);
								
								//getting available billing plans
								$billingPlanList = $billing->getAvailablePlans($currency['id']);								
								$smarty->assign("availablePlans",$billingPlanList);
								
					
								//distinct months count and user count
								$months = $billing->getDistinctMonths();
								$sources = $billing->getDistinctSource($currency['id']);
								
								//remove options with less emission sources than current are 
								//for($i=0;$i<count($sources);$i++) {
								//	if ($sources[$i]['Source count'] < $customerLimits['Source count']['current_value']) {
								//		array_splice($sources,$i,1);								
								//	}
								//}
								
								
								
								$smarty->assign("months",$months);
								$smarty->assign("monthsCount",count($months));
								$smarty->assign("sources",$sources);
								
								//limits list
								foreach ($customerPlan['limits'] as $limit=>$value) {
									$list[$limit][0] = $value['increase_step'];
									for ($i=1;$i<10;$i++) {
										$list[$limit][$i] = $list[$limit][$i-1] + $value['increase_step'];	
									}																					
								}										
								$smarty->assign("list",$list);
								
								//modules in billing
							//	$smarty->assign("appliedModules",$billing->($customerID));
							
								$appliedModules = $billing->getPurchasedModule($customerID,null,'today&future');
								
								$howApplied = array();
								foreach ($appliedModules as $module) {
									$howApplied[$module['module_id']] = $module['id'];
								}
								$modulesPlans = $billing->getModuleBillingPlans(null,$currency['id']);
								$smarty->assign("allModules",$modulesPlans);
																
								$moduleBPsheet = array();//grouped by modules and monthes
								foreach ($modulesPlans as $plan) {
									$moduleBPsheet[$plan['module_id']][$plan['type']][$plan['month_count']] = array(
											'id' => $plan['id'],
											'price' => $plan['price']
										);
									$moduleBPsheet[$plan['module_id']]['name'] = $plan['module_name'];
									$moduleBPsheet[$plan['module_id']]['applied'] = ((isset($howApplied[$plan['module_id']]))?$howApplied[$plan['module_id']]:false);
								}
								$smarty->assign("allModules", $moduleBPsheet);
								$ids_names = $vps2voc->getModules();
								$ids = array();
								foreach($ids_names as $id => $key) {
									$ids []= $id;
								}
								$smarty->assign("ids",json_encode($ids));
								$smarty->assign('date',date('Y-m-d'));
								
								$title = "Available Billing Plans";
								setTitle($title, $smarty);																																		
								break;															
						}
						
						$smarty->assign("category","billing");
						$smarty->display("tpls:vps.tpl");
						
						break;					
								
					case "invoices":
												
						$smarty->assign("currentBookmark","viewDetails");
						
						$xnyo->filter_get_var('invoiceID', 'int');
						$invoiceID = $_GET['invoiceID'];
						
						$invoice = new Invoice($db);
						
						/**
						 * DEPRECATED возвращает старый инвойс
						//$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
						 */
						
						$invoiceDetails = $invoice->getInvoiceItemsDetails($invoiceID);						
						$smarty->assign("invoiceDetails", $invoiceDetails);
						
						$payment = new Payment($db);
						
						$paymentHistory = $payment->getHistory($invoiceID);						
						$smarty->assign("paymentHistory", $paymentHistory);
						
						$xnyo->filter_get_var('successPayment', 'int');
						if (isset($_GET['successPayment'])) {
							$successPayment = $_GET['successPayment'];
							if ($successPayment == 1) { //	success paypal payment
								$smarty->assign("success","Thank you for using VOC WEB MANAGER. Your payment is successfully accepted.");
							} elseif ($successPayment == 0) {
								$smarty->assign("canceled","Payment was canceled by user.");
							}	 							
						}
						
						$billing = new Billing($db);
						$currentCurrency = $billing->getCurrencyDetails($invoiceDetails['currency_id']);
						$smarty->assign("currentCurrency", $currentCurrency);
						
						$title = "Invoice ".$invoiceID;
						setTitle($title, $smarty);	
						
						$smarty->assign("category", $category);
						$smarty->display("tpls:vps.tpl");		
						break;
					
					case "myInfo":																								
						$userData = $user->getUserDetails($userID);
						showMyInfo($db,$smarty,$userData);
						
						break;					
				}	
				
				break; 
				
																			
			case "editCategory":
				$xnyo->filter_get_var('category', 'text');
				$category = $_GET['category'];
				
				switch ($category) {
					case "billing":
						$xnyo->filter_get_var('subCategory','text');
						$subCategory = $_GET['subCategory'];												
						
						switch ($subCategory) {
							case "MSDSLimit":								
								$xnyo->filter_get_var('plusTo','int');
								$plusToValue = $_GET['plusTo'];
								$smarty->assign("plusTo",$plusToValue);
								
								$billing = new Billing($db);	

								$currency = $billing->getCurrencyByCustomer($customerID);
								$smarty->assign("curentCurrency",$currency);
								
								$currentBillingPlan = $billing->getCustomerPlan($customerID);
								
								$areYouSureAction = "change MSDS limit";
								$from =	$currentBillingPlan;
								
								$to = $from;						
								$to['limits']['MSDS']['max_value'] += $plusToValue;
								
								$increaseCost = $to['limits']['MSDS']['increase_cost'] * ($plusToValue/$to['limits']['MSDS']['increase_step']);	//убого
								$smarty->assign("increaseCost",$increaseCost);
								  
								$smarty->assign("areYouSureAction",$areYouSureAction);
								$smarty->assign("from",$from);
								$smarty->assign("to",$to);
								
								$title = "Increase MSDS limit";
								setTitle($title, $smarty);	
						
								$smarty->assign("category","billing");
								$smarty->assign("subCategory",$subCategory);
								$smarty->assign("currentBookmark",$_GET["action"]);
								$smarty->display("tpls:vps.tpl");
								break;
								
							case "memoryLimit":
								$xnyo->filter_get_var('plusTo','int');
								$plusToValue = $_GET['plusTo'];		
								$smarty->assign("plusTo",$plusToValue);						
								
								$billing = new Billing($db);				

								$currency = $billing->getCurrencyByCustomer($customerID);
								$smarty->assign("curentCurrency",$currency);
								
								$currentBillingPlan = $billing->getCustomerPlan($customerID);
								
								$smarty->assign("currentCurrency",$currency);
								
								$areYouSureAction = "change memory limit";
								$from =	$currentBillingPlan;
								
								$to = $from;						
								$to['limits']['memory']['max_value'] += $plusToValue;
								
								$increaseCost = $to['limits']['memory']['increase_cost'] * ($plusToValue/$to['limits']['memory']['increase_step']);	//убого
								$smarty->assign("increaseCost",$increaseCost);
								
								$smarty->assign("areYouSureAction",$areYouSureAction);
								$smarty->assign("from",$from);
								$smarty->assign("to",$to);
								
								$title = "Increase memory limit";
								setTitle($title, $smarty);	
								
								$smarty->assign("category","billing");
								$smarty->assign("subCategory",$subCategory);
								$smarty->assign("currentBookmark",$_GET["action"]);
								$smarty->display("tpls:vps.tpl");
								break;
								
							case 'modules':
								$billing = new Billing($db);
								
								$currentCurrency = $billing->getCurrencyByCustomer($customerID);
								$smarty->assign("currentCurrency",$currentCurrency);
								
								
								if (isset($_GET['total']) && ($_GET['total'] == 'delete')) {
									//Delete Module
									$moduleID = $_GET['module'];
									$status = $_GET['status'];
									
									
									
									if ($status == 'delete_all') {
										$plans = $billing->getPurchasedModule($customerID,$moduleID,'today&future','today',$currentCurrency['id']);
										$smarty->assign("plans",$plans);
										$smarty->assign("moduleID",$moduleID);
									} elseif ($status == 'delete_plan') {
										$planID = $_GET['plan'];
										$plans = $billing->getPurchasedModule($customerID,$moduleID,'today&future','today',$currentCurrency['id']);
										foreach ($plans as $module_plan) {
											if ($module_plan['id'] == $planID) {
												$plan = $module_plan;
											}
										}
										$smarty->assign("plan",$plan); //TODO: it can be more than 1 plans
									}
									$smarty->assign("status",$status);
									$smarty->assign("areYouSureAction","remove module plan");
								} else {
									//edit of MBP
									$vps2voc = new VPS2VOC($db);
									$modules = $vps2voc->getModules();
									$no_changes = true;
									$plans = array();
									$oldModulePlans = array();
									$ids = array();
									$plan_start = date('Y-m-d',strtotime(html_entity_decode($_GET['startDate'])));
											if ($plan_start < date('Y-m-d')) {
												$plan_start = date('Y-m-d');
											}
									foreach($modules as $key => $value) {
										if (!is_null($_GET['selectedModulePlan_'.$key])) {
											$plan = $billing->getModuleBillingPlans($_GET['selectedModulePlan_'.$key],$currentCurrency['id']);
											$plan = $plan[0];
											$plan['start'] = $plan_start;
											$plan['end'] = date('Y-m-d', strtotime($plan['start'].'+'.$plan['month_count'].' month - 1 day'));
									
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
									
									$smarty->assign("plans",$plans);
									$smarty->assign("oldPlans",$oldModulePlans);
									$smarty->assign("plan_ids",json_encode($ids));
									$smarty->assign("start",$plan_start);
									$smarty->assign("areYouSureAction","apply module plan");
								}
								$smarty->assign("category","billing");
								$smarty->assign("subCategory",$subCategory);
								$smarty->assign("currentBookmark",$_GET["action"]);
								$smarty->display("tpls:vps.tpl");
								
								break;
								
							
								
							default:					
								$xnyo->filter_get_var('selectedBillingPlan', 'text');
								$selectedBillingPlanID = $_GET['selectedBillingPlan'];
							
								$billing = new Billing($db);
								
								$currentCurrency = $billing->getCurrencyByCustomer($customerID);
								$smarty->assign("currentCurrency",$currentCurrency);
								
								$newBillingDetails = $billing->getBillingPlanDetails($selectedBillingPlanID, false, $currentCurrency['id']);
								$currentBillingPlan = $billing->getCustomerPlan($customerID);																
							
								//check current and new plan. if equal than go to billing plan page
								if ($currentBillingPlan['billingID'] == $newBillingDetails['billingID']) {
									header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");
									break;
								}
								
							
								//manage applyWhen
								$xnyo->filter_get_var('applyWhen','text');
								$applyWhen = $_GET['applyWhen'];
								switch ($applyWhen) {
									case 'bpEnd':
										$invoice = new Invoice($db);
										$currentInvoice = $invoice->getCurrentInvoice($customerID);
										if ($currentInvoice['periodEndDate'] == NULL) {
											$firstInvoice = $invoice->getInvoiceWhenTrialPeriod($customerID);
											$dateWhenNewPlanWillBeImplemented = $firstInvoice['periodStartDate'];											
										} else {
											$dateWhenNewPlanWillBeImplemented = $currentInvoice['periodEndDate'];
										}																	
										//$dateWhenNewPlanWillBeImplemented = substr($currentInvoice['periodEndDate'],0,10);	//	maybe +1 day?										
										break;
									case 'asap':										
										$dateWhenNewPlanWillBeImplemented = "ASAP";
										break;
									default:
										header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");
									break;
								}			
								
								if($currentBillingPlan['type'] != $newBillingDetails['type'] and $applyWhen == "asap")
								{
									$notification = "New type of billing plan from <b>{$currentBillingPlan['type']}</b> to <b>{$newBillingDetails['type']}</b> as soon as posible (ASAP). <b>All current modules will be canceled!</b>";
								}
								
								$smarty->assign("notification",$notification);
								$smarty->assign("applyWhen",$applyWhen);																	
								$smarty->assign("dateWhenNewPlanWillBeImplemented",$dateWhenNewPlanWillBeImplemented);
								
								$areYouSureAction = "change billing plan";
								$from = $currentBillingPlan;
								$to = $newBillingDetails;
								
								$title = "Change billing plan";
								setTitle($title, $smarty);																								
								
								$smarty->assign("areYouSureAction",$areYouSureAction);
								
								$smarty->assign("from",$from);
								$smarty->assign("to",$to);																		
							
								$smarty->assign("category","billing");
								$smarty->assign("currentBookmark",$_GET["action"]);
								$smarty->display("tpls:vps.tpl");
								
								break;
						}											
												
						break;											
											
					case "myInfo":					
									
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
						$xnyo->filter_get_var('fax', 'text');
						
						
						
						$userDetails = array (
							'user_id'			=> $userID,							
							'firstName'			=> $_GET['firstName'],
							'lastName' 			=> $_GET['lastName'],
							'currency_id'		 => $_GET['currency_id'],
							'secondary_contact' => $_GET['secondaryContact'],
							'email'				=> $_GET['email'],
							'company_id' 		=> $_GET['companyID'],
							'facility_id' 		=> "NULL",
							'department_id' 	=> "NULL",					
							'address1' 			=> $_GET['address1'],
							'address2'			=> $_GET['address2'],
							'city'				=> $_GET['city'],
							'state_id'			=> $_GET['state'],
							'zip'				=> $_GET['zip'],
							'country_id'		=> $_GET['country'],
							'phone'				=> $_GET['phone'],
							'fax'				=> $_GET['fax']										
						);
						
						$billing = new Billing($db);

						$db->beginTransaction();
						
						$curentCurrency = $billing->getCurrencyByCustomer($userDetails['company_id']);
						$newCurrency = $billing->getCurrencyDetails($userDetails['currency_id']);
						
						if($curentCurrency['iso'] != $newCurrency['iso']) /*Currency changed. Recount ballance in new currency*/
						{
							$invoice = new Invoice($db);
							$balance = $invoice->getBalance($userDetails['company_id']);
							
							$convertor = new CurrencyConvertor();
							
							$newBallance = $convertor->Sum(array($curentCurrency['iso'] => $balance), $newCurrency['iso']);
							
							$newBallance = round($newBallance,2);
							
							$convertStatus = "<br/>Your ballance has been converted from <b>{$curentCurrency['iso']} $balance</b> to <b>{$newCurrency['iso']} = $newBallance</b>";
							
							$query = "UPDATE " . TB_VPS_CUSTOMER . " SET balance = $newBallance WHERE customer_id = {$userDetails['company_id']}";
							$db->query($query);
						}
						
						$user->setUserDetails($userDetails);
						
						$billing->setCurrency($userDetails['company_id'], $userDetails['currency_id']);
						
						
						
						$db->commitTransaction();
						
						$smarty->assign("message","Your user information is successfully edited." . $convertStatus);
						
						showMyInfo($db,$smarty,$userDetails);						
						break;
				}
								
				break;
				
				
			case "confirmEdit":							
				$xnyo->filter_get_var('category', 'text');
				$category = $_GET['category'];
				
				switch ($category) {
					
					case "payInvoice": /*Оплата с текущего баланса*/
						
						$invoice = new Invoice($db);
						$idata = $invoice->getInvoiceDetails($_GET['invoiceID']);
						$balance = $invoice->getBalance($idata['customerID']);
						
						
						if(($balance - $idata['total']) < 0)
						{
							header ("Location: /voc_src/vwm/vps.php?action=viewList&category=invoices&subCategory=Due&error=no_enough_money");
						}
						else /*Все хорошо, денег на оплату хватает*/
						{
							$db->beginTransaction();
							$invoice->payInvoiceFromBalance($idata);
							$db->commitTransaction();
							header ("Location: /voc_src/vwm/vps.php?action=viewList&category=invoices&subCategory=Due");
						}
						
					break;
					
					case "billing":
						$xnyo->filter_post_var('subCategory','text');
						$subCategory = $_POST['subCategory'];
						
						switch ($subCategory) {
							case "MSDSLimit":
								$xnyo->filter_post_var('plusTo','int');
								$plusToValue = $_POST['plusTo'];
								 
								$billing = new Billing($db);
								
								$currency = $billing->getCurrencyByCustomer($customerID);
								
								$limitName = 'MSDS';
								$billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue,$currency['id']);

								header("Location: vps.php?action=viewDetails&category=dashboard");
								break;
								
							case "memoryLimit":
								$xnyo->filter_post_var('plusTo','int');
								$plusToValue = $_POST['plusTo'];
								
								$billing = new Billing($db);
								
								$currency = $billing->getCurrencyByCustomer($customerID);
								
								$limitName = 'memory';
								$billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue,$currency['id']);
																
								header("Location: vps.php?action=viewDetails&category=dashboard");
								break;
							case "modules":
								$billing = new Billing($db);
								if (isset($_POST['total']) && $_POST['total'] == 'delete') {
									if ($_POST['status'] == 'delete_all') {
										$plans = $billing->getPurchasedModule($customerID,$_POST['module_id'],'today&future');
										foreach ($plans as $plan) {
											$billing->removeModuleBillingPlan($customerID,$plan['id']);
										}
									} elseif ($_POST['status'] == 'delete_plan') {
										$billing->removeModuleBillingPlan($customerID,$_POST['plan_id']);
									}
								} else {
									$plans = json_decode($_POST['changeTo']);
									$start_date = $_POST['startDate'];
									$billing->applyModuleBillingPlan($customerID,$plans,$start_date); //plans is array of module plans id!
								}
								//echo "<a href='vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan'>next</a>";
								header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
								break;	
							default:
								$xnyo->filter_post_var('changeTo', 'text');
								$xnyo->filter_post_var('applyWhen', 'text');
							
								if ($_POST['applyWhen'] == 'bpEnd' || $_POST['applyWhen'] == 'asap') {
									$applyWhen = $_POST['applyWhen'];
									//not expected variable. fraud?	
								} else {
									break;
								}
								$newBillingPlanID = $_POST['changeTo'];
								
								$billing = new Billing($db);

								$currentCurrency = $billing->getCurrencyByCustomer($customerID);
								$newBillingDetails = $billing->getBillingPlanDetails($newBillingPlanID, false, $currentCurrency['id']);
								$currentBillingPlan = $billing->getCustomerPlan($customerID);	
								//$db->beginTransaction();
								
								if($newBillingDetails['type'] != $currentBillingPlan['type'] and $applyWhen == "asap")
								{
									$appliedModules = $billing->getPurchasedModule($customerID,null,'today&future','today',$currentCurrency['id']);
									//echo "CANCEL ALL MODULES";
									var_dump($appliedModules);
									foreach($appliedModules as $module)
									{
										$billing->removeModuleBillingPlan($customerID,$module['id']);
										//echo "<br/>module {$module['id']} deleted<br/>";
									}
								}
								
								
								
								$result = $billing->setScheduledPlan($customerID, $newBillingPlanID, $applyWhen);
								
								//echo "<a href='vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan'>next</a>";
								//exit;
								
								header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");

								break;
						}												
						
					break;
						
				}
				
				break;
				
				
				
			case "addUser":			
				$xnyo->filter_get_var('step', 'text');
				
				switch ($_GET['step']) {
					
					case "first":	
						
//						if (isset($_SESSION['userDetails'])) {
//							//new user registration flag
//							$newUserRegistration = true;
//							$smarty->assign("newUserRegistration",$newUserRegistration);									
//						var_dump($_SESSION['userDetails']);	
//							showAvailableBillingPlans($db, $smarty);
//							break;
//						} 
						$xnyo->filter_get_var('accessname', 'text');
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
						$xnyo->filter_get_var('fax', 'text');
						
						$userDetails = array (
							'accessname' 		=> $_GET['accessname'],
							'password' 			=> $_GET['password'],
							'accesslevel_id' 	=> $_GET['accessLevelID'],
							'firstName'			=> $_GET['firstName'],
							'lastName' 			=> $_GET['lastName'],
							'secondary_contact' => $_GET['secondaryContact'],
							'email'				=> $_GET['email'],
							'company_id' 		=> $_GET['companyID'],
							'facility_id' 		=> "NULL",
							'department_id' 	=> "NULL",					
							'address1' 			=> $_GET['address1'],
							'address2'			=> $_GET['address2'],
							'city'				=> $_GET['city'],
							'state_id'			=> $_GET['state'],
							'zip'				=> $_GET['zip'],
							'country_id'		=> $_GET['country'],
							'phone'				=> $_GET['phone'],
							'fax'				=> $_GET['fax'],
							'currency_id'		=> $_GET['currency_id']												
						);
						
						//check if company already added
						if ($user->ifCustomerExist($userDetails['company_id'])) {
							$userDetails['accesslevel_id'] = 1;
							
							$user->addUser($userDetails);
							
							$smarty->assign("status", "userAdded");
							$smarty->display("tpls:authorization.tpl");
							
						} else {
							if (isset($_GET['accessname'])) {
								//	refresh user data
								$_SESSION['userDetails'] = $userDetails;	
							}
														
							//new user registration flag
							$newUserRegistration = true;
							$smarty->assign("newUserRegistration",$newUserRegistration);									
							
							showAvailableBillingPlans($db, $smarty, $_GET['currency_id']);
							
						}		
						
						break;
					
					//chose billing plan	
					case "second":
						$xnyo->filter_get_var('selectedBillingPlan', 'int');
						
						if (empty($_GET['selectedBillingPlan'])) {
							//new user registration flag
							$newUserRegistration = true;
							$smarty->assign("newUserRegistration",$newUserRegistration);									
							
							showAvailableBillingPlans($db, $smarty);
							
						} else {
							$selectedBillingPlanID = $_GET['selectedBillingPlan'];
							
							$billing = new Billing($db);
							$customerPlan = $billing->getBillingPlanDetails($selectedBillingPlanID, false, $_SESSION['userDetails']['currency_id']);
							
							foreach ($customerPlan['limits'] as $limit=>$value) {
								if ($value['default_limit'] == $value['max_value']) {
									$customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type']." (free)"; 
								} else {
									$customerPlan['limits'][$limit]['max_value'] .= " ".$value['unit_type'];
								}		
							}								
							$invoice = new Invoice($db);
							
							$totalInvoiceSimple = $invoice->calculateTotal($customerPlan['one_time_charge'], $customerPlan['price']);
							$totalInvoice = number_format($totalInvoiceSimple,2);
													
																																														
							$smarty->assign("billingPlan",$customerPlan);
							$smarty->assign("totalInvoice", $totalInvoice);	
							$smarty->assign("totalInvoiceSimple",$totalInvoiceSimple);
							$_SESSION['selectedBillingPlan'] = $customerPlan['billingID'];
							
							//new user registration flag
							$newUserRegistration = true;
							$smarty->assign("newUserRegistration",$newUserRegistration);
							
							//Apply to smarty Selected Modules
							$plans = json_decode($_POST['changeTo']);
							
							$selectedModules = getSelectedModulesfromGET($db,$_GET['currencyID']);
							
							$totalPrice = 0;
							foreach($selectedModules as $m) // Count total price
							{
								$totalPrice += $m['price'];
							}
							$smarty->assign('totalModulesPrice',$totalPrice);
							$smarty->assign('totalModulesPriceFormat',number_format($totalPrice,2));
							$smarty->assign('totalInvoiceForAllFormat',number_format($totalPrice + $totalInvoiceSimple,2));
							
							$_SESSION['selectedModules'] = $selectedModules;
							$smarty->assign('appliedModules',$selectedModules);
							$smarty->assign('isRegistration',true);
							
							$currencyDetails = $billing->getCurrencyDetails($_SESSION['userDetails']['currency_id']);							
							$smarty->assign("currentCurrency", $currencyDetails);
							
							$title = "Please confirm";
							setTitle($title, $smarty);
									
							$smarty->assign("currentBookmark","MyBillingPlan");	
							$smarty->assign("category","billing");
							$smarty->display("tpls:vps.tpl");							
						}
						break;
						
					case "third":
						$xnyo->filter_post_var("registrationAction");
						if ($_POST['registrationAction'] == "Save") {
							
							/**
							 * REGISTER USER
							 */
							$selectedBillingPlanID = $_SESSION['selectedBillingPlan'];
							$selectedModules = $_SESSION['selectedModules'];	

							$currencyID = $_POST['currencyID'];;
							
							$multiInvoiceData = prepareModulesForMultiInvoice($selectedModules,$selectedBillingPlanID,$db,$currencyID);
			
							$userDetails = $_SESSION['userDetails'];														
							if(!$userDetails) {
								throw new Exception('User data lost during registration. Please try to register one more time.');
							}
							
							
							//	START TRANSACTION
							$db->beginTransaction();
							
							$userID = $user->addUser($userDetails);		
							
							if(!$userID or $userID == 0) {
								//addUser fail
								throw new Exception('User::addUser(); failed. Please try to register one more time.');
							}	 		
							
							$billing = new Billing($db);																					
							$billing->addCustomerPlan($userDetails['company_id'], $selectedBillingPlanID);							
							$billing->setCurrency($userDetails['company_id'], $userDetails['currency_id']);
							
							$vps2voc = new VPS2VOC($db);
							$customerDetails = $vps2voc->getCustomerDetails($userDetails['company_id']);					
							
							$invoice = new Invoice($db);
												
							//$invoice->createInvoiceForBilling($userDetails['company_id'], $customerDetails['trial_end_date'], $selectedBillingPlanID);
							$invoice->createMultiInvoiceForNewCustomer($userDetails['company_id'],
																	$customerDetails['trial_end_date'],
																	$selectedBillingPlanID,$multiInvoiceData); // Create Multy invoice
																								
							//	COMMIT TRANSACTION								
							$db->commitTransaction();
							
							
							unset($_SESSION['userDetails']);
							unset($_SESSION['registration']);
							unset($_SESSION['selectedBillingPlan']);
							
							if ($authResult = $user->authorize($userDetails['accessname'], $_SESSION['originalPassword'])) {
								unset($_SESSION['originalPassword']);
												
								//	Redirect user to dashboard
								session_start();	
									
								$_SESSION['userID']=$user->getUserIDbyAccessname($userDetails['accessname']);
								$_SESSION['accessname']=$userDetails['accessname'];					
								$accessLevel=$user->getUserAccessLevel($_SESSION['userID']);
								$_SESSION['accessLevel']=$accessLevel;
								$_SESSION['customerID'] = $user->getCustomerIDbyUserID($_SESSION['userID']);
								
								header("Location: vps.php?action=viewDetails&category=dashboard");
								//echo "<br/><a href='vps.php?action=viewDetails&category=dashboard' target='_blank'>redirect</a>";							
							} else {							
								header ('Location: vps.php');
							}
						} elseif ($_POST['registrationAction'] == "Cancel") {
							header ('Location: vps.php?action=addUser&step=first');
						}
						break;
				}											
				
				break;
				
				
				
				
			case "contactAdmin":
				$xnyo->filter_post_var('contactAdminAction','text');
				$contactAdminAction = $_POST['contactAdminAction'];
				
				if (isset($_SESSION['registration'])) {
					$newUserRegistration = true;
					$smarty->assign("newUserRegistration",$newUserRegistration);
				}
				
				$billing = new Billing($db);
				
				switch ($contactAdminAction) {
					case "Send":												
						$xnyo->filter_post_var('bplimit','int');
						$xnyo->filter_post_var('monthsCount','int');
						$xnyo->filter_post_var('type','text');
						$xnyo->filter_post_var('MSDSDefaultLimit','text');
						$xnyo->filter_post_var('memoryDefaultLimit','text');
						$xnyo->filter_post_var('description','text');
																								
						//validation
						$intArray = array ('bplimit','monthsCount','MSDSDefaultLimit','memoryDefaultLimit');
						foreach ($intArray as $value) {
							$problem[$value] = ((preg_match('/^[0]*(\d+)$/',$_POST[$value],$matches)) ? false : true);
							$request[$value] = ($matches[1]) ? $matches[1] : $_POST[$value];	
						}
						$request['type'] = $_POST['type'];
						if ($_POST['type'] == 'self' || $_POST['type'] == 'gyant') {
							$problem['type'] = false;							 
						} else {
							$problem['type'] = true;							
						}
						$request['description'] = $_POST['description'];
						$request['date'] = date('Y-m-d'); 
						
						$totalProblem = false;
						foreach ($problem as $problemWithInput) {
							if ($problemWithInput) {
								$totalProblem = true;
								break;
							}
						}
						
						if (!$totalProblem) {
							//save user if registration
							if ($newUserRegistration) {
								$userDetails = $_SESSION['userDetails'];							
								$user->addUser($userDetails);
								
								$billing = new Billing($db);
								$billing->addCustomerPlan($userDetails['company_id']);				 																
								
								unset($_SESSION['userDetails']);
								unset($_SESSION['registration']);
								unset($_SESSION['selectedBillingPlan']);
											
								if ($authResult = $user->authorize($userDetails['accessname'], $_SESSION['originalPassword'])) {
									unset($_SESSION['originalPassword']);
									
									//	Redirect user to dashboard
									session_start();	
									
									$_SESSION['userID']=$user->getUserIDbyAccessname($userDetails['accessname']);
									$_SESSION['accessname']=$userDetails['accessname'];					
									$accessLevel=$user->getUserAccessLevel($_SESSION['userID']);
									$_SESSION['accessLevel']=$accessLevel;
									$_SESSION['customerID'] = $user->getCustomerIDbyUserID($_SESSION['userID']);
									$customerID = $_SESSION['customerID']; 																
								}		
							}
							
							$request['customerID'] = $customerID;
							$billing->saveDefinedBillingPlanRequest($request);
							header("Location: vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan");
								
						} else {
							$_SESSION['problems'] = $problem;
							$_SESSION['request'] = $request;
							header ('Location: vps.php?action=contactAdmin');
						}																								
						break;
					case "Discard":
						if (!$newUserRegistration) {
							header("Location: vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans");	
						} else {
							header ('Location: vps.php?action=addUser&step=first');	
						}												
						break;
					default:						
						if (isset($_SESSION['request'])) {
							$request = $_SESSION['request'];
							$request['description'] = stripslashes($request['description']);
							$smarty->assign("request",$request);													
							$smarty->assign("problems",$_SESSION['problems']);						
							unset($_SESSION['request']);
							unset($_SESSION['problems']);						
						} 																		
						$smarty->assign("currentBookmark",$_GET["action"]);
						$smarty->assign("category","billing");
						$smarty->display("tpls:vps.tpl");
						break;					
				}								
				break;
				
			case "logout":
				$user->logout();
				break;
				
			case "ping":
				$invoice = new Invoice($db);
				var_dump($invoice->getCurrentInvoiceForModule(177, 15));
				break;
				
		}
	}
?>