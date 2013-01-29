<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\SpentTimeGauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;
use VWM\Apps\Gauge\Entity\NoxGauge;
use VWM\Apps\Gauge\Entity\VocGauge;


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

	/**
	 * @var VWM\Hierarchy\Company
	 */
	protected $company;

	/**
	 * @var VWM\Hierarchy\Department[]
	 */
	protected $departments;
	
	/*
	 * name of unit_class for getUnitTypeList function
	 * USAWght for default
	 * @var string 
	 */
	protected $unitTypeClass = 'USAWght';
	

	const TABLE_NAME = 'facility';
	const TABLE_GAUGE = 'product_gauge';
	const TB_PROCESS = 'process';
	const TB_UNITTYPE = 'unittype';
	const TB_DEFAULT = '`default`';
	const TB_TYPE = 'type';
	const TB_UNITCLASS = 'unit_class';
	const CATEGORY = 'facility';
	
	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = "Facility";
		
		if($id !== null) {
			$this->setFacilityId($id);
			if(!$this->_load()) {
				throw new Exception('404');
			}
		}
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

	public function getUnitTypeClass() {
		return $this->unitTypeClass;
	}

	public function setUnitTypeClass($unitTypeClass) {
		$this->unitTypeClass = $unitTypeClass;
	}

		/**
	 * Saves facility into database
	 * @return int|bool object id or false on failure
	 */
	public function save() {		
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getFacilityId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
	protected function _insert() {
		$clientFacilityId = ($this->getClientFacilityId()) 
				? "'{$this->db->sqltext($this->getClientFacilityId())}'" 
				: "NULL";						
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
		
		$sql = "INSERT INTO ".TB_FACILITY." (" .
				"epa, company_id, name, address, city, zip, county, state, " .
				"country, phone, fax, email, contact, title, creater_id, " .
				"voc_limit, voc_annual_limit, gcg_id, monthly_nox_limit, " .
				"client_facility_id, last_update_time " .
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
				"{$this->db->sqltext($this->getMonthlyNoxLimit())}, " .
				"{$clientFacilityId}, " .
				"{$lastUpdateTime} " .
				")";
		$r = $this->db->exec($sql);
		if($r) {
			$this->setFacilityId($this->db->getLastInsertedID());	
			return $this->getFacilityId();
		} else {
			return false;
		}
		
		
	}
	
	protected function _update() {
		$clientFacilityId = ($this->getClientFacilityId()) 
				? "'{$this->db->sqltext($this->getClientFacilityId())}'" 
				: "NULL";						
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".TB_FACILITY." SET " .
				"epa='{$this->db->sqltext($this->getEpa())}', " .
				"voc_limit={$this->db->sqltext($this->getVocLimit())}, " .
				"voc_annual_limit={$this->db->sqltext($this->getVocAnnualLimit())}, " .
				"name='{$this->db->sqltext($this->getName())}', " .				
				"address='{$this->db->sqltext($this->getAddress())}', "	.
				"city='{$this->db->sqltext($this->getCity())}', " .
				"zip='{$this->db->sqltext($this->getZip())}', " .
				"county='{$this->db->sqltext($this->getCounty())}', " .
				"state='{$this->db->sqltext($this->getState())}', " .
				"country={$this->db->sqltext($this->getCountry())}, " .
				"phone='{$this->db->sqltext($this->getPhone())}', " .
				"fax='{$this->db->sqltext($this->getFax())}', " .
				"email='{$this->db->sqltext($this->getEmail())}', " .
				"contact='{$this->db->sqltext($this->getContact())}', " .
				"monthly_nox_limit={$this->db->sqltext($this->getMonthlyNoxLimit())}, " .
				"title='{$this->db->sqltext($this->getTitle())}', "	.
				"client_facility_id={$clientFacilityId}, " .
				"last_update_time={$lastUpdateTime} " .
				"WHERE facility_id={$this->db->sqltext($this->getFacilityId())}";	
		
		$result = $this->db->exec($sql);
		if($result) {			
			return $this->getFacilityId();
		} else {
			return false;
		}
	}		
	
	
	private function _load() {
		if(!$this->getFacilityId()) {
			throw new \Exception('Facility ID should be set before calling this method');
		}
		
		$sql = "SELECT * FROM ".TB_FACILITY." " .
				"WHERE facility_id = {$this->db->sqltext($this->getFacilityId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		
		return true;
	}


	/**
	 * Get facility's company
	 * @return \VWM\Hierarchy\Company
	 * @throws \Exception
	 */
	public function getCompany() {
		if($this->company === null) {
			if(!$this->getCompanyId()) {
				throw new \Exception('Company Id is not set');
			}

			$this->company = new Company($this->db, $this->getCompanyId());
		}

		return $this->company;
	}


	/**
	 * Get facility's departments
	 * @var VWM\Hierarchy\Department[]
	 * @throws \Exception
	 */
	public function getDepartments() {
		if($this->departments === null) {
			if(!$this->getFacilityId()) {
				throw new \Exception('Facility Id is not set');
			}

			$sql = "SELECT * " .
					"FROM ".Department::TABLE_NAME." " .
					"WHERE facility_id = {$this->db->sqltext($this->getFacilityId())}";
			$this->db->query($sql);
			if($this->db->num_rows() == 0) {
				$this->departments = array();
				return $this->departments;
			}

			$rows = $this->db->fetch_all_array();
			foreach ($rows as $row) {
				$department = new Department($this->db);
				$department->initByArray($row);
				$this->departments[] = $department;
			}
		}

		return $this->departments;
	}
	
	public function getAllAvailableGauges(){
		$sql = "SELECT gauge_type FROM " . self::TABLE_GAUGE . " WHERE `limit`<>0 AND department_id is NULL AND facility_id=".$this->db->sqltext($this->getFacilityId());
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		$gauges = array();
		foreach ($rows as $row) {
			switch ($row["gauge_type"]) {
				case Gauge::QUANTITY_GAUGE:
					$gauge = new QtyProductGauge($this->db);
					break;
				case Gauge::TIME_GAUGE:
					$gauge = new SpentTimeGauge($this->db);
					break;
				case Gauge::VOC_GAUGE:
					$gauge = new VocGauge($this->db);
					break;
				case Gauge::NOX_GAUGE:
					$gauge = new NoxGauge($this->db);
					break;
				default:
					break;
			}
			$gauge->setDepartmentId($this->department_id);
			$gauge->setFacilityId($this->facility_id);
			$gauge->load();

			$gauges[] = $gauge;
		}
		return $gauges;
	}
	
	/**
	 * @return \VWM\Apps\Process\Process[]
	 */
	public function getProcessList(){
		$sql = "SELECT id ".
				"FROM ".self::TB_PROCESS.
				" WHERE facility_id={$this->db->sqltext($this->getFacilityId())}";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$processListId = $this->db->fetch_all();
		foreach ($processListId as $processId){
			$processList[] = new \VWM\Apps\Process\Process($this->db, $processId->id);
		}
		return $processList;
	}
	
	public function getUnitTypeList() {
		
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.system " .
				 "FROM " . self::TB_UNITTYPE ." ut ". 
				 "INNER JOIN " . self::TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . self::TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . self::TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = '" .self::CATEGORY."' ".
				 "AND def.id_of_object = {$this->db->sqltext($this->getFacilityId())} ".
				 "AND uc.name = '{$this->db->sqltext($this->getUnitTypeClass())}' ".
				 "ORDER BY ut.unittype_id";
		
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unittype = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system
				);
				$unittypes[] = $unittype;
			}
		} else {
			$company = $this->getCompany();
			$company->setUnitTypeClass($this->getUnitTypeClass());
			$unittypes = $company->getUnitTypeList();
		}

		return $unittypes;
	}
	
	
	
}

?>
