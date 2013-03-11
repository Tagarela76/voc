<?php

namespace VWM\Apps\Calendar\Entity;

use VWM\Framework\Test\DbTestCase;

class CalendarEventTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_USER, TB_CALENDAR
	);
	
	public function testCalendarEvent() {
		
		$calendarEvent = new CalendarEvent($this->db, '1');
		$this->assertTrue($calendarEvent instanceof CalendarEvent);
		
	}
	
	public function testCalendarEventSave() {
		
		$calendarEvent = new CalendarEvent($this->db);
		$calendarEvent->setTitle("testEvent5");
		$calendarEvent->setDescription("testDesc5");
		$calendarEvent->setEventDate("32323");
		$calendarEvent->setAuthorId("1");
		$expectedId = 5;
		$result = $calendarEvent->save();
		
		$this->assertEquals($expectedId, $result);	// last id
		
		$myTest = \Phactory::get(TB_CALENDAR, array('title'=>"testEvent5"));
		$this->assertTrue($myTest->id == '5');

		$sql = "SELECT * FROM " . TB_CALENDAR . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$calendarEventActual = new CalendarEvent($this->db);
		$calendarEventActual->initByArray($row);
		$calendarEventActual->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		$this->assertEquals($calendarEvent, $calendarEventActual);
		
		// check UPDATE
		
		 $calendarEventUpdated = new CalendarEvent($this->db, '1');
		 $newTitle = "newTitle";
		 $calendarEventUpdated->setTitle($newTitle);
		 $calendarEventUpdated->save();
		 $calendarEventUpdatedTest = \Phactory::get(TB_CALENDAR, array('id'=>"1"));		
		 $this->assertTrue($calendarEventUpdatedTest->title == $newTitle);
		
	}
	
	public function testCalendarEventDelete() {
		
		$calendarEvent = new CalendarEvent($this->db, '1');
		$calendarEvent->delete();
		$calendarEventDeleted = new CalendarEvent($this->db, '1');
		$this->assertTrue(is_null($calendarEventDeleted->title));
	}

}

?>
