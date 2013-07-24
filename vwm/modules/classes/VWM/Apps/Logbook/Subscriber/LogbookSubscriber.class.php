<?php

namespace VWM\Apps\Logbook\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VWM\Apps\Logbook\VWMLogbookEvents\LogbookEvents;


class LogbookSubscriber implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    /**
     *
     * event for recurce updating reminder delivery time
     *
     * @param EventReminder $event
     */
    public function deletePendingLogbook($event)
    {
        //get Logbook Pending Record
        $logbookPendingRecord = $event->getLogbookPendingRecord();
        $logbookPendingRecord->delete();
    }

    public static function getSubscribedEvents()
    {
        return array(
            LogbookEvents::SAVE_LOGBOOK => array(
                array('deletePendingLogbook'),
            ),
        );
    }

}

