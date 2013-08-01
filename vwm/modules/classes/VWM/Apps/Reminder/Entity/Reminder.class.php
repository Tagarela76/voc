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
     * delivary date
     *
     * @var int
     */
    protected $delivery_date;

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

    /**
     *
     * priority level from 0% to 100%
     *
     * @var int
     */
    protected $priority = 0;

    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var string
     */
    protected $appointment = 0;

    /**
     *
     * period daily/weekly/monthly/yearly/custom
     *
     * @var int
     */
    protected $periodicity = 0;

    /**
     *
     * get active reminder or not
     *
     * @var boolean
     */
    protected $active = 0;
    
    /**
     *
     * reminder description
     * 
     * @var string 
     */
    protected $description = null;

    /**
     *
     * beforehand reminder date
     * 
     * @var int 
     */
    protected $beforehand_reminder_date = 0;

    /**
     *
     * The number of unit type for which we need to remind the reminder
     * 
     * @var int 
     */
    protected $time_number = 0;

    /**
     *
     * reminder id
     * 
     * @var int
     */
    protected $reminder_unit_type_id = 0;

    const TABLE_NAME = 'reminder';
    const TB_REMIND2USER = 'remind2user';
    const APPOINTMENT_EMAIL = 1;
    const APPOINTMENT_TELEPHONE = 2;

    //reminder periodicity
    const DAILY = 0;
    const WEEKLY = 1;
    const MONTHLY = 2;
    const YEARLY = 3;
    const EVERY2YEAR = 4;
    const EVERY3YEAR = 5;

    function __construct($id = null, EMail $email = null)
    {
        $this->modelName = 'Reminder';

        if (isset($id)) {
            $this->id = $id;
            $this->load();

            if (!isset($this->users)) {
                $this->loadUsers();
            }
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

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getAppointment()
    {
        return $this->appointment;
    }

    public function setAppointment($appointment)
    {
        $this->appointment = $appointment;
    }

    public function getPeriodicity()
    {
        return $this->periodicity;
    }

    public function setPeriodicity($periodicity)
    {
        $this->periodicity = $periodicity;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    public function setDeliveryDate($nextDate)
    {
        $this->delivery_date = $nextDate;
    }
    
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getBeforehandReminderDate()
    {
        return $this->beforehand_reminder_date;
    }

    public function setBeforehandReminderDate($beforeReminderDate)
    {
        $this->beforehand_reminder_date = $beforeReminderDate;
    }

    public function getTimeNumber()
    {
        return $this->time_number;
    }

    public function setTimeNumber($time_number)
    {
        $this->time_number = $time_number;
    }

    public function getReminderUnitTypeId()
    {
        return $this->reminder_unit_type_id;
    }

    public function setReminderUnitTypeId($reminderUnitTypeId)
    {
        $this->reminder_unit_type_id = $reminderUnitTypeId;
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
            'delivery_date' => $this->next_date,
            'facility_id' => $this->facility_id,
            'url' => $this->url,
            'priority' => $this->priority,
            'type' => $this->type,
            'appointment' => $this->appointment,
            'periodicity' => $this->periodicity
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
        //set next date = date when we use daily reminding
        if ($this->getPeriodicity() == self::DAILY) {
            $nextDate = $this->getDate();
        } else {
            $nextDate = $this->getDeliveryDate();
        }
        
        $description = (is_null($this->getDescription()))?'NULL':$this->getDescription();
        
        $db = \VOCApp::getInstance()->getService('db');

        $query = "INSERT INTO " . self::TABLE_NAME . " " .
                "(name, date, delivery_date, facility_id, priority, type, appointment, periodicity, description, active, beforehand_reminder_date, time_number, reminder_unit_type_id) " .
                "VALUES ( " .
                "'{$db->sqltext($this->getName())}' " .
                ", {$db->sqltext($this->getDate())} " .
                ", {$db->sqltext($nextDate)} " .
                ", {$db->sqltext($this->getFacilityId())} " .
                ", {$db->sqltext($this->getPriority())} " .
                ", '{$db->sqltext($this->getType())}' " .
                ", {$db->sqltext($this->getAppointment())} " .
                ", {$db->sqltext($this->getPeriodicity())} " .
                ", '{$db->sqltext($description)}' " .
                ", {$db->sqltext($this->getActive())} " .
                ", {$db->sqltext($this->getBeforehandReminderDate())} " .
                ", {$db->sqltext($this->getTimeNumber())} " .
                ", {$db->sqltext($this->getReminderUnitTypeId())} " .
                ")";

        $db->exec($query);
        $this->id = $db->getLastInsertedID();

        return $this->id;
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $description = (is_null($this->getDescription()))?'NULL':$this->getDescription();
        
        $query = "UPDATE " . self::TABLE_NAME . " " .
                " SET name = '{$db->sqltext($this->getName())}', " .
                " date = {$db->sqltext($this->getDate())}, " .
                " delivery_date = {$db->sqltext($this->getDeliveryDate())}, " .
                " priority = {$db->sqltext($this->getPriority())}, " .
                " type = '{$db->sqltext($this->getType())}', " .
                " appointment = {$db->sqltext($this->getAppointment())}, " .
                " periodicity = {$db->sqltext($this->getPeriodicity())}, " .
                " active = {$db->sqltext($this->getActive())}, " .
                " description = '{$db->sqltext($description)}', " .
                " beforehand_reminder_date = {$db->sqltext($this->getBeforehandReminderDate())}, " .
                " time_number = {$db->sqltext($this->getTimeNumber())}, " .
                " reminder_unit_type_id = {$db->sqltext($this->getReminderUnitTypeId())}, " .
                " facility_id = {$db->sqltext($this->getFacilityId())} " .
                "WHERE id = {$db->sqltext($this->getId())}";
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
   /* public function getUsers()
    {
        $users = array();
        if (!is_null($this->users)) {
            return $this->users;
        }
        $rManager = \VOCApp::getInstance()->getService('reminder');
        if (is_null($this->getId())) {
            return false;
        }
        $users = $rManager->getUsersByReminderId($this->getId());

        return $users;
    }*/

    public function getUsers()
    {
        $users = array();
        if (!is_null($this->users)) {
            return $this->users;
        }
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');
        if (is_null($this->getId())) {
            return false;
        }
        $users = $rUManager->getReminderUsersByReminderId($this->getId());

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
     * get date in facility date format
     * 
     * @return string
     */
    public function getDateInOutputFormat()
    {
       $db = \VOCApp::getInstance()->getService('db');
       $facilityId = $this->getFacilityId();
       $dataChain = new \TypeChain(date("y-m-d", $this->getDate()), 'date', $db, $facilityId, 'facility');
       $date = $dataChain->formatOutput();
       return $date;
    }
    /**
     * 
     * get delivery date in facility date format
     * 
     * @return string
     */
    public function getDeliveryDateInOutputFormat()
    {
       $db = \VOCApp::getInstance()->getService('db');
       $facilityId = $this->getFacilityId();
       $dataChain = new \TypeChain(date("y-m-d", $this->getDeliveryDate()), 'date', $db, $facilityId, 'facility');
       $date = $dataChain->formatOutput();
       return $date;
    }

    /**
     *
     * @return boolean
     */
    public function isAtLeastOneUserSelect()
    {
        return (count($this->users) != 0) ? true : false;
    }

    public function isCurrentDate()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $facility = new \VWM\Hierarchy\Facility($db, $this->getFacilityId());
        $companyId = $facility->getCompanyId();
        $currentDate = time();

        $dataChain = new \TypeChain($this->getDeliveryDate(), 'date', $db, $companyId, 'company');
        $deliveryDate = ($dataChain->getTimestamp());

        return ($currentDate < $deliveryDate) ? true : false;
    }

    /**
     * 
     * get is beforehand reminder is actual
     * it might be more then current time
     * 
     * @return boolean
     */
    public function isActualBeforehandReminder()
    {
        //check if user select to remind him about reminder
        if($this->getBeforehandReminderDate()==0){
            return true;
        }
        $currentDate = time();
        $beforhandDate = $this->getBeforehandReminderDate();
        return ($currentDate < $beforhandDate) ? true : false;
    }

}
