<?php
	chdir('../..');		
	
	require_once('modules/Reform.inc.php');
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
		
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
			$xnyo->filter_post_var("equip_desc", "text");
			$xnyo->filter_post_var("selectInventoryID", "text");
			$xnyo->filter_post_var("permit", "text");			
			$xnyo->filter_post_var("expire_date", "text");			
			$xnyo->filter_post_var("daily", "text");
			$xnyo->filter_post_var("dept_track", "text");
			$xnyo->filter_post_var("facility_track", "text");
			$xnyo->filter_post_var("department_id", "text");
			
			// protecting from xss
			foreach ($_POST as $key=>$value)
			{		
				switch ($key)
				{	
					case "expire_date" : break;
									
					default:
					{
						$_POST[$key]=Reform::HtmlEncode($value);
						break;
					}
				}
			}
			
			$deptTrack = (!isset($_POST['dept_track'])) ? "no" : "yes";
			$facilityTrack = (!isset($_POST['facility_track'])) ? "no" : "yes";
			
			//	process inventory module
			$company = new Company($db);
			$companyID = $company->getCompanyIDbyDepartmentID($_POST['department_id']);
			
			$ms = new ModuleSystem($db);			
			$inventoryID = ($user->checkAccess('inventory', $companyID)) ? $_POST["selectInventoryID"] : 0;			
			
			$regData = array(
				"equipment_id"	=>	$_POST["id"],
				"name"			=>	$_POST["equip_desc"],
				"department_id"	=>	$_POST['department_id'],
				"equip_desc"	=>	$_POST["equip_desc"],
				"inventory_id"	=>	$inventoryID,
				"permit"		=>	$_POST["permit"],
				"expire_date"	=>	$_POST["expire_date"],				
				"daily"			=>	$_POST["daily"],
				"dept_track"	=>	$deptTrack,
				"facility_track"=>	$facilityTrack,
				"creater_id"	=>	18
			);
												
			if (trim($regData["daily"]) == "") {
				$regData["daily"] = 0;
			}
			
			$validation = new Validation($db);
			$validateStatus = $validation->validateRegDataEquipment($regData);			
		
			if ($validateStatus["summary"] == "true") {		
				//	convert date to timestamp							
				$regData['expire'] = new TypeChain($regData['expire_date'],'date',$db,$regData['department_id'],'department');
							
				$equipment = new Equipment($db);
				//	setter injection								
				$equipment->setTrashRecord(new Trash($db));														
				$equipment->setEquipmentDetails($regData);
			} 
			
			echo json_encode($validateStatus);			
			break;
			
			
		case "addItem":
			$xnyo->filter_post_var("department_id", "text");
			$xnyo->filter_post_var("equip_desc", "text");
			$xnyo->filter_post_var("selectInventoryID", "text");
			$xnyo->filter_post_var("permit", "text");
			$xnyo->filter_post_var("expire_date", "text");						
			$xnyo->filter_post_var("daily", "text");
			$xnyo->filter_post_var("dept_track", "text");
			$xnyo->filter_post_var("facility_track", "text");
			
			// protecting from xss
			foreach ($_POST as $key=>$value)
			{		
				switch ($key)
				{	
					case "expire_date" : break;
									
					default:
					{
						$_POST[$key]=Reform::HtmlEncode($value);
						break;
					}
				}
			}
			
			$deptTrack = (!isset($_POST['dept_track'])) ? "no" : "yes";
			$facilityTrack = (!isset($_POST['facility_track'])) ? "no" : "yes";
			
			//	process inventory module
			$company = new Company($db);
			$companyID = $company->getCompanyIDbyDepartmentID($_POST['department_id']);
			
			$ms = new ModuleSystem($db);			
			$inventoryID = ($user->checkAccess('inventory', $companyID)) ? $_POST["selectInventoryID"] : 0;
			 			
			$equipmentData = array(
				"department_id"	=>	$_POST['department_id'],
				"equip_desc"	=>	$_POST["equip_desc"],
				"inventory_id"	=>	$inventoryID,
				"permit"		=>	$_POST["permit"],
				"expire_date"	=>	$_POST["expire_date"],							
				"daily"			=>	$_POST["daily"],
				"dept_track"	=>	$deptTrack,
				"facility_track"=>	$facilityTrack,
				"creater_id"	=>	18
			);
			
			if (trim($equipmentData["daily"]) == "") {
				$equipmentData["daily"] = 0;
			}
						
			$validation = new Validation($db);					
			$validStatus = $validation->validateRegDataEquipment($equipmentData);
			
			$equipment = new Equipment($db);
			if ($validStatus['summary'] == 'true') {
				$equipmentData['expire'] = new TypeChain($equipmentData['expire_date'],'date',$db,$equipmentData['department_id'],'department');
//var_dump($equipmentData['expire']->formatInput());
				//	setter injection								
				$equipment->setTrashRecord(new Trash($db));
				$equipment->addNewEquipment($equipmentData);
				
				//save to bridge
				$company = new Company($db);
				$companyID = $company->getCompanyIDbyDepartmentID($_POST['department_id']);							
				$voc2vps = new VOC2VPS($db);							
				$customerLimits = $voc2vps->getCustomerLimits($companyID);
				$limit = array (
					'limit_id'	 	=> 3,
					'current_value' => $customerLimits['Source count']['current_value']+1,
					'max_value' 	=> $customerLimits['Source count']['max_value']
				);								
				$voc2vps->setCustomerLimitByID($companyID, $limit);
			}
			
			echo json_encode($validStatus);	
			break;
	}
	
?>
