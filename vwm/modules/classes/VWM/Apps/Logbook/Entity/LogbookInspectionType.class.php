<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;
use \VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;
use \VWM\Apps\Logbook\Manager\LogbookDescriptionManager;

class LogbookInspectionType extends Model
{
    /**
     *
     * @var int
     */
    protected $id;

    /**
     * 
     * var int[]
     */

    protected $templateIds = array();
    
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

    public function getTemplateIds()
    {
        if (!empty($this->templateIds)) {
            return $this->$templateIds;
        }
        if(is_null($this->getId())){
            return false;
        }
        $itManager = new InspectionTypeManager();
        $ltManager = new LogbookSetupTemplateManager();
        
        $templates = $ltManager->getLogbookTemplateListByInspectionTypeId($this->getId());
        
        $templatesIds = array();
        foreach($templates as $template){
            $templatesIds[] = $template->getId();
        }
        
        if (count($templatesIds)>1){
            return implode(',', $templatesIds);
        }
        return $templatesIds[0];
        
    }

    public function setTemplateIds($templateIds)
    {
        $this->templateIds = $templateIds;
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
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "settings = '{$db->sqltext($this->getInspectionTypeRaw())}'";
        $db->query($query);
        $id = $db->getLastInsertedID();
        $this->setId($id);
        
        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "UPDATE " . self::TABLE_NAME . " SET " .
                "settings = '{$db->sqltext($this->getInspectionTypeRaw())}' ".
                "WHERE id={$db->sqltext($this->getId())}";
        $db->query($query);
        return $this->getId();
    }

    /*
     * redefine abstract method
     */

    public function getAttributes()
    {
        return array(
            'id' => $this->getId(),
            'templatesId' => $this->getTemplateIds(),
            'settings' => $this->getInspectionTypeRaw()
        );
    }

    /**
     * delete inspection Person
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $ldManaget = \VOCApp::getInstance()->getService('logbookDescription');
        $itManager = \VOCApp::getInstance()->getService('inspectionType');
        //delete inspection Type
        $query = "DELETE FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->getId())}";
        $db->query($query);
        /*
        //delete inspection type to template connection
        $itManager->unAssignInspectionTypeFromInspectionTemplate($this->getId());
        //delete logbook description
        $ldManager->deleteDescriptionsByInspectionTypeId($this->getId());
        //delete custom logbook description
        $ldManager->deleteCustomDescriptionByInspectionTypeId($this->getId());*/
    }
    
}
?>