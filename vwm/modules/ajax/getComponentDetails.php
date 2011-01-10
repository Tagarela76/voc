<?php
if(isset($_GET['componentID'])){
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
	
	$xnyo->filter_get_var('componentID', 'int');	
	
	$db->select_db(DB_NAME);
	$query = 'SELECT * FROM '.TB_COMPONENT.' WHERE component_id = \''.$_GET['componentID'].'\'';
	$db->query($query);	
	if ($db->num_rows() == 1) {
		$data = $db->fetch(0);
		$cas= $data->cas;
		$description = $data->description;				
		$response='{"description":"'.$description.'","cas":"'.$cas.'"}';		
		echo $response;
	} elseif ($db->num_rows > 1) {
		echo 'false';
	}
	
}
?> 