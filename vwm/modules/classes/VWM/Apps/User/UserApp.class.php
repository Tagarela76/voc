<?php

namespace VWM\Apps\User;

use \VWM\Framework\App;

/**
 * Logbook responsible for Logbook
 */
class UserApp extends App
{
    public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}
?>
