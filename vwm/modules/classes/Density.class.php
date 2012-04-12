<?php

class Density {
	
	private $db;
	
	private $id;
	private $numerator;		//	unittype id
	private $denominator; 	//	unittype id
			



    function Density($db, $id = null) {
    	$this->db = $db;
    	if (!is_null($id)) {
    		$this->id = $id;
    		$this->_load();
    	}
    }
    
    
    
    public function setID($id) {
    	$this->id = $id;
    }
    public function setNumerator($numerator) {
    	$this->numerator = $numerator;
    }
    public function setDenominator($denominator) {
    	$this->denominator = $denominator;
    }
    
    public function getID() {
    	return $this->id;
    }
    public function getNumerator() {
    	return $this->numerator;
    }
    public function getDenominator() {
    	return $this->denominator;
    }
    
    
    
    
    public function save() {    	
    }
    
    
    
    
    public function delete() {    	
    }
    
    
    
    
    private function _load() {
    	$query = "SELECT * FROM ".TB_DENSITY." WHERE id = ".$this->id;
    	$this->db->query($query);
    	
    	if ($this->db->num_rows() > 0) {
    		$data = $this->db->fetch_all();
    		foreach ($data as $row) {
    			$this->setNumerator($row->numerator);
    			$this->setDenominator($row->denominator);
    		}
    	}    	
    }
    
    public function getAllDensity($cUnitType) {
    	
    	$query = "SELECT * FROM ".TB_DENSITY."";
    	$this->db->query($query);
    	$num_rows = $this->db->num_rows();
    	
    	if ($num_rows) {
			for ($i=0; $i < $num_rows; $i++) {
				$data=$this->db->fetch($i);
				$density=array (
					'id'					=>	$data->id,
					'numeratorID'			=>	$data->numerator,
					'denominatorID'			=>  $data->denominator,
					'numerator'				=>  '',
					'denominator'			=>  ''
				);
				$densities[] = $density;
			}
		}
		
		$j = 0;
		while ($densities[$j]) {
			$unittypeData = $cUnitType->getUnittypeDetails($densities[$j]['numeratorID']);
			$densities[$j]['numerator'] = $unittypeData['name']; 
			$unittypeData = $cUnitType->getUnittypeDetails($densities[$j]['denominatorID']);
			$densities[$j]['denominator'] = $unittypeData['name']; 
			$j++;
		}
		
		return $densities;
    }
}
?>