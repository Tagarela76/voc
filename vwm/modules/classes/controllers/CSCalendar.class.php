<?php

class CSCalendar extends Controller {
	
	function CSCalendar($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='calendar';
		$this->parent_category='calendar';		
	}
	
	function runAction() {		
		$this->runCommon('sales');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function actionBrowseCategory($vars) {
		extract($vars);

		$sub = $this->getFromRequest("subBookmark");
/*
		if (!isset($sub) || $sub == '') {
			if (isset($bookmarksList[0])) {
				$sub = $bookmarksList[0]->get_name();
				header ("Location: ?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark={$sub}");
			} else {
				$sub = $this->getFromRequest("bookmark");
			}
		}
*/
/*	
		if (!isset($sub) || $sub == '') {
			$sub = $this->getFromRequest("bookmark");
		}	
 */	 
/*	

 * 
 */
		//	set js scripts
		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js', 'extensions/calendar/js/coda.js');
		$this->smarty->assign('jsSources', $jsSources);
		$cssSource = array('extensions/calendar/css/master.css');
		$this->smarty->assign('cssSource', $cssSource);
		$this->smarty->assign('doNotShowControls', true);

		$this->smarty->assign('tpl', 'tpls/calendar.tpl');
		$this->smarty->display("tpls:index.tpl");
		
               
	}
	

	private function actionViewDetails() {
		
		$manager = new SalesContactsManager($this->db);
		$contact = $manager->getSalesContact($this->getFromRequest('id'),$this->user->xnyo->user['user_id']);
		$this->smarty->assign("parent",$this->parent_category);
		$this->smarty->assign("request",$this->getFromRequest());
		$this->smarty->assign('contact', $contact);
		$this->smarty->assign('tpl', 'tpls/viewContact.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		
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
        

	
	
}