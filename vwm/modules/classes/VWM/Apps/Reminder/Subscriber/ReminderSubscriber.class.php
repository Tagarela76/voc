<?php

namespace VWM\Apps\Reminder\Subscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VWM\Apps\Reminder\VWMReminderEvents\ReminderEvents;
use VWM\Apps\Reminder\Event\EventReminderUser; 
use VWM\Apps\User\VWMUserEvents\UserEvents;

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
    
    /**
     * 
     * add reminder User
     * 
     * @param \VWM\Apps\Reminder\Event\EventReminderUser $event
     */
    public function addReminderUser(EventReminderUser $event)
    {
        $reminderUser = $event->getReminderUser();
        $reminderUser->save();
    }
    
    /**
     * 
     * delete reminder User
     * 
     * @param \VWM\Apps\Reminder\Event\EventReminderUser $event
     */
    public function deleteReminderUser(EventReminderUser $event)
    {
        $reminderUser = $event->getReminderUser();
        $reminderUser->delete();
    }


    public static function getSubscribedEvents()
    {
        return array(
            ReminderEvents::REMINDER_SENT => array(
                array('setNextReminderTime'),
            ),
            ReminderEvents::BEFOREHAND_REMINDER_SENT => array(
                array('setNextBeforeReminderTime'),
            ),
            UserEvents::SAVE_USER => array(
                array('addReminderUser')
            ),
            UserEvents::DELETE_USER => array(
                array('deleteReminderUser')
            )
        );
    }

}

