<?php
	/**
	 * VOC WEB MANAGER PAYMENT SYSTEM Notificator/Cleaner script
	 *
	 * This script:
	 * 	-generates new Invoices;
	 * 	-changes Billing plan to scheduled;
	 * 	-sends notification emails to customers;
	 * 	-gets & deactivates customers who didn't pay for service in time.
	 * 	-cancel expired limit invoices
	 *
	 * ============ DAILY RUN ============
	 * */


	//	Do not apply changes to DB?
	define('DRY_RUN', false);


	if (!isset($inspectorFlag)) {
		chdir('../..');

		require('config/constants.php');
		require_once ('modules/xnyo/xnyo.class.php');

		$site_path = getcwd().DIRECTORY_SEPARATOR;
		define ('site_path', $site_path);

		//	Include Class Autoloader
		require_once('modules/classAutoloader.php');

		//	Start xnyo Framework
		require ('modules/xnyo/startXnyo.php');

		$db->select_db(DB_NAME);
	}

	$customerObj = new VPSUser($db);
	$invoiceObj = new Invoice($db);
	$billingObj = new Billing($db);
	$vps2voc = new VPS2VOC($db);
	$user = new User($db, null,null, null);
	$modSystem = new ModuleSystem($db);
	$config = $customerObj->loadConfigs($db);


	$currentDate = date('Y-m-d');
