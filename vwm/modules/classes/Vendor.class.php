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
}
?>