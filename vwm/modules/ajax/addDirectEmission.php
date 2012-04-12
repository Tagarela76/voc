<?php
chdir('../..');	

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');
require_once ('modules/classes/Unittype.class.php');
require_once ('extensions/carbon_footprint/classes/CarbonFootprint.class.php');

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
$db->select_db(DB_NAME);
 
$unittypeObject = new Unittype($db);
 
$xnyo->filter_post_var('fuel', 'text');

$fuel = $_POST['fuel'];

$carbonFootprintObj=new CarbonFootprint($db);
$unittypeObject = new Unittype($db);
$unittype_id=$carbonFootprintObj->getUnittipeOfEmissionFactor($fuel);

$unittypeClass=$unittypeObject->isWeightOrVolume($unittype_id);
$unittypeName=$unittypeObject->getNameByID($unittype_id);
$result="{'unitypeClass':'".$unittypeClass."', 'defaultUnittype':'".$unittypeName."'}";
echo $result;
?>