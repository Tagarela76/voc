<?php
define("USERS_TABLE", "user");
chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php'); 			

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php'); 
	//v
	//	Start Smarty templates engine
	require ('modules/xnyo/smarty/startSmarty.php');
	
	require_once('modules/Reform.inc.php');
	
	$db->select_db(DB_NAME);

	/* HERE CODE: */
	
	//update EPA Regulations
	//$rag = new RegAgency($db);
	//$rag->loadAgencyFromXML();
	
	$ram = new RegActManager($db);
	$ram->parseXML();
	
	//send periodic Notifiers
	$eNotificator = new EmailNotifications($db);
	$eNotificator->checkPeriodicNotifiers();
?>
