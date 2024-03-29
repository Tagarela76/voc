<?php

return array(
    'dbLogger' => function($c) {
        $log = new \VWM\Apps\Logger\Logger('db logger');
        $dir = dirname(__FILE__) . '/../../../../../../tmp/logs/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if (!file_exists($dir.'db_logger.log')) {
            $fp = fopen($dir.'db_logger.log', "a+");
            fclose($fp);
            chmod($dir.'db_logger.log', 0777);
        }
        $log->pushHandler(new Monolog\Handler\StreamHandler($dir.'db_logger.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    },
    'errorLogger'=> function($c) {
        $log = new \VWM\Apps\Logger\Logger('error logger');
        $dir = dirname(__FILE__) . '/../../../../../../tmp/logs/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if (!file_exists($dir.'error_logger.log')) {
            $fp = fopen($dir.'error_logger.log', "a+");
            fclose($fp);
            chmod($dir.'error_logger.log', 0777);
        }
        $log->pushHandler(new Monolog\Handler\StreamHandler($dir.'error_logger.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    },
    'reminderLogger'=> function($c) {
        $log = new \VWM\Apps\Logger\Logger('send reminder logger');
        $dir = dirname(__FILE__) . '/../../../../../../tmp/logs/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if (!file_exists($dir.'reminder_logger.log')) {
            $fp = fopen($dir.'reminder_logger.log', "a+");
            fclose($fp);
            chmod($dir.'error_logger.log', 0777);
        }
        $log->pushHandler(new Monolog\Handler\StreamHandler($dir.'reminder_logger.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    },
    'mixLogger'=> function($c) {
        $log = new \VWM\Apps\Logger\Logger('mix logger');
        $dir = dirname(__FILE__) . '/../../../../../../tmp/logs/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if (!file_exists($dir.'mix_logger.log')) {
            $fp = fopen($dir.'mix_logger.log', "a+");
            fclose($fp);
            chmod($dir.'error_logger.log', 0777);
        }
        $log->pushHandler(new Monolog\Handler\StreamHandler($dir.'mix_logger.log', \VWM\Apps\Logger\Logger::DEBUG));
        return $log;
    }
);