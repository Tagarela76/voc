<?php
	define ("USERS_TABLE", "vps_user");

	chdir('..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');


	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');


	function write2log($var, $msg = null) {
		$debugMsg = "";
        if ($msg != null) {
          $debugMsg .= $msg . "\n";
        }
        $debugMsg .= var_export($var, true) . "\n\n";
        $type = 3; //append message to the log file
        error_log($debugMsg, $type, "../voc_logs/log_ipn.log");
    }




	$postdata="";
	foreach ($_POST as $key=>$value) {
		$postdata.=$key."=".urlencode($value)."&";
	}
	$postdata.="cmd=_notify-validate";

	$curl = curl_init("https://www.sandbox.paypal.com/cgi-bin/webscr");
	curl_setopt ($curl, CURLOPT_HEADER, 0);
	curl_setopt ($curl, CURLOPT_POST, 1);
	curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);
	$response = curl_exec ($curl);
	curl_close ($curl);

	if ($response != "VERIFIED") {

		write2log("Ough! IPN was send not by PayPal. Fraud? EXIT.");
		exit;						//	query was send NOT by Paypal
	}

	write2log($_POST, date('d-m-Y H:i:s').' $_POST:');



	$xnyo = new Xnyo;

	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;

	$xnyo->start();



	$db->select_db(DB_NAME);

	//take account info
	$db->query("SELECT value FROM ".TB_VPS_CONFIG." WHERE name = 'paypal_merchant_email'");
	$paypalMerchantEmail = $db->fetch(0);
	$paypalemail = $paypalMerchantEmail->value;
	$db->query("SELECT value FROM ".TB_VPS_CONFIG." WHERE name = 'paypal_merchant_id'");
	$paypalMerchantID = $db->fetch(0);
	$merchantID = $paypalMerchantID->value;

	$xnyo->filter_post_var('receiver_email', 'text');
	$xnyo->filter_post_var('txn_type', 'text');
	$xnyo->filter_post_var('receiver_id', 'text');

	if ($_POST['receiver_email'] != $paypalemail
		//|| $_POST['txn_type']	 != "web_accept" // Reversed txn doesn't have this field
		|| $_POST['receiver_id'] != $merchantID) {

		write2log("Receiver email or ID mismatch. Or transaction type. EXIT.");
		exit;						//	not our payment
	}

	$billing = new Billing($db);

	//check amount
	$xnyo->filter_post_var('item_number', 'text');
	$query = "SELECT * " .
	 		"FROM ".TB_VPS_INVOICE." " .
	 		"WHERE invoice_id = ".$_POST['item_number'];

	$db->query($query);

	if ($db->num_rows()) {
		$invoiceData = $db->fetch(0);

		$xnyo->filter_post_var('mc_gross', 'text');
		$xnyo->filter_post_var('mc_currency', 'text');

		$postAmount = str_replace("-","",$_POST["mc_gross"]);

		$currencyDetails = $billing->getCurrencyDetails($invoiceData->currency_id);

		if ($invoiceData->total != $postAmount || $_POST['mc_currency'] != $currencyDetails['iso']) {	//customer pays entire amount case

			write2log("Amount mismatch. EXIT.");
			exit;					//	amount mismatch
		}
	}

	//check unique txn
	$xnyo->filter_post_var('payment_status', 'text');
	$xnyo->filter_post_var('txn_id', 'text');

	$query = "SELECT * " .
	 		"FROM ".TB_VPS_PAYMENT." " .
	 		"WHERE txn_id = '".$_POST['txn_id']."' " .
	 		"AND status = '".$_POST['payment_status']."' ";

	$db->query($query);
	if ($db->num_rows()) {

		write2log("Transaction with current ID and status is already processed. EXIT.");
		exit;					//	txn is not unique
	}

	$payment = new Payment($db);
	$xnyo->filter_post_var('item_number', 'text');
	$xnyo->filter_post_var('custom', 'text');	// user ID
	$paid = $payment->createPayment($_POST['item_number'], $_POST['custom'], $_POST['txn_id'], $_POST['payment_status']);
	write2log("Payment created.");

	$invoice = new Invoice($db);

	switch ($_POST['payment_status']) {
		case 'Completed':
			$invoice->updateInvoice($_POST['item_number'], $paid);
			write2log("Invoice updated.");
			break;
		default:
			break;

	}

?>
