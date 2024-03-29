<?php

class CSupGom extends Controller {
	
	function CSupGom($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='gom';
		$this->parent_category='sales';		
	}
	
	function runAction() {		
		$this->runCommon('supplier');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}

	protected function actionBrowseCategory() {
		$this->bookmarkGom();
	}
	
	protected function bookmarkGom($vars) {
		extract($vars);

		$request = $this->getFromRequest();
		if (!$request['supplierID']){
			$supplierID = $supplierIDS[0]['supplier_id'];
		}else{
			$supplierID = $request['supplierID'];
		}
		
		$supplierID = 0; //KOSTYL" supplier not needed
		$jobberID = $request['jobberID'];
		
		
		$gomManager = new Accessory($this->db);

		// SOrt
		$sortStr = $this->sortList('gomPrice',2);		
		
		$goms = $gomManager->getGomPriceList($jobberID);

		if (!$goms){
			$goms = $gomManager->getGomList($jobberID);
			foreach ($goms as $product){
				$priceProduct = new ProductPrice($this->db, $product);
				$priceProduct->supman_id = $supplierID;
				$priceProduct->jobber_id = $jobberID;
				$priceProduct->unittype = GOMInventory::PIECES_UNIT_TYPE_ID;
				$priceProduct->save();
			}
			
		}
		
		// Pagination	
		$count = $gomManager->getCountGoms($jobberID);
		$pagination = new Pagination($count);
		$pagination->url = "?action=browseCategory&category=sales&bookmark=gom&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}";
		$this->smarty->assign('pagination', $pagination);
		
		$goms = $gomManager->getGomPriceList($jobberID,null, $pagination, $sortStr);
	
		foreach ($goms as $product){
			$comapnyArray = $gomManager->getCompanyListWhichGOMUse($product['product_id']);
			$price4prduct = new ProductPrice($this->db, $product);
			$price4prduct->jobber_id = $jobberID;
			$price4prduct->supman_id = $supplierID;
			$price4prduct->url = "supplier.php?action=edit&category=gom&id=".$price4prduct->price_id."&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}&bookmark={$request['bookmark']}";
			//$price4prduct->save();
			$productsArr[] = $price4prduct;
			if ($comapnyArray){
				$comapnyList[] = $comapnyArray;
			}		
		}

		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js');
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign('products', $productsArr);
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign("comapnyList", $comapnyList);
		$this->smarty->assign('tpl', 'tpls/bookmarkGom.tpl');

               
	}
	

	private function actionNewUnittype() {
		$ajaxResponse = new AJAXResponse();
		$form = $this->getFromPost();
		$request = $this->getFromRequest();
		$priceID = $request['id'];
		$jobberID = $request['jobberID'];	

		$unittypeManager = new Unittype($this->db);

		if ($form) {
		$unitTypelist = $unittypeManager->getUnittypeListDefault('AllOther');
		$result = true;
		foreach($unitTypelist as $unittype){
			if ($form['unittype'] == $unittype['unittype_desc']){
				$result = false;
			}
		}
			if ($result) {				
				
				//$unittypeName = substr($form['unittype'], 0, 3);
				//$unittypeName .= '.';
				
				$unittypeDesc = $form['unittype'];		
				$unittypeID = $unittypeManager->insertOtherUnitType($unittypeDesc, $unittypeDesc);
				//var_dump($form['unittype']);die();
				$ajaxResponse->setSuccess(true);
				$ajaxResponse->setMessage('Changes saved successfully');
				$ajaxResponse->data = array(
					'id'	=> $unittypeID,
					'name'	=> $form['unittype']									
				);				

			} else {
				$ajaxResponse->setSuccess(false);
				$ajaxResponse->setMessage('This unit type already exist!');
				$validationRes["summary"] = false;		
				$validationRes["unittype"] = "This unit type already exist!";				
				$ajaxResponse->validationRes = $validationRes;
			}															
		} else {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage('Wrong unit type name!');
			$validationRes["summary"] = false;		
			$validationRes["unittype"] = "Wrong unit type name!";		
			$ajaxResponse->validationRes = $validationRes;
		}
		
		$ajaxResponse->response();		
		//header("Location: ?action=edit&category=gom&id={$priceID}&jobberID={$jobberID}&supplierID={$request['supplierID']}&bookmark=gom");
		
		/*$this->user->xnyo->user['user_id']
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign("request",$this->getFromRequest());
		$this->smarty->assign('contact', $contact);
		$this->smarty->assign('tpl', 'tpls/priceEdit.tpl');
		$this->smarty->display("tpls:index.tpl");*/
	}
	
	private function actionEdit() {
		$request = $this->getFromRequest();
		$priceID = $request['id'];
		$jobberID = $request['jobberID'];

		$gomManager = new Accessory($this->db);

		$product = $gomManager->getGomPriceList($jobberID,$priceID, $pagination, $sortStr);

		if (!$product) throw new Exception('404');

			$form = $_POST;

			if (count($form) > 0) {
				$price4prduct = new ProductPrice($this->db, $product[0]);

				$price4prduct->price = $form['price'];
				$price4prduct->unittype = $form['selectUnittype'];
				$result = $price4prduct->save();
				
				if ($result == 'true') {
					header("Location: ?action=browseCategory&category=sales&bookmark=gom&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}");
				}
			}
		//	Get UnitType list
		$unitType = new Unittype($this->db);
		$unitTypelist = $unitType->getUnittypeListDefault('AllOther');

		$typeEx[] = "AllOther";
		$this->smarty->assign('unittype', $unitTypelist);
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('unitTypeClass', 'AllOther');
			

		$jsSources = array (
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',

			'modules/js/addUsage.js');
	    $this->smarty->assign('jsSources',$jsSources);	
		$this->smarty->assign("product", $product[0]);
		$this->smarty->assign("addUsageUrl", "?action=newUnittype&category=gom&id={$priceID}&jobberID={$jobberID}&supplierID={$request['supplierID']}&bookmark=gom");
		$this->smarty->assign("request", $request);
		$this->smarty->assign('tpl', 'tpls/priceEdit.tpl');
		$this->smarty->display("tpls:index.tpl");
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