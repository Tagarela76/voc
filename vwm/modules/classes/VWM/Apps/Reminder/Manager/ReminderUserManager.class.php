<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Apps\Reminder\Entity\ReminderUser;

class ReminderUserManager
{

    const TABLE_NAME = 'reminder2reminder_user';

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
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\ReminderUser[]
     */
    public function getReminderUsersByReminderId($reminderId)
    {
        if (is_null($reminderId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT reminder_user_id FROM " . self::TABLE_NAME . " " .
                "WHERE reminder_id = {$db->sqltext($reminderId)}";
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
     * registered values: all, registered, unregistered
     * 
     * @param int $facilityId
     * @param string $registered
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\ReminderUser[]
     * @throws Exception
     */
    public function getReminderUserListByFacilityId($facilityId, $registered = 'all')
    {
        if (is_null($facilityId)) {
            return false;
        }

        $reminderUserList = array();
        $db = \VOCApp::getInstance()->getService('db');

        $sql = "SELECT * FROM " . ReminderUser::TABLE_NAME . " " .
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
     * @param int $facilityId
     * 
     * @return VWM\Apps\Reminder\Entity\ReminderUser[]
     */
    public function getReminderUserListByFacility($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminderUserList = array();
        $sql = "SELECT * FROM " . ReminderUser::TABLE_NAME . " " .
                "WHERE facility_id={$db->sqltext($facilityId)}";
        $db->query($sql);
        $rows = $db->fetch_all_array();
        foreach ($rows as $row) {
            $reminderUser = new ReminderUser();
            $reminderUser->initByArray($row);
            $reminderUserList[] = $reminderUser;
        }
        return $reminderUserList;
    }

}
?>
