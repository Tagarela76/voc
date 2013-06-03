<?php
namespace VWM\Apps\Logbook\Entity;

class LogbookInspectionTypeSetting
{
    /**
     *
     * name of inspection type
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
    protected $subtypes ;
    /**
     *
     * @var array 
     */
    protected $additionFieldList;
}
?>
