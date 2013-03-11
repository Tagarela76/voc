<?php

namespace VWM\Apps\Calendar\Manager;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Calendar\Entity\CalendarEvent;

class CalendarEventManagerTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_USER, TB_CALENDAR
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
