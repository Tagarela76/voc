<?php
	
	class MInventory {
		
		function MInventory() {
		}
		
		
		
		public function getNewObject(array $params) {	    
			return new Inventory($params['db'], $params['id']);
		}
		
		/**
		 * ?action=browseCategory&category=facility&id=82&bookmark=inventory&tab=material
		 * @param array parameters: $facility obj, $request [$departments obj], $sort
		 */
		public function prepareList($params) {
			extract($params);    		
			$result = array();
			//	prepare inventory list depending from caller	    		    		    		    	    	    	    
			if (isset($departments) && $request['tab'] == Inventory::PAINT_MATERIAL) {
				$inventoryList = $departments->getInventoryList($sort);//	TODO: $customID
			} else {
				$inventoryList = $facility->getInventoryList($sort);	//	TODO: $customID	
			}
			
			if ($inventoryList !== false) {
				if ($request['tab'] == Inventory::PAINT_ACCESSORY) {			    				    	
					$result['childCategoryItems'] = $inventoryList[Inventory::PAINT_ACCESSORY];										
				} elseif($request['tab'] == Inventory::PAINT_MATERIAL) {			
					$result['childCategoryItems'] = $inventoryList[Inventory::PAINT_MATERIAL];
				} else {
					throw new Exception('404');
				}			
			}
			
			//	set link url
			foreach ($result['childCategoryItems'] as $inventory) {								
				$inventory->url = "?action=viewDetails&category=inventory&id=".$inventory->getID()."&".$request['category']."ID=".$request['id'];																					
			}	
			$result['tpl'] = 'inventory/design/inventoryListoop.tpl';
			return $result;
		}
		
		/**
		 * function prepareView($params)
		 * View inventory details
		 * return prepared for smarty params(inventory obj, editURL, deleteURL)
		 * @param $params array of params: db, user, request
		 */   	
		public function prepareView($params) {
			extract($params);
			$result = array();
			
			$inventory = new Inventory($db, $request['id']);
			if ($inventory->getName() === null) {
				throw new Exception('404');	//	no such inventory
			}	
			
			if (isset($request['facilityID'])) {		
				//	Access control
				if (!$user->checkAccess('facility', $request['facilityID'])) {						
					throw new Exception('deny');
				}					
				
				$result['editUrl'] = '?action=edit&category=inventory&id='.$request["id"].'&facilityID='.$request["facilityID"];
				$result['deleteUrl'] = '?action=deleteItem&category=inventory&id='.$request["id"].'&facilityID='.$request["facilityID"].'&tab='.$inventory->getType();
				
			} elseif (isset($request['departmentID'])) {
				//	Access control
				if (!$user->checkAccess('department', $request['departmentID'])) {						
					throw new Exception('deny');
				}					
				
				if($inventory->getType() == Inventory::PAINT_MATERIAL) {									
					foreach ($inventory->getProducts() as $product) {
						foreach($product->getUseLocation() as $useLocation) {
							if ($useLocation['departmentID'] == $request['departmentID']) {
								$product->setTotalQty($useLocation['totalQty']);
								$product->setToDateLeft($useLocation['totalQty'] - $useLocation['used']);	
								$product->setLastInventory($useLocation['lastInventory']);
							}
						} 									
					}	
				}								
				
				$result['editUrl'] = '?action=edit&category=inventory&id='.$request["id"].'&departmentID='.$request["departmentID"];
				$result['deleteUrl'] = '?action=deleteItem&category=inventory&id='.$request["id"].'&departmentID='.$request["departmentID"].'&tab='.$inventory->getType();							
			} else {
				throw new Exception('404');
			}		
			$result['inventory'] = $inventory;
			$result['tpl'] = 'inventory/design/viewInventoryNew.tpl';
			return $result;
		}
		
		
		/**
		 * function prepareAdd($params)
		 * Add inventory 
		 * return prepared for smarty params
		 * @param $params array of params: db, request, form, parentCategory, smarty
		 */     	
		public function prepareAdd($params) {
			extract($params);
			$result = array();
			
			if (count($form) > 0) { 
				if ($parentCategory == 'facility') {
					$department = new Department($db);																										
					$inventory = new Inventory($db);									
					$inventory->setName($form['inventory_name']);
					$inventory->setDescription($form['inventory_desc']);
					$inventory->setType($request['tab']);																									
					$inventory->setFacilityID($request["facilityID"]);
					switch ($request['tab']) {
					
						case Inventory::PAINT_MATERIAL:
							foreach($form['product_id'] as $key=>$productID) {																
							$product = new PaintMaterial($db);
							$data = $product->getProductDetails($productID);
							
							$product->setProductID($productID);
							$product->setSupplier($data['supplier_id']);
							$product->setProductNR($data['product_nr']);
							$product->setName($data['name']);
							$product->setOS_use($form['OS_use'][$productID]);
							$product->setCS_use($form['CS_use'][$productID]);
							$product->setStorageLocation($form['storageLocation'][$productID]);
							$product->setTotalQty($form['totalQty'][$productID]);
							//	useLocation
							if (isset($form['useLocation'][$productID])) {
								foreach ($form['useLocation'][$productID] as $useLocationDepID) {
									$departmentDetails = $department->getDepartmentDetails($useLocationDepID);
									$useLocation = array(
										'departmentID' 	=> $departmentDetails['department_id'],
										'name' 			=> $departmentDetails['name'],
										'totalQty'		=> 0
									); 
									$product->addUseLocation($useLocation);									
								}
							}																				
							$inventory->addProduct($product);
							
							//	BAD VALIDATION MODEL
							$product = array(
								"supplier_id"		=>	$data['supplier_id'],
								"product_id"		=>	$productID,
								"product_nr"		=>	$data['product_nr'],									
								"name"				=>	$data['name'],
								"quantity"			=>	$form['totalQty'][$productID],
								"OSuse"				=>	$form['OS_use'][$productID],
								"CSuse"				=>	$form['CS_use'][$productID],
								"locationStorage"	=>	$form['storageLocation'][$productID],
								"locationUse"		=>	$form['useLocation'][$productID]
							);
							$products[]=$product;							
						}								
						break;
						
						case Inventory::PAINT_ACCESSORY:
							
							foreach($form['product_id'] as $key=>$productID) {
							$product = new PaintAccessory($db);
							$product->setAccessoryID($productID);
							$data = $product->getAccessoryDetails();
							
							$product->setAccessoryID($productID);
							//$product->setSupplier($data['supplier_id']);
							//$product->setProductNR($data['product_nr']);
							$product->setAccessoryName($data['name']);
							
							$product->setUnitAmount($form['unitAmount'][$productID]);
							$product->setUnitCount($form['unitCount'][$productID]);
							$product->setUnitQuantity($form['unitQuantity'][$productID]);
							$totalQty = (float)$form['unitAmount'][$productID] * (float)$form['unitQuantity'][$productID];
							$product->setTotalQuantity($totalQty);
							
							$inventory->addProduct($product);
							
							$result['tab'] = $request['tab'];
							
							//	getting accessories list
							$company = new Company($db);
							
							$cAccessory = new Accessory($db);
							$dataAccessory = $cAccessory->getAllAccessory($company->getCompanyIDbyDepartmentID($useLocationDepID));
							$result['accessory'] = $dataAccessory;//$smarty->assign('accessory', $dataAccessory);
							
							//	BAD VALIDATION MODEL
							$product = array(
								"supplier_id"		=>	$data['supplier_id'],
								"product_id"		=>	$productID,
								"product_nr"		=>	$data['product_nr'],									
								"name"				=>	$data['name'],
								"quantity"			=>	$totalQty,					//	the same rule as quantity
								"OSuse"				=>	$form['unitAmount'][$productID],	//	...
								"CSuse"				=>	$form['unitQuantity'][$productID],//	...
								"locationStorage"	=>	$form['unitCount'][$productID],	//	the same rule as location storage
							);				
							$products[]=$product;											
						}																																				
						break;
						
						default:					
							throw new Exception('404');
						break;	
					}																																					
					
					$regData = array (							
						'inventory_name'	=>	$form['inventory_name'],
						'name'				=>	$form['inventory_name'],
						'inventory_desc'	=>	$form['inventory_desc']									
													  
					);
					$regData['products'] = $products;																		
					
					$validate = new Validation($db);
					$validateStatus = $validate->validateRegDataInventory($regData, count($regData['products']));
					if (!($validate->isUniqueName("inventory", $regData["inventory_name"], $request['facilityID'], $regData["inventory_id"],$request['tab']))) {
						$validateStatus['summary'] = 'false';
						$validateStatus['inventory_name'] = 'alredyExist';
					}
					
					if ($validateStatus['summary'] == 'true') {
						//	ok, save	
						
						//	setter injection
						$inventory->setTrashRecord(new Trash($db));						
						$inventory->save();
						
						//	redirect
						header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=inventory&tab=".$request['tab']."&notify=9");
						die();	
						
					} else {										
						$result['inventory'] = $inventory;									
						$notify = new Notify($smarty);
						$notify->formErrors();																
						$result['validStatus'] = $validateStatus;													
					}
					
					//	parentCategory		
				} elseif ($parentCategory == 'department') {
					
					$department = new Department($db);
					$department->initializeByID($request['departmentID']);
					
					switch ($request['tab']) {
						case Inventory::PAINT_MATERIAL:
							
							//	REMOVED INVENTORIES PROCESSING
							$fullInventoryList = $department->getInventoryList();									
						foreach ($fullInventoryList[Inventory::PAINT_MATERIAL] as $inventoryFromDep) {
							$deleted = true;
							foreach($form['id'] as $inventoryID) {
								if ($inventoryFromDep->getID() == $inventoryID) {
									$deleted = false;
									break;
								}
							}										
							if ($deleted) {
								foreach ($inventoryFromDep->getProducts() as $product) {
									foreach($product->getUseLocation() as $useLocation) {
										if ($useLocation['departmentID'] != $request['departmentID']) {
											$newUseLocations[] = $useLocation;
										}													
									}
									$product->clearUseLocation();
									foreach ($newUseLocations as $newUseLocation) {
										$product->addUseLocation($newUseLocation);
									}													
									$newUseLocations = array();							
								}
								$inventoryFromDep->setTrashRecord(new Trash($db));
								$inventoryFromDep->save();											
							}																				
						}
						
						//	ADDED INVENTORIES PROCESSING
						$departmentDetails = $department->getDepartmentDetails($request['departmentID']);
						$locationOfUse = array(
							'departmentID'	=> $request['departmentID'],
							'name'			=> $departmentDetails['name'],
							'totalQty'		=> 0
						);
						foreach($form['id'] as $inventoryID) {
							$inventory = new Inventory($db, $inventoryID);
							foreach ($inventory->getProducts() as $product) {
								$add = true;
								foreach($product->getUseLocation() as $useLocation) {
									if ($useLocation['departmentID'] == $request['departmentID']) {
										$add = false;	//	use location already added
										break;
									}													
								}
								
								if ($add) {
									$product->addUseLocation($locationOfUse);
								}											
							}
							
							$inventory->setTrashRecord(new Trash($db));
							$inventory->save();
						}
						
						//	redirect
						header("Location: ?action=browseCategory&category=department&id=".$request['departmentID']."&bookmark=inventory&tab=".$request['tab']."&notify=9");
						die();	
						break;
						
						case Inventory::PAINT_ACCESSORY:
							$inventory = new Inventory($db);
						$inventory->setName($form['inventory_name']);
						$inventory->setDescription($form['inventory_desc']);
						$inventory->setType($request['tab']);																									
						$inventory->setFacilityID($department->getFacilityID());
						foreach($form['product_id'] as $key=>$productID) {
							$product = new PaintAccessory($db);
							$product->setAccessoryID($productID);
							$data = $product->getAccessoryDetails();
							
							$product->setAccessoryID($productID);
							//$product->setSupplier($data['supplier_id']);
							//$product->setProductNR($data['product_nr']);
							$product->setAccessoryName($data['name']);
							
							$product->setUnitAmount($form['unitAmount'][$productID]);
							$product->setUnitCount($form['unitCount'][$productID]);
							$product->setUnitQuantity($form['unitQuantity'][$productID]);
							$totalQty = (float)$form['unitAmount'][$productID] * (float)$form['unitQuantity'][$productID];
							$product->setTotalQuantity($totalQty);
							
							$inventory->addProduct($product);
							
							//	BAD VALIDATION MODEL
							$product = array(
								"supplier_id"		=>	$data['supplier_id'],
								"product_id"		=>	$productID,
								"product_nr"		=>	$data['product_nr'],									
								"name"				=>	$data['name'],
								"quantity"			=>	$totalQty,					//	the same rule as quantity
								"OSuse"				=>	$form['unitAmount'][$productID],	//	...
								"CSuse"				=>	$form['unitQuantity'][$productID],//	...
								"locationStorage"	=>	$form['unitCount'][$productID],	//	the same rule as location storage
							);				
							$products[]=$product;											
						}			
						$regData = array (							
							'inventory_name'	=>	$form['inventory_name'],
							'name'				=>	$form['inventory_name'],
							'inventory_desc'	=>	$form['inventory_desc']									
						);
						
						$regData['products'] = $products;																		
						
						$validate = new Validation($db);
						$validateStatus = $validate->validateRegDataInventory($regData, count($regData['products']));
						if (!($validate->isUniqueName("inventory", $regData["inventory_name"], $department->getFacilityID(), $regData["inventory_id"],$request['tab']))) {
							$validateStatus['summary'] = 'false';
							$validateStatus['inventory_name'] = 'alredyExist';
						}
						
						if ($validateStatus['summary'] == 'true') {
							//	ok, save								
							$inventory->save();
							
							//	redirect
							header("Location: ?action=browseCategory&category=department&id=".$request['departmentID']."&bookmark=inventory&tab=".$request['tab']."&notify=9");
							die();	
							
						} else {										
							$result['inventory'] = $inventory;									
							$notify = new Notify($smarty);
							$notify->formErrors();																
							$result['validStatus'] = $validateStatus;													
						}
						break;
						
						default:
							throw new Exception('404');
						break;
					}									
					
				} else {
					throw new Exception('404');
				}															
			}
			
			//	IF ERRORS OR NO POST REQUEST
			
			if ($parentCategory == 'facility') { 
				if (!isset($inventory)) {
					$inventory = new Inventory($db);
					$inventory->setType($request['tab']);
					$result['inventory'] = $inventory;	
				}																											
				
				$result['tab'] = $request['tab'];
				
				//	getting product list
				$productInfo = new Product($db);								
				$facility = new Facility($db);
				$facilityDetails = $facility->getFacilityDetails($request['facilityID']);	
				
				$productList = $productInfo->getFormatedProductList($facilityDetails['company_id']);	//TODO: filter already used products
				//	NICE PRODUCT LIST 
				foreach ($productList as $oneProduct) {
					$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
				}
																
				//$result['product'] = $productList;
				$result['product'] = $productListGrouped;
				
				//	get departments list
				$department = new Department($db);
				$departmentList = $department->getDepartmentListByFacility($request['facilityID']);
				$result['departments'] = $departmentList;
				
				//	getting accessories list
				$cAccessory = new Accessory($db);
				$dataAccessory = $cAccessory->getAllAccessory($facilityDetails['company_id']);
				$result['accessory'] = $dataAccessory;
				$result['tpl'] = 'inventory/design/addInventoryNew.tpl';
			} elseif ($parentCategory == 'department') {
				
				switch($request['tab']) {
					case Inventory::PAINT_MATERIAL:
						$department = new Department($db);								
						$department->initializeByID($request['departmentID']);
						$departmentInventoryList = $department->getInventoryList();
						$facilityInventoryList = $department->getAvailableInventoryList();
						
						$result['department'] = $department;								
						$result['facInventory'] = $facilityInventoryList[Inventory::PAINT_MATERIAL];
						$result['depInventory'] = $departmentInventoryList[Inventory::PAINT_MATERIAL];
						$result['tpl'] = 'inventory/design/manageDepInventory.tpl';
					break;
					case Inventory::PAINT_ACCESSORY:
						
						if (!isset($inventory)) {
							$inventory = new Inventory($db);
							$inventory->setType($request['tab']);
							$result['inventory'] = $inventory;	
						}																											
						
						$result['tab'] = $request['tab'];
						//	TODO: нафтг здесь продукты?
						//	getting product list
						$productInfo = new Product($db);								
						$company = new Company($db);																			
						$productList = $productInfo->getFormatedProductList($company->getCompanyIDbyDepartmentID($request['departmentID']));//TODO: filter already used products
						//	NICE PRODUCT LIST 
						foreach ($productList as $oneProduct) {
							$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
						}														
						//$result['product'] = $productList;
						$result['product'] = $productListGrouped;
						
						//	getting accessories list
						$cAccessory = new Accessory($db);
						$dataAccessory = $cAccessory->getAllAccessory($company->getCompanyIDbyDepartmentID($request['departmentID']));
						$result['accessory'] = $dataAccessory;
						$result['tpl'] = 'inventory/design/addInventoryNew.tpl';
					break;
					default:
						throw new Exception('404');
					break;
				}
				
			}
			
			return $result;
		}
		
		
		/**
    	 * ?action=addItem&category=equipment&departmentID=273
    	 * @param array parameters: $department obj
    	 */
    	public function prepare4equipmentAdd($params) {
    		extract($params);
    		$result = array();
    		$inventoryList = $department->getInventoryList();
    		
    		$result['inventoryList'] = $inventoryList[Inventory::PAINT_MATERIAL];    		
    		$result['inventoryDet'] = $inventoryList[Inventory::PAINT_MATERIAL][0];    
    		return $result;		
    	}
    	
    	
    	
    	/**
    	 * ?action=edit&category=equipment&id=3215031
    	 * @param array parameters: $department obj, $db, $inventory_id
    	 */
    	public function prepare4equipmentEdit($params) {
    		extract($params);
    		$result = array();
    		$inventoryList = $department->getInventoryList();
    		
    		$result['inventoryList'] = $inventoryList[Inventory::PAINT_MATERIAL];    		
    		$result['inventoryDet'] = new Inventory($db, $inventoryID);
    		return $result;   	
    	}
		
		/**
		 * function prepareEdit($params)
		 * Add inventory 
		 * return prepared for smarty params
		 * @param $params array of params: db, request, form, facilityID, smarty
		 */     	
		public function prepareEdit($params) {
			extract($params);
			$result = array();
			//-----------------------------------------------------
			if (count($form) > 0) {
				switch ($request['tab']) {																			
					case Inventory::PAINT_MATERIAL:
						
						//	FACILITY LEVEL
						if (isset($request['facilityID'])) {
	
							$department = new Department($db);
							$inventory = new Inventory($db, $form['id']);
							
							//	no such inventory											
							if ($inventory->getName() === null) {
								throw new Exception('404');
							}
							
							$inventory->setName($form['inventory_name']);
							$inventory->setDescription($form['inventory_desc']);
							$inventory->setType($request['tab']);																									
							$inventory->setFacilityID($facilityID);								
							$inventory->deleteProducts();
							
							foreach($form['product_id'] as $key=>$productID) {									
								$product = new PaintMaterial($db);
								$data = $product->getProductDetails($productID);
								
								$product->setProductID($productID);
								$product->setSupplier($data['supplier_id']);
								$product->setProductNR($data['product_nr']);
								$product->setName($data['name']);
								$product->setOS_use($form['OS_use'][$productID]);
								$product->setCS_use($form['CS_use'][$productID]);
								$product->setStorageLocation($form['storageLocation'][$productID]);
								$product->setTotalQty($form['totalQty'][$productID]);
								//	useLocation
								if (isset($form['useLocation'][$productID])) {
									foreach ($form['useLocation'][$productID] as $useLocationDepID) {
										$departmentDetails = $department->getDepartmentDetails($useLocationDepID);
										$useLocation = array(
											'departmentID' 	=> $departmentDetails['department_id'],
											'name' 			=> $departmentDetails['name'],
											'totalQty'		=> 0
										); 
										$product->addUseLocation($useLocation);									
									}
								}
																			
								$inventory->addProduct($product);

								//	BAD VALIDATION MODEL
								$product = array(
									"supplier_id"		=>	$data['supplier_id'],
									"product_id"		=>	$productID,
									"product_nr"		=>	$data['product_nr'],									
									"name"				=>	$data['name'],
									"quantity"			=>	$form['totalQty'][$productID],
									"OSuse"				=>	$form['OS_use'][$productID],
									"CSuse"				=>	$form['CS_use'][$productID],
									"locationStorage"	=>	$form['storageLocation'][$productID],
									"locationUse"		=>	$form['useLocation'][$productID]
								);
								$products[]=$product;							
							}
							
							//	DEPARTMENT LEVEL	
						} elseif (isset($request['departmentID'])) {
	
							$inventory = new Inventory($db, $form['id']);		
							
							//	no such inventory											
							if ($inventory->getName() === null) {
								throw new Exception('404');
							}
							$form['inventory_name'] = $inventory->getName();
							$form['name'] = $inventory->getName();
							$form['inventory_desc'] = $inventory->getDescription();
							foreach($inventory->getProducts() as $product) {												
								foreach($form['product_id'] as $productID) {																										
									if ($product->getProductID() == $productID) {
										$newUseLocation = array();
										
										$product->setOS_use($form['OS_use'][$productID]);
										$product->setCS_use($form['CS_use'][$productID]);
										foreach ($product->getUseLocation() as $key=>$useLocation) {
											if ($useLocation['departmentID'] == $request['departmentID']) {
												$useLocation['totalQty'] = $form['totalQty'][$productID];												
											}
											$newUseLocation[] = $useLocation;
										}
										$product->clearUseLocation();														
										foreach ($newUseLocation as $useLocation) {
											$product->addUseLocation($useLocation);															
										}																																																																	
										
										//	BAD VALIDATION MODEL
										$productForValidation = array(															
											"quantity"	=>	$form['totalQty'][$productID],
											"OSuse"		=>	$form['OS_use'][$productID],
											"CSuse"		=>	$form['CS_use'][$productID]													
										);
										$products[]=$productForValidation;							
									}												
								}												
							}																						
							
							//	HZ level																						
						} else {
							throw new Exception('404');					
						}											
					break;
					
					
					case Inventory::PAINT_ACCESSORY:
					
					$inventory = new Inventory($db, $form['id']);
					
					//	no such inventory											
					if ($inventory->getName() === null) {
						throw new Exception('404');
					}
					
					$inventory->setName($form['inventory_name']);
					$inventory->setDescription($form['inventory_desc']);
					$inventory->setType($request['tab']);																									
					$inventory->setFacilityID($facilityID);								
					$inventory->deleteProducts();
					
					$result['tab'] = $inventory->getType();
					//	getting accessories list
					$company = new Company($db);
					$cAccessory = new Accessory($db);
					$dataAccessory = $cAccessory->getAllAccessory($company->getCompanyIDbyDepartmentID($request['departmentID']));
					$result['accessory'] = $dataAccessory;
					
					foreach($form['product_id'] as $key=>$productID) {
						$product = new PaintAccessory($db);
						$product->setAccessoryID($productID);
						$data = $product->getAccessoryDetails();
						
						$product->setAccessoryID($productID);
						//$product->setSupplier($data['supplier_id']);
						//$product->setProductNR($data['product_nr']);
						$product->setAccessoryName($data['name']);
						
						$product->setUnitAmount($form['unitAmount'][$productID]);
						$product->setUnitCount($form['unitCount'][$productID]);
						$product->setUnitQuantity($form['unitQuantity'][$productID]);
						$totalQty = (float)$form['unitAmount'][$productID] * (float)$form['unitQuantity'][$productID];
						$product->setTotalQuantity($totalQty);
						
						$inventory->addProduct($product);
						
						//	BAD VALIDATION MODEL
						$product = array(
							"supplier_id"		=>	$data['supplier_id'],
							"product_id"		=>	$productID,
							"product_nr"		=>	$data['product_nr'],									
							"name"				=>	$data['name'],
							"quantity"			=>	$totalQty,					//	the same rule as quantity
							"OSuse"				=>	$form['unitAmount'][$productID],	//	...
							"CSuse"				=>	$form['unitQuantity'][$productID],//	...
							"locationStorage"	=>	$form['unitCount'][$productID],	//	the same rule as location storage
						);				
						$products[]=$product;											
					}			
					break;
					
					default:									
						throw new Exception('404');											
					break;
				}
				
				$regData = array (							
					'inventory_name'	=>	$form['inventory_name'],
					'name'				=>	$form['inventory_name'],
					'inventory_desc'	=>	$form['inventory_desc']
				);
				$regData['products'] = $products;																		
				
				$validate = new Validation($db);
				$validateStatus = $validate->validateRegDataInventory($regData, count($regData['products']));
				if (!($validate->isUniqueName("inventory", $regData["inventory_name"], $inventory->getFacilityID(), $request['id'],$request['tab']))) {
					$validateStatus['summary'] = 'false';
					$validateStatus['inventory_name'] = 'alredyExist';
				}	
					
				if($inventory->getType() == Inventory::PAINT_MATERIAL) {
					$inventoryValidation = $inventory->validateProduct();									
					if (!$inventoryValidation['summary']) {
						$validateStatus['summary'] = 'false';
						foreach($inventoryValidation['products'] as $key=>$product) {
							if ($product !== true) {
								$validateStatus['products'][$key]['quantity'] = 'conflict';
								$validateStatus['products'][$key]['limit'] = $product + $products[$key]['quantity'];		
							}
						}																												
					}								
				}					

				if ($validateStatus['summary'] == 'true') {
					//	ok, save	
					
					//	setter injection
					$inventory->setTrashRecord(new Trash($db));																				
					$inventory->save();

					//	redirect
					if (isset($request['departmentID'])) {			
						header("Location: ?action=browseCategory&category=department&id=".$request['departmentID']."&bookmark=inventory&tab=".$inventory->getType()."&notify=10");
						die();																
					} elseif (isset($request['facilityID'])) {
						header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=inventory&tab=".$inventory->getType()."&notify=10");
						die();										
					} else {
						throw new Exception('404');
					}											
				} else {																		
					$result['inventory'] = $inventory;									
					$notify = new Notify($smarty);
					$notify->formErrors();																
					$result['validStatus'] = $validateStatus;					
				}							
			}
			
			//	IF ERRORS OR NO POST REQUEST									
			if (!isset($inventory)) {
				$inventory = new Inventory($db, $request['id']);	
				if ($inventory->getName() === null) {									
					//	throw new Exception('404');
				}								
				$result['inventory'] = $inventory;									
			};						
			
			if (isset($request['facilityID'])) {
	
				$result['parentCategory'] = 'facility';
				
				$result['tab'] = $inventory->getType();
				
				
				//	getting product list
				$facility = new Facility($db);
				$facilityDetails = $facility->getFacilityDetails($request['facilityID']);																																								
				$productInfo = new Product($db);																			
				$productListTemp = $productInfo->getFormatedProductList($facilityDetails['company_id'], $categoryDetails['products']);				
				for ($i=0; $i < count($productListTemp); $i++) {							
					if ($productListTemp[$i]['inventory_id'] == $id || $productListTemp[$i]['inventory_id'] == 0) {
						$productList[] = $productListTemp[$i];
					}														
				}			

				//	NICE PRODUCT LIST 
				foreach ($productList as $oneProduct) {
					$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
				}																	
				//$result['product'] = $productList;
				$result['product'] = $productListGrouped;
				
				//	get departments list
				$department = new Department($db);
				$departmentList = $department->getDepartmentListByFacility($inventory->getFacilityID());
				$result['departments'] = $departmentList;
				
				//	getting accessories list
				$cAccessory = new Accessory($db);
				$dataAccessory = $cAccessory->getAllAccessory($facilityDetails['company_id']);
				$result['accessory'] = $dataAccessory;
	
			} elseif (isset($request['departmentID'])) {
	
				$result['parentCategory'] = 'department';
				
				$result['tab'] = $inventory->getType();
				//	getting accessories list
				$company = new Company($db);
				$cAccessory = new Accessory($db);
				$dataAccessory = $cAccessory->getAllAccessory($company->getCompanyIDbyDepartmentID($request['departmentID']));
				$result['accessory'] = $dataAccessory;
				
				//	getting product list
				$company = new Company($db);																																																
				$productInfo = new Product($db);																			
				$productListTemp = $productInfo->getFormatedProductList($company->getCompanyIDbyDepartmentID($request['departmentID']), $categoryDetails['products']);
				for ($i=0; $i < count($productListTemp); $i++) {							
					if ($productListTemp[$i]['inventory_id'] == $id || $productListTemp[$i]['inventory_id'] == 0) {
						$productList[] = $productListTemp[$i];
					}														
				}
				//	NICE PRODUCT LIST 
				foreach ($productList as $oneProduct) {
					$productListGrouped[$oneProduct['supplier']][] = $oneProduct;
				}																				
				//$result['product'] = $productList;
				$result['product'] = $productListGrouped;
				
				if($inventory->getType() == Inventory::PAINT_MATERIAL) {
					foreach ($inventory->getProducts() as $product) {
						$allowEdit[$product->getProductID()] = false;
						foreach($product->getUseLocation() as $useLocation) {										
							if ($useLocation['departmentID'] == $request['departmentID']) {
								$product->setTotalQty($useLocation['totalQty']);
								$product->setToDateLeft($useLocation['totalQty'] - $useLocation['used']);
								$product->setLastInventory($useLocation['lastInventory']);
								$allowEdit[$product->getProductID()] = true;	
							}
						} 									
					}								
					$result['allowEdit'] = $allowEdit;
				}
	
			}				
			//-----------------------------------------------------
			$result['tpl'] = 'inventory/design/addInventoryNew.tpl';
			return $result;
		}

		
		/**
		 * 
		 * ?action=deleteItem&category=inventory&facilityID=82&tab=material&id[]=164&id[]=165
		 * @param array $params - $equipment obj, $db, $request
		 */
		public function prepareDelete($params) {
			extract($params);
						
			$result = array();
			
			foreach ($request['id'] as $inventoryID) {
				$inventory = new Inventory($db, $inventoryID);

				$delete["id"]			=	$inventory->getID();
				//$delete["custom_id"]	=	$inventory->get();	//TODO: customID
				$delete["name"]			=	$inventory->getName();
				$delete["description"]	=	$inventory->getDescription();

				$delete["linkedItem"] = "Equipment";
				$delete["inUseList"] = $inventory->isInUseList();
				$delete["linkedItemCount"] = count($delete["inUseList"]);
				if (!empty($delete["inUseList"])){
					for ($i=0;$i<$delete["linkedItemCount"];$i++){						
						$delete["inUseList"][$i]["inUseList2"] = $equipment->isInUseList($delete["inUseList"][$i]["id"]);
						$delete["inUseList"][$i]["linkedItemCount2"] = count($delete["inUseList"][$i]["inUseList2"]);
					}
				}
				if ($delete["inUseList"]) {
					$result['linkedNotify'] = true;
				}
				
				$result['itemForDelete'][] = $delete;
			}
			
			return $result;
		}
	}
?>