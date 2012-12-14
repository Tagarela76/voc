<?php

namespace VWM\Label;

class CompanyLevelLabel {
	
	/**	 
	 * @var \db
	 */
	protected $db;

	const LABEL_ID_REPAIR_ORDER = 'repair_order';
    
    const LABEL_ID_PRODUCT_NAME = 'product_name';
    
    const LABEL_ID_ADD_JOB = 'add_job';
    
    const LABEL_ID_DESCRIPTION = 'description';

    const LABEL_ID_R_O_DESCRIPTION = 'r_o_description';
    
    const LABEL_ID_CONTACT = 'contact';
    
    const LABEL_ID_R_O_VIN_NUMBER = 'r_o_vin_number';
    
    const LABEL_ID_VOC = 'voc';

    const LABEL_ID_CREATION_DATE = 'creation_date';
    
    const LABEL_ID_UNIT_TYPE = 'unit_type';
    
    const LABEL_ID_PAINT_SHOP_PRODUCT = 'paint_shop_product';
    
    const LABEL_ID_BODY_SHOP_PRODUCT = 'body_shop_product';
    
    const LABEL_ID_DETAILING_SHOP_PRODUCT = 'detailing_shop_product';
    
    const LABEL_ID_FUEL_AND_OIL_PRODUCT = 'fuel_and_oils_product';
	
	const LABEL_ID_SPENT_TIME = 'spent_time';
    
    const LABEL_ID_POWDER_COATING = "powder_coating";


	public function __construct(\db $db) {
		$this->db = $db;
	}
    
    public function getRepairOrderLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_REPAIR_ORDER . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getAddJobLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_ADD_JOB . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getProductNameLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_PRODUCT_NAME . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getDescriptionLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_DESCRIPTION . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getRODescriptionLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_R_O_DESCRIPTION . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getContactLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_CONTACT . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getROVinNumberLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_R_O_VIN_NUMBER . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getVocLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_VOC . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getCreationDateLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_CREATION_DATE . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getUnitTypeLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_UNIT_TYPE . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	
	}
    
    public function getPaintShopProductLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_PAINT_SHOP_PRODUCT . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	}
    
    public function getBodyShopProductLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_BODY_SHOP_PRODUCT . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	}
    
    public function getDetailingShopProductLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_DETAILING_SHOP_PRODUCT . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	}
    
    public function getFuelAndOilProductLabel() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_FUEL_AND_OIL_PRODUCT . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	}
    
    public function getPowderCoating() {
		
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_POWDER_COATING . "' " . 
			   "LIMIT 1"	;		
 		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->fetch(0);
		}
	}

    public function getDefaultLabels() {
        
        return array(
            self::LABEL_ID_REPAIR_ORDER => $this->getRepairOrderLabel()->default_label_text,
            self::LABEL_ID_PRODUCT_NAME => $this->getProductNameLabel()->default_label_text,
            self::LABEL_ID_ADD_JOB => $this->getAddJobLabel()->default_label_text,
            self::LABEL_ID_DESCRIPTION => $this->getDescriptionLabel()->default_label_text,
            self::LABEL_ID_R_O_DESCRIPTION => $this->getRODescriptionLabel()->default_label_text,
            self::LABEL_ID_CONTACT => $this->getContactLabel()->default_label_text,
            self::LABEL_ID_R_O_VIN_NUMBER => $this->getROVinNumberLabel()->default_label_text,
            self::LABEL_ID_VOC => $this->getVocLabel()->default_label_text,
            self::LABEL_ID_CREATION_DATE => $this->getCreationDateLabel()->default_label_text,
            self::LABEL_ID_UNIT_TYPE => $this->getUnitTypeLabel()->default_label_text,
            self::LABEL_ID_PAINT_SHOP_PRODUCT => $this->getPaintShopProductLabel()->default_label_text,
            self::LABEL_ID_BODY_SHOP_PRODUCT => $this->getBodyShopProductLabel()->default_label_text,
            self::LABEL_ID_DETAILING_SHOP_PRODUCT => $this->getDetailingShopProductLabel()->default_label_text,
            self::LABEL_ID_FUEL_AND_OIL_PRODUCT => $this->getFuelAndOilProductLabel()->default_label_text,
			self::LABEL_ID_SPENT_TIME => $this->getSpentTimeLabel()->default_label_text,
            self::LABEL_ID_POWDER_COATING => $this->getPowderCoating()->default_label_text,
        );
    }
    
    public function getDefaultLabelName($labelId) {
        
        $labels = $this->getDefaultLabels();
        foreach ($labels as $id => $label) {
            if($labelId == $id) {
                return $label;
            }
        }
    }
	
	public function getSpentTimeLabel(){
		$sql = "SELECT * FROM " . TB_COMPANY_LEVEL_LABEL . " " . 
			   "WHERE label_id='" . self::LABEL_ID_SPENT_TIME . "' " . 
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
