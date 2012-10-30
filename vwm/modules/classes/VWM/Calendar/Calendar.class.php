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
		
		$dayAsInt = 0;
		$classesForCurrentDays = array();
        foreach ($this->calArray as $dayAsStr) { 
            $dayAsInt++;
            $currentDay = date('n/' . $dayAsInt . '/Y', $this->timestamp);
            $class = '';
            if ($currentDay == date('n/j/Y')) { 
                $class = 'class="today"'; 
            } else {
                $class = '';
            }
			$classesForCurrentDay["class"] = $class;
			$classesForCurrentDay["currentDays"] = $currentDay;
			$classesForCurrentDays[] = $classesForCurrentDay;
        }

		$this->smarty->assign("classesForCurrentDays",$classesForCurrentDays);
		
		$this->smarty->assign("navButtonBackward",$this->getNavButton('backward'));
		$this->smarty->assign("currentMonthName",$this->getCurrentMonthName());
		$this->smarty->assign("year",$this->year);
		$this->smarty->assign("navButtonForward",$this->getNavButton('forward'));
		$this->smarty->assign("days",$this->days);
		$this->smarty->assign("dayMonthBegan",$this->dayMonthBegan);
		$this->smarty->assign("daysInMonth",$this->daysInMonth);
		$result = $this->smarty->fetch("tpls/calendar.tpl");
		echo $result;
	}
}

?>
