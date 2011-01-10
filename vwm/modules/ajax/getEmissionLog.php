<?php
if(isset($_POST['year']))
{
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
	
	$xnyo->filter_post_var('year', 'int');
	$xnyo->filter_post_var('category', 'text');
	$xnyo->filter_post_var('category_id', 'int');
	
	$emissionLog = new EmissionLog ($db);
	echo json_encode($emissionLog->getEmissionLog($_POST['year'],$_POST['category'],$_POST['category_id']));			
	
}
?> 