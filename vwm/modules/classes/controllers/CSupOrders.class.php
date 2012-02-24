<?php

class CSupOrders extends Controller {
	
	function CSupOrders($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='orders';
		$this->parent_category='sales';		
	}
	
	function runAction() {		
		$this->runCommon('supplier');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}

	protected function actionBrowseCategory() {
		$this->bookmarkOrders();
	}
	
	protected function bookmarkOrders($vars) {
		extract($vars);

		$request = $this->getFromRequest();
		if (!$request.supplierID){
			$supplierID = $supplierIDS[0]['supplier_id'];
		}else{
			$supplierID = $request['supplierID'];
		}		
		// SOrt
		$sortStr = $this->sortList('orders',5);

		$inventoryManager = new InventoryManager($this->db);
		$productManager = new Product($this->db);
		$facilityManager = new Facility($this->db);

		$products = $productManager->getProductListByMFG($supplierID);
		// Pagination	
		$count = $inventoryManager->getCountSupplierOrders($products);
		$pagination = new Pagination($count);
		$pagination->url = "?action=browseCategory&category=sales&bookmark=orders";
		$this->smarty->assign('pagination', $pagination);
	
		$order = $inventoryManager->getSupplierOrders(null, $products, $pagination, $sortStr);


		/* 		foreach($products as $product){
		  $order = $inventoryManager->getSupplierOrders(null,$product['product_id'],null,$sortStr);

		 */
			if ($order){
				foreach ($order as $o){
					$facilityDetails = $facilityManager->getFacilityDetails($o['order_facility_id']);
					$SupData = $inventoryManager->getProductsSupplierList($o['order_facility_id'], $o['order_product_id']);
					$o['order_created_date'] = date('m/d/Y',$o['order_created_date']);
					$o['unittype'] = $SupData[0]['in_stock_unit_type'];
					$o['url'] = "supplier.php?action=viewDetails&category=orders&id=".$o['order_id']."&facilityID=".$o['order_facility_id']."";
					$o['completeUrl'] = "supplier.php?action=completeOrder&category=orders&id=".$o['order_id']."";
					$o['client'] = $facilityDetails['title'];
					
					$orderList[] = $o;
				}				

			}

		$this->smarty->assign('orderList', $orderList);

//set js scripts

		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js');
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("itemsCount", $totalCount);
		$this->smarty->assign('tpl', 'tpls/bookmarkOrders.tpl');

               
	}
	

	private function actionViewDetails() {
		$inventoryManager = new InventoryManager($this->db);
		$facilityID = $this->getFromRequest('facilityID');
		$orderID = $this->getFromRequest('id');
		
		$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID, $orderID);
		$orderDetails[0]['order_created_date'] = date('m/d/Y', $orderDetails[0]['order_created_date']);


		$this->smarty->assign("editUrl", "?action=edit&category=inventory&id=" . $orderDetails[0]['order_id'] . "&facilityID=" . $facilityID . "");
		$this->smarty->assign('order', $orderDetails[0]);
		$this->smarty->assign('tpl', 'tpls/orderDetail.tpl');
		$this->smarty->assign("parent", $this->parent_category);
		$this->smarty->assign("request", $this->getFromRequest());
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionCompleteOrder() {
		$inventoryManager = new InventoryManager($this->db);
		$request = $this->getFromRequest();
		$orderID = $request['id'];
		
		$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID, $orderID);
		$orderDetails[0]['order_created_date'] = date('m/d/Y', $orderDetails[0]['order_created_date']);
		if ($orderDetails && $orderDetails[0]['order_status'] != OrderInventory::COMPLETED && $orderDetails[0]['order_status'] != OrderInventory::CANCELED) {
			$facilityID = $orderDetails[0]['order_facility_id'];
			$this->smarty->assign('tpl', 'tpls/orderComplete.tpl');
			$this->smarty->assign("action", "?action=completeOrder&category=orders&id=" . $orderDetails[0]['order_id'] . "");
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=sales&bookmark=orders");
			$this->smarty->assign("itemType", 'order');
			$this->smarty->assign('order', $orderDetails[0]);
			$this->smarty->assign("request", $this->getFromRequest());
		} else {
			throw new Exception('deny');
		}		
		

