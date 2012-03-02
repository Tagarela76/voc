<?php
class JobberManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getJobberDetails($jobber_id, $vanilla=false) {
		$jobber_id=mysql_real_escape_string($jobber_id);
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM jobber WHERE jobber_id = {$jobber_id}");
		$jobberDetails = $this->db->fetch_array(0);

		if (!$vanilla) {
			$reg = new Registration($this->db);
			//	Set State
			if ($reg->isOwnState($jobberDetails['country']))
			{
				//	have own state list
				$jobberDetails["state"] = $reg->getState($jobberDetails['state']);
			}
			
			//	Set Country
			$jobberDetails["country"] = $reg->getCountry($jobberDetails['country']);
		}
		return $jobberDetails;
	}
	
	public function getJobberCount() {

		$query = "SELECT count(*) Num FROM jobber";
		$query = mysql_escape_string($query);
		$this->db->query($query);
		$count = $this->db->fetch(0)->Num;
		return $count;
	}
	
	public function getJobberList() {

		$query = "SELECT * FROM jobber";
		$this->db->query($query);
		//echo 	$query;
		if ($this->db->num_rows() == 0) {
			return false;
		}	
		$arr = $this->db->fetch_all_array();
		
		$list = array();
			foreach($arr as $l) {
				$jobber = new Jobber($this->db, $l);
				$list[] = $jobber;
			}
			
			
		return $list;
	}	
	
	public function deleteJobber($jobberID) {


		$query1 = "DELETE FROM jobber WHERE jobber_id = {$jobberID}";
		$this->db->query($query1);		


		$query = "DELETE FROM supplier2jobber WHERE jobber_id = {$jobberID}";
	
		$this->db->query($query);		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function updateJobberSuppliers($jobberID, $supplierArr) {

		$query = "DELETE FROM supplier2jobber WHERE jobber_id = {$jobberID}";
		$this->db->query($query);
		
		foreach ($supplierArr as $id){
			$query = "INSERT INTO supplier2jobber (jobber_id,supplier_id) VALUES ({$jobberID},{$id})";
			$this->db->query($query);	
		}
		
		//echo 	$query;
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	

}