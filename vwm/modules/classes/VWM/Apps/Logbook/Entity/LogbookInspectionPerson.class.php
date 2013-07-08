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
    
    /**
     *
     * @var boolean 
     */
    protected $deleted = 0;

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
    
    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    
    public function __construct($id = null)
    {
        $this->modelName = "LogbookInspectionPerson";
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

        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "deleted = 0, " .
                "name = '{$db->sqltext($this->getName())}'";
        $db->query($query);
        $id = $db->getLastInsertedID();
        $this->setId($id);

        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "UPDATE " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "name = '{$db->sqltext($this->getName())}' " .
                "deleted = '{$db->sqltext($this->getDeleted())}' " .
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
    
    /**
     * delete inspection Person
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "UPDATE " . self::TABLE_NAME . " " .
                "SET deleted = 1 ".
                "WHERE id={$db->sqltext($this->getId())}";
        $db->query($query);
    }

}
?>