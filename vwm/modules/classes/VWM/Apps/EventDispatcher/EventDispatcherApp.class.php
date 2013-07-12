<?php

namespace VWM\Apps\EventDispatcher;

use VWM\Framework\App;

class EventDispatcherApp extends App
{
     public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}

?>
