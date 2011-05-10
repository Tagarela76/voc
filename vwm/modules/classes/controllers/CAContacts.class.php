<?php
class CAContacts extends Controller {
	
	function CAContacts($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='contacts';
		$this->parent_category='salescontacts';		
	}
	
	function runAction() {
		
		$this->runCommon('admin');
		
		$functionName='action'.ucfirst($this->action);	
					
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkContacts($vars) {
		extract($vars);
		
		$manager = new SalesContactsManager($this->db);
		$totalCount = $manager->getTotalCount();
		
		$pagination = new Pagination($totalCount);
		$pagination->url = "?action=browseCategory&category=salescontacts&bookmark=contacts";
		
		
		$sub = $this->getFromRequest("subBookmark");
		
		if(!isset($sub)) {
			$sub = "contacts";
		}
		
		$contactsList = $manager->getContactsList($pagination,strtolower($sub));
		
		
		
		/*$apmethodList=$apmethod->getApmethodList($pagination);
		$field='apmethod_id';
		$list = $apmethodList;
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=apmethod&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}*/
		
		

		$this->smarty->assign("contacts",$contactsList);
		
		$this->smarty->assign("itemsCount",$totalCount);
		
		$this->smarty->assign('tpl', 'tpls/bookmarkContacts.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		
		$manager = new SalesContactsManager($this->db);
		
		$contact = $manager->getSalesContact($this->getFromRequest('id'));
		
		$this->smarty->assign('contact', $contact);
		$this->smarty->assign('tpl', 'tpls/viewContact.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		
		$id = $this->getFromRequest('id');
		
		
		
		$contactsManager = new SalesContactsManager($this->db);
		$contact = $contactsManager->getSalesContact($id);
		
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
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		}
		//var_dump($contact);
		$this->smarty->assign("data",$contact);
		
		
		
		$countries =  $registration->getCountryList();
		
		
		
		 
		
		$state = new State($this->db);
		$stateList = $state->getStateList($usaID);														
		$this->smarty->assign("states", $stateList);	
		$this->smarty->assign("usaID", $usaID);
		
		
		$this->smarty->assign("countries", $countries);
		
		$jsSources = array();											
		array_push($jsSources, 'modules/js/addContact.js');	
		$this->smarty->assign('jsSources', $jsSources);
		
		$this->smarty->assign('tpl', 'tpls/addContact.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
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
				if($result == true) {
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark=$sub");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		} else {
			
			$contact->country_id = $usaID;
		}
		//var_dump($contact);
		$this->smarty->assign("data",$contact);
		
		
		
		$countries =  $registration->getCountryList();
		
		
		
		 
		
		$state = new State($this->db);
		$stateList = $state->getStateList($usaID);														
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
				
				$contact = $manager->getSalesContact($this->getFromRequest('item_'.$i));
				
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
			
			$manager->deleteSalesContact($id);
		}
		header ('Location: admin.php?action=browseCategory&category=salescontacts&bookmark='.$this->getFromRequest('category'));
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
		//var_Dump($form);
		
		//$errors = array();
		
		$contact = new SalesContact($this->db);
		
			
		foreach($form as $key => $value) {
			try {
				$contact->$key = $value;
			}catch(Exception $e) {
				//$errors[] = $e->getMessage();
				$contact->unsafe_set_value($key,$value);
			}
		} 
		
		if(empty($contact->errors)) {
			$contact->erorrs = false;
		}
		
		//var_dump($contact->errors);
		
		return $contact;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}