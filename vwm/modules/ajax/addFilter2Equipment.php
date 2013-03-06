<?php
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
    require $site_path.'../vendor/autoload.php';

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

	//	filter action var
	$xnyo->filter_get_var('action', 'text');

	$action = $_GET['action'];
	try {
		//	logged in?
		$user = new User($db, $xnyo, $access, $auth);
		if (!$user->isLoggedIn()) {
			throw new Exception('deny');
		}

		switch ($action) {

			case "isMixDescrUnique":
				{

					$validation = new Validation($db);

					$data = array("description" => mysql_escape_string($_GET['descr']),
									"department_id" => mysql_escape_string($_GET['depID']));


					$result = $validation->isUniqueUsage($data);

					$response = array('isUnique'=>$result, 'other' => array($data));
					echo json_encode($response);

					break;
				}

			case "productSupportWeight":
				{
					$productID = mysql_escape_string($_GET['productID']);
					$unittypeID = mysql_escape_string($_GET['unittypeID']);

					$productObj = new Product($db);
					$productDetails = $productObj->getProductDetails($productID);

					try {
						$density = floatval($productDetails['density']);
					}catch(Exception $e) {
						$density = 0;
					}

					$result = true;
					if($density < 1) { //Check, if product has'nt density, can we calculate VOC in future without density with current unit type.

						$unittypeObj = new Unittype($db);
						$res = $unittypeObj->isWeightOrVolume($unittypeID);

						if($res == "weight") {
							$result = false;
						}
					}

					echo json_encode(Array("supportWeight" => $result));

					break;
				}

			case "getProductInfo":
				{
					$productID = mysql_escape_string($_GET['productID']);

					$productObj = new Product($db);
					$productDetails = $productObj->getProductDetails($productID);

					echo json_encode($productDetails);
					break;
				}

			case "getVOC":
				{
					$products_json = '[["454","3","1"],["453","0.04","1"],["173","0.03","1"],["145","0.04","1"]]';

					$products = json_decode($products_json);


					break;
				}


			case "addProduct":

				$xnyo->filter_get_var('productID', 'text');
				$xnyo->filter_get_var('quantity', 'text');
				$xnyo->filter_get_var('unittype', 'text');

				$request = $_GET;

				$validation = new Validation($db);
				$validStatus = $validation->validateRegData(array('quantity' => $request['quantity']));

				//we have new validation in Mix->calculateCurrantUsage
				//but we still need those - to disallow product adding
				$isProdConflict = $validation->checkWeight2Volume($request['productID'], $request['unittype']);
				if ($isProdConflict !== true) {
					$validStatus['summary'] = 'false';
					$validStatus['description'] = $isProdConflict;
				}

				//	<validation completed>

				$productMix = new MixProduct($db);
				if ($validStatus['summary'] == 'true') {
					if ($productMix->initializeByID($request['productID'])) {
						$productMix->product_id = $request['productID'];
						$productMix->quantity = $request['quantity'];
						$productMix->unit_type = $request['unittype'];
					} else {
						//	no such product at DB; looks like fraud
						$validStatus['summary'] = 'false';
						$validStatus['description'] = 'No such product!';
					}
				}

				// prepare product for view
				$product = array (
					'product_id' => $productMix->product_id,
				 	'supplier' => $productMix->getSupplier(),
					'product_nr' => $productMix->getProductNR(),
					//'description' => $productMix->ge,
					'quantity' => $productMix->quantity,
					'unittype' => $productMix->unit_type,
				);
				$response = array('validStatus'=>$validStatus, 'product'=>$product);
				echo json_encode($response);
				break;
		}

	} catch(Exception $e) {
		switch ($e->getMessage()) {
			case '404':
				$smarty->display('tpls:errors/404.tpl');
				break;
			case 'deny':
				$smarty->display('tpls:errors/deny.tpl');
			default:
				$smarty->assign('message', $e->getMessage());
				$smarty->display('tpls:errors/other.tpl');
		}
	}

?>
