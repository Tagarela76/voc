<?php

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
	
	protected function actionBrowseCategory($vars) {
		extract($vars);
		$calendarManager = new CalendarManager($this->db);
		$user = new User($this->db);
		$userID = $user->getLoggedUserID();

		$week = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
		$year = date('Y') ;
		$month = date("m");
		$day = date('j');
		$firstday = date("w", mktime(12,0,0,$month,1,$year));
		$nr = date("t",mktime(12,0,0,$month,1,$year));
		$nr++;
		$firstday++;		

		for($i=1;$i<$nr;$i++){
			$result = $calendarManager->getEvents($i, $month, $year,$userID);
			if ($result){
				$events[$i] = $result;
			}
		}

		$categoryList = $calendarManager->getCategory();
		var_dump($categoryList);
		//	set js scripts
		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js','modules/js/checkBoxes.js','modules/js/addEventPopups.js', 'extensions/calendar/js/coda.js');
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('week', $week);
		$this->smarty->assign('firstday', $firstday);
		$this->smarty->assign('nr', $nr);
		$this->smarty->assign('month', $month);
		$this->smarty->assign('day', $day);
		$this->smarty->assign('year', $year);		
	
		$this->smarty->assign('events', $events);	
		$this->smarty->assign('categoryList', $categoryList);	
		
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl', 'tpls/calendar.tpl');
		$this->smarty->display("tpls:index.tpl");
		
               
	}
	

	private function actionViewDetails() {

	}
	
	private function actionEdit() {
		

	}
	
	private function actionAddItem() {		
		

	}
	
	private function actionDeleteItem() {

	}
	
	private function actionConfirmDelete() {

	}

        

	
	
}