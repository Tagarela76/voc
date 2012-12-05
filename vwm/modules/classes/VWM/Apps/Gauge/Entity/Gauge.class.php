<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Model;

abstract class Gauge extends Model {
	
	/**
	 * Gauge id
	 * @var int 
	 */
	protected $id;

	/**
	 * Gauge top value (maximum)
	 * @var float
	 */
	protected $limit=0;

	/**
	 * Gauge unit type
	 * @var int
	 */
    protected $unit_type=1;

	/**
	 * Gauge tracking period (monthly or annually)
	 * @var int
	 */
    protected $period=0;

	/**
	 * Gauge for this department
	 * @var int
	 */
	protected $department_id;

	/**
	 * Gauge for this facility
	 * @var int
	 */
	protected $facility_id;

    protected $last_update_time;

    const PERIOD_MONTHLY = 0;
	const PERIOD_ANNUALLY= 1;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getLimit() {
		return $this->limit;
	}

	public function setLimit($limit) {
		$this->limit = $limit;
	}

	public function getUnitType() {
		return $this->unit_type;
	}

	public function setUnitType($unit_type) {
		$this->unit_type = $unit_type;
	}

	public function getPeriod() {
		return $this->period;
	}

	public function setPeriod($period) {
		$this->period = $period;
	}

	public function getDepartmentId() {
		return $this->department_id;
	}

	public function setDepartmentId($department_id) {
		$this->department_id = $department_id;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getPeriodOptions() {
		return array(
			'Monthly' => self::PERIOD_MONTHLY,
			'Annually' => self::PERIOD_ANNUALLY,
		);
	}

	public function getPeriodName() {
		$options = $this->getPeriodOptions();
		foreach ($options as $key => $option) {
			if($option == $this->getPeriod()) {
				return $key;
			}
		}
	}


	public function getCurrentUsage() {
		throw new Exception("getCurrentUsage() should be implemented by child");
	}
}

?>
