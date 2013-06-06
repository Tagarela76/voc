<?php

namespace VWM\Apps\Logbook\Entity;

use \VWM\Framework\Model;

class InspectionTypeSettings extends Model
{

    /**
     *
     * @var string 
     */
    protected $typeName;

    /**
     *
     * @var bool 
     */
    protected $permit;
    
    /**
     *
     * @var array 
     */
    protected $subtypes = array();
    
    /**
     *
     * @var array
     */
    protected $additionFieldList = array();

    public function __construct($id = null)
    {
        $this->modelName = "InspectionTypeSettings";
    }
    
    public function getTypeName()
    {
        return $this->typeName;
    }

    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }

    public function getPermit()
    {
        return $this->permit;
    }

    public function setPermit($permit)
    {
        $this->permit = $permit;
    }

    public function getSubtypes()
    {
        return $this->subtypes;
    }

    public function setSubtypes($subtypes)
    {
        $this->subtypes = $subtypes;
    }

    public function getAdditionFieldList()
    {
        return $this->additionFieldList;
    }

    public function setAdditionFieldList($additionFieldList)
    {
        $this->additionFieldList = $additionFieldList;
    }

    public function getAttributes()
    {
        $inspectionTypeSettings = array();
        $inspectionTypeSettings['typeName'] = $this->getTypeName();
        $inspectionTypeSettings['permit'] = $this->getPermit();
        
        $subTypes = $this->getSubtypes();
        if(!empty($subTypes) &&  !is_null($subTypes)){
            $inspectionTypeSettings['subtypes'] = $subTypes;
        }
        
        $additionFieldList = $this->getAdditionFieldList();
        if(!empty($additionFieldList) &&  !is_null($additionFieldList)){
            $inspectionTypeSettings['additionFieldList'] = $additionFieldList;
        }
        
        return $inspectionTypeSettings;
    }
}
?>
