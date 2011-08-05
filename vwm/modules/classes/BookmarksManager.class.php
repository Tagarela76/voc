<?php
class BookmarksManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getBookmark($bookmarkName) {
		$query = "SELECT * from " . TB_CONTACTS_TYPE . " WHERE name = '".$bookmarkName."'";
		
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$bookmarksArr = $arr[0];
		
		$bookmark = new Bookmark($this->db, $bookmarksArr);
		return $bookmark;
	}        
        
	public function getBookmarksList($arr_itemID) {
            
                $itemCount = count($arr_itemID);
		$query = "SELECT * FROM " . TB_CONTACTS_TYPE . "";
                //echo "$arr_itemID = ", $arr_itemID[0];
		if(isset($arr_itemID)) {
			$query .= " WHERE id='".$arr_itemID[0]."'";
                        if($itemCount>1){
                                for ($i = 1; $i < $itemCount; $i++) {
                                        $query .= " OR id='".$arr_itemID[i]."'";
                                }
                        }                        
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
        
	public function deleteBookmarks(Bookmark $bookmarkList) {
            
		$itemsCount= count($bookmarkList);
                foreach($bookmarkList as $bookmark) {
                        $bookmark->deleteBookmark($bookmark->get_id());
		}
	}
        
	public function getCount() {
                $query = "SELECT count(*) Num FROM " . TB_CONTACTS_TYPE . "";
                $this->db->query($query);
		$countBookmarks = $this->db->fetch(0)->Num;
                return $countBookmarks;
	}

}

?>