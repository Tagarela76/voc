<?php
	
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/lib/Reform.inc.php');
		
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
	$xnyo->filter_post_var('action', 'text');
	$action = $_POST['action'];

	$xnyo->filter_post_var("id", "text");
	$xnyo->filter_post_var("work_order_number", "text");
	$xnyo->filter_post_var("work_order_description", "text");
	$xnyo->filter_post_var("work_order_customer_name", "text");
	$xnyo->filter_post_var("work_order_status", "text");
	$xnyo->filter_post_var("work_order_id", "text");

	$workOrderId = $_POST["work_order_id"]; // could be NULL (add action)
	if ($workOrderId == '') {
		$workOrderId = null;
	}
	$workOrder = new WorkOrder($db, $workOrderId);	
	// we should save old work order number for use it in mix update
	$workOrderOldDesc = $workOrder->number;
	$workOrder->number = $_POST["work_order_number"];
	$workOrder->facility_id = $_POST["id"];
	$workOrder->description = $_POST["work_order_description"];
	$workOrder->customer_name = $_POST["work_order_customer_name"];
	$workOrder->status = $_POST["work_order_status"];

	$validation = new Validation($db);
	$validStatus = $validation->validateRegDataWorkOrder($workOrder);

	if ($action == 'addItem') {
		if (!$validation->isUniqueName("workOrder", $workOrder->number, $workOrder->facility_id)) {
			$validStatus['summary'] = 'false';
			$validStatus['number'] = 'alredyExist';
		}
	}	
	if ($validStatus['summary'] == 'true') {					

		$workOrderId = $workOrder->save();
		// add empty mix
		if ($action == 'addItem') {
			// get current facility departments
			$facility = new Facility($db);
			$departmentIds = $facility->getDepartmentList($_POST["id"]);
			if (!empty($departmentIds)) {
				// add empty mix for each facility department
				$mixOptimized = new MixOptimized($db);
				$mixOptimized->description = $_POST["work_order_number"];
				$mixOptimized->wo_id = $workOrderId;
				$mixOptimized->iteration = 0;
				$mixOptimized->facility_id = $_POST["id"]; 
				$mixOptimized->department_id = $departmentIds[0];
				$mixOptimized->save();
			}
		}

		if ($action == 'edit') {
			// get work order mix id
			$mixIDs = $workOrder->getMixes(); 
			// now we should update child work order mix (don't touch iteration suffix)
			foreach ($mixIDs as $mixID) {
				// add empty mix for each facility department
				$mixOptimized = new MixOptimized($db, $mixID->mix_id);
				preg_match("/$workOrderOldDesc(.*)/", $mixOptimized->description, $suffix);  
				
                $mixOptimized->description = $_POST["work_order_number"];
                if(!empty($suffix[1]) ) {
                    $mixOptimized->description .= $suffix[1]; 
                }
                $mixOptimized->save(); 
			}
		}
	}

	echo json_encode($validStatus);		

?>
