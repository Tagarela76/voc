<?php

use VWM\Framework\Model;

class Reminder extends Model {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var string 
	 */
	public $name;
	
	/**
	 * reminder date
	 * @var int 
	 */
	public $date;
	
	/**
	 *
	 * @var int 
	 */
	public $facility_id;
	
	/**
	 *
	 * @var string 
	 */
	public $url;
	
	/**
	 *
	 * @var array 
	 */
	public $users;
	
	/**
	 *
	 * @var object
	 */
	public $email;


	function __construct(db $db, $id = null, EMail $email = null) {
		$this->db = $db;
		$this->modelName = 'Reminder';
		if (isset($id)) {
			$this->id = $id;
			$this->_load();
		}
		if (!isset($this->users)) {
			$this->loadUsers();
		}
		if (isset($email)) {
			$this->email = $email;
		}
	}
	
	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM " . TB_REMINDER . " ".
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
	 * Insert or Update Reminder
	 * @return int 
	 */
	public function save() {

		if (!isset($this->id)) {
			$id = $this->add();
		} else {
			$id = $this->update();
		}
		return $id;
	}
	
	/**
	 * Add reminder and return inserted reminder id
	 * @return int 
	 */
	public function add() {

		$query = "INSERT INTO " . TB_REMINDER . "(name, date, facility_id) 
				VALUES ( 
				'{$this->db->sqltext($this->name)}'
				, {$this->db->sqltext($this->date)}
				, {$this->db->sqltext($this->facility_id)}		
				)";
		$this->db->query($query); 
		$id = $this->db->getLastInsertedID();
		$this->id = $id;
		return $id;
	}
	
	/**
	 * Update reminder and return updated reminder id
	 * @return int 
	 */
	public function update() {

		$query = "UPDATE " . TB_REMINDER . "
					set name='{$this->db->sqltext($this->name)}',
						date={$this->db->sqltext($this->date)},
						facility_id={$this->db->sqltext($this->facility_id)}			
					WHERE id={$this->db->sqltext($this->id)}";
		$this->db->query($query);

		return $this->id;
	}
	
	/**
	 * Delete reminder 
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_REMINDER . "
				 WHERE id={$this->db->sqltext($this->id)}";
		$this->db->query($sql);
	}
	
	public function isUniqueName() {
		$sql = "SELECT id FROM " . TB_REMINDER . "
				 WHERE name = '{$this->db->sqltext($this->name)}' " .
				"AND facility_id = {$this->db->sqltext($this->facility_id)}";
		$this->db->query($sql);
		return ($this->db->num_rows() == 0) ? true : false;
	}
	
	/**
	 *
	 * @return boolean 
	 */
	private function loadUsers() { 
		
		if (!isset($this->users)) {
			$sql = "SELECT u.user_id, u.username, u.email ".
					"FROM " . TB_USER . " u" .
					" LEFT JOIN " . TB_REMIND2USER . " r2u ON r2u.user_id = u.user_id " .
					"WHERE r2u.reminders_id={$this->db->sqltext($this->id)}";
			$this->db->query($sql);

			if ($this->db->num_rows() == 0) {
				return false;
			} else {
				$this->users = $this->db->fetch_all_array();
			} 
		}
	}
	
	public function getUsers() {
		return $this->users;
	}

	public function setRemind2User($userId) { 
		
        $sql = "SELECT * FROM " . TB_REMIND2USER . "
				 WHERE user_id={$this->db->sqltext($userId)}
                  AND reminders_id={$this->db->sqltext($this->id)}";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
            $query = "INSERT INTO " . TB_REMIND2USER . "(user_id, reminders_id) 
                    VALUES ({$this->db->sqltext($userId)}, {$this->db->sqltext($this->id)})";
            $this->db->query($query);
        }       
	}
    
    public function unSetRemind2User($userId) {

		$sql = "DELETE FROM " . TB_REMIND2USER . "
				 WHERE user_id={$this->db->sqltext($userId)}
                  AND reminders_id={$this->db->sqltext($this->id)}";
		$this->db->query($sql);
	}
	
	public function sendRemind() {

        $to = array();
		$users = $this->getUsers();
		foreach ($users as $user) {
			$to[] = $user["email"];
		}
  	 	
    	$from = REMIND_SENDER."@".DOMAIN;
    	$theme = "Notification ";
		$message = $this->name;

		if (count($to) != 0) {
			$this->email->sendMail($from, $to, $theme, $message);
		}
    }
}

?>