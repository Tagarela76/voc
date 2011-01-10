<?php
if(isset($_GET['inventoryID'])){
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
	
	$db->select_db(DB_NAME);
		
	$xnyo->filter_get_var('inventoryID', 'int');
	
	$inventory = new Inventory($db, $_GET['inventoryID']);
	echo $inventory->getDescription();		
}
?> 