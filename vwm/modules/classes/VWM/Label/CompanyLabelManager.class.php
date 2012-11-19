<?php

namespace VWM\Label;

class CompanyLabelManager {
	
	/**	 
	 * @var \db
	 */
	protected $db;
	
    /**
	 *
	 * @var int
	 */
	protected $id;
	/**	 
	 * @var int
	 */
	
    /**
     *
     * @var int
     */
    protected $company_level_label_id;

    /**
     *
     * @var string
     */
    protected $label_text;
    
    /**
     *
     * @var int
     */
    protected $industry_type_id;
    
    /**
	 *
	 * @var datetime
	 */
	protected $last_update_time;
	
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCompanyLevelLabelId() {
        return $this->company_level_label_id;
    }

    public function setCompanyLevelLabelId($companyLevelLabelId) {
        $this->company_level_label_id = $companyLevelLabelId;
    }

    public function getLabelText() {
        return $this->label_text;
    }

    public function setLabelText($labelText) {
        $this->label_text = $labelText;
    }

    public function getIndustryTypeId() {
        return $this->industry_type_id;
    }

    public function setIndustryTypeId($industryTypeId) {
        $this->industry_type_id = $industryTypeId;
    }

    public function getLastUpdateTime() {
        return $this->last_update_time;
    }

    public function setLastUpdateTime($lastUpdateTime) {
        $this->last_update_time = $lastUpdateTime;
    }
    
	public function __construct(\db $db, $industryTypeId = null) {
		$this->db = $db;
		if (!is_null($industryTypeId)) {
			$this->setIndustryTypeId($industryTypeId);
		}		
	}
	
    public function save() {	
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
    
	/**
	 * Insert
	 * @return boolean
	 */
	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "INSERT INTO ".TB_INDUSTRY_TYPE2LABEL." (" .
				"company_level_label_id, label_text, industry_type_id, last_update_time" .
				") VALUES ( ".
				"{$this->db->sqltext($this->getCompanyLevelLabelId())}, " .
				"'{$this->db->sqltext($this->getLabelText())}', " .
				"{$this->db->sqltext($this->getIndustryTypeId())}, " .
				"{$lastUpdateTime} " .
				")"; 
		$response = $this->db->exec($sql);
		if($response) {
			$this->setId($this->db->getLastInsertedID());	
			return $this->getId();
		} else {
			return false;
		}			
	}
	
	/**
	 * Update 
	 * @return boolean
	 */
	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".TB_INDUSTRY_TYPE2LABEL." SET " .
				"company_level_label_id={$this->db->sqltext($this->getCompanyLevelLabelId())}, " .
				"label_text='{$this->db->sqltext($this->getLabelText())}', " .
				"industry_type_id={$this->db->sqltext($this->getIndustryTypeId())}, " .			
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";	
	
		$response = $this->db->exec($sql);
		if($response) {			
			return $this->getId();
		} else {
			return false;
		}
	}		
	
	/**
	 * 
	 * @param string $companyLevelLabelId
	 * @return boolean|\VWM\Label\CompanyLabelManager
	 */
	public function getLabel($companyLevelLabelId) {
		
		$sql = "SELECT itl.* FROM " . TB_INDUSTRY_TYPE2LABEL . " itl " .
			   "JOIN " . TB_COMPANY_LEVEL_LABEL . " cll " . 
			   "ON itl.company_level_label_id= cll.id " .
			   "WHERE cll.label_id='{$this->db->sqltext($companyLevelLabelId)}' " .
			   "AND itl.industry_type_id={$this->db->sqltext($this->getIndustryTypeId())}";  
 		$this->db->query($sql);
		$row = $this->db->fetch(0); 
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$companyLabelManager = new CompanyLabelManager($this->db);
        $companyLabelManager->setId($row->id);
        $companyLabelManager->setCompanyLevelLabelId($row->company_level_label_id);
        $companyLabelManager->setLabelText($row->label_text);
        $companyLabelManager->setIndustryTypeId($row->industry_type_id);
		
		return $companyLabelManager;
	}
    
        
    public function validate($data) {
        $errors = array();
        $errors["validateStatus"] = "success";
        if ($data["repair_order"] == "") {
            $errors["repairOrderError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["product_name"] == "") {
            $errors["productNameLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["add_job"] == "") {
            $errors["addJobLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["description"] == "") {
            $errors["descriptionLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["r_o_description"] == "") {
            $errors["roDescriptionLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["contact"] == "") {
            $errors["contactLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["r_o_vin_number"] == "") {
            $errors["roVinNumberLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["voc"] == "") {
            $errors["vocLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["creation_date"] == "") {
            $errors["creationDateLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        if ($data["unit_type"] == "") {
            $errors["unitTypeLabelError"] = 'true';
            $errors["validateStatus"] = "false";
        }
        return $errors;
    }
    
    public function getDefaultLabelByLabelText($labelText) {
		
		$sql = "SELECT cll.default_label_text FROM " . TB_INDUSTRY_TYPE2LABEL . " itl " .
			   "JOIN " . TB_COMPANY_LEVEL_LABEL . " cll " . 
			   "ON itl.company_level_label_id= cll.id " .
			   "WHERE itl.label_text='{$this->db->sqltext($labelText)}' " .
			   "AND itl.industry_type_id={$this->db->sqltext($this->getIndustryTypeId())}"; 
 		$this->db->query($sql); 
		$row = $this->db->fetch(0); 
		if ($this->db->num_rows() == 0) {
			return $labelText;
		}
		return $row->default_label_text;
	}
}

?>
