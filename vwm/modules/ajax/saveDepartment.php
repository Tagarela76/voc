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
	
	//	filter action var	
	$xnyo->filter_post_var('action', 'text');
	$action = $_POST['action'];
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
		
	switch ($action) {
		//case 'updateItem':
		case 'edit':							
			$xnyo->filter_post_var("id", "text");
			$xnyo->filter_post_var("voc_limit", "text");
			$xnyo->filter_post_var("voc_annual_limit", "text");
			$xnyo->filter_post_var("name", "text");
			$xnyo->filter_post_var("share_wo", "text");

			$regData = array(
				"department_id"	=>	$_POST["id"],
				"name"			=>	$_POST["name"],
				"voc_limit"		=>	$_POST["voc_limit"],
				"voc_annual_limit"		=>	$_POST["voc_annual_limit"],
				"share_wo"		=> $_POST["share_wo"]
			);						
			
			$departmentObj = new Department($db);
			$validate = new Validation($db);
			
			//	validate
			$validateStatus = $validate->validateRegData($regData);
			$departmentDetails = $departmentObj->getDepartmentDetails($regData['department_id']);						
			
			//	validate unique
			if ($regData["name"] != $departmentDetails["name"]) {	//	dep name was changed
				if (!$validate->isUniqueName("department", $regData["name"], $departmentDetails["facility_id"])) {
					$validateStatus['summary'] = 'false';
					$validateStatus['name'] = 'alredyExist';
				}
			}			
						
			if ($validateStatus["summary"] == "true") {
				//	setter injection
				$departmentObj->setTrashRecord(new Trash($db));
								
				$departmentObj->setDepartmentDetails($regData);				
			}
			
			echo json_encode($validateStatus);
			break;
			
		
			
		case 'addItem':
			$xnyo->filter_post_var("id", "text");
			$xnyo->filter_post_var("name", "text");
			$xnyo->filter_post_var("voc_limit", "text");
			$xnyo->filter_post_var("voc_annual_limit", "text");
			$xnyo->filter_post_var("share_wo", "text");
			$xnyo->filter_post_var("share_wo", "text");

			$departments = new Department($db);			
			
			$departmentData = array (
				"facility_id"	=>	$_POST["id"],
				"name"			=>	$_POST["name"],
				"voc_limit"		=>	$_POST["voc_limit"],
				"voc_annual_limit"		=>	$_POST["voc_annual_limit"],
				"creater_id"	=>	$_SESSION['user_id'],
				"share_wo"		=> $_POST["share_wo"]
			);
			
			$validation = new Validation($db);
			$validStatus = $validation->validateRegData($departmentData);
			
			if (!$validation->isUniqueName("department", $departmentData['name'], $departmentData['facility_id'])) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {					
				//	setter injection
				$departments->setTrashRecord(new Trash($db));
							
				$departments->addNewDepartment($departmentData);
			}
			
			echo json_encode($validStatus);			
			break;
			
	}

	
	
	
	
?>
