<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\SpentTimeGauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;
use VWM\Apps\Gauge\Entity\NoxGauge;
use VWM\Apps\Gauge\Entity\VocGauge;


class Department extends Model {

	protected $department_id;
	protected $name;
	protected $facility_id;
	protected $creator_id;
	protected $voc_limit;
	protected $voc_annual_limit;

	const TABLE_NAME = 'department';

	public function __construct(\db $db, $departmentId=null) {
		$this->db = $db;
		if (isset($departmentId)) {
			$this->setDepartmentId($departmentId);
			$this->load();
		}
	}
	
	public function load(){
		if (is_null($this->getDepartmentId())) {
			return false;
		}
		
		$sql = "SELECT * FROM ".self::TABLE_NAME." WHERE department_id =".
				$this->db->sqltext($this->getDepartmentId());
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
		
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
	
	public function getGauge($gaugeType){
		switch ($gaugeType){
			case Gauge::QUANTITY_GAUGE:
				$gauge = new QtyProductGauge($this->db);
				break;
			case Gauge::TIME_GAUGE:
				$gauge = new SpentTimeGauge($this->db);
				break;
			case Gauge::VOC_GAUGE:
				break;
			default:
				break;
		}
		
		$gauge->setDepartmentId($this->department_id);
		$gauge->setFacilityId($this->facility_id);
		$gauge->load();
		return $gauge;
		
	}

	public function getAllAvailableGauges() {
		$sql = "SELECT gauge_type FROM " . QtyProductGauge::TABLE_NAME . " WHERE `limit`<>0 AND department_id=" . $this->db->sqltext($this->getDepartmentId());
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

}

?>
