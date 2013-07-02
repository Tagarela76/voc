<?php

namespace VWM\Apps\Logger;

use VWM\Framework\App;

/**
 * Logger responsible for Logger
 */
class LoggerApp extends App
{
     public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}
?>
