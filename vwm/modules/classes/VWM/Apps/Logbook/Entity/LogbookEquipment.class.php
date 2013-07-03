<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class LogbookEquipment extends Model
{

    /**
     *
     * logbook equipment id
     * 
     * @var int 
     */
    protected $equipment_id;

    /**
     *
     * facility id
     * 
     * @var int
     */
    protected $facility_id = null;

    /**
     *
     * logbook equipment name
     * 
     * @var string 
     */
    protected $equip_desc = null;

    /**
     *
     * logbook equipment permit 
     * 
     * @var string 
     */
    protected $permit = null;
    protected $voc_emissions = 0;

    const TABLE_NAME = 'equipment';

    public function __construct($id)
    {
        $this->modelName = "LogbookEquipment";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    public function getId()
    {
        return $this->equipment_id;
    }

    public function setId($id)
    {
        $this->equipment_id = $id;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facilityId)
    {
        $this->facility_id = $facilityId;
    }

    public function getEquipDesc()
    {
        return $this->equip_desc;
    }

    public function setEquipDesc($equipDesc)
    {
        $this->equip_desc = $equipDesc;
    }

    public function getVocEmissions()
    {
        return $this->voc_emissions;
    }

    public function setVocEmissions($vocEmissions)
    {
        $this->voc_emissions = $vocEmissions;
    }

    public function getPermit()
    {
        return $this->permit;
    }

    public function setPermit($permit)
    {
        $this->permit = $permit;
    }

        public function load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE equipment_id = {$db->sqltext($this->getId())} " .
                "LIMIT 1";
        $db->query($query);

        if ($db->num_rows() == 0) {
            return false;
        }
        $rows = $db->fetch(0);
        $this->initByArray($rows);
    }

    public function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "equip_desc = '{$db->sqltext($this->getEquipDesc())}', " .
                "permit = '{$db->sqltext($this->getPermit())}', " .
                "voc_emissions = {$db->sqltext($this->getVocEmissions())}";
        $response = $db->exec($query);
        $id = $db->getLastInsertedID();
        if ($response) {
            $this->setId($id);
            return $id;
        } else {
            return false;
        }
    }

    public function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "UPDATE " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "equip_desc = '{$db->sqltext($this->getEquipDesc())}', " .
                "permit = '{$db->sqltext($this->getPermit())}', " .
                "voc_emissions = {$db->sqltext($this->getVocEmissions())} " .
                "WHERE equipment_id = {$db->sqltext($this->getId())}";
        $response = $db->exec($query);

        if ($response) {
            return $this->getId();
        } else {
            return false;
        }
    }

    /**
     * 
     * get attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getEquipDesc(),
            'facilityId' => $this->getFacilityId(),
            'voc_emissions' => $this->getVocEmissions(),
            'permit' => $this->getPermit()
        );
    }

    /**
     * delete logbook equipment
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "DELETE FROM " . self::TABLE_NAME . " " .
                "WHERE equipment_id = {$db->sqltext($this->getId())}";
        $db->query($query);
    }

}
?>
