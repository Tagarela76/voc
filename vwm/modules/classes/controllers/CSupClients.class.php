<?php

class CSupClients extends Controller {
	
	function CSupClients($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='clients';
		$this->parent_category='sales';		
	}
	
	function runAction() {		
		$this->runCommon('supplier');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}

	protected function actionBrowseCategory() {
		$this->bookmarkClients();
	}
	
	protected function bookmarkClients($vars) {
		extract($vars);

		$request = $this->getFromRequest();
		if (!$request.supplierID){
			$supplierID = $supplierIDS[0]['supplier_id'];
		}else{
			$supplierID = $request['supplierID'];
		}
		
		$inventoryManager = new InventoryManager($this->db);
		$result = $inventoryManager->getSupplierWholeDiscount($supplierID);

		if ($result){
			$discountList = $result;
			$tmpArr = array ();
			foreach ($discountList as $discount){
				$discount['url'] = "?action=viewDetails&category=clients&companyID={$discount['company_id']}&facilityID={$discount['facility_id']}&supplierID={$supplierID}";
				$tmpArr[] = $discount;
			}
			$discountList = $tmpArr;
		}

		

		//set js scripts
		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js');
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign('clients', $discountList);
		$this->smarty->assign('supplierID', $supplierID);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("itemsCount", $totalCount);
		$this->smarty->assign("request",$request);
		$this->smarty->assign('tpl', 'tpls/bookmarkClients.tpl');
		$this->smarty->assign('pagination', $pagination);
               
	}
	

	private function actionViewDetails() {
		

		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];

		$inventoryManager = new InventoryManager($this->db);
		
		
		
		$facilityID = $this->getFromRequest('facilityID');
		
		$result = $inventoryManager->getSupplierWholeDiscount($supplierID,$facilityID);
		if ($result){
			$client = $result;
		}	
		$result = $inventoryManager->getSupplierSeparateDiscount($facilityID,$supplierID);	
		
		if ($result){
			foreach ($result as $prdct){
				$prdct['url'] = "?action=editPDiscount&category=clients&facilityID={$facilityID}&productID={$prdct['product_id']}&supplierID={$supplierID}";
				$pdiscount[] = $prdct;
			}
		}		
	//	$result = $inventoryManager->getSupplierDiscounts($facilityID,$supplierID);	

		//$this->user->xnyo->user['user_id']
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign("request",$request);
		$this->smarty->assign('client', $client[0]);
		$this->smarty->assign('clients', $pdiscount);
		$this->smarty->assign('tpl', 'tpls/clientDetail.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		
		$inventoryManager = new InventoryManager($this->db);

		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];
		$facilityID = $this->getFromRequest('facilityID');
		$result = $inventoryManager->getSupplierWholeDiscount($supplierID,$facilityID);

		if ($result){
			$client = $result;
		}else{
			throw new Exception('404');
		}		

			$error = $this->getFromRequest('error');

				if ( $error == null ){
				$form = $_POST;

				if (count($form) > 0) {
				
					$result = $inventoryManager->updateSupplierDiscounts($form);			
					if ($result == 'true'){
						header("Location: ?action=browseCategory&category=sales&bookmark=clients");
					}else{
						header("Location: ?action=addItem&category=clients&error=exist");
					}
				}
		}		
		
		
		$jsSources = array ('modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js');
	    $this->smarty->assign('jsSources',$jsSources);		
		$this->smarty->assign('client', $client[0]);
		$this->smarty->assign("request",$request);
		$this->smarty->assign('tpl', 'tpls/clientEdit.tpl');
		$this->smarty->display("tpls:index.tpl");
		
	}
	
	private function actionEditPDiscount() {
		
		$inventoryManager = new InventoryManager($this->db);
		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];
		$productID = $request['productID'];
		$facilityID = $request['facilityID'];
		
		$result = $inventoryManager->getSupplierSeparateDiscount($facilityID, $supplierID, $productID);
		if ($result){
			$discount = $result;
		}else{
			throw new Exception('404');
		}		

			$error = $this->getFromRequest('error');

				if ( $error == null ){
				$form = $_POST;

				if (count($form) > 0) {

					$result = $inventoryManager->updateSupplierDiscounts($form);			
					if ($result == 'true'){
						header("Location: ?action=viewDetails&category=clients&facilityID={$discount[0]['facility_id']}&supplierID={$discount[0]['supplier_id']}");
					}else{
						header("Location: ?action=addItem&category=clients&error=exist");
					}
				}
		}		
		
		
		$jsSources = array ('modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js');
	    $this->smarty->assign('jsSources',$jsSources);		
		$this->smarty->assign('client', $discount[0]);
		$this->smarty->assign("request",$request);
		$this->smarty->assign('tpl', 'tpls/clientEdit.tpl');
		$this->smarty->display("tpls:index.tpl");
		
	}	
	
	private function actionAddItem() {
		$inventoryManager = new InventoryManager($this->db);
		$companyManager = new Company($this->db);

		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];
		// Check user supplier in GET
		$supplierIDS = $inventoryManager->getSaleUserSupplierLst($this->user->xnyo->user['user_id']);
		foreach($supplierIDS as $sid){
			$supplierIDArray[] = $sid['supplier_id'];
		}
		if (!in_array($supplierID, $supplierIDArray)){
			throw new Exception('404');
		}
		
		$companyList = $companyManager->getCompanyList();
		$facility = new Facility($this->db);
		$facilityList = $facility->getFacilityListByCompany($companyList[0]['id']);
		$this->smarty->assign("facility", $facilityList);
		

		$error = $this->getFromRequest('error');

		if ($error == null) {
			$form = $_POST;

			if (count($form) > 0) {
			
				$form['supplier_id'] = $supplierID;

				$checkID = $inventoryManager->checkDiscountID($form['companyID'], $form['facilityID'],$form['supplier_id']);
				if ($checkID){
					$form['discount_id'] = $checkID[0]['discount_id'];
				}
						
				$result = $inventoryManager->updateSupplierDiscounts($form);

				if ($result) {
					header("Location: ?action=browseCategory&category=sales&bookmark=clients");
				} else {
					header("Location: ?action=addItem&category=clients&error=exist");
				}
			}
		}

		$this->smarty->assign('companies', $companyList);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('tpl', 'tpls/clientAdd.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
/*	
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