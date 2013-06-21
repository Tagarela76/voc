<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class LogbookDescription extends Model
{

    /**
     *
     * logbook Description id
     * 
     * @var int 
     */
    protected $id;

    /**
     *
     * description
     * 
     * @var string 
     */
    protected $description;

    /**
     *
     * has or no notes
     * 
     * @var boolean 
     */
    protected $notes = 0;
    
    /**
     *
     * logbook description type
     * 
     * @var string =  inspection_type
     */
    private $origin = 'inspection_type';

    /**
     *
     * inspection type id
     * 
     * @var int 
     */
    protected $inspection_type_id;

    /**
     * Description table name
     */
    const TABLE_NAME = 'logbook_description';
    
    /**
     * 
     * get id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 
     * set id
     * 
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * set description
     * 
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 
     * get notes
     * 
     * @return boolean
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     *
     * set notes
     *  
     * @param boolean $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * 
     * get inspection type id
     * 
     * @return int
     */
    public function getInspectionTypeId()
    {
        return $this->inspection_type_id;
    }

    /**
     * 
     * set inspection type id
     * 
     * @param int $inspection_type_id
     */
    public function setInspectionTypeId($inspection_type_id)
    {
        $this->inspection_type_id = $inspection_type_id;
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
        $this->modelName = "LogbookDescription";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    /**
     * 
     * initialize class
     * 
     * @return boolean
     */
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
        );
    }
    
    /**
     * delete logbook description
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');

        if (is_null($this->getId())) {
            return false;
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " " .
                "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($query);
    }

}
?>
