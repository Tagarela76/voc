<?php
	chdir('../..');		
	
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
	
	$xnyo->filter_get_var('query', 'text');
	$xnyo->filter_get_var('departmentID', 'text');
	$xnyo->filter_get_var('facilityID', 'text');
	$xnyo->filter_get_var('category', 'text');
	$xnyo->filter_get_var('field', 'text');
	
	$request = $_GET;		
	switch ($request['category']) {
		case "mix":
			$mixObj = new Mix($db);
//			$mixList = $mixObj->mixAutocomplete($request['query'], $request['departmentID']);
//			if ($mixList) {
//				foreach ($mixList as $mix) {
//					$suggestions[] = $mix['description'];
//				}		

			$suggestions = $mixObj->mixAutocomplete($request['query'], $request['departmentID']);	//for new autocomplete
			if ($suggestions) {																		//new
				$response = array('query'=>$request['query'], 'suggestions'=>$suggestions);			
				echo json_encode($response);	
			}	
			break;
			
			
		case "product":									
			$query = "SELECT f.company_id FROM ".TB_DEPARTMENT." d, ".TB_FACILITY." f " .
					"WHERE d.facility_id = f.facility_id " .
					"AND d.department_id = ".$request['departmentID'];
			$db->query($query);
			
			if ($db->num_rows() > 0) {
				$companyID = $db->fetch(0)->company_id;
				
				$productObj = new Product($db);
				$productList = $productObj->productAutocomplete($request['query'], $companyID);

				if ($productList) {
					foreach ($productList as $product) {
						$suggestions[] = $product['productNR'];
					}
					
					$response = array('query'=>$request['query'], 'suggestions'=>$suggestions);
					echo json_encode($response);	
				}	
			}
			
			break;
		case "productAll":			
			$productObj = new Product($db);
			$productList = $productObj->productAutocomplete($_GET['query']);
			
			if ($productList) {
				foreach ($productList as $product) {
					$suggestions[] = $product['productNR'];
				}
				$response = array('query'=>$_GET['query'], 'suggestions'=>$suggestions);
				echo json_encode($response);	
			}	
			break;
		case 'logbook':
			$logbookObj = new Logbook($db, $request['facilityID']);
			$suggestions = $logbookObj->logbookAutocomplete($_GET['query']);
			//$suggestions = array($_GET['query'], $request['facilityID']);
			$response = array('query'=>$_GET['query'], 'suggestions'=>$suggestions);
			echo json_encode($response);
			break;
			
		case 'track':
			$trackingManagerObj = new TrackManager($db);
			$trackingList = $trackingManagerObj->trackAutocomplete($_GET['query']);
			$response = array('query'=>$_GET['query'], 'suggestions'=>$trackingList);
			echo json_encode($response);			
			break;
			
	}
	
?>
