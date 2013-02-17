<?php

use VWM\Apps\Sales\Manager\SalesContactsManager;
use VWM\Apps\Sales\Entity\MeetingWithContact;
use VWM\Framework\Utils\DateTime;

class CSMeetingWithContact extends Controller {

	public function __construct($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='meetingWithContact';
		$this->parent_category='contacts';
	}

	public function runAction() {
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	public function actionAdd() {		
		$meeting = new MeetingWithContact($this->db);
		$meeting->setUserId($this->xnyo->user['user_id']);
		$meeting->setContactId($this->getFromRequest('contactId'));

		if($this->getFromPost()) {
			$meeting->setMeetingDate($this->getFromPost('meeting_date'));
			$meeting->setNotes($this->getFromPost('notes'));
			$violationList = $meeting->validate();
			if(count($violationList) == 0) {
				$format = \VOCApp::getInstance()->getDateFormat();
				$format .= " H:i";
				$meetingDate = DateTime::createFromFormat($format,
						$meeting->getMeetingDate());

				$meeting->setMeetingDate($meetingDate->getTimestamp());
				if(!$meeting->save()) {
					throw new Exception("Failed to save meeting");
				}
				header("Location: sales.php?action=viewDetails&category=contacts&id=".$meeting->getContactId());
			} else {				
				$this->smarty->assign('violationList', $violationList);
			}
		}

		$this->smarty->assign('meeting', $meeting);
		
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		$typeChain = new TypeChain(null, 'date', $this->db, 0, 'company');
		$this->smarty->assign('dataChain', $typeChain);

		$this->smarty->assign('tpl', 'tpls/addMeetingWithContact.tpl');
		$this->smarty->assign('request', $this->getFromRequest());
		$this->render();
	}


	public function actionEdit() {
		$meeting = new MeetingWithContact($this->db, $this->getFromRequest('id'));

		if($this->getFromPost()) {
			$meeting->setMeetingDate($this->getFromPost('meeting_date'));
			$meeting->setNotes($this->getFromPost('notes'));
			$violationList = $meeting->validate();
			if(count($violationList) == 0) {
				$format = \VOCApp::getInstance()->getDateFormat();
				$format .= " H:i";
				$meetingDate = DateTime::createFromFormat($format,
						$meeting->getMeetingDate());

				$meeting->setMeetingDate($meetingDate->getTimestamp());
				if(!$meeting->save()) {
					throw new Exception("Failed to save meeting");
				}
				header("Location: sales.php?action=viewDetails&category=contacts&id=".$meeting->getContactId());
			} else {
				var_dump($violationList);
				$this->smarty->assign('violationList', $violationList);
			}
		}

		$this->smarty->assign('meeting', $meeting);

		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		$typeChain = new TypeChain(null, 'date', $this->db, 0, 'company');
		$this->smarty->assign('dataChain', $typeChain);
		
		$this->smarty->assign('tpl', 'tpls/addMeetingWithContact.tpl');
		$this->render();
	}
}

