<?php

class RegAct {
	private $db;
	public $rin;
	
	public $title;
	public $stage;
	public $significant;
	
	public $date_received;
	public $date_completed;
	public $decision;
	public $legal_deadline;
	
	public $category;
		
	public $reg_agency_id;
	public $reg_agency;
	
	//if we get reg act for User
	public $user_id;
	public $readed;
	public $mailed;
	
	//additional
	private $tableMap = array(
			'rin', 'reg_agency_id', 'title', 'stage', 'significant', 'date_received', 'legal_deadline', 'date_completed', 'category', 'decision' 
		);

    function __construct($db, $rin = null) {
    	$this->db = $db;
    	if (!is_null($rin)) {
    		$this->rin = $rin;
    		$this->_load();
    	}
    }
        
    public function save() {
    	if (!$this->_validate()) {
    		return false;
    	}
    	$new = true;
    	$this->db->query("SELECT rin FROM ".TB_REG_ACTS." WHERE rin = '$this->rin' LIMIT 1");
    	if ($this->db->num_rows() > 0) {
    		$new = false;
    	}
    	if (!$new) {
    		$query = "UPDATE ".TB_REG_ACTS." SET ";
    		foreach ($this->tableMap as $property) {
    			if (property_exists($this, $property)) {
    				$query .= " $property = '".$this->$property."', ";
    			}
    		}
    		$query = substr($query,0,-2);
    		$query .= "WHERE rin = '$this->rin' ";
    	} else {
    		$query = "INSERT INTO ".TB_REG_ACTS." " .
    					"(".implode(', ', $this->tableMap).") " .
    				" VALUES (" ;
    		foreach ($this->tableMap as $property) {
    			if (property_exists($this,$property) && !is_null($this->$property)) {
	    			$query .= "'".$this->$property."', ";
    			} else {
	    			$query .= " NULL, "; // we can get here only on date_complete in review regAct, but if it'll be more fields in db it should be))
	    		}
    		}
    		$query = substr($query, 0, -2);
    		$query .= ")";
    	}
    	$this->db->query($query);//var_dump($query);
    }
    
    public function delete() {
    	if (!is_null($this->rin)) {
    		$query = "DELETE FROM ".TB_REG_ACTS." WHERE rin = '$this->rin'";
    		$this->db->query($query);
    	}
    }
    
    private function _load() {
    	if (!is_null($this->rin)) {
    		$query = "SELECT * FROM ".TB_REG_ACTS." WHERE rin = '$this->rin' LIMIT 1 ";
    		$this->db->query($query);
    		$data = $this->db->fetch(0);
    		foreach ($data as $property => $value) {
    			if(property_exists($this,$property)) {
    				$this->$property = $value;
    			}
    		}
    		$this->reg_agency = new RegAgency($this->db,$this->reg_agency_id);
    	}
    }
    
    public function _validate() {
    	//TODO add here validation!
    	
    	return true;
    }
}
?>