		$form = $_POST;
		if (count($form) > 0) {
				$form['status'] = OrderInventory::COMPLETED;
				$form['order_completed_date'] = time();
				//ORDERS FOR THIS PODUCT
				$orderList = $inventoryManager->getSupplierOrders($request['facilityID'], $orderDetails[0]['order_product_id']);
				$ProductInventory = new ProductInventory($this->db);
				$order = $inventoryManager->getSupplierOrderDetails($request['facilityID'], $form['order_id']);

				if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED) {
					$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
				} else {
					$dateBegin = $ProductInventory->period_start_date;
				}
				//
				$category = "facility";
			
				$productDetails = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $orderDetails[0]['order_facility_id'], $orderDetails[0]['order_product_id']);
				$product = $productDetails[0];
								$result = $inventoryManager->unitTypeConverter($product);
								if ($result){
									$product->usage = $result['usage'];
									$addToStock = $product->in_stock - $product->usage + $order[0]['order_amount'];
									$product->in_stock = $addToStock;
									$result1 = $product->save();
									
								}else{
									$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID,$request['id']);

									// For orders with status: Canceled or Completed denied edit function
									if ($orderDetails[0]['order_status'] != OrderInventory::COMPLETED && $orderDetails[0]['order_status'] != OrderInventory::CANCELED){
										$statuslist = $inventoryManager->getSupplierOrdersStatusList();

										$this->smarty->assign('status',$statuslist);

										$this->smarty->assign('order',$orderDetails[0]);	
										$this->smarty->assign('tpl', 'tpls/orderComplete.tpl');					
									}else{
										throw new Exception('deny');
									}
									$result1 = false;
									$this->smarty->assign('check','false');	
									
									//	E-mail notification about density not found
									$email = new EMail();
									$to = array('denis.nt@kttsoft.com');
									$from = AUTH_SENDER . "@" . DOMAIN;//$from = "authentification@vocwebmanager.com";
									$theme = $orderDetails[0]['order_name'].'. Problem with status changing to "completed"';
									$message = "Can't convert product usage to stock unit type, because the density do not specify! Order id is ".$orderDetails[0]['order_id'];
									$email->sendMail($from, $to, $theme, $message);		
			
									
									$this->smarty->assign('tpl','inventory/design/inventoryOrdersEdit.tpl');
								}	


		if ($result1) {		
			$result2 = $inventoryManager->updateSupplierOrder($form);	
		}
		if ($result2 == 'true') {

		$clientEmail = $inventoryManager->getClientEmail($facilityID);
		switch ($form['status']){
			case OrderInventory::IN_PROGRESS:
				$status = 'IN PROGRESSED';
			break;	
			case OrderInventory::CONFIRM:
				$status = 'CONFIRMED';
			break;
			case OrderInventory::COMPLETED:
				$status = 'COMPLETED';
			break;
			case OrderInventory::CANCELED:
				$status = 'CANCELED';
			break;	
		}
				foreach($clientEmail as $email){
					// EMAIL NOTIFICATION FOR CLIENT 
					
					$ifEmail = $inventoryManager->checkSupplierEmail($email['email']);
					
					$user = new User($this->db);
					$userDetails = $user->getUserDetails($_SESSION['user_id']);	

					if ($ifEmail){
						$text['msg'] = "You {$status} the order {$orderDetails[0]['order_name']} id: {$orderDetails[0]['order_id']} from Facility";
						$text['title'] = "Status of ".$orderDetails[0]['order_name']." id: {$orderDetails[0]['order_id']} was changed";
						$inventoryManager->sendEmailToSupplier($email['email'] , $text);
					}
						$text['msg'] = "Your order {$orderDetails[0]['order_name']} id: {$orderDetails[0]['order_id']} to supplier is {$status}";
						$text['title'] = "Status of ".$orderDetails[0]['order_name']." id: {$orderDetails[0]['order_id']} was changed";
						$inventoryManager->sendEmailToManager($userDetails['email'] , $text);
									
					
									
				}
				
				header("Location: supplier.php?action=browseCategory&category=sales&bookmark=orders");
			}		
		}
		$this->smarty->display("tpls:index.tpl");
	}	
	
	private function actionEdit() {
		
		$inventoryManager = new InventoryManager($this->db);
		$request = $this->getFromRequest();
		
		$facilityID = $this->getFromRequest('facilityID');
		$orderID = $this->getFromRequest('id');
		$orderDetails = $inventoryManager->getSupplierOrderDetails($facilityID, $orderID);

		if ($orderDetails && $orderDetails[0]['order_status'] != OrderInventory::COMPLETED && $orderDetails[0]['order_status'] != OrderInventory::CANCELED) {
			$statuslist = $inventoryManager->getSupplierOrdersStatusList();
			$this->smarty->assign('status', $statuslist);
			$this->smarty->assign('order', $orderDetails[0]);
			
			//$this->user->xnyo->user['user_id']
			
			$this->smarty->assign("parent", $this->parent_category);
			$this->smarty->assign("request", $this->getFromRequest());
			$this->smarty->assign('tpl', 'tpls/orderEdit.tpl');
			
		} else {
			throw new Exception('deny');
		}

		$form = $_POST;
		if (count($form) > 0) {
			//protected from xss
			

			if ($form['status'] == OrderInventory::COMPLETED) {
				$form['order_completed_date'] = time();
				//ORDERS FOR THIS PODUCT
				$orderList = $inventoryManager->getSupplierOrders($request['facilityID'], $orderDetails[0]['order_product_id']);
				$ProductInventory = new ProductInventory($this->db);
				$order = $inventoryManager->getSupplierOrderDetails($request['facilityID'], $form['order_id']);
				if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED) {

					$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
				} else {
					$dateBegin = $ProductInventory->period_start_date;
				}
				//
				$category = "facility";
				
				$productDetails = $inventoryManager->getProductUsageGetAll($dateBegin, $ProductInventory->period_end_date, $category, $form["facilityID"], $orderDetails[0]['order_product_id']);
				$product = $productDetails[0];

				$addToStock = $product->in_stock - $product->usage + $order[0]['order_amount'];
				$product->in_stock = $addToStock;
				$result = $product->save();
			}

			$result = $inventoryManager->updateSupplierOrder($form);



		if ($result == 'true') {

		$clientEmail = $inventoryManager->getClientEmail($facilityID);
		switch ($form['status']){
			case OrderInventory::IN_PROGRESS:
				$status = 'IN PROGRESSED';
			break;	
			case OrderInventory::CONFIRM:
				$status = 'CONFIRMED';
			break;
			case OrderInventory::COMPLETED:
				$status = 'COMPLETED';
			break;
			case OrderInventory::CANCELED:
				$status = 'CANCELED';
			break;	
		}
				foreach($clientEmail as $email){
					// EMAIL NOTIFICATION FOR CLIENT 
					
					$ifEmail = $inventoryManager->checkSupplierEmail($email['email']);
					
					$user = new User($this->db);
					$userDetails = $user->getUserDetails($_SESSION['user_id']);	

					if ($ifEmail){
						$text['msg'] = "You {$status} the order {$orderDetails[0]['order_name']} id: {$orderDetails[0]['order_id']} from Facility";
						$text['title'] = "Status of ".$orderDetails[0]['order_name']." id: {$orderDetails[0]['order_id']} was changed";
						$inventoryManager->sendEmailToSupplier($email['email'] , $text);
					}
						$text['msg'] = "Your order {$orderDetails[0]['order_name']} id: {$orderDetails[0]['order_id']} to supplier is {$status}";
						$text['title'] = "Status of ".$orderDetails[0]['order_name']." id: {$orderDetails[0]['order_id']} was changed";
						$inventoryManager->sendEmailToManager($userDetails['email'] , $text);
									
					
									
				}
				
				header("Location: supplier.php?action=browseCategory&category=sales&bookmark=orders");
			}
		}	
