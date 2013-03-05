<?php
chdir('../..');

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');
require_once ('modules/classes/EquipmentProperties.class.php');
require_once ('modules/classes/Equipment.class.php');

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
$db->select_db(DB_NAME);

$xnyo->filter_post_var('departmentId', 'text');
$equipmentObj= new Equipment($db);

$departmentId = $_POST['departmentId'];
$equipmentList=$equipmentObj->getEquipmentList($departmentId);
if ($equipmentList!=null)
	echo json_encode($equipmentList);
else echo 'false';
?>
