<?php
class CInventory extends Controller
{
	function CInventory($smarty,$xnyo,$db,$user,$action)
		{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='inventory';
		}

	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
		else
			throw new Exception('404');
	}

	private function actionConfirmDelete()
	{
		foreach($this->itemID as $inventoryID)
		{
			$inventory = new Inventory($this->db, $inventoryID);
			$lastInventoryType = $inventory->getType();
			$lastInventoryFaciltiyID = $inventory->getFacilityID();

			//	setter injection
			$inventory->setTrashRecord(new Trash($this->db));
			$inventory->delete();
		}

		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=facility&id=$lastInventoryFaciltiyID&bookmark=inventory&tab=$lastInventoryType&notify=8");
	}

	private function actionDeleteItem()
		{
		if (!is_null($this->getFromRequest('departmentID')))
		{
			//	Access control
			if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
				throw new Exception('deny');
			}

			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));

			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=inventory&tab=".$this->getFromRequest('tab'));
			$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'), array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab')));
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			$this->setPermissionsNew('viewData');

			//	calling from facility
		}
		elseif (!is_null($this->getFromRequest('facilityID')))
		{
			if (!$this->user->checkAccess('facility', $this->getFromRequest('facilityID'))) {
				throw new Exception('deny');
			}
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID =  $facilityDetails['company_id'];
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=inventory&tab=".$this->getFromRequest('tab'));
			$this->setListCategoriesLeftNew('facility',$this->getFromRequest('facilityID'), array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab')));
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewFacility');
		}
		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw  new Exception('deny');
		}
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mInventory = new $moduleMap['inventory'];
		$equipment = new Equipment($this->db);
		$params = array(
			'equipment'	=> $equipment,
			'db'		=> $this->db,
			'request'	=> $this->getFromRequest()
		);
		extract($mInventory->prepareDelete($params));
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
		}

	private function actionViewDetails()
		{
			
		$this->smarty->assign('tab',$tab = $this->getFromRequest('tab'));
		$productID = $this->getFromRequest('id');
		$category = 'facility';
		$facilityID = $this->getFromRequest('facilityID');
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);		
		switch ($tab){
			case 'products':
				$productarr = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $facilityID, $productID);
				$product = $productarr[0];
				$this->smarty->assign("product",$product);
				$this->smarty->assign("parentCategory",$category);
				$this->smarty->assign("editUrl","?action=edit&category=inventory&id=".$product->product_id."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab'));
			//ORDERS FOR THIS PODUCT
				$orderList = $inventoryManager->getSupplierOrders($facilityID, $product->product_id);
				
				foreach ($orderList as $order){
					$SupData = $inventoryManager->getProductsSupplierList($facilityID, $order['order_product_id']);
					$order['order_created_date'] = date('m/d/Y',$order['order_created_date']);
					$order['discount'] = $SupData[0]['discount'];
					$order['url'] = "?action=viewDetails&category=inventory&id=".$order['order_id']."&facilityID=".$facilityID."&tab=orders";
					$arr[] = $order;
				}

				$orderList = $arr;
				$this->smarty->assign('orderList',$orderList);	
			//
/*		if (!is_null($this->getFromRequest('facilityID')))
		{
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID = $facilityDetails['company_id'];
			$backCategory = 'facility';
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			//$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'inventory', 'tab'=>''));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign('backUrl','?action=browseCategory&category=facility&id='.$this->getFromRequest('facilityID').'&bookmark=inventory&tab=material');
		} elseif (!is_null($this->getFromRequest('departmentID')))
		{
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails($this->getFromRequest('departmentID'));
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
			$companyID = $facilityDetails['company_id'];
			$backCategory = 'department';
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			//$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'),array('bookmark'=>'inventory', 'tab'=>''));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=inventory&tab=material');
		}
		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw new Exception('deny');
		}
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module)
		{
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);

		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw new Exception('deny');
		}

		$mInventory = new $moduleMap['inventory'];
		$params = array(
			'db' => $this->db,
			'user' => $this->user,
			'request' => $this->getFromRequest()
		);
		$result = $mInventory->prepareView($params);
		foreach($result as $key => $value)
		{
			$this->smarty->assign($key,$value);
		}

		$this->setListCategoriesLeftNew($backCategory, $this->getFromRequest($backCategory.'ID'),array('bookmark'=>'inventory','tab'=>$result['inventory']->getType()));
		$this->smarty->assign('backUrl','?action=browseCategory&category='.$backCategory.'&id='.$this->getFromRequest($backCategory.'ID').'&bookmark=inventory&tab='.$result['inventory']->getType());
		
 */				
				$this->smarty->assign('tpl','inventory/design/inventoryProductsDetail.tpl');
				break;
			case 'orders':
				$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID,$this->getFromRequest('id'));
					$SupData = $inventoryManager->getProductsSupplierList($facilityID, $orderDetails[0]['order_product_id']);
					$orderDetails[0]['order_created_date'] = date('m/d/Y',$orderDetails[0]['order_created_date']);
					$orderDetails[0]['discount'] = $SupData[0]['discount'];				
				
				
				$this->smarty->assign("editUrl","?action=edit&category=inventory&id=".$orderDetails[0]['order_id']."&facilityID=".$facilityID."&tab=".$this->getFromRequest('tab'));
				$this->smarty->assign('order',$orderDetails[0]);	
				$this->smarty->assign('tpl','inventory/design/inventoryOrdersDetail.tpl');					
				break;

			case 'settings':

				break;	
			default :
				throw new Exception('Unknown Tab for Inventory');
				break;			
		}


		$this->smarty->display("tpls:index.tpl");

	}

	private function actionAddItem() {
		// inventory from?
/*		if (is_null($this->getFromRequest('facilityID')) && !is_null($this->getFromRequest('departmentID'))) {
			$parentCategory = "department";
			$request = $this->getFromRequest();
			$request['id'] = $this->getFromRequest('departmentID');
			$department = new Department($this->db);
			if(!$department->initializeByID($this->getFromRequest('departmentID'))) {
				throw new Exception('404');
			}
		} elseif (!is_null($this->getFromRequest('facilityID')) && is_null($this->getFromRequest('departmentID'))) {
			$parentCategory = "facility";
			$request = $this->getFromRequest();
			$request['id'] = $this->getFromRequest('facilityID');
			$facility = new Facility($this->db);
			if(!$facility->initializeByID($this->getFromRequest('facilityID'))) {
				throw new Exception('404');
			}
		} else {
			throw new Exception('I do not know whom I should link inventory. To department or facility?');
		}

		//	Access control
		if (!$this->user->checkAccess($parentCategory, $this->getFromRequest($parentCategory.'ID'))) {
			throw new Exception('deny');
		}

		$this->smarty->assign('parentCategory', $parentCategory);
		$request['parent_category'] = $parentCategory;

		//protected from xss
		if (isset($_POST["inventory_name"]))
			$_POST["inventory_name"]=Reform::HtmlEncode($_POST["inventory_name"]);
		if (isset($_POST["inventory_desc"]))
			$_POST["inventory_desc"]=Reform::HtmlEncode($_POST["inventory_desc"]);

		$form = $_POST;

		if (count($form)>0) {
			$form['OS_use'] = str_replace(',','.',$form['OS_use']);
			$form['CS_use'] = str_replace(',','.',$form['CS_use']);
			$form['totalQty'] = str_replace(',','.',$form['totalQty']);
			$form['unitAmount'] = str_replace(',','.',$form['unitAmount']);
			$form['unitQuantity'] = str_replace(',','.',$form['unitQuantity']);
		}

		$paramsForListLeft = array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab'));

		//	IF ERRORS OR NO POST REQUEST
		if ($parentCategory == 'facility') {
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID = $facilityDetails['company_id'];
			$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $paramsForListLeft);
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewFacility');

			//	set js scripts
			$jsSources = array(
				'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
				'modules/js/inventory.js'
			);
			$this->smarty->assign('jsSources', $jsSources);
			$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
			$this->smarty->assign('cssSources', $cssSources);

		} elseif ($parentCategory == 'department') {
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails($this->getFromRequest('departmentID'));
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
			$companyID = $facilityDetails['company_id'];

			$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			$this->setPermissionsNew('viewData');

			//	set js scripts
			$jsSources = array(
				'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
				'modules/js/inventory.js'
			);
			$this->smarty->assign('jsSources', $jsSources);
			$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
			$this->smarty->assign('cssSources', $cssSources);

		} else {
			throw new Exception('I do not know whom I should link inventory. To department or facility?');
		}

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);

		if (!$showModules['inventory']) {
			throw new Exception('deny');
		}

		//	ok, has access to module
		$mInventory = new $moduleMap['inventory'];

		$params = array(
			'db' => $this->db,
			'request' => $request,//$this->getFromRequest(),
			'form' => $form,
			'parentCategory' => $parentCategory,
			'smarty' => $this->smarty
		);
		$result = $mInventory->prepareAdd($params);

		foreach ($result as $key=>$value) {
			$this->smarty->assign($key,$value);
		}
		
 */		
			$error = $this->getFromRequest('error');

			
				$inventoryManager = new InventoryManager($this->db);
				$product = new Product($this->db);
				$facility = new Facility($this->db);

				$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
				$companyID = $facilityDetails['company_id'];
				
				$productLst = $product->getProductList($companyID);
				if ( $error == null ){
				$form = $_POST;
				if (count($form) > 0) {
					
					$newOrder = new OrderInventory($this->db);
					$ProductInventory = new ProductInventory($this->db);
					$checkInventory = $inventoryManager->checkInventory($form['order_product_id'], $form["facilityID"]);
					
					if (empty($checkInventory)){
						
						$ProductInventory->set_amount($form['amount']);
						$ProductInventory->set_product_id($form['order_product_id']);
						$ProductInventory->set_facility_id($form["facilityID"]);
						$ProductInventory->save();
					}
					$checkOrder = $inventoryManager->checkInventoryOrderByPrductId($form['order_product_id'], $form["facilityID"]);

					if ($checkOrder['order_status'] != 1 && $checkOrder['order_status'] != 2){
						//TODO get price
						$price = 10;
						$newOrder->order_product_id =  $form['order_product_id'];
						$newOrder->order_facility_id = $form["facilityID"];
						$newOrder->order_name = 'Order for product "'.$form["product_nr"].'"';
						$newOrder->order_total = $form['amount'] * $price;
						$newOrder->order_status = 1;
						$newOrder->order_created_date = time();
						$result = $newOrder->save();
					}
					
					if ($result == 'true'){
						header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$this->getFromRequest('tab'));
					}else{
						header("Location: ?action=addItem&category=inventory&facilityID={$form['facilityID']}&tab=".$this->getFromRequest('tab')."&error=exist");
					}
				}
		}

		$this->smarty->assign('products', $productLst);
		$this->smarty->assign('tpl', "inventory/design/inventoryOrdersAdd.tpl");
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit() {
		
		
							if (!is_null($this->getFromRequest('facilityID'))) {
								//	Access control
								if (!$this->user->checkAccess('facility', $this->getFromRequest('facilityID'))) {
									throw new Exception('deny');
								}

								$backCategory = 'facility';
								$facilityID = $this->getFromRequest('facilityID');

							}
		$this->smarty->assign('tab',$tab = $this->getFromRequest('tab'));
		$request = $this->getFromRequest();
		$productID = $this->getFromRequest('id');
		$category = 'facility';
		$facilityID = $this->getFromRequest('facilityID');
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$type = new Unittype($this->db);		

	

		$unitTypeEx = $type->getDefaultUnitTypelist($facilityDetails['company_id']);

		$unitTypeClass = $type->getUnittypeListDefaultByCompanyId($unitTypeEx[0]);
	
		$unittypeListDefault = $type->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);	
		
		
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('TypeEx', $unitTypeClass);		
	//	var_dump($unitTypeEx);
		
		
		
		
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);
		switch ($tab){
			case 'products':		
				$productarr = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $facilityID, $productID);
				$product = $productarr[0];
				$this->smarty->assign("product",$product);


									$form = $_POST;

									if (count($form) > 0) {
										//protected from xss
										$form["in_stock"]=Reform::HtmlEncode($form["in_stock"]);
										$form["limit"]=Reform::HtmlEncode($form["limit"]);
										$form['amount'] = Reform::HtmlEncode($form['amount']);

										$ProductInventory->set_amount($form['amount']);
										$ProductInventory->set_in_stock($form['in_stock']);
										$ProductInventory->set_inventory_limit($form['limit']);
										$ProductInventory->set_product_id($form['product_id']);
										$ProductInventory->set_in_stock_unit_type($form['in_stock_unit_type']);
										$ProductInventory->set_inventory_id($form['inventory_id']);
										$ProductInventory->set_facility_id($facilityID);
										$result = $ProductInventory->save();
										if ($result == 'true'){
											header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$this->getFromRequest('tab'));
										}

									}
		/*
									//	IF ERRORS OR NO POST REQUEST
										$facility = new Facility($this->db);
										$facilityDetails = $facility->getFacilityDetails($facilityID);
										$companyID = $facilityDetails['company_id'];

										$this->setNavigationUpNew('department', $this->getFromRequest($backCategory.'ID'));
										$this->setPermissionsNew('viewData');
									$ms = new ModuleSystem($this->db);
									$moduleMap = $ms->getModulesMap();
									foreach($moduleMap as $key=>$module) {
										$showModules[$key] = $this->user->checkAccess($key, $companyID);
									}
									$this->smarty->assign('show',$showModules);

									if(!$showModules['inventory']) {
										throw new Exception('deny');
									}

									//	ok, we have access to inventory..
									$mIventory = new $moduleMap['inventory'];

									$params = array(
											'db' => $this->db,
											'request' => $this->getFromRequest(),
											'form' => $form,
											'facilityID' => $facilityID,
											'smarty' => $this->smarty
									);
									$result = $mIventory->prepareEdit($params);

									foreach ($result as $key=>$value) {
										$this->smarty->assign($key,$value);
									}

									$this->setListCategoriesLeftNew($backCategory, $this->getFromRequest($backCategory.'ID'), array('bookmark'=>'inventory','tab'=>$result['tab']));

									//	set js scripts
									$jsSources = array(
										'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
										'modules/js/inventory.js'
									);
									$this->smarty->assign('jsSources', $jsSources);
									$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
									$this->smarty->assign('cssSources', $cssSources);
		*/

				$this->smarty->assign('tpl', "inventory/design/inventoryProductsEdit.tpl");
				break;
			case 'discounts':
				
				
				$supplierDiscount = $inventoryManager->getSupplierDiscounts($facilityID,$this->getFromRequest('id'));

									$form = $_POST;

									if (count($form) > 0) {
										//protected from xss
										$form["discount"]=Reform::HtmlEncode($form["discount"]);
										$form["facilityID"]=Reform::HtmlEncode($form["facilityID"]);
										$form['supplier_id'] = Reform::HtmlEncode($form['supplier_id']);
										$form['discount_id'] = Reform::HtmlEncode($form['discount_id']);
										$form['supplier'] = Reform::HtmlEncode($form['supplier']);

										$result = $inventoryManager->updateSupplierDiscounts($form);
									
										if ($result == 'true'){
											header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$this->getFromRequest('tab'));
										}

									}				
				
				
				$this->smarty->assign('supplier',$supplierDiscount);	
				$this->smarty->assign('tpl','inventory/design/inventoryDiscountsEdit.tpl');	
			break;
			case 'orders':
				if ($request['cancel']){
						if ($request['cancel'] == 'confirm'){
							
							for ($i =0;$i < $request['itemsCount']; $i++){
								$orderDetailsArr = array();
								if (isset($request['item_'.$i])){
									$orderDetailsArr['order_id'] = $request['item_'.$i];
									$orderDetailsArr['status'] = 4;

									$inventoryManager->updateSupplierOrder($orderDetailsArr);
								}
							}

							header("Location: ?action=browseCategory&category=facility&id={$facilityID}&bookmark=inventory&tab=".$request['tab']);
						}else{					
							foreach ($request['id'] as $orderId){
								$orderDetailsArr = $inventoryManager->getSupplierOrderDetails($facilityID,$orderId);
								$orderDetailsArr[0]['status'] = '4';
								$arr[] =  $orderDetailsArr[0];


								
							}
						}
					$this->smarty->assign('cancelUrl',"?action=browseCategory&category=facility&id={$facilityID}&bookmark=inventory&tab={$request[tab]}");
					$this->smarty->assign('itemsCount',count($arr));
					$this->smarty->assign('itemForDelete',$arr);
					$this->smarty->assign('tpl','inventory/design/deleteOrder.tpl');
				}else{
					$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID,$request['id']);

					$form = $_POST;
					if (count($form) > 0) {
						//protected from xss
						$form["facilityID"]=Reform::HtmlEncode($form["facilityID"]);
						$form['order_id'] = Reform::HtmlEncode($form['order_id']);
						$form['status'] = Reform::HtmlEncode($form['status']);
						$result = $inventoryManager->updateSupplierOrder($form);
						
						if ($result == 'true'){
							if ($form['status'] == 3){
								$productDetails = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $request['facilityID'], $orderDetails[0]['order_product_id']);
								$product = $productDetails[0];
								$addToStock = $product->in_stock - $product->usage + $product->amount;
								$product->in_stock = $addToStock;
								$result = $product->save();
								
							}
							if ($result == 'true'){
								header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$request['tab']);
							}
						}
					}				
					$statuslist = $inventoryManager->getSupplierOrdersStatusList();

					$this->smarty->assign('status',$statuslist);

					$this->smarty->assign('order',$orderDetails[0]);	
					$this->smarty->assign('tpl','inventory/design/inventoryOrdersEdit.tpl');
				}
			break;			
			
			case 'settings':
				
				$inventoryEmail = $inventoryManager->getSupplierSettings($facilityID);
				
									$form = $_POST;

									if (count($form) > 0) {
										//protected from xss
										$form["email_all"]=Reform::HtmlEncode($form["email_all"]);
										$form["email_manager"]=Reform::HtmlEncode($form["email_manager"]);
										$form['facilityID'] = Reform::HtmlEncode($form['facilityID']);

							
										$result = $inventoryManager->updateSupplierSettings($form);
									
										if ($result == 'true'){
											header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=products");
										}
									}
													
				$this->smarty->assign('email',$inventoryEmail);	
				$this->smarty->assign('tpl','inventory/design/inventorySettings.tpl');
				break;	
			default :
				throw new Exception('Unknown Tab for Inventory');
				break;				
		}
		$this->smarty->display("tpls:index.tpl");
	}

	/**
	 * bookmarkInventory($vars)
	 * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
	 */
	protected function bookmarkInventory($vars)
	{
		/*New inventory 26 Jan 2012*/		
		extract($vars);
		$category = 'facility';
		$facilityID = $this->getFromRequest('id');	
		$inventoryManager = new InventoryManager($this->db);
		$sortStr = $this->sortList('inventory',3);
		if (!$this->user->checkAccess('inventory', $facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$this->smarty->assign('tab',$tab = $this->getFromRequest('tab'));
	

		switch ($tab){
			case 'products':
				//Product Usage
				$ProductInventory = new ProductInventory($this->db);


				$data = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $facilityID);	
				
					foreach ($data as $value) {

						$value->url = "?action=viewDetails&category=inventory&id=".$value->product_id."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab')."";		


					//	ini indicator (gauge)	

					$pxCount = round(200 * $value->usage / $value->in_stock);
					if ($pxCount > 200) {
							$pxCount = 200;
					}				

					$value->pxCount = $pxCount;	

					}

				$this->smarty->assign('Products',$data);
				$this->smarty->assign('tpl','inventory/design/inventoryProducts.tpl');	
				break;
			case 'orders':
				
				$orderList = $inventoryManager->getSupplierOrders($facilityID);
				
				foreach ($orderList as $order){
					$SupData = $inventoryManager->getProductsSupplierList($facilityID, $order['order_product_id']);
					//var_dump($order,$SupData);
					$order['order_created_date'] = date('m/d/Y',$order['order_created_date']);
					$order['discount'] = $SupData[0]['discount'];
					$order['url'] = "?action=viewDetails&category=inventory&id=".$order['order_id']."&facilityID=".$facilityID."&tab=".$this->getFromRequest('tab')."";
					$arr[] = $order;
				}

				$orderList = $arr;
				$this->smarty->assign('orderList',$orderList);
				$this->smarty->assign('tpl','inventory/design/inventoryOrders.tpl');	
				break;
			case 'discounts':
				
				
				$SupData = $inventoryManager->getProductsSupplierList($this->getFromRequest('id'));


				$supplierlist = array();
					foreach ( $SupData as $supplier) {

						$supplier['url'] = "?action=edit&category=inventory&id=".$supplier['supplier_id']."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab')."";
						$supplierlist[] = $supplier;
						
					}				

				
				
				$this->smarty->assign('supplierlist',$supplierlist);	
				$this->smarty->assign('tpl','inventory/design/inventoryDiscounts.tpl');	
				break;
			case 'settings':

				break;	
			default :
				throw new Exception('Unknown Tab for Inventory');
				break;			
		}
		

	
	
		
		


		
		
		
		
		
		
		
		
/*		
//TODEL
		$mInventory = new $moduleMap['inventory'];
		$facility->initializeByID($this->getFromRequest('id'));
		//	ini VOC indicator (gauge)
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		$params = array(
			'facility' => $facility,
			'request' => $this->getFromRequest(),
			'sort'=>$sortStr
		);

		$result = $mInventory->prepareList($params);
//TODEL
		$export=$this->getFromRequest('export');
		if ($export) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$company = new Company($this->db);
			$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->title = "Inventories of facility ".$facilityDetails['name'];
			if ($_GET['searchAction']=='search') {
				$exporter->search_term = $this->getFromRequest('q');
			} else {
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}
			$widths = array(
				'id' => 10,
				'name' => 40,
				'description' => 50,
			);
			$header = array(
				'id' => 'ID Number',
				'name' => 'Inventory Name',
				'description' => 'Inventory Description',
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($result['childCategoryItems']);
			$exporter->export();
			die();
		} else {
			foreach ($result as $key=>$value) {
				$this->smarty->assign($key,$value);
			}
			//	set js scripts
			$jsSources = array('modules/js/checkBoxes.js');
			$this->smarty->assign('jsSources', $jsSources);
			$this->smarty->assign('tpl','inventory/design/inventoryProducts.tpl');
		}
*/	
	}

	/**
	 * bookmarkDInventory($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDInventory($vars)
	{

		extract($vars);
		$sortStr=$this->sortList('inventory',3);
		$this->smarty->assign('tab',$this->getFromRequest('tab'));
		$departments = new Department($this->db);
		$departments->initializeByID($this->getFromRequest('id'));
		$facility = new Facility($this->db);
		$facility->initializeByID($departments->getFacilityID());

		if (!$this->user->checkAccess('inventory', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}

		//	OK, this company has access to this module, so let's setup..
		$mInventory = new $moduleMap['inventory'];

		$params = array(
			'facility' => $facility,
			'departments'=>$departments,
			'request' => $this->getFromRequest(),
			'sort'=>$sortStr
		);
		$result = $mInventory->prepareList($params);
		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Inventories of department ".$departmentDetails['name'];
			if ($_GET['searchAction']=='search')
			{
				$exporter->search_term = $this->getFromRequest('q');
			}
			else
			{
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}
			$widths = array(
				'id' => 10,
				'name' => 40,
				'description' => 50,
			);
			$header = array(
				'id' => 'ID Number',
				'name' => 'Inventory Name',
				'description' => 'Inventory Description',
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($result['childCategoryItems']);
			$exporter->export();
			die();
		}
		else
		{
			foreach ($result as $key=>$value) {
				$this->smarty->assign($key,$value);
			}
			//	set js scripts
			$jsSources = array('modules/js/checkBoxes.js');
			$this->smarty->assign('jsSources', $jsSources);
		}
		}
	public function actionProcessororder(){
		
		$hash = $this->getFromRequest('hash');
		$to = $this->getFromRequest('to');
		
		$userEmail = base64_decode($to);

		$request = $this->getFromRequest();
		$inventoryManager = new InventoryManager($this->db);
		$orderDetails = $inventoryManager->getOrderDetailsByHash($hash);
		$result = $orderDetails;
		if (isset($request['result']) && $request['result'] != ''){
			if ($request['result'] == 'yes'){
				if ($orderDetails['hash_type'] == 'confirm'){
					$orderDetails['status'] = 2;
				}elseif($orderDetails['hash_type'] == 'cancel'){
					$orderDetails['status'] = 4;
				}
				
				$result = $inventoryManager->updateSupplierOrder($orderDetails);
					if($result){
						$inventoryManager->sendEmailToManager($userEmail, "Status of ".$orderDetails['order_name']." was change by supplier. Order status: ".$orderDetails['hash_type']."ed.");
		
						header("Location: ?action=processororderResult&category=inventory&result=positive");
			
					}				
			}else{
				header("Location: ?action=processororderResult&category=inventory&result=negative");
			}

		}
		$orderDetails['order_created_date'] = date('m/d/Y',$orderDetails['order_created_date']);
		$this->smarty->assign('result',$result);
		$this->smarty->assign('order',$orderDetails);
		$this->smarty->assign('request',$request);
		$this->smarty->display("tpls:inventory/design/processororder.tpl");
		if ($result){
			$this->smarty->display('tpls:inventory/design/inventoryOrdersDetail.tpl');	
		}
	}	
	
	public function actionProcessororderResult(){

		$request = $this->getFromRequest();
		$this->smarty->assign('request',$request);
		$this->smarty->display("tpls:inventory/design/processororderResult.tpl");

	}	

		
}
?>