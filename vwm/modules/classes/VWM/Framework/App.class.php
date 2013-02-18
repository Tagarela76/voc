<?php

namespace VWM\Framework;

/**
 * App is equal to app in Django, equal to bundle in Symfony
 */
abstract class App
{

    public function __construct()
    {
    }

    /**
     * Adds app's services to the VOCApp service container
     */
    protected function addServices($appPath)
    {
        $appServices = require_once $appPath.'/Resources/services.php';
        foreach ($appServices as $name => $class) {
            \VOCApp::getInstance()->addSharedService($name, $class);
        }
    }
}