<?php
namespace VWM\Apps\Reminder\Event;
use \Symfony\Component\EventDispatcher\Event;
use \VWM\Apps\Reminder\Entity\Reminder;

class EventReminder extends Event
{
    protected $reminder;
    
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }
    
    public function getReminder()
    {
        return $this->reminder;
    }
}
?>
