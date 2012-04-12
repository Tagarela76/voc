<?php
	define ("USERS_TABLE", "vps_user");
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
	require_once 'modules/classes/Payment.class.php';
	
	require_once 'modules/classes/Invoice.class.php';
	require_once 'modules/classes/Billing.class.php';
	require_once 'modules/classes/Bridge.class.php';
														
	class PaymentTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;
		
		protected $db;
		protected $payment;
		
		protected $invoiceDates;
		protected $seedPath;
	
		public function __construct() {
			//	Start xnyo Framework
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];			
			$this->payment = new Payment($this->db);
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
    	
    	
    	public function testGetHistory() {
    		$expectedHistory = array(
    			array (
    				'date' 			=> $this->invoiceDates[0]['generation_date'],
            		'description' 	=> 'Generated Invoice',
            		'invoiceID' 	=> '1',
            		'due' 			=> '129.99',
            		'paid' 			=> '0.00',
            		'balance'		=> '0.00',
            		'status'		=> '--'
    			),
    			array (
    				'date' 			=> '2009-07-30 15:15:15',
            		'description' 	=> 'eprst123456789098765',
            		'invoiceID' 	=> '1',
            		'due' 			=> '0.00',
            		'paid' 			=> '129.99',
            		'balance' 		=> '0.00',
            		'status' 		=> 'Completed'
				)            
        	);
        		    	
    		$history = $this->payment->getHistory(1);
    		
    		//	assertions
    		$this->assertEquals($expectedHistory,$history);
    	}   
    	
    	
    	public function testCreatePayment() {    		    		    		
    		$invoiceID = 1;
    		$userID = 1;
    		$txn = "1t2r3x4nooooGagarin";
    		$status = "Pending";
    		    		
    		$paid = $this->payment->createPayment($invoiceID, $userID, $txn, $status);    		    		    		    		    		
    		    		
    		$expectedResult = array (
				'payment_id'		=> 2,            	
            	'invoice_id'		=> $invoiceID,            
            	'user_id' 			=> $userID,            
            	'txn_id' 			=> $txn,            	
            	'paid' 				=> '129.99',            	
            	'due' 				=> '0.00',
            	'balance'			=> '0.00',            
            	'payment_date' 		=> date('Y-m-d H:i:s'),
            	'status'			=> $status,
            	'payment_method_id'	=> null           	
    		);
    		    				     
    		$query = "SELECT * FROM ".TB_VPS_PAYMENT;			
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();   		
    		
    		$this->assertEquals($expectedResult['payment_id'],$result[1]['payment_id']);
			$this->assertEquals($expectedResult['invoice_id'],$result[1]['invoice_id']);
			$this->assertEquals($expectedResult['user_id'],$result[1]['user_id']);
			$this->assertEquals($expectedResult['txn_id'],$result[1]['txn_id']);
			$this->assertEquals($expectedResult['paid'],$result[1]['paid']);
			$this->assertEquals($expectedResult['due'],$result[1]['due']);
			$this->assertEquals($expectedResult['balance'],$result[1]['balance']);
			$this->assertEquals($expectedResult['payment_date'],$result[1]['payment_date']);    		
			$this->assertEquals($expectedResult['status'],$result[1]['status']);
			$this->assertEquals($expectedResult['payment_method_id'],$result[1]['payment_method_id']);
    	}
    	
    	
    	public function testCancelInvoicePayment() {
    		$invoiceID = 1;
    		$userID = 1;
    		    		
    		$this->payment->cancelInvoicePayment($invoiceID, $userID);    		    		    		    		    		
    		    		
    		$expectedResult = array (
				'payment_id'	=> 2,            	
            	'invoice_id'	=> $invoiceID,            
            	'user_id' 		=> $userID,            
            	'txn_id' 		=> 'Canceled',            	
            	'paid' 			=> '129.99',            	
            	'due' 			=> '0.00',
            	'balance'		=> '0.00',            
            	'payment_date' 	=> date('Y-m-d H:i:s'),
            	'status'		=> 'Completed'           	
    		);
    		    				     
    		$query = "SELECT * FROM ".TB_VPS_PAYMENT;			
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();   		
    		
    		$this->assertEquals($expectedResult['payment_id'],$result[1]['payment_id']);
			$this->assertEquals($expectedResult['invoice_id'],$result[1]['invoice_id']);
			$this->assertEquals($expectedResult['user_id'],$result[1]['user_id']);
			$this->assertEquals($expectedResult['txn_id'],$result[1]['txn_id']);
			$this->assertEquals($expectedResult['paid'],$result[1]['paid']);
			$this->assertEquals($expectedResult['due'],$result[1]['due']);
			$this->assertEquals($expectedResult['balance'],$result[1]['balance']);
			$this->assertEquals($expectedResult['payment_date'],$result[1]['payment_date']);    		
			$this->assertEquals($expectedResult['status'],$result[1]['status']);
    		
    	}
    	
    	
    	public function testClonePayments() {
    		$fromInvoiceID = 1;
    		$toInvoiceID = 2;
    		$xmlDataSetPath = dirname(__FILE__).'/_files/clonePayments.xml';
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
    		$this->payment->clonePayments($fromInvoiceID, $toInvoiceID);	
    		
    		$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
    	}
    	    	    								
	}			
?>
