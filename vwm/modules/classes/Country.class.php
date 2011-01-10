<?php

class Country {
	
	private $db;
	
	function Country($db) {
		$this->db=$db;
	}
	
	
	public function getCountryList(Pagination $pagination = null,$filter=" TRUE ", $sort=' ORDER BY name ') {
		$query = "SELECT * FROM ".TB_COUNTRY." WHERE $filter $sort";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$country=array (
					'country_id'			=>	$data->country_id,
					'name'			=>	$data->name,
					'date_type' => $data->date_type
				);
				$countrys[]=$country;
			}
		}
		
		return $countrys;
	}
	
	
	public function getCountryDetails($countryID) {
		echo "getCountryDetails";
		//$this->db->select_db(DB_NAME);
		$this->AddDatetypeColumn();
		$this->db->query("SELECT country_id,name as 'country_name',date_type FROM ".TB_COUNTRY." WHERE country_id = ".$countryID);
		$countryDetails=$this->db->fetch_array(0);
		/*$countryDetails=array(
			'country_id'	=>	$data->country_id,
			'country_name'	=>	$data->name,
			'date_type' 	=> $data->date_type
		);*/
		
		return $countryDetails;
	}
	
	public function getCountryIDByName($name) {
		$query="SELECT country_id FROM ".TB_COUNTRY." WHERE name='".$name."'";
		$this->db->query($query);
		$data=$this->db->fetch(0);
		return $data->country_id;
	}
	
	public function setCountryDetails($countryDetails){
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_COUNTRY." SET ";
		
		$query.="name='".$countryDetails['country_name']."', ";
		$query.="date_type='".$countryDetails['date_type']."'";
		$query.=" WHERE country_id=".$countryDetails['country_id'];
		
		$this->db->query($query);
		
		
		$query="DELETE FROM ".TB_STATE." WHERE country_id=".$countryDetails['country_id'];
		$this->db->query($query);
		
		for ($i=0;$i<count($countryDetails['states']);$i++) {
			$query="INSERT INTO ".TB_STATE." (name, country_id) VALUES (";
			
			$query.="'".$countryDetails['states'][$i]["name"]."', ";
			$query.="'".$countryDetails["country_id"]."'";
			
			$query.=')';
			
			$this->db->query($query);
		}
	}
	
	public function addNewCountry($countryData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_COUNTRY." (name, date_type) VALUES (";
		
		$query.="'".$countryData["name"]."', ";
		$query.="'".$countryData["date_type"]."'";
		$query.=')';
		
		$this->db->query($query);
		
		$this->db->query("SELECT * FROM ".TB_COUNTRY." WHERE name='".$countryData["name"]."'");
		$data=$this->db->fetch(0);
		
		for ($i=0;$i<count($countryData['states']);$i++) {
			$query="INSERT INTO ".TB_STATE." (name, country_id) VALUES (";
			
			$query.="'".$countryData['states'][$i]["name"]."', ";
			$query.="'".$data->country_id."'";
			
			$query.=')';
			
			$this->db->query($query);
		}
	}
	
	public function deleteCountry($countryID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_STATE." WHERE country_id=".$countryID);
		
		$this->db->query("DELETE FROM ".TB_COUNTRY." WHERE country_id=".$countryID);
	}
	
	
	public function queryTotalCount($filter=" TRUE ") {
		$query = "SELECT COUNT(*) cnt FROM ".TB_COUNTRY." WHERE $filter";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
	
	
	private function AddDatetypeColumn() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT date_type FROM ".TB_COUNTRY);
		$n = $this->db->num_rows();
		if ($n==0) {
            $this->db->query("alter table ".TB_COUNTRY." add column date_type varchar(30) NULL default NULL");
        	$listofcountrys = $this->getCountryList();
        	$num_country = count($listofcountrys);
        	for ($i=0; $i<$num_country; $i++) {
           	$query="UPDATE ".TB_COUNTRY." SET ";
           	if (($listofcountrys[$i]['name']=='Canada')||($listofcountrys[$i]['name']=='USA'))
           	   $query.="date_type='m/d/Y g:iA'"; 
           	  else $query.="date_type='d-m-Y g:iA'";  
			$query.=" where country_id=".($i+1);
			$this->db->query($query);
        	}
        }
    
    }
	
}
?>