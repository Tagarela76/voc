<?php

class Hazardous {
	
	private $db;
	
	private $class;
	private $irr;
	private $ohh;
	private $sens;
	private $oxy_1;

    function Hazardous($db) {
    	$this->db = $db;
    }
    
    //		SETTERS
    
    public function setClass($class) {
    	$this->class = $class;
    }
    
    public function setIrr($irr) {
    	$this->irr = $irr;
    }
    
    public function setOhh($ohh) {
    	$this->ohh = $ohh;
    }
    
    public function setSens($sens) {
    	$this->sens = $sens;
    }
    
    public function setOxy_1($oxy_1) {
    	$this->oxy_1 = $oxy_1;
    }
    
    //		GETTERS
    
    public function getClass() {
    	return $this->class;
    }
    
    public function getIrr() {
    	return $this->irr;
    }
    
    public function getOhh() {
    	return $this->ohh;
    }
    
    public function getSens() {
    	return $this->sens;
    }
    
    public function getOxy_1() {
    	return $this->oxy_1;
    }
    
    
    public function getChemicalClassification($productID,$getRuleDetails = false) {
    	$rule = new Rule($this->db);
    	$productID = $this->db->sqltext($productID);
    	
//		//$this->db->select_db(DB_NAME);
		$query = "SELECT DISTINCT chemical_class_id " .
				"FROM product2chemical_class " .
				"WHERE product_id = ".$productID;
		$this->db->query($query);
		
		$numRows = $this->db->num_rows();
		if ($numRows) {	
			$data = $this->db->fetch_all();						
			for ($i=0; $i < $numRows; $i++) {
				$chemicalClass = $this->getChemicalClassDetails($data[$i]->chemical_class_id);
				$chemicalClass['rules'] = null;				
				
				$query = "SELECT rule_id " .
					"FROM product2chemical_class " .
					"WHERE product_id = ".$productID." " .
							"AND chemical_class_id = ".$chemicalClass['id']."";
				$this->db->query($query);
				$rulesData = $this->db->fetch_all();
				foreach ($rulesData as $ruleData) {
					if ($getRuleDetails) {
						$ruleDetails = $rule->getRuleDetails($ruleData->rule_id);
						$chemicalClass['rules'][] = $ruleDetails;	
					} else {
						$chemicalClass['rules'][] = $ruleData->rule_id;
					}
				}
																 
				$chemicalClasses[] = $chemicalClass; 
			}			
			return $chemicalClasses;
		} else 
			return false;		
	}
	
    public function getChemicalClassesList() {
    	//$this->db->select_db(DB_NAME);
		$query = "SELECT id " .
				"FROM chemical_class";				
		$this->db->query($query);
		
		$numRows = $this->db->num_rows();
		if ($numRows) {						
			$data = $this->db->fetch_all();
			for ($i=0; $i < $numRows; $i++) {
				$chemicalClass = $this->getChemicalClassDetails($data[$i]->id);
				$chemicalClasses[$i] = $chemicalClass;
			}						
			return $chemicalClasses;
		} else 
			return false;
    }
    
    /*	$chemicalClasses[0] = array('id'=>$id, 'rules'=>array(ruleid, ruleid ...))
     * 	$productID = product ID
     * */
    public function setProduct2ChemicalClasses($productID, $chemicalClasses) {
    	//	if nothing set - just delete old links
    	if (is_null($chemicalClasses)) {
    		$this->deleteProduct2ChemicalClassesLink($productID);
    		return true;
    	}
    	
    	//	insert/update links if there are any changes
    	if (!$this->isProduct2ChemicalClassesLink($productID, $chemicalClasses)) {
    		$this->deleteProduct2ChemicalClassesLink($productID);
    		$this->insertProduct2ChemicalClassesLink($productID,$chemicalClasses);
    	}	
    }
    
    public function getChemicalClassDetails($id) {
    	//$this->db->select_db(DB_NAME);
		$query = "SELECT * " .
				"FROM chemical_class " .
				"WHERE id = ".$id;				
		$this->db->query($query);
		
		$numRows = $this->db->num_rows();
		if ($numRows) {										
			$data = $this->db->fetch(0);
			$chemicalClass = array (
				'id'			=> $data->id,
				'name'			=> $data->name,
				'description'	=> $data->description
			);
			return $chemicalClass;	 
		} else 
			return false;
    }
    
    private function isProduct2ChemicalClassesLink($productID, $chemicalClasses) {    	    	
    	//$this->db->select_db(DB_NAME);
		$query = "SELECT chemical_class_id " .
				"FROM product2chemical_class " .
				"WHERE product_id = ".$productID." " .
				"ORDER BY chemical_class_id";							
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$linksData = $this->db->fetch_all();			
			$links = array();			
			for($i=0;$i<count($linksData);$i++) {
				$links[$i] = $linksData[$i]->chemical_class_id;
			}								
			
			sort($chemicalClasses);
			
			return ($links == $chemicalClasses) ? true : false;																				
		} else 
			return false;
    }
    
    public function deleteProduct2ChemicalClassesLink($productID) {
    	//$this->db->select_db(DB_NAME);
		$query = "DELETE FROM product2chemical_class WHERE product_id = ".$productID;											
		$this->db->query($query);
    }
    
    private function insertProduct2ChemicalClassesLink($productID, $chemicalClasses) {    	
    	//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO product2chemical_class (product_id, chemical_class_id, rule_id) VALUES ";
		foreach($chemicalClasses as $chemicalClass) {
			$wasAdded = false;
			foreach ($chemicalClass['rules'] as $ruleID) {
				if ($ruleID != 0) {
					$query .= "(".$productID.", ".$chemicalClass['id'].", ".$ruleID."), ";
					$wasAdded = true;	
				}
			}
			if (!$wasAdded) {
				$query .= "(".$productID.", ".$chemicalClass['id'].", NULL), ";
			}		 
		}
		$query = substr($query, 0, -2);															
		$this->db->query($query);
    }
}
?>