<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Model;

class QtyProductGauge extends Gauge {

	const TABLE_NAME = 'qty_product_gauge';

	public function __construct(\db $db, $facilityId = null) {
		$this->db = $db;
		$this->modelName = 'QtyProductGauge';
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
					"LIMIT 1";
		} else {
			$sql = "SELECT * " .
					"FROM " . self::TABLE_NAME . " " .
					"WHERE facility_id={$this->db->sqltext($this->getFacilityId())} " .
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

	/**
	 * Insert new settings
	 * @return int| boolean
	 */
	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";
		$departmentId = ($this->getDepartmentId()) ? "'{$this->getDepartmentId()}'" : "NULL";

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"`limit`, unit_type, period, facility_id, department_id, last_update_time" .
				") VALUES ( " .
				"{$this->db->sqltext($this->getLimit())}, " .
				"{$this->db->sqltext($this->getUnitType())}, " .
				"{$this->db->sqltext($this->getPeriod())}, " .
				"{$this->db->sqltext($this->getFacilityId())}, " .
				"{$departmentId}, " .
				"{$lastUpdateTime} " .
				")";

		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}
	}

	/**
	 * Update update settings
	 * @return boolean
	 */
	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";
		$departmentId = ($this->getDepartmentId()) ? "'{$this->getDepartmentId()}'" : "NULL";

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"`limit`={$this->db->sqltext($this->getLimit())}, " .
				"unit_type='{$this->db->sqltext($this->getUnitType())}', " .
				"period={$this->db->sqltext($this->getPeriod())}, " .
				"facility_id={$this->db->sqltext($this->getFacilityId())}, " .
				"department_id={$departmentId}, " .
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}

	/**
	 * Delete settings for facility
	 */
	public function delete() {

		$sql = "DELETE FROM " . self::TABLE_NAME . "
				 WHERE facility_id={$this->db->sqltext($this->getFacilityId())}";
		$this->db->query($sql);
	}

	public function getCurrentUsage() {
		$month = 'MONTH(CURRENT_DATE)';
		$year = 'YEAR(CURRENT_DATE)';
		$department = $this->department_id;

		if (is_null($department)) {
			$query = "SELECT mg.quantity_lbs quantity, p.density, t.type_id " .
					"FROM " . TB_MIXGROUP . " mg " .
					"JOIN " . TB_USAGE . " m " .
					"ON mg.mix_id = m.mix_id " .
					"JOIN " . TB_DEPARTMENT . " d " .
					"ON m.department_id = d.department_id " .
					"JOIN " . TB_PRODUCT . " p " .
					"ON p.product_id = mg.product_id " .
					"JOIN " . TB_UNITTYPE . " t " .
					"ON t.unittype_id = mg.unit_type " .
					"WHERE d.facility_id={$this->db->sqltext($this->facility_id)} ";
		} else {
			$query = "SELECT mg.quantity_lbs quantity, p.density, t.type_id " .
					"FROM " . TB_MIXGROUP . " mg " .
					"JOIN " . TB_USAGE . " m " .
					"ON mg.mix_id = m.mix_id " .
					"JOIN " . TB_PRODUCT . " p " .
					"ON p.product_id = mg.product_id " .
					"JOIN " . TB_UNITTYPE . " t " .
					"ON t.unittype_id = mg.unit_type " .
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

		// convert to preffered unit type
		//type_id = 2 ->Weight
		//type_id = 4 ->Volume Liquid

		$unitTypeConverter = new \UnitTypeConverter($this->db);
		$unitType = new \Unittype($this->db);
		$gaugeUnittypeDetails = $unitType->getUnittypeDetails($this->unit_type);
		$destinationType = $unitType->getDescriptionByID($this->unit_type);

		if ($gaugeUnittypeDetails['type_id'] == 2) {
			foreach ($facilityProductsDetails as $product) {
				$productQty += $unitTypeConverter->fromDefaultWeight($product['quantity'], $destinationType);
			}
		} else {
			foreach ($facilityProductsDetails as $product) {
				$productQty += $product['quantity'] / $product['density'];
			}
		}


		return $productQty;
	}

}

?>
