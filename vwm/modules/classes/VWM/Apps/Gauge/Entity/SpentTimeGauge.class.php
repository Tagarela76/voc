<?php
namespace VWM\Apps\Gauge\Entity;



class SpentTimeGauge extends Gauge {
	
	const TABLE_NAME = 'product_gauge';
	
	public function __construct(\db $db, $facilityId = null) {
		$this->db = $db;
		$this->modelName = 'timeProductGauge';
		$this->gauge_type= Gauge::TIME_GAUGE;
		if (isset($facilityId)) {
			$this->setFacilityId($facilityId);
			$this->load();
		}
	}
	
	public function load() {

		if (is_null($this->getFacilityId())) {
			return false;
		}

		if ($this->getDepartmentId()) {
			$sql = "SELECT * " .
					"FROM " . self::TABLE_NAME . " " .
					"WHERE department_id={$this->db->sqltext($this->getDepartmentId())} " .
					"AND gauge_type=2 ".
					"LIMIT 1";
					
		} else {
			$sql = "SELECT * " .
					"FROM " . self::TABLE_NAME . " " .
					"WHERE facility_id={$this->db->sqltext($this->getFacilityId())} " .
					"AND gauge_type=2 ".
					"AND department_id IS NULL " .
					"LIMIT 1";
					
		}
		
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
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
		
		foreach ($facilityProductsDetails as $product) {
			$productQty += $product['spent_time'];
		}

		return $productQty;
	}
	
	
	
}
?>
