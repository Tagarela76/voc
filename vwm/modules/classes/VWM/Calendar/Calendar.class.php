<?php

namespace VWM\Calendar;
use VWM\Calendar\CalendarEventManager;

class Calendar extends PHPCalendar {
	
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
	
	public function getSmarty() {
		return $this->smarty;
	}

	public function setSmarty(\Smarty $smarty) {
		$this->smarty = $smarty;
	}

	public function getCalendarEventManager() {
		return $this->calendarEventManager;
	}

	public function setCalendarEventManager(CalendarEventManager $calendarEventManager) {
		$this->calendarEventManager = $calendarEventManager;
	}

	public function getCalendar() {
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
		
		$this->smarty->assign("navButtonBackward",$this->getNavButton('backward'));
		$this->smarty->assign("currentMonthName",$this->getCurrentMonthName());
		$this->smarty->assign("year",$this->year);
		$this->smarty->assign("navButtonForward",$this->getNavButton('forward'));
		$this->smarty->assign("days",$this->days);
		$this->smarty->assign("dayMonthBegan",$this->dayMonthBegan);
		$this->smarty->assign("pieceOfCalendar",$pieceOfCalendar);
		
		$result = $this->smarty->fetch("tpls/phpCalendar.tpl");
		return $result;
	}
	
	protected function getNavButton($direction) {
        $when = $direction == 'forward' ? '+1 month' : '-1 month';

        return '<a class="cal-nav-buttons" href="?action=browseCategory&category=calendar&timestamp=' .
            $this->getFirstOfMonth(
                strtotime($when, $this->timestamp)) . '">' . 
                date("M", strtotime($when, $this->timestamp)) . '</a>';
    }
}

?>