$this->smarty->display("tpls:index.tpl");		
/*		
		$id = $this->getFromRequest('id');
		$contactsManager = new SalesContactsManager($this->db);
		$contact = $contactsManager->getSalesContact($id,$this->user->xnyo->user['user_id']);
		$country = new Country($this->db);
		$registration = new Registration($this->db);
		$usaID = $country->getCountryIDByName('USA');
		$this->smarty->assign($usaID);
	
		if ($this->getFromPost('save') == 'Save') {
			
			$contact = $this->createContactByForm($_POST);
			$contact->id = $id;
                        
			if(!empty($contact->errors)) {
				
				$this->smarty->assign("error_message","Errors on the form");
			} else {
				
				$result = $contactsManager->saveContact($contact);
				if($result == true) {
					header("Location: sales.php?action=browseCategory&category=salescontacts&bookmark=contacts&page=".$this->getFromRequest('page')."&subBookmark=".$this->getFromRequest('subBookmark')."");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		}
		$this->smarty->assign("data",$contact);
                $countries =  $registration->getCountryList();
		$state = new State($this->db);
		$stateList = $state->getStateList($usaID);		
		$this->smarty->assign("creater_id",$this->user->xnyo->user['user_id']);
		$this->smarty->assign("request",$this->getFromRequest());
		$this->smarty->assign("states", $stateList);	
		$this->smarty->assign("usaID", $usaID);
		$this->smarty->assign("countries", $countries);
		$jsSources = array();											
		array_push($jsSources, 'modules/js/addContact.js');	
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/addContact.tpl');
		$this->smarty->display("tpls:index.tpl");
 * 
 */
	}
