<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class LogbookRecord extends Model
{

    /**
     *
     * logbook id
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
    protected $facility_id;

    /**
     * department id
     * @var int 
     */
    protected $department_id = null;

    /**
     *
     * inspection person id
     * 
     * @var int 
     */
    protected $inspection_person_id;

    /**
     *
     * inspection type
     * 
     * @var string 
     */
    protected $inspection_type;

    /**
     *
     * inspection sub type
     * 
     * @var string 
     */
    protected $inspection_sub_type;

    /**
     *
     * logbook description
     * 
     * @var string 
     */
    protected $description;

    /**
     *
     *  time in unix type
     * 
     * @var int
     */
    protected $date_time;
    /* Addition fields */

    /**
     *
     * Addition field - inspection type permit
     * 
     * @var boolean
     */
    protected $permit = null;

    /**
     *
     * Addition field - inspection sub type qty
     * 
     * @var int
     */
    protected $qty = null;

    /**
     *
     * Addition field - loogbook description notes
     * 
     * @var string
     */
    protected $description_notes = null;

    /**
     *
     * @var string
     */
    protected $sub_type_notes = null;
    
    //check for additio fields
    /**
     *
     * if logbook has Permit
     * 
     * @var boolean 
     */
    protected $hasPermit = 0;
    
    /**
     *
     * if logbook has qty
     * 
     * @var boolean 
     */
    protected $hasQty = 0;
    
     /**
     *
     * if logbook has description notes
     * 
     * @var boolean 
     */
    protected $hasDescriptionNotes = 0;
    
     /**
     *
     * if logbook has sub types notes
     * 
     * @var boolean 
     */
    protected $hasSubTypeNotes = 0;

    const TABLE_NAME = 'logbook_record';
    const FILENAME = '/modules/classes/VWM/Apps/Logbook/Resources/inspectionTypes.json';

    public function __construct($id = null)
    {
        $this->modelName = "LogbookRecord";
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

    public function getInspectionPersonId()
    {
        return $this->inspection_person_id;
    }

    public function setInspectionPersonId($inspection_person_id)
    {
        $this->inspection_person_id = $inspection_person_id;
    }

    public function getInspectionType()
    {
        return $this->inspection_type;
    }

    public function setInspectionType($inspection_type)
    {
        $this->inspection_type = $inspection_type;
    }

    public function getInspectionSubType()
    {
        return $this->inspection_sub_type;
    }

    public function setInspectionSubType($inspection_sub_type)
    {
        $this->inspection_sub_type = $inspection_sub_type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDateTime()
    {
        return $this->date_time;
    }

    public function setDateTime($date_time)
    {
        $this->date_time = $date_time;
    }

    public function getPermit()
    {
        return $this->permit;
    }

    public function setPermit($permit)
    {
        $this->permit = $permit;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    public function getDescriptionNotes()
    {
        return $this->description_notes;
    }

    public function setDescriptionNotes($description_notes)
    {
        $this->description_notes = $description_notes;
    }

    public function getSubTypeNotes()
    {
        return $this->sub_type_notes;
    }

    public function setSubTypeNotes($sub_type_notes)
    {
        $this->sub_type_notes = $sub_type_notes;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }

    public function getDepartmentId()
    {
        return $this->department_id;
    }

    public function setDepartmentId($department_id)
    {
        $this->department_id = $department_id;
    }

    public function getHasPermit()
    {
        return $this->hasPermit;
    }

    public function setHasPermit($hasPermit)
    {
        $this->hasPermit = $hasPermit;
    }

    public function getHasQty()
    {
        return $this->hasQty;
    }

    public function setHasQty($hasQty)
    {
        $this->hasQty = $hasQty;
    }

    public function getHasDescriptionNotes()
    {
        return $this->hasDescriptionNotes;
    }

    public function setHasDescriptionNotes($hasDescriptionNotes)
    {
        $this->hasDescriptionNotes = $hasDescriptionNotes;
    }

    public function getHasSubTypeNotes()
    {
        return $this->hasSubTypeNotes;
    }

    public function setHasSubTypeNotes($hasSubTypeNotes)
    {
        $this->hasSubTypeNotes = $hasSubTypeNotes;
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
        //initialize addition fields
        $this->getAvailableLogbookAdditionFields();
    }

    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $qty = $this->getQty();
        $departmentId = $this->getDepartmentId();
        $subTipesNotes = $this->getSubTypeNotes();
        $dateTime = $this->getDateTime();
        $descriptionNotes = $this->getDescriptionNotes();

        if (is_null($qty)) {
            $qty = 'NULL';
        }
        if (is_null($departmentId)) {
            $departmentId = 'NULL';
        }
        if (is_null($dateTime)) {
            $dateTime = time();
        }
        if (is_null($subTipesNotes)) {
            $subTipesNotes = 'NULL';
        }
        if (is_null($descriptionNotes)) {
            $descriptionNotes = 'NULL';
        }

        $sql = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "departmet_id = {$db->sqltext($departmentId)}, " .
                "inspection_type = '{$db->sqltext($this->getInspectionType())}', " .
                "inspection_sub_type = '{$db->sqltext($this->getInspectionSubType())}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description = '{$db->sqltext($this->getDescription())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "permit = {$db->sqltext($this->getPermit())}, " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "qty = {$db->sqltext($qty)}";

        $db->query($sql);
        $id = $db->getLastInsertedID();
        $this->setId($id);

        return $id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $qty = $this->getQty();
        $departmentId = $this->getDepartmentId();
        $subTipesNotes = $this->getSubTypeNotes();
        $dateTime = $this->getDateTime();
        $descriptionNotes = $this->getDescriptionNotes();

        if (is_null($qty)) {
            $qty = 'NULL';
        }
        if (is_null($departmentId)) {
            $departmentId = 'NULL';
        }
        if (is_null($dateTime)) {
            $dateTime = time();
        }
        if (is_null($subTipesNotes)) {
            $subTipesNotes = 'NULL';
        }
        if (is_null($descriptionNotes)) {
            $descriptionNotes = 'NULL';
        }

        $sql = "UPDATE " . self::TABLE_NAME . " SET " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "departmet_id = {$db->sqltext($departmentId)}, " .
                "inspection_type = '{$db->sqltext($this->getInspectionType())}', " .
                "inspection_sub_type = '{$db->sqltext($this->getInspectionSubType())}', " .
                "inspection_person_id = {$db->sqltext($this->getInspectionPersonId())}, " .
                "date_time = {$db->sqltext($dateTime)}, " .
                "description = '{$db->sqltext($this->getDescription())}', " .
                "description_notes = '{$db->sqltext($descriptionNotes)}', " .
                "permit = {$db->sqltext($this->getPermit())}, " .
                "sub_type_notes = '{$db->sqltext($subTipesNotes)}', " .
                "qty = {$db->sqltext($qty)} ".
                "WHERE id={$db->sqltext($this->getId())}";

        $db->query($sql);
        return $this->getId();
    }

    /*
     * redefine abstract method
     */

    public function getAttributes()
    {
        
    }

    /**
     * 
     *  Getting addition fields of logbook from json file
     * 
     * @return boolean
     */
    public function getAvailableLogbookAdditionFields($inspectionTypeName = null, $inspectionSubTypeName = null, $inspectionDescriptionName = null)
    {
        if(is_null($inspectionTypeName)){
            $inspectionTypeName = $this->getInspectionType();
        }
        if(is_null($inspectionSubTypeName)){
            $inspectionSubTypeName = $this->getInspectionSubType();
        }
        if(is_null($inspectionDescriptionName)){
            $inspectionDescriptionName = $this->getDescription();
        }
        /*get current directory*/
        $path = getcwd();
        /*get file content*/
        $json = file_get_contents($path . self::FILENAME);
        $typeList = json_decode($json);
        /*get inspection type*/
        foreach($typeList->inspectionTypes as $type){
            if ($type->typeName == $inspectionTypeName){
                $inspectionType = $type;
                break;
            }
        }
        
        /*get inspection sub type*/
        foreach($inspectionType->subtypes as $subtype){
            if($subtype->name == $inspectionSubTypeName){
                $inspectionSubType = $subtype;
                break;
            }
        }
        
        foreach ($typeList->description as $description){
            if($description->name == $inspectionDescriptionName){
                $inspectionDescription = $description;
                break;
            }
        }
        /*set addition fields available*/
        $this->setHasPermit($inspectionType->permit);
        $this->setHasQty($inspectionSubType->qty);
        $this->setHasSubTypeNotes($inspectionSubType->notes);
        $this->setHasDescriptionNotes($inspectionDescription->notes);
        
        return true;
    }

}
?>