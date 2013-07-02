<?php

return array(
    'dbLogger' => function($c) {
        $log = new \VWM\Apps\Logger\Logger('db logger');
        $log->pushHandler(new Monolog\Handler\StreamHandler(dirname(__FILE__) . '../../../../../../../tmp/logs/db_error.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    }
);