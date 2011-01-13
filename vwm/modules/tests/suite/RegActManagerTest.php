<?php
	define ("USERS_TABLE", "vps_user");
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
	
	require_once 'modules/classes/RegActManager.class.php';
	
	class RegActManagerTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;
		
		protected $db;
		
		
		
		protected $seedPath;
		
		protected $xmlReviewPath;
		protected $xmlCompletedPath;
		
		const REG_ACT_CATEGORY_REVIEW = 'review';
		const REG_ACT_CATEGORY_COMPLETED = 'completed';
	
		public function __construct() {
			//	Start xnyo Framework
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];
			
			$this->xmlReviewPath = "/home/developer/mywork/www/voc_src/vwm/modules/tests/suite/EO_RULES_UNDER_REVIEW.xml";
			$this->xmlCompletedPath = "/home/developer/mywork/www/voc_src/vwm/modules/tests/suite/EO_RULE_COMPLETED_30_DAYS.xml";
			
			$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
			
			
		}
		
		protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}

	    protected function getDataSet() {

	    	$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');
        	$dataSet->addTable(TB_VPS_CUSTOMER, FIXTURE_PATH.'reg_act.csv');
        	$dataSet->addTable(TB_VPS_CUSTOMER, FIXTURE_PATH.'reg_agency.csv');
        	$dataSet->addTable(TB_VPS_CUSTOMER, FIXTURE_PATH.'users2regs.csv');
        	$dataSet->addTable(TB_VPS_CUSTOMER, FIXTURE_PATH.'user.csv');
    		
    		//	replace '__NULL__' to real NULL
    		$replacedDataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );	
    		    		
        	return $replacedDataSet;
    	}
    	
    	public function testParseXMLV1()
    	{
    		$category = self::REG_ACT_CATEGORY_COMPLETED;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_act'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);

    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['users2regs']);
    	}
		public function testParseXMLV2()
    	{
    		$category = self::REG_ACT_CATEGORY_REVIEW;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_act'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);

    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['users2regs']);	
    	}
		public function testParseXMLV3()
    	{
    		$category = NULL;
    		
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$regActManager->parseXML($category);
    		
    		$outputTable['reg_act'] = $this->getConnection()->createDataSet()->getTable(TB_REG_ACTS);
    		$outputTable['reg_agency'] = $this->getConnection()->createDataSet()->getTable(TB_REG_AGENCY);
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_REG_ACTS,TB_REG_AGENCY,TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);

    		//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_acts']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['reg_agency']);
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['users2regs']);	
    	}
    	
    	public function testMarkAsRead()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$userID = 1;
    		$IDarray = array(1);
    		
    		$regActManager->markAsRead($userID,$IDarray);
    		
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['users2regs']);
    	}
    	
    	public function testMarkAsMailed()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$userID = 1;
    		$IDarray = array(1);
    		
    		$regActManager->markAsRead($userID,$IDarray);
    		
    		$outputTable['users2regs'] = $this->getConnection()->createDataSet()->getTable(TB_USERS2REGS);
    		
    		//	take expected output
    		$tables = array(TB_USERS2REGS);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_REG_ACTS), $outputTable['users2regs']);
    	}
    	
    	public function testGetRegActsListV1()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$customerID = 1;
    		$recievedRegActsList = $regActManager->getRegActsList($customerID);
    		
    		$regAct1 = new RegAct();
    		
		    $regAct1->rin = "2060-AQ54";
		    $regAct1->reg_agency_id  = 2060;
		    $regAct1->title = "Notice of Upcoming Joint Rulemaking to Establish 2017 and Later Model Year Light duty Vehicle Greenhouse Gas Emissions and CAFE Standards";
		    $regAct1->stage = "Notice";
		    $regAct1->significant = "Yes";
		    $regAct1->date_received = "11/23/2010";
		    $regAct1->legal_deadline = "None";
		    $regAct1->date_completed = "11/30/2010";
		    $regAct1->category = "completed";
    		
    		$regActList = array(
    			$regAct1
    		);
    		
    		$this->assertEquals($recievedRegActsList,$regActList);
    	}
    	
		public function testGetRegActsListV2()
    	{
    		$regActManager = new RegActManager($this->db, $this->xmlReviewPath, $this->xmlCompletedPath);
    		
    		$customerID = NULL;
    		$recievedRegActsList = $regActManager->getRegActsList($customerID);
    		
    		$regAct1 = new RegAct();
    		
		    $regAct1->rin = "2060-AQ54";
		    $regAct1->reg_agency_id  = 2060;
		    $regAct1->title = "Notice of Upcoming Joint Rulemaking to Establish 2017 and Later Model Year Light duty Vehicle Greenhouse Gas Emissions and CAFE Standards";
		    $regAct1->stage = "Notice";
		    $regAct1->significant = "Yes";
		    $regAct1->date_received = "11/23/2010";
		    $regAct1->legal_deadline = "None";
		    $regAct1->date_completed = "11/30/2010";
		    $regAct1->category = "completed";
    		
    		$regActList = array(
    			$regAct1
    		);
    		
    		$this->assertEquals($recievedRegActsList,$regActList);
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
    			$dataSet->addTable($table, EXPECTED_PATH.$table.'_'.$method.'.csv');	
    		}    		    		    		
			$dataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );
			
			return $dataSet;
    	}
	}
?>