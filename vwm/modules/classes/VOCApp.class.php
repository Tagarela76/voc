<?php

use VWM\Framework\Cache as Caching;
/**
 * Description of VOCApp
 *
 */
class VOCApp {

	/**
	 * Singleton instace of VOCApp
	 */
	static private $instance;
	private $db;
	private $user_id;
	private $customer_id;
	private $date_format;
	private $date_format_js;

	/**
	 * @var VWM\Framework\Cache\Cache
	 */
	private $_cache;

	private function __construct() {

	}

	private function startup() {
		$this->date_format = DEFAULT_DATE_FORMAT;

		//	load cache
		if($this->_cache === null && USE_MEMCACHED) {
			$cacheServers = array(
				//	server config here
			);

			$this->_cache = new Caching\VOCMemCache();
			$this->_cache->setServers($cacheServers);
			$this->_cache->init();
		}

	}

	/**
	 * This implements the 'singleton' design pattern
	 *
	 * @return VOCApp The one and only instance
	 */
	static function get_instance() {

		if (!self::$instance) {
			self::$instance = new VOCApp();
			self::$instance->startup();  // init AFTER object was linked with self::$instance
		}

		return self::$instance;
	}

	public function setDB($db) {
		$this->db = $db;
	}

	public function setUserID($id) {
		$this->user_id = intval($id);
	}

	public function setCustomerID($id) {
		$this->customer_id = intval($id);
	}
	public function getCustomerID() {
		return $this->customer_id;
	}

	public function getDateFormat() {

		if ($this->customer_id and !$this->date_format) {
			$co = new Company($this->db);
			$codetails = $co->getCompanyDetails($this->customer_id);
			$dateformatid = $codetails['date_format_id'];

			$chain = new TypeChain(null, 'Date', $this->db, $this->customer_id, 'company');
			$this->date_format = $chain->getFromTypeController('getFormat');
		}
		return $this->date_format;
	}

	public function getDateFormat_JS() {
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
	public function getCache() {
		if ($this->_cache !== null) {
			return $this->_cache;
		} else {
			return false;
		}

	}

	/**
	 *
	 * @param type $datetimeObj
	 * @param boolean $print if print eq true than print value, else - return
	 */
	public function printDatetimeByCurrentDateformat($datetimeObj, $print = true) {
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
	public function printDatetimeByTimestampInCurrentDateformat($stamp, $print = true) {
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
	public function printDatetimeByMySqlDateInCurrentDateformat($mysqlDate, $print = true) {
		$dt = DateTime::createFromFormat('Y-m-d', $mysqlDate);
		$res = $this->printDatetimeByCurrentDateformat($dt, $print);
		if (!$print) {
			return $res;
		}
	}

}

?>
