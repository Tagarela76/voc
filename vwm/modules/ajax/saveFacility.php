<?php
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/lib/Reform.inc.php');
	
	require_once('modules/phpgacl/gacl.class.php');
	require_once('modules/phpgacl/gacl_api.class.php');
		
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	
	$xnyo->filter_vars = false;
	
	$xnyo->start();
	
	$db->select_db(DB_NAME);
	
	//	filter action var	
	$xnyo->filter_post_var('action', 'text');
	$action = $_POST['action'];
	$categoty = 'facility';
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
	
	switch ($action) {		
		case 'edit':
			$registration = new Registration($db);
			if ($registration->isOwnState($_POST["country"])) {				
				$state = $_POST["selectState"];
			} else {				
				$state = $_POST["textState"];
			}
			
			$facility = new \VWM\Hierarchy\Facility($db, $_POST["id"]);			
			$facility->setAddress($_POST["address"]);
			$facility->setCity($_POST["city"]);
			$facility->setClientFacilityId($_POST["client_facility_id"]);
			$facility->setCompanyId($_POST["id"]);
			$facility->setContact($_POST["contact"]);
			$facility->setCountry($_POST["country"]);
			$facility->setCounty($_POST["county"]);
			$facility->setCreaterId($_SESSION['user_id']);
			$facility->setEmail($_POST["email"]);			
			$facility->setEpa($_POST["epa"]);
			$facility->setFax($_POST["fax"]);			
			$facility->setMonthlyNoxLimit($_POST["monthly_nox_limit"]);
			$facility->setName($_POST["name"]);
			$facility->setPhone($_POST["phone"]);
			$facility->setState($state);
			$facility->setTitle($_POST["title"]);
			$facility->setVocAnnualLimit($_POST["voc_annual_limit"]);
			$facility->setVocLimit($_POST["voc_limit"]);
			$facility->setZip($_POST["zip"]);	
			
			//default unit type
			$facilityUnitType = ($_POST["unittype"]);
			$facilityUnitType = explode(',',$facilityUnitType);
			
			//default ap method type
			$facilityAPMethod = ($_POST["apMethods"]);
			$facilityAPMethod = explode(',',$facilityAPMethod);
			
			$violationList = $facility->validate();		
			if(count($violationList) == 0) {
				$result = $facility->save();
				
				if(!$result) {
					throw new Exception('Failed to save facility');
				}
				//save facility Unit type
					$unittype = new Unittype($db);
					$unittype->setDefaultCategoryUnitTypelist($facilityUnitType, $categoty, $result);
					
				//save facility ap methods
				$apmethod = new Apmethod($db);
				$apmethod->setDefaultCategoryAPMethodlist($facilityAPMethod, $categoty, $result);
				
				//	TODO: WHY???!
				// clear jobber array
				foreach ($_POST["jobber"] as $jobberPost) {
					if ($jobberPost != '') {
						$jobber[] =  $jobberPost;
					}
				}
				
				if ($_POST["jobber"] && isset($jobber)){				
					$jobberManager = new JobberManager($db);
					$jobberManager->updateJobberFacility($facility->getFacilityId(), $jobber);

				}		
			}
			
			// convert violationlist to old school system
			// because I have no time today to rebuild templates and js
			// TODO:
			$output = array();
			foreach ($violationList as $violation) {
				$output[$violation->getPropertyPath()] = 'failed';				
			}			
			if(count($output) > 0) {
				$output['summary'] = 'false';
			} else {
				$output['summary'] = 'true';
			}
			echo json_encode($output);		
			die;
			//
			
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
			$xnyo->filter_post_var("monthly_nox_limit", "text");
			
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
				"creater_id"	=>	0,
				"monthly_nox_limit"	=> $_POST["monthly_nox_limit"]
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
				// clear jobber array
				foreach ($_POST["jobber"] as $jobberPost) {
					if ($jobberPost != '') {
						$jobber[] =  $jobberPost;
					}
				}
				
				if ($_POST["jobber"] && isset($jobber)){
				//	$jobber = $_POST["jobber"];
					$jobberManager = new JobberManager($db);
					$jobberManager->updateJobberFacility($regData['facility_id'], $jobber);

				}		
			}

			
			echo json_encode($validateStatus);		
			break;
			
			
			
		case 'addItem':			
			$GCG = new GCG($db);
			$gcgID = $GCG->create();
						
			$registration = new Registration($db);
			if ($registration->isOwnState($_POST["country"])) {				
				$state = $_POST["selectState"];
			} else {				
				$state = $_POST["textState"];
			}
		
			$facility = new \VWM\Hierarchy\Facility($db);
			$facility->setAddress($_POST["address"]);
			$facility->setCity($_POST["city"]);
			$facility->setClientFacilityId($_POST["client_facility_id"]);
			$facility->setCompanyId($_POST["id"]);
			$facility->setContact($_POST["contact"]);
			$facility->setCountry($_POST["country"]);
			$facility->setCounty($_POST["county"]);
			$facility->setCreaterId($_SESSION['user_id']);
			$facility->setEmail($_POST["email"]);			
			$facility->setEpa($_POST["epa"]);
			$facility->setFax($_POST["fax"]);
			$facility->setGcgId($gcgID);
			$facility->setMonthlyNoxLimit($_POST["monthly_nox_limit"]);
			$facility->setName($_POST["name"]);
			$facility->setPhone($_POST["phone"]);
			$facility->setState($state);
			$facility->setTitle($_POST["title"]);
			$facility->setVocAnnualLimit($_POST["voc_annual_limit"]);
			$facility->setVocLimit($_POST["voc_limit"]);
			$facility->setZip($_POST["zip"]);	
			$facilityUnitType = ($_POST["unittype"]);
			$facilityUnitType = explode(',',$facilityUnitType);
			
			
			$violationList = $facility->validate();		
			if(count($violationList) == 0) {
				$result = $facility->save();
				if(!$result) {
					throw new Exception('Failed to save facility');
				}
					
				//save facility Unit type
					$unittype = new Unittype($db);
					$unittype->setDefaultCategoryUnitTypelist($facilityUnitType, $categoty, $result);
					
				//   CREATE ACO
				$gacl_api = new gacl_api();
				$acoID = $gacl_api->add_object('access', "facility_".$facility->getFacilityId(), "facility_".$facility->getFacilityId(), 0, 0, 'ACO');
				
				//   CREATE ARO GROUPs
				$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
				$aro_group_facility = $gacl_api->add_group("facility_".$facility->getFacilityId(), "facility_".$facility->getFacilityId(), $giantcomliance, 'ARO');
				$aro_group_company=$gacl_api->get_group_id ("company_".$facility->getCompanyId());
				$aro_group_root=$gacl_api->get_group_id("root");
				//   CREATE ACL
				$acoArray = array("access"=>array("facility_".$facility->getFacilityId()));
				$facilityGroup = array($aro_group_facility);
				$companyGroup = array($aro_group_company);
				$rootGroup = array($aro_group_root);
				$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,1,1,NULL,'facility users has access to facility ACO ');
				$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company users has access to facility ACO ');
				$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to facility ACO ');
				
				if ($_POST["jobber"]){
					$jobberManager = new JobberManager($db);
					$jobberManager->updateJobberFacility($facility->getFacilityId(), $_POST["jobber"]);
				}
			}
			
			// convert violationlist to old school system
			// because I have no time today to rebuild templates and js
			// TODO:
			$output = array();
			foreach ($violationList as $violation) {
				$output[$violation->getPropertyPath()] = 'failed';				
			}			
			if(count($output) > 0) {
				$output['summary'] = 'false';
			} else {
				$output['summary'] = 'true';
			}
			echo json_encode($output);								
			break; 
	}
?>
