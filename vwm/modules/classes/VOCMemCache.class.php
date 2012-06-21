<?php

class VOCMemCache extends Cache {

	/**
	 * @var boolean whether to use memcached or memcache as the underlying caching extension.
	 * If true {@link http://pecl.php.net/package/memcached memcached} will be used.
	 * If false {@link http://pecl.php.net/package/memcache memcache}. will be used.
	 * Defaults to false.
	 */
	public $useMemcached = false;

	/**
	 * @var Memcache the Memcache instance
	 */
	private $_cache = null;

	/**
	 * @var array list of memcache server configurations
	 */
	private $_servers = array();

	public function init() {
		parent::init();
		$servers = $this->getServers();
		$cache = $this->getMemCache();
		if (count($servers)) {
			foreach ($servers as $server) {
				if ($this->useMemcached) {
					$cache->addServer($server->host, $server->port, $server->weight);
				} else {
					$cache->addServer($server->host, $server->port, $server->persistent, $server->weight, $server->timeout, $server->status);
				}
			}
		} else {
			$cache->addServer('localhost', 11211);
		}
	}

	public function getMemcache() {
		if ($this->_cache !== null) {
			return $this->_cache;
		} else {
			return $this->_cache = $this->useMemcached ? new Memcached : new Memcache;
		}
	}

	public function getServers() {
		return $this->_servers;
	}

	public function setServers($_servers) {
		 foreach($_servers as $s) {
			$this->_servers[] = new MemCacheServerConfiguration($s);
		 }
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string $key a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key) {
		return $this->_cache->get($key);
	}

	/**
	 * Retrieves multiple values from cache with the specified keys.
	 * @param array $keys a list of keys identifying the cached values
	 * @return array a list of cached values indexed by the keys
	 */
	protected function getValues($keys) {
		return $this->useMemcached ? $this->_cache->getMulti($keys) : $this->_cache->get($keys);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string $key the key identifying the value to be cached
	 * @param string $value the value to be cached
	 * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key, $value, $expire) {
		if ($expire > 0)
			$expire+=time();
		else
			$expire = 0;


		return $this->useMemcached ? $this->_cache->set($key, $value, $expire) : $this->_cache->set($key, $value, 0, $expire);
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string $key the key identifying the value to be cached
	 * @param string $value the value to be cached
	 * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key, $value, $expire) {
		if ($expire > 0)
			$expire+=time();
		else
			$expire = 0;


		return $this->useMemcached ? $this->_cache->add($key, $value, $expire) : $this->_cache->add($key, $value, 0, $expire);
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string $key the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key) {
		return $this->_cache->delete($key, 0);
	}

	/**
	 * Deletes all values from cache.
	 * This is the implementation of the method declared in the parent class.
	 * @return boolean whether the flush operation was successful.
	 * @since 1.1.5
	 */
	protected function flushValues() {
		return $this->_cache->flush();
	}

}

class MemCacheServerConfiguration {

	/**
	 * @var string memcache server hostname or IP address
	 */
	public $host;

	/**
	 * @var integer memcache server port
	 */
	public $port = 11211;

	/**
	 * @var boolean whether to use a persistent connection
	 */
	public $persistent = true;

	/**
	 * @var integer probability of using this server among all servers.
	 */
	public $weight = 1;

	/**
	 * @var integer value in seconds which will be used for connecting to the server
	 */
	public $timeout = 15;

	/**
	 * @var integer how often a failed server will be retried (in seconds)
	 */
	public $retryInterval = 15;

	/**
	 * @var boolean if the server should be flagged as online upon a failure
	 */
	public $status = true;

	/**
	 * Constructor.
	 * @param array $config list of memcache server configurations.
	 * @throws Exception if the configuration is not an array
	 */
	public function __construct($config) {
		if (is_array($config)) {
			foreach ($config as $key => $value) {
				$this->$key = $value;
			}

			if ($this->host === null) {
				throw new Exception('MemCache server configuration must have "host" value.');
			}
		} else {
			throw new Exception('MemCache server configuration must be an array.');
		}
	}

}
