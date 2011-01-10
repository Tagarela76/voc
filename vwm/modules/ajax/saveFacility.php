<?php
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/Reform.inc.php');
		
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();
	
	$db->select_db(DB_NAME);
	
	//	filter action var	
	$xnyo->filter_post_var('action', 'text');
	$action = $_POST['action'];
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
	
	switch ($action) {
		//case 'updateItem':
		case 'edit':
			$xnyo->filter_post_var("id", "text");			
			$xnyo->filter_post_var("epa", "text");
			$xnyo->filter_post_var("voc_limit", "text");
			$xnyo->filter_post_var("voc_annual_limit", "text");
			$xnyo->filter_post_var("name", "text");
			$xnyo->filter_post_var("address", "text");
			$xnyo->filter_post_var("city", "text");
			$xnyo->filter_post_var("zip", "text");
			$xnyo->filter_post_var("county", "text");						
			$xnyo->filter_post_var("country", "text");
			$xnyo->filter_post_var("phone", "text");
			$xnyo->filter_post_var("fax", "text");
			$xnyo->filter_post_var("email", "text");
			$xnyo->filter_post_var("contact", "text");
			$xnyo->filter_post_var("title", "text");						
			
			// protecting from xss
			foreach ($_POST as $key=>$value)
			{								
				$_POST[$key]=Reform::HtmlEncode($value);
			}
			
			$regData = array (
				"facility_id"	=>	$_POST["id"],
				"epa"			=>	$_POST["epa"],
				"voc_limit"		=>	$_POST["voc_limit"],
				"voc_annual_limit"	=>	$_POST["voc_annual_limit"],
				"name"			=>	$_POST["name"],
				"address"		=>	$_POST["address"],
				"city"			=>	$_POST["city"],
				"zip"			=>	$_POST["zip"],
				"county"		=>	$_POST["county"],				
				"country"		=>	$_POST["country"],
				"phone"			=>	$_POST["phone"],
				"fax"			=>	$_POST["fax"],
				"email"			=>	$_POST["email"],
				"contact"		=>	$_POST["contact"],
				"title"			=>	$_POST["title"],
				"creater_id"	=>	0
			);
			
			$registration = new Registration($db);
			if ($registration->isOwnState($regData["country"])) {
				$xnyo->filter_post_var("selectState", "text");
				$regData["state"] = $_POST["selectState"];
			} else {
				$xnyo->filter_post_var("textState", "text");
				$regData["state"] = $_POST["textState"];
			}
			
			$facility = new Facility($db);
			$validate = new Validation($db);
			$validateStatus = $validate->validateRegData($regData);
			$facilityDetails = $facility->getFacilityDetails($regData['facility_id']);
			
			//	validate unique
			if ($regData["name"] != $facilityDetails["name"]) {	//	fac name was changed
				if (!$validate->isUniqueName("facility", $regData["name"], $facilityDetails["company_id"])) {
					$validateStatus['summary'] = 'false';
					$validateStatus['name'] = 'alredyExist';
				}
			}			
			
			if ($validateStatus["summary"] == "true") {
				//	setter injection
				$facility->setTrashRecord(new Trash($db));															
				$facility->setFacilityDetails($regData);
			}
			
			echo json_encode($validateStatus);			
			break;
			
			
			
		case 'addItem':
			$facility = new Facility($db);
			
			$xnyo->filter_post_var("voc_limit", "text");
			$xnyo->filter_post_var("voc_annual_limit", "text");
			$xnyo->filter_post_var("epa", "text");
			$xnyo->filter_post_var("id", "text");
			$xnyo->filter_post_var("name", "text");
			$xnyo->filter_post_var("address", "text");
			$xnyo->filter_post_var("city", "text");
			$xnyo->filter_post_var("zip", "text");
			$xnyo->filter_post_var("county", "text");
			$xnyo->filter_post_var("country", "text");
			
			//	"Init state" dances
			$registration = new Registration($db);
			if ($registration->isOwnState($_POST["country"])) {
				$xnyo->filter_post_var("selectState", "text");
				$state = $_POST["selectState"];
			} else {
				$xnyo->filter_post_var("textState", "text");
				$state = $_POST["textState"];
			}
			
			$xnyo->filter_post_var("phone", "text");
			$xnyo->filter_post_var("fax", "text");
			$xnyo->filter_post_var("email", "text");
			$xnyo->filter_post_var("contact", "text");
			$xnyo->filter_post_var("title", "text");
			
			// protecting from xss
			foreach ($_POST as $key=>$value)
			{								
				$_POST[$key]=Reform::HtmlEncode($value);
			}
			
			$facilityData = array (
				"voc_limit"		=>	$_POST["voc_limit"],
				"voc_annual_limit"		=>	$_POST["voc_annual_limit"],
				"epa"			=>	$_POST["epa"],
				"company_id"	=>	$_POST["id"],
				"name"			=>	$_POST["name"],
				"address"		=>	$_POST["address"],
				"city"			=>	$_POST["city"],
				"zip"			=>	$_POST["zip"],
				"county"		=>	$_POST["county"],
				"state"			=>	$state,
				"country"		=>	$_POST["country"],
				"phone"			=>	$_POST["phone"],
				"fax"			=>	$_POST["fax"],
				"email"			=>	$_POST["email"],
				"contact"		=>	$_POST["contact"],
				"title"			=>	$_POST["title"],
				"creater_id"	=>	$_SESSION['user_id']
			);
			
			
			$validation = new Validation($db);
			$validStatus = $validation->validateRegData($facilityData);
			
			if (!$validation->isUniqueName("facility", $facilityData["name"], $facilityData['company_id'])) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				//	setter injection
				$facility->setTrashRecord(new Trash($db));	
				$facility->addNewFacility($facilityData);
			}
			
			echo json_encode($validStatus);					
			break; 
	}
?>
