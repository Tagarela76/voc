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
     * event for creating logbook from recurring logbook
     *
     * @param EventReminder $event
     */
    public function deletePendingLogbook($event)
    {
        //get Logbook Pending Record
        $logbookPendingRecord = $event->getLogbookPendingRecord();
        $logbookPendingRecord->delete();
    }
    
    /**
     *
     * event for recurce updating  recurring logbook
     *
     * @param EventReminder $event
     */
    public function deleteAllLogbookPendingRecordByParentId($event)
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        //get Logbook Pending Record
        $logbookPendingRecord = $event->getLogbookPendingRecord();
        
        $lbManager->deleteAllLogbookPendingRecordByParentId($logbookPendingRecord->getParentId());
        
    }

    public static function getSubscribedEvents()
    {
        return array(
            LogbookEvents::EDIT_RECURRING_LOGBOOK => array(
                array('deleteAllLogbookPendingRecordByParentId'),
            ),
            LogbookEvents::ADD_PENDING_LOGBOOK => array(
                array('deletePendingLogbook'),
            ),
        );
    }

}

