<?php

use VWM\Framework\Cache as Caching;
use VWM\Framework\DIC\PimpleContainer;


/**
 * Description of VOCApp
 */
class VOCApp
{

    /**
     * Singleton instace of VOCApp
     */
    static private $instance;

    private $db;
    private $user_id;
    private $customer_id;
    private $date_format;
    private $date_format_js;
    private $eventDispatcher;

    /**
     * @var User
     */
    private $user;

    /**
     * @var VWM\Framework\Cache\Cache
     */
    private $_cache;

    /**
     * @var VWM\Framework\VOCAccessControl
     */
    private $_accessControl;

    /**
     * @var VWM\Framework\DIC\ContainerInterface
     */
    private $container;

    private function __construct()
    {
    }

    private function startup()
    {
        $this->date_format = DEFAULT_DATE_FORMAT;

        //	load cache
        if ($this->_cache === null && USE_MEMCACHED) {
            $cacheServers = array(
                    //	server config here
            );

            $this->_cache = new Caching\VOCMemCache();
            $this->_cache->setServers($cacheServers);
            $this->_cache->init();
        }

        // init DIC container
        if ($this->container === null) {
            $this->container = new PimpleContainer();
        }

        $apps = array(
            new VWM\Apps\WorkOrder\WorkOrderApp(),
            new VWM\Apps\Logbook\LogbookApp(),
            new VWM\Apps\Reminder\ReminderApp(),
            new VWM\Apps\Logger\LoggerApp()
        );
    }

    /**
     * This implements the 'singleton' design pattern
     *
     * @return VOCApp The one and only instance
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new VOCApp();
            self::$instance->startup();  // init AFTER object was linked with self::$instance
        }

        return self::$instance;
    }

    /**
     * For compliance with old code - DEPRICATED
     */
    static function get_instance()
    {
        return self::getInstance();
    }

    /**
     * Get service instance by name
     *
     * @param string $service name
     *
     * @return mixed The value of an object
     */
    public function getService($service)
    {
        return $this->container[$service];
    }

    public function addSharedService($name, $callback)
    {
        $this->container[$name] = $this->container->share($callback);
    }

    /**
     * Setter for xnyo db
     *
     * @param \db $db
     */
    public function setDB($db)
    {
        $this->db = $db;

        // add db to container as service
        $this->container['db'] = $this->db;
    }

    /**
     * Setter for xnyo smarty
     *
     * @param \Smarty $smarty
     */
    public function setSmarty($smarty)
    {
        $this->smarty = $smarty;

        // add db to container as service
        $this->container['smarty'] = $this->smarty;
    }
    
    /**
     * Setter for xnyo smarty
     *
     * @param \Smarty $smarty
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        // add db to container as service
        $this->container['eventDispatcher'] = $this->eventDispatcher;
    }
    
    /**
     * Logged in User Id
     *
     * @param int $id
     */
    public function setUserID($id)
    {
        $this->user_id = intval($id);
    }

    /**
     * Logged in user company id
     * TODO: what if Super User?
     *
     * @param int $id
     */
    public function setCustomerID($id)
    {
        $this->customer_id = intval($id);
    }

    /**
     * Logged in user company id
     * TODO: what if Super User?
     *
     * @return int Logged in user company id
     */
    public function getCustomerID()
    {
        return $this->customer_id;
    }

    /**
     * Get active date format in PHP format
     *
     * @return string
     */
    public function getDateFormat()
    {
        if ($this->customer_id and !$this->date_format) {
            $chain = new TypeChain(null, 'Date', $this->db, $this->customer_id, 'company');
            $this->date_format = $chain->getFromTypeController('getFormat');
        } else {
            $this->date_format = DEFAULT_DATE_FORMAT;
        }
        return $this->date_format;
    }

    public function getDateFormat_JS()
    {
        if ($this->customer_id and !$this->date_format_js) {
            $co = new Company($this->db);
            $codetails = $co->getCompanyDetails($this->customer_id);
            $dateformatid = $codetails['date_format_id'];

            $chain = new TypeChain(null, 'Date', $this->db, $this->customer_id, 'company');
            $this->date_format_js = $chain->getFromTypeController('getFormatForCalendar');
        }
        return $this->date_format_js;
    }

    /**
     * @return VWM\Framework\Cache\Cache
     */
    public function getCache()
    {
        if ($this->_cache !== null) {
            return $this->_cache;
        } else {
            return false;
        }
    }

    public function getAccessControl()
    {
        if ($this->_accessControl === null) {
            $this->_accessControl = new VWM\Framework\VOCAccessControl;
        }

        return $this->_accessControl;
    }

    /**
     *
     * @param type $datetimeObj
     * @param boolean $print if print eq true than print value, else - return
     */
    public function printDatetimeByCurrentDateformat($datetimeObj, $print = true)
    {
        if (get_class($datetimeObj) == "DateTime") {
            if ($print) {
                echo $datetimeObj->format($this->getDateFormat());
            } else {
                return $datetimeObj->format($this->getDateFormat());
            }
        }
    }

    /**
     *
     * @param type $stamp
     * @param type $print if print eq true than print value, else - return
     */
    public function printDatetimeByTimestampInCurrentDateformat($stamp, $print = true)
    {
        $dt = new DateTime();
        $dt->setTimestamp((int) $stamp);
        $res = $this->printDatetimeByCurrentDateformat($dt, $print);
        if (!$print) {
            return $res;
        }
    }

    /**
     *
     * @param string from MySQL
     * @param type $print if print eq true than print value, else - return
     */
    public function printDatetimeByMySqlDateInCurrentDateformat($mysqlDate, $print = true)
    {
        $dt = DateTime::createFromFormat('Y-m-d', $mysqlDate);
        $res = $this->printDatetimeByCurrentDateformat($dt, $print);
        if (!$print) {
            return $res;
        }
    }

    /**
     * @param \User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Translate message (Not implemented yet)
     * @param string $category
     * @param string $message
     * @return string
     */
    public static function t($category, $message)
    {
        return $message;
    }

    public function setDateFormat($dataFormat)
    {
        $this->date_format = $dataFormat;
    }

}