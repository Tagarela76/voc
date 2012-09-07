<?php
chdir('../vwm/');	

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');

$site_path = getcwd().DIRECTORY_SEPARATOR; 
define ('site_path', $site_path);
	
//	Include Class Autoloader
require_once('modules/classAutoloader.php');

$xnyo = new Xnyo;

$xnyo->database_type	= DB_TYPE;
$xnyo->db_host 		= DB_HOST;
$xnyo->db_user		= DB_USER;
$xnyo->db_passwd		= DB_PASS;

$xnyo->start();
$db->select_db(DB_NAME);

if(!$argv[1]) {
	echo "Please pass company id\n";
	return false;
}
$sql = "DELETE FROM ".TB_PFP2COMPANY." " .
		"WHERE company_id = {$db->sqltext($argv[1])} " .
		"AND is_available = 1";
if($db->exec($sql)) {
	echo "Success\n";
} else {
	echo "Fail\n";
}
	
	
?>
