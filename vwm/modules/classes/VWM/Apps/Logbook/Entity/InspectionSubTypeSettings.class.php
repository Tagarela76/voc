<?php

namespace VWM\Apps\Logbook\Entity;
use \VWM\Framework\Model;

class InspectionSubTypeSettings extends Model
{

    /**
     *
     * @var string 
     */
    protected $name;

    /**
     *
     * @var bool 
     */
    protected $qty;

    /**
     *
     * @var bool 
     */
    protected $notes;

    /**
     *
     * @var bool 
     */
    protected $valueGauge;
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getValueGauge()
    {
        return $this->valueGauge;
    }

    public function setValueGauge($valueGauge)
    {
        $this->valueGauge = $valueGauge;
    }

    
    public function __construct($id = null)
    {
        $this->modelName = "InspectionSubTypeSettings";
    }
    
    public function getAttributes()
    {
        $subType = new \stdClass();
        $subType->name = $this->getName();
        $subType->notes = $this->getNotes();
        $subType->qty = $this->getQty();
        $subType->valueGauge = $this->getValueGauge();
        
        return $subType;
    }

}
?>
