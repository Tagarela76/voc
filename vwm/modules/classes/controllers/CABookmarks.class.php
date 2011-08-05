<?php
class CABookmarks extends Controller{
    
        function CABookmarks($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);	
	}
        
        function runAction() {		
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
        
	private function actionViewDetails() {
		
		$manager = new BookmarksManager($this->db);
		$bookmark = $manager->getBookmark($this->getFromRequest('id'));
		$this->smarty->assign('bookmark', $bookmark);
		$this->smarty->assign('tpl', 'tpls/viewBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");
	}        
        
	private function actionAddItem() {
		$bookmark = new Bookmark($this->db);
		
		if ($this->getFromPost('save') == 'Save') {
			
			$bookmark = $this->bookmarkByForm($_POST,$bookmark);
			
                        $bookmark->controller = $this->getFromRequest("bookmark");
                        if(!empty($bookmark->errors)) {
				$this->smarty->assign("error_message","Errors on the form");
			} else {
                                $result = $bookmark->saveBookmark();
				if($result == true) {
                                       header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts");                                      
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
			   
		}

		$this->smarty->assign("data",$bookmark);	
		$this->smarty->assign('tpl', 'tpls/addBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
        
        private function actionEdit() {
                
                $name = $this->getFromRequest('subBookmark');
                if (!isset($name)){
                    $name =  $this->getFromRequest('bookmark');
                }
		$bookmarksManager = new BookmarksManager($this->db);
		$bookmark = $bookmarksManager->getBookmark($name);
		
		if ($this->getFromPost('save') == 'Save') {
			$bookmark = $this->bookmarkByForm($_POST,$bookmark);
                        $bookmark->controller = $this->getFromRequest("bookmark");
                        
			if(!empty($bookmark->errors)) {
				
				$this->smarty->assign("error_message","Errors on the form");
			} else {
                                $result = $bookmark->saveBookmark();
				if($result == true) {
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts");
				} else {
					$this->smarty->assign("error_message",$bookmark->getErrorMessage());
				}
			}
		}
                
		$this->smarty->assign("data",$bookmark);
		$this->smarty->assign('tpl', 'tpls/addBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
        
	private function actionDeleteItem() {
                /*$bookmark = new Bookmark($this->db);
		if ($this->getFromPost('save') == 'Save') {
			$bookmark = $this->bookmarkByForm($_POST,$bookmark);
                        $bookmark->controller = $this->getFromRequest("bookmark");
                        if(!empty($bookmark->errors)) {
				$this->smarty->assign("error_message","Errors on the form");
			} else {
                                $result = $bookmark->saveBookmark();
				if($result == true) {
                                       header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts");                                      
				} else {
					$this->smarty->assign("error_message",$contact->getErrorMessage());
				}
			}
			   
		}
		$this->smarty->assign("data",$bookmark);	
		$this->smarty->assign('tpl', 'tpls/addBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");*/
            
		$bookmark=$this->getFromRequest('bookmark');
                $manager = new BookmarksManager($this->db);
                $bookmarksList = $manager->getBookmarksList();
                $totalCount = $manager->getCount();
                $this->smarty->assign("bookmarks",$bookmarksList);		
		$this->smarty->assign("itemsCount",$totalCount);
                
                if ($this->getFromPost('delete') == 'Save') {
                    echo "confirmed";
                }
                                
		//$this->smarty->assign("gobackAction","viewDetails");
                $this->smarty->assign("data",$bookmark);
		$this->smarty->assign('tpl', 'tpls/deleteBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");
		$this->finalDeleteItemACommon($item);
                
		
                
                /*$itemsCount= $this->getFromRequest('itemsCount');
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
		$this->finalDeleteItemACommon($itemForDelete);*/
	}
        
	private function actionConfirmDelete() {
		//$itemsCount= $this->getFromRequest('itemsCount');		
		$manager = new SalesContactsManager($this->db);
		
		//for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			
			//$manager->deleteSalesContact($id);
		//}
		header ('Location: admin.php?action=browseCategory&category=salescontacts&bookmark='.$this->getFromRequest('category'));
		die();
	}
        
	private function bookmarkByForm($form, Bookmark $bookmark) {	

		//$bookmark = new Bookmark($this->db);		
		foreach($form as $key => $value) {
			try {
				$bookmark->$key = $value;
			}catch(Exception $e) {
				$bookmark->unsafe_set_value($key,$value);
			}
		} 
		
		if(empty($bookmark->errors)) {
			$bookmark->erorrs = false;
		}		
                
		return $bookmark;
	}
        
}

?>