<?php

use VWM\Framework\Model;

class ReminderManager extends Model {

	function __construct(db $db) {
		$this->db = $db;
		$this->modelName = 'ReminderManager';
	}

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

	public function getReminders() {

		$reminders = array();
		$sql = "SELECT * ".
				"FROM " . TB_REMINDER;
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch_all_array();

		foreach ($rows as $row) {
			$reminder = new Reminder($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($reminder, $key)) {
					$reminder->$key = $value;
				}
			}
			$reminders[] = $reminder;
		}
		return $reminders;
	}
}

?>