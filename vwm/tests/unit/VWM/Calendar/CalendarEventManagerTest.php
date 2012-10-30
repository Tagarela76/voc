<?php

namespace VWM\Calendar;

use VWM\Framework\Test\DbTestCase;
use VWM\Calendar\CalendarEvent;
use VWM\Calendar\CalendarEventManager;

class CalendarEventManagerTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_CALENDAR
	);
	
	public function testGetAllEventsByUser() {
		
		$calendarEventManager = new CalendarEventManager($this->db);
		$userId = 1;
		$calendarEvents = $calendarEventManager->getAllEventsByUser($userId);
		$this->assertTrue($calendarEvents[0] instanceof CalendarEvent);
		$this->assertTrue(count($calendarEvents) == 2);
		$this->assertTrue(is_array($calendarEvents));
		
	}

}

?>
