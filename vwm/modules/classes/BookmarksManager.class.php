<?php
class BookmarksManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getBookmark($id) {
		$query = "SELECT * from " . TB_BOOKMARKS_TYPE . " WHERE id = '".$id."'";
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarksArr = $arr[0];

		$bookmark = new Bookmark($this->db, $bookmarksArr);
		return $bookmark;
	}        
        
	public function getBookmarkStats($name,$user_id = null) {
		//$query = "SELECT * FROM " . TB_BOOKMARKS_TYPE . " WHERE name='" . $name . "'";
		$query = "SELECT bt.* FROM " . TB_BOOKMARKS_TYPE . " bt LEFT JOIN " .users2bookmarks. " ub ON bt.id=ub.bookmark_id ";
		$query .=" WHERE bt.name='" . $name . "'";
		if ($user_id != null){
			$query .=" AND ub.user_id = " .$user_id. "";
		}	

		$this->db->query($query);
		if (! $this->db->num_rows() > 0){
			throw new Exception('Permission denied');			
		}	
	
		$subNumber = $this->db->fetch(0)->id;
			
		return $subNumber;
	}   
	
	public function Users2bookmarks($name,$users_id, $bookmark_id = null) {
		
		if ($bookmark_id == null){
					$query = "SELECT id from " . TB_BOOKMARKS_TYPE . " WHERE name = '".$name."'";
					$this->db->query($query);
					$bookmark_id = $this->db->fetch(0)->id;
		}
		$query = "DELETE FROM ".users2bookmarks." WHERE bookmark_id = {$bookmark_id}";

		$this->db->query($query);
					foreach ($users_id as $userid ) {

							$query = "INSERT INTO users2bookmarks (user_id, bookmark_id) VALUES ("
									. $userid . ","
									. $bookmark_id. ")";
							$this->db->query($query);
	
					}		
	}
	
	
					
						
	
        
        /**
         *  This method does ....
         * @param array $arrItemID ???
         * @return Bookmark 
         */
	public function getBookmarksList($user_id = null) {
            
        //$itemCount = $this->getCount();
		//$query = "SELECT * FROM " . TB_BOOKMARKS_TYPE . "";
		$query = "SELECT DISTINCT bt.* FROM " . TB_BOOKMARKS_TYPE . " bt LEFT JOIN " .users2bookmarks. " ub ON bt.id=ub.bookmark_id";
		if ($user_id != null){
			$query .=" WHERE ub.user_id = " .$user_id. "";
		}


		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarks = array();
		foreach($arr as $b) {
			$bookmark = new Bookmark($this->db, $b);
			$bookmarks[] = $bookmark;                        
		}
                       
		return $bookmarks;
	}
	
	/**** GET ORIGIN SUPPLIER***/
	public function getOriginSupplier() {

        $itemCount = $this->getCountSupplier();
		$query = "SELECT * FROM " . TB_SUPPLIER . " WHERE supplier_id=original_id ORDER BY supplier ASC";
                
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarks = array();
		foreach($arr as $b) {
			//$bookmark = new Bookmark($this->db, $b);
			$bookmarks[] = $b; 
			
		}
          // die(var_dump($arr,$bookmarks));            
		return $bookmarks;
	}

	public function getAllSuppliersByOrigin($origin) {

        $itemCount = $this->getCountSupplier();
		$query = "SELECT * FROM " . TB_SUPPLIER . " WHERE original_id='$origin' ORDER BY supplier ASC";
                
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarks = array();
		foreach($arr as $b) {
			//$bookmark = new Bookmark($this->db, $b);
			$bookmarks[] = $b; 
			
		}
          // die(var_dump($arr,$bookmarks));            
		return $bookmarks;
	}
	
	public function getBookmarksListSupplier() {

        $itemCount = $this->getCountSupplier();
		$query = "SELECT * FROM " . TB_SUPPLIER . " ORDER BY supplier ASC";
                
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarks = array();
		foreach($arr as $b) {
			//$bookmark = new Bookmark($this->db, $b);
			$bookmarks[] = $b; 
			
		}
          // die(var_dump($arr,$bookmarks));            
		return $bookmarks;
	}	

	
	
	
	public function getCountSupplier() {
                $query = "SELECT count(*) Num FROM " . TB_SUPPLIER . "";
                $query = mysql_escape_string($query);
                $this->db->query($query);
		$countBookmarks = $this->db->fetch(0)->Num;
                return $countBookmarks;
	}
        
	public function deleteBookmarks(Bookmark $bookmarkList) {
            
		$itemsCount= count($bookmarkList);
                foreach($bookmarkList as $bookmark) {
                        $bookmark->deleteBookmark($bookmark->id);
		}
	}
        
	public function getCount() {
                $query = "SELECT count(*) Num FROM " . TB_BOOKMARKS_TYPE . "";
                $query = mysql_escape_string($query);
                $this->db->query($query);
		$countBookmarks = $this->db->fetch(0)->Num;
                return $countBookmarks;
	}
      
    public function updateType($bookmarksDeleted) {
            foreach($bookmarksDeleted as $b) {
                $query = "UPDATE " . TB_CONTACTS . " SET 
					type = 1
					WHERE type = {$b->id}";
                $this->db->query($query);
            }
            return true;
	}
        
}
?>