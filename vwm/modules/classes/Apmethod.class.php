<?php

class Apmethod {
	
	private $db;
	
	function Apmethod($db) {
		$this->db=$db;
	}
	
	
	public function getApmethodList($pagination) {
		$query = "SELECT * FROM ".TB_APMETHOD." ORDER BY apmethod_desc";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$apmethod=array (
					'apmethod_id'			=>	$data->apmethod_id,
					'description'			=>	$data->apmethod_desc
				);
				$apmethods[]=$apmethod;
			}
		}
		
		return $apmethods;
	}
	
	public function getApmethodDetails($apmethodID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_APMETHOD." WHERE apmethod_id=".$apmethodID);
		$apmethodDetails=$this->db->fetch_array(0);
		/*$apmethodDetails=array(
			'apmethod_id'	=>	$data->apmethod_id,
			'apmethod_desc'	=>	$data->apmethod_desc
		);*/
		
		
		return $apmethodDetails;
	}
	
	public function getDefaultApmethodlist($companyID) {
		//$this->db->select_db(DB_NAME);
		$query ="SELECT * FROM ".TB_DEFAULT." d WHERE d.id_of_object=".(int)$companyID;
		$query.=" AND subject='apmethod'";
		$this->db->query($query);
		
		
		if ($this->db->num_rows()) {
			for ($j=0; $j < $this->db->num_rows(); $j++) {
				$data=$this->db->fetch($j);			
				$apmethod[$j]=$data->id_of_subject;					
			}
		}
		
		return $apmethod;
	}
	
	public function getDefaultApmethodDescriptions($companyID) {
		//$this->db->select_db(DB_NAME);
		$query ="SELECT apm.apmethod_id, apm.apmethod_desc"; 
		$query.=" FROM ".TB_DEFAULT." def, ".TB_APMETHOD." apm WHERE def.id_of_object=".(int)$companyID;
		$query.= " AND apm.apmethod_id=def.id_of_subject";
		$query.=" AND def.subject='apmethod'";
		$this->db->query($query);
		
		
		if ($this->db->num_rows()) {
			for ($j=0; $j < $this->db->num_rows(); $j++) {
				$data=$this->db->fetch($j);				
				$apmethod=array (
					'apmethod_id'			=>	$data->apmethod_id,
					'description'			=>	$data->apmethod_desc
				);	
				$apmethods[]=$apmethod;				
			}
		}
		
		return $apmethods;
		
	}
	
	public function setDefaultAPMethodlist($apmethodID, $categoryName, $companyID) {
		
		$this->deleteDefaultApmethod($companyID);
				
		$query = "SELECT ".TB_APMETHOD.".apmethod_id FROM ".TB_APMETHOD." WHERE ".TB_APMETHOD.".apmethod_id IN ".
					"(SELECT DISTINCT apmethod_id  FROM ".TB_USAGE." WHERE ".TB_USAGE.".department_id IN ".
    						"(SELECT department_id FROM ".TB_DEPARTMENT." WHERE ".TB_DEPARTMENT.".facility_id IN ".
     							"(SELECT facility_id FROM ".TB_FACILITY." WHERE ".TB_FACILITY.".company_id='".$companyID."')))";
     	
     	
     	$this->db->query($query);
     	
     	// select AP Methods for which has already created products
     	if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$apmethod[$i]=$data->apmethod_id;					
			}
		}
		
		// insert unit types that exist but are not marked
		$i = 0;
		$j = 0;
		$flag = 0;
		while ($apmethod[$j]) {
			while ($apmethodID[$i]) {
				if ($apmethod[$j] == $apmethodID[$i]) {
					$flag = 1;
				}
				$i++;
			}
			if ($flag == 0) {
				$this->insertDefaultApmethod('apmethod', $apmethod[$j], $categoryName, $companyID);
			}
			$i = 0;
			$j++;
			$flag = 0;
		}
		
		// insert marked unit types
		$i = 0;
		while ($apmethodID[$i]) {
			$this->insertDefaultApmethod('apmethod', $apmethodID[$i], $categoryName, $companyID);
			$i++;
		}
	}
	
	private function deleteDefaultApmethod($companyID) {
		
		$query = "DELETE FROM ".TB_DEFAULT." WHERE `id_of_object` = '".$companyID."'" ;
		$query.= " AND subject='apmethod'"; 
		$this->db->query($query);
	}
	
	private function insertDefaultApmethod($APMethodName, $APMethodID, $companyName, $companyID) {
		//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO ".TB_DEFAULT." (subject, id_of_subject, object, id_of_object) " .
				 "VALUES ('".$APMethodName."', ".(int)$APMethodID.", '".$companyName."', ".(int)$companyID.")";
		$this->db->query($query);		
	} 
	
	
	
	public function setApmethodDetails($apmethodDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_APMETHOD." SET ";
		
		$query.="apmethod_desc='".$apmethodDetails['apmethod_desc']."'";
		
		$query.=" WHERE apmethod_id=".$apmethodDetails['apmethod_id'];
		
		$this->db->query($query);
	}
	
	public function addNewApmethod($apmethodData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_APMETHOD." (apmethod_desc) VALUES (";
		
		$query.="'".$apmethodData["apmethod_desc"]."'";
		
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteApmethod($apmethodID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_APMETHOD." WHERE apmethod_id=".$apmethodID);
	}
	
	
	public function queryTotalCount() {
		$query = "SELECT COUNT(*) cnt FROM ".TB_APMETHOD."";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
}
?>