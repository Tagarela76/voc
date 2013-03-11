<?php

namespace VWM\Apps\Calendar;

use VWM\Apps\Calendar\Manager\CalendarEventManager;

/**
 * VOC WEB MANAGER Calendar
 */
class Calendar extends PHPCalendar 
{
	
	/**
	 *
	 * @var \Smarty
	 */
	protected $smarty;
	
	/**
	 *
	 * @var CalendarEventManager
	 */
	protected $calendarEventManager;
	
    /**
     * @return \Smarty
     */
	public function getSmarty() 
    {
		return $this->smarty;
	}

    /**
     * @parent \Smarty
     */
	public function setSmarty(\Smarty $smarty) 
    {
		$this->smarty = $smarty;
	}

    /**
     * @return CalendarEventManager
     */
	public function getCalendarEventManager() 
    {
		return $this->calendarEventManager;
	}

    /**
     * @param CalendarEventManager
     */
	public function setCalendarEventManager(CalendarEventManager $calendarEventManager) 
    {
		$this->calendarEventManager = $calendarEventManager;
	}

    /**
     * Get HTML code for the calendar
     *
     * @return string HTML code ready to use
     */
	public function getCalendar() 
    {
		parent::getCalendar();

		$userCalendarEvents = $this->getCalendarEventManager()->getUserCalendarEvents();		
		// this piece i cannot add in tpl(((
        $dayAsInt = 0;
		$pieceOfCalendar = ''; 
        foreach ($this->calArray as $dayAsStr) {			
            $dayAsInt++;
            // Highlight today
            $currentDay = date('n/' . $dayAsInt . '/Y', $this->timestamp);
            $class = '';
            if ($currentDay == date('n/j/Y')) { 
                $class = 'class="today"'; 
            } else {
                $class = '';
            } 
			// collect events
			$calendarEvents = '';
			foreach ($userCalendarEvents  as $userCalendarEvent) {
				if ($userCalendarEvent->event_date == strtotime($currentDay)) {
					$calendarEvents .= "<a href='#event_" . $userCalendarEvent->id . "' " .
							"title='" . $userCalendarEvent->title . "'" .
							"onclick='calendarPage.calendarUpdateEvent.openDialog(".$userCalendarEvent->id .");' >" . 
							$userCalendarEvent->title . 
							"</a><br>";
					$calendarEvents .= "<span style='display: none;' ". 
							"id=event_" . $userCalendarEvent->id . 
							"><p>$userCalendarEvent->title</p>" .
							"<p>$userCalendarEvent->description</p></span>";
				}
			}
            // Set the actual calendar squares, hyperlinked to their timestamps
            $pieceOfCalendar .= 
                '<td><div ' . $class . '>' . $dayAsInt . '<br>' . $calendarEvents .
				'<span class="addEvent" onclick="calendarPage.calendarAddEvent.openDialog('.strtotime($currentDay).');">'.
				'<img src="images/add.png" />'.
				'</span></div></td>';

            // Our calendar has Saturday as the last day of the week,
            // so we'll wrap to a newline after every SAT
            if ($dayAsInt != $this->daysInMonth && $dayAsStr == 'Sat') {
                $pieceOfCalendar .= '</tr><tr>';
            }
        }
		
		$this->smarty->assign("navButtonBackwardTimestamp",$this->getNavButtonTimestamp('backward'));
		$this->smarty->assign("navButtonBackwardDate",$this->getNavButtonDate('backward'));
		$this->smarty->assign("currentMonthName",$this->getCurrentMonthName());
		$this->smarty->assign("year",$this->year);
		$this->smarty->assign("navButtonForwardTimestamp",$this->getNavButtonTimestamp('forward'));
		$this->smarty->assign("navButtonForwardDate",$this->getNavButtonDate('forward'));
		$this->smarty->assign("days",$this->days);
		$this->smarty->assign("dayMonthBegan",$this->dayMonthBegan);
		$this->smarty->assign("pieceOfCalendar",$pieceOfCalendar);
		
		$result = $this->smarty->fetch("tpls/phpCalendar.tpl");

		return $result;
	}

	protected function getNavButtonTimestamp($direction) 
    {
		$when = $direction == 'forward' ? '+1 month' : '-1 month';
		$timestamp = $this->getFirstOfMonth(strtotime($when, $this->timestamp));

		return $timestamp;
	}
	
	protected function getNavButtonDate($direction) 
    {
		$when = $direction == 'forward' ? '+1 month' : '-1 month';
		$date = date("M", strtotime($when, $this->timestamp));

		return $date;      
	}
}

