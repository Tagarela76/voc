<?php

chdir('../..');

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');

$site_path = getcwd() . DIRECTORY_SEPARATOR;
define('site_path', $site_path);

//	Include Class Autoloader
require_once('modules/classAutoloader.php');

$xnyo = new Xnyo;

$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;

$xnyo->start();

$xnyo->filter_post_var('resourceQty', 'text');
$xnyo->filter_post_var('resourceRate', 'text');
$xnyo->filter_post_var('resourceUnittypeId', 'text');
$xnyo->filter_post_var('resourceResourceUnittypeId', 'text');

$db->select_db(DB_NAME);

$qty = $_POST['resourceQty'];
$rate = $_POST['resourceRate'];
$resourceUnittypeId = $_POST['resourceUnittypeId'];
$resourceResourceUnittypeId = $_POST['resourceResourceUnittypeId'];

$resoutceInstance = new VWM\Apps\Process\ResourceInstance($db);

$resoutceInstance->setQty($qty);
$resoutceInstance->setRate($rate);
$resoutceInstance->setUnittypeId($resourceUnittypeId);
$resoutceInstance->setRateUnittypeId($resourceUnittypeId);

/*switch ($resourceResourceUnittypeId) {
	case 'TIME':
		$resoutceInstance->setResourceTypeId(1);
		break;
	case 'VOLUME':
		$resoutceInstance->setResourceTypeId(2);
		break;
	case 'GOM':
		$resoutceInstance->setResourceTypeId(3);
		break;
	default :
		break;
}*/

$resoutceInstance->setResourceTypeId($resourceResourceUnittypeId);
$resoutceInstance->calculateTotalCost();

$resourceTotalCost = array(
	'laborCost'=>$resoutceInstance->getLaborCost(),
	'materialCost'=>$resoutceInstance->getMaterialCost(),
	'totalCost'=>$resoutceInstance->getTotalCost()
);

$resourceTotalCost = json_encode($resourceTotalCost);
echo  $resourceTotalCost;

?>
