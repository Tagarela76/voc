<?php

namespace VWM\Apps\Reminder\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VWM\Apps\Reminder\VWMReminderEvents\ReminderEvents;

use VWM\Apps\Reminder\Entity\Reminder;

class ReminderSubscriber implements EventSubscriberInterface
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
    public function setNextReminderTime($event)
    {
        $rManager = \VOCApp::getInstance()->getService('reminder');
        //get Reminder
        $reminder = $event->getReminder();

        $currentDate = $reminder->getDeliveryDate();

        //check for delivety day. It cant be null
        if(is_null($currentDate)){
            $currentDate = $reminder->getDate();
        }

        // get over that period will be re-sent reminder
        $periodicity = $reminder->getPeriodicity();
        $deliveryDate = $rManager->getNextRemindDate($periodicity, $currentDate);

        $reminder->setDeliveryDate($deliveryDate);
        $reminder->save();

    }

    public static function getSubscribedEvents()
    {
        return array(
            ReminderEvents::SET_NEXT_REMINDER_TIME => array(
                array('setNextReminderTime'),
            ),
        );
    }

}