//	$currentDate = '2012-02-19';

	//	current Date in timestamp
	// I do not use time() to simplify debugging (test script with different current dates)
	$cDateArr = explode("-",$currentDate);
	$cDateInt = mktime(0,0,0,$cDateArr[1],$cDateArr[2],$cDateArr[0]) ;

	$customers = $customerObj->getCustomerList();


	foreach ($customers as $customer) {
		if (strtolower($customer['status']) == 'off') {
			// skip iteration
			continue;
		}

		/*
		 * remove modules
		 */
		//	TODO: validate
		$purchasedModules = $billingObj->getPurchasedPlansForCustomerView($customer['id']);
		foreach ($purchasedModules['modules'] as $purchasedModule) {
			$shouldDeactivateModule = true;
			$removeModuleBillingPlanIDs = array();

			if ($purchasedModule['status'] == 'active') {
				foreach ($purchasedModule['plans'] as $plan) {
					$sDateArr = explode("-",$plan['start']);
					$sDateInt = mktime(0,0,0,$sDateArr[1],$sDateArr[2],$sDateArr[0]) ;

					$eDateArr = explode("-",$plan['end']);
					$eDateInt = mktime(0,0,0,$eDateArr[1],$eDateArr[2],$eDateArr[0]) ;
					if ($cDateInt>=$sDateInt && $cDateInt<=$eDateInt) {
						$shouldDeactivateModule = false;
						break;
					}

					if ($cDateInt > $eDateInt) {
						$removeModuleBillingPlanIDs[] = $plan['id'];
					}

				}
			}
			if ($shouldDeactivateModule) {
				foreach ($removeModuleBillingPlanIDs as $removeModuleBillingPlanID) {
					if (!DRY_RUN) {
						$db->beginTransaction();
						$billingObj->removeModuleBillingPlan($customer['id'], $removeModuleBillingPlanID);
						$db->commitTransaction();
					}
				}
			}
		}



		$isBillingInvoice = false;
		if (false !== ($currentInvoices = getCurrentInvoice($customer['id']))) {
			/*
			 * Current invoice is defined - client is in BILLING PERIOD now
			 */

			//	save here already processed invoices
			$alreadyProcessedInvoices = array();

			foreach ($currentInvoices as $currentInvoice) {
				if (!$isBillingInvoice && !is_null($currentInvoice->billing_info)) {
					$isBillingInvoice = true;
				}

				if ($currentInvoice->status == 'paid') {
					/*
					 * Current invoice is paid. Maybe it's time to generate new one or activate module?
					 */
					$eDateArr = explode("-",$currentInvoice->period_end_date);
					$eDateInt = mktime(0,0,0,$eDateArr[1],$eDateArr[2],$eDateArr[0]) ;

					$sDateArr = explode("-",$currentInvoice->period_start_date);
					$sDateInt = mktime(0,0,0,$sDateArr[1],$sDateArr[2],$sDateArr[0]) ;


					//	module processing
					if(!is_null($currentInvoice->module_id) && $sDateInt <= $cDateInt) {
						$moduleName = $vps2voc->getModuleNameByID($currentInvoice->module_id);
						if (!$modSystem->searchModule2company($moduleName, $customer['id'])) {
							/*
							 * activate module
							 */
							if (!DRY_RUN) {
								$db->beginTransaction();
								$modSystem->setModule2company($moduleName, '1', $customer['id']);
								$db->commitTransaction();
							}
							echo "activate module<br>";
						} else {
							/*
							 * remove old customer2module links
							 */
							if (count($purchasedModules) > 1) {
								//	find module plan that we need to keep
								$purchasedModules = $billingObj->getPurchasedModule($customer['id'], $currentInvoice->module_id, 'all');
								$closest2todayTimestamp = 0;
								foreach ($purchasedModules as $key=>$purchasedModule) {
									$moduleStartDateArr = explode("-",$purchasedModule['start_date']);
									$purchasedModules[$key]['moduleStartDateArrInt'] = mktime(0,0,0,$moduleStartDateArr[1],$moduleStartDateArr[2],$moduleStartDateArr[0]) ;

									if ($purchasedModules[$key]['moduleStartDateArrInt'] > $closest2todayTimestamp && $purchasedModules[$key]['moduleStartDateArrInt'] <= $cDateInt) {
										$closest2todayTimestamp = $purchasedModules[$key]['moduleStartDateArrInt'];
										$modulePlanID2keep = $purchasedModule['id'];
									}
								}

								//	remove others
								foreach ($purchasedModules as $purchasedModule) {
									if ($purchasedModule['id'] != $modulePlanID2keep && $purchasedModule['moduleStartDateArrInt'] < $cDateInt) {
										echo "remove module billing plan ".$purchasedModule['id']."<br>";
										if (!DRY_RUN) {
											$db->beginTransaction();
											$billingObj->removeModuleBillingPlan($customer['id'], $purchasedModule['id']);
											$db->commitTransaction();
										}
									}
								}
							}
						}
					}


					$daysLeft = round(($eDateInt - $cDateInt)/86400);	//	86400 = 24hours*60sec*60min

					if ( $daysLeft <= 30) {
						$futureInvoice = getFutureInvoice($currentInvoice->customer_id);
						$fsDateArr = explode("-",$futureInvoice[0]->period_start_date);
						$fsDateInt = mktime(0,0,0,$fsDateArr[1],$fsDateArr[2],$fsDateArr[0]) ;

						if (date('Y-m-d',$eDateInt) <= date('Y-m-d',$fsDateInt - 86400)) {
							/*
							 * Invoice for future is already generated
							 */
							echo "invoice is already generated for this customer ".$customer['id']."<br>";
						} else {
							/*
							 * Generate invoice for future period
							 */
							if (!$alreadyProcessedInvoices[$currentInvoice->invoice_id]) {
								echo 'generate invoice '.$currentInvoice->invoice_id.'<br>';
								if (!DRY_RUN) {
									$db->beginTransaction();
									$log .= createInvoice($currentInvoice);
									$db->commitTransaction();
								}
							}
						}
					}


				} else {
					/*
					 * Current invoice is DUE. Check for suspension date
					 */
					$sDateArr = explode("-",$currentInvoice->suspension_date);
					$sDateInt = mktime(0,0,0,$sDateArr[1],$sDateArr[2],$sDateArr[0]) ;
					if ($cDateInt >= $sDateInt) {
						/*
						 * Suspend account
						 */
						if (!is_null($currentInvoice->billing_info)) {
							echo 'suspension '.$currentInvoice->invoice_id.'<br>';
							if (!DRY_RUN) {
								$log .=	manageCustomersAndNotify($customer['id'], -1);	//	deactivate customer
							}
						} elseif (!is_null($currentInvoice->module_id)) {

							echo 'Deactivate module '.$currentInvoice->invoice_id.'<br>';
							$moduleName = $vps2voc->getModuleNameByID($currentInvoice->module_id);

							if (!DRY_RUN) {
								$db->beginTransaction();
								$modSystem->setModule2company($moduleName, '0', $customer['id']);
								$invoiceObj->cancelInvoice($currentInvoice->invoice_id);
								$db->commitTransaction();
							}
							$log .= "Custom Invoice ID ".$currentInvoice->invoice_id." is canceled. Today = Suspension date.\n";
						} else {
							echo 'cancel '.$currentInvoice->invoice_id.'<br>';
							if (!DRY_RUN) {
								$db->beginTransaction();
								$invoiceObj->cancelInvoice($currentInvoice->invoice_id);
								$db->commitTransaction();
							}
							$log .= "Custom Invoice ID ".$currentInvoice->invoice_id." is canceled. Today = Suspension date.\n";
						}
					} else {
						/*
						 * Notify client
						 */
						$daysLeft = round(($sDateInt - $cDateInt)/86400);
						var_dump($daysLeft);
						echo 'notify '.$currentInvoice->invoice_id.'<br>';
						if (!DRY_RUN) {
							$log .= manageCustomersAndNotify($customer['id'], $daysLeft);
						}
					}
				}

				$alreadyProcessedInvoices[$currentInvoice->invoice_id] = true;
			}
		}

		if( !$isBillingInvoice && (false !== ($futureInvoices = getFutureInvoice($customer['id']))) ) {
			/*
			 * TRIAL PERIOD
			 */

			//	save here already processed invoices
			$alreadyProcessedInvoices = array();

			foreach ($futureInvoices as $futureInvoice) {

				if (!$isBillingInvoice && !is_null($futureInvoice->billing_info)) {
					$isBillingInvoice = true;
				}


				if ($alreadyProcessedInvoices[$futureInvoice->invoice_id]) {
					//skip
					continue;
				}


				if ($futureInvoice->status == 'paid') {
					continue;
				} else {
					$sDateArr = explode("-",$futureInvoice->suspension_date);
					$sDateInt = mktime(0,0,0,$sDateArr[1],$sDateArr[2],$sDateArr[0]) ;
					if ($cDateInt >= $sDateInt) {
						if ($currentInvoice->suspension_disable == 1) {
							echo 'suspension '.$futureInvoice->invoice_id.'<br>';
							if (!DRY_RUN) {
								$log .=	manageCustomersAndNotify($customer['id'], -1);	//	deactivate customer
							}
						} else {
							echo 'cancel '.$futureInvoice->invoice_id.'<br>';
							if (!DRY_RUN) {
								$db->beginTransaction();
								$invoiceObj->cancelInvoice($futureInvoice->invoice_id);
								$db->commitTransaction();
							}
							$log .= "Custom Invoice ID ".$futureInvoice->invoice_id." is canceled. Today = Suspension date.\n";
						}
					} else {
						$daysLeft = round(($sDateInt - $cDateInt)/86400);
						echo 'notify '.$futureInvoice->invoice_id.'<br>';
						if (!DRY_RUN) {
							$log .= manageCustomersAndNotify($customer['id'], $daysLeft);
						}
					}
				}

				$alreadyProcessedInvoices[$futureInvoice->invoice_id] = true;
			}
		}

		if (!$isBillingInvoice) {
			echo "no invoices<br>";
			if (!DRY_RUN) {
				$log .= manageCustomersAndNotify($customer['id'], 0);
			}
		}

	}

	if (!DRY_RUN) {
		$log .= checkLimitInvoices();
		$log .= checkCustomInvoices();

		$log .= date('Y-m-d H:i:s')."	Stopping notification script.\n";
		$log .= "\n\n";

		$handle = fopen('../voc_logs/vpsNotification.log', 'a');
		fwrite($handle, $log);
		fclose($handle);

		//save action to DB
		if (isset($inspectorFlag)) {
			$query = "INSERT INTO ".TB_VPS_NOTIFICATION_SCRIPT." (run_date, mode) VALUES ('".date('Y-m-d H:i:s')."', 'inspector')";
		} else {
			$query = "INSERT INTO ".TB_VPS_NOTIFICATION_SCRIPT." (run_date) VALUES ('".date('Y-m-d H:i:s')."')";
		}

		$db->exec($query);
	}








