<?php

use VWM\Calendar\Calendar;
use VWM\Calendar\CalendarEvent;
use \VWM\Calendar\CalendarEventManager;

class CSCalendar extends Controller {
	
	function CSCalendar($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='calendar';
		$this->parent_category='calendar';		
	}
	
	function runAction() {		
		$this->runCommon('sales');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function actionBrowseCategory() {
		
		$user = new User($this->db);
		$userId = $user->getLoggedUserID();	
		$this->smarty->assign('userId', $userId);

		$phpCalendar = new Calendar(); 
		$phpCalendar->setSmarty($this->smarty); 
		
		$calendarEventManager = new CalendarEventManager($this->db);
		$userCalendarEvents = $calendarEventManager->getAllEventsByUser($userId);
		$calendarEventManager->setUserCalendarEvents($userCalendarEvents);

		$phpCalendar->setCalendarEventManager($calendarEventManager); 
		
		$phpCalendarTpl = $phpCalendar->getCalendar(); 

		$this->smarty->assign('phpCalendarTpl', $phpCalendarTpl);	
		
		//	set js scripts
		$jsSources = array(
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
			"modules/js/calendarSettings.js"
		);

		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array(
			'modules/js/phpcalendar.css',
			'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		// inject tooltip
		$libraryInjection = new LibraryInjection($this->smarty);
		$libraryInjection->injectToolTip(); 
		
		$this->smarty->assign('tpl', 'tpls/calendar.tpl');
		$this->smarty->display("tpls:index.tpl");
		
               
	}
	

	protected function actionOpenEventWindow() {

		$action = $this->getFromRequest('calendarAction');	
		if ($action == 'add') {
			$timestamp = $this->getFromRequest('timestamp');
			$this->smarty->assign('timestamp', $timestamp);
		} else {
			$eventId = $this->getFromRequest('eventId');
			$calendarEvent = new CalendarEvent($this->db, $eventId);
			$this->smarty->assign('data', $calendarEvent);
			$this->smarty->assign('timestamp', $calendarEvent->getEventDate());
		}
		echo $this->smarty->fetch('tpls/addCalendarEvent.tpl');
    }

    protected function actionAddUpdateEvent() {

		$calendarEvent = new CalendarEvent($this->db, $this->getFromRequest('eventId'));
		$calendarEvent->setTitle($this->getFromRequest('title'));
		$calendarEvent->setDescription($this->getFromRequest('description'));
		$calendarEvent->setEventDate($this->getFromRequest('timestamp'));
		$calendarEvent->setAuthorId($this->getFromRequest('userId'));
		
		$violationList = $calendarEvent->validate();	
			if(count($violationList) == 0) {  
				$calendarEvent->save();
				$result = '';
			} else {											
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $calendarEvent);
				$result = $this->smarty->fetch("tpls/addCalendarEvent.tpl");
			}
		echo $result;	
				
    }
	
	protected function actionDeleteEvent() {

		$calendarEvent = new CalendarEvent($this->db, $this->getFromRequest('eventId'));
		$calendarEvent->delete();		
    }
}