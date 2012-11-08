<?php

namespace VWM\ManageColumns;

class BrowseCategoryEntity {
	
	function __construct(\db $db) {
		$this->db = $db;
	}
	
	const BROWSE_CATEGORY_MIX = "Mix Browse Category Columns";
	
	public function getBrowseCategoryMix() {
		
		$sql = "SELECT * FROM " . TB_BROWSE_CATEGORY_ENTITY . " " . 
			   "WHERE name='" . self::BROWSE_CATEGORY_MIX . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
	
}

?>
