<?php

namespace VWM\Apps\Reminder;

use \VWM\Framework\App;

/**
 * Reminder responsible for Logbook
 */
class ReminderApp extends App
{
    public function __construct()
    {
        $this->addServices(dirname(__FILE__));
    }
}

?>
