<?php

namespace VWM\Apps\Logbook\Entity;
use \VWM\Framework\Model;

class LogbookCustomDescription extends LogbookDescription
{
    /**
     *
     * facility_id;
     * 
     * @var int 
     */
    protected $facility_id;
    
     /**
     *
     * logbook description type
     * 
     * @var string =  inspection_type
     */
    private $origin = 'custom_description';
    
    /**
     * 
     * get facility id
     * 
     * @return int
     */
    public function getFacilityId()
    {
        return $this->facility_id;
    }

    /**
     * 
     * set facility id
     * 
     * @param int $facility_id
     */
    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }
    
    /**
     * 
     * get logbook description type
     * 
     * @return string
     */
    private function getOrigin()
    {
        return $this->origin;
    }

    /**
     * 
     * set logbook description type
     * 
     * @param string $origin
     */
    private function setOrigin($origin)
    {
        $this->origin = $origin;
    }
    
    public function __construct()
    {
        $this->modelName = "LogbookCustomDescription";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }
    
    /**
     * 
     * save insert function
     * 
     * @return boolean|int
     */
    public function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "description = '{$db->sqltext($this->getDescription())}', " .
                "notes = {$db->sqltext($this->getNotes())}, " .
                "origin = '{$db->sqltext($this->getOrigin())}', " .
                "facility_id = '{$db->sqltext($this->getFacilityId())}', " .
                "inspection_type_id = {$db->sqltext($this->getInspectionTypeId())} ";
                
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
                "description = '{$db->sqltext($this->getDescription())}', " .
                "notes = {$db->sqltext($this->getNotes())}, " .
                "origin = '{$db->sqltext($this->getOrigin())}', " .
                "facility_id = '{$db->sqltext($this->getFacilityId())}', " .
                "inspection_type_id = {$db->sqltext($this->getInspectionTypeId())} ".
                "WHERE id={$db->sqltext($this->getId())}";
        $response = $db->exec($query);
        $id = $db->getLastInsertedID();
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
            'description' => $this->getDescription(),
            'notes' => $this->getNotes(),
            'origin' => $this->getOrigin(),
            'inspection_type_id' => $this->getInspectionTypeId(),
            'facility_id' => $this->getFacilityId()
        );
    }
    
}
?>
