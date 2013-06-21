<?php

return array(
    'inspectionType' => function($c) {
        return new \VWM\Apps\Logbook\Manager\InspectionTypeManager();
    },
    'logbookSetupTemplate' => function($c) {
        return new \VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager();
    },
    'logbook' => function($c) {
        return new \VWM\Apps\Logbook\Manager\LogbookManager();
    },
    'logbookDescription' => function($c) {
        return new \VWM\Apps\Logbook\Manager\LogbookDescriptionManager();
    }
);