//	function loadConfig($db) {
//		$config = false;
//
//		$query = "SELECT * FROM ".TB_VPS_CONFIG;
//		$db->query($query);
//
//		if ($db->num_rows()) {
//			$numRows = $db->num_rows();
//			for ($i=0; $i < $numRows; $i++) {
//				$data=$db->fetch($i);
//				$config[$data->name] = $data->value;
//			}
//		}
//		return $config;
//	}
//
//

	function getCurrentInvoice($customerID) {
		global $db;
		global $currentDate;

		$query = "SELECT * " .
				"FROM ".TB_VPS_INVOICE." i, ".TB_VPS_INVOICE_ITEM." ii " .
				"WHERE i.invoice_id = ii.invoice_id " .
					"AND '".$currentDate."' >= i.period_start_date " .
					" AND '".$currentDate."' <= i.period_end_date " .
					" AND (ii.billing_info IS NOT NULL OR ii.module_id) " .
					" AND i.status <> 'canceled' " .
					" AND i.customer_id = ".$customerID." ";
		$db->query($query);

		return ($db->num_rows() > 0) ? $db->fetch_all() : false;
	}

	function getFutureInvoice($customerID) {
		global $db;
		global $currentDate;

		$query = "SELECT * FROM ".TB_VPS_INVOICE." WHERE " .
					" '".$currentDate."' < period_start_date " .
					" AND (billing_info IS NOT NULL OR module_id) " .
					" AND status <> 'canceled' " .
					" AND customer_id = ".$customerID." ";

		$query = 'SELECT * ' .
				'FROM '.TB_VPS_INVOICE.' i, '.TB_VPS_INVOICE_ITEM.' ii ' .
				'WHERE i.invoice_id = ii.invoice_id ' .
				"AND '".$currentDate."' < i.period_start_date " .
				'AND (ii.billing_info IS NOT NULL OR ii.module_id) ' .
				"AND i.status <> 'canceled' " .
				"AND i.customer_id = ".$customerID." ";

		$db->query($query);
		return ($db->num_rows() > 0) ? $db->fetch_all() : false;
	}



	function createInvoice(StdClass $invoice) {
		global $db;
		global $config;
		global $currentDate;

		$invoiceObj = new Invoice($db);
		$invoiceObj->currentDate = $currentDate;
		$billingObj = new Billing($db);
		$billingObj->currentDate = $currentDate;

		$currentCurrency = $billingObj->getCurrencyByCustomer($invoice->customer_id);

		$invoiceDetails = $invoiceObj->getInvoiceItemsDetails($invoice->invoice_id);
		$multiInvoiceData = array(
			'billingID'				=> false,
			'appliedModules' 		=> array(),
			'not_approach_modules' 	=> array(),
		);

		foreach ($invoiceDetails['invoice_items'] as $invoiceItem) {
			if (!is_null($invoiceItem['billingInfo'])) {
				//	create new invoice
				$log = date('Y-m-d H:i:s')."		Checking scheduled billing plan for customer ".$invoice->customer_id.".\n";

				//	check scheduled billing plan, if no then apply current billing plan
				$scheduledBillingPlan = $billingObj->getScheduledPlanByCustomer($invoice->customer_id);

				if ($scheduledBillingPlan) {
					//	TODO: scheduled modules may not work
					$log .= date('Y-m-d H:i:s')."		Scheduled billing plan for customer ".$invoice->customer_id." is found. Billing plan ID = ".$scheduledBillingPlan['billingID'].".\n";
					$log .= date('Y-m-d H:i:s')."		Changing billing plan for customer ".$invoice->customer_id." to billing plan ".$scheduledBillingPlan['billingID'].".\n";
					$billingObj->setCustomerPlan($invoice->customer_id, $scheduledBillingPlan['billingID']); //apply new plan

					$log .= date('Y-m-d H:i:s')."		Deleting billing plan from schedule. ID=".$scheduledBillingPlan['id']."\n";
					$billingObj->deletePlanFromSchedule($scheduledBillingPlan['id']);
				}

				$billingPlan = $billingObj->getCustomerPlan($invoice->customer_id);
				$multiInvoiceData['billingID'] = $billingPlan['billingID'];
			}

			if (!is_null($invoiceItem['moduleID'])) {
				$currentModuleBP = $billingObj->getPurchasedModule($invoice->customer_id, $invoiceItem['moduleID'], $invoiceType = 'todayOnly', $period = 'today', $currentCurrency['id']);
				if (!$currentModuleBP || count($currentDate) == 0) {
					throw new Exception('Billing::getPurchasedModule says that no modules purchased. Probably this is lie.');
				}
				$multiInvoiceData['appliedModules'][] = $currentModuleBP[0];
			}
		}

		$log .= date('Y-m-d H:i:s')."		Creating new invoice for customer ".$invoice->customer_id.".\n";
		$newInvoicePeriodStartDate = date('Y-m-d',strtotime($invoice->period_end_date)+86400); // +1 day

		if ($multiInvoiceData['billingID']) {
			$invoiceData = $invoiceObj->createMultiInvoiceForNewCustomer($invoice->customer_id, $newInvoicePeriodStartDate, $billingPlan['billingID'], $multiInvoiceData);
		} else {
			foreach ($multiInvoiceData['appliedModules'] as $module) {
				$invoiceData = $invoiceObj->createInvoiceForModule($invoice->customer_id, $newInvoicePeriodStartDate, $currentModuleBP[0]['id']);
			}
		}

//		if (!is_null($invoiceDetails['billingInfo'])) {
//
//			//create new invoice
//			$log = date('Y-m-d H:i:s')."		Checking scheduled billing plan for customer ".$invoice->customer_id.".\n";
//
//			//	check scheduled billing plan,
//			// 	if no then apply current billing plan
//
//			$scheduledBillingPlan = $billingObj->getScheduledPlanByCustomer($invoice->customer_id);
//
//			if ($scheduledBillingPlan) {
//
//				$log .= date('Y-m-d H:i:s')."		Scheduled billing plan for customer ".$invoice->customer_id." is found. Billing plan ID = ".$scheduledBillingPlan['billingID'].".\n";
//
//				$log .= date('Y-m-d H:i:s')."		Changing billing plan for customer ".$invoice->customer_id." to billing plan ".$scheduledBillingPlan['billingID'].".\n";
//				$billingObj->setCustomerPlan($invoice->customer_id, $scheduledBillingPlan['billingID']); //apply new plan
//
//				$log .= date('Y-m-d H:i:s')."		Deleting billing plan from schedule. ID=".$scheduledBillingPlan['id']."\n";
//				$billingObj->deletePlanFromSchedule($scheduledBillingPlan['id']);
//
//			}
//
//			$billingPlan = $billingObj->getCustomerPlan($invoice->customer_id);
//
//			$log .= date('Y-m-d H:i:s')."		Creating new invoice for customer ".$invoice->customer_id.".\n";
//			$newInvoicePeriodStartDate = date('Y-m-d',strtotime($invoice->period_end_date)+86400); // +1 day
//			foreach ($invoiceDetails['modules'] as $module) {
//				//$modulesDetails[] = $billingObj-
//			}
//			$multiInvoiceData = array(
//    			'billingID' => $billingPlan['billingID'],
//    			'appliedModules' => array (
//    				array(
//    					'id' 			=> '20',
//          				'month_count' 	=> '6',
//          				'price' 		=> '800.00',
//          				'module_id' 	=> '12',
//          				'type' 			=> 'self',
//          				'module_name'	=> 'carbon_footprint',
//    				),
//    			),
//    			'not_approach_modules' => array(),
//    		);
//			//$invoiceData = $invoiceObj->createInvoiceForBilling($invoice->customer_id, $newInvoicePeriodStartDate,$billingPlan['billingID']); //creating new invoice
//			$invoiceData = $invoiceObj->createMultiInvoiceForNewCustomer($invoice->customer_id, $newInvoicePeriodStartDate, $billingPlan['billingID']);
//		} else {
//			//	tmp
//			if ($invoice->invoice_id == 206) return false;
//
//			$currentModuleBP = $billingObj->getPurchasedModule($invoice->customer_id, $invoice->module_id);
//			$newInvoicePeriodStartDate = date('Y-m-d',strtotime($invoice->period_end_date)+86400); // +1 day
//			$invoiceData = $invoiceObj->createInvoiceForModule($invoice->customer_id, $newInvoicePeriodStartDate, $currentModuleBP[0]['id']);
//		}

		$log .= date('Y-m-d H:i:s')."		New invoice added:\n" .
			"					customer ID: ".$invoiceData['customerID']."\n " .
			"					amount: \$ ".$invoiceData['amount']."\n " .
			"					discount: \$ ".$invoiceData['discount']."\n " .
			"					paid: \$ ".$invoiceData['paid']."\n " .
			"					due: \$ ".$invoiceData['due']."\n " .
			"					generation date: ".$invoiceData['generationDate']."\n " .
			"					period start date: ".$invoiceData['periodStartDate']."\n " .
			"					period end date: ".$invoiceData['periodEndDate'].".\n";
		//send first e-mail
		$email = new EMail();

		$from = VPS_SENDER_EMAIL;
		$to = getCustomerEmail($invoice->customer_id);
		$subject = $config['invoice_generation_email_subject'];

		$message = $config['invoice_generation_email_message']."\n";
		$message .= "customer ID: ".$invoiceData['customerID']."\n " .
			"amount: \$ ".$invoiceData['amount']."\n " .
			"discount: \$ ".$invoiceData['discount']."\n " .
			"paid: \$ ".$invoiceData['paid']."\n " .
			"due: \$ ".$invoiceData['due']."\n " .
			"generation date: ".$invoiceData['generationDate']."\n " .
			"period start date: ".$invoiceData['periodStartDate']."\n " .
			"period end date: ".$invoiceData['periodEndDate'].".\n";

		$log .= "Sending e-mail.\n";

		$email->sendMail($from, $to, $subject, $message);

		return $log;
	}


	function checkLimitInvoices() {
		global $db;
		global $config;
		global $currentDate;

		$log = "";
		$invoice = new Invoice($db);

		$query = "SELECT invoice_id " .
				"FROM ".TB_VPS_INVOICE." " .
				"WHERE limit_info IS NOT NULL " .
				"AND suspension_date <= '".$currentDate."' " .
				"AND status = 'due'";

		$query = 'SELECT invoice_id ' .
				'FROM '.TB_VPS_INVOICE.' i, '.TB_VPS_INVOICE_ITEM.' ii ' .
				'WHERE i.invoice_id = ii.invoice_id ' .
				"AND i.suspension_date <= '".$currentDate."' " .
				'AND ii.limit_info IS NOT NULL ' .
				"AND i.status = 'due' ";

		$db->query($query);

		if ($db->num_rows() > 0) {
			$invoices = $db->fetch_all();
			foreach ($invoices as $expiredInvoice) {
				$db->beginTransaction();
				$invoice->cancelInvoice($expiredInvoice->invoice_id);
				$db->commitTransaction();
				$log .= "Limit Invoice ID ".$expiredInvoice->invoice_id." is canceled. Today = Suspension date.\n";
			}
		}

		return $log;
	}




	function checkCustomInvoices() {
		global $db;
		global $config;
		global $currentDate;

		$log = "";

		$query = "SELECT i.invoice_id, i.customer_id, i.suspension_disable, DATEDIFF(i.suspension_date, '".$currentDate."') days_left " .
				"FROM ".TB_VPS_INVOICE." i, ".TB_VPS_CUSTOMER." c " .
				"WHERE c.customer_id = i.customer_id " .
				"AND i.custom_info IS NOT NULL " .
				"AND c.status = 'on' " .
				"AND i.status = 'due'";

		$query = "SELECT i.invoice_id, i.customer_id, i.suspension_disable, DATEDIFF(i.suspension_date, '".$currentDate."') days_left " .
				'FROM '.TB_VPS_INVOICE.' i, '.TB_VPS_INVOICE_ITEM.' ii, '.TB_VPS_CUSTOMER.' c ' .
				'WHERE i.invoice_id = ii.invoice_id ' .
				'AND c.customer_id = i.customer_id ' .
				"AND ii.custom_info IS NOT NULL " .
				"AND c.status = 'on' " .
				"AND i.status = 'due'";

		$db->query($query);

		if ($db->num_rows() > 0) {
			$dueInvoices = $db->fetch_all();
			$invoice = new Invoice($db);
			foreach ($dueInvoices as $expiredInvoice) {
				if ($expiredInvoice->suspensionDisable == 1) {
					$log .= manageCustomersAndNotify($expiredInvoice->customer_id, $expiredInvoice->days_left);
				} else {
					if ($expiredInvoice->days_left < 0) {
						$db->beginTransaction();
						$invoice->cancelInvoice($expiredInvoice->invoice_id);
						$db->commitTransaction();
						$log .= "Custom Invoice ID ".$expiredInvoice->invoice_id." is canceled. Today = Suspension date.\n";
					}
				}
			}
		}

		return $log;
	}



	function manageCustomersAndNotify($customerID, $daysLeft) {
		global $db;
		global $config;
		$log = "";

		$email = new EMail($db);

		$from = VPS_SENDER_EMAIL;
		$to = getCustomerEmail($customerID);

		switch (true) {
			case ($daysLeft <= 0):	//not VOC-WEB-MANAGER user any more!
				echo "Give us our money, you bastard!\n";

				$subject = $config['deacivate_email_subject'];
				$message = $config['deacivate_email_message'];
				$email->sendMail($from, $to, $subject, $message);

				$log .= "Customer ".$customerID." is not a customer now. They didn't pay.\n";

				$customer = new VPSUser($db);
				$db->beginTransaction();
				$customer->deactivateCustomer($customerID, "off");
				$db->commitTransaction();
				break;
			case ($daysLeft == $config['first_notification_period']):
				//send second e-mail
				$subject = $config['first_notification_email_subject'];
				$message = $config['first_notification_email_message'];
				$log .= "Sending e-mail.\n";

				$email->sendMail($from, $to, $subject, $message);
				break;
			case ($daysLeft == $config['second_notification_period']):
				//send third e-mail
				$subject = $config['second_notification_email_subject'];
				$message = $config['second_notification_email_message'];

				$log .= "Sending e-mail.\n";

				$email->sendMail($from, $to, $subject, $message);
				break;
		}
		return $log;
	}


	function getCustomerEmail($customerID) {
		global $db;

		$vps2voc = new VPS2VOC($db);
		$customerDetails = $vps2voc->getCustomerDetails($customerID);
		return $customerDetails["email"];

	}