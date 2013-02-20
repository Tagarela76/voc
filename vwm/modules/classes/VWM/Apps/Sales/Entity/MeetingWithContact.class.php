<?php

namespace VWM\Apps\Sales\Entity;

use VWM\Framework\Model;
use VWM\Apps\Sales\Manager\SalesContactsManager;
use VWM\Framework\Utils\DateTime;

class MeetingWithContact extends Model {

	protected $id;
	protected $contact_id;
	protected $user_id;
	protected $meeting_date;
	protected $notes;
	protected $last_update_time;

	/**
	 * @var SalesContact
	 */
	protected $contact;

	/**	 
	 * @var \stdClass
	 */
	protected $user;

	/**
	 * @var SalesContactsManager
	 */
	protected $salesManager;

	const TABLE_NAME = 'meeting_with_contact';

	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = "MeetingWithContact";

		$this->salesManager = new SalesContactsManager($this->db);
		
		if($id !== null) {
			$this->setId($id);
			if(!$this->_load()) {
				throw new Exception('404');
			}
		}
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getContactId() {
		return $this->contact_id;
	}

	public function setContactId($contact_id) {
		$this->contact_id = $contact_id;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}

	public function getMeetingDate($formatted = false) {
		if($formatted) {
			$datetime = new DateTime();
			$datetime->setTimestamp($this->meeting_date);
			
			return $datetime->format(
					\VOCApp::getInstance()->getDateFormat()." H:i");
		}
		return $this->meeting_date;
	}

	public function setMeetingDate($meeting_date) {
		$this->meeting_date = $meeting_date;
	}

	public function getNotes() {
		return $this->notes;
	}

	public function setNotes($notes) {
		$this->notes = $notes;
	}

	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}


	protected function _insert() {
		$notes = ($this->getNotes())
				? "'{$this->db->sqltext($this->getNotes())}'"
				: "NULL";
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
		
		$sql = "INSERT INTO ".self::TABLE_NAME." " .
				"(contact_id, user_id, meeting_date, notes, last_update_time) VALUES (" .
				"{$this->db->sqltext($this->getContactId())}, " .
				"{$this->db->sqltext($this->getUserId())}, " .
				"{$this->db->sqltext($this->getMeetingDate())}, " .
				"{$notes}, " .
				"{$lastUpdateTime} )";
		if(!$this->db->exec($sql)) {
			return false;
		}

		$this->setId($this->db->getLastInsertedID());
		return $this->getId();
	}

	protected function _update() {
		$notes = ($this->getNotes())
				? "'{$this->db->sqltext($this->getNotes())}'"
				: "NULL";
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";

		$sql = "UPDATE ".self::TABLE_NAME." SET " .
				"contact_id = {$this->db->sqltext($this->getContactId())}, " .
				"user_id = {$this->db->sqltext($this->getUserId())}, " .
				"meeting_date = {$this->db->sqltext($this->getMeetingDate())}, " .
				"notes = {$notes}, " .
				"last_update_time = {$lastUpdateTime} " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		if(!$this->db->exec($sql)) {
			return false;
		}
		
		return $this->getId();
	}

	protected function _load() {
		if(!$this->getId()) {
			throw new \Exception('Meeting ID should be set before calling this method');
		}

		$sql = "SELECT * FROM ".self::TABLE_NAME." " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}

		$row = $this->db->fetch_array(0);
		$this->initByArray($row);

		return true;
	}

	public function getContact() {
		if(!$this->contact) {
			$contact = $this->salesManager->getSalesContact($this->getContactId());
			$this->contact = $contact;
		}

		return $this->contact;
	}

	public function getUser() {
		if(!$this->user) {
			$userDetails = \VOCApp::getInstance()
					->getUser()
					->getUserDetails($this->getUserId());
			$this->user = new \stdClass();
			$this->user->username = $userDetails['username'];
		}

		return $this->user;
	}
}

?>
