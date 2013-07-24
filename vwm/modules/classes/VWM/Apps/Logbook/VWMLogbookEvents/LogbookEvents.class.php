<?php

namespace VWM\Apps\Logbook\VWMLogbookEvents;

final class LogbookEvents
{
    /**
     * event apear when we save logbook from recurring one
     * We need delete pending logbook
     */
    const EDIT_RECURRING_LOGBOOK = 'vwm.apps.logbook.event.logbookevent.editrecurringlogbook';
    const ADD_PENDING_LOGBOOK = 'vwm.apps.logbook.event.logbookevent.addpendinglogbook';

}
