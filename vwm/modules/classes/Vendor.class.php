<?php

class Vendor {
	
	protected $db;
		
	protected $vendor_id;
	protected $vendor_name;
	protected $vendor_code;
		
    function Vendor($db) {    	
    	$this->db = $db;
    }
    public function getVendor_id() {
		return $this->vendor_id;
	}

	public function getVendor_name() {
		return $this->vendor_name;
	}

	public function getVendor_code() {
		return $this->vendor_code;
	}

	public function setVendor_id($vendor_id) {
		$this->vendor_id = $vendor_id;
	}

	public function setVendor_name($vendor_name) {
		$this->vendor_name = $vendor_name;
	}

	public function setVendor_code($vendor_code) {
		$this->vendor_code = $vendor_code;
	}
	
	public function getVendorList() {
		$query = "SELECT * FROM vendor";
		$this->db->query($query);
		
		if ($this->db->num_rows() == 0) {
			return array();
		} else {
			return $this->db->fetch_all_array();
		}
	}
	
	public function getVendorDetails($vendor_id) {
		$query = "SELECT * FROM vendor WHERE vendor_id = ".mysql_real_escape_string($vendor_id);
		$this->db->query($query);
		
		if ($this->db->num_rows() == 0) {
			return array();
		} else {
			return $this->db->fetch_array(0);
		}
	}
	
	public function getVendorDetailsByCode($vendor_code) {
		$query = "SELECT * FROM vendor WHERE code LIKE '".mysql_real_escape_string($vendor_code)."'";
		$this->db->query($query);
		
		if ($this->db->num_rows() == 0) {
			return array();
		} else {
			return $this->db->fetch_array(0);
		}
	}
	
	public function addVendor($vendor_code, $vendor_name) {
		$query = "INSERT INTO vendor (name, code) VALUES ('".mysql_real_escape_string($vendor_name)."', '".mysql_real_escape_string($vendor_code)."')";
		$this->db->query($query);
		if (mysql_errno() == 0) {
			$vendor_id = $this->db->getLastInsertedID();
		} else {
			$vendor_id = 0;
		}
		
		return $vendor_id;
	}
		
	public function deleteVendor($vendor_id) {
		
	}
}
?>