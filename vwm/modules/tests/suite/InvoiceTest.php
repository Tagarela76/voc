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
	
	
	class InvoiceTest extends PHPUnit_Extensions_Database_TestCase {
		
		protected $pdo;		
		protected $db;

		protected $expectedDetailesOfCustomer2 = array (
            		"company_id" => 2,
            		"name" => 'TestCompany',
            		"address" => '&#60;b&#62;gdfgdf&#60;&#47;b&#62;',
            		"city" => 'hkjhkh',
            		"zip" => '74654',
            		"county" => 'k',
            		"state" => null, 
            		"phone" => 'kgh',
            		"fax" => 'jg',
            		"email" => 'ghjgd&#64;o.ia',
            		"contact" => 'gd',
            		"title" => 'dfgfdg',
            		"creater_id" => 1,
            		"country" => 215,
            		"gcg_id" => 41,
            		"trial_end_date" => '2010-05-05',
            		"voc_unittype_id" => 2,
            		"customer_id" => 2,
            		"billing_id" => 2,
            		"status" => 'on',
            		"discount" => '0.00',
            		"balance" => '0.00',
					"currency_id"	=> 1,
            		"period_end_date" => '2011-01-04',
            		"deadline_counter" => 17
		        );
		 		
		        
		const CSV_NULL = '__NULL__';
		const CURRENT_DATE = '2010-12-18';
	
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
    	    	

    	public function testCreateMultiInvoiceForNewCustomer() {
    		
 //   		$this->markTestSkipped();
            
    		//	setup input data
    		$customerID = 2;
    		$periodStartDate = self::CURRENT_DATE;
    		$billingID = 5;
    		$multiInvoiceData = array(
    			'billingID' => 5,
    			'appliedModules' => array (
    				array(
    					'id' 			=> '20',
          				'month_count' 	=> '6',
          				'price' 		=> '800.00',
          				'module_id' 	=> '12',
          				'type' 			=> 'self',
          				'module_name'	=> 'carbon_footprint',
    				),
    			),    			
    			'not_approach_modules' => array(
    				array(
    					'id' 			=> '15',
          				'month_count' 	=> '12',
          				'price' 		=> '1000.00',
          				'module_id' 	=> '4',
          				'type' 			=> 'self',
          				'module_name'	=> 'inventory',
    				),
    				array(
    					'id' 			=> '27',
          				'month_count' 	=> '12',
          				'price' 		=> '1000.00',
          				'module_id' 	=> '11',
          				'type' 			=> 'self',
          				'module_name'	=> 'reduction',
    				),
    			),
    		);

    		//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;

    		//	run original method
    		$invoice->createMultiInvoiceForNewCustomer($customerID, $periodStartDate, $billingID, $multiInvoiceData);
    		
    		//	take real output
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		$outputTable['module2customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_MODULE2CUSTOMER);       		
    		
    		//	take expected output
    		$tables = array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM, TB_VPS_MODULE2CUSTOMER);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);
    		    
			//	assetions
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_MODULE2CUSTOMER), $outputTable['module2customer']);
    		   		
    	}
    	
    	
    	
    	public function testCreateInvoiceForModuleV1() {
    		/*
    		 * First assertion 
    		 */
    		
 //   		$this->markTestSkipped();
    		
    		//	setup input data (should return false - no such billing plan)
    		$customerID = 2;
    		$startDate = self::CURRENT_DATE;
    		$moduleBillingPlanID = 666;
    		
    		//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		//	run original method & assert
    		$this->assertFalse( $invoice->createInvoiceForModule($customerID, $startDate, $moduleBillingPlanID) );
    		
    		
    		/*
    		 * Second assertion 
    		 */
    		$customerID = 2;
    		$startDate = self::CURRENT_DATE;
    		$moduleBillingPlanID = 5;
    		
    		$invoiceID = $invoice->createInvoiceForModule($customerID, $startDate, $moduleBillingPlanID);    		
    		
    		//	take real output
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);    		
    		
    		//	take expected output
    		$tables = array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM);
    		$dataSet = $this->_loadExpectedTables($tables, __FUNCTION__);
  			    		
			//	assetions
			$this->assertEquals(2, $invoiceID, 'Inserted Invoice ID doesn\'t returned');
			$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);    		    
    	}
    	
    	
		public function testCreateInvoiceForModuleV2() {    		
    		
    		/*
    		 * Third assertion 
    		 */
			
//			$this->markTestSkipped();
			
    		$customerID = 2;
    		$startDate = date('Y-m-d',strtotime(self::CURRENT_DATE." + 15 days"));
    		$moduleBillingPlanID = array(15, 27);    			    		
    		
    		//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		$invoiceID = $invoice->createInvoiceForModule($customerID, $startDate, $moduleBillingPlanID);    		
    		
    		//	take actual output
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);    		
    		
    		//	take expected output
			$tables = array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM);
    		$dataSet = $this->_loadExpectedTables($tables,__FUNCTION__);    		
   		
			//	assetions
			$this->assertEquals(2, $invoiceID, 'Inserted Invoice ID doesn\'t returned');
			$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);    		
    	}

    	
    	
    	   
		//			<<	testCreateInvoiceForBilling scheme	>>
    	//	testVar	|	trial		|	asap	|	
    	//	--------+---------------+-----------|
    	//		1	|	false		|	false	|
    	//		2	|	false		|	true	|
    	//		3	|	true		|	false	|
    	//		4	|	true		|	true	|
		public function testCreateInvoiceForBillingV1() {
			
//			$this->markTestSkipped();
			
			$customerID = 2; 
			$periodStartDate = '2011-01-05';
			$billingID = 2;
			$asap = false;
			
			//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		$invoiceData = $invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);
    		
    		$expectedInvoice = array(    			
    			"customerID" =>2,
    			"oneTimeCharge"	=> '160.00',
				"amount" => '576.00',
    			"discount" => 0,
    			"total" => '736',
    			"paid" => '0',
    			"due" => '736',    			
    			"generationDate" => self::CURRENT_DATE,
    			"suspensionDate" => "2011-01-05",
    			"periodStartDate" => "'2011-01-05'",
    			"periodEndDate" => "'2011-07-05'",
    			"billingInfo" => "'Sources: 1, Months: 6, Type: self'",
    			"limitInfo" => "NULL",
    			"customInfo" => "NULL",
    			"module_id" => "NULL",
    			"status" => 'due',
    			"suspensionDisable" => 1,    			
    		);    		
    		$this->assertEquals($expectedInvoice, $invoiceData);	
    		
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM), __FUNCTION__);    		

    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
		}

    	

		public function testCreateInvoiceForBillingV2() {									
			$customerID = 2; 
			$periodStartDate = self::CURRENT_DATE;
			$billingID = 4;
			$asap = true;
			
			//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		$invoiceData = $invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);
    		$expectedInvoice = array(    			
    			"customerID" =>2,
    			"oneTimeCharge"	=> '240.00',
				"amount" => '149.00',
    			"discount" => 7,
    			"total" => '382',
    			"paid" => '0',
    			"due" => '382',    			
    			"generationDate" => self::CURRENT_DATE,
    			"suspensionDate" => "2011-01-04",	//TODO : я сомневаюсь, что дата отключения должна быть именно такой.  
    			"periodStartDate" => "'".self::CURRENT_DATE."'",
    			"periodEndDate" => "'2011-01-18'",
    			"billingInfo" => "'Sources: 2, Months: 1, Type: self, ASAP'",
    			"limitInfo" => "NULL",
    			"customInfo" => "NULL",
    			"module_id" => "NULL",
    			"status" => 'due',
    			"suspensionDisable" => 1,    			
    		);    		
    		$this->assertEquals($expectedInvoice, $invoiceData);	

    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM), __FUNCTION__);    		

    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
		}
    	
		
		
		
		public function testCreateInvoiceForBillingV3() {							
				
			$customerID = 4;
			$periodStartDate = '2011-01-31';
			$billingID = 4;
			$asap = false;
				
			//	setup invoice object
			$invoice = new Invoice($this->db);
			$invoice->currentDate = self::CURRENT_DATE;

			$invoiceData = $invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);			
			$expectedInvoice = array(    			
    			"customerID" => 4,
    			"oneTimeCharge"	=> '240.00',
				"amount" => '149.00',
    			"discount" => 0,
    			"total" => '389',
    			"paid" => '0',
    			"due" => '389',    			
    			"generationDate" => self::CURRENT_DATE,
    			"suspensionDate" => "2011-01-31",
    			"periodStartDate" => "'2011-01-31'",
    			"periodEndDate" => "'2011-03-03'",
    			"billingInfo" => "'Sources: 2, Months: 1, Type: self'",
    			"limitInfo" => "NULL",
    			"customInfo" => "NULL",
    			"module_id" => "NULL",
    			"status" => 'due',
    			"suspensionDisable" => 1,    			
    		);    		
    		$this->assertEquals($expectedInvoice, $invoiceData);    						
			
			$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM), __FUNCTION__);
    		
			$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
    		
    		
    		//	now we have test for future like in real life
    		//	V4
    		$customerID = 4;
			$periodStartDate = self::CURRENT_DATE;
			$billingID = 2;
			$asap = true;
							
			$invoiceData = $invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);
			$expectedInvoice = array(    			
    			"customerID" => 4,
    			"oneTimeCharge"	=> '160.00',
				"amount" => '576.00',
    			"discount" => 0,
    			"total" => '736',
    			"paid" => '0',
    			"due" => '736',    			
    			"generationDate" => self::CURRENT_DATE,
    			"suspensionDate" => "2011-01-31",
    			"periodStartDate" => "'2011-01-31'",
    			"periodEndDate" => "'2011-07-31'",
    			"billingInfo" => "'Sources: 1, Months: 6, Type: self, ASAP'",
    			"limitInfo" => "NULL",
    			"customInfo" => "NULL",
    			"module_id" => "NULL",
    			"status" => 'due',
    			"suspensionDisable" => 1,    			
    		);    				
			$this->assertEquals($expectedInvoice, $invoiceData);
			
			$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);

    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM), 'testCreateInvoiceForBillingV4');
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);    		
		}
		
		
		
    	public function testGetCurrentInvoice() {
    		$invoice = new Invoice($this->db);
    		$invoice->currentDate = self::CURRENT_DATE;
    		   
    		$realOutput = $invoice->getCurrentInvoice(2);   		    		    		

    		$this->assertEquals(1, $realOutput['invoiceID']);
    		
    		
    		$realOutput = $invoice->getCurrentInvoice(1);
    		$this->assertFalse($realOutput);
    	}
    	
    	
    	
    	
    	public function testGetInvoiceDetails() {
    		$invoice = new Invoice($this->db);
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		$invoiceDetails = $invoice->getInvoiceDetails(1);
    		$expectedInvoice = array(
    			"invoiceID" => 1,
    			"customerID" =>2,
    			"oneTimeCharge"	=> 0,
				"amount" => '100',
    			"discount" => '0.00',
    			"total" => '100.00',
    			"paid" => '100.00',
    			"due" => '0.00',
    			"balance" => '0.00',
    			"generationDate" => '2010-05-05',
    			"suspensionDate" => '2010-05-06',
    			"periodStartDate" => '2010-05-06',
    			"periodEndDate" => '2011-01-04',
    			"billingInfo" => 'Sources: 1, Months: 6, Type: self',
    			"limitInfo" => null,
    			"customInfo" => null,
    			"moduleID" => null,
    			"status" => 'PAID',
    			"currency_id"	=> 1,
    			"suspensionDisable" => true,
    			"daysLeft2BPEnd" => 17,
    			"daysCountAtBP" => 243,
    			'customerDetails' => $this->expectedDetailesOfCustomer2,
    			'module_name' => null 		            	
    		);    		
    		
    		//	why not assert whole array? because of phpunit bugs with array asserting.. 
    		foreach ($expectedInvoice as $key=>$value) {
    			$this->assertEquals($expectedInvoice[$key], $invoiceDetails[$key], 'Failed at field: '.$key);	
    		}    		
    	}
    	
    	
    	// TODO: check payments
    	// TODO: check billing plan changing
    	public function testUpdateInvoiceV1() {
    		$invoice = new Invoice($this->db);
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		//	first create invoice (smth like fixture)
    		$customerID = 2;
    		$periodStartDate = self::CURRENT_DATE;
    		$billingID = 5;
    		$multiInvoiceData = array(
    			'billingID' => 5,
    			'appliedModules' => array (
    				array(
    					'id' 			=> '20',
          				'month_count' 	=> '6',
          				'price' 		=> '800.00',
          				'module_id' 	=> '12',
          				'type' 			=> 'self',
          				'module_name'	=> 'carbon_footprint',
    				),
    			),    			
    			'not_approach_modules' => array(),
    		);    		
    		$invoiceID = $invoice->createMultiInvoiceForNewCustomer($customerID, $periodStartDate, $billingID, $multiInvoiceData);
    		
    		//	run original method
    		$invoice->updateInvoice($invoiceID, 1934);
    		
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		//$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['module2customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_MODULE2CUSTOMER);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_INVOICE_ITEM, TB_VPS_CUSTOMER, TB_VPS_MODULE2CUSTOMER), __FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
    		//$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_MODULE2CUSTOMER), $outputTable['module2customer']);    		
    	}
    	
    	
    	//	TODO: add cutomer limit
    	public function testUpdateInvoiceV2() {
    		//$this->markTestIncomplete();
    		
    		//	first create invoice (smth like fixture)
    		$customerID = 2; 
			$periodStartDate = self::CURRENT_DATE;
			$billingID = 4;
			$asap = true;
			
			//	setup invoice object
    		$invoice = new Invoice($this->db);  
    		$invoice->currentDate = self::CURRENT_DATE;
    		
    		$invoiceData = $invoice->createInvoiceForBilling($customerID, $periodStartDate, $billingID, $asap);
    		
    		//	run original method
    		$invoice->updateInvoice(2, 382);    		    	

    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);
    		$outputTable['invoice_item'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE_ITEM);
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['schedule_customer_plan'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN);
    		$outputTable['schedule_limit'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_CUSTOMER, 
    													TB_VPS_INVOICE, 
    													TB_VPS_INVOICE_ITEM,
    													TB_VPS_SCHEDULE_CUSTOMER_PLAN,
    													TB_VPS_SCHEDULE_LIMIT), 
    												__FUNCTION__);
    												
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE_ITEM), $outputTable['invoice_item']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $outputTable['schedule_customer_plan']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $outputTable['schedule_limit']);
    	}
    	
    	
    	
    	public function testCancelInvoiceV1() {
    		//	setup objects
    		$invoice = new Invoice($this->db);
    		$payment = new Payment($this->db);    		
    		$invoice->currentDate = self::CURRENT_DATE;
    		$payment->currentDate = self::CURRENT_DATE.' 14:36:59';
    		$invoice->setPayment($payment);
    		
    		$invoice->cancelInvoice(1);
    		
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['payment'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_CUSTOMER, TB_VPS_PAYMENT), __FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_PAYMENT), $outputTable['payment']);
    	}
    	
    	
		
    	public function testCancelInvoiceV2() {    		    		
    		//$this->markTestSkipped();
    		
    		//	setup objects
    		$invoice = new Invoice($this->db);
    		$payment = new Payment($this->db);    		
    		$invoice->currentDate = self::CURRENT_DATE;
    		$payment->currentDate = self::CURRENT_DATE.' 14:36:59';
    		$invoice->setPayment($payment);
    		
    		//	create invoce wich we will cancel
    		$customerID = 2;
    		$periodStartDate = '2011-01-05';
    		$billingID = 5;
    		$multiInvoiceData = array(
    			'billingID' => 5,
    			'appliedModules' => array (
    				array(
    					'id' 			=> '20',
          				'month_count' 	=> '6',
          				'price' 		=> '800.00',
          				'module_id' 	=> '12',
          				'type' 			=> 'self',
          				'module_name'	=> 'carbon_footprint',
    				),
    			),    			
    			'not_approach_modules' => array(    				    				
    			),
    		);
    		
    		$invoiceID = $invoice->createMultiInvoiceForNewCustomer($customerID, $periodStartDate, $billingID, $multiInvoiceData);
    		
    		$invoice->cancelInvoice($invoiceID);
    		    		
    		$outputTable['invoice'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE);    		
    		$outputTable['customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER);
    		$outputTable['payment'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT);
    		$outputTable['module2customer'] = $this->getConnection()->createDataSet()->getTable(TB_VPS_MODULE2CUSTOMER);
    		
    		//	take expected output
    		$dataSet = $this->_loadExpectedTables(array(TB_VPS_INVOICE, TB_VPS_CUSTOMER, TB_VPS_PAYMENT, TB_VPS_MODULE2CUSTOMER), __FUNCTION__);
    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_INVOICE), $outputTable['invoice']);    		
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_CUSTOMER), $outputTable['customer']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_PAYMENT), $outputTable['payment']);
    		$this->assertTablesEqual($dataSet->getTable(TB_VPS_MODULE2CUSTOMER), $outputTable['module2customer']);
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
