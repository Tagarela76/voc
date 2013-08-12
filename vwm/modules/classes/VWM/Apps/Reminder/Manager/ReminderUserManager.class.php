<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Apps\Reminder\Entity\ReminderUser;
use VWM\Apps\User\Entity\User;
use VWM\Apps\Reminder\Entity\Reminder;

class ReminderUserManager
{
    const TABLE_NAME = 'reminder2reminder_user';
    /**
     * all users
     */
    const ALL_USERS = 'all';
    /**
     * users wich registered in VOC
     */
    const REGISTERED_USERS = 'registered';
    /**
     * users unregistered in VOC
     */
    const UNREGISTERED_USERS = 'unregistered';
    /**
     * 
     * set reminder to reminder user
     * 
     * @param int $userId
     * @param int $reminderId
     * 
     * @return int
     */
    public function setReminder2ReminderUser($reminderUserId, $reminderId)
    {
        if (is_null($reminderUserId) || is_null($reminderId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');

        $sql = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "reminder_id = {$db->sqltext($reminderId)}, " .
                "reminder_user_id = {$db->sqltext($reminderUserId)}";
        $db->query($sql);
        $id = $db->getLastInsertedID();

        return $id;
    }

    /**
     * 
     * get Reminder Users by Reminder
     * 
     * @param int $reminderId
     * @param \Pagination $pagination
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\ReminderUser[]
     */
    public function getReminderUsersByReminderId($reminderId, \Pagination $pagination = null)
    {
        if (is_null($reminderId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT reminder_user_id FROM " . self::TABLE_NAME . " " .
                "WHERE reminder_id = {$db->sqltext($reminderId)}";
        if (isset($pagination)) {
            $sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        $db->query($sql);
        $rows = $db->fetch_all_array();
        $remindersUsers = array();
        foreach ($rows as $row) {
            $reminderUser = new ReminderUser();
            $reminderUser->setId($row['reminder_user_id']);
            $reminderUser->load();
            $remindersUsers[] = $reminderUser;
        }

        return $remindersUsers;
    }

    /**
     * 
     * get count of users by reminder Id
     * 
     * @param int $reminderId
     * 
     * @return int
     */
    public function countReminderUsersByReminderId($reminderId)
    {
        if (is_null($reminderId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT count(*) count FROM " . self::TABLE_NAME . " " .
                "WHERE reminder_id = {$db->sqltext($reminderId)}";
        $db->query($sql);
        $result = $db->fetch(0);
        
        return $result->count;
    }

    /**
     * 
     * unset reminder users from Reminder
     * 
     * @param int $reminderId
     * 
     * @return boolean|null
     */
    public function unSetReminder2ReminderUser($reminderId)
    {
        if (is_null($reminderId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM " . self::TABLE_NAME . " " .
                "WHERE reminder_id = {$db->sqltext($reminderId)}";
        $db->query($sql);
    }

    /**
     * 
     * get reminder User List By Facility id
     * 
     * @param int $facilityId
     * @param string $registered
     * values can be one of constants ALL_USERS, REGISTERED_USERS, UNREGISTERED_USERS or one of values(all, registered, unregistered)
     * @param \Pagination $pagination
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\ReminderUser[]
     * @throws Exception
     */
    public function getReminderUserListByFacilityId($facilityId, $registered = 'all', \Pagination $pagination = null)
    {
        if (is_null($facilityId)) {
            return false;
        }

        $reminderUserList = array();
        $db = \VOCApp::getInstance()->getService('db');

        $sql = "SELECT * FROM " . ReminderUser::TABLE_NAME . " " .
                "WHERE facility_id = {$db->sqltext($facilityId)}";

        switch ($registered) {
            case self::ALL_USERS:
                break;
            case self::REGISTERED_USERS:
                $sql.= " AND user_id <> 0";
                break;
            case self::UNREGISTERED_USERS:
                $sql.= " AND user_id = 0";
                break;
            default :
                throw new Exception('case does not exist!');
                break;
        }
        if (isset($pagination)) {
            $sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        $db->query($sql);
        $rows = $db->fetch_all_array();
        foreach ($rows as $row) {
            $reminderUser = new ReminderUser();
            $reminderUser->initByArray($row);
            $reminderUserList[] = $reminderUser;
        }

        return $reminderUserList;
    }

    /**
     * 
     * get reminder User List By Facility id
     * registered values: all, registered, unregistered
     * 
     * @param int $facilityId
     * @param string $registered
     * 
     * @return int
     * @throws Exception
     */
    public function countReminderUserListByFacilityId($facilityId, $registered = 'all')
    {
        if (is_null($facilityId)) {
            return false;
        }

        $reminderUserList = array();
        $db = \VOCApp::getInstance()->getService('db');

        $sql = "SELECT count(*) count FROM " . ReminderUser::TABLE_NAME . " " .
                "WHERE facility_id = {$db->sqltext($facilityId)}";

        switch ($registered) {
            case 'all':
                break;
            case 'registered':
                $sql.= " AND user_id <> 0";
                break;
            case 'unregistered':
                $sql.= " AND user_id = 0";
                break;
            default :
                throw new Exception('case does not exist!');
                break;
        }
        $db->query($sql);
        $result = $db->fetch(0);

        return $result->count;
    }
    
    /**
     * 
     * get reminder User by user id
     * 
     * @param int $userId
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\ReminderUser
     */
    public function getReminderUserByUserId($userId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT * FROM " . ReminderUser::TABLE_NAME . " " .
                "WHERE user_id = {$db->sqltext($userId)}";
        $db->query($sql);
        $rows = $db->fetch_all_array();
        if ($db->num_rows() == 0) {
            return false;
        } else {
            $reminder = new ReminderUser();
            $reminder->initByArray($rows[0]);
            return $reminder;
        }
    }

    /**
     * 
     * get User By Reminder User Id
     * 
     * @param int $reminderUserId
     * 
     * @return boolean|\VWM\Apps\User\Entity\User[]
     */
    public function getUserByReminderUserId($reminderUserId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        
        if(is_null($reminderUserId)){
            return false;
        }
        
        $sql = "SELECT user_id FROM ".ReminderUser::TABLE_NAME." ".
               "WHERE id = {$db->sqltext($reminderUserId)} LIMIT 1";
        $db->query($sql);
        
        if ($db->num_rows() == 0){
            return false;
        }
        
        $result = $db->fetch(0);
        if($result->user_id == 0){
            return false;
        }
        
        $user = new User();
        $user->setUserId($result->user_id);
        $user->load();
        
        return $user;
    }
    /**
     * 
     * get users wich have Reminder by facility Id
     * 
     * @param int $facilityId
     * @param \Pagination $pagination
     * 
     * @return \VWM\Apps\Reminder\Entity\ReminderUser[]
     */
    public function getUsersWithReminderByFacilityId($facilityId, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminderUserList = array();
        $sql = "SELECT ru.id, ru.user_id, ru.email, ru.facility_id FROM " . ReminderUser::TABLE_NAME . " ru " .
                "RIGHT JOIN ".ReminderUserManager::TABLE_NAME." r2ru ".
                "ON ru.id = reminder_user_id ".
                "WHERE ru.facility_id={$db->sqltext($facilityId)} GROUP BY ru.id";
                
        if (isset($pagination)) {
            $sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        $db->query($sql);
        $rows = $db->fetch_all_array();
        foreach ($rows as $row) {
            $reminderUser = new ReminderUser();
            $reminderUser->initByArray($row);
            $reminderUserList[] = $reminderUser;
        }
        return $reminderUserList;
    }
    /**
     * 
     * get count users by wich have reminders by facility id
     * 
     * @param int $facilityId
     * 
     * @return int
     */
    public  function countUsersWithReminderByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminderUserList = array();
        $sql = "SELECT  count(DISTINCT(ru.id)) count FROM " . ReminderUser::TABLE_NAME . " ru " .
                "RIGHT JOIN ".ReminderUserManager::TABLE_NAME." r2ru ".
                "ON ru.id = reminder_user_id ".
                "WHERE ru.facility_id={$db->sqltext($facilityId)}";
                
        $db->query($sql);
        $result = $db->fetch(0);
        
        return $result->count;
    }
    /**
     * 
     * getReminders by reminder Id
     * 
     * @param int $reminderUserId
     * @param \Pagination $pagination
     * 
     * @return array|\VWM\Apps\Reminder\Entity\Reminder[]
     */
    public function getReminderListByReminderUserId($reminderUserId, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        
        $sql = "SELECT r.id FROM " . ReminderUser::TABLE_NAME . " ru " .
               "RIGHT JOIN ".ReminderUserManager::TABLE_NAME. " r2ru ".
               "ON ru.id = r2ru.reminder_user_id ".
               "RIGHT JOIN ".Reminder::TABLE_NAME. " r ".
               "ON r.id = r2ru.reminder_id ".
               "WHERE ru.id = {$db->sqltext($reminderUserId)} GROUP BY r.id";
                

        if (isset($pagination)) {
            $sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        $db->query($sql);

        if ($db->num_rows() == 0) {
            return $reminders;
        }
        $rows = $db->fetch_all_array();

        foreach ($rows as $row) {
            $reminder = new Reminder();
            $reminder->setId($row['id']);
            $reminder->load();
            $reminders[] = $reminder;
        }
        return $reminders;
    }
    
    /**
     * 
     * getReminders count by reminder user Id
     * 
     * @param int $reminderUserId
     * 
     * @return int
     */
    public function countReminderListByReminderUserId($reminderUserId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        
        $sql = "SELECT count(r.id) count FROM " . ReminderUser::TABLE_NAME . " ru " .
               "RIGHT JOIN ".ReminderUserManager::TABLE_NAME. " r2ru ".
               "ON ru.id = r2ru.reminder_user_id ".
               "RIGHT JOIN ".Reminder::TABLE_NAME. " r ".
               "ON r.id = r2ru.reminder_id ".
               "WHERE ru.id = {$db->sqltext($reminderUserId)} LIMIT 1";
        $db->query($sql);

        $rows = $db->fetch(0);

        return $rows->count;
    }

}
?>
