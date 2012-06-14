<?php

	/**
	 * VOC WEB MANAGER PAYMENT SYSTEM Notificator script
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

	if (!isset($inspectorFlag)) {
		chdir('../..');

		require('config/constants.php');
		require_once ('modules/xnyo/xnyo.class.php');

		$xnyo = new Xnyo;

		$xnyo->database_type	= DB_TYPE;
		$xnyo->db_host 			= DB_HOST;
		$xnyo->db_user			= DB_USER;
		$xnyo->db_passwd		= DB_PASS;

		$xnyo->start();
	}

	include("modules/classes/Invoice.class.php");
	include("modules/classes/Payment.class.php");
	include("modules/classes/Billing.class.php");
	include("modules/classes/EMail.class.php");
	include("modules/classes/VPSUser.class.php");
	include("modules/classes/Bridge.class.php");


	function loadConfig($db) {
		$query = "SELECT * FROM ".TB_VPS_CONFIG;
		$db->query($query);

		if ($db->num_rows()) {
			$numRows = $db->num_rows();
			for ($i=0; $i < $numRows; $i++) {
				$data=$db->fetch($i);
				$config[$data->name] = $data->value;
			}
		}

		return $config;
	}

	function manageCustomersAndNotify($customerID, $daysLeft, $config, $db) {
		$log = "";

		$email = new EMail($db);

		$from = VPS_SENDER_EMAIL;
		$to = getCustomerEmail($customerID,$db);

		switch (true) {
			case ($daysLeft <= 0):	//not VOC-WEB-MANAGER user any more!

				$subject = $config['deacivate_email_subject'];
				$message = $config['deacivate_email_message'];
				$email->sendMail($from, $to, $subject, $message);

				$log .= "Customer ".$customerID." is not a customer now. They didn't pay.\n";

				$customer = new VPSUser($db);
				$customer->deactivateCustomer($customerID, "off");
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

	function getCustomerEmail($customerID, $db) {

		$vps2voc = new VPS2VOC($db);
		$customerDetails = $vps2voc->getCustomerDetails($customerID);
		return $customerDetails["email"];

	}

	function createInvoice(StdClass $invoice, $db, $config) {

		$invoiceObj = new Invoice($db);
		$billingObj = new Billing($db);

		//create new invoice
		$log = date('Y-m-d H:i:s')."		Checking scheduled billing plan for customer ".$invoice->customer_id.".\n";

		//	check scheduled billing plan,
		// 	if no then apply current billing plan

		$scheduledBillingPlan = $billingObj->getScheduledPlanByCustomer($invoice->customer_id);

		if ($scheduledBillingPlan) {

			$log .= date('Y-m-d H:i:s')."		Scheduled billing plan for customer ".$invoice->customer_id." is found. Billing plan ID = ".$scheduledBillingPlan['billingID'].".\n";

			$log .= date('Y-m-d H:i:s')."		Changing billing plan for customer ".$invoice->customer_id." to billing plan ".$scheduledBillingPlan['billingID'].".\n";
			$billingObj->setCustomerPlan($invoice->customer_id, $scheduledBillingPlan['billingID']); //apply new plan

			$log .= date('Y-m-d H:i:s')."		Deleting billing plan from schedule. ID=".$scheduledBillingPlan['id']."\n";
			$billingObj->deletePlanFromSchedule($scheduledBillingPlan['id']);

		}

		$billingPlan = $billingObj->getCustomerPlan($invoice->customer_id);

		$log .= date('Y-m-d H:i:s')."		Creating new invoice for customer ".$invoice->customer_id.".\n";
		$newInvoicePeriodStartDate = date('Y-m-d',strtotime($invoice->period_end_date)+86400); // +1 day
		$invoiceData = $invoiceObj->createInvoiceForBilling($invoice->customer_id, $newInvoicePeriodStartDate,$billingPlan['billingID']); //creating new invoice

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
		$to = getCustomerEmail($invoice->customer_id,$db);
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
	}	//end of createInvoice function

	function checkBillingInvoices($db,$config) {

		$log = "";

		$VPSUser = new VPSUser($db);
		$customers = $VPSUser->getCustomerList();
		$currentTime = time();
		foreach ($customers as $customer) {
			$query = "SELECT invoice_id " .
					"FROM ".TB_VPS_INVOICE." " .
					"WHERE customer_id = ".$customer['id']." " .
					"AND period_start_date = '".$customer['trial_end_date']."' " .
					"AND status = 'PAID'";
			$db->query($query);
			if ($db->num_rows() == 0) {
				//	trial period
				$daysLeft = ($trialEndTimestamp - $currentTime)*86400;
				if ($daysLeft >= 30 ) continue;

				$invoiceData = new StdClass;
				$invoiceData->customer_id = $customer['id'];
				$invoiceData->period_end_date = $customer['trial_end_date'];
				$invoiceData->days_left = $daysLeft;

				$invoicesData[] = $invoiceData;
			} else {
				$query = "SELECT customer_id, CURDATE() today, period_end_date, DATEDIFF(period_end_date, CURDATE()) days_left " .
						"FROM ".TB_VPS_INVOICE." " .
						"WHERE customer_id = ".$customer['id']." " .
						"AND status = 'PAID' " .
						"AND CURDATE() >= period_start_date " .
						"AND CURDATE() <= period_end_date " .
						"AND DATEDIFF(period_end_date, CURDATE()) < ".$config['invoice_generation_period'];

				$db->query($query);

				if ($db->num_rows() == 0) continue;
				$invoicesData = $db->fetch_all();
			}


			foreach ($invoicesData as $invoiceData) {
				$query = "SELECT * " .
						"FROM ".TB_VPS_INVOICE." " .
						"WHERE customer_id = ".$invoiceData->customer_id." " .
						"AND period_start_date >= '".$invoiceData->period_end_date."' " .
						"AND billing_info IS NOT NULL " .
						"AND status <> 'canceled'";
				$db->query($query);

				if (!$db->num_rows()) {	// here they are!
					$log .= createInvoice($invoiceData, $db, $config);
				} else {

					//	if invoice already created, but not paid then email notification
					$invoiceObj = new Invoice($db);

					//	check if paid
					$invoiceDetails = $invoiceObj->getInvoiceDetails($db->fetch(0)->invoice_id);
					if ($invoiceDetails['status'] == "DUE") {
						//A-ha! Customer didn't pay! Ok, let's notify him.
						$log .= manageCustomersAndNotify($invoiceData->customer_id, $invoiceData->days_left, $config, $db);
					}
				}
			}

		}

		return $log;
	}





	function checkLimitInvoices($db,$config) {
		$log = "";
		$invoice = new Invoice($db);

		$query = "SELECT invoice_id " .
				"FROM ".TB_VPS_INVOICE." " .
				"WHERE limit_info IS NOT NULL " .
				"AND suspension_date <= CURDATE() " .
				"AND status = 'due'";
		$db->query($query);

		$numRows = $db->num_rows();
		if ($numRows) {
			for ($i=0; $i < $numRows; $i++) {
				$data = $db->fetch($i);
				$invoices[] = $data->invoice_id;
			}

			foreach ($invoices as $expiredInvoice) {
				$invoice->cancelInvoice($expiredInvoice);
				$log .= "Limit Invoice ID ".$expiredInvoice." is canceled. Today = Suspension date.\n";
			}
		}

		return $log;
	}

	function checkCustomInvoices($db, $config) {
		$log = "";

		$query = "SELECT i.invoice_id, i.customer_id, i.suspension_disable, DATEDIFF(i.suspension_date, CURDATE()) days_left " .
				"FROM ".TB_VPS_INVOICE." i, ".TB_VPS_CUSTOMER." c " .
				"WHERE c.customer_id = i.customer_id " .
				"AND i.custom_info IS NOT NULL " .
				"AND c.status = 'on' " .
				"AND i.status = 'due'";
		$db->query($query);

		$numRows = $db->num_rows();
		if ($numRows) {
			for ($i=0; $i < $numRows; $i++) {
				$data = $db->fetch($i);
				$dueInvoice = array (
					'invoiceID' 		=> $data->invoice_id,
					'customerID'	 	=> $data->customer_id,
					'suspensionDisable'	=> ($data->suspension_disable == 0) ? false : true,
					'daysLeft'			=> $data->days_left
				);
				$dueInvoices[] = $dueInvoice;
			}
			$invoice = new Invoice($db);
			foreach ($dueInvoices as $expiredInvoice) {

				if ($expiredInvoice['suspensionDisable']) {
					$log .= manageCustomersAndNotify($expiredInvoice['customerID'], $expiredInvoice['daysLeft'], $config, $db);
				} else {
					if ($expiredInvoice['daysLeft'] < 0) {
						$invoice->cancelInvoice($expiredInvoice['invoiceID']);
						$log .= "Custom Invoice ID ".$expiredInvoice['invoiceID']." is canceled. Today = Suspension date.\n";
					}
				}
			}
		}

		return $log;
	}


	//vps config ini
	$config = loadConfig($db);

	//logIni
	$log = date('Y-m-d H:i:s')."	Starting notification script.\n";

	$log .= checkBillingInvoices($db,$config);

	$log .= checkLimitInvoices($db,$config);

	$log .= checkCustomInvoices($db,$config);

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

	$db->query($query);

	//refresh customer's deadline_counter in Bridge
	$query = "SELECT customer_id FROM ".TB_VPS_CUSTOMER." WHERE status= 'on' ";
    $db->query($query);
    $customers = $db->fetch_all_array();

    foreach ($customers as $customer)
    {
		$vpsRegistrationPeriod = $configs['vps_registration_period'];
		$customerID = (int)$customer['customer_id'];

		$query = "SELECT MIN(DATEDIFF(i.suspension_date, CURDATE())) days_left " .
    			 					"FROM ".TB_VPS_INVOICE." i, ".TB_VPS_CUSTOMER." c " .
    			 					"WHERE i.customer_id = c.customer_id AND i.suspension_disable=1 AND i.status = 'due' AND c.customer_id=".$customerID;
    	$db->query($query);

    	$daysLeft = "NULL";

    	//customer has due invoices

    	  $vps2voc = new VPS2VOC($db);
		  $customerDetails = $vps2voc->getCustomerDetails($customerID);
		  $timeStampTrialDaysLeft = strtotime($customerDetails['trial_end_date']) - strtotime(date('Y-m-d'));

		  if ( $timeStampTrialDaysLeft < $vpsRegistrationPeriod*24*60*60 && $timeStampTrialDaysLeft > 0) {

				$daysLeft = (int)round($timeStampTrialDaysLeft/60/60/24);

		  } else {

			if ($db->num_rows() > 0) {

    			$data = $db->fetch(0);
    			if (!is_null($data->days_left)) $daysLeft = (int)$data->days_left;

				}
		 	 }	
    }

?>
