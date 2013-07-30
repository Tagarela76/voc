<?php

namespace VWM\Apps\Reminder\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VWM\Apps\Reminder\VWMReminderEvents\ReminderEvents;

use VWM\Apps\Reminder\Entity\Reminder;

class BeforehandReminderSubscriber implements EventSubscriberInterface
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
    public function setNextBeforeReminderTime($event)
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        //get Reminder
        $reminder = $event->getReminder();
        
        $beforeReminderDate = $reminder->getBeforehandReminderDate();
        
        if ($beforeReminderDate != 0) {
            // get over that period will be re-sent reminder
            $periodicity = $reminder->getPeriodicity();
            $beforeReminderDate = $rManager->getNextRemindDate($periodicity, $beforeReminderDate);
        }
        
        $reminder->setBeforehandReminderDate($beforeReminderDate);
        $reminder->save();

    }

    public static function getSubscribedEvents()
    {
        return array(
            ReminderEvents::SEND_BEFOREHAND_REMINDER => array(
                array('setNextBeforeReminderTime'),
            ),
        );
    }

}

