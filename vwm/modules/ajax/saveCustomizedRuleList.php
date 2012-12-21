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
	$xnyo->filter_post_var('ruleCount', 'int');
	$ruleCount = $_POST['ruleCount'];
	
	for($i=0;$i<$ruleCount;$i++) {
		$xnyo->filter_post_var('ruleID_'.$i, 'int');
		if (isset($_POST['ruleID_'.$i])) {
			$ruleList[] = $_POST['ruleID_'.$i];				
		}
	}

	$xnyo->filter_post_var('role', 'text');
	$xnyo->filter_post_var('roleID', 'int');
	$role = $_POST['role'];
	$roleID = $_POST['roleID'];	
	$rule = new Rule($db);
	$rule->setCustomizedRuleList($ruleList, $role, $roleID);	
?>
