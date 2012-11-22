<?php

namespace VWM\ManageColumns;

class BrowseCategoryEntity {
	
	function __construct(\db $db) {
		$this->db = $db;
	}
	
	const BROWSE_CATEGORY_MIX = "browse_category_mix";
	
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
    
    public function getBrowseCategory() {
        
        return array(
            self::BROWSE_CATEGORY_MIX => $this->getBrowseCategoryMix()->default_value,
        );
    }
    
    public function getDefaultBrowseCategoryValue($browseCategoryEntityName) {
        
        $browseCategoryEntities = $this->getBrowseCategory();
        foreach ($browseCategoryEntities as $id => $browseCategory) {
            if($browseCategoryEntityName == $id) {
                return $browseCategory;
            }
        }
    }
	
}

?>
