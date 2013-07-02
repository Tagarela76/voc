<?php

return array(
    'dbLogger' => function($c) {
    $log = new Monolog\Logger('db logger');
    $log->pushHandler(new Monolog\Handler\StreamHandler(dirname(__FILE__).'../../../../../../../tmp/logs/db_error.log', Monolog\Logger::WARNING));
        return $log;
    }
  
);