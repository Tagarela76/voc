<?php

return array(
    'dbLogger' => function($c) {
        $log = new \VWM\Apps\Logger\Logger('db logger');
        $dir = dirname(__FILE__) . '/../../../../../../tmp/logs/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        $log->pushHandler(new Monolog\Handler\StreamHandler($dir.'db_logger.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    }
);