<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class LogbookInspectionType extends Model
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
    protected $settings = null;

    const TABLE_NAME = 'inspection_type';

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

    public function getInspectionTypeRaw()
    {
        return $this->settings;
    }

    public function setInspectionTypeRaw($settings)
    {
        $this->settings = $settings;
    }

    /**
     * 
     * get inspection type
     * 
     * @return std
     */
    public function getInspectionType()
    {
        $settings = $this->getInspectionTypeRaw();
        if (is_null($settings)) {
            return false;
        }
        $settings = json_decode($settings);
        return $settings;
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
        
    }

    protected function _update()
    {
        
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
        
    }

}
?>