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
	protected $creater_id;
	protected $voc_limit;
	protected $voc_annual_limit;
	protected $share_wo;

	/**
	 *
	 * @var Facility
	 */
	protected $facility;

	public $searchCriteria = array();
	
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

	public function getCreaterId() {
		return $this->creater_id;
	}

	public function setCreaterId($creator_id) {
		$this->creater_id = $creator_id;
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
	
	public function getShareWo() {
		return $this->share_wo;
	}

	public function setShareWo($shareWo) {
		$this->share_wo = $shareWo;
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
	
	
	public function countRepairOrderInDepartment() {

		$sql = "SELECT count(*) repairOrderCount FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE department_id=".$this->db->sqltext($this->department_id);
		if (count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}
		$this->db->query($sql);
		if ($this->db->num_rows() > 0) {
			return (int) $this->db->fetch(0)->repairOrderCount;
		} else {
			return false;
		}
	}
	
	public function getRepairOrdersList(Pagination $pagination = null) {
		
		$repairOrders = array();
		
		$sql =  "SELECT * FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE department_id=".$this->db->sqltext($this->department_id);
	
		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}

		$sql .= " ORDER BY dw.id DESC";

        if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}        
		
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();
		
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		foreach ($rows as $row) {
			$repairOrder = new \RepairOrder($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($repairOrder, $key)) {
					$repairOrder->$key = $value;
				}
			}
			$repairOrders[] = $repairOrder;
		}
		return $repairOrders;
	}
	
	protected function _insert() {
		
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$query = "INSERT INTO ".self::TABLE_NAME." (" .
				"name, facility_id, creater_id, voc_limit, voc_annual_limit, share_wo, last_update_time " .
				") VALUES ( ".
				"'{$this->db->sqltext($this->getName())}', " .
				"{$this->db->sqltext($this->getFacilityId())}, " .
				"'{$this->db->sqltext($this->getCreaterId())}', " .
				"'{$this->db->sqltext($this->getVocLimit())}', " .
				"'{$this->db->sqltext($this->getVocAnnualLimit())}', " .
				"'{$this->db->sqltext($this->getShareWo())}', " .
				"{$lastUpdateTime} " .
				")";
		$response = $this->db->exec($query);
		if ($response) {
			$this->setDepartmentId($this->db->getLastInsertedID());
			return $this->getDepartmentId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
				
		$query = "UPDATE " . self::TABLE_NAME . " SET " .
				"name='" . $this->db->sqltext($this->getName()) . "', " .
				"facility_id=" . $this->db->sqltext($this->getFacilityId()) . ", " .
				"creater_id=" . $this->db->sqltext($this->getCreaterId()) . ", " .
				"voc_limit=" . $this->db->sqltext($this->getVocLimit()) . ", " .
				"voc_annual_limit=" . $this->db->sqltext($this->getVocAnnualLimit()) . ", " .
				"last_update_time=" . $lastUpdateTime . ", " .
				"share_wo=" . $this->db->sqltext($this->getShareWo()) .
				" WHERE department_id=" . $this->db->sqltext($this->getDepartmentId());
		$response = $this->db->exec($query);
		if ($response) {
			return $this->department_id;
		} else {
			return false;
		}
	}
	
	public function save() {		
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->department_id) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}


	/**
	 * Get department's facility
	 * @return \VWM\Hierarchy\Facility
	 * @throws \Exception
	 */
	public function getFacility() {
		if($this->facility === null) {
			if(!$this->getFacilityId()) {
				throw new \Exception('Facility Id is not set');
			}
				
			$this->facility = new Facility($this->db, $this->getFacilityId());	
		}	

		return $this->facility;
	}
}

?>
