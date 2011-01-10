<?php

class GCG {
	private $db;
	//	Settings
	private $prefix = "GC";	// In future will be stored in VOC-settings table
	private $numberLengthLimit = 6;
	private $revNumberLengthLimit = 2;

    function GCG($db) {
    	$this->db = $db;
    	//Getting Prefix
    }
    
    public function create() {
    	//$this->db->select_db(DB_NAME);
    	
    	//	Year generation
    	$year = date("y");
    	
    	//	Number generation
    	$query = "SELECT MAX(number) FROM ".TB_GCG." WHERE 1";
    	$this->db->query($query);
    	
    	$maxNumber = $this->db->fetch_array();
    	$number = $maxNumber["MAX(number)"];
    	
    	if ($number == null) {
    		$number = 1;
    	} else {
    		$number++;
    	}
    	
    	//	Revision number generation
    	$rev_number = 0;
    	
		//	Insert GCG record into DB
		$query = "INSERT INTO ".TB_GCG." (year, number, rev_number) VALUES ("
										.$year
										.", ".$number
										.", ".$rev_number.")";
		
		$this->db->query($query);
		
		//	Return GCG ID
		$query = "SELECT gcg_id FROM ".TB_GCG." WHERE number=".$number;
		$this->db->query($query);
		
		return $this->db->fetch(0)->gcg_id;
    }
    
    public function getByID($gcgID) {
    	//$this->db->select_db(DB_NAME);
    	
    	// Get data
    	$query = "SELECT year, number, rev_number FROM ".TB_GCG." WHERE gcg_id=".$gcgID." LIMIT 1";
    	$this->db->query($query);
    	
    	$gcgData = $this->db->fetch(0);
    	
    	if ($gcgData != null) {
    		$year = $gcgData->year;
    		$number = $gcgData->number;
    		$revNumber = $gcgData->rev_number;
    	}
    	
    	//	Ouput formatting
    	//	example: GC09-000001-R00
    	//	Add Prefix
    	$gcgNumber = $this->prefix;
    	
    	//	Add Year
    	$gcgNumber .= $this->yearToStr($year);
    	
    	//	Add Number
    	$gcgNumber .= "-".$this->numberToStr($number);
    	
    	//	Add Revision Number
    	$gcgNumber .= "-R".$this->revNumberToStr($revNumber);
    	
    	return $gcgNumber;
    }
    
    private function yearToStr($year) {
    	$year = trim($year);
    	
    	if (strlen($year) == 1) {
    		$year = "0".$year;
    	}
    	
    	return $year;
    }
    
    private function numberToStr($number) {
    	$number = trim($number);
    	
    	if (strlen($number) < $this->numberLengthLimit) {
    		while (strlen($number) < $this->numberLengthLimit) {
    			$number = "0".$number;
    		}
    	}
    	
    	return $number;
    }
    
    private function revNumberToStr($revNumber) {
    	$revNumber = trim($revNumber);
    	
    	if (strlen($revNumber) < $this->revNumberLengthLimit) {
    		while (strlen($revNumber) < $this->revNumberLengthLimit) {
    			$revNumber = "0".$revNumber;
    		}
    	}
    
    	return $revNumber;	
    }
}
?>