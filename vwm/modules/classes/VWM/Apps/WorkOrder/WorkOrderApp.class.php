<?php

namespace VWM\Apps\WorkOrder;

use \VWM\Framework\App;

/**
 * WorkOrderApp responsible for WorkOrders, Mixes, Pfps
 */
class WorkOrderApp extends App
{
    public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}

?>
