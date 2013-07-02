<?php

namespace VWM\Apps\Logger;

use \VWM\Framework\App;

/**
 * Logbook responsible for Logbook
 */
class LoggerApp extends App
{
     public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}
?>