/*	
	private function actionAddItem() {		
		
		$contact = new SalesContact($this->db);
		$country = new Country($this->db);
		$registration = new Registration($this->db);
		$usaID = $country->getCountryIDByName('USA');
		$this->smarty->assign($usaID);

		if ($this->getFromPost('save') == 'Save') {
			
			$contact = $this->createContactByForm($_POST);
			
			$sub = $this->getFromRequest("subBookmark");
			
			if(!isset($sub)) {
				$sub = "contacts";
			}
			$contact->type = $sub;
      
			if(!empty($contact->errors)) {			
				$this->smarty->assign("error_message","Errors on the form");
			} else {
				$contactsManager = new SalesContactsManager($this->db);
				$result = $contactsManager->addContact($contact);
				if($result) {
					header("Location: sales.php?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark=$sub");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		} else {
			
			$contact->country_id = $usaID;
		}
		$this->smarty->assign("data",$contact);
		
		$this->smarty->assign("creater_id",$this->user->xnyo->user['user_id']);
		$countries =  $registration->getCountryList();
		$state = new State($this->db);
		$stateList = $state->getStateList($usaID);		
		$this->smarty->assign("request",$this->getFromRequest());
		$this->smarty->assign("states", $stateList);	
		$this->smarty->assign("usaID", $usaID);
		$this->smarty->assign("countries", $countries);
		$jsSources = array();											
		array_push($jsSources, 'modules/js/addContact.js');	
		$this->smarty->assign('jsSources', $jsSources);		
		$this->smarty->assign('tpl', 'tpls/addContact.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$manager = new SalesContactsManager($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {				
				$contact = $manager->getSalesContact($this->getFromRequest('item_'.$i),$this->user->xnyo->user['user_id']);				
				$item["id"]	= $contact->id;
				$item["name"] = $contact->contact;				
				$itemForDelete []= $item;
			}
		}
		
		$this->smarty->assign("gobackAction","viewDetails");
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');		
		$manager = new SalesContactsManager($this->db);
		
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			
			$manager->deleteSalesContact($id, $this->user->xnyo->user['user_id']);
		}
		header ('Location: sales.php?action=browseCategory&category=salescontacts&bookmark='.$this->getFromRequest('category'));
		die();
	}
	
	private function createContactByForm($form) {		
		
		if($form['state_select_type'] == 'text') {
			unset($form['selState']);
			$form['state'] = $form['txState'];
		} else {
			unset($form['txState']);
			$form['state_id'] = $form['selState'];
		}
		$contact = new SalesContact($this->db);		
		foreach($form as $key => $value) {
			try {
				$contact->$key = $value;
			}catch(Exception $e) {
				$contact->unsafe_set_value($key,$value);
			}
		} 
		
		if(empty($contact->errors)) {
			$contact->erorrs = false;
		}		
		return $contact;
	}
  */      

	
	
}