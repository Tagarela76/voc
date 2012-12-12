<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;

class Department extends Model {

	protected $department_id;
	protected $name;
	protected $facility_id;
	protected $creator_id;
	protected $voc_limit;
	protected $voc_annual_limit;

	const TABLE_NAME = 'department';

	public function __construct(\db $db) {
		$this->db = $db;
	}

	public function getDepartmentId() {
		return $this->department_id;
	}

	public function setDepartmentId($department_id) {
		$this->department_id = $department_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getCreatorId() {
		return $this->creator_id;
	}

	public function setCreatorId($creator_id) {
		$this->creator_id = $creator_id;
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


}

?>
