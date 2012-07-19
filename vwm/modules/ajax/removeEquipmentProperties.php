<?php
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	/*$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();*/

	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php');

	$db->select_db(DB_NAME);

	require ('modules/xnyo/smarty/startSmarty.php');

	//	logged in?
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		throw new Exception('deny');
	}

	$xnyo->filter_get_var('rowsToRemove', 'text');
	$xnyo->filter_get_var('property', 'text');
      
	switch ($_REQUEST['property']) {
		case 'filter':   
                    $rowsToRemove = $_REQUEST['rowsToRemove'];
                    foreach ($rowsToRemove as $rowToRemove) {
                        $equipmentFilter = new EquipmentFilter($db, trim($rowToRemove));
                        $equipmentFilter->delete();
                    }
           
                break;
                case 'lighting':
                    $rowsToRemove = $_REQUEST['rowsToRemove'];
                    foreach ($rowsToRemove as $rowToRemove) {
                        $equipmentLighting = new EquipmentLighting($db, trim($rowToRemove));
                        $equipmentLighting->delete();
                    }
                break;
        }
?>
