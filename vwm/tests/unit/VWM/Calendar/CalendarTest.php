<?php

namespace VWM\Calendar;

use VWM\Framework\Test\DbTestCase;
use VWM\Calendar\Calendar;

class CalendarTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_CALENDAR
	);
	
	public function testGetCalendar() {
		
		 $phpCalendar = new Calendar(); 
		 var_dump($phpCalendar);
		 echo $phpCalendar->getCalendar();
		
	}

}

?>
