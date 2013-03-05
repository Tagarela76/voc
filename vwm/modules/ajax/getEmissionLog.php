<?php

$form = $_GET['debug'] ? $_REQUEST : $_POST;

//var_Dump($form);

if(isset($form['year']))
{
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require $site_path.'../vendor/autoload.php';

	$xnyo = new Xnyo;

	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;

	$xnyo->start();



	$xnyo->filter_post_var('year', 'int');
	$xnyo->filter_post_var('category', 'text');
	$xnyo->filter_post_var('category_id', 'int');
	$xnyo->filter_post_var('action', 'int');
	$emissionLog = new EmissionLog ($db);
	if (isset($form['action']) && $form['action'] == 'getNoxLog') {
		// get nox log
		$log = $emissionLog->getNoxEmissionLog($form['year'], $form['category_id'], $form['category']);
	} else {
		$log = $emissionLog->getEmissionLog($form['year'],$form['category'],$form['category_id']);
	}

	echo json_encode($log);

}

?>