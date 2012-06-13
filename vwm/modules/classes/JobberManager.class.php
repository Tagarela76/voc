<?php
class JobberManager {

        private $db;

	function __construct($db) {
                $this->db=$db;
        }

	public function getJobberDetails($jobber_id, $vanilla=false) {
		$jobber_id=mysql_real_escape_string($jobber_id);

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

	public function getJobbersSupplierList($jobberID) {

		$query = "SELECT * FROM supplier2jobber WHERE jobber_id = {$jobberID}";
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$list = $this->db->fetch_all_array();

		return $list;
	}

	public function getMoreJobbersWithSameSupplierID($supplierID,$jobberID) {

		$query = "SELECT jobber_id FROM supplier2jobber WHERE supplier_id = {$supplierID} AND jobber_id != {$jobberID}";
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$list = $this->db->fetch_all_array();

		return $list;
	}
	public function getFacilityJobberList($facilityID) {

		$query = "SELECT * FROM facility2jobber WHERE facility_id = {$facilityID}";
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();

		return $arr;
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
	public function deleteJobber2Facility($facilityID) {

		$query = "DELETE FROM supplier2jobber WHERE facility_id = {$facilityID}";

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

		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}

	public function updateJobberFacility($facilityID, $jobberArr) {
		// clear jobber array
		foreach ($jobberArr as $jobberPost) {
			if ($jobberPost != '') {
				$jobber[] =  $jobberPost;
			}
		}
		if (!isset($jobber)) {
			// empty jobber array
			return true;
		}
		$query = "DELETE FROM facility2jobber WHERE facility_id = {$facilityID}";
		$this->db->query($query);

		foreach ($jobberArr as $id){
			$query = "INSERT INTO facility2jobber VALUES (NULL, ".$facilityID." , ".$id." )";

			$this->db->query($query);
		}


		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}

}