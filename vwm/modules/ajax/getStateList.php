<?php
if(isset($_GET['countryID'])){
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
	
	$xnyo->filter_get_var('countryID', 'int');
	
	
	$db->select_db(DB_NAME);
	$db->query('SELECT * FROM state WHERE country_id = \''.$_GET['countryID'].'\' ORDER BY name');
	
	for ($i=0; $i < $db->num_rows(); $i++) {
		$data=$db->fetch($i);
		
		$state_id = $data->state_id;
		$state_name = $data->name;
		
		$obj = "obj.options[obj.options.length] = new Option('";
		$obj .= $state_name."', '";
		$obj .= $state_id."');\n";
		echo $obj;			
	}
}
?> 