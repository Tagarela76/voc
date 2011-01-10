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
			case "addProduct":								
				
				$xnyo->filter_get_var('productID', 'text');
				$xnyo->filter_get_var('quantity', 'text');
				$xnyo->filter_get_var('unittype', 'text');
																
				$request = $_GET;

				$validation = new Validation($db);
				$validStatus = $validation->validateRegData(array('quantity' => $request['quantity']));
				
				//Ksenya: we have new validation in Mix->calculateCurrantUsage
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
//				$inventory = new Inventory($db);		
//				$inventory->setType($request['tab']);		
//				switch ($request['tab']) {
//					case Inventory::PAINT_MATERIAL:											
//						$product = new PaintMaterial($db);
//						$productDetails = $product->getProductDetails($request['productID']);
//						$product->setProductID($request['productID']);									
//						$product->setSupplier($productDetails['supplier_id']);
//						$product->setProductNR($productDetails['product_nr']);
//						$product->setName($productDetails['name']);
//						break;
//					case Inventory::PAINT_ACCESSORY:
//						$product = new PaintAccessory($db);
//						$product->setAccessoryID($request['productID']);
//						$productDetails = $product->getAccessoryDetails();
//						$product->setAccessoryID($request['productID']);									
//						//$product->setSupplier($productDetails['supplier_id']);
//						//$product->setProductNR($productDetails['product_nr']);						
//						$product->setAccessoryName($productDetails['name']);						
//						break;
//					default:					
//						throw new Exception('404');
//					break;	
//				}
//				$smarty->assign('parentCategory', 'facility');	//	заблуждение, на самом деле нужно для поля Селект. Запарился я что-то
//				$smarty->assign('inventory', $inventory);			
//				$smarty->assign('request', $request);
//				$smarty->assign('product', $product);
//				$smarty->display('tpls:inventory/design/addInventoryRow.tpl');			
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
