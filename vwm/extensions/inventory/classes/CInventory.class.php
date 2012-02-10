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

	private function actionViewDetails(){
			
		$this->smarty->assign('tab',$tab = $this->getFromRequest('tab'));

		$category = 'facility';
		$facilityID = $this->getFromRequest('facilityID');
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);		
		switch ($tab){
			case 'products':
				$productID	 = $this->getFromRequest('id');
				$error = false;
			//ORDERS FOR THIS PRODUCT
				// SOrt
				$sortStr = $this->sortList('orders',5);
				
				$orderList = $inventoryManager->getSupplierOrders($facilityID, $productID,null,$sortStr);		

				if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

					$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
				}else{
					$dateBegin = $ProductInventory->period_start_date;
				}

				$productarr = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $facilityID, $productID);
				
				$product = $productarr[0];
				if ($product->usage != 0){
					$result = $inventoryManager->unitTypeConverter($product);
					if ($result){
						$product->set_sum($result['usage']);
						$this->smarty->assign('typeName',$result['unittype']);
					}else{
						//throw new Exception('Can\'t convert to this type!');
						$error[] = 'Can\'t convert to this type! Product : '.$product->product_nr;
					
					}
				}else{
					$type = new Unittype($this->db);
					$typeName = $type->getUnittypeDetails($product->in_stock_unit_type);
					$this->smarty->assign('typeName',$typeName['name']);
				}
		
				$this->smarty->assign("product",$product);
				$this->smarty->assign("parentCategory",$category);
				$this->smarty->assign("editUrl","?action=edit&category=inventory&id=".$product->product_id."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab'));

				if ($error){
					$this->smarty->assign('error',$error);
					//$error[] = 'Can\'t convert to this type! Product : '.$value->product_nr; 
				}					
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
				throw new Exception('404');
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

					
					$ProductInventory = new ProductInventory($this->db);
					$checkInventory = $inventoryManager->checkInventory($form['order_product_id'], $form["facilityID"]);
					
					if (empty($checkInventory)){
						
						$ProductInventory->set_amount($form['order_amount']);
						$ProductInventory->set_product_id($form['order_product_id']);
						$ProductInventory->set_facility_id($form["facilityID"]);
						$ProductInventory->save();
					}
					$isThereActiveOrders = $inventoryManager->isThereActiveOrdersByProductID($form['order_product_id'], $form["facilityID"]);

					if (!$isThereActiveOrders){
						//TODO get price
						
						$price = 10;
						$newOrder = new OrderInventory($this->db,$form);
				
						$newOrder->order_facility_id = $form["facilityID"];
						$newOrder->order_name = 'Order for product "'.$form["product_nr"].'"';
						$newOrder->order_total = $form['order_amount'] * $price;
					/*	$newOrder->order_product_id =  $form['order_product_id'];
					 	$newOrder->order_status = OrderInventory::IN_PROGRESS;
						$newOrder->order_created_date = time();
						$newOrder->order_amount = $form['order_amount'];*/

						$result = $newOrder->save();
					}
					
					if ($result == 'true'){
						header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$this->getFromRequest('tab'));
					}else{
						header("Location: ?action=addItem&category=inventory&facilityID={$form['facilityID']}&tab=".$this->getFromRequest('tab')."&error=exist");
					}
				}
		}
		$jsSources = array ('modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js');
	    $this->smarty->assign('jsSources',$jsSources);
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
		$companyID = $facilityDetails['company_id'];
		$this->smarty->assign('companyID', $companyID);
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);		

	

		switch ($tab){
			case 'products':

			//ORDERS FOR THIS PRODUCT

				$error = false;
				$orderList = $inventoryManager->getSupplierOrders($facilityID, $productID);		

				if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

					$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
				}else{
					$dateBegin = $ProductInventory->period_start_date;
				}

				$productarr = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $facilityID, $productID);
				
				$product = $productarr[0];
				if ($product->usage != 0){
					$result = $inventoryManager->unitTypeConverter($product);
					if ($result){
						$product->set_sum($result['usage']);
						$this->smarty->assign('typeName',$result['unittype']);
					}else{
						//throw new Exception('Can\'t convert to this type!');
						$error[] = 'Can\'t convert to this type! Product : '.$product->product_nr;
					}
				}				
				if ($error){
					$this->smarty->assign('error',$error);
					//$error[] = 'Can\'t convert to this type! Product : '.$value->product_nr; 
				}
				
				$this->smarty->assign("product",$product);

