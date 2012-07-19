<?php

chdir('../..');

require_once('modules/Reform.inc.php');
require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');

$site_path = getcwd() . DIRECTORY_SEPARATOR;
define('site_path', $site_path);

//	Include Class Autoloader
require_once('modules/classAutoloader.php');

$xnyo = new Xnyo();
$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;
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

		$xnyo->filter_post_var("model_number", "text");
		$xnyo->filter_post_var("serial_number", "text");

		$xnyo->filter_post_var("equipment_filter_id", "text");
		$xnyo->filter_post_var("equipment_filter_name", "text");
		$xnyo->filter_post_var("equipment_height_size", "text");
		$xnyo->filter_post_var("equipment_width_size", "text");
		$xnyo->filter_post_var("equipment_length_size", "text");
		$xnyo->filter_post_var("equipment_filter_quantity", "text");
		$xnyo->filter_post_var("equipment_filter_type", "text");

		$xnyo->filter_post_var("equipment_lighting_id", "text");
		$xnyo->filter_post_var("equipment_lighting_name", "text");
		$xnyo->filter_post_var("equipment_lighting_size", "text");
		$xnyo->filter_post_var("equipment_lighting_voltage", "text");
		$xnyo->filter_post_var("equipment_lighting_wattage", "text");
		$xnyo->filter_post_var("equipment_lighting_bulb_type", "text");
		$xnyo->filter_post_var("equipment_lighting_color", "text");
		// protecting from xss
		foreach ($_POST as $key => $value) {
			switch ($key) {
				case "expire_date" : break;

				default: {
						$_POST[$key] = Reform::HtmlEncode($value);
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
			"equipment_id" => $_POST["id"],
			"name" => $_POST["equip_desc"],
			"department_id" => $_POST['department_id'],
			"equip_desc" => $_POST["equip_desc"],
			"inventory_id" => $inventoryID,
			"permit" => $_POST["permit"],
			"expire_date" => $_POST["expire_date"],
			"daily" => $_POST["daily"],
			"dept_track" => $deptTrack,
			"facility_track" => $facilityTrack,
			"model_number" => $_REQUEST["model_number"],
			"serial_number" => $_REQUEST["serial_number"],
			"creater_id" => 18
		);

		if (trim($regData["daily"]) == "") {
			$regData["daily"] = 0;
		}

		$validation = new Validation($db);
		$validateStatus = $validation->validateRegDataEquipment($regData);

		if ($validateStatus["summary"] == "true") {

			//	convert date to timestamp
			$regData['expire'] = new TypeChain($regData['expire_date'], 'date', $db, $regData['department_id'], 'department');

			$equipment = new Equipment($db);
			//	setter injection
			$equipment->setTrashRecord(new Trash($db));
			$equipment->setEquipmentDetails($regData);

			// add filters
			$equipment_filter_id = explode(',', $_REQUEST['equipment_filter_id']);
			$equipment_filter_name = explode(',', $_REQUEST['equipment_filter_name']);
			$equipment_height_size = explode(',', $_REQUEST['equipment_height_size']);
			$equipment_width_size = explode(',', $_REQUEST['equipment_width_size']);
			$equipment_length_size = explode(',', $_REQUEST['equipment_length_size']);
			$equipment_filter_quantity = explode(',', $_REQUEST['equipment_filter_quantity']);
			$equipment_filter_type = explode(',', $_REQUEST['equipment_filter_type']);

			$equipmentFilterCount = sizeof($equipment_filter_name) - 1; // delete last because empty element
			for ($i = 0; $i < $equipmentFilterCount; $i++) {
				$equipmentFilter = new EquipmentFilter($db, $equipment_filter_id[$i]);
				if (isset($equipmentFilter->name)) {
					$equipmentFilter->equipment_filter_id = $equipment_filter_id[$i];
				} else {
					$equipmentFilter->equipment_filter_id = null;
				}
				//	$equipmentFilter->equipment_filter_id = $equipmentFilter->getFiterIdByName($equipment_filter_name[$i]);
				$equipmentFilter->equipment_id = $regData['equipment_id'];
				$equipmentFilter->name = $equipment_filter_name[$i];
				$equipmentFilter->height_size = $equipment_height_size[$i];
				$equipmentFilter->width_size = $equipment_width_size[$i];
				$equipmentFilter->length_size = $equipment_length_size[$i];
				$equipmentFilter->qty = $equipment_filter_quantity[$i];
				$equipmentFilter->equipment_filter_type_id = $equipment_filter_type[$i];
				$validateStatusEquipmentFilter = $validation->validateRegDataEquipmentFilter($equipmentFilter, $equipment_filter_id[$i]);
				if ($validateStatusEquipmentFilter["summary"] == "true") {
					$equipmentFilter->save();
				}
				$validateStatus = array_merge($validateStatus, $validateStatusEquipmentFilter);
			}

			// add lighting
			$equipment_lighting_id = explode(',', $_REQUEST['equipment_lighting_id']);
			$equipment_lighting_name = explode(',', $_REQUEST['equipment_lighting_name']);
			$equipment_lighting_size = explode(',', $_REQUEST['equipment_lighting_size']);
			$equipment_lighting_voltage = explode(',', $_REQUEST['equipment_lighting_voltage']);
			$equipment_lighting_wattage = explode(',', $_REQUEST['equipment_lighting_wattage']);
			$equipment_lighting_bulb_type = explode(',', $_REQUEST['equipment_lighting_bulb_type']);
			$equipment_lighting_color = explode(',', $_REQUEST['equipment_lighting_color']);

			$equipmentLightingCount = sizeof($equipment_lighting_name) - 1; // delete last because empty element
			for ($i = 0; $i < $equipmentLightingCount; $i++) {
				$equipmentLighting = new EquipmentLighting($db, $equipment_lighting_id[$i]);
				//        $equipmentLighting->equipment_lighting_id = $equipmentLighting->getLightingIdByName($equipment_lighting_name[$i]);
				if (isset($equipmentLighting->name)) {
					$equipmentLighting->equipment_lighting_id = $equipment_lighting_id[$i];
				} else {
					$equipmentLighting->equipment_lighting_id = null;
				}
				$equipmentLighting->equipment_id = $regData['equipment_id'];
				$equipmentLighting->name = $equipment_lighting_name[$i];
				$equipmentLighting->size = $equipment_lighting_size[$i];
				$equipmentLighting->voltage = $equipment_lighting_voltage[$i];
				$equipmentLighting->wattage = $equipment_lighting_wattage[$i];
				$equipmentLighting->bulb_type = $equipment_lighting_bulb_type[$i];
				$equipmentLighting->color = $equipment_lighting_color[$i];
				$validateStatusEquipmentLighting = $validation->validateRegDataEquipmentLighting($equipmentLighting, $equipment_lighting_id[$i]);
				if ($validateStatusEquipmentLighting["summary"] == "true") {
					$equipmentLighting->save();
				}
				$validateStatus = array_merge($validateStatus, $validateStatusEquipmentLighting);
			}
			$validStatus = "true";
			foreach ($validateStatus as $key => $valid) {
				if ($valid == 'failed') {
					$validStatus = "false";
				}
			}
			// result validate status
			$validateStatus['summary'] = $validStatus;
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

		$xnyo->filter_post_var("model_number", "text");
		$xnyo->filter_post_var("serial_number", "text");
		$xnyo->filter_post_var("equipment_filter_id", "text");
		$xnyo->filter_post_var("equipment_filter_name", "text");
		$xnyo->filter_post_var("equipment_height_size", "text");
		$xnyo->filter_post_var("equipment_width_size", "text");
		$xnyo->filter_post_var("equipment_length_size", "text");
		$xnyo->filter_post_var("equipment_filter_quantity", "text");
		$xnyo->filter_post_var("equipment_filter_type", "text");

		$xnyo->filter_post_var("equipment_lighting_id", "text");
		$xnyo->filter_post_var("equipment_lighting_name", "text");
		$xnyo->filter_post_var("equipment_lighting_size", "text");
		$xnyo->filter_post_var("equipment_lighting_voltage", "text");
		$xnyo->filter_post_var("equipment_lighting_wattage", "text");
		$xnyo->filter_post_var("equipment_lighting_bulb_type", "text");
		$xnyo->filter_post_var("equipment_lighting_color", "text");
		// protecting from xss
		foreach ($_POST as $key => $value) {
			switch ($key) {
				case "expire_date" : break;

				default: {
						$_POST[$key] = Reform::HtmlEncode($value);
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
			"department_id" => $_POST['department_id'],
			"equip_desc" => $_POST["equip_desc"],
			"inventory_id" => $inventoryID,
			"permit" => $_POST["permit"],
			"expire_date" => $_POST["expire_date"],
			"daily" => $_POST["daily"],
			"dept_track" => $deptTrack,
			"facility_track" => $facilityTrack,
			"model_number" => $_REQUEST["model_number"],
			"serial_number" => $_REQUEST["serial_number"],
			"creater_id" => 18
		);

		if (trim($equipmentData["daily"]) == "") {
			$equipmentData["daily"] = 0;
		}

		$validation = new Validation($db);
		$validStatus = $validation->validateRegDataEquipment($equipmentData);

		$equipment = new Equipment($db);
		if ($validStatus['summary'] == 'true') {
			$equipmentData['expire'] = new TypeChain($equipmentData['expire_date'], 'date', $db, $equipmentData['department_id'], 'department');

			//	setter injection
			$equipment->setTrashRecord(new Trash($db));
			$equipmentId = $equipment->addNewEquipment($equipmentData);
			// add filters
			$equipment_filter_id = explode(',', $_REQUEST['equipment_filter_id']);
			$equipment_filter_name = explode(',', $_REQUEST['equipment_filter_name']);
			$equipment_height_size = explode(',', $_REQUEST['equipment_height_size']);
			$equipment_width_size = explode(',', $_REQUEST['equipment_width_size']);
			$equipment_length_size = explode(',', $_REQUEST['equipment_length_size']);
			$equipment_filter_quantity = explode(',', $_REQUEST['equipment_filter_quantity']);
			$equipment_filter_type = explode(',', $_REQUEST['equipment_filter_type']);

			$equipmentFilterCount = sizeof($equipment_filter_id) - 1; // delete last because empty element
			for ($i = 0; $i < $equipmentFilterCount; $i++) {
				$equipmentFilter = new EquipmentFilter($db);
				$equipmentFilter->equipment_id = $equipmentId;
				$equipmentFilter->name = $equipment_filter_name[$i];
				$equipmentFilter->height_size = $equipment_height_size[$i];
				$equipmentFilter->width_size = $equipment_width_size[$i];
				$equipmentFilter->length_size = $equipment_length_size[$i];
				$equipmentFilter->qty = $equipment_filter_quantity[$i];
				$equipmentFilter->equipment_filter_type_id = $equipment_filter_type[$i];
				$validateStatusEquipmentFilter = $validation->validateRegDataEquipmentFilter($equipmentFilter, $equipment_filter_id[$i]);
				if ($validateStatusEquipmentFilter["summary"] == "true") {
					$equipmentFilter->save();
				}
				$validStatus = array_merge($validStatus, $validateStatusEquipmentFilter);
			}

			// add lighting
			$equipment_lighting_id = explode(',', $_REQUEST['equipment_lighting_id']);
			$equipment_lighting_name = explode(',', $_REQUEST['equipment_lighting_name']);
			$equipment_lighting_size = explode(',', $_REQUEST['equipment_lighting_size']);
			$equipment_lighting_voltage = explode(',', $_REQUEST['equipment_lighting_voltage']);
			$equipment_lighting_wattage = explode(',', $_REQUEST['equipment_lighting_wattage']);
			$equipment_lighting_bulb_type = explode(',', $_REQUEST['equipment_lighting_bulb_type']);
			$equipment_lighting_color = explode(',', $_REQUEST['equipment_lighting_color']);

			$equipmentLightingCount = sizeof($equipment_lighting_id) - 1; // delete last because empty element
			for ($i = 0; $i < $equipmentLightingCount; $i++) {
				$equipmentLighting = new EquipmentLighting($db);
				$equipmentLighting->equipment_id = $equipmentId;
				$equipmentLighting->name = $equipment_lighting_name[$i];
				$equipmentLighting->size = $equipment_lighting_size[$i];
				$equipmentLighting->voltage = $equipment_lighting_voltage[$i];
				$equipmentLighting->wattage = $equipment_lighting_wattage[$i];
				$equipmentLighting->bulb_type = $equipment_lighting_bulb_type[$i];
				$equipmentLighting->color = $equipment_lighting_color[$i];
				$validateStatusEquipmentLighting = $validation->validateRegDataEquipmentLighting($equipmentLighting, $equipment_lighting_id[$i]);
				if ($validateStatusEquipmentLighting["summary"] == "true") {
					$equipmentLighting->save();
				}
				$validStatus = array_merge($validStatus, $validateStatusEquipmentLighting);
			}

			//save to bridge
			$company = new Company($db);
			$companyID = $company->getCompanyIDbyDepartmentID($_POST['department_id']);
			$voc2vps = new VOC2VPS($db);
			$customerLimits = $voc2vps->getCustomerLimits($companyID);
			$limit = array(
				'limit_id' => 3,
				'current_value' => $customerLimits['Source count']['current_value'] + 1,
				'max_value' => $customerLimits['Source count']['max_value']
			);
			$voc2vps->setCustomerLimitByID($companyID, $limit);
			
			$validateStatus = "true";
			foreach ($validStatus as $key => $valid) {
				if ($valid == 'failed') {
					$validateStatus = "false";
				}
			}
			// result validate status
			$validStatus['summary'] = $validateStatus;
		}

		echo json_encode($validStatus);
		break;
}
?>
