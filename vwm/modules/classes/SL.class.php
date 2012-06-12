<?php


/**
 * Project:     VOC WEB MANAGER
 * File:        SL.class.php
 *
 * Simple Localization.
 * It's really simple
 *
 */

class SL {
	/**
	 * @var string Path to localization files *locale*.php
	 */
	public $localizationPath = 'modules/localization/';

	/**
	 * XNYO database
	 * @var unknown_type
	 */
	private $db;

	/**
	 * Read only
	 * @var array
	 */
	private $localeContstants = array();

	/**
	 * Read only
	 * @var array
	 */
	private $localeContstantsKeyValue = array();


	/**
	 * Loads locale constants as PHP constants
	 * @param string $locale
	 * @param xnyo database $db
	 * @throws Exception
	 */
	public function __construct($locale, $db) {
		$this->db = $db;
		$sql = "SELECT * FROM localization WHERE region = '".mysql_escape_string($locale)."'";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			throw new Exception('Can not load locale '.$locale);
		}

		$rows = $this->db->fetch_all();
		foreach ($rows as $row) {
			define($row->id, $row->string);
			$this->localeContstants[] = array('id'=>$row->id, 'string'=>$row->string);
			$this->localeContstantsKeyValue[$row->id] = $row->string;
		}
	}



	public function getLocaleConstants() {
		return $this->localeContstants;
	}


	public function getLocaleConstantAsAssociativeArray() {
		return $this->localeContstantsKeyValue;
	}



	public function setLocaleConstant($id, $string) {
		$sql = "UPDATE localization SET string = '".mysql_escape_string($string)."' WHERE id = '".mysql_escape_string($id)."'";

		if ($this->db->exec($sql)) {
			return true;
		} else {
			return false;
		}

	}


	/**
	 *
	 * Load from file with constants
	 * @param string $locale
	 * @throws Exception
	 */
	function SL_old($locale) {

    	$filePath = $this->localizationPath.$locale.".php";
    	if (file_exists($filePath)) {
    		require_once($filePath);
    	} else {
    		throw new Exception('Can not load locale '.$filePath);
    	}
    }
}
?>