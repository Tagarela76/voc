<?php

namespace VWM\Apps\Logger\Manager;

use Monolog\Logger;

class LoggerManager extends Logger
{
    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addDebug($message, array $context = array())
    {
        if (VOC_DEBUG) {
            return parent::addDebug($message, $context);
        }
    }
    /**
     * Adds a log record at the INFO level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addInfo($message, array $context = array())
    {
        if (VOC_DEBUG) {
            return parent::addInfo($message, $context);
        }
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addNotice($message, array $context = array())
    {
        if (VOC_DEBUG) {
            return parent::addNotice($message, $context);
        }
    }
}
?>
