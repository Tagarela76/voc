<?php

namespace VWM\Apps\Logbook\Entity;
use \VWM\Framework\Model;

class InspectionGaugeTypeSettings extends Model
{
    /**
     *
     * @var string 
     */
    protected $name;

    /**
     *
     * @var int 
     */
    protected $gaugeType;

    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getGaugeType()
    {
        return $this->gaugeType;
    }

    public function setGaugeType($gaugeType)
    {
        $this->gaugeType = $gaugeType;
    }

    public function __construct($id = null)
    {
        $this->modelName = "InspectionGaugeTypeSettings";
    }
    
    public function getAttributes()
    {
        $gaugeType = new \stdClass();
        $gaugeType->name = $this->getName();
        $gaugeType->gaugeType = $this->getGaugeType();
            
        return $gaugeType;
    }

}
?>
