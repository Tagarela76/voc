<?php

namespace VWM\Apps\Gauge\Entity;

use VWM\Framework\Model;

/**
 * Used Product Quantity Gauge
 */
class QtyProductGauge extends Gauge
{

    const GAUGE_TYPE_NAME = 'Product\'s Quantity';
    const QTY_PRIORITY = 3;

    public function __construct(\db $db, $facilityId = null)
    {
        $this->db = $db;
        $this->gauge_priority = self::QTY_PRIORITY;
        $this->modelName = 'QtyProductGauge';
        $this->gauge_type = Gauge::QUANTITY_GAUGE;
        if (isset($facilityId)) {
            $this->setFacilityId($facilityId);
            $this->load();
        }
    }

    /**
     * Delete settings for facility
     * TODO: is this method actually needed?
     */
    public function delete()
    {
        $sql = "DELETE FROM " . self::TABLE_NAME . "
				 WHERE facility_id={$this->db->sqltext($this->getFacilityId())}";
        $this->db->query($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUsage()
    {
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
        $productQty = '';
        if ($gaugeUnittypeDetails['type_id'] == 2) {
            foreach ($facilityProductsDetails as $product) {
                $productQty += $unitTypeConverter
                        ->fromDefaultWeight($product['quantity'], $destinationType);
            }
        } else {
            foreach ($facilityProductsDetails as $product) {
                $productQty += $product['quantity'] / $product['density'];
            }
        }

        return round($productQty, 2);
    }

}