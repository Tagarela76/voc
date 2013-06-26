<?php

namespace VWM\Apps\Reminder\Manager;

use VWM\Framework\Model;
use \VWM\Apps\Reminder\Entity\Reminder;

class ReminderManager
{
    public function getReminders()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        $sql = "SELECT * " .
                "FROM " . TB_REMINDER;
        $db->query($sql);

        if ($db->num_rows() == 0) {
            return false;
        }
        $rows = $db->fetch_all_array();

        foreach ($rows as $row) {
            $reminder = new Reminder($db);
            foreach ($row as $key => $value) {
                if (property_exists($reminder, $key)) {
                    $reminder->$key = $value;
                }
            }
            $reminders[] = $reminder;
        }
        return $reminders;
    }

    /**
     * 
     * set user to remind
     * 
     * @param int $userId
     * @param int $reminderId
     * 
     * @return boolean
     */
    public function setRemind2User($userId, $reminderId)
    {
        if (is_null($reminderId) || is_null($userId)) {
            return false;
        }

        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO " . TB_REMIND2USER . "(user_id, reminders_id)
				VALUES ({$db->sqltext($userId)}, {$db->sqltext($reminderId)})";
        $db->query($query);
    }

    /**
     * 
     * unset all users from remind
     * 
     * @param int $reminderId
     * 
     * @return boolean|NULL
     */
    public function unSetRemind2User($reminderId)
    {
        if(is_null($reminderId)){
            return false;
        }
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM " . Reminder::TB_REMIND2USER . "
				 WHERE reminders_id={$db->sqltext($reminderId)}";
        $db->query($sql);
    }
    
    /**
     * 
     * get Users by ReminderId
     * 
     * @param int $reminderId
     * 
     * @return boolean|array()
     */
    public function getUsersByReminderId($reminderId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');

        if (is_null($reminderId)) {
            return false;
        }

        $sql = "SELECT u.user_id, u.username, u.email " .
                "FROM " . TB_USER . " u" .
                " LEFT JOIN " . Reminder::TB_REMIND2USER . " r2u ON r2u.user_id = u.user_id " .
                "WHERE r2u.reminders_id={$db->sqltext($reminderId)}";
        $db->query($sql);
        
        if ($db->num_rows() == 0) {
            return array();
        } else {
            return $db->fetch_all_array();
        }
    }
    
    /**
     * get users wich we need remind today
     */
    public function getCurrentReminders()
    {
        //get current date
        $currentDate = date("m.d.Y");
        $currentDate = explode('.', $currentDate);
        $CurrentDate = mktime('0','0','0',$currentDate[0],$currentDate[1],$currentDate[2]);
        
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        
        $sql = "SELECT * " .
                "FROM " . Reminder::TABLE_NAME . " ".
                "WHERE date = {$db->sqltext($CurrentDate)} ";
                
        $db->query($sql);
        
        if ($db->num_rows() == 0) {
            return false;
        }
        $rows = $db->fetch_all_array();
        
        foreach ($rows as $row) {
            $reminder = new Reminder();
            $reminder->initByArray($row);
            $reminders[] = $reminder;
        }
        return $reminders;
    }
    
    /**
     * 
     * sent remind to user
     * 
     * @param int $reminderId
     * 
     * @return string
     */
    public function sendRemindToUser($reminderId)
    {
        $reminder = new Reminder();
        $reminder->setId($reminderId);
        $reminder->load();
        
        $text = 'No users to remind with id '.$reminder->getName();
        
        $users = $this->getUsersByReminderId($reminderId);
        
        $email = new \EMail();
    	$from = AUTH_SENDER."@".DOMAIN;
        $messageSubject = "Reminder ";
        $messageText = $reminder->getName();
        $headers = "Content-type: text/html";
        
        if (count($users) != 0) {
            $text = '';
            foreach($users as $user){
                if(($user["email"] == 'denis.kv@kttsoft.com') || ($user["email"]=='denis.nt@kttsoft.com')){
                    $result = $email->sendMail($from, $user["email"], $messageSubject, $messageText);
                }
                    $text.='Reminder to '.$user["username"].' sent successfully;';
                    $text.=' ';
            }
        }
        return $text;
    }

}
?>