<?php

return array(
    'dbLogger' => function($c) {
        $log = new \VWM\Apps\Logger\Manager\LoggerManager('db logger');
        $log->pushHandler(new Monolog\Handler\StreamHandler(dirname(__FILE__) . '../../../../../../../tmp/logs/db_error.log', \VWM\Apps\Logger\Manager\LoggerManager::DEBUG));
        return $log;
    }
);