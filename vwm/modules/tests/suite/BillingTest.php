<?php
	chdir('../../..');
	
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/CsvDataSet.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/ReplacementDataSet.php';		
	
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');									
	
	
	class BillingTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;		
		protected $db;
		
		protected $billingPlanDetailsForCustomer2 = array(		
				'billingID'			=> 2,
    			'name' 				=> '1 Source, 6 Months, Self',
    			'description' 		=> '',
    			'one_time_charge'	=> '160.00',
    			'bplimit'	 		=> '1',
    			'months_count' 		=> '6',
    			'price' 			=> '576.00',
    			'type'				=> 'self',
    			'defined'			=> 0,
				'customerID'		=> 2,
				'limits'			=> array(
										'MSDS'	=> array (
    										'limit_price_id' 	=> 1,
											'limit_id'			=> 1,
											'default_limit'		=> 50,
											'increase_cost'		=> '67.00',
											'max_value'			=> 100,
											'increase_step' 	=> 10,
											'unit_type'			=> null
    										), 
    									'memory'=>  array ( 
											'limit_price_id' 	=> 2,
											'limit_id'			=> 2,
											'default_limit'		=> 300,
											'increase_cost'		=> '55.00',
											'max_value'			=> 350,
											'increase_step' 	=> 100,
											'unit_type'			=> 'Mb' 
    										),	
										),
						    			    		
    	);    				
		
		const CSV_NULL = '__NULL__';
		const CURRENT_DATE = '2010-12-25';

	
		public function __construct() {
			//	Start xnyo Framework
			require ('modules/xnyo/startXnyo.php');		
			$GLOBALS["db"]->select_db(DB_NAME);	
			$this->db = $GLOBALS["db"];						
					
			//	connect to DB
        	$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);

        	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);        	        	 
    	}

    	
    	protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}


	    protected function getDataSet() {
	    	//The constructor takes three parameters: $delimiter, $enclosure and $escape
        	$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(';','"','\\');
        	$dataSet->addTable(TB_VPS_CUSTOMER, FIXTURE_PATH.'vps_customer.csv');
        	$dataSet->addTable(TB_VPS_CUSTOMER_LIMIT, FIXTURE_PATH.'vps_customer_limit.csv');
        	$dataSet->addTable(TB_COMPANY, FIXTURE_PATH.'company.csv');
        	$dataSet->addTable(TB_VPS_BILLING, FIXTURE_PATH.'vps_billing.csv');        	
    		$dataSet->addTable(TB_VPS_INVOICE, FIXTURE_PATH.'vps_invoice.csv');
    		$dataSet->addTable(TB_VPS_INVOICE_ITEM, FIXTURE_PATH.'vps_invoice_item.csv');
    		$dataSet->addTable(TB_VPS_MODULE2CUSTOMER, FIXTURE_PATH.'vps_module2customer.csv');
    		$dataSet->addTable(TB_VPS_PAYMENT, FIXTURE_PATH.'vps_payment.csv');
    		$dataSet->addTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN, FIXTURE_PATH.'vps_schedule_customer_plan.csv');
    		$dataSet->addTable(TB_VPS_SCHEDULE_LIMIT, FIXTURE_PATH.'vps_schedule_limit.csv');
    		
    		//	replace '__NULL__' to real NULL
    		$replacedDataSet = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($dataSet, array( self::CSV_NULL => null) );	
    		    		
        	return $replacedDataSet;
    	}
    	    	
    	    	
    	public function testGetCustomerPlan() {
    		
    		$billing = new Billing($this->db);    		
 				                
    		$customerID = array();
    		$expectedResult = array();
    		
    		//	prepare test data for successful assert
    		$customerID[0] = 2;    		    		
    		$expectedResult[0] = $this->billingPlanDetailsForCustomer2;
    		
    		//	TODO: complete this case
    		//$customerID[1] = 111;	//	without billing plan
    		//$expectedResult[1] = false;
    		
    		$customerID[2] = 100;	//	no such customer
    		$expectedResult[2] = false;
    		
    		//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $billing->getCustomerPlan($customerID[$key]));	
    		}
    		
    	}
    	
    	
    	
    	
   		public function testAddCustomerPlanV1() {
   			//$this->markTestIncomplete();
   			$billing = new Billing($this->db);
   			   			    		
    		$customerID = 6;
    		$billingID = 2;    		
    		$billing->addCustomerPlan($customerID, $billingID);
    		    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['customer_limit'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_CUSTOMER, TB_VPS_CUSTOMER_LIMIT), __FUNCTION__);
    		    		    	
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $outputTable['customer_limit']);    	    				    		
    	}
    	
    	
    	
    	
    	public function testAddCustomerPlanV2() {
    		$billing = new Billing($this->db);
   			   			    		
    		$customerID = 7;    		    		
    		$billing->addCustomerPlan($customerID);
    		    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['customer_limit'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_CUSTOMER, TB_VPS_CUSTOMER_LIMIT), __FUNCTION__);
    		    		    	
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $outputTable['customer_limit']);
    	}
    	
    	
    	
    	/**
    	 * with scheduled billing plan
    	 */	
    	public function testSetCustomerPlanV1() {
    		$customerID = 2;
    		$billingID = 4;
    		
    		$billing = new Billing($this->db);
    		$billing->setCustomerPlan($customerID, $billingID);
    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['customer_limit'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_CUSTOMER, TB_VPS_CUSTOMER_LIMIT), __FUNCTION__);
    		    		    	
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $outputTable['customer_limit']);
    	}
    	
    	
		/**
		 * without scheduled billing plan
		 */	
    	public function testSetCustomerPlanV2() {
    		$customerID = 4;
    		$billingID = 5;
    		
    		$billing = new Billing($this->db);
    		$billing->setCustomerPlan($customerID, $billingID);
    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);

    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_CUSTOMER), __FUNCTION__);
    		    		    	
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    	}
    	
    	
    	
    	
    	public function testGetBillingPlanDetails() {    
			$billing = new Billing($this->db);
    		 	    		
    		//	with default limits $customerID = false    		
    		$billingID = 2;    	    
    		$customerID = false;		
    		$expectedResult = array (
				'billingID'			=> 2,
    			'name' 				=> '1 Source, 6 Months, Self',
    			'description' 		=> '',
    			'one_time_charge'	=> '160.00',
    			'bplimit'	 		=> '1',
    			'months_count' 		=> '6',
    			'price' 			=> '576.00',
    			'type'				=> 'self',
    			'defined'			=> 0    			    			    		
    		);
    		$memoryLimit = array ( 
				'limit_price_id' 	=> 2,
				'limit_id'			=> 2,
				'default_limit'		=> 300,
				'increase_cost'		=> '55.00',
				'max_value'			=> 300,
				'increase_step' 	=> 100,
				'unit_type'			=> 'Mb' 
    		);
    		$MSDSLimit = array (
    			'limit_price_id' 	=> 1,
				'limit_id'			=> 1,
				'default_limit'		=> 50,
				'increase_cost'		=> '67.00',
				'max_value'			=> 50,
				'increase_step' 	=> 10,
				'unit_type'			=> null
    		);
    		$expectedResult['limits'] = array('MSDS' => $MSDSLimit, 'memory' => $memoryLimit);	
    		
    		$this->assertEquals($expectedResult,$billing->getBillingPlanDetails($billingID, $customerID));    		    		
    		
    		//	with customer's limits
    		$billingID = 2;    	
    		$customerID = 2;    		
			$expectedResult = $this->billingPlanDetailsForCustomer2;   
			 		
    		$this->assertEquals($expectedResult, $billing->getBillingPlanDetails($billingID, $customerID));	    		
    	}
    	
    	
    	public function testGetScheduledPlanByCustomer() {    
    		$this->markTestIncomplete();		
    		$customerID = array();
    		$expectedResult = array();
    		
    		//	when scheduled plan is set
    		$customerID[0] = 105;
    		$expectedResult[0] = array (
				'id' 		=> 1,
    			'billingID' => 2,
    			'type'		=> 'bpEnd',
    			'limits'	=> array (
    							0	=> 3,
    							1	=> 4
    						)    			
    		);
    		
    		//	when scheduled plan is NOT set
    		$customerID[1] = 111;
    		$expectedResult[1] = false;
    		
    		//	assertions
    		foreach ($customerID as $key=>$value) {
    			$this->assertEquals($expectedResult[$key],$this->billing->getScheduledPlanByCustomer($customerID[$key]));
    		}
    	}
    	
    	
    	
    	
    	public function testSetScheduledPlanV1() {
    		$customerID = 4;
    		$billingID = 1;
    		$type = 'bpEnd';    		
    		
    		$billing = new Billing($this->db);
    		$billing->currentDate = self::CURRENT_DATE;
    		
    		$this->assertEquals('add', $billing->setScheduledPlan($customerID, $billingID, $type));
    		
    		$tables = array(TB_VPS_CUSTOMER, TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM, 
    						TB_VPS_SCHEDULE_CUSTOMER_PLAN, TB_VPS_SCHEDULE_LIMIT);
    		foreach ($tables as $table) {
    			$outputTable[$table] = $this->getConnection()->createDataSet()->getTable($table);
    		}				

    		//	take expected output
    		$dataSet = $this->_loadExpectedTables($tables, __FUNCTION__);
    		
    		foreach ($tables as $table) {
    			$this->assertTablesEqual($dataSet->getTable($table), $outputTable[$table]);
    		}     		
    	}
    	
    	
    	
    	public function testSetScheduledPlanV2() {
    		$customerID = 4;
    		$billingID = 1;
    		$type = 'bpEnd';
    		$limitPrice = array(5, 6);    		
    		
    		$billing = new Billing($this->db);
    		$billing->currentDate = self::CURRENT_DATE;
    		
    		$this->assertEquals('add', $billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice));
    		
    		$tables = array(TB_VPS_CUSTOMER, TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM, 
    						TB_VPS_SCHEDULE_CUSTOMER_PLAN);

			// a little hack. expected data is the same as from previsos test except TB_VPS_SCHEDULE_LIMIT
    		foreach ($tables as $table) {
    			$outputTable[$table] = $this->getConnection()->createDataSet()->getTable($table);
    		}	
    		$outputTable[TB_VPS_SCHEDULE_LIMIT] = $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT);			

    		//	take expected output
    		$dataSet = $this->_loadExpectedTables($tables, 'testSetScheduledPlanV1');
    		$dataSetScheduledLimits = $this->_loadExpectedTables(array(TB_VPS_SCHEDULE_LIMIT), __FUNCTION__);
    			    			    		
    		foreach ($tables as $table) {
    			$this->assertTablesEqual($dataSet->getTable($table), $outputTable[$table]);
    		}     		
    	}
    	
    	
    	
    	
    	//			<<	testSetScheduledPlan scheme	>>
    	//	testVar	|	SQL action	|	limits	|	change type
    	//	--------+---------------+-----------+--------------
    	//		1	|	insert		|	default	|	bpEnd
    	//		2	|	insert		|	default	|	asap
    	//		3	|	insert		|	defined	|	bpEnd
    	//		4	|	insert		|	defined	|	asap
    	//		5	|	update		|	default	|	bpEnd
    	//		6	|	update		|	default	|	asap
    	//		7	|	update		|	defined	|	bpEnd
    	//		8	|	update		|	defined	|	asap		
    	
    	public function testSetScheduledPlanVar1() {
    		$this->markTestIncomplete();
    		//	INSERT scheduled plan with DEFAULT limits, BPEND type
    		
    		//	inputs
    		$customerID = 116;
    		$billingID = 1;
    		$type = 'bpEnd';
    		$limitPrice = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar1.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_end_date']." +1 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
						
			
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan1.xml', PATH_BRIDGE_XML);
    	}
    	
    	public function testSetScheduledPlanVar2() {
    		$this->markTestIncomplete();
    		//	INSERT scheduled plan with DEFAULT limits, ASAP type
    		
    		//	inputs
    		$customerID = 116;
    		$billingID = 1;
    		$type = 'asap';
    		$limitPrice = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar2.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime("+1 months"));	//	suspension date equal period_end_date
								$values->item(11)->nodeValue = date('Y-m-d');							//	period_start_date equal generation date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime("+1 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);			
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$bridgeDataSetPath = dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan2.xml';
    		$this->DOM->load($bridgeDataSetPath);
    		$this->DOM->getElementsByTagName('customer')->item(5)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime("+1 months"));    		
    		$this->DOM->save($bridgeDataSetPath);
    		$this->assertXmlFileEqualsXmlFile($bridgeDataSetPath, PATH_BRIDGE_XML);
    	}
    	
	 	public function testSetScheduledPlanVar3() {
	 		$this->markTestIncomplete();
    		//	INSERT scheduled plan with DEFINED limits, BPEND type
    		
    		//	inputs
    		$customerID = 116;
    		$billingID = 3;
    		$type = 'bpEnd';
    		$limitPrice = array(5, 6);
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar3.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_end_date']." +6 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan1.xml', PATH_BRIDGE_XML);
    	}
    	
    	public function testSetScheduledPlanVar4() {
    		$this->markTestIncomplete();
    		//	INSERT scheduled plan with DEFINED limits, ASAP type
    		
    		//	inputs
    		$customerID = 116;
    		$billingID = 3;
    		$type = 'asap';
    		$limitPrice = array(5, 6);
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar4.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	suspension date is equal to prev period_end_date invoice
								$values->item(11)->nodeValue = date('Y-m-d');							//	period_start_date equal generation date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime("+6 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);		
			
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan1.xml', PATH_BRIDGE_XML);
    	}
    	
    	public function testSetScheduledPlanVar5() {
    		$this->markTestIncomplete();
    		//	UPDATE scheduled plan with DEFAULT limits, BPEND type
    		
    		//	inputs
    		$customerID = 105;
    		$billingID = 2;
    		$type = 'bpEnd';
    		$limitPrice = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar5.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']." +6 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
						
			
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan5.xml', PATH_BRIDGE_XML);	
    	}
    	
    	public function testSetScheduledPlanVar6() {
    		$this->markTestIncomplete();
    		//		6	|	update		|	default	|	asap
    		
    		//	inputs
    		$customerID = 105;
    		$billingID = 2;
    		$type = 'asap';
    		$limitPrice = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar6.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = date('Y-m-d');	//	period_start_date equal generation date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime(" +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan5.xml', PATH_BRIDGE_XML);    		
    	}
    	
    	public function testSetScheduledPlanVar7() {
    		$this->markTestIncomplete();
    	//		7	|	update		|	defined	|	bpEnd
    	//	UPDATE scheduled plan with DEFINED limits, BPEND type
    		
    		//	inputs
    		$customerID = 105;
    		$billingID = 2;
    		$type = 'bpEnd';
    		$limitPrice = array(5, 6);
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar7.xml';
    		    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']." +6 months"));	//	period_finish_date = period_start_date +1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
						
			
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan5.xml', PATH_BRIDGE_XML);
    	} 
    	
    	public function testSetScheduledPlanVar8() {
    		$this->markTestIncomplete();
		//		8	|	update		|	defined	|	asap		
    	//	UPDATE scheduled plan with DEFINED limits, ASAP type
    	//	inputs
    		$customerID = 105;
    		$billingID = 2;
    		$type = 'asap';
    		$limitPrice = array(5, 6);
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/setScheduledPlanVar8.xml';    		            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = date('Y-m-d');	//	period_start_date equal generation date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime(" +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->billing->setScheduledPlan($customerID, $billingID, $type, $limitPrice);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridge-set-scheduled-plan5.xml', PATH_BRIDGE_XML);   
    	}
    	
    	
	   	public function testGetAvailablePlans() {   
	   		$this->markTestIncomplete(); 		
    		$expectedResult = array (
				array (
    			    'billingID' 		=> 1,
    			    'one_time_charge'	=> '7.00',
            		'bplimit'			=> 1,
            		'months_count' 		=> 1,
            		'price' 			=> '9.99',
            		'type'				=> 'self'
    			),
    			array (
    				'billingID' 		=> 2,
    			    'one_time_charge'	=> '30.00',
            		'bplimit'			=> 2,
            		'months_count' 		=> 6,
            		'price' 			=> '99.99',
            		'type'				=> 'self'
    			)    			
    		);
    		$this->assertEquals($expectedResult,$this->billing->getAvailablePlans());
    	}
    	
    	
    	public function testDeletePlanFromSchedule() {
    		$this->markTestIncomplete();
    		$scheduleID = 2;
    		    		
    		$this->billing->deletePlanFromSchedule($scheduleID);
    		
    		$xmlDataSet = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/vps_scheduled_company_plan-after-delete-seed.xml');
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
    	}
    	
    	
    	public function testGetDistinctMonths() {    	
    		$this->markTestIncomplete();	    	    		
    		$expectedResult = array (1, 6);			
			$this->assertEquals($expectedResult,$this->billing->getDistinctMonths());
    	}
    	
    	
    	public function testGetDistinctSource() {    	
    		$this->markTestIncomplete();		
    		$expectedResult = array (array('bplimit'=>1, 'one_time_charge'=>'7.00'), array('bplimit'=>2, 'one_time_charge'=>'30.00'));			
			$this->assertEquals($expectedResult,$this->billing->getDistinctSource());
   		}
   		
   		public function testGetAvailableExtraLimits() {
   			$this->markTestIncomplete();
   			$expectedResult = array ();
   			$expectedResult[0] = array (
   				'limit_price_id'	=> 1,
   				'name'				=> 'MSDS',
   				'increase_step'		=> 10,
   				'unit_type'			=> '',
   				'bplimit'			=> 1,
   				'default_limit'		=> 50,
   				'increase_cost'		=> '67.00',
   				'type'				=> 'self' 
   			);	
   			$expectedResult[1] = array (
   				'limit_price_id'	=> 2,
   				'name'				=> 'memory',
   				'increase_step'		=> 100,
   				'unit_type'			=> 'Mb',
   				'bplimit'			=> 1,
   				'default_limit'		=> 300,
   				'increase_cost'		=> '67.00',
   				'type'				=> 'self' 
   			);
   			$expectedResult[2] = array (
   				'limit_price_id'	=> 3,
   				'name'				=> 'MSDS',
   				'increase_step'		=> 10,
   				'unit_type'			=> '',
   				'bplimit'			=> 2,
   				'default_limit'		=> 60,
   				'increase_cost'		=> '70.00',
   				'type'				=> 'self' 
   			);
   			$expectedResult[3] = array (
   				'limit_price_id'	=> 4,
   				'name'				=> 'memory',
   				'increase_step'		=> 100,
   				'unit_type'			=> 'Mb',
   				'bplimit'			=> 2,
   				'default_limit'		=> 310,
   				'increase_cost'		=> '70.00',
   				'type'				=> 'self' 
   			);
   			$expectedResult['sources'] = array (1, 2);
   			$MSDS = array (
   				'limit_id'		=> 1,
   				'name'			=> 'MSDS',
   				'increase_step'	=> 10,
   				'unit_type'		=> '',
   				'type'			=> 'limit'
   			);
   			$memory = array (
   				'limit_id'		=> 2,
   				'name'			=> 'memory',
   				'increase_step'	=> 100,
   				'unit_type'		=> 'Mb',
   				'type'			=> 'limit'
   			);
   			$sourceCount = array (
   				'limit_id'		=> 3,
   				'name'			=> 'Source count',
   				'increase_step'	=> 1,
   				'unit_type'		=> '',
   				'type'			=> 'bplimit'
   			);
   			$expectedResult['info'] = array ('MSDS'=>$MSDS, 'memory'=>$memory, 'Source count'=>$sourceCount);
   			
   			$this->assertEquals($expectedResult,$this->billing->getAvailableExtraLimits());
   			
   		}
   		
   		public function testGetDefinedPlans() {
   			$this->markTestIncomplete();
   			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET billing_id = 3 WHERE customer_id = 116";
   			$this->db->query($query);
   			
   			$expectedResult = array (
				array (
    			    'billingID' 		=> 3,
    			    'customer_id'		=> 116,
    			    'bplimit'			=> 1,
    			    'months_count' 		=> 6,
    			    'one_time_charge'	=> '45.00',
       	    		'price' 			=> '200.00',
            		'type'				=> 'gyant'
    			)
    		);
    		$MSDSLimit = array ( 
				'limit_price_id' 	=> 3,
				'limit_id'			=> 1,
				'default_limit'		=> 60,
				'increase_cost'		=> '70.00',
				'max_value'			=> 60,
				'increase_step' 	=> 10,
				'unit_type'			=> null 
    		);
    		$memoryLimit = array (
    			'limit_price_id' 	=> 4,
				'limit_id'			=> 2,
				'default_limit'		=> 310,
				'increase_cost'		=> '70.00',
				'max_value'			=> 310,
				'increase_step' 	=> 100,
				'unit_type'			=> 'Mb'
    		);
    		$expectedResult[0]['limits'] = array('MSDS' => $MSDSLimit, 'memory' => $memoryLimit);
    		 
   			$this->assertEquals($expectedResult,$this->billing->getDefinedPlans());
   			$this->assertEquals($expectedResult,$this->billing->getDefinedPlans(116));
   		} 
    	
    	
    	//			<<	testUpdateBillingPlan scheme	>>
    	//	testVar	|	defined	billing plan
    	//	--------+------------
    	//		1	|	true
    	//		2	|	false
    	
    	public function testUpdateBillingPlanVar1() {
    		$this->markTestIncomplete();
    	//		1	|	true
    	
    	//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET billing_id = 3 WHERE customer_id = 116";
   			$this->db->query($query);
   			$query = "DELETE FROM ".TB_VPS_CUSTOMER_LIMIT." WHERE customer_id = 116";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_CUSTOMER_LIMIT." (id, customer_id, limit_price_id, max_value) VALUES " .
   					"(5, 116, 5, 100), " .
   					"(6, 116, 6, 400)";
   			$this->db->query($query);
    	
    		$input = array (
    			'billingID'			=> 3,
    			'customerID'		=> 116,
    			'bplimit'			=> 1,
    			'monthsCount'		=> 6,
		 		'oneTimeCharge'		=> '50.00',	
		 		'price'				=> '201.00',
		 		'type'				=> 'gyant',
		 		'MSDSDefaultLimit'	=> '101',
		 		'MSDSIncreaseCost'	=> '101.00',
		 		'memoryDefaultLimit'=> '404',
		 		'memoryIncreaseCost'=> '404.00',
		 		'defined' 			=> 1     			
    		);    		
    		$xmlFlatDataSet = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/updateBillingPlanVar1.xml');
    		
    		$this->billing->updateBillingPlan($input);
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_BILLING), $this->getConnection()->createDataSet()->getTable(TB_VPS_BILLING));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_LIMIT_PRICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_LIMIT_PRICE));
    	}
    	
    	public function testUpdateBillingPlanVar2() {
    		$this->markTestIncomplete();
    	//		1	|	false
    	
    	//	input data
    		$input = array (
    	 		'billingID'		=> 1,
		 		'oneTimeCharge'	=> '10.00',
		 		'price'			=>	'10.00',	
		 		'defined'		=> 0 				
    	 	);
    		$xmlFlatDataSet = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/updateBillingPlanVar2.xml');
    		$this->billing->updateBillingPlan($input);
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_BILLING), $this->getConnection()->createDataSet()->getTable(TB_VPS_BILLING));
    	}
    	
    	
    	//			<<	testAddDefinedBillingPlan scheme	>>
    	//	testVar	|	customer already has billing plan
    	//	--------+--------------------------------------
    	//		1	|				true
    	//		2	|				false
    	
    	public function testAddDefinedBillingPlanVar1 () {
    		$this->markTestIncomplete();
   		//		1	|				true
			$input = array (
    			'customerID'		=> 116,
    			'bplimit'			=> 1,
    			'monthsCount'		=> 6,
		 		'oneTimeCharge'		=> '50.00',	
		 		'price'				=> '201.00',
		 		'type'				=> 'gyant',
		 		'MSDSDefaultLimit'	=> '101',
		 		'MSDSIncreaseCost'	=> '101.00',
		 		'memoryDefaultLimit'=> '404',
		 		'memoryIncreaseCost'=> '404.00',
		 		'requestID'			=> 1,
		 		'applyWhen'			=> 'bpEnd' 				
    	  	);
    	  	$xmlFlatDataSet = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/addDefinedBillingPlanVar1.xml');
    	  	
    	  	//	prepare dynamic fields at dataSet for invoices
    		$invoicesDataSetPath = dirname(__FILE__).'/_files/addDefinedBillingPlanVar1_invoices.xml';    		            
			$this->DOM->load($invoicesDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_end_date'];	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_end_date']." +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($invoicesDataSetPath);
    	  	$invoicesDataSet = $this->createXMLDataSet($invoicesDataSetPath);
    	  	
    		$this->billing->addDefinedBillingPlan($input);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_BILLING), $this->getConnection()->createDataSet()->getTable(TB_VPS_BILLING));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_DEFINED_BP_REQUEST), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEFINED_BP_REQUEST));
    		$this->assertTablesEqual($invoicesDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_LIMIT_PRICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_LIMIT_PRICE));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT)); 
    	}
    	
    	public function testAddDefinedBillingPlanVar2 () {
    		$this->markTestIncomplete();
   		//		2	|				false
			$input = array (
    			'customerID'		=> 111,
    			'bplimit'			=> 1,
    			'monthsCount'		=> 6,
		 		'oneTimeCharge'		=> '50.00',	
		 		'price'				=> '201.00',
		 		'type'				=> 'gyant',
		 		'MSDSDefaultLimit'	=> '101',
		 		'MSDSIncreaseCost'	=> '101.00',
		 		'memoryDefaultLimit'=> '404',
		 		'memoryIncreaseCost'=> '404.00',
		 		'requestID'			=> 1,
		 		'applyWhen'			=> 'asap' 				
    	  	);
    	  	$xmlFlatDataSet = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/addDefinedBillingPlanVar2.xml');
    	  	
    	  	//	prepare dynamic fields at dataSet for invoices
    		$invoicesDataSetPath = dirname(__FILE__).'/_files/addDefinedBillingPlanVar2_invoices.xml';    		            
			$this->DOM->load($invoicesDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = '2009-11-16';	//	suspension date equal period_end_date of prev invoice
								$values->item(11)->nodeValue = '2009-11-16';	//	period_start_date equal suspension date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime("2009-11-16 +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($invoicesDataSetPath);
    	  	$invoicesDataSet = $this->createXMLDataSet($invoicesDataSetPath);
    	  	
    	  	$this->billing->addDefinedBillingPlan($input);
    	  	
    	  	//	assertions
    	  	$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_BILLING), $this->getConnection()->createDataSet()->getTable(TB_VPS_BILLING));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_DEFINED_BP_REQUEST), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEFINED_BP_REQUEST));
    		$this->assertTablesEqual($invoicesDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_LIMIT_PRICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_LIMIT_PRICE));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT)); 
    	}
    	
    	//			<<	testiInvoiceIncreaseLimit scheme	>>
    	//	testVar	|	limitName
    	//	--------+----------------
    	//		1	|	MSDS
    	//		2	|	memory
    	
    	public function testiInvoiceIncreaseLimitVar1() {
    		$this->markTestIncomplete();
    	//			1	|	MSDS
    	
    		//	prepare inputs
			$limitName = "MSDS";
    		$customerID = 105;
    		$plusToValue = 25;
    		
    		//	prepare dynamic fields at dataSet for invoices
    		$dataSetPath = dirname(__FILE__).'/_files/invoiceIncreaseLimitVar1.xml';    		            
			$this->DOM->load($dataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime("+30 days"));	//	suspension date see at config
								break;
						}							
					}										
				}
			}
			$this->DOM->save($dataSetPath);  
    		$xmlDataSet = $this->createXMLDataSet($dataSetPath);
    		
    		$this->billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));	
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    	}
    	
    	
    	public function testiInvoiceIncreaseLimitVar2() {
    		$this->markTestIncomplete();
    	//			2	|	memory
    	
    		//	prepare inputs
			$limitName = "memory";
    		$customerID = 105;
    		$plusToValue = 25;
    		
    		//	prepare dynamic fields at dataSet for invoices
    		$dataSetPath = dirname(__FILE__).'/_files/invoiceIncreaseLimitVar2.xml';    		            
			$this->DOM->load($dataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('table');
			foreach ($elements as $element) {
				$tableName = $element->getAttribute('name');							
				if ($tableName == 'vps_invoice') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '1':
								$values->item(9)->nodeValue = $this->invoiceDates[0]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[0]['period_end_date'];
								break;
							case '2':
								$values->item(9)->nodeValue = $this->invoiceDates[1]['generation_date'];
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
							case '3':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime("+30 days"));	//	suspension date see at config
								break;
						}							
					}										
				}
			}
			$this->DOM->save($dataSetPath);  
    		$xmlDataSet = $this->createXMLDataSet($dataSetPath);
    		
    		$this->billing->invoiceIncreaseLimit($limitName, $customerID, $plusToValue);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));	
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    	}
    	
    	
    	//			<<	testIncreaseLimit scheme	>>
    	//	testVar	|	limitName
    	//	--------+----------------
    	//		1	|	MSDS
    	//		2	|	memory
    	
    	public function testIncreaseLimitVar1() {
    		$this->markTestIncomplete();
    		//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-167.50' WHERE customer_id = 105";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '0.00', '167.50', '0.00', '167.50', '0.00', '167.50', '-167.50', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+1 months'))."', null, null, null, 'Increase MSDS + 25 ', null, 'due', 0)";
   			$this->db->query($query);
   			
    		$invoiceID = 3;
    		$dataSetPath = dirname(__FILE__).'/_files/increaseLimitVar1.xml';
    		$xmlFlatDataSet = $this->createFlatXMLDataSet($dataSetPath);
    		
    		$this->billing->increaseLimit($invoiceID);
    		
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeIncreaseInvoiceVar1.xml', PATH_BRIDGE_XML); 
    		
    	}
    	
    	public function testIncreaseLimitVar2() {
    		$this->markTestIncomplete();
    		//		2	|	memory
    		
    		//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-16.75' WHERE customer_id = 105";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '0.00', '16.75', '0.00', '16.75', '0.00', '16.75', '-16.75', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+1 months'))."', null, null, null, 'Increase memory + 25 Mb', null, 'due', 0)";
   			$this->db->query($query);
   			
    		$invoiceID = 3;
    		$dataSetPath = dirname(__FILE__).'/_files/increaseLimitVar2.xml';
    		$xmlFlatDataSet = $this->createFlatXMLDataSet($dataSetPath);
    		
    		$this->billing->increaseLimit($invoiceID);
    		
    		$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeIncreaseInvoiceVar2.xml', PATH_BRIDGE_XML); 
    	}
    	
    	
    	public function testSaveDefinedBillingPlanRequest() {
    		$this->markTestIncomplete();    		
    		$request = array (
    	 	 	'customerID'		=>	105,
	    		'bplimit'			=>	3,
	    		'monthsCount'		=>	6,
	    		'type'				=>	"gyant",
	    		'MSDSDefaultLimit'	=>	500,
	    		'memoryDefaultLimit'=>	5000,
	    		'description'		=>	"' or die();",
	    		'date'				=>	"2009-11-16"
    	 	);
    	 	
    	 	$dataSetPath = dirname(__FILE__).'/_files/saveDefinedBillingPlanRequest.xml';
    		$xmlFlatDataSet = $this->createFlatXMLDataSet($dataSetPath);
    		
    	 	$this->billing->saveDefinedBillingPlanRequest($request);
    	 	
    	 	$this->assertTablesEqual($xmlFlatDataSet->getTable(TB_VPS_DEFINED_BP_REQUEST), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEFINED_BP_REQUEST));
    	}
    	
    	
    	public function testGetRequest() {
    		$this->markTestIncomplete();
    		$requestID = array();
    		$expectedResult = array();
    		
    		//	prepare test data for successful assert
    		$requestID[0] = 'All';    		    		
    		$expectedResult[0] = array(
    			array (
					'id' 			=> 1,
            		'customerID' 	=> 116,
            		'bplimit'		=> 1,
            		'monthsCount'	=> 6,
            		'type'			=> 'gyant',
            		'MSDSLimit'	 	=> 101,
            		'memoryLimit'	=> 404,
            		'description'	=> 'Work on saturdays!',
            		'date'		 	=> '2009-11-14',
            		'status'	 	=> 'unprocessed'
				)
    		);
    		
    		$requestID[1] = 1;
    		$expectedResult[1] = $expectedResult[0];
    		
    		$requestID[2] = 100;	//	no such request
    		$expectedResult[2] = false;
    		
    		//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->billing->getRequest($requestID[$key]));	
    		}    		    		
    	} 
    	
    	
    	public function testCountRequests() {
    		$this->markTestIncomplete();
    		$expectedResult = array('unprocessed' => 1);
    		$this->assertEquals($expectedResult, $this->billing->countRequests());
    		
    		//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "INSERT INTO ".TB_VPS_DEFINED_BP_REQUEST." (customer_id, bplimit, months_count, type, MSDS_limit, memory_limit, description, date, status) VALUES ( " .
    			 "105, 5, 12, 'self', 777, 666, '', '2009-11-16', 'processed')";
   			$this->db->query($query);
   			
   			$expectedResult = array('unprocessed' => 1, 'processed' => 1);
    		$this->assertEquals($expectedResult, $this->billing->countRequests());
    		
    		$query = "TRUNCATE TABLE ".TB_VPS_DEFINED_BP_REQUEST."";
   			$this->db->query($query);
   			
   			$expectedResult = false;
    		$this->assertEquals($expectedResult, $this->billing->countRequests());
    	}
    	
    	public function testGetTrialLimitPriceDetails() {
    		$this->markTestIncomplete();
    		$limitID[0] = 1;
    		$expectedResult[0] = array(
    			'limit_id'		=> 1,
    			'bplimit'		=> 1,
    			'default_limit'	=> 50,
    			'type'			=> 'self'
    		);
    		$limitID[1] = 2;
    		$expectedResult[1] = array(
    			'limit_id'		=> 2,
    			'bplimit'		=> 1,
    			'default_limit'	=> 300,
    			'type'			=> 'self'
    		);
    		$limitID[2] = 3;
    		$expectedResult[2] = false;
    		
    		//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->billing->getTrialLimitPriceDetails($limitID[$key]));	
    		}    
    	}
    	
    	public function testGetMinBPLimitCount() {
    		$this->markTestIncomplete();
    		$this->assertEquals(1, $this->billing->getMinBPLimitCount());
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
