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
		if (!$request['supplierID']){
			$supplierID = $supplierIDS[0]['supplier_id'];
		}else{
			$supplierID = $request['supplierID'];
		}
		$jobberID = $request['jobberID'];
		$inventoryManager = new InventoryManager($this->db);
		// SOrt
		$sortStr = $this->sortList('clients',2);

		$result = $inventoryManager->getSupplierWholeDiscount($supplierID,null,$jobberID,$pagination,$sortStr);

		if ($result){
			foreach ($result as $discount){
				if (!$discount['discount_id']){
					
					$data['discount_id'] = $discount['discount_id'];
					$data['companyID'] = $discount['company_id'];
					$data['facilityID'] = $discount['facility_id'];
					$data['supplier_id'] = $discount['supplier_id'];
					$data['jobberID'] = $discount['jobber_id'];
					$data['discount'] = 0;
					
					$inventoryManager->updateSupplierDiscounts($data);
				}
			}
			// Pagination	
			$count = $inventoryManager->getCountSupplierDiscounts($supplierID,$jobberID);
			$pagination = new Pagination($count);
			$pagination->url = "?action=browseCategory&category=sales&bookmark=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}";
			$this->smarty->assign('pagination', $pagination);	
			
			$result = $inventoryManager->getSupplierWholeDiscount($supplierID,null,$jobberID,$pagination,$sortStr);

			$discountList = $result;
			$tmpArr = array ();
			foreach ($discountList as $discount){
				$discount['url'] = "?action=viewDetails&category=clients&companyID={$discount['company_id']}&facilityID={$discount['facility_id']}&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}";
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

               
	}
	

	private function actionViewDetails() {
		

		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];

		$inventoryManager = new InventoryManager($this->db);
		
		
		
		$facilityID = $this->getFromRequest('facilityID');
		
		$result = $inventoryManager->getSupplierWholeDiscount($supplierID,$facilityID,$request['jobberID']);
		if ($result){
			$client = $result;
		}else{
			throw new Exception('404');
		}	
	
		$result = $inventoryManager->getSupplierSeparateDiscount($facilityID,$supplierID,null,$request['jobberID']);	

		if ($result){
			foreach ($result as $prdct){
				$prdct['url'] = "?action=editPDiscount&category=clients&facilityID={$facilityID}&productID={$prdct['product_id']}&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}";
				$prdct['discount'] = ($prdct['discount']) ? $prdct['discount'] : 0;
				$pdiscount[] = $prdct;
				
			}
		}		
	//	$result = $inventoryManager->getSupplierDiscounts($facilityID,$supplierID);	

		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=sales&bookmark=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}");
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
		$result = $inventoryManager->getSupplierWholeDiscount($supplierID,$facilityID,$request['jobberID']);

		if ($result){
			$client = $result;
		}else{
			throw new Exception('404');
		}		

			$error = $this->getFromRequest('error');

				if ( $error == null ){
				$form = $_POST;

				if (count($form) > 0) {
					$form['jobberID'] = $request['jobberID'];
					$result = $inventoryManager->updateSupplierDiscounts($form);			
					if ($result == 'true'){
						header("Location: ?action=browseCategory&category=sales&bookmark=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}");
					}else{
						header("Location: ?action=addItem&category=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}&error=exist");
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

		$result = $inventoryManager->getSupplierSeparateDiscount($facilityID, $supplierID, $productID,$request['jobberID']);
		if ($result){
			$discount = $result;
		}else{
			throw new Exception('404');
		}		

			$error = $this->getFromRequest('error');

				if ( $error == null ){
				$form = $_POST;

				if (count($form) > 0) {
					$form['jobberID'] = $request['jobberID'];
					$result = $inventoryManager->updateSupplierDiscounts($form);			
					if ($result == 'true'){
						header("Location: ?action=viewDetails&category=clients&facilityID={$discount[0]['facility_id']}&supplierID={$discount[0]['supplier_id']}&jobberID={$request['jobberID']}");
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

		
		$companyList = $companyManager->getCompanyList();
		$facility = new Facility($this->db);
		$facilityList = $facility->getFacilityListByCompany($companyList[0]['id']);
		$this->smarty->assign("facility", $facilityList);
		

		$error = $this->getFromRequest('error');

		if ($error == null) {
			$form = $_POST;

			if (count($form) > 0) {
			
				$form['supplier_id'] = $supplierID;
				$form['jobberID'] = $request['jobberID'];
				$checkID = $inventoryManager->checkDiscountID($form['companyID'], $form['facilityID'],$form['supplier_id'],$request['jobberID']);
				if ($checkID){
					$form['discount_id'] = $checkID[0]['discount_id'];
				}
						
				$result = $inventoryManager->updateSupplierDiscounts($form);

				if ($result) {
					header("Location: ?action=browseCategory&category=sales&bookmark=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}");
				} else {
					header("Location: ?action=addItem&category=clients&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}&error=exist");
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