// UNITTYPE{

		$type = new Unittype($this->db);		

		$res = $inventoryManager->getDefaultTypesAndUnitTypes($companyID);
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];	

		
	//	$unittypeList = $inventoryManager->getUnitTypeList($companyID);
		
		if ( $product->in_stock_unit_type != '' && $product->in_stock_unit_type != '0'){
			
			$unitTypeClass = $type->getUnittypeClass($product->in_stock_unit_type);
		}else{
			$unitTypeClass = $type->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		}
		$unittypeList = $type->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
	
			//$unitType = $type->getDefaultUnitTypelist($companyID);
			//$unittypeListDefault = $type->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);	
		$this->smarty->assign('unitTypeClass', $unitTypeClass);
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('typeEx', $typeEx);		
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('unittype', $unittypeList);
		//$this->smarty->assign('unittype', $unittypeListDefault);

		$jsSources = array (
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',

			'modules/js/addUsage.js');
	    $this->smarty->assign('jsSources',$jsSources);	
// }UNITTYPE
		
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
										$ProductInventory->set_in_stock_unit_type($form['selectUnittype']);
										$ProductInventory->set_inventory_id($form['inventory_id']);
										$ProductInventory->set_facility_id($facilityID);
										$result = $ProductInventory->save();
										if ($result == 'true'){
											header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$this->getFromRequest('tab'));
										}else{
											echo $result;
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
				
				
				//$supplierDiscount = $inventoryManager->getSupplierDiscounts($facilityID,$this->getFromRequest('id'));
				$supplierDiscount = $inventoryManager->getProductsSupplierList($facilityID,$this->getFromRequest('id'));
				$discount = $supplierDiscount[0];

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
				
				$jsSources = array ('modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js');
				$this->smarty->assign('jsSources',$jsSources);				
				$this->smarty->assign('supplier',$discount);	
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
				
					// For orders with status: Canceled or Completed denied edit function
					if ($orderDetails[0]['order_status'] != OrderInventory::COMPLETED && $orderDetails[0]['order_status'] != OrderInventory::CANCELED){
						$statuslist = $inventoryManager->getSupplierOrdersStatusList();

						$this->smarty->assign('status',$statuslist);

						$this->smarty->assign('order',$orderDetails[0]);	
						$this->smarty->assign('tpl','inventory/design/inventoryOrdersEdit.tpl');					
					}else{
						throw new Exception('deny');
					}
					$form = $_POST;
					if (count($form) > 0) {
						//protected from xss
						$form["facilityID"]=Reform::HtmlEncode($form["facilityID"]);
						$form['order_id'] = Reform::HtmlEncode($form['order_id']);
						$form['status'] = Reform::HtmlEncode($form['status']);
						$form['order_completed_date'] = time();
						
							if ($form['status'] == OrderInventory::COMPLETED){
								
							//ORDERS FOR THIS PODUCT
								$orderList = $inventoryManager->getSupplierOrders($request['facilityID'], $orderDetails[0]['order_product_id']);		
	
								if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

									$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
								}else{
									$dateBegin = $ProductInventory->period_start_date;
								}
							//
								$productDetails = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $request['facilityID'], $orderDetails[0]['order_product_id']);
								$product = $productDetails[0];

								$addToStock = $product->in_stock - $product->usage + $orderList[0]['order_amount'];
								$product->in_stock = $addToStock;
								$result = $product->save();
								
							}
							
						$result = $inventoryManager->updateSupplierOrder($form);
						


							if ($result == 'true'){
								header("Location: ?action=browseCategory&category=facility&id={$form['facilityID']}&bookmark=inventory&tab=".$request['tab']);
							}
					
					}	
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
				throw new Exception('404');
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

				// Pagination	
				$count = $inventoryManager->getCountInventoryPrduct($facilityID);
				$pagination = new Pagination($count);
				$pagination->url = "?action=browseCategory&category=facility&id={$facilityID}&bookmark=inventory&tab=products";
				$this->smarty->assign('pagination', $pagination);			
				
				$supplierPrductIdList = $inventoryManager->getInventoryPrductIdByFacility($facilityID, $pagination);


			
				// kostyl' for product usage after completed oreder
				foreach ($supplierPrductIdList as $id){
					
				//ORDERS FOR THIS PODUCT
					$orderList = $inventoryManager->getSupplierOrders($facilityID, $id);		

					if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

						$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
					}else{
						$dateBegin = $ProductInventory->period_start_date;
					}
					
					$dataArr[] = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $facilityID,$id);
					
				
				}

				foreach ($dataArr as $arr) {
					$data[] = $arr[0];
				}

				//$data = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $facilityID);	

				$error = false;
					foreach ($data as $value) {
						
						$value->url = "?action=viewDetails&category=inventory&id=".$value->product_id."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab')."";		
						if ($value->usage == null){
							$value->set_sum(0);
						}
					// UNITTEPY	
					if ($value->usage != 0){
						$result = $inventoryManager->unitTypeConverter($value);
						if ($result){
							$value->set_sum($result['usage']);
							$typeNameArr[$value->product_id] = $result['unittype'];
							
							
						}else{
							//throw new Exception('Can\'t convert to this type!');
							$error[] = 'Can\'t convert to this type! Product : '.$value->product_nr;  
						}
					}else{
						$type = new Unittype($this->db);
						$typeName = $type->getUnittypeDetails($value->in_stock_unit_type);
						$typeNameArr[$value->product_id] = $typeName['name'];
					}
					$this->smarty->assign('typeName',$typeNameArr);
					//	ini indicator (gauge)	

					$pxCount = round(200 * $value->usage / $value->in_stock);
					if ($pxCount > 200) {
							$pxCount = 200;
					}				

					$value->pxCount = $pxCount;	

					}
					
				$this->smarty->assign('Products',$data);
				$this->smarty->assign('tpl','inventory/design/inventoryProducts.tpl');	
				if ($error){
					$this->smarty->assign('error',$error);
					//$error[] = 'Can\'t convert to this type! Product : '.$value->product_nr; 
				}
				break;
			case 'orders':
				// SOrt
				$sortStr = $this->sortList('orders',5);
				
				// Pagination	
				$count = $inventoryManager->getCountSupplierOrders($facilityID);
				
				$pagination = new Pagination($count);
				$pagination->url = "?action=browseCategory&category=facility&id={$facilityID}&bookmark=inventory&tab=orders";
				$this->smarty->assign('pagination', $pagination);				
				
				$orderList = $inventoryManager->getSupplierOrders($facilityID,null, $pagination,$sortStr);
				

				
				foreach ($orderList as $order){
					$SupData = $inventoryManager->getProductsSupplierList($facilityID, $order['order_product_id']);
					//var_dump($order,$SupData);
					$order['order_created_date'] = date('m/d/Y',$order['order_created_date']);
					$order['discount'] = $SupData[0]['discount'];
					$order['url'] = "?action=viewDetails&category=inventory&id=".$order['order_id']."&facilityID=".$facilityID."&tab=".$this->getFromRequest('tab')."";
					$arr[] = $order;
				}

				$orderList = $arr;
				$jsSources = array('modules/js/checkBoxes.js');
				$this->smarty->assign('jsSources', $jsSources);				
				$this->smarty->assign('orderList',$orderList);
				$this->smarty->assign('tpl','inventory/design/inventoryOrders.tpl');	
				break;
			case 'discounts':
				$sortStr = $this->sortList('discounts',4);
				
				$SupData = $inventoryManager->getProductsSupplierList($facilityID, null,$sortStr);

				$supplierlist = array();
					foreach ( $SupData as $supplier) {

						$supplier['url'] = "?action=edit&category=inventory&id=".$supplier['product_id']."&".$category."ID=".$facilityID."&tab=".$this->getFromRequest('tab')."";
						$supplierlist[] = $supplier;
						
					}				

				
				
				$this->smarty->assign('supplierlist',$supplierlist);	
				$this->smarty->assign('tpl','inventory/design/inventoryDiscounts.tpl');	
				break;
			case 'settings':

				break;	
			default :
				throw new Exception('404');
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
		
		if ($orderDetails == false || $orderDetails->order_status == OrderInventory::COMPLETED) {
			throw new Exception('deny');
		}
		
		$inventoryAsArray = $inventoryManager->checkInventory($orderDetails->order_product_id, $orderDetails->order_facility_id);
		$productInventory = new ProductInventory($this->db, $inventoryAsArray);
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($orderDetails->order_facility_id);		
				
		if (isset($request['result']) && $request['result'] != ''){
			switch($request['result']) {
				case 'confirm':
					$orderDetails->order_status = OrderInventory::CONFIRM;
					break;
				case 'cancel':
					$orderDetails->order_status = OrderInventory::CANCELED;
					break;
				default :
					throw new Exception('deny');
					break;
			}			
			
			$arrayForUpdate = array(
				'status'	=> $orderDetails->order_status,
				'order_id'	=> $orderDetails->order_id
			);
			$result = $inventoryManager->updateSupplierOrder($arrayForUpdate);
			if($result){
				$inventoryManager->sendEmailToManager($userEmail, "Status of ".$orderDetails->order_name." was change by supplier.");
				header("Location: ?action=processororderResult&category=inventory&result=positive");										
			}else{
				throw new Exception('deny');
			}
		}
		
		//$this->smarty->assign('result',$result);
		$this->smarty->assign('order',$orderDetails);
		$this->smarty->assign('inventory',$productInventory);
		$this->smarty->assign('facility',$facilityDetails);
		$this->smarty->assign('request',$request);
		$this->smarty->display("tpls:inventory/design/processororder.tpl");
		/*if ($result){
			$this->smarty->display('tpls:inventory/design/inventoryOrdersDetail.tpl');	
		}*/
	}	
	
	public function actionProcessororderResult(){

		$request = $this->getFromRequest();
		$this->smarty->assign('request',$request);
		$this->smarty->display("tpls:inventory/design/processororderResult.tpl");

	}	

		
}
?>