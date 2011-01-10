<?php
	chdir('../..');	

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

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
	
	//	filter vars
	$xnyo->filter_post_var('notifyCount', 'int');
	$notifyCount = $_POST['notifyCount'];
	for($i=0;$i<$notifyCount;$i++) {
		$xnyo->filter_post_var('notifyID_'.$i, 'int');
		if (isset($_POST['notifyID_'.$i])) {
			$limites[] = $_POST['notifyID_'.$i];				
		}
	}

	$xnyo->filter_post_var('userID', 'int');
	$xnyo->filter_post_var('category', 'text');
	$xnyo->filter_post_var('categoryID', 'int');
	$userID = $_POST['userID'];
	$category = $_POST['category'];
	$categoryID = $_POST['categoryID'];	
	$emailNotifications = new EmailNotifications($db);
	$emailNotifications->saveLimits2User($limites,$userID);
?>
