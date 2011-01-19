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
	
	class ProductTest extends PHPUnit_Extensions_Database_TestCase
	{
	protected $pdo;	
		protected $db;
		protected $seedPath;
		
		private $productsExpected;
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
			
			$this->createProducts();
		}
		
		protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}
    	
		protected function getDataSet() {

	    	$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');
	    	
        	$dataSet->addTable(TB_FACILITY, FIXTURE_PATH.'facility.csv');
        	$dataSet->addTable(TB_DEPARTMENT, FIXTURE_PATH.'department.csv');
        	$dataSet->addTable(TB_USAGE, FIXTURE_PATH.'mix.csv');
        	$dataSet->addTable(TB_EQUIPMENT, FIXTURE_PATH.'equipment.csv');
        	$dataSet->addTable(TB_USER, FIXTURE_PATH.'user.csv');
        	$dataSet->addTable(TB_PRODUCT, FIXTURE_PATH.'product.csv');
        	$dataSet->addTable(TB_MIXGROUP, FIXTURE_PATH.'mixgroup.csv');
    		
    		//	replace '__NULL__' to real NULL
    		$replacedDataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );	
    		    		
        	return $replacedDataSet;
    	}
    	
		private function createEmptyEmissionsExpected($startDate,$endDate,$emissionList)
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
    	
    	private function createProducts()
    	{
    		$products = array("XPB44447(0811-F1)","PV150006","Cocaine");
    		
    		$this->defaultStartDate = "2011-01-01";
			$this->defaultEndDate = "2011-01-07";
			
			$this->productsExpected = $this->createEmptyEmissionsExpected($this->defaultStartDate,$this->defaultEndDate,$products);
    	}
    	
    	public function testGetProductUsageByDays()
    	{
    		$product = new Product($this->db);
    		$category 	=	"department";
    		$categoryID = 	"212";
    		
    		$prus = $product->getProductUsageByDays($this->defaultStartDate,$this->defaultEndDate,$category,$categoryID);
    		
    		$pr = $this->productsExpected;
    		
    		$pr["XPB44447(0811-F1)"][0] = array(strtotime("2011-01-01")*1000,317.56);
    		
    		$pr["PV150006"][1] = array(strtotime("2011-01-02")*1000,99.96);
    		$pr["PV150006"][2] = array(strtotime("2011-01-03")*1000,14.50);
    		
    		$pr["Cocaine"][0] = array(strtotime("2011-01-01")*1000,8.33);
    		$pr["Cocaine"][2] = array(strtotime("2011-01-03")*1000,87.96);
    		
    		
    		
    		$this->assertEquals($prus, $pr);
    		
    	}
	}
?>