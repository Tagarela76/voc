<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;


class Facility extends Model {
	
	protected $facility_id;
	
	protected $company_id;
	
	protected $name;
	
	protected $epa;
	
	protected $address;
	
	protected $city;
			
	protected $zip;
	
	protected $county;
	
	protected $state;
	
	protected $country;
	
	protected $phone;
	
	protected $fax;
	
	protected $email;
	
	protected $contact;
	
	protected $title;
	
	protected $creater_id;
	
	protected $voc_limit;
	
	protected $voc_annual_limit;
	
	protected $gcg_id;
			
	protected $monthly_nox_limit;
	
	protected $client_facility_id;

	protected $last_update_time;		
	
	public function __construct(\db $db) {
		$this->db = $db;
		$this->modelName = "Facility";
	}
	
	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getCompanyId() {
		return $this->company_id;
	}

	public function setCompanyId($company_id) {
		$this->company_id = $company_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getEpa() {
		return $this->epa;
	}

	public function setEpa($epa) {
		$this->epa = $epa;
	}

	public function getAddress() {
		return $this->address;
	}

	public function setAddress($address) {
		$this->address = $address;
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function getZip() {
		return $this->zip;
	}

	public function setZip($zip) {
		$this->zip = $zip;
	}

	public function getCounty() {
		return $this->county;
	}

	public function setCounty($county) {
		$this->county = $county;
	}

	public function getState() {
		return $this->state;
	}

	public function setState($state) {
		$this->state = $state;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setCountry($country) {
		$this->country = $country;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function getFax() {
		return $this->fax;
	}

	public function setFax($fax) {
		$this->fax = $fax;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getContact() {
		return $this->contact;
	}

	public function setContact($contact) {
		$this->contact = $contact;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getCreaterId() {
		return $this->creater_id;
	}

	public function setCreaterId($creater_id) {
		$this->creater_id = $creater_id;
	}

	public function getVocLimit() {
		return $this->voc_limit;
	}

	public function setVocLimit($voc_limit) {
		$this->voc_limit = $voc_limit;
	}

	public function getVocAnnualLimit() {
		return $this->voc_annual_limit;
	}

	public function setVocAnnualLimit($voc_annual_limit) {
		$this->voc_annual_limit = $voc_annual_limit;
	}

	public function getGcgId() {
		return $this->gcg_id;
	}

	public function setGcgId($gcg_id) {
		$this->gcg_id = $gcg_id;
	}

	public function getMonthlyNoxLimit() {
		return $this->monthly_nox_limit;
	}

	public function setMonthlyNoxLimit($monthly_nox_limit) {
		$this->monthly_nox_limit = $monthly_nox_limit;
	}

	public function getClientFacilityId() {
		return $this->client_facility_id;
	}

	public function setClientFacilityId($client_facility_id) {
		$this->client_facility_id = $client_facility_id;
	}

	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

	/**
	 * Saves facility into database
	 * @return int|bool object id or false on failure
	 */
	public function save() {
		if($this->getFacilityId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
	private function _insert() {
		$sql = "INSERT INTO ".TB_FACILITY." (" .
				"epa, company_id, name, address, city, zip, county, state, " .
				"country, phone, fax, email, contact, title, creater_id, " .
				"voc_limit, voc_annual_limit, gcg_id, monthly_nox_limit " .
				") VALUES ( ".
				"'{$this->db->sqltext($this->getEpa())}', " .
				"{$this->db->sqltext($this->getCompanyId())}, " .
				"'{$this->db->sqltext($this->getName())}', " .
				"'{$this->db->sqltext($this->getAddress())}', " .
				"'{$this->db->sqltext($this->getCity())}', " .
				"'{$this->db->sqltext($this->getZip())}', " .
				"'{$this->db->sqltext($this->getCounty())}', " .
				"'{$this->db->sqltext($this->getState())}', " .
				"{$this->db->sqltext($this->getCountry())}, " .
				"'{$this->db->sqltext($this->getPhone())}', " .
				"'{$this->db->sqltext($this->getFax())}', " .
				"'{$this->db->sqltext($this->getEmail())}', " .
				"'{$this->db->sqltext($this->getContact())}', " .
				"'{$this->db->sqltext($this->getTitle())}', " .
				"'{$this->db->sqltext($this->getCreaterId())}', " .
				"{$this->db->sqltext($this->getVocLimit())}, " .
				"{$this->db->sqltext($this->getVocAnnualLimit())}, " .
				"{$this->db->sqltext($this->getGcgId())}, " .				
				"{$this->db->sqltext($this->getMonthlyNoxLimit())} " .
				")";
		$r = $this->db->exec($sql);
		if($r) {
			$this->setFacilityId($this->db->getLastInsertedID());	
			return $this->getFacilityId();
		} else {
			return false;
		}
		
		
	}
	
	private function _update() {
		
	}
}

?>
