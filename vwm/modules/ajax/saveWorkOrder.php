<?php
	
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/Reform.inc.php');
		
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();
	
	$db->select_db(DB_NAME);
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
	
	$xnyo->filter_post_var("id", "text");
	$xnyo->filter_post_var("work_order_number", "text");
	$xnyo->filter_post_var("work_order_description", "text");
	$xnyo->filter_post_var("work_order_customer_name", "text");
	$xnyo->filter_post_var("work_order_status", "text");
	$xnyo->filter_post_var("work_order_id", "text");
	$workOrderId = $_POST["work_order_id"]; // could be NULL (add action)
	$workOrder = new WorkOrder($db, $workOrderId);		
	$workOrder->number = $_POST["work_order_number"];
	$workOrder->facility_id = $_POST["id"];
	$workOrder->description = $_POST["work_order_description"];
	$workOrder->customer_name = $_POST["work_order_customer_name"];
	$workOrder->status = $_POST["work_order_status"];

	$validation = new Validation($db);
	$validStatus = $validation->validateRegDataWorkOrder($workOrder);

	if (!$validation->isUniqueName("workOrder", $workOrder->number, $workOrder->facility_id)) {
		$validStatus['summary'] = 'false';
		$validStatus['name'] = 'alredyExist';
	}
//	var_dump($validStatus); die();	
	if ($validStatus['summary'] == 'true') {					

		$workOrder->save();
	}

	echo json_encode($validStatus);		

?>
