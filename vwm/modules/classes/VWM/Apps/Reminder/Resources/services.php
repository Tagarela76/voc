<?php

return array(
    'reminder' => function($c) {
        return new VWM\Apps\Reminder\Manager\ReminderManager();
    },
);