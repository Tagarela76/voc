<?php

namespace VWM\Framework\Test;

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');

abstract class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * @var db
	 */
	protected $db;

	public function __construct() {
		$this->db = $GLOBALS["db"];
	}
}