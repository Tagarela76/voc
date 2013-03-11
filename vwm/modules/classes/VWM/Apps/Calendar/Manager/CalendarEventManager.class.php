<?php

namespace VWM\Apps\Calendar\Manager;

use VWM\Apps\Calendar\Entity\CalendarEvent;

class CalendarEventManager 
{

	/**
	 *
	 * @var \db
	 */
	private $db;
	
	/**
	 *
	 * @var array of CalendarEvent
	 */
	protected $userCalendarEvents;
	
	public function getUserCalendarEvents() 
    {
		return $this->userCalendarEvents;
	}

	public function setUserCalendarEvents($userCalendarEvents) 
    {
		$this->userCalendarEvents = $userCalendarEvents;
	}

	public function __construct(\db $db) 
    {
		$this->db = $db;
	}

	public function getAllEventsByUser($userId) 
    {		
		
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
			$calendarEvent->setId($row['id']);
			$calendarEvent->setTitle($row['title']);
			$calendarEvent->setDescription($row['description']);
			$calendarEvent->setAuthorId($row['author_id']);
			$calendarEvent->setEventDate($row['event_date']);

			$calendarEvents[] = $calendarEvent;
		}
		return $calendarEvents;
	}
}
