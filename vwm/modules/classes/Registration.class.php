<?php

class Registration {
	
	private $db;
	
	function Registration($db) {
		$this->db=$db;
	}
	
	public function getCountryList() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_COUNTRY." ORDER BY name");
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$country=array (
					'id'			=>	$data->country_id,
					'name'			=>	$data->name
				);
				$countries[]=$country;
			}
		}
		
		return $countries;
	}
	
	public function getStateList($country_id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_STATE." WHERE country_id=".$country_id." ORDER BY name");
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$state=array (
					'id'			=>	$data->state_id,
					'name'			=>	$data->name
				);
				$states[]=$state;
			}
		}
		
		return $states;
	}
	
	public function isOwnState($country_id) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT * FROM ".TB_STATE." WHERE country_id='".$country_id."'";
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getState($state_id) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_STATE." WHERE state_id=".$state_id;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			$state = $this->db->fetch(0);
			return $state->name;
		}
		
		return "none";
	}
	
	public function getCountry($country_id) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_COUNTRY." WHERE country_id=".$country_id;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			$country = $this->db->fetch(0);
			return $country->name;
		}
		
		return "none";
	}
}
?>