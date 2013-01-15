<?php

namespace VWM\Apps\Gauge\Entity;

class SpentTimeGauge extends Gauge {

	const TABLE_NAME = 'product_gauge';
	const GAUGE_TYPE_NAME = 'Time Spent';
	const TIME_PRIORITY = 2;

	public function __construct(\db $db, $facilityId = null) {
		$this->db = $db;
		$this->gauge_priority = self::TIME_PRIORITY;
		$this->modelName = 'timeProductGauge';
		$this->gauge_type = Gauge::TIME_GAUGE;
		if (isset($facilityId)) {
			$this->setFacilityId($facilityId);
			$this->load();
		}
	}

	public function getCurrentUsage() {
		$month = 'MONTH(CURRENT_DATE)';
		$year = 'YEAR(CURRENT_DATE)';
		$department = $this->department_id;

		if (is_null($department)) {
			$query = "SELECT spent_time " .
					"FROM " . TB_USAGE . " m " .
					"JOIN " . TB_DEPARTMENT . " d " .
					"ON m.department_id = d.department_id " .
					"WHERE d.facility_id={$this->db->sqltext($this->facility_id)} ";
		} else {

			$query = "SELECT spent_time " .
					"FROM " . TB_USAGE . " m " .
					"WHERE m.department_id={$this->db->sqltext($this->department_id)} ";
		}
		if ($this->period == 0) {
			$query .= "AND MONTH(FROM_UNIXTIME(m.creation_time)) = {$this->db->sqltext($month)} " .
					"AND YEAR(FROM_UNIXTIME(m.creation_time)) = {$this->db->sqltext($year)}";
		} else {
			$query .= "AND YEAR(FROM_UNIXTIME(m.creation_time)) = {$this->db->sqltext($year)}";
		}


		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$facilityProductsDetails = $this->db->fetch_all_array();
		} else {
			$facilityProductsDetails = 0;
		}

		$spentTime='';
		foreach ($facilityProductsDetails as $product) {
			$spentTime += $product['spent_time'];
		}

		$unittype = new \Unittype($this->db);
		$unitType = $unittype->getNameByID($this->unit_type);
		$unitTypeConverter = new \UnitTypeConverter($this->db);
		$spentTime = $unitTypeConverter->convertDefaultTime($spentTime, $unitType);
		
		return  round($spentTime, 2);
	}
}

?>
