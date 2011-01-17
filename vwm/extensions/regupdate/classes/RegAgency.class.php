<?php

class RegAgency {
	private $db;
	public $id;
	
	public $code;
	public $name;
	public $acronym;
	
	public $track = 1;

    function __construct($db, $id = null) {
    	$this->db = $db;
    	if (!is_null($id)) {
    		$this->id = $id;
    		$this->_load();
    	}
    }
    
    public function getAgencyIdByCode($code) {
    	$query = "SELECT id FROM ".TB_REG_AGENCY." WHERE code = '$code' LIMIT 1";
    	$this->db->query($query);//var_dump($query);
    	return ($this->db->num_rows()>0)?$this->db->fetch(0)->id:false;
    }
    
    public function loadAgencyFromXML($agencyXML = 'http://localhost/voc_src/vwm/regAgency.xml') {
    	$xmlDOM = new DOMDocument();
	    $xmlDOM->preserveWhiteSpace = false;
	    $xmlDOM->formatOutput = true;
	    $xmlDOM->load($agencyXML);
	    $XMLagenciesTag = $xmlDOM->getElementsByTagName('AGENCIES')->item(0);
	    $XMLagencies = $XMLagenciesTag->getElementsByTagName('AGENCY');
	    $agencyCount = $XMLagencies->length;
	    $code_array = array();
	    for ($i = 0; $i < $agencyCount; $i++) {
	    	$XMLagency = $XMLagencies->item($i);
		    $this->code = $XMLagency->getElementsByTagName('AGENCY_CODE')->item(0)->nodeValue;
		    $this->name = $XMLagency->getElementsByTagName('NAME')->item(0)->nodeValue;
		    $this->acronym = $XMLagency->getElementsByTagName('ACRONYM')->item(0)->nodeValue;
		    $this->track = 1;
		    $this->save();
		    $code_array []= $this->code;
	    }
	    //now lets off tracking of agencies not in xml!
	    $query = "UPDATE ".TB_REG_AGENCY." SET track = 0 WHERE code NOT IN (".implode(', ',$code_array).") ";
	    $this->db->query($query);
    }
    
    public function save() {
    	$new = true;
    	$this->db->query("SELECT id FROM ".TB_REG_AGENCY." WHERE code = '$this->code' LIMIT 1");
    	if ($this->db->num_rows() > 0) {
    		$new = false;
    		$this->id = $this->db->fetch(0)->id;
    	}
    	if (!$new) {
    		$query = "UPDATE ".TB_REG_AGENCY." SET " .
    					"code = '$this->code', " .
    					"name = '".mysql_escape_string($this->name)."', " .
    					"acronym = '$this->acronym', " .
    					"track = '$this->track' " .
    				" WHERE id = '$this->id' ";
    	} else {
    		$query = "INSERT INTO ".TB_REG_AGENCY." " .
    					"(code, name, acronym, track) " .
    				" VALUES " .
    					"('$this->code', '$this->name', '$this->acronym', '$this->track')";
    	}
    	$this->db->query($query);//var_dump($query);
    }
    
    public function delete() {
    	if (!is_null($this->id)) {
    		$query = "DELETE FROM ".TB_REG_AGENCY." WHERE id = '$this->id'";
    		$this->db->query($query);
    	}
    }
    
    private function _load() {
    	if (!is_null($this->id)) {
    		$query = "SELECT * FROM ".TB_REG_AGENCY." WHERE id = '$this->id' LIMIT 1 ";
    		$this->db->query($query);
    		$data = $this->db->fetch(0);
    		foreach ($data as $property => $value) {
    			if(property_exists($this,$property)) {
    				$this->$property = $value;
    			}
    		}
    	}
    }
}
?>