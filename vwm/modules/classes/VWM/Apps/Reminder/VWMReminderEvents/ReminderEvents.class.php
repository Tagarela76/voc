<?php

namespace VWM\Apps\Reminder\VWMReminderEvents;

final class ReminderEvents
{
    /**
     * 
     * event apear when we sent remind to users
     * 
     */

    const REMINDER_SENT = 'vwm.apps.reminder.event.reminderevent.remindersent';
    /**
     * 
     * event apear when we sent beforehand reminder to user
     * 
     */
    const BEFOREHAND_REMINDER_SENT = 'vwm.apps.reminder.event.reminderevent.beforehandremindersent';

}
