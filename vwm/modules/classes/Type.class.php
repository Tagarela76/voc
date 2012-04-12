<?php

class Type {
	
	private $db;
	
	function Type($db) {
		$this->db=$db;
	}
	
	public function getTypeList() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_TYPE." ORDER BY type_desc");
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$type=array (
					'type_id'			=>	$data->type_id,
					'description'			=>	$data->type_desc
				);
				$types[]=$type;
			}
		}
		
		return $types;
	}
	
	
	public function getTypeDetails($typeID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_TYPE." WHERE type_id=".$typeID);
		$typeDetails=$this->db->fetch_array(0);
		/*$typeDetails=array(
			'type_id'	=>	$data->type_id,
			'type_desc'	=>	$data->type_desc
		);*/
		
		
		return $typeDetails;
	}
	
	public function setTypeDetails($typeDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_TYPE." SET ";
		
		$query.="type_desc='".$typeDetails['description']."'";
		
		$query.=" WHERE type_id=".$typeDetails['type_id'];
		
		$this->db->query($query);
	}
	
	public function addNewType($typeData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_TYPE." (type_desc) VALUES (";
		
		$query.="'".$typeData["description"]."'";
		
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteType($typeID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_TYPE." WHERE type_id=".$typeID);
	}
}
?>