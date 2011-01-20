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
	
	
	
	class FacilityTest extends PHPUnit_Extensions_Database_TestCase
	{
		protected $pdo;	
		protected $db;
		protected $seedPath;
		
		private $facilityExpected;
		private $defaultStartDate;
		private $defaultEndDate;
		
		const CSV_NULL = '__NULL__';
		
		public function __construct() {
			//	Start xnyo Framework
			
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];
			$this->db->select_db(DB_NAME);
			
			$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
			
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
			
			$this->defaultStartDate = "2011-01-01";
			$this->defaultEndDate = "2011-01-07";
			
			$this->createFacilities();
		}
		
		protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}
    	
		protected function getDataSet() {

	    	$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');
	    	
        	$dataSet->addTable(TB_DEPARTMENT, FIXTURE_PATH.'department.csv');
        	$dataSet->addTable(TB_USAGE, FIXTURE_PATH.'mix.csv');
    		
    		//	replace '__NULL__' to real NULL
    		$replacedDataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );	
    		    		
        	return $replacedDataSet;
    	}
		private function createEmptyFacilitiesExpected($startDate,$endDate,$emissionList)
    	{
    		$emAr = array();
    		foreach($emissionList as $e)
    		{
    			$emAr[$e] = array();
    		}
    		$beginTime 	= 	strtotime($startDate);
    		$endTime 	=	strtotime($endDate);
    		$daySeconds = (60*60*24);
    		$keys = array_keys($emAr);
    		for($i = $beginTime, $curDay = 0; $i <= $endTime; $i += $daySeconds, $curDay++)
    		{
    			foreach($keys as $key)
    			{
    				$emAr[$key][$curDay][0] = $i*1000;
    				$emAr[$key][$curDay][1] = 0;
    			}
    		}
    		return $emAr;
    	}
    	private function createFacilities()
    	{
    		$fsList = array("Administrative Services");
    		$fs = $this->createEmptyFacilitiesExpected($this->defaultStartDate,$this->defaultEndDate,$fsList);
    		
    		$fs["Administrative Services"][0] = array( strtotime("2011-01-01") * 1000, 36.23 );
    		$fs["Administrative Services"][1] = array( strtotime("2011-01-02") * 1000, 64.56 );
    		$fs["Administrative Services"][2] = array( strtotime("2011-01-03") * 1000, 999.99 );
    		$fs["Administrative Services"][3] = array( strtotime("2011-01-04") * 1000, 5.82 );
    		
    		$this->facilityExpected = $fs;
    		//exit;
    	}
    	
    	public function testGetDepartmentUsageByDays()
    	{
    		$facility = new Facility($this->db);
    		
    		$beginDate 	= 	"2011-01-01";
    		$endDate 	=	"2011-01-07";
    		$facilityID = 	"66";
    		
    		$depUsage = $facility->getDepartmentUsageByDays($beginDate, $endDate, $facilityID);
    		
    		$this->assertEquals($depUsage, $this->facilityExpected);
    	}
	}
?>