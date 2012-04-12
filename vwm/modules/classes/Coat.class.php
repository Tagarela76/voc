<?php

class Coat {
	
	private $db;
	
	function Coat($db) {
		$this->db=$db;
	}
	
	public function getCoatList(Pagination $pagination = null,$filter=' TRUE ', $sort=' ORDER BY coat_desc ') {
		$query = "SELECT * FROM ".TB_COAT." WHERE $filter $sort ";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()." ";
		}		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$coat=array (
					'coat_id'			=>	$data->coat_id,
					'description'			=>	$data->coat_desc
				);
				$coats[]=$coat;
			}
		}
		
		return $coats;
	}
	
	public function getCoatDetails($coatID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_COAT." WHERE coat_id=".$coatID);
		$coatDetails=$this->db->fetch_array(0);
		/*$coatDetails=array(
			'coat_id'	=>	$data->coat_id,
			'coat_desc'	=>	$data->coat_desc
		);*/
		
		return $coatDetails;
	}
	
	public function setCoatDetails($coatDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_COAT." SET ";
		
		$query.="coat_desc='".$coatDetails['coat_desc']."'";
		
		$query.=" WHERE coat_id=".$coatDetails['coat_id'];
		
		$this->db->query($query);
	}
	
	public function addNewCoat($coatData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_COAT." (coat_desc) VALUES (";
		
		$query.="'".$coatData["coat_desc"]."'";
		
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteCoat($coatID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_COAT." WHERE coat_id=".$coatID);
	}
	
	
	public function queryTotalCount($filter=" TRUE ") {
		$query = "SELECT COUNT(*) cnt FROM ".TB_COAT." WHERE $filter";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
	
	public function getCoatArrayListedById(){
		$query = "SELECT * FROM ".TB_COAT;
		$this->db->query($query);
		$data = $this->db->fetch_all();
		$coatList = array();
		foreach($data as $coat) {
			$coatList [$coat->coat_id] = $coat->coat_desc;
		}
		return $coatList;
	}
}
?>