<?php
/*
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	$xnyo = new Xnyo;

	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;

	$xnyo->start();

	
	$xnyo->filter_post_var('resourcesAttributes', 'text');
	$xnyo->filter_post_var('stepAttributes', 'text');

	$db->select_db(DB_NAME);
	
	$resourcesAttributes = json_decode($_POST['resourcesAttributes']);
	$stepAttributes = json_decode($_POST['stepAttributes']);
	
	$stepInstance = new \VWM\Apps\Process\StepInstance($db);
	$stepInstance->setId($stepAttributes->stepId);
	$stepInstance->load();
	$stepInstance->setDescription($stepAttributes->stepDescription);
	
	foreach($resourcesAttributes as $resourceAttributes){
		$resources[] = json_decode($resourceAttributes);
	}

	$resourceInstanceArray = array();
	foreach($resources as $resource){
		$resourceInstance = new \VWM\Apps\Process\ResourceInstance($db);
		$resourceInstance->setDescription($resource->description);
		$resourceInstance->setQty($resource->qty);
		$resourceInstance->setRate($resource->rate);
		$resourceInstance->setUnittypeId($resource->unittypeId);
		$resourceInstance->setResourceTypeId($resource->resourceTypeId);
		$resourceInstance->setRateUnittypeId($resource->unittypeId);
		$resourceInstance->setStepId($stepInstance->getId());
		$resourceInstanceArray[] = $resourceInstance;
	}
	
	$stepInstance->setResources($resourceInstanceArray);
	$stepId = $stepInstance->save();
	
	if($stepId){
		echo '?action=viewDetails&category=repairOrder';
	}else{
		echo false;
	}*/
?>
