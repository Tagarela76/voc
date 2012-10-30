<?php
namespace VWM\Calendar;
use VWM\Framework\Model;

class CalendarEvent extends Model {
	
	/**
	 *
	 * @var int
	 */
	protected $id;
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var int
	 */
	protected $event_date;
	
	/**
	 *
	 * @var int
	 */
	protected $author_id;
	
	/**
	 *
	 * @var datetime
	 */
	protected $last_update_time;
	
	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

		
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}
	
	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getEventDate() {
		return $this->event_date;
	}

	public function setEventDate($event_date) {
		$this->event_date = $event_date;
	}

	public function getAuthorId() {
		return $this->author_id;
	}

	public function setAuthorId($author_id) {
		$this->author_id = $author_id;
	}

	function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'CalendarEvent';
		if (isset($id)) {
			$this->setId($id);
			$this->_load();
		}		
	}
	
	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM " . TB_CALENDAR . " ".
				"WHERE id={$this->db->sqltext($this->id)} " . 
				"LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * INSERT OR UPDATE EVENT
	 * @return int
	 */
	public function save() {		
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getId() ) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
	/**
	 * Insert new event
	 * @return boolean
	 */
	private function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "INSERT INTO ".TB_CALENDAR." (" .
				"title, description, event_date, author_id, last_update_time" .
				") VALUES ( ".
				"'{$this->db->sqltext($this->getTitle())}', " .
				"'{$this->db->sqltext($this->getDescription())}', " .
				"{$this->db->sqltext($this->getEventDate())}, " .
				"{$this->db->sqltext($this->getAuthorId())}, " .
				"{$lastUpdateTime} " .
				")";
		$response = $this->db->exec($sql);
		if($response) {
			$this->setId($this->db->getLastInsertedID());	
			return $this->getId();
		} else {
			return false;
		}
		
		
	}
	
	/**
	 * Update event
	 * @return boolean
	 */
	private function _update() {				
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".TB_CALENDAR." SET " .
				"title='{$this->db->sqltext($this->getTitle())}', " .
				"description='{$this->db->sqltext($this->getDescription())}', " .
				"event_date={$this->db->sqltext($this->getEventDate())}, " .
				"author_id={$this->db->sqltext($this->getAuthorId())}, " .				
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";	
		
		$response = $this->db->exec($sql);
		if($response) {			
			return $this->getId();
		} else {
			return false;
		}
	}		
	
	/**
	 * Delete event
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_CALENDAR . "
				 WHERE id={$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
	}
}

?>
