<?php
if(isset($_GET['facilityCode'])){
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

	$xnyo->filter_get_var('facilityCode', 'int');


	$db->select_db(DB_NAME);
	$query="SELECT department_id,name FROM ".TB_DEPARTMENT." WHERE facility_id=".$_GET['facilityCode']." ORDER BY name";
	$db->query($query);
	$result=$db->fetch_all_array();
	echo (($result!=null)?json_encode($result):'false');
}
?>