<?php

namespace VWM\Apps\Logbook;

use \VWM\Framework\App;

/**
 * Logbook responsible for Logbook
 */
class LogbookApp extends App
{
    public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}

?>
