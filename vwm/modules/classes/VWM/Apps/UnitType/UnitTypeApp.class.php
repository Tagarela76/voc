<?php

namespace VWM\Apps\UnitType;

use VWM\Framework\App;

/**
 * Reminder responsible for Logbook
 */
class UnitTypeApp extends App
{
    public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}
