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
    
	private function actionAddItem() {
		
		$bookmark = new Bookmark($this->db);
        $manager = new BookmarksManager($this->db);
        $bookmarksList = $manager->getBookmarksList();

//Get sales users list
		$users = new User($this->db);
		$userlist = $users->getUsersList('sales');
		$this->smarty->assign("userlist",$userlist);
	
		if ($this->getFromPost('save') == 'Save') {
			
			$bookmark = $this->bookmarkByForm($_POST,$bookmark);

                        $bookmark->controller = $this->getFromRequest("bookmark");
                        if(!empty($bookmark->errors)) {
				$this->smarty->assign("error_message","Errors on the form");
			} else {
					
                    $result = $bookmark->saveBookmark();                               

				if ($result == true) {
					$manager->Users2bookmarks($_POST['name'],$_POST['Username']);
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
        
        private function actionEdit() {
                
                $name = $this->getFromRequest('subBookmark');
                if (!isset($name)){
                    $name =  $this->getFromRequest('bookmark');
                }
                    $name = htmlentities($name);
                $query = "SELECT * from " . TB_BOOKMARKS_TYPE . " WHERE name = '".$name."'";                
		$this->db->query($query);
		$id = $this->db->fetch_all_array();
		$bookmarksManager = new BookmarksManager($this->db);
		$bookmark = $bookmarksManager->getBookmark($id[0]["id"]);
        
		$query = "SELECT user_id from users2bookmarks WHERE bookmark_id = '".$id[0]["id"]."'";
		$this->db->query($query);
		$users_id = $this->db->fetch_all_array();

		
		if ($this->getFromPost('save') == 'Save') {
			$bookmark = $this->bookmarkByForm($_POST,$bookmark);
				
                        $bookmark->controller = $this->getFromRequest("bookmark");
			if(!empty($bookmark->errors)) {
				
				$this->smarty->assign("error_message","Errors on the form");
			} else {
                                $result = $bookmark->saveBookmark();
				if($result == true) {
					$bookmarksManager->Users2bookmarks($_POST['name'],$_POST['Username'],$id[0]["id"]);
					header("Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts");
				} else {
					$this->smarty->assign("error_message",$bookmark->getErrorMessage());
				}
			}
		}
//Get sales users list
		$users = new User($this->db);
		$userlist = $users->getUsersList('sales');
		$this->smarty->assign("userlist",$userlist);		
        $this->smarty->assign("users_id",$users_id);
		
		$this->smarty->assign("data",$bookmark);
		$this->smarty->assign('tpl', 'tpls/addBookmark.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
        
	private function actionDeleteItem() {
                    $manager = new BookmarksManager($this->db);
                    
					$sub = $this->getFromRequest("subBookmark");
					$sub = strtolower($sub);
					$sub = htmlentities($sub);
					$subID = $manager->getBookmarkStats($sub);
					$subtodelete = $manager->getBookmark($subID);
					
					$submass[0]['id'] = $subtodelete->id;
					$submass[0]['name'] = $subtodelete->name;
				
					$this->smarty->assign("flashback", "salescontacts");
                   	$this->finalDeleteItemACommon($submass);
					
                    //$this->finalDeleteItemACommon($itemForDelete);
	}
        
	private function actionConfirmDelete() {
            
	$manager = new BookmarksManager($this->db);
	$bookmarksList = $manager->getBookmarksList();                
	$itemsCount= $manager->getCount();
	$itemForDelete = array();
	$countChecked = 0;
                
		for ($i=0; $i<=$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {				
				$bookmark = $manager->getBookmark($this->getFromRequest('item_'.$i));
                $item = new Bookmark($this->db);
				$item->id	= $bookmark->id;
				$item->name = $bookmark->name;				
                $item->controller = $bookmark->controller;
				$itemForDelete [$i]= $item;
                                $countChecked++;
			}
		}
                
                $manager->deleteBookmarks($itemForDelete);
                $manager->updateType($itemForDelete);
                
		header ('Location: admin.php?action=browseCategory&category=salescontacts&bookmark=contacts');
		
	}
        
	private function bookmarkByForm($form, Bookmark $bookmark) {	
            		
		foreach($form as $key => $value) {
                    try {
                                $bookmark->$key = htmlentities($value);
                                
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
