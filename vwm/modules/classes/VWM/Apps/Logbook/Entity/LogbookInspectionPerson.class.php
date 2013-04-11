<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class LogbookInspectionPerson extends Model
{
    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var int
     */
    protected $facility_id;

    /**
     *
     * @var string
     */
    protected $name;

    const TABLE_NAME = 'inspection_persons';

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

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __construct($id = null)
    {
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    public function load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($this->getId())) {
            return false;
        }
        $sql = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->getId())} " .
                "LIMIT 1";
        $db->query($sql);

        if ($db->num_rows() == 0) {
            return false;
        }
        $rows = $db->fetch(0);
        $this->initByArray($rows);
    }

    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "INSERT INTO " . self::TB_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "name = '{$db->sqltext($this->getName())}'";
        $db->query($query);
        $id = $db->getLastInsertedID();
        $this->setId($id);

        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "UPDATE " . self::TB_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "name = '{$db->sqltext($this->getName())}' " .
                "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($query);

        return $this->getId();
    }

    /*
     * redefine abstract method
     */

    public function getAttributes()
    {
        
    }

}
?>