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
	$xnyo->filter_post_var("pfpTypeName", "text");

	$pfpTypes = new PfpTypes($db);	
	$pfpTypes->facility_id = $_POST["id"];
	$pfpTypes->name = $_POST["pfpTypeName"];

	$validation = new Validation($db);
	$validStatus = $validation->validateRegDataPfpType($pfpTypes);

    if (!$validation->isUniqueName("pfpTypes", $pfpTypes->name, $pfpTypes->facility_id)) {
        $validStatus['summary'] = 'false';
        $validStatus['pfpType'] = 'alredyExist';
    }
    
	if ($validStatus['summary'] == 'true') {					

		$pfpTypes->save();
	}

	echo json_encode($validStatus);		

?>
