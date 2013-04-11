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
    /*Addition fields*/
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

    const TABLE_NAME = 'logbook_record';
    
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
}
?>