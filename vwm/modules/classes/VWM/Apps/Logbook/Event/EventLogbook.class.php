<?php

namespace VWM\Apps\Logbook\Event;

use Symfony\Component\EventDispatcher\Event;
use VWM\Apps\Logbook\Entity\LogbookPendingRecord;

class EventLogbook extends Event
{
    /**
     *
     * Logbook Pending Record
     *
     * @var VWM\Apps\Logbook\Entity\LogbookPendingRecord
     */
    protected $logbookPendingRecord;

    public function __construct(Reminder $logbookPendingRecord)
    {
        $this->logbookPendingRecord = $logbookPendingRecord;
    }

    /**
     *
     * get reminder
     *
     * @return VWM\Apps\Reminder\Entity\Reminder
     */
    public function getLogbookPendingRecord()
    {
        return $this->logbookPendingRecord;
    }
}
?>
