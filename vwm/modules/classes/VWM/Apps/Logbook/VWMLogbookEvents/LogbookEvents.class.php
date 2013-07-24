<?php

namespace VWM\Apps\Logbook\VWMLogbookEvents;

final class LogbookEvents
{
    /**
     * event apear when we save logbook from recurring one
     * We need delete padding logbook
     */
    const SAVE_LOGBOOK = 'vwm.apps.logbook.event.logbookerevent.deletependinglogbook';

}
