<?php

namespace VWM\Apps\Reminder\VWMReminderEvents;

final class ReminderEvents
{
    /**
     * event apear when we sent remind to users
     * We need check and update delivery time
     */
    const REMINDER_SENT = 'vwm.apps.reminder.event.reminderevent.remindersent';
    
    const BEFOREHAND_REMINDER_SENT = 'vwm.apps.reminder.event.reminderevent.beforehandremindersent';

}
