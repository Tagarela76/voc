<?php

namespace VWM\Apps\Reminder\Entity;

use VWM\Framework\Model;

class ReminderUser extends Model
{

    /**
     *
     * reminder user id
     * 
     * @var int 
     */
    protected $id = null;

    /**
     *
     * user id
     * 
     * @var int 
     */
    protected $user_id = null;

    /**
     *
     * user email
     * 
     * @var string 
     */
    protected $email = null;
    
    /**
     *
     * facility_id
     * 
     * @var int
     */
    protected $facility_id;

    const TABLE_NAME = 'reminder_user';

    function __construct($id = null)
    {
        $this->modelName = 'ReminderUser';

        if (isset($id)) {
            $this->id = $id;
            $this->load();
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

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facility_id)
    {
        $this->facility_id = $facility_id;
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

    /**
     * @return array property => value
     */
    public function getAttributes()
    {
        return array(
            'id' => $this->getId(),
            'email' => $this->getEmail()
        );
    }

    /**
     * add reminder user 
     */
    public function _insert()
    {
        $userId = $this->getUserId();
        $userId = is_null($userId)?'NULL':$userId;
        
        $email = $this->getEmail();
        $email = is_null($email)?'NULL':$email;
        
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "user_id = {$db->sqltext($userId)}, " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "email = '{$db->sqltext($email)}' ";
        $db->query($sql);
        $id = $db->getLastInsertedID();

        if (isset($id)) {
            $this->setId($id);
        } else {
            return false;
        }
        return $this->getId();
    }

    /**
     * update reminder user
     */
    public function _update()
    {
        $userId = $this->getUserId();
        $userId = is_null($userId)?'NULL':$userId;
        
        $email = $this->getEmail();
        $email = is_null($email)?'NULL':$email;
        
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "UPDATE " . self::TABLE_NAME . " SET " .
                "user_id = {$db->sqltext($userId)}, " .
                "facility_id = {$db->sqltext($this->getFacilityId())}, " .
                "email = '{$db->sqltext($email)}' " .
                "WHERE id = {$db->sqltext($this->getId())}";
        $db->query($sql);

        return $this->getId();
    }
    
    public function _delete()
    {
        
    }

}
?>
