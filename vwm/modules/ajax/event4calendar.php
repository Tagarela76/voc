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
	
	$xnyo->filter_vars = false;
	
	$xnyo->start();
	
	$db->select_db(DB_NAME);
	
	$data = $_POST;
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
	$data['user_id'] =  $user->getLoggedUserID();

	$calendar = new Calendar($db, $data);
	$result = $calendar->save();

	echo json_encode($result);		

?>
