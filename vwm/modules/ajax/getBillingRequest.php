<?php
if(isset($_GET['customerID'])){
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require $site_path.'../vendor/autoload.php';

	$xnyo = new Xnyo;

	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;

	$xnyo->start();

	$xnyo->filter_get_var('customerID', 'int');

	$db->select_db(DB_NAME);
	$billing = new Billing($db);
	$vps2voc = new VPS2VOC($db);
	$requestDetails = $billing->getRequestFromCustomerID($_GET['customerID']);
	if ($requestDetails!=false)
	{
		$customerDetails = $vps2voc->getCustomerDetails($_GET['customerID']);

		$definedPlans['request_id'] = $requestDetails['id'];
		$definedPlans['customer_id'] = $requestDetails['customer_id'];
		$definedPlans['customerName'] = $customerDetails['name'];
		$definedPlans['bplimit'] = $requestDetails['bplimit'];
		$definedPlans['months_count'] = $requestDetails['months_count'];
		$definedPlans['type'] = $requestDetails['type'];
		$definedPlans['MSDSLimit'] = $requestDetails['MSDS_limit'];
		$definedPlans['memoryLimit'] = $requestDetails['memory_limit'];
		$definedPlans['description'] = stripslashes($requestDetails['description']);
		$definedPlans['date'] = $requestDetails['date'];
		$definedPlans['status'] = $requestDetails['status'];

		echo json_encode($definedPlans);
	}
	else
	{
		echo 'false';
	}
}
?>