<?php
namespace VWM\Apps\EventDispatcher;

final class StoreEvents
{
    /**
     * event apear when we sent remind to users
     * We need check and update delivery time
     */
    const SET_NEXT_REMINDER_TIME = 'vwm.apps.reminder.event.reminderevent.setnextremindertime';
    
}
?>
