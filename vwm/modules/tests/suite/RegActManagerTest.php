<?php
	chdir('../../..');
	
	//define ("USERS_TABLE", "vps_user");
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/CsvDataSet.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/ReplacementDataSet.php';	
	//require_once 'extensions/regupdate/classes/RegActManager.class.php';
	
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	
	class RegActManagerTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;	
		protected $db;
		protected $seedPath;
		protected $xmlReviewPath;
		protected $xmlCompletedPath;
		
		const CATEGORY_REVIEW = 'review';
		const CATEGORY_COMPLETED = 'completed';
		const CSV_NULL = '__NULL__';
		
		private $regActList;
	
		public function __construct() {
			//	Start xnyo Framework
			
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];
			$this->db->select_db(DB_NAME);
			
			$this->xmlReviewPath = "modules/tests/suite/EO_RULES_UNDER_REVIEW.xml";
			$this->xmlCompletedPath = "modules/tests/suite/EO_RULE_COMPLETED_30_DAYS.xml";
			
			//throw new Exception("Host: " . DB_HOST . ", Name: " . DB_NAME);
			//exit;
			
			$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
			
			//require_once '../../../extensions/regupdate/classes/RegActManager.class.php';
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
			
			$this->createRegActList();
		}
		
		protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}

	    protected function getDataSet() {

	    	$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');
	    	
        	$dataSet->addTable(TB_REG_ACTS, FIXTURE_PATH.'reg_act.csv');
        	$dataSet->addTable(TB_REG_AGENCY, FIXTURE_PATH.'reg_agency.csv');
        	$dataSet->addTable(TB_USERS2REGS, FIXTURE_PATH.'users2regs.csv');
        	$dataSet->addTable(TB_USER, FIXTURE_PATH.'user.csv');
        	$dataSet->addTable(TB_COUNTRY, FIXTURE_PATH.'country.csv');
        	$dataSet->addTable(TB_COMPANY, FIXTURE_PATH.'company.csv');
    		
    		//	replace '__NULL__' to real NULL
    		$replacedDataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );	
    		    		
        	return $replacedDataSet;
    	}
    	
    	private function createRegActList()
    	{
    		$regAct1 = new RegAct($this->db);
    		
    		//$regAct1->id = 1;
		    $regAct1->rin = "2060-AQ54";
		    $regAct1->reg_agency_id  = 2060;
		    $regAct1->title = "Notice of Upcoming Joint Rulemaking to Establish 2017 and Later Model Year Light duty Vehicle Greenhouse Gas Emissions and CAFE Standards";
		    $regAct1->stage = "Notice";
		    $regAct1->significant = "Yes";
		    $regAct1->date_received = "2010-11-23";
		    $regAct1->legal_deadline = "None";
		    $regAct1->date_completed = "2010-11-30";
		    $regAct1->category = "completed";
		    $regAct1->decision = "Consistent with Change";
		    $regAct1->mailed = 0;
		    $regAct1->readed = 0;
		    $regAct1->user_id = 1;
		    $regAct1->reg_agency_id = "2";
		    
		    $regAgency = new RegAgency($this->db,2);
		    $regAct1->reg_agency = $regAgency;
		    //$regAct1->id = 2;
    		
    		$regActList = array(
    			$regAct1
    		);
    		
    		$this->regActList = $regActList;
    	}
    	
    	public function testParseXMLV1()
    	{    		
    		//$this->markTestSkipped();
    		
    		$category = self::CATEGORY_COMPLETED;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_acts'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);

    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_AGENCY), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_USERS2REGS), $outputTable['users2regs']);
    	}
    	
    	
    	
		public function testParseXMLV2()
    	{
    		//$this->markTestSkipped();
    		
    		$category = self::CATEGORY_REVIEW;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_acts'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		$outputTable['user'] = $this->getConnection()->createDataSet()->getTable(TB_USER);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS,TB_USER);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		
    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_USER), $outputTable['user']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_AGENCY), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_USERS2REGS), $outputTable['users2regs']);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    			
    	}
    	
    	
		public function testParseXMLV3()
    	{
    		//$this->markTestSkipped();
    		$category = NULL;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_acts'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);

    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_AGENCY), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_USERS2REGS), $outputTable['users2regs']);	
    	}
    	
    	public function testMarkRINV1()
    	{
    		//$this->markTestSkipped();
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$userID = 1;
    		$RINarray = array("2060-AQ54");
    		$action = "readed";
    		
    		$regActManager->markRIN($userID,$action,$RINarray);
    		
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_USERS2REGS), $outputTable['users2regs']);
    	}
    	
		public function testMarkRINV2()
    	{
    		//$this->markTestSkipped();
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$userID = 1;
    		$RINarray = array("2060-AQ54");
    		$action = "mailed";
    		
    		$regActManager->markRIN($userID,$action,$RINarray);
    		
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_USERS2REGS), $outputTable['users2regs']);
    	}
    	
    	
    	public function testGetRegActsListV1()
    	{
    		//$this->markTestSkipped();
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$customerID = 1;
    		$recievedRegActsList = $regActManager->getRegActsList($customerID);
    		
    		
    		
    		$this->assertEquals($recievedRegActsList,$this->regActList);
    	}
    	
		public function testGetRegActsListV2()
    	{
    		//$this->markTestSkipped();
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$customerID = 1;
    		$recievedRegActsList = $regActManager->getRegActsList();
    		
    		
    		$this->assertEquals($recievedRegActsList,$this->regActList);
    	}
    	
    	public function testGetUnreadListV1()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		$userID = 1;
    		$recievedRegActsList = $regActManager->getUnreadList($userID);
    		
    		$this->assertEquals($recievedRegActsList,$this->regActList);
    	}
    	
		public function testGetUnreadListV2()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		$userID = 1;
    		$category = self::CATEGORY_REVIEW;
    		$recievedRegActsList = $regActManager->getUnreadList($userID,$category);
    		$this->assertEquals($recievedRegActsList,false);
    	}
    	
		public function testGetUnreadListV3()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		$userID = 1;
    		$category = self::CATEGORY_COMPLETED;
    		$recievedRegActsList = $regActManager->getUnreadList($userID,$category);
    		$this->assertEquals($recievedRegActsList,$this->regActList);
    	}
    	
		public function testGetUnreadListV4()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		$userID = 1;
    		$category = self::CATEGORY_COMPLETED;
    		$mailed = false;
    		
    		$recievedRegActsList = $regActManager->getUnreadList($userID,$category,$mailed);
    		$this->assertEquals($recievedRegActsList,$this->regActList);
    	}
    	
    	public function testGetMessageForNotificator()
    	{
    		$regActManager = new RegActManager($this->db,$this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$userID = 1;
    		$message = $regActManager->getMessageForNotificator($userID);
    		
    		$this->assertType(gettype($message),"string");
    	}
    	
		
		/**
    	 * 
    	 * Generate expected dataset of $tables for $method
    	 * @param array $tables - tables that should be at dataset
    	 * @param String $method - method name
    	 */
    	private function _loadExpectedTables(Array $tables, $method) {
    		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');	//The constructor takes three parameters: $delimiter, $enclosure and $escape
    		foreach ($tables as $table) {
    			$csvPath = EXPECTED_PATH . get_class($this) . "/$method/$table.csv";
    			
    			$dataSet->addTable($table,$csvPath);
    			//$dataSet->addTable($table, EXPECTED_PATH.$table.'_'.$method.'.csv');	
    		}    		    		    		
			$dataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );
			
			return $dataSet;
    	}
	}
?>