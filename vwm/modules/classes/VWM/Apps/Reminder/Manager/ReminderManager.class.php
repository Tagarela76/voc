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

        $sql = "SELECT u.user_id, u.username, u.email, u.mobile " .
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
     * 
     * get users wich we need remind today
     * 
     * @param int $currentDate
     * @return boolean|\VWM\Apps\Reminder\Entity\Reminder
     */
    public function getCurrentReminders($currentDate = null)
    {
        //get current date
        if (is_null($currentDate)) {
            $currentDate = date("m.d.Y");
            $currentDate = explode('.', $currentDate);
            $deliveryDate = mktime('0', '0', '0', $currentDate[0], $currentDate[1], $currentDate[2]);
        }
        
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        
        $sql = "SELECT * " .
                "FROM " . Reminder::TABLE_NAME . " ".
                "WHERE delivery_date = {$db->sqltext($deliveryDate)} ".
                "AND active = 1";
                
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
        
        $email = new \EMail(true);
    	$from = AUTH_SENDER."@".DOMAIN;
        $messageSubject = "Reminder ";
        
        $tpl = dirname(__FILE__).'/../../../../../../design/user/tpls/reminderNotification.tpl';
        
        $smarty = \VOCApp::getInstance()->getService('smarty');
        $smarty->assign('reminder', $reminder);
        $messageText = $smarty->fetch($tpl);
        
        if (count($users) != 0) {
            $text = '';
            foreach($users as $user){
                if(($user["email"] == 'denis.kv@kttsoft.com') || ($user["email"] == 'denis.nt@kttsoft.com')){
                    $result = $email->sendMail($from, $user["email"], $messageSubject, $messageText);
                }
                    $text.='Reminder to '.$user["username"].' sent successfully;';
                    $text.=' ';
            }
        }
        return $text;
    }
    
    /**
     * 
     * get Reminder Timing List
     * 
     * @return array()
     */
    public function getReminderTimingList()
    {
        return array(
            0 =>array(
             'id' => Reminder::DAILY,
             'description' => 'daily'
            ),
            1 =>array(
             'id' => Reminder::WEEKLY,
             'description' => 'weekly'
            ),
            2 =>array(
             'id' => Reminder::MONTHLY,
             'description' => 'monthly'
            ),
            3 =>array(
             'id' => Reminder::YEARLY,
             'description' => 'yearly'
            ),
        );
    }
    
    /**
     * 
     * get Reminder Type List
     * 
     * @return array()
     */
    public function getReminderTypeList()
    {
        return array(
            0 => 'Permit',
            1 => 'License',
            2 => 'Process'
        );
    }
    
    /**
     * 
     * get users by reminder ids
     * 
     * @param string $remindersIds
     * 
     * @return array
     */
    public function getUserListByReminderIds($remindersIds)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $sql = "SELECT u.user_id, u.username, u.email, u.mobile " .
                "FROM " . TB_USER . " u" .
                " LEFT JOIN " . Reminder::TB_REMIND2USER . " r2u ON r2u.user_id = u.user_id " .
                "WHERE r2u.reminders_id IN ({$db->sqltext($remindersIds)}) ".
                "GROUP BY u.user_id";
        $db->query($sql);
        
        if ($db->num_rows() == 0) {
            return array();
        } else {
            return $db->fetch_all_array();
        }
    }
    
    /**
     * 
     * @param int $userId
     * @param \Pagination $pagination
     * 
     * @return boolean|\VWM\Apps\Reminder\Entity\Reminder
     */
    public function getRemindersByUserId($userId, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        $sql = "SELECT * " .
                "FROM " . Reminder::TABLE_NAME . " r" .
                " LEFT JOIN " . Reminder::TB_REMIND2USER . " r2u ON r2u.reminders_id = r.id " .
                "WHERE r2u.user_id = {$userId}";

        if (isset($pagination)) {
            $sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

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
     * get Reminder count
     * 
     * @param int $userId
     * 
     * @return int
     */
    public function countRemindersByUserId($userId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $reminders = array();
        $sql = "SELECT count(*) count " .
                "FROM " . Reminder::TABLE_NAME . " r" .
                " LEFT JOIN " . Reminder::TB_REMIND2USER . " r2u ON r2u.reminders_id = r.id " .
                "WHERE r2u.user_id = {$userId} LIMIT 1";
        
        $db->query($sql);
        $result = $db->fetch(0);
        
        return $result->count;
    }
    
    /**
     * 
     * Get over that period will be re-sent reminder
     * 
     * @param int $periodicity
     * 
     * @return int
     */
    public function getNextRemindDate($periodicity, $currentDate)
    {
        switch ($periodicity) {
            case Reminder::DAILY :
                $date = strtotime("+1 days", $currentDate);
                break;
            case Reminder::WEEKLY :
                $date = strtotime("+1 week", $currentDate);
                break;
            case Reminder::MONTHLY :
                $date = strtotime("+1 month", $currentDate);
                break;
            case Reminder::YEARLY :
                $date = strtotime("+1 year", $currentDate);
                break;
            default :
                throw new Exception("Inccorect next send perion");
                break;
        }
        return $date;
    }
    
    
}
?>