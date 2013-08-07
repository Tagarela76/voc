<?php

namespace VWM\Apps\Reminder\Event;

use Symfony\Component\EventDispatcher\Event;
use VWM\Apps\Reminder\Entity\ReminderUser;

class EventReminderUser extends Event
{
    protected $reminderUser;
    
    public function __construct(ReminderUser $reminderUser)
    {
        $this->reminderUser = $reminderUser;
    }

    /**
     *
     * get reminder
     *
     * @return VWM\Apps\Reminder\Entity\ReminderUser
     */
    public function getReminderUser()
    {
        return $this->reminderUser;
    }
}
?>
