<?php

namespace VWM\Apps\Reminder\Event;

use Symfony\Component\EventDispatcher\Event;
use VWM\Apps\Reminder\Entity\Reminder;

class EventReminder extends Event
{
    /**
     *
     * reminder
     *
     * @var VWM\Apps\Reminder\Entity\Reminder
     */
    protected $reminder;

    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     *
     * get reminder
     *
     * @return VWM\Apps\Reminder\Entity\Reminder
     */
    public function getReminder()
    {
        return $this->reminder;
    }
}
?>
