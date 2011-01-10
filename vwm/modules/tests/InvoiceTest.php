<?php
	define ("USERS_TABLE", "vps_user");
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
	require_once 'modules/classes/Invoice.class.php';
	
	require_once 'modules/classes/Billing.class.php';
	require_once 'modules/classes/Bridge.class.php';
	require_once 'modules/classes/EMail.class.php';
	require_once 'modules/classes/Payment.class.php';
	
	class InvoiceTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;
		
		protected $db;
		protected $invoice;
		
		protected $DOM;
		protected $invoiceDates;
		
		protected $seedPath;
		protected $firstInvoice;
		protected $secondInvoice;  
	
		public function __construct() {
			//	Start xnyo Framework
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];
			$this->invoice = new Invoice($this->db);
			$this->seedPath = dirname(__FILE__).'/_files/seedInvoiceTest.xml';
			
			//	set dates for 1st invoice
			$this->invoiceDates[0] = array(
				'generation_date' 	=> date('Y-m-d',strtotime('-4 days')),
				'suspension_date' 	=> date('Y-m-d',strtotime('+10 days')),
				'period_start_date'	=> date('Y-m-d',strtotime('+10 days')),
				'period_end_date'	=> date('Y-m-d',strtotime('+6 months +10 days'))	//	billing period = 6 months
			);
			//	set dates for 2nd invoice
			$this->invoiceDates[1] = array(
				'generation_date' 	=> date('Y-m-d',strtotime('-20 days')),
				'suspension_date' 	=> date('Y-m-d',strtotime('-5 days')),
				'period_start_date'	=> date('Y-m-d',strtotime('-5 days')),
				'period_end_date'	=> date('Y-m-d',strtotime('+1 months -5 days'))	//	billing period = 6 months
			);
			
			$this->firstInvoice = array (
				'invoiceID'			=> 1,
    			'customerID'		=> 116,
			    'oneTimeCharge' 	=> '30.00',
    			'amount' 			=> '99.99',
    			'discount' 			=> '0.00',
    			'total' 			=> '129.99',
    			'paid' 				=> '129.99',
    			'due' 				=> '0.00',
    			'balance' 			=> '0.00',
    			'generationDate' 	=> $this->invoiceDates[0]['generation_date'],
    			'suspensionDate' 	=> $this->invoiceDates[0]['suspension_date'],
    			'periodStartDate' 	=> $this->invoiceDates[0]['period_start_date'],
    			'periodEndDate'		=> $this->invoiceDates[0]['period_end_date'],
    			'billingInfo' 		=> 'bla bla',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'PAID',
    			'suspensionDisable' => true,
    			'daysLeft2BPEnd' 	=> 191,
    			'daysCountAtBP' 	=> 181,
    			'customerDetails'	=> array (
    				'company_id' 		=> 116,
            		'name'				=> 'Tukalenko Inc Corp',
            		'address' 			=> '23974 Aliso Creek Road, Suite 280',
            		'city' 				=> 'Laguna Niguel',
            		'zip' 				=> '92677',
            		'state' 			=> 133,
            		'country' 			=> 215,
            		'county'		 	=> 1,
            		'phone'				=> '949 495-0999',
            		'fax'				=> '(714) 379-8894',
            		'email'				=> 'test_email@somewhere.com',
            		'contact'			=> 'Denis',
            		'title'				=> 'Software Sales',
            		'trial_end_date'	=> '2009-11-16',
            		'period_end_date' 	=> '2009-12-16',
            		'deadline_counter' 	=> 'NULL',
            		'status'			=> 'on'
    			)            
			);
			$this->secondInvoice = array (
				'invoiceID'			=> 2,
    			'customerID'		=> 105,
			    'oneTimeCharge' 	=> '7.00',
    			'amount' 			=> '9.99',
    			'discount' 			=> '0.00',
    			'total' 			=> '16.99',
    			'paid' 				=> '16.99',
    			'due' 				=> '0.00',
    			'balance' 			=> '0.00',
    			'generationDate' 	=> $this->invoiceDates[1]['generation_date'],
    			'suspensionDate' 	=> $this->invoiceDates[1]['suspension_date'],
    			'periodStartDate' 	=> $this->invoiceDates[1]['period_start_date'],
    			'periodEndDate'		=> $this->invoiceDates[1]['period_end_date'],
    			'billingInfo' 		=> 'bla bla',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'PAID',
    			'suspensionDisable' => true,
    			'daysLeft2BPEnd' 	=> 26,
    			'daysCountAtBP' 	=> 31,
    			'customerDetails'	=> array (
    				'company_id' 		=> 105,
            		'name'				=> 'Gyant Demo Version',
            		'address' 			=> '23974 Aliso Creek Road, Suite 280',
            		'city' 				=> 'Laguna Niguel',
            		'zip' 				=> '92677',
            		'state' 			=> 133,
            		'country' 			=> 215,
            		'county'		 	=> 1,
            		'phone'				=> '949 495-0999',
            		'fax'				=> '(714) 379-8894',
            		'email'				=> 'test_email@somewhere.com',
            		'contact'			=> 'Jon Gypsyn',
            		'title'				=> 'Software Sales',
            		'trial_end_date'	=> '2009-11-16',
            		'period_end_date' 	=> '2009-12-16',
            		'deadline_counter' 	=> 'NULL',
            		'status'			=> 'on'
    			)            
			);
			
			
			$xmlDataSetPath = $this->seedPath;
			$this->DOM = new DOMDocument();        	
      		$this->DOM->preserveWhiteSpace = false;
      		$this->DOM->formatOutput = true;            
			$this->DOM->load($xmlDataSetPath);			
			//	set invoices fields
			$elements = $this->DOM->getElementsByTagName('vps_invoice');
			foreach ($this->invoiceDates as $invoiceRow => $invoiceDates) {
				foreach ($invoiceDates as $key => $value) {
					$elements->item($invoiceRow)->setAttribute($key, $value);
				}	
			}
			$this->DOM->save($xmlDataSetPath);
				
        	$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
        	 
    	}
    	
    	protected function getConnection() {
        	return $this->createDefaultDBConnection($this->pdo, DB_NAME);       	
    	}

	    protected function getDataSet() {
	    	//	Copy bridge.xml master copy
        	copy(PATH_BRIDGE_XML_MASTER_COPY, PATH_BRIDGE_XML);
        	chmod(PATH_BRIDGE_XML, 0777);
        	
        	return $this->createFlatXMLDataSet($this->seedPath);
    	}
    	
    	
    	//			<<	testCreateInvoiceForBilling scheme	>>
    	//	testVar	|	trial		|	asap	|	
    	//	--------+---------------+-----------|
    	//		1	|	false		|	false	|
    	//		2	|	false		|	true	|
    	//		3	|	true		|	false	|
    	//		4	|	true		|	true	|
    		
    	public function testCreateInvoiceForBillingVar1() {
    		//	input data
    		$customerID = 105;
    		$periodStartDate = $this->invoiceDates[1]['period_end_date'];
    		$billingID = 3;
    		$asap = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createInvoiceForBillingVar1.xml';    		            
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
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']." +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->invoice->createInvoiceForBilling($customerID,$periodStartDate,$billingID, $asap);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeCreateInvoiceForBillingVar1.xml', PATH_BRIDGE_XML); 
    	}
    	
    	
    	public function testCreateInvoiceForBillingVar2() {
    		//	input data
    		$customerID = 105;
    		$periodStartDate = date('Y-m-d');
    		$billingID = 3;
    		$asap = true;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createInvoiceForBillingVar2.xml';    		            
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
								$values->item(11)->nodeValue = date('Y-m-d');								//	period_start_date equal generation date
								$values->item(12)->nodeValue = date('Y-m-d', strtotime(date('Y-m-d')." +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->invoice->createInvoiceForBilling($customerID,$periodStartDate,$billingID, $asap);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeCreateInvoiceForBillingVar1.xml', PATH_BRIDGE_XML); 
    	}
    	
    	
    	public function testCreateInvoiceForBillingVar3() {
    		//	input data
    		$customerID = 116;
    		$periodStartDate = $this->invoiceDates[0]['period_start_date'];
    		$billingID = 3;
    		$asap = false;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createInvoiceForBillingVar3.xml';    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_start_date'];	//	suspension date equal period_start_date of future invoice
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];	//	period_start_date equal period_start_date of future invoice
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_start_date']." +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}				
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->invoice->createInvoiceForBilling($customerID,$periodStartDate,$billingID, $asap);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeCreateInvoiceForBillingVar3.xml', PATH_BRIDGE_XML); 
    	}
    	
    	
    	public function testCreateInvoiceForBillingVar4() {
    		//	input data
    		$customerID = 116;
    		$periodStartDate = $this->invoiceDates[0]['period_start_date'];
    		$billingID = 3;
    		$asap = true;
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createInvoiceForBillingVar4.xml';    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[0]['period_start_date'];	//	suspension date equal period_start_date of future invoice
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];	//	period_start_date equal period_start_date of future invoice
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_start_date']." +6 months"));	//	period_finish_date = period_start_date +6 month
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}		
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->invoice->createInvoiceForBilling($customerID,$periodStartDate,$billingID, $asap);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
    		
    		//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeCreateInvoiceForBillingVar3.xml', PATH_BRIDGE_XML); 
    	}
    	
    	
    	
    	public function testCreateInvoiceForLimit() {
    		$customerID = 105;
    		$amount = '50.00';
    		$limitInfo = "'Increase memory + 7 Mb'";
    		
    		//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createInvoiceForLimitVar1.xml';    		            
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
								$values->item(10)->nodeValue = date('Y-m-d', strtotime("+30 days"));	//	suspension date equal generation date + 1 month
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->invoice->createInvoiceForLimit($customerID, $amount, $limitInfo);
    		
    		//	assertions
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
    	}


		public function testCreateCustomInvoice() {
			
			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '5000.00', discount = '70.00' WHERE customer_id = 105";
   			$this->db->query($query);
   			
   			$customerID = 105;
   			$amount = '50.00';
   			$suspensionDate = date('Y-m-d', strtotime("+15 days"));
   			$suspensionDisable = 0;
   			$customInfo = "'Den studenta'";
   			
   			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/createCustomInvoice.xml';    		            
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
								$values->item(10)->nodeValue = $suspensionDate;
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
   			
   			$this->invoice->createCustomInvoice($customerID, $amount, $suspensionDate, $suspensionDisable, $customInfo);
   			
   			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
		}
		
		
		//			<<	testUpdateInvoice scheme	>>
    	//	testVar	|	type		|	asap	|	disable	|	
    	//	--------+---------------+-----------+-----------|
    	//		1	|	billing		|	false	|	true	|
    	//		2	|	billing		|	true	|	true	|
    	//		3	|	limit		|	--		|	false	|
    	//		4	|	custom		|	--		|	true	|
    	
		public function testUpdateInvoiceVar1() {
			
			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-245.00' WHERE customer_id = 105";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '45.00', '200.00', '0.00', '245.00', '0.00', '245.00', '-245.00', '".date('Y-m-d')."', '".$this->invoiceDates[1]['period_end_date']."', '".$this->invoiceDates[1]['period_end_date']."', '".date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date'].'+6 months'))."', 'Sources: 1, Months: 6, type: gyant', null, null, 'due', 1)";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_PAYMENT." (payment_id, invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) VALUES ( " .
    			 "2, 3, 0, 'afivemuurowrm', '245.00', '0.00', '-245.00', '".date('Y-m-d H:i:s')."', 'Completed', 1)";
   			$this->db->query($query);
   			
			$invoiceID = 3;
			$paid = 245;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/updateInvoiceVar1.xml';    		    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date'].'+6 months'));
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			$this->invoice->updateInvoice($invoiceID, $paid);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
   			
   			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeUpdateInvoiceVar1.xml';
    		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date'].'+6 months'));    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
		}	
		
		public function testUpdateInvoiceVar2() {
			
			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-245.00' WHERE customer_id = 105";
   			$this->db->query($query);
   			
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '45.00', '200.00', '0.00', '245.00', '0.00', '245.00', '-245.00', '".date('Y-m-d')."', '".$this->invoiceDates[1]['period_end_date']."', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+6 months'))."', 'Sources: 1, Months: 6, type: gyant, ASAP', null, null, 'due', 1)";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_PAYMENT." (payment_id, invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) VALUES ( " .
    			 "2, 3, 0, 'afivemuurowrm', '245.00', '0.00', '-245.00', '".date('Y-m-d H:i:s')."', 'Completed', 1)";
   			$this->db->query($query);
   			
			$invoiceID = 3;
			$paid = 245;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/updateInvoiceVar2.xml';    		    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								$values->item(11)->nodeValue = date('Y-m-d');
								$values->item(12)->nodeValue = date('Y-m-d', strtotime('+6 months'));
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			$this->invoice->updateInvoice($invoiceID, $paid);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_CUSTOMER_PLAN));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_SCHEDULE_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_SCHEDULE_LIMIT));
   			
   			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeUpdateInvoiceVar2.xml';
    		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime('+6 months'));
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('limit')->item(0)->getElementsByTagName('max_value')->item(0)->nodeValue = '100';
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('limit')->item(1)->getElementsByTagName('max_value')->item(0)->nodeValue = '400';
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
		}	
		
		//	testVar	|	type		|	asap	|	disable	|	
    	//	--------+---------------+-----------+-----------|
    	//		3	|	limit		|	--		|	false	|
		public function testUpdateInvoiceVar3() {
			
			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-100.00' WHERE customer_id = 105";
   			$this->db->query($query);
   			
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, 'Increase MSDS + 10 ', null, 'due', 0)";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_PAYMENT." (payment_id, invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) VALUES ( " .
    			 "2, 3, 0, 'afivemuurowrm', '100.00', '0.00', '-100.00', '".date('Y-m-d H:i:s')."', 'Completed', 1)";
   			$this->db->query($query);
   			
			$invoiceID = 3;
			$paid = 100;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/updateInvoiceVar3.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+30 days'));								
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			$this->invoice->updateInvoice($invoiceID, $paid);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER_LIMIT), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER_LIMIT));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
   			
   			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeUpdateInvoiceVar3.xml';
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
		}	
		
		
		//			<<	testUpdateInvoice scheme	>>
    	//	testVar	|	type		|	asap	|	disable	|	
    	//	--------+---------------+-----------+-----------|
    	//		4	|	custom		|	--		|	true	|
    	
    	public function testUpdateInvoiceVar4() {
			
			//	testing sucks
   			//$this->db->select_db(DB_NAME);
   			$query = "UPDATE ".TB_VPS_CUSTOMER." SET balance = '-100.00' WHERE customer_id = 105";
   			$this->db->query($query);
   			
   			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan', 'due', 1)";
   			$this->db->query($query);
   			$query = "INSERT INTO ".TB_VPS_PAYMENT." (payment_id, invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) VALUES ( " .
    			 "2, 3, 0, 'afivemuurowrm', '100.00', '0.00', '-100.00', '".date('Y-m-d H:i:s')."', 'Completed', 1)";
   			$this->db->query($query);
   			
			$invoiceID = 3;
			$paid = 100;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/updateInvoiceVar4.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+30 days'));								
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			$this->invoice->updateInvoice($invoiceID, $paid);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));

		}
		
		
		public function testGetInvoiceDetails() {
			$invoiceID[0] = 1;
			$expectedResult[0] = $this->firstInvoice;
			
			$invoiceID[1] = 666;
			$expectedResult[1] = false;  
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getInvoiceDetails($invoiceID[$key]));	
    		}
		}
		
		
		public function testGetCurrentInvoice() {
			$customerID[0] = 105;
			$doNotSelectCanceled[0] = true;
			$expectedResult[0] = $this->secondInvoice;
			
			$customerID[1] = 116;
			$doNotSelectCanceled[1] = true;
			$expectedResult[1] = false;
			
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 116, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', '".date('Y-m-d', strtotime('-5 days'))."', '".date('Y-m-d', strtotime('+25 days'))."', 'Billing Limit', null, null, 'canceled', 1)";
   			$this->db->query($query);
   			$customerID[2] = 116;
   			$doNotSelectCanceled[2] = false;
			$expectedResult[2] = array (
				'invoiceID'			=> 3,
    			'customerID'		=> 116,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'balance' 			=> '-100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> date('Y-m-d', strtotime('-5 days')),
    			'periodEndDate'		=> date('Y-m-d', strtotime('+25 days')),
    			'billingInfo' 		=> 'Billing Limit',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'CANCELED',
    			'suspensionDisable' => true,
    			'daysLeft2BPEnd' 	=> 25,
    			'daysCountAtBP' 	=> 30,
    			'customerDetails'	=> array (
    				'company_id' 		=> 116,
            		'name'				=> 'Tukalenko Inc Corp',
            		'address' 			=> '23974 Aliso Creek Road, Suite 280',
            		'city' 				=> 'Laguna Niguel',
            		'zip' 				=> '92677',
            		'state' 			=> 133,
            		'country' 			=> 215,
            		'county'		 	=> 1,
            		'phone'				=> '949 495-0999',
            		'fax'				=> '(714) 379-8894',
            		'email'				=> 'test_email@somewhere.com',
            		'contact'			=> 'Denis',
            		'title'				=> 'Software Sales',
            		'trial_end_date'	=> '2009-11-16',
            		'period_end_date' 	=> '2009-12-16',
            		'deadline_counter' 	=> 'NULL',
            		'status'			=> 'on'
    			)            
			);
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getCurrentInvoice($customerID[$key], 'today', $doNotSelectCanceled[$key]));	
    		}  
		}
		
		
		public function testGetLastInvoice() {
			$customerID[0] = 105;
			$doNotSelectCanceled[0] = true;
			$expectedResult[0] = $this->secondInvoice;
			
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 116, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', '".date('Y-m-d', strtotime('-5 days'))."', '".date('Y-m-d', strtotime('+25 days'))."', 'Billing Limit', null, null, 'canceled', 1)";
   			$this->db->query($query);
   			$customerID[1] = 116;
   			$doNotSelectCanceled[1] = false;
			$expectedResult[1] = array (
				'invoiceID'			=> 3,
    			'customerID'		=> 116,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'balance' 			=> '-100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> date('Y-m-d', strtotime('-5 days')),
    			'periodEndDate'		=> date('Y-m-d', strtotime('+25 days')),
    			'billingInfo' 		=> 'Billing Limit',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'CANCELED',
    			'suspensionDisable' => true,
    			'daysLeft2BPEnd' 	=> 25,
    			'daysCountAtBP' 	=> 30,
    			'customerDetails'	=> array (
    				'company_id' 		=> 116,
            		'name'				=> 'Tukalenko Inc Corp',
            		'address' 			=> '23974 Aliso Creek Road, Suite 280',
            		'city' 				=> 'Laguna Niguel',
            		'zip' 				=> '92677',
            		'state' 			=> 133,
            		'country' 			=> 215,
            		'county'		 	=> 1,
            		'phone'				=> '949 495-0999',
            		'fax'				=> '(714) 379-8894',
            		'email'				=> 'test_email@somewhere.com',
            		'contact'			=> 'Denis',
            		'title'				=> 'Software Sales',
            		'trial_end_date'	=> '2009-11-16',
            		'period_end_date' 	=> '2009-12-16',
            		'deadline_counter' 	=> 'NULL',
            		'status'			=> 'on'
    			)            
			);
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getLastInvoice($customerID[$key], $doNotSelectCanceled[$key]));	
    		} 
		}
		
		
		public function testGetAllInvoicesList() {
			$customerID = 105;
			$expectedResult = array($this->secondInvoice);
			$this->assertEquals($expectedResult, $this->invoice->getAllInvoicesList($customerID));	
		}
		
		public function testGetPaidInvoicesList() {
			$customerID = 105;
			$expectedResult = array($this->secondInvoice);
			$this->assertEquals($expectedResult, $this->invoice->getPaidInvoicesList($customerID));	
		}
		
		public function testGetDueInvoicesList() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 116, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', '".date('Y-m-d', strtotime('-5 days'))."', '".date('Y-m-d', strtotime('+25 days'))."', 'Billing Limit', null, null, 'due', 1)";
   			$this->db->query($query);
			$customerID = 116;
			$expectedResult[0] = array (
				'invoiceID'			=> 3,
    			'customerID'		=> 116,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> date('Y-m-d', strtotime('-5 days')),
    			'periodEndDate'		=> date('Y-m-d', strtotime('+25 days')),
    			'billingInfo' 		=> 'Billing Limit',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'due',
    			'daysLeft'		 	=> 30,
    			'paypal'			=> array (
    				'merchantEmail'		=> 'detook_1248695878_biz@gmail.com',
            		'itemName'	 		=> 'unitTestSecond',
                    'itemNumber' 		=> 3,
                    'amount' 			=> '100.00',
                    'noShipping'		=> 1,
                    'noNote'			=> 0,
                    'returnURL'			=> 'http://vocwebmanager.com/test_ggbdfr/VOC15/vps.php?action=viewDetails&category=invoices&invoiceID=3&successPayment=1',
                    'cancelURL'		 	=> 'http://vocwebmanager.com/test_ggbdfr/VOC15/vps.php?action=viewDetails&category=invoices&invoiceID=3&successPayment=0',
                    'notifyURL'		 	=> 'http://vocwebmanager.com/test_ggbdfr/VOC15/payments/ipn.php'
    			)            
			);
			$this->assertEquals($expectedResult, $this->invoice->getDueInvoicesList($customerID));
			
			//	testing sucks
			$query = "INSERT INTO ".TB_VPS_PAYMENT." (payment_id, invoice_id, user_id, txn_id, paid, due, balance, payment_date, status) VALUES ( " .
    			 "2, 3, 0, 'afivemuurowrm', '100.00', '0.00', '-100.00', '".date('Y-m-d H:i:s')."', 'Pending')";
   			$this->db->query($query);
   			
   			unset($expectedResult[0]['paypal']);
   			$this->assertEquals($expectedResult, $this->invoice->getDueInvoicesList($customerID));
		}
		
		
		public function testGetCanceledInvoicesList() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 116, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', '".date('Y-m-d', strtotime('-5 days'))."', '".date('Y-m-d', strtotime('+25 days'))."', 'Billing Limit', null, null, 'canceled', 1)";
   			$this->db->query($query);
   			
   			$customerID = 116;
   			$expectedResult[0] = array (
				'invoiceID'			=> 3,
    			'customerID'		=> 116,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'balance' 			=> '-100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> date('Y-m-d', strtotime('-5 days')),
    			'periodEndDate'		=> date('Y-m-d', strtotime('+25 days')),
    			'billingInfo' 		=> 'Billing Limit',
    			'limitInfo'			=> '', 
    			'customInfo'		=> '', 
    			'status'			=> 'CANCELED',
    			'suspensionDisable' => true,
    			'daysLeft2BPEnd' 	=> 25,
    			'daysCountAtBP' 	=> 30,
    			'customerDetails'	=> array (
    				'company_id' 		=> 116,
            		'name'				=> 'Tukalenko Inc Corp',
            		'address' 			=> '23974 Aliso Creek Road, Suite 280',
            		'city' 				=> 'Laguna Niguel',
            		'zip' 				=> '92677',
            		'state' 			=> 133,
            		'country' 			=> 215,
            		'county'		 	=> 1,
            		'phone'				=> '949 495-0999',
            		'fax'				=> '(714) 379-8894',
            		'email'				=> 'test_email@somewhere.com',
            		'contact'			=> 'Denis',
            		'title'				=> 'Software Sales',
            		'trial_end_date'	=> '2009-11-16',
            		'period_end_date' 	=> '2009-12-16',
            		'deadline_counter' 	=> 'NULL',
            		'status'			=> 'on'
    			)            
			);
			
			$this->assertEquals($expectedResult, $this->invoice->getCanceledInvoicesList($customerID));
			
		}
		
		public function	testGetDiscount() {
			$customerID[0] = 105;
			$expectedResult[0] = '0.00';
			
			$customerID[1] = 666;
			$expectedResult[1] = false;
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getDiscount($customerID[$key]));	
    		} 
		} 
		
		public function testGetBalance() {
			$customerID[0] = 105;
			$expectedResult[0] = '0.00';
			
			$customerID[1] = 666;
			$expectedResult[1] = false;
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getBalance($customerID[$key]));	
    		}
		}
		
		public function testGetInvoiceWhenTrialPeriod() {
			$customerID[0] = 105;
			$expectedResult[0] = $this->secondInvoice;
			
			$customerID[1] = 666;
			$expectedResult[1] = false;
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getInvoiceWhenTrialPeriod($customerID[$key]));	
    		}
		}
		
		public function testGetInvoiceForFuturePeriod() {
			$customerID[0] = 116;
			$expectedResult[0] = array('invoiceID'=>1, 'total'=>'129.99', 'status'=>'paid');
			
			$customerID[1] = 666;
			$expectedResult[1] = false;
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getInvoiceForFuturePeriod($customerID[$key]));	
    		}
		}
		
		//			<<	testManualBalanceChange scheme	>>
    	//	testVar	|	operation	|	
    	//	--------+---------------|
    	//		1	|		+		|
    	//		2	|		-		|
		public function testManualBalanceChangeVar1() {
			$customerID = 105;
			$operation = "+";
			$balance = 100;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/manualBalanceChangeVar1.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d');								
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->invoice->manualBalanceChange($customerID,$operation, $balance);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
		}
		
		public function testManualBalanceChangeVar2() {
			$customerID = 105;
			$operation = "-";
			$balance = 100;
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/manualBalanceChangeVar2.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d');								
								break;
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->invoice->manualBalanceChange($customerID,$operation, $balance);
			
			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));	
		}
		
		
		//			<<	testRestoreInvoice scheme	>>
    	//	testVar	|	invoice type	|	shiftStartDate	|	disable
    	//	--------+-------------------+-------------------+------------
    	//		1	|		billig		|		false		|	true
    	//		2	|		billing		|		true		|	true
    	//		3	|		custom		|		false		|	false
		public function testRestoreInvoiceVar1() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "UPDATE ".TB_VPS_INVOICE." SET status = 'canceled' WHERE invoice_id = 2";
   			$this->db->query($query);
   			
			$invoiceID = 2;
			$shift = 5;
			$shiftStartDate = false;
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/restoreInvoiceVar1.xml';    		    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[1]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[1]['period_start_date'];
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']. '+ '.$shift.' days'));								
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			
			$this->invoice->restoreInvoice($invoiceID, $shift, $shiftStartDate);

			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			
   			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeRestoreInvoiceVar1.xml';
 	  		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']. '+ '.$shift.' days'));    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
		}
		
		//			<<	testRestoreInvoice scheme	>>
    	//	testVar	|	invoice type	|	shiftStartDate	|	disable
    	//	--------+-------------------+-------------------+------------
    	//		2	|		billing		|		true		|	true
		public function testRestoreInvoiceVar2() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "UPDATE ".TB_VPS_INVOICE." SET status = 'canceled' WHERE invoice_id = 2";
   			$this->db->query($query);
   			
			$invoiceID = 2;
			$shift = 5;
			$shiftStartDate = true;
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/restoreInvoiceVar2.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['suspension_date']. '+ '.$shift.' days')); 
								$values->item(11)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_start_date']. '+ '.$shift.' days'));
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']. '+ '.$shift.' days'));								
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			
			$this->invoice->restoreInvoice($invoiceID, $shift, $shiftStartDate);

			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			
   			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeRestoreInvoiceVar1.xml';
 	  		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']. '+ '.$shift.' days'));    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);		
		}
		
		//			<<	testRestoreInvoice scheme	>>
    	//	testVar	|	invoice type	|	shiftStartDate	|	disable
    	//	--------+-------------------+-------------------+------------
    	//		3	|		custom		|		false		|	false
		public function testRestoreInvoiceVar3() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES ( " .
    			 "3, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan', 'canceled', 0)";
   			$this->db->query($query);
   			
			$invoiceID = 3;
			$shift = 5;
			$shiftStartDate = false;
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/restoreInvoiceVar3.xml';    		    		            
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
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+ 30 days')); 								
								break;
							case '4':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+ 35 days')); 								
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
			
			
			$this->invoice->restoreInvoice($invoiceID, $shift, $shiftStartDate);

			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));	
		}
		
		//	due
		public function testChangeInvoiceStatusVar1() {
			
			//	test one
			$invoiceID = 1;
			$newStatus = "due";
			$this->invoice->changeInvoiceStatus($invoiceID, $newStatus);
			
			//	test two
			$invoiceID = 2;
			$newStatus = "due";
			$newDueAmount = 20;
			$this->invoice->changeInvoiceStatus($invoiceID, $newStatus, null, $newDueAmount);
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/changeInvoiceStatusVar1.xml';    		    		            
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
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
							case '3':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			//	assert bridge
			$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeChangeInvoiceStatusVar1.xml', PATH_BRIDGE_XML);
		}
		
		
		//	paid && canceled
		public function testChangeInvoiceStatusVar2() {
			
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "UPDATE ".TB_VPS_INVOICE." SET paid = '0.00', due = '129.99', status = 'due' WHERE invoice_id = 1";
   			$this->db->query($query);
   			
			//	test one
			$invoiceID = 1;
			$newStatus = "paid";
			$this->invoice->changeInvoiceStatus($invoiceID, $newStatus, 2);
			
			//	test two
			$invoiceID = 2;
			$newStatus = "canceled";
			$this->invoice->changeInvoiceStatus($invoiceID, $newStatus);
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/changeInvoiceStatusVar2.xml';    		    		            
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
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
							case '3':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			
			//	assert bridge
   			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeChangeInvoiceStatusVar2.xml';
 	  		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(5)->getElementsByTagName('period_end_date')->item(0)->nodeValue = $this->invoiceDates[0]['period_end_date'];    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);	
		}
		
		
		public function testCancelInvoice() {
			$invoiceID = 1;
			$this->invoice->cancelInvoice($invoiceID);
			
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/cancelInvoice.xml';    		    		            
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
						}							
					}										
				}
				//	set payment fields
				if ($tableName == 'vps_payment') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
		}
		
		
		public function testGetCustomDueInvoices() {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES " .
    			 "(3, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 1', 'due', 0) , " .
    			 "(4, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 2', 'due', 0) , " .
    			 "(5, 105, '0.00', '100.00', '0.00', '100.00', '100.00', '0.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 3', 'paid', 0)";
   			$this->db->query($query);
   			
   			$customerInfo = array (
	   			'company_id' 		=> 105,
				'name'				=> 'Gyant Demo Version',
				'address' 			=> '23974 Aliso Creek Road, Suite 280',
				'city' 				=> 'Laguna Niguel',
				'zip' 				=> '92677',
				'state' 			=> 133,
				'country' 			=> 215,
				'county'		 	=> 1,
				'phone'				=> '949 495-0999',
				'fax'				=> '(714) 379-8894',
				'email'				=> 'test_email@somewhere.com',
				'contact'			=> 'Jon Gypsyn',
				'title'				=> 'Software Sales',
				'trial_end_date'	=> '2009-11-16',
				'period_end_date' 	=> '2009-12-16',
				'deadline_counter' 	=> 'NULL',
				'status'			=> 'on'
   			);            
   			$customerID[0] = 105;
   			$expectedResult[0][0] = array (
				'invoiceID'			=> 3,
    			'customerID'		=> 105,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'balance' 			=> '-100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> '',
    			'periodEndDate'		=> '',
    			'billingInfo' 		=> '',
    			'limitInfo'			=> '', 
    			'customInfo'		=> 'I L Kazahstan 1', 
    			'status'			=> 'DUE',
    			'suspensionDisable' => false,
    			'daysLeft2BPEnd' 	=> '',
    			'daysCountAtBP' 	=> '',
    			'customerDetails'	=> $customerInfo
   			);
   			$expectedResult[0][1] = array (
				'invoiceID'			=> 4,
    			'customerID'		=> 105,
			    'oneTimeCharge' 	=> '0.00',
    			'amount' 			=> '100.00',
    			'discount' 			=> '0.00',
    			'total' 			=> '100.00',
    			'paid' 				=> '0.00',
    			'due' 				=> '100.00',
    			'balance' 			=> '-100.00',
    			'generationDate' 	=> date('Y-m-d'),
    			'suspensionDate' 	=> date('Y-m-d', strtotime('+30 days')),
    			'periodStartDate' 	=> '',
    			'periodEndDate'		=> '',
    			'billingInfo' 		=> '',
    			'limitInfo'			=> '', 
    			'customInfo'		=> 'I L Kazahstan 2', 
    			'status'			=> 'DUE',
    			'suspensionDisable' => false,
    			'daysLeft2BPEnd' 	=> '',
    			'daysCountAtBP' 	=> '',
    			'customerDetails'	=> $customerInfo
   			);
   			
   			$customerID[1] = 666;
   			$expectedResult[1] = false;
   			
   			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->invoice->getCustomDueInvoices($customerID[$key]));	
    		} 
		}
		
		public function testRestoreDueCustomInvoices () {
			//	testing sucks
			//$this->db->select_db(DB_NAME);
			$query = "INSERT INTO ".TB_VPS_INVOICE." (invoice_id, customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable) VALUES " .
    			 "(3, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 1', 'due', 0) , " .
    			 "(4, 105, '0.00', '100.00', '0.00', '100.00', '0.00', '100.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 2', 'canceled', 0) , " .
    			 "(5, 105, '0.00', '100.00', '0.00', '100.00', '100.00', '0.00', '-100.00', '".date('Y-m-d')."', '".date('Y-m-d', strtotime('+30 days'))."', null, null, null, null, 'I L Kazahstan 3', 'canceled', 0)";
   			$this->db->query($query);
   			
   			$customerID = 105;
   			$shift = 5;
   			
   			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/restoreDueCustomInvoices.xml';    		    		            
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
							case '4':
							case '5':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+30 days')); 
								break;
							case '6':
								$values->item(9)->nodeValue = date('Y-m-d');
								$values->item(10)->nodeValue = date('Y-m-d', strtotime('+35 days')); 
								break;
						}							
					}										
				}
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
   			$this->invoice->restoreDueCustomInvoices($customerID, $shift);
   			
   			//	assertions
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));   			
   			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
		}
		
		public function testCalculateTotal() {
			$this->assertEquals(13, $this->invoice->calculateTotal(6, 10, 3));
			$this->assertEquals(10, $this->invoice->calculateTotal(0, 10));
		}
	}
?>
