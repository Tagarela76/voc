<?php

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

	$xnyo->filter_get_var('sysType', 'text');
	$xnyo->filter_post_var('sysType', 'text');
	$xnyo->filter_get_var('departmentId', 'text');
	$xnyo->filter_post_var('departmentId', 'text');
	$xnyo->filter_get_var('companyEx', 'text');

	$sysType = $_REQUEST['sysType'];
	$departmentId = $_REQUEST['departmentId'];

	$db->select_db(DB_NAME);

	$department = new \VWM\Hierarchy\Department($db, $departmentId);
	$unitTypes = $department->getUnitTypeList();
	$uManager = new \VWM\Apps\UnitType\Manager\UnitTypeManager($db);
	$unitTypeClasses = $uManager->getUnitTypeListByUnitClass($sysType, $unitTypes);
	$data = array();
	foreach($unitTypeClasses as $unitType){
		$type = array(
			'unittype_id'=>$unitType->getUnitTypeId(),
			'name'=>$unitType->getUnitTypeDesc()
		);
		$data[] = $type;
	}



        echo json_encode($data);
?>
