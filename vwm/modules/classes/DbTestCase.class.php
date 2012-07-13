<?php

require_once('PHPUnit/Runner/Version.php');
require_once('PHPUnit/Autoload.php');

require_once(site_path.'modules/lib/phactory/lib/Phactory.php');

abstract class DbTestCase extends TestCase {

	const FIXTURE_FOLDER = '/tests/fixtures';

	// only instantiate pdo once for test clean-up/fixture load
	static private $pdo = null;

	//
	protected $fixtures = false;


	public static function setUpBeforeClass() {
		if (Phactory::getConnection() === null) {
			if (self::$pdo == null) {
				self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
			}

			Phactory::setConnection(self::$pdo);
		}

		// reset any existing blueprints and empty any tables Phactory has used
		Phactory::reset();
	}

	public static function tearDownAfterClass() {
		Phactory::reset();
	}

	protected function setUp() {
		if (is_array($this->fixtures)) {
			$this->loadFixtures($this->fixtures);
		}
	}

	protected function loadFixtures($tableNames) {
		//reset tables
		Phactory::reset();

		foreach ($tableNames as $tableName) {
			//load table
			$this->loadFixture($tableName);
		}
	}


	protected function loadFixture($tableName) {
		$fileName = site_path.self::FIXTURE_FOLDER.DIRECTORY_SEPARATOR.$tableName.'.php';

		if(!is_file($fileName)) {
			return false;
		}

		// https://github.com/chriskite/phactory/issues/3
		Phactory::setInflection($tableName, $tableName);

		// define default values for each user we will create
		Phactory::define($tableName);

		foreach(require($fileName) as $row) {
			Phactory::create($tableName, $row);
		}
	}

}