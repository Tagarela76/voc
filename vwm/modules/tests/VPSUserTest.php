<?php
	define ("USERS_TABLE", "vps_user");
	require('config/constants4unitTest.php');	
	
	require_once 'PHPUnit/Extensions/Database/TestCase.php';
	require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
	require_once 'modules/classes/VPSUser.class.php';
	require_once 'modules/classes/Bridge.class.php';
	require_once 'modules/classes/Invoice.class.php';
	require_once 'modules/classes/Payment.class.php';
														
	class VPSUserTest extends PHPUnit_Extensions_Database_TestCase {
		protected $pdo;
		
		protected $db;	
		protected $user;
		protected $seedPath;
		
		protected $invoiceDates;
		protected $DOM;
	
		public function __construct() {
			//	Start xnyo Framework
			require ('modules/xnyo/startXnyo.php');		
			
			$this->db = $GLOBALS["db"];			
			$xnyo->load_plugin('auth');			
			$this->auth = $GLOBALS['auth'];
			$this->xnyo = $xnyo;
			$this->user = new VPSUser($this->db, $this->auth, $this->access, $this->xnyo);
			$this->seedPath = dirname(__FILE__).'/_files/seedVPSUserTest.xml';
			
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
    	
    	    					
		public function testAuthorize() {
			$query = "SELECT * FROM ".TB_VPS_USER;
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $vpsUsers = $statement->fetchAll();		

			//correct authorization
			$this->assertTrue($this->user->authorize($vpsUsers[0]['accessname'],'password'));

			//invalid password			
			$this->assertFalse($this->user->authorize($vpsUsers[0]['accessname'],'qwerty?'));
			//invalid login
			$this->assertFalse($this->user->authorize('user_noT_ExisT',$vpsUsers[0]['password']));
			//invalid login & password
			$this->assertFalse($this->user->authorize('user_noT_ExisT','qwerty?'));
			
			//user from VOC but not registered yet
			$userFromVocResult = array(
				'showAddUser' 		=> true,
    			'accessname' 		=> 'gennadiy',
    			'password' 			=> '5f4dcc3b5aa765d61d8327deb882cf99',	//	returned md5 password
    			'accesslevel_id'	=> '0',
    			'firstName' 		=> 'gennadiy',
    			'lastName'	 		=> null, 
    			'secondary_contact' => '',	 
    			'email' 			=> '',
    			'secondary_email' 	=> '', 
    			'company_id' 		=> '105',
    			'facility_id' 		=> '0',
    			'department_id' 	=> '0',
    			'address1' 			=> '23974 Aliso Creek Road, Suite 280',
    			'address2' 			=> '', 
    			'city' 				=> 'Laguna Niguel',
    			'state_id' 			=> '133',
    			'zip' 				=> '92677',
    			'country_id' 		=> '215',
    			'phone' 			=> '',
    			'fax' 				=> '(714) 379-8894'				
			);			
			$this->assertEquals($userFromVocResult,$this->user->authorize('gennadiy','password'));
		}
		
				
		public function testAddUser() {						
			$userDetails = array(
				'showAddUser'		=> true,
				'accessname'		=> 'ilya',
				'password' 			=> 'ilya',
				'accesslevel_id' 	=> '0',
				'firstName'			=> 'ilya',
				'lastName' 			=> '', 
				'secondary_contact' => '',	 
				'email' 			=> 'unitTestUser@qw.qw',
				'secondary_email' 	=> '', 
				'company_id' 		=> '113',
				'facility_id' 		=> '0',
				'department_id' 	=> '0',
				'address1' 			=> 'turkey',
				'address2' 			=> '', 
				'city' 				=> 'turkeystan',
				'state_id'	 		=> '15',
				'zip' 				=> '12312',
				'country_id' 		=> '205',
				'phone' 			=> 'aa',
				'fax' 				=> 'adasd'				
			);
			
			$this->user->addUser($userDetails, false);
					
			$xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/vps_user-after-insert-seed.xml');
			$this->assertTablesEqual($xml_dataset->getTable(TB_VPS_USER), $this->getConnection()->createDataSet()->getTable(TB_VPS_USER));
		}
		
				
		public function testSetUserDetails() {			
			//testing $fullUpdate=false
			$userDetails = array(
				'user_id'			=> 1,
				'firstName' 		=> 'innokentiy',
				'lastName' 			=> 'UpdatingLastName', 
				'secondary_contact' => '',	 
				'email' 			=> 'unitTestUser@qw.qw',
				'secondary_email'	=> '', 
				'company_id' 		=> '115',
				'facility_id' 		=> '0',
				'department_id' 	=> '0',
				'address1' 			=> 'turkey',
				'address2' 			=> '', 
				'city' 				=> 'turkeystan',
				'state_id' 			=> '15',
				'zip' 				=> '12312',
				'country_id' 		=> '205',
				'phone' 			=> 'aa',
				'fax' 				=> 'adasd'				
			);
			
			$this->user->setUserDetails($userDetails);
			
			$xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/vps_user-after-update-seed.xml');
			$this->assertTablesEqual($xml_dataset->getTable(TB_VPS_USER), $this->getConnection()->createDataSet()->getTable(TB_VPS_USER));
						
			//testing $fullUpdate=true
			$userDetails = array(
				'user_id'			=> 1,
				'accessname'		=> 'innokentiy',
				'password'			=> 'UpdatingPassword',
				'accesslevel_id'	=> 0,
				'firstName' 		=> 'innokentiy',
				'lastName' 			=> 'UpdatingLastName', 
				'secondary_contact' => '',	 
				'email' 			=> 'unitTestUser@qw.qw',
				'secondary_email'	=> '', 
				'company_id' 		=> '115',
				'facility_id' 		=> '0',
				'department_id' 	=> '0',
				'address1' 			=> 'turkey',
				'address2' 			=> '', 
				'city' 				=> 'turkeystan',
				'state_id' 			=> '15',
				'zip' 				=> '12312',
				'country_id' 		=> '205',
				'phone' 			=> 'aa',
				'fax' 				=> 'adasd'				
			);
			
			$this->user->setUserDetails($userDetails, true);
			
			$xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/vps_user-after-full-update-seed.xml');
			$this->assertTablesEqual($xml_dataset->getTable(TB_VPS_USER), $this->getConnection()->createDataSet()->getTable(TB_VPS_USER));
		}									        


		public function testGetUserIDbyAccessname() {
			$query = "SELECT * FROM ".TB_VPS_USER;
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();
		    												
			$userID = $this->user->getUserIDbyAccessname($result[0]['accessname']);
			$this->assertEquals($result[0]['user_id'],$userID);
		}
		
		
		public function testGetUserAccessLevel() {
			$query = "SELECT * FROM ".TB_VPS_USER;
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();
		    					
			$userAccessLevel = $this->user->getUserAccessLevel($result[0]['user_id']);
			$this->assertEquals("CompanyLevel",$userAccessLevel);		
		}
		
		
		public function testGetCustomerIDbyUserID() {
			$query = "SELECT * FROM ".TB_VPS_USER;
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();
		    						
			$customerID = $this->user->getCustomerIDbyUserID($result[0]['user_id']);
			$this->assertEquals($result[0]['company_id'],$customerID);		
		}
		
		
		public function testGetUserDetails() {
			$query = "SELECT * FROM ".TB_VPS_USER;			
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();
		   			   			
			$userDetails = $this->user->getUserDetails($result[0]['user_id']);
						
			$this->assertEquals($result[0]['accessname'],$userDetails['accessname']);
			$this->assertEquals($result[0]['password'],$userDetails['password']);
			$this->assertEquals($result[0]['first_name'],$userDetails['firstName']);
			$this->assertEquals($result[0]['last_name'],$userDetails['lastName']);
			$this->assertEquals($result[0]['secondary_contact'],$userDetails['secondaryContact']);
			$this->assertEquals($result[0]['email'],$userDetails['email']);
			$this->assertEquals($result[0]['secondary_email'],$userDetails['secondaryEmail']);
			$this->assertEquals($result[0]['company_id'],$userDetails['company_id']);
			$this->assertEquals($result[0]['facility_id'],$userDetails['facility_id']);
			$this->assertEquals($result[0]['department_id'],$userDetails['department_id']);
			$this->assertEquals($result[0]['address1'],$userDetails['address1']);
			$this->assertEquals($result[0]['address2'],$userDetails['address2']);
			$this->assertEquals($result[0]['city'],$userDetails['city']);
			$this->assertEquals($result[0]['state_id'],$userDetails['state_id']);
			$this->assertEquals($result[0]['zip'],$userDetails['zip']);
			$this->assertEquals($result[0]['country_id'],$userDetails['country_id']);
			$this->assertEquals($result[0]['phone'],$userDetails['phone']);
			$this->assertEquals($result[0]['fax'],$userDetails['fax']);										
		}
		
		
		public function testIfCustomerExist() {
			$query = "SELECT * FROM ".TB_VPS_CUSTOMER;			
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();		    			
	
			//customer exist			
			$exist = $this->user->ifCustomerExist($result[0]['customer_id']);
			$this->assertTrue($exist);
			
			//no such customer
			$exist = $this->user->ifCustomerExist(count($result)+1);
			$this->assertFalse($exist);
		}
		
		
		public function testChangeCustomerStatus() {
			$customerID = 105;
			$newStatus = "off";
		
			$this->user->changeCustomerStatus($customerID, $newStatus);
			
			$query = "SELECT * FROM ".TB_VPS_CUSTOMER;			
		    $statement = $this->getConnection()->getConnection()->query($query);
		    $result = $statement->fetchAll();
		    
		    $this->assertEquals($newStatus,$result[0]['status']);
		    
		    //	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeChangeCustomerStatus.xml', PATH_BRIDGE_XML);
		}
			
		
		public function testDeactivateCustomerVar1() {
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/deactivateCustomerVar1.xml';    		    		            
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
				//	set deactivation fields
				if ($tableName == 'vps_deactivation') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(2)->nodeValue = date('Y-m-d H:i:s');
								$values->item(3)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->user->deactivateCustomer(105);
			
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_DEACTIVATION), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEACTIVATION));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			
			 //	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeChangeCustomerStatus.xml', PATH_BRIDGE_XML);
		}
		
		
		public function testDeactivateCustomerVar2() {
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/deactivateCustomerVar2.xml';    		    		            
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
				//	set deactivation fields
				if ($tableName == 'vps_deactivation') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(2)->nodeValue = date('Y-m-d H:i:s');
								$values->item(3)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->user->deactivateCustomer(116);
			
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_DEACTIVATION), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEACTIVATION));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			
			//	assert bridge
    		$this->assertXmlFileEqualsXmlFile(dirname(__FILE__).'/_files/bridge/bridgeDeactivateCustomerVar2.xml', PATH_BRIDGE_XML);
		}
		
		//	no invoice for future period
		public function testActivateCustomerVar1() {
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/activateCustomerVar1.xml';    		    		            
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
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']." +5 days"));
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
				//	set deactivation fields
				if ($tableName == 'vps_deactivation') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(2)->nodeValue = date('Y-m-d H:i:s');
								$values->item(3)->nodeValue = $this->invoiceDates[1]['period_end_date'];
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->user->deactivateCustomer(105);
			$this->user->activateCustomer(105, 5);
			
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_DEACTIVATION), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEACTIVATION));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			
			//	assert bridge
			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeActivateCustomerVar1.xml';
    		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(0)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[1]['period_end_date']." +5 days"));    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
		}
		
		//there where invoices for future period	
		public function testActivateCustomerVar2() {
			//	prepare dynamic fields at dataSet
    		$xmlDataSetPath = dirname(__FILE__).'/_files/activateCustomerVar2.xml';    		    		            
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
								$values->item(10)->nodeValue = $this->invoiceDates[0]['suspension_date']; 
								$values->item(11)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								$values->item(12)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_end_date']." +5 days"));
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
							case '4':
								$values->item(7)->nodeValue = date('Y-m-d H:i:s');
								break;
						}
					}
				}		
				//	set deactivation fields
				if ($tableName == 'vps_deactivation') {
					$rows = $element->getElementsByTagName('row');
					foreach($rows as $row) {
						$values = $row->getElementsByTagName('value');
						switch ($values->item(0)->nodeValue) {
							case '2':
								$values->item(2)->nodeValue = date('Y-m-d H:i:s');
								$values->item(3)->nodeValue = $this->invoiceDates[0]['period_start_date'];
								break;
						}
					}
				}			
			}
			$this->DOM->save($xmlDataSetPath);
    		$xmlDataSet = $this->createXMLDataSet($xmlDataSetPath);
    		
			$this->user->deactivateCustomer(116);
			$this->user->activateCustomer(116, 5);
			
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_CUSTOMER), $this->getConnection()->createDataSet()->getTable(TB_VPS_CUSTOMER));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_DEACTIVATION), $this->getConnection()->createDataSet()->getTable(TB_VPS_DEACTIVATION));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_INVOICE), $this->getConnection()->createDataSet()->getTable(TB_VPS_INVOICE));
			$this->assertTablesEqual($xmlDataSet->getTable(TB_VPS_PAYMENT), $this->getConnection()->createDataSet()->getTable(TB_VPS_PAYMENT));
			
			//	assert bridge
			$bridgePath = dirname(__FILE__).'/_files/bridge/bridgeActivateCustomerVar2.xml';
    		$this->DOM->load($bridgePath);
    		$this->DOM->getElementsByTagName('customer')->item(5)->getElementsByTagName('period_end_date')->item(0)->nodeValue = date('Y-m-d', strtotime($this->invoiceDates[0]['period_end_date']." +5 days"));    		
    		$this->DOM->save($bridgePath);
    		$this->assertXmlFileEqualsXmlFile($bridgePath, PATH_BRIDGE_XML);
			//$this->assertXmlFileEqualsXmlFile(PATH_BRIDGE_XML_MASTER_COPY, PATH_BRIDGE_XML);
		}
		
		public function testGetLastDeactivation() {
			$customerID[0] = 116;
			$expectedResult[0] = array (
				'id'				=> 1,
				'customer_id'		=> 116,
				'date'				=> '2008-08-08 01:01:01',
				'period_end_date'	=> '2008-09-08',
				'daysLeft'			=> 31
			);
			$expectedResult[0]['daysPassed'] = floor((strtotime("now") - strtotime($expectedResult[0]['date']))/ (60*60*24));
			
			$customerID[1] = 105;
			$expectedResult[1] = false;
			
			//	assertions
    		foreach ($expectedResult as $key=>$value) {
    			$this->assertEquals($expectedResult[$key], $this->user->getLastDeactivation($customerID[$key]));	
    		}  
		}
		
		
		public function testGetCustomerList() {
			$expectedResult = array (
				array (
            		'id' 			=> 105,
            		'phone' 		=> '949 495-0999',
            		'contactPerson' => 'Jon Gypsyn',
            		'email' 		=> 'test_email@somewhere.com',
            		'name' 			=> 'Gyant Demo Version',
            		'trial_end_date'=> '2009-11-16',
            		'discount' 		=> '0.00',
            		'status'	 	=> 'on',
            		'balance' 		=> '0.00',
            		'time_with_us'	=> 1
            	),
            	array (
            		'id' 			=> 111,
            		'phone' 		=> 'kgh',
            		'contactPerson' => 'gd',
            		'email' 		=> 'ghjg@o.ia',
            		'name' 			=> 'TestCompany',
            		'trial_end_date'=> '2009-11-16',
            		'discount' 		=> '0.00',
            		'status'	 	=> 'on',
            		'balance' 		=> '0.00',
            		'time_with_us'	=> 1
            	),
            	array (
            		'id' 			=> 115,
            		'phone' 		=> '949 495-0999',
            		'contactPerson' => 'Denis',
            		'email' 		=> 'test_email@somewhere.com',
            		'name' 			=> 'Tukalenko Inc',
            		'trial_end_date'=> '2009-11-16',
            		'discount' 		=> '0.00',
            		'status'	 	=> 'on',
            		'balance' 		=> '0.00',
            		'time_with_us'	=> 1
            	),
            	array (
            		'id' 			=> 116,
            		'phone' 		=> '949 495-0999',
            		'contactPerson' => 'Denis',
            		'email' 		=> 'test_email@somewhere.com',
            		'name' 			=> 'Tukalenko Inc Corp',
            		'trial_end_date'=> '2009-11-16',
            		'discount' 		=> '0.00',
            		'status'	 	=> 'on',
            		'balance' 		=> '0.00',
            		'time_with_us'	=> 1
            	)
			);

			$this->assertEquals($expectedResult,$this->user->getCustomerList());
		}
	}
?>
