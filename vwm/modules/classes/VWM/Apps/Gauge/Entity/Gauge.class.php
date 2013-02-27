<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Model;

abstract class Gauge extends Model
{

    /**
     * Gauge id
     * @var int
     */
    protected $id;

    /**
     * Gauge top value (maximum)
     * @var float
     */
    protected $limit = 0;

    /**
     * Gauge unit type
     * @var int
     */
    protected $unit_type = 1;

    /**
     * Gauge tracking period (monthly or annually)
     * @var int
     */
    protected $period = 0;

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

    /**
     * Gauge type
     * @var int
     */
    protected $gauge_type;

    /**
     * Gauge unit type  name;
     */
    protected $unit_type_name;
    protected $last_update_time;

    /**
     * Guage pxCount
     * @var  int
     */
    protected $pxCount;

    /**
     * @var int priority
     */
    protected $gauge_priority;

    /**
     * @var float current usage for period
     */
    protected $currentUsage;

    const PERIOD_MONTHLY = 0;
    const PERIOD_ANNUALLY = 1;
    const QUANTITY_GAUGE = 1;
    const TIME_GAUGE = 2;
    const VOC_GAUGE = 3;
    const NOX_GAUGE = 4;
    const TABLE_NAME = 'product_gauge';

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

    public function getGaugePriority()
    {
        return $this->gauge_priority;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getUnitType()
    {
        return $this->unit_type;
    }

    public function setUnitType($unit_type)
    {
        $this->unit_type = $unit_type;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function getDepartmentId()
    {
        return $this->department_id;
    }

    public function setDepartmentId($department_id)
    {
        $this->department_id = $department_id;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }

    public function getPxCount()
    {
        $this->pxCount = round(200 * $this->getCurrentUsage() / $this->limit);
        if ($this->pxCount > 200) {
            $this->pxCount = 200;
        }
        return $this->pxCount;
    }

    public function getUnitTypeName()
    {
        return $this->unit_type_name;
    }

    public function setUnitTypeName($unit_type_name)
    {
        $this->unit_type_name = $unit_type_name;
    }

    public function getPeriodOptions()
    {
        return array(
            'Monthly' => self::PERIOD_MONTHLY,
            'Annually' => self::PERIOD_ANNUALLY,
        );
    }

    public function getGaugeType()
    {
        return $this->gauge_type;
    }

    public function getPeriodName()
    {
        $options = $this->getPeriodOptions();
        foreach ($options as $key => $option) {
            if ($option == $this->getPeriod()) {
                return $key;
            }
        }
    }

    /**
     * Get current absolute usage
     *
     * @return float current absolute usage
     */
    abstract public function getCurrentUsage();

    public static function getGaugeTypes()
    {
        return array(
            self::QUANTITY_GAUGE => 'Product\'s Quantity',
            self::TIME_GAUGE => 'Time Spent',
            self::VOC_GAUGE => 'VOC',
            self::NOX_GAUGE => 'NOx',
        );
    }

    protected function _insert()
    {
        $lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";
        $departmentId = ($this->getDepartmentId()) ? "'{$this->getDepartmentId()}'" : "NULL";

        $sql = "INSERT INTO " . self::TABLE_NAME . " (" .
                "`limit`, unit_type, period, facility_id, department_id, last_update_time, gauge_type" .
                ") VALUES ( " .
                "{$this->db->sqltext($this->getLimit())}, " .
                "{$this->db->sqltext($this->getUnitType())}, " .
                "{$this->db->sqltext($this->getPeriod())}, " .
                "{$this->db->sqltext($this->getFacilityId())}, " .
                "{$departmentId}, " .
                "{$lastUpdateTime}, " .
                "{$this->db->sqltext($this->getGaugeType())} " .
                ")";

        $response = $this->db->exec($sql);
        if ($response) {
            $this->setId($this->db->getLastInsertedID());
            return $this->getId();
        } else {
            return false;
        }
    }

    protected function _update()
    {
        $lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";
        $departmentId = ($this->getDepartmentId()) ? "'{$this->getDepartmentId()}'" : "NULL";

        $sql = "UPDATE " . self::TABLE_NAME . " SET " .
                "`limit`={$this->db->sqltext($this->getLimit())}, " .
                "unit_type='{$this->db->sqltext($this->getUnitType())}', " .
                "period={$this->db->sqltext($this->getPeriod())}, " .
                "facility_id={$this->db->sqltext($this->getFacilityId())}, " .
                "department_id={$departmentId}, " .
                "gauge_type={$this->db->sqltext($this->getGaugeType())}, " .
                "last_update_time={$lastUpdateTime} " .
                "WHERE id={$this->db->sqltext($this->getId())}";

        $response = $this->db->exec($sql);
        if ($response) {
            return $this->getId();
        } else {
            return false;
        }
    }

    public function load()
    {
        if (is_null($this->getFacilityId())) {
            return false;
        }

        if ($this->getDepartmentId()) {
            $sql = "SELECT * " .
                    "FROM " . self::TABLE_NAME . " " .
                    "WHERE department_id = {$this->db->sqltext($this->getDepartmentId())} " .
                    "AND gauge_type = {$this->db->sqltext($this->getGaugeType())} " .
                    "LIMIT 1";
        } else {
            $sql = "SELECT * " .
                    "FROM " . self::TABLE_NAME . " " .
                    "WHERE facility_id = {$this->db->sqltext($this->getFacilityId())} " .
                    "AND gauge_type = {$this->db->sqltext($this->getGaugeType())} " .
                    "AND department_id IS NULL " .
                    "LIMIT 1";
        }

        $this->db->query($sql);

        if ($this->db->num_rows() == 0) {

            return false;
        }
        $row = $this->db->fetch(0);
        $this->initByArray($row);

        return true;
    }

    public function getGaugeTypeName()
    {
        $types = $this->getGaugeTypes();
        return $types[$this->gauge_type];
    }

}
