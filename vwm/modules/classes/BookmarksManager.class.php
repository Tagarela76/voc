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
        
        
        
        /**
         *  This method does ....
         * @param array $arrItemID ???
         * @return Bookmark 
         */
	public function getBookmarksList() {
            
                $itemCount = $this->getCount();
		$query = "SELECT * FROM " . TB_BOOKMARKS_TYPE . "";
                
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