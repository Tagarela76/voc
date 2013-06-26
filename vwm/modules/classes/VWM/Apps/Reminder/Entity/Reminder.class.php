<?php

namespace VWM\Apps\Reminder\Entity;

use VWM\Framework\Model;

class Reminder extends Model
{

    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     * reminder date
     * @var int
     */
    protected $date;

    /**
     *
     * @var int
     */
    protected $facility_id;

    /**
     *
     * @var string
     */
    protected $url;

    /**
     *
     * @var array
     */
    protected $users;

    /**
     *
     * @var Email
     */
    protected $email;

    const TABLE_NAME = 'reminder';
    const TB_REMIND2USER = 'remind2user';
    
    function __construct($id = null, EMail $email = null)
    {
        $this->modelName = 'Reminder';

        if (isset($id)) {
            $this->id = $id;
            $this->load();
        }

        if (!isset($this->users)) {
            $this->loadUsers();
        }

        if (isset($email)) {
            $this->email = $email;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return array property => value
     */
    public function getAttributes()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'facility_id' => $this->facility_id,
            'url' => $this->url,
        );
    }

    public function load()
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
                "'{$db->sqltext($this->name)}' " .
                ", {$db->sqltext($this->date)} " .
                ", {$db->sqltext($this->facility_id)} " .
                ")";
        $db->exec($query);
        $this->id = $db->getLastInsertedID();
        
        return $this->id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "UPDATE " . self::TABLE_NAME . " " .
                " SET name = '{$db->sqltext($this->name)}', " .
                " date = {$db->sqltext($this->date)}, " .
                " facility_id = {$db->sqltext($this->facility_id)} " .
                "WHERE id = {$db->sqltext($this->id)}";
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
				 WHERE name = '{$db->sqltext($this->name)}' " .
                "AND facility_id = {$db->sqltext($this->facility_id)}";
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
        $users = array();
        if(!is_null($this->users)){
            return $this->users;
        }
        $rManager = \VOCApp::getInstance()->getService('reminder');
        if(is_null($this->getId())){
            return false;
        }
        $users = $rManager->getUsersByReminderId($this->getId());
        
        return $users;
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
     *
     * @return boolean
     */
    public function isAtLeastOneUserSelect()
    {
        return (count($this->users) != 0) ? true : false;
    }

}
