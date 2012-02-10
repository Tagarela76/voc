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
		$sub = $this->getFromRequest("subBookmark");
		if (!isset($sub) || $sub == '') {
			$sub = $this->getFromRequest("bookmark");
		}
		$sub = strtolower($sub);
		$sub = htmlentities($sub);
		$manager = new BookmarksManager($this->db);
		$subNumber = $manager->getBookmarkStats($sub);
		
		$filterStr = $this->filterList('contacts');
		$manager = new SalesContactsManager($this->db);

		
		/*SORT*/
		$sortStr="";
		if (!is_null($this->getFromRequest('sort')))
		{
			$sort= new Sort($this->db,'contacts',0);
			$sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));										
			$this->smarty->assign('sort',$this->getFromRequest('sort'));
		}
		else									
			$this->smarty->assign('sort',0);
		if (!is_null($this->getFromRequest('searchAction')))									
			$this->smarty->assign('searchAction',$this->getFromRequest('searchAction'));
		/*/SORT*/
		
	
		// search (not empty q)
		if ($this->getFromRequest('q') != '') {
			$contactsToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$searchedContactsCount = $manager->countSearchedContacts($contactsToFind, 'company', 'contact', $subNumber);
			$pagination = new Pagination($searchedContactsCount);
			$pagination->url = "?q=" . urlencode($this->getFromRequest('q')) . "&action=browseCategory&category=salescontacts&bookmark=contacts";
			if ($sub != 'contacts') {
				$pagination->url .= "&subBookmark=" . urlencode($sub);
			}
			$contactsList = $manager->searchContacts($contactsToFind, 'company', 'contact', $subNumber, $pagination,$sortStr);
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			$this->smarty->assign('pagination', $pagination);
			$totalCount = $manager->getTotalCount(strtolower($sub));
		} else { // search (empty q)
			$totalCount = $manager->getTotalCount(strtolower($sub));
			//pag for filter
			// kostyl'!!
			if ($_REQUEST['filterField'] == 'id') {
				$filterStr = " c." . $filterStr;
			}			
			if ($this->getFromRequest('searchAction') == 'filter') {
				$count = $manager->countContacts($subNumber, $filterStr);
				$pagination = new Pagination($count);
				$pagination->url = "?action=browseCategory&category=" . $this->getFromRequest('category') . "&bookmark=" . $this->getFromRequest('bookmark');
				if ($this->getFromRequest('filterField') != '') {
					$pagination->url .= "&filterField=" . $this->getFromRequest('filterField');
				}
				if ($this->getFromRequest('filterCondition') != '') {
					$pagination->url .= "&filterCondition=" . $this->getFromRequest('filterCondition');
				}
				if ($this->getFromRequest('filterValue') != '') {
					$pagination->url .= "&filterValue=" . $this->getFromRequest('filterValue');
				}
				if ($this->getFromRequest('filterField') != '') {
					$pagination->url .= "&searchAction=filter";
				}
			}
			// q is empty
			else {
				$pagination = new Pagination($totalCount);
				$pagination->url = "?action=browseCategory&category=salescontacts&bookmark=contacts";
				if ($subNumber != 1) {
					$pagination->url .= "&subBookmark=" . urlencode($sub);
				}
			}

			$contactsList = $manager->getContactsList($pagination, $sub, $filterStr, null ,$sortStr);
			$this->smarty->assign('pagination', $pagination);
			
			
		}
		




		$page = $this->getFromRequest("page");
		$this->smarty->assign('page', $page);
		//	set js scripts
		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("contacts", $contactsList);
		$this->smarty->assign("itemsCount", $totalCount);
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
				//SAVE CONTACT TPES
				$contactsManager->saveSalesContactType($id,$_POST['type']);
				

				$result = $contactsManager->saveContact($contact);
				if($result == true) {
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts&page=".$this->getFromRequest('page')."&subBookmark=".$this->getFromRequest('subBookmark')."");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		}
		
		$ContactTypeList = $contactsManager->getSalesContactTypeList();

		$this->smarty->assign("typelist",$ContactTypeList);
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
				if($result) {
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark=$sub");
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
		} else {
			
			$contact->country_id = $usaID;
		}
		$this->smarty->assign("creater_id",$this->user->xnyo->user['user_id']);
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
		header ('Location: admin.php?action=browseCategory&category=salescontacts&bookmark='.$this->getFromRequest('category').'&suBookmark='.$this->getFromRequest('subBookmark').'');
		die();
	}
	
	private function createContactByForm($form) {		
		
		if($form['state_select_type'] == 'text') {
			unset($form['selState']);
			$form['state'] = $form['txState'];
		} else {
			unset($form['txState']);
			$form['state_id'] = $form['selState'];
			$form['state'] = $form['state_name_5'];
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