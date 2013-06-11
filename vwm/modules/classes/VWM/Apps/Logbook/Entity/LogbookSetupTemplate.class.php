<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;
use \VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;

class LogbookSetupTemplate extends Model
{

    /**
     *
     * template Id
     * 
     * @var int 
     */
    protected $id;

    /**
     *
     * template name
     * 
     * @var string 
     */
    protected $name;
    
    protected $facilityIds = array();

    const TABLE_NAME = 'logbook_setup_template';

    public function __construct($id = null)
    {
        $this->modelName = "LogbookSetupTemplate";
        if (isset($id)) {
            $this->setId($id);
            $this->load();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 
     * get logbookSetupTemplate facility ids
     * 
     * @return boolean| int[]
     */
    public function getFacilityIds()
    {
        if (!empty($this->facilityIds)) {
            return $this->facilityIds;
        }
        
        if(is_null($this->getId())){
            return false;
        }
        
        $ltManager = new LogbookSetupTemplateManager();
        $facilityList = $ltManager->getFacilityListByLogbookSetupTemplateId($this->getId());
        $facilityIds = array();
        foreach($facilityList as $facility){
            $facilityIds[] = $facility->getId();
        }
        
        return $facilityIds;
    }

    public function setFacilityIds($facilityIds)
    {
        $this->facilityIds = $facilityIds;
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
    /*
     * redefine abstract method
     */

    public function getAttributes()
    {
        
    }

    public function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "name = '{$db->sqltext($this->getName())}'";
        $db->query($query);
        $id = $db->getLastInsertedID();
        $this->setId($id);

        return $id;
    }

    public function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "UPDATE " . self::TABLE_NAME . " SET " .
                "name = '{$db->sqltext($this->getName())}' " .
                "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($query);
        return $this->getId();
    }
    
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if(is_null($this->getId())){
            throw new Exception('Can\'t delete. No such id' );
        }
        $query = "DELETE FROM ".  self::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($this->getId())}";
        $db->query($query);
        $ltManager = new LogbookSetupTemplateManager();
        $ltManager->unAssignLogbookTemplateFromFacility($this->getId());
        
    }

}
?>
