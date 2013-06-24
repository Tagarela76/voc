<?php

namespace VWM\Apps\Reminder\Entity;

use VWM\Framework\Model;

class Reminder extends Model
{

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
     * @var Email
     */
    public $email;

    const TABLE_NAME = 'reminder';

    function __construct($id = null, EMail $email = null)
    {
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

    /**
     * @return array property => value
     */
    public function getAttributes()
    {
        return array(
            'id'            => $this->id,
            'name'          => $this->name,
            'date'          => $this->date,
            'facility_id'   => $this->facility_id,
            'url'           => $this->url,
        );
    }

    private function _load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (!isset($this->id)) {
            return false;
        }
        $sql = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($this->id)} " .
                "LIMIT 1";
        $db->query($sql);

        if ($db->num_rows() == 0) {
            return false;
        }
        $row = $db->fetch(0);
        $this->initByArray($row);
    }


    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . self::TABLE_NAME . " (name, date, facility_id) VALUES ( " .
				"'{$this->db->sqltext($this->name)}' " .
				", {$this->db->sqltext($this->date)} " .
				", {$this->db->sqltext($this->facility_id)} " .
				")";
        $db->exec($query);
        $this->id = $this->db->getLastInsertedID();

        return $this->id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "UPDATE " . self::TABLE_NAME . " " .
				" SET name = '{$this->db->sqltext($this->name)}', " .
                " date = {$this->db->sqltext($this->date)}, " .
				" facility_id = {$this->db->sqltext($this->facility_id)} " .
				"WHERE id = {$this->db->sqltext($this->id)}";
        $db->exec($query);

        return $this->id;
    }

    /**
     * Delete reminder
     */
    public function delete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM " . self::TABLE_NAME . "
				 WHERE id={$db->sqltext($this->id)}";

        $db->exec($sql);
    }

    /**
     * @return boolean
     */
    public function isUniqueName()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT id FROM " . self::TABLE_NAME . "
				 WHERE name = '{$this->db->sqltext($this->name)}' " .
                "AND facility_id = {$this->db->sqltext($this->facility_id)}";
        $db->query($sql);

        return ($db->num_rows() == 0) ? true : false;
    }

    /**
     *
     * @return boolean
     */
    private function loadUsers()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (!isset($this->users)) {
            $sql = "SELECT u.user_id, u.username, u.email " .
                    "FROM " . TB_USER . " u" .
                    " LEFT JOIN " . TB_REMIND2USER . " r2u ON r2u.user_id = u.user_id " .
                    "WHERE r2u.reminders_id={$db->sqltext($this->id)}";
            $db->query($sql);

            if ($db->num_rows() == 0) {

                return false;
            } else {
                $this->users = $this->db->fetch_all_array();

                return true;
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     *
     * @param type array
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * set user to remind
     * @param type int
     */
    public function setRemind2User($userId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . TB_REMIND2USER . "(user_id, reminders_id)
				VALUES ({$db->sqltext($userId)}, {$db->sqltext($this->id)})";
        $db->query($query);
    }

    /**
     * unset all users from remind
     */
    public function unSetRemind2User()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM " . TB_REMIND2USER . "
				 WHERE reminders_id={$db->sqltext($this->id)}";
        $db->query($sql);
    }

    /**
     *
     */
    public function sendRemind()
    {
        $to = array();
        $users = $this->getUsers();
        foreach ($users as $user) {
            $to[] = $user["email"];
        }

        $from = REMIND_SENDER . "@" . DOMAIN;
        $theme = "Notification ";
        $message = $this->name;

        if (count($to) != 0) {
            $this->email->sendMail($from, $to, $theme, $message);
        }
    }

    /**
     *
     * @return boolean
     */
    public function isAtLeastOneUserSelect()
    {
        return (count($this->users) != 0) ? true : false;
    }

}
