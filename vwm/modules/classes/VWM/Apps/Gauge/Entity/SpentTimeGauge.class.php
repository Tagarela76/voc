<?php

namespace VWM\Apps\Gauge\Entity;

/**
 * Spent Time Gauge
 */
class SpentTimeGauge extends Gauge
{

    const TABLE_NAME = 'product_gauge';
    const GAUGE_TYPE_NAME = 'Time Spent';
    const TIME_PRIORITY = 2;

    public function __construct(\db $db, $facilityId = null)
    {
        $this->db = $db;
        $this->gauge_priority = self::TIME_PRIORITY;
        $this->modelName = 'timeProductGauge';
        $this->gauge_type = Gauge::TIME_GAUGE;
        if (isset($facilityId)) {
            $this->setFacilityId($facilityId);
            $this->load();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUsage()
    {
        if ($this->currentUsage) {
            return $this->currentUsage;
        }

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
            $mixRows = $this->db->fetch_all_array();
        } else {

            // spent time is 0 minutes in this period
            return 0;
        }

        $spentTimeInMinutes = 0;
        foreach ($mixRows as $mixRow) {
            $spentTimeInMinutes += $mixRow['spent_time'];
        }

        $unittype = new \Unittype($this->db);
        $unitType = $unittype->getNameByID($this->unit_type);
        $unitTypeConverter = new \UnitTypeConverter($this->db);
        $spentTime = $unitTypeConverter->convertDefaultTime($spentTimeInMinutes,
                $unitType);

        $this->currentUsage = round($spentTime, 2);

        return $this->currentUsage;
    }

}