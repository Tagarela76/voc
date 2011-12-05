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
    $xnyo->filter_get_var('subBookmark', 'text');
	
	$request = $_GET;
	switch ($request['category']) {
		case "mix":
			$mixObj = new Mix($db);
			$suggestions = $mixObj->mixAutocomplete($request['query'], $request['departmentID']);	//for new autocomplete
			if ($suggestions) {																		//new
				$response = array('query'=>$request['query'], 'suggestions'=>$suggestions);			
				echo json_encode($response);
			}
			break;

		case "product":
			/**Not work yet**/
                     if(isset($request['facilityID'])) {
                            $query = "SELECT f.company_id FROM ".TB_FACILITY." f " .
					"WHERE f.facility_id = ".$request['facilityID'];
                        } elseif (isset($request['departmentID'])) {
                            $query = "SELECT f.company_id FROM ".TB_DEPARTMENT." d, ".TB_FACILITY." f " .
					"WHERE d.facility_id = f.facility_id " .
					"AND d.department_id = ".$request['departmentID'];
                        }
			
			$db->query($query);
                        
			if ($db->num_rows() > 0) {
				$companyID = $db->fetch(0)->company_id;
				$productObj = new Product($db);
				$sub = $request['subBookmark'];
				$productList = $productObj->productAutocompleteAdmin($request['query'],$sub );
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
                        
		case "logbook":
			$logbookObj = new Logbook($db, $request['facilityID']);
			$suggestions = $logbookObj->logbookAutocomplete($_GET['query']);
			$response = array('query'=>$_GET['query'], 'suggestions'=>$suggestions);
			echo json_encode($response);
			break;

		case "track":
			$trackingManagerObj = new TrackManager($db);
			$trackingList = $trackingManagerObj->trackAutocomplete($_GET['query']);
			$response = array('query'=>$_GET['query'], 'suggestions'=>$trackingList);
			echo json_encode($response);
			break;
                    
        case "salescontacts":
                        $sub = $request['subBookmark'];
                        if($sub == '') {
                                $sub = "contacts";
                        }
                        $sub = strtolower($sub);
                        $sub = htmlentities($sub);
                         $contactObj = new SalesContactsManager($db);
                                $suggestions = $contactObj->contactAutocomplete($request['query'], $sub);
                                if ($suggestions) {																		//new
                                        $response = array('query'=>$request['query'], 'suggestions'=>$suggestions);			
                                        echo json_encode($response);
                                }
                        break;
        }

?>
