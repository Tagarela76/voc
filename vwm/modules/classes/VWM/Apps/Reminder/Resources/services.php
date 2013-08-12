<?php

return array(
    'reminder' => function($c) {
        return new VWM\Apps\Reminder\Manager\ReminderManager();
    },
    'reminderUser' => function($c) {
        return new VWM\Apps\Reminder\Manager\ReminderUserManager();
    },
);