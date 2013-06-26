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
    protected $id;

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
    protected $name = null;

    const TABLE_NAME = 'logbook_equipment';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facilityId)
    {
        $this->facility_id = $facilityId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __construct($id)
    {
        $this->modelName = "LogbookEquipment";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    public function load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->getId())} " .
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
                "name = '{$db->sqltext($this->getName())}'";
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
                "name = '{$db->sqltext($this->getName())}' " .
                "WHERE id = {$db->sqltext($this->getId())}";
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
            'name' => $this->getName(),
            'facilityId' => $this->getFacilityId()
        );
    }
    
    /**
     * delete logbook equipment
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "DELETE FROM " .self::TABLE_NAME. " ".
                 "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($query);
    }

}
?>
