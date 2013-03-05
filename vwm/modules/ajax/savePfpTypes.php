<?php
	use \VWM\Hierarchy\Department;
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/lib/Reform.inc.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require $site_path.'../vendor/autoload.php';

	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();

	$db->select_db(DB_NAME);

	//	logged in?
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}

	$xnyo->filter_post_var("id", "text");
	$xnyo->filter_post_var("pfpTypeName", "text");
	$xnyo->filter_post_var("departmentsId", "text");
	$xnyo->filter_post_var("pfpId", "text");
	$pfpId = $_POST["pfpId"];
	if($pfpId == ''){
		$pfpTypes = new PfpTypes($db);
	}else{
		$pfpTypes = new PfpTypes($db, $pfpId);
	}


	$pfpTypes->facility_id = $_POST["id"];
	$pfpTypes->name = $_POST["pfpTypeName"];

	$departmentsId = $_POST["departmentsId"];
	$departmentsId = explode(',', $departmentsId);
	$pfpTypesDepartments = array();
	foreach ($departmentsId as $departmentId){
		$department = new Department($db, $departmentId);
		$pfpTypesDepartments[]=$department;

	}

	$validation = new Validation($db);
	$validStatus = $validation->validateRegDataPfpType($pfpTypes);

    if (!$validation->isUniqueName("pfpTypes", $pfpTypes->name, $pfpTypes->facility_id) && $pfpId=='') {
        $validStatus['summary'] = 'false';
        $validStatus['pfpType'] = 'alredyExist';
    }

	if ($validStatus['summary'] == 'true') {
		$pfpTypes->setDepartments($pfpTypesDepartments);
		$pfpTypes->save();
	}


	echo json_encode($validStatus);

?>
