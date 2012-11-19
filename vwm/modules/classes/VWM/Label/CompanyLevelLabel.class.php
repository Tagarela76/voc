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
    
}

?>
