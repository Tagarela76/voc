<?php

namespace VWM\Calendar;

use VWM\Calendar\CalendarEvent;

class CalendarEventManager {

	/**
	 *
	 * @var \db
	 */
	private $db;
	
	public function __construct(\db $db) {
		$this->db = $db;
	}

	public function getAllEventsByUser($userId) {		
		
		$sql = "SELECT * ".
				"FROM " . TB_CALENDAR . " ".
				"WHERE author_id={$this->db->sqltext($userId)}";
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$calendarEvents = array();
		foreach ($rows as $row) {
			$calendarEvent = new CalendarEvent($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($calendarEvent, $key)) {
					$calendarEvent->$key = $value;
				}
			}
			$calendarEvents[] = $calendarEvent;
		}
		return $calendarEvents;
	}
}
?>
