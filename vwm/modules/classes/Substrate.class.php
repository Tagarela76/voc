<?php

class Substrate {
	
	private $db;
	
	function Substrate($db) {
		$this->db=$db;
	}
	
	public function getSubstrateList(Pagination $pagination = null) {
		$query = "SELECT * FROM ".TB_SUBSTRATE." ORDER BY substrate_desc";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$substrate=array (
					'substrate_id'			=>	$data->substrate_id,
					'substrate_desc'			=>	$data->substrate_desc
				);
				$substrates[]=$substrate;
			}
		}
		
		return $substrates;
	}
	
	
	public function getSubstrateDetails($substrateID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_SUBSTRATE." WHERE substrate_id=".$substrateID);
		$substrateDetails=$this->db->fetch_array(0);
		/*$substrateDetails=array(
			'substrate_id'	=>	$data->substrate_id,
			'substrate_desc'	=>	$data->substrate_desc
		);*/
		
		
		return $substrateDetails;
	}
	
	public function setSubstrateDetails($substrateDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_SUBSTRATE." SET ";
		
		$query.="substrate_desc='".$substrateDetails['substrate_desc']."'";
		
		$query.=" WHERE substrate_id=".$substrateDetails['substrate_id'];
		
		$this->db->query($query);
	}
	
	public function addNewSubstrate($substrateData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_SUBSTRATE." (substrate_desc) VALUES (";
		
		$query.="'".$substrateData["description"]."'";
		
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteSubstrate($substrateID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_SUBSTRATE." WHERE substrate_id=".$substrateID);
	}
	
	
	public function queryTotalCount() {
		$query = "SELECT COUNT(*) cnt FROM ".TB_SUBSTRATE."";
		$this->db->query($query);
		return $this->db->fetch(0)->cnt;
	}
}
?>