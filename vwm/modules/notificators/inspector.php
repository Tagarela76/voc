<?php

	/**
	 * 	VOC WEB MANAGER PAYMENT SYSTEM Notificator script INSPECTOR 
	 * 
	 *	Inspect vpsNotification.php for idle
	 *	if there was idle time we should run vpsNotification.php for idle date
	 *   
	 * */
	
	chdir('../..');	

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');


	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php'); 
		
	$db->select_db(DB_NAME);	
	
	$query = "SELECT max(run_date) lastRunDate FROM ".TB_VPS_NOTIFICATION_SCRIPT."";			 
	$db->query($query);
	if ($db->num_rows()) {						
		$data=$db->fetch(0);
		$lastRunDate = getdate(strtotime($data->lastRunDate));
		$today = getdate();
		
		if ($lastRunDate['mday'] == $today['mday'] && $lastRunDate['mon'] == $today['mon']) {
			
			//everithing is allright =)
			
		} else {
			
			//cleaner.php idle -> run cleaner.php
			$inspectorFlag = true;
			require('modules/notificators/cleaner.php');
			
		}			
	}	
?>
