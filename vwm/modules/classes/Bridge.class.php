<?php
class Bridge {
	
	private $db;
	private $bridgeDOM;
	private $xmlfile = PATH_BRIDGE_XML;
	private $xmlschema = PATH_BRIDGE_XML_SCHEMA;
	
	private $customer_fields = array('name', 'address', 'city', 'zip', 'state', 'country', 'county', 'phone', 'fax', 'email',
    	  						 'contact', 'title', 'trial_end_date', 'period_end_date', 'deadline_counter', 'status');
    	  						 
	private $customerlimit_fields = array('limit_id', 'current_value', 'max_value');
	    	  						 
    private $limit_fields = array('name', 'increase_step', 'unit_type', 'type');
    
    private $user_fields = array('username', 'accessname', 'password', 'email', 'phone', 'accesslevel_id', 'company_id', 'facility_id', 'department_id');
    
    	
    function Bridge($db = false, $xmlfile="", $xmlschema="") {
    	
      $this->bridgeDOM = new DOMDocument();
      $this->bridgeDOM->preserveWhiteSpace = false;
      $this->bridgeDOM->formatOutput = true;
      
      if ($xmlfile) $this->xmlfile = $xmlfile;
      if ($xmlschema) $this->xmlschema = $xmlschema;
      $this->bridgeDOM->load($this->xmlfile);
      
      if (!$db) {
       require('config/constants.php');
		require_once ('modules/xnyo/xnyo.class.php');
		
		$xnyo = new Xnyo;
		
		$xnyo->database_type	= DB_TYPE;
		$xnyo->db_host 			= DB_HOST;
		$xnyo->db_user			= DB_USER;
		$xnyo->db_passwd		= DB_PASS;
		
		$xnyo->start();
      }	
		
		$this->db = $db;
      	
    }
    
     public function getModuleNameByID($moduleID) 
     {    	
    	$this->db->query("SELECT name FROM ".TB_MODULE." WHERE id=$moduleID LIMIT 1");
    	$name=$this->db->fetch(0)->name;    			
    	return $name;    	
    }
    
    public function getAllModulesDetails() 
    {   	
    	$this->db->query("SELECT * FROM ".TB_MODULE);    	
    	return $this->db->fetch_all_array();    		
    }	
    
    //DEPRECATED
    public function addNewCustomer($customerID, $customerDetails) {
    	
    	$customerID = (int)$customerID;
    	$customer = $this->bridgeDOM->createElement('customer');
    	$customer->setAttribute('id', $customerID);
    	$customer->setIdAttribute('id', true);
         
        foreach ($this->customer_fields as $field)
        {
           $field = $this->bridgeDOM->createElement($field);
       	   $text = $this->bridgeDOM->createTextNode('');
       	   $field->appendChild($text);
       	   $customer->appendChild($field);
       			
        }
         
        $limits = $this->getAllLimitsDetails();
        foreach ($limits as $limit)
        {
           $customer_limit = $this->bridgeDOM->createElement('limit');
       	   
       	   $limit_id = $this->bridgeDOM->createElement('limit_id');  
       	   $limit_id->appendChild($this->bridgeDOM->createTextNode($limit['limit_id']));
       	   $current_value = $this->bridgeDOM->createElement('current_value');
       	   $current_value->appendChild($this->bridgeDOM->createTextNode('0'));
       	   $max_value = $this->bridgeDOM->createElement('max_value');
       	   
       	   $billing = new Billing($this->db);
       	   
       	   if ($limit['type'] == 'bplimit') {
       	   	
       	   		$count = $billing->getMinBPLimitCount(); 
       	   		$max_value->appendChild($this->bridgeDOM->createTextNode((string)$count));
       	   			
       	   } else {
       	   	 
       	   $trial_limit_price = $billing->getTrialLimitPriceDetails($limit['limit_id']);
       	   $max_value->appendChild($this->bridgeDOM->createTextNode((string)$trial_limit_price['default_limit']));
       	   
       	   }
       	   
       	   
       	   $customer_limit->appendChild($limit_id);
       	   $customer_limit->appendChild($current_value);
       	   $customer_limit->appendChild($max_value);
       	   
       	   $customer->appendChild($customer_limit);
        }
            	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLCustomers = $XMLBridge->getElementsByTagName('customers')->item(0);
    	$XMLCustomers->appendChild($customer);
    	$this->bridgeDOM->save($this->xmlfile);
    	
    	$this->setCustomerDetails($customerID, $customerDetails);
    	    	
    }
    //DEPRECATED
    public function addNewUser($userID, $userDetails) {
    	
    	$userID = (int)$userID;
    	$user = $this->bridgeDOM->createElement('user'); 
    	$user->setAttribute('id', $userID);
        $user->setIdAttribute('id', true);
        
        foreach ($this->user_fields as $field)
        {
           $field = $this->bridgeDOM->createElement($field);
       	   $text = $this->bridgeDOM->createTextNode('');
       	   $field->appendChild($text);
       	   $user->appendChild($field);
       			
        }
        
        $XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
        $XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
    	$XMLUsers->appendChild($user);
    	$this->bridgeDOM->save($this->xmlfile);
    	
    	$this->setUserDetails($userID, $userDetails);
    	    	
    }
    
    //DEPRECATED
    // input $type = customer | limit | user
    // return DOMElement by ID and type
    public function getObjByID($ID, $type) {
    	
    	$ID = (int)$ID;
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLObjects = $XMLBridge->getElementsByTagName($type.'s')->item(0);  	// input $type.'s' - tag which desired object belongs :    
    	 																			// customers | limits | users - main tags in bridge  
       	$objects = $XMLObjects->getElementsByTagName($type);
       	
    	foreach ($objects as $object)
    	
    		if ((int)$object->getAttribute('id') == $ID)
    		 
    				return $object; 	
    		
       	return null;
    }
    
    public function getCustomerDetails($customerID) {    	
    	$customerID = (int)$customerID;
    	$customerDetails = array();
    		//old version with xml
//    	$customer = $this->getObjByID($customerID, 'customer');
//    	
//    	if (is_null($customer)) {
//    		return null; //no customer
//    	}
//    			
//       	$data = array();       			
//       	foreach ($this->customer_fields as $field)
//    		{    		
//    			$customer_field = $customer->getElementsByTagName($field)->item(0);     		
//       			$data[$field] = $customer_field->nodeValue;       			
//       		}
//       				
//       	$customerDetails = array (
//				'company_id'	  =>	$customerID,
//				'name'			  =>	$data['name'],
//				'address'		  =>	$data['address'],
//				'city'			  =>	$data['city'],
//				'zip'			  =>	$data['zip'],
//				'state'			  =>	$data['state'],
//				'country'		  =>	$data['country'],
//				'county'		  =>	$data['county'],
//				'phone'			  =>	$data['phone'],
//				'fax'			  =>	$data['fax'],
//				'email'			  =>	$data['email'],
//				'contact'		  =>	$data['contact'],
//				'title'			  =>	$data['title'],
//				'trial_end_date'  =>	$data['trial_end_date'],
//				'period_end_date' =>	$data['period_end_date'],
//				'deadline_counter'=>	$data['deadline_counter'],
//				'status'		  =>	$data['status']
//			);
    		//new version with db
//    	$query = "SELECT * , IFNULL( (SELECT period_end_date FROM `".TB_VPS_INVOICE."` WHERE status = 'paid' " .
//    			"AND customer_id = vc.customer_id " .
//    			"AND billing_info IS NOT NULL " .
//    			"AND '".date('Y-m-d')."' BETWEEN period_start_date AND period_end_date),1) AS period_end_date " .
//    			"FROM `".TB_COMPANY."` c, `".TB_VPS_CUSTOMER."` vc " .
//    			"WHERE c.company_id = vc.customer_id ";
    	
    	$query = "SELECT * FROM `".TB_COMPANY."` c, `".TB_VPS_CUSTOMER."` vc WHERE c.company_id = vc.customer_id";
    			
    	$this->db->query($query);
    	$customerDetails = $this->db->fetch_all_array();
    	$companyList = array();
    	foreach($customerDetails as $details) {
    		$companyList []= $details['company_id'];
    	}
    	$invoice = new Invoice($this->db);
    	//$dates = $invoice->getDatesForCustomerList($companyList);
    	foreach($customerDetails as $key => $details) {
    		$customerDetails [$key]['period_end_date']= $dates[$details['company_id']]['period_end_date'];
    		if (strtoupper($dates[$details['company_id']]['status']) == 'PAID') {
    			$customerDetails [$key]['deadline_counter']= false; //no need in counter if invoice was paid!
    		} else {
    			$customerDetails [$key]['deadline_counter']= $dates [$details['company_id']]['suspension_date'] - date('Y-m-d'); 
    		}
    	}
    	return $customerDetails;    	
    }
    
    //DEPRECATED: we use info from db
    public function setCustomerDetails($customerID, $customerDetails) {
    	
    	$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
      			
		foreach ($this->customer_fields as $field)
    	
    		if (array_key_exists($field, $customerDetails)) {
    	
    				$customer_field = $customer->getElementsByTagName($field)->item(0);   
       				$customer_field->nodeValue = $customerDetails[$field];
       		}
       		
    	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    //DEPRECATED
    public function setCustomerDeadLineCounter($customerID, $counter_value) {
    	
    	$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
      			
		$customer_field = $customer->getElementsByTagName('deadline_counter')->item(0);   
       	$customer_field->nodeValue = $counter_value;
       	
       	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    //DEPRECATED
    public function setCustomerPeriodEndDate($customerID, $end_date) {
    	
    	$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
      			
		$customer_field = $customer->getElementsByTagName('period_end_date')->item(0);   
       	$customer_field->nodeValue = $end_date;
       	
       	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    
    
    /**     
     * GEt modules list or module details by ID
     * @param [int $id] - Module ID
     * @return array $modules - array where key is a module ID, and value is module name: $module[3]='Logbook'
     */
    public function getModules($id = null) {
    	$query = "SELECT id, name FROM ".TB_MODULE." ";
    	if (!is_null($id)) {
    		$safeID = mysql_escape_string($id);
    		$query .= " WHERE id = ".$id;	
    	}    	
    	
    	$this->db->query($query);
    	if ($this->db->num_rows() > 0) {
    		$rows = $this->db->fetch_all_array();
    		foreach ($rows as $row) {
    			$modules[$row['id']] = $row['name'];    				    			
    		}   		
    		return $modules;
    	} else {
    		return false;
    	}
    }
    
    
    public function getUserID($accessname, $password) {
    	
//    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
//    	$XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
//       	$users = $XMLUsers->getElementsByTagName('user');
//       	
//    	foreach ($users as $user)
//    	
//    	{
//    		$user_accessname 	 = strtolower($user->getElementsByTagName('accessname')->item(0)->nodeValue);
//    		$user_password 		 = strtolower($user->getElementsByTagName('password')->item(0)->nodeValue);
//    		if (!strcmp($user_accessname, strtolower($accessname)) && !strcmp($user_password, strtolower($password)))
//    	
//    					return (int)$user->getAttribute('id');
//    	}
    	
    	$query = "SELECT user_id FROM `".TB_USER."` WHERE accessname = '$accessname' AND password = '$password' LIMIT 1";
    	$this->db->query($query);
    	return ($this->db->num_rows() > 0)?$this->db->fetch(0)->user_id:0;
    }
    
    public function getUserDetails($userID) {
    	
    	$userID = (int)$userID;
//    	$userDetails = array();
//    	$user = $this->getObjByID($userID, 'user');	
//    			
//       	$data = array();
//       			
//       	foreach ($this->user_fields as $field)
//    		{
//    			$user_field = $user->getElementsByTagName($field)->item(0);   
//       			$data[$field] = $user_field->nodeValue;
//       		}
//       				
//       	$userDetails = array (
//				'user_id'			     =>	$userID,
//				'username'		 	 	 =>	$data['username'],
//				'accessname'			 =>	$data['accessname'],
//				'password'				 =>	$data['password'],
//				'email'					 =>	$data['email'],
//				'phone'					 =>	$data['phone'],
//				'accesslevel_id'		 =>	$data['accesslevel_id'],
//				'company_id'		 	 =>	$data['company_id'],
//				'facility_id'		 	 =>	$data['facility_id'],
//				'department_id'		 	 =>	$data['department_id']
//				
//			);
    	
    	$query = "SELECT * FROM `".TB_USER."` WHERE user_id = '$userID' LIMIT 1";
    	$this->db->query($query);
    	$userDetails = $this->db->fetch_array(0);
    	return $userDetails;    	
    }
    //DEPRECATED
    public function setUserDetails($userID, $userDetails) {
    	
    	$userID = (int)$userID;
    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
       	$users = $XMLUsers->getElementsByTagName('user');
       	
    	foreach ($users as $user)
    	
    		if ((int)$user->getAttribute('id') == $userID)
    		
				foreach ($this->user_fields as $field)
    	
    				if (array_key_exists($field, $userDetails)) {
    	
    						$user_field = $user->getElementsByTagName($field)->item(0);   
       						$user_field->nodeValue = $userDetails[$field];
       				}
       				
    	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    
    public function deleteCustomer($customerID) {
    	
    	$customerID = (int)$customerID;
    	
//    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
//    	$XMLCustomers = $XMLBridge->getElementsByTagName('customers')->item(0);
//    	$customers = $XMLCustomers->getElementsByTagName('customer');
//       	
//    	foreach ($customers as $customer)
//    	
//    		if ((int)$customer->getAttribute('id') == $customerID) 
//    			
//    			$XMLCustomers->removeChild($customer);
//        	
//    	$this->bridgeDOM->save($this->xmlfile);
    	
    	//	remove customer from VPS DB
    	$query = "DELETE FROM ".TB_VPS_CUSTOMER." WHERE customer_id = ".$customerID." ";    	    			     			
    	$this->db->exec($query);
    	
    }
    
    public function deleteUser($userID) {    	
    	$userID = (int)$userID;
    	
    	$query = "SELECT accessname FROM ".TB_USER." WHERE user_id = ".$userID;
    	$this->db->query($query);
    	if ($this->db->num_rows() > 0) {
    		$accessname = $this->db->fetch(0)->accessname;
    		$vpsUser = new VPSUser($this->db);
    		$vpsUserID = $vpsUser->getUserIDbyAccessname($accessname);
    		$vpsUser->deleteUser($vpsUserID);
    	}
//    	
//    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
//    	$XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
//    	$users = $XMLUsers->getElementsByTagName('user');
//       	
//       	
//    	foreach ($users as $user)
//    	
//    		if ((int)$user->getAttribute('id') == $userID) 
//    			
//    			$XMLUsers->removeChild($user);
//        	
//    	$this->bridgeDOM->save($this->xmlfile);    	        	
    }
    
    //DEPRECATED
    public function deleteAllCustomers() {
    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLCustomers = $XMLBridge->getElementsByTagName('customers')->item(0);
    	
    	$customers = $XMLCustomers->getElementsByTagName('customer');
    	$cnt = $customers->length; 
    	for ($i=0; $i< $cnt; $i++) 
    			
    			$XMLCustomers->removeChild($customers->item(0));
    			
    	$this->bridgeDOM->save($this->xmlfile);
   
    	
    }
    
    //DEPRECATED
    public function deleteAllUsers() {
    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
    	
    	$users = $XMLUsers->getElementsByTagName('user');
    	$cnt = $users->length; 
    	for ($i=0; $i< $cnt; $i++) 
    			
    			$XMLUsers->removeChild($users->item(0));
    			
    	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    
    /*
     * input string accessname and string password (it's not md5) 
     */
    public function CheckUserExistence($accessname, $password, $accesslevel_id = 0) {
    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLUsers = $XMLBridge->getElementsByTagName('users')->item(0);
       	$users = $XMLUsers->getElementsByTagName('user');
       	
    	foreach ($users as $user)
    	
    	{
    		$user_accessname 	 = strtolower($user->getElementsByTagName('accessname')->item(0)->nodeValue);
    		$user_password 		 = strtolower($user->getElementsByTagName('password')->item(0)->nodeValue);
    		$user_accesslevel_id = (int)$user->getElementsByTagName('accesslevel_id')->item(0)->nodeValue;
    			
    		if (!strcmp($user_accessname, strtolower($accessname)) && !strcmp($user_password, strtolower($password)) && $user_accesslevel_id == $accesslevel_id)
    	
    					return true;
    	}
    	
    	return false;
    }
    
    //emission sources for VOC 
    public function getBillingPlanLimitName() {
    	return "Emission Sources";
    }
    
    public function getAllLimitsDetails() {
    	
    	$return = array();    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLLimits = $XMLBridge->getElementsByTagName('limits')->item(0);
    	$limits = $XMLLimits->getElementsByTagName('limit');
    	 
    	foreach ($limits as $limit) {
    		
    			$id = (int)$limit->getAttribute('id');
    			$name = $limit->getElementsByTagName('name')->item(0);
    			$increase_step = $limit->getElementsByTagName('increase_step')->item(0);
    			$unit_type = $limit->getElementsByTagName('unit_type')->item(0);
    			$type = $limit->getElementsByTagName('type')->item(0);
    			
    			$return[$name->nodeValue] = array(
    					'limit_id' => $id,
						'name' => $name->nodeValue,
						'increase_step' => $increase_step->nodeValue,
						'unit_type' => $unit_type->nodeValue,
						'type' => $type->nodeValue
				);    			
    	}    	
    	return $return;    	
    }	    
    
    public function getLimitDetailsByID($limitID) {
    	
    	$return = array();
    	$limitID = (int)$limitID;
    	
    	$limit = $this->getObjByID($limitID, 'limit');	
    	
    	if (isset($limit)) {
    		
    			$id = (int)$limit->getAttribute('id');
    			$name = $limit->getElementsByTagName('name')->item(0);
    			$increase_step = $limit->getElementsByTagName('increase_step')->item(0);
    			$unit_type = $limit->getElementsByTagName('unit_type')->item(0);
    			$type = $limit->getElementsByTagName('type')->item(0);
    			
    			$return = array(
    					'limit_id' => $id,
						'name' => $name->nodeValue,
						'increase_step' => $increase_step->nodeValue,
						'unit_type' => $unit_type->nodeValue,
						'type' => $type->nodeValue
						
				);
    			
    		}
    	
    	return $return;
    	
    }
    
    public function getLimitNameByID($limitID) {
    	
    	$name = null;
    	$limitID = (int)$limitID;
    	
    	$limit = $this->getObjByID($limitID, 'limit');	
    	
    	if (isset($limit)) 
    		$name = $limit->getElementsByTagName('name')->item(0)->nodeValue;
    			
    	return $name;
    	
    }
    
    //see Billing.class ~657
    //	output:
    //	$limit = array('current_value', 'max_value');
    //	$limits['MSDS'] = $limit;
    public function getCustomerLimits($customerID) {
    
    	$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
    	$customer_limits = $customer->getElementsByTagName('limit');
    	
    	$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
    	$XMLLimits = $XMLBridge->getElementsByTagName('limits')->item(0);
    	$limits = $XMLLimits->getElementsByTagName('limit');
    	
    	$return_limits = array();
    	                                                      
    	foreach ($customer_limits as $customer_limit) {
    		
    		$limit_id = $customer_limit->getElementsByTagName('limit_id')->item(0)->nodeValue;
    		foreach ($limits as $limit)
    		
    			if 	((int)$limit->getAttribute('id') == (int)$limit_id) {
    				
    				$limit_name = $limit->getElementsByTagName('name')->item(0);
    				$current_value = $customer_limit->getElementsByTagName('current_value')->item(0);
    				$max_value = $customer_limit->getElementsByTagName('max_value')->item(0);
    				$return_limits[$limit_name->nodeValue] = array('current_value' => $current_value->nodeValue, 'max_value' => $max_value->nodeValue);
    			}
    	}		
    					
		return $return_limits; 			    
    }
    
    //see Billing.class 40
    //	input description
    //	$limits['MSDS']['limit_id']
    //	$limits['MSDS']['current_value']
    //	$limits['MSDS']['max_value']
    public function setCustomerLimits($customerID, $limits) {
	
		$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
    	$customer_limits = $customer->getElementsByTagName('limit');
      	
      	foreach ($limits as $limit)	{
      		
      		$XMLlimit = $this->getObjByID($limit['limit_id'], 'limit');
      		
      		if (isset($XMLlimit)) {
      			
      			foreach ($customer_limits as $customer_limit) {
      				
      				$limit_id = (int)$customer_limit->getElementsByTagName('limit_id')->item(0)->nodeValue;
					
					if ($limit_id == (int)$limit['limit_id'])      			 
      			
						foreach ($this->customerlimit_fields as $field)
		
    						if (array_key_exists($field, $limit)) {
    	
    								$customer_field = $customer_limit->getElementsByTagName($field)->item(0);   
       								$customer_field->nodeValue = $limit[$field];
       						}
      			}
      		}
      			
      	}
      		
    	$this->bridgeDOM->save($this->xmlfile);
    	
    }
    
    /*	
     * 	input description
        int $customerID 
    	$limit['limit_id']
    	$limit['current_value']
     	$limit['max_value']
    */
    public function setCustomerLimitByID($customerID, $limit) {
	
		$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
    	$customer_limits = $customer->getElementsByTagName('limit');
      	
      	$XMLlimit = $this->getObjByID($limit['limit_id'], 'limit');
      		
      	if (isset($XMLlimit)) {
      			
      			foreach ($customer_limits as $customer_limit) {
      				
      				$limit_id = (int)$customer_limit->getElementsByTagName('limit_id')->item(0)->nodeValue;
				
					if ($limit_id == (int)$limit['limit_id'])      			 
      		
						foreach ($this->customerlimit_fields as $field)
		
    						if (array_key_exists($field, $limit)) {
    	
    								$customer_field = $customer_limit->getElementsByTagName($field)->item(0);   
       								$customer_field->nodeValue = $limit[$field];
       						}
      			}
      	}
      		
      	$this->bridgeDOM->save($this->xmlfile);
      	
      	//	also let's save to DB
      	$query = "SELECT lp.limit_id, cl.limit_price_id, cl.current_value, cl.max_value FROM vps_customer_limit cl, vps_limit_price lp " .
	      	"WHERE cl.limit_price_id = lp.limit_price_id AND " .
			"cl.customer_id = ".$customerID.""; 
      	$this->db->query($query);
      	if ( $this->db->num_rows() > 0 ) {
	      	$limitRows = $this->db->fetch_all();
	      	foreach ($limitRows as $limitRow) {
		      	$query = "UPDATE vps_customer_limit " .
		      			"SET current_value = ".$limitRow->current_value.", max_value = ".$limitRow->max_value." " .
		      			"WHERE customer_id = ".$customerID." " .
		      			"AND limit_price_id = ".$limitRow->limit_price_id."";
		      	$this->db->query($query);		      	
	      	}
      	}
    	
    }
    
    /**	
	 * 
	 * Activate or deactivate company accounts.
	 *  Customer didn't pay in time or smth else. 
	 *  Function is called by Admin or VPS.
	 * 
	 * $newStatus: on, off
	 * 
	 * */
	public function changeCustomerStatus($customerID, $newStatus) {
	
    	$customerID = (int)$customerID;
    	$customer = $this->getObjByID($customerID, 'customer');
    	$status = $customer->getElementsByTagName('status')->item(0);
    	$status->nodeValue = (string)$newStatus;
    	
    	if (trim($newStatus) == 'off') {
    		$deadline_counter = $customer->getElementsByTagName('deadline_counter')->item(0);
    		$deadline_counter->nodeValue = "NULL";
    	}
    	
    	$this->bridgeDOM->save($this->xmlfile);
	}
	
	/*
	 * function get ID's of all customers registered at VOCWEBMANAGER and saved at Bridge
	 */
	public function getAllCustomers() {
		$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
		$XMLCustomers = $XMLBridge->getElementsByTagName('customer');
		foreach ($XMLCustomers as $XMLCustomer) {
			$customerIDs[] = $XMLCustomer->getAttribute('id');
		}
		return (isset($customerIDs)) ? $customerIDs : false;
	}
	
	
	
	//	gets first at list company level user by company ID
	public function getCompanyLevelUserByCompanyID($companyID) {
		$XMLBridge = $this->bridgeDOM->getElementsByTagName('bridge')->item(0);
		$XMLUsers = $XMLBridge->getElementsByTagName('user');
		foreach ($XMLUsers as $XMLUser) {
			if ($XMLUser->getElementsByTagName('company_id')->item(0)->nodeValue == $companyID 
				&& $XMLUser->getElementsByTagName('accesslevel_id')->item(0)->nodeValue == 0) {	//	0 - company level accesslevel 
					
					$companyLevelUserID = $XMLUser->getAttribute('id');	
				break;
			}
		}
		return (isset($companyLevelUserID)) ? $companyLevelUserID : false;
	}
	
	
	
	/*
	 * function deletes all customers from xml and then 
	 * 			copies all companies (or customers of voc) to bridge xml
	 *  was created for combine main system with bridge-vps 
	 */
	 public function CopyAllCustomersToBridge() {
	 	
	 	$this->deleteAllCustomers();
	 	
	 	//Main system service
	 	//getting all companies from db
	 	//$this->db->select_db(DB_NAME);
		$this->db->query('SELECT * FROM '.TB_COMPANY);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		
		$allData = $this->db->fetch_all();
		foreach ($allData as $data) {
			
			$query = "SELECT status FROM vps_customer WHERE customer_id = ".$data->company_id."";
			$this->db->query($query);
			if ($this->db->num_rows() > 0) {
				$status = $this->db->fetch(0)->status;				
			} else {
				$status = 'off';
			}			
			
			//	get last paid invoice
			$query = "SELECT period_end_date " .
    			 "FROM ".TB_VPS_INVOICE." " .
    			 "WHERE customer_id = ".$data->company_id." " .    			 
    			 "AND billing_info IS NOT NULL " .
    			 "AND status = 'paid' " .
    			 "ORDER BY generation_date DESC " .
    			 "LIMIT 1";	
    		$this->db->query($query);
    		
			if ($this->db->num_rows() > 0) {
				$periodEndDate = $this->db->fetch(0)->period_end_date;				
			} else {
				$periodEndDate = $data->trial_end_date;
			}	
			
			$companyDetails = array (
				'company_id'		=>	$data->company_id,
				'name'				=>	$data->name,
				'address'			=>	$data->address,
				'city'				=>	$data->city,
				'zip'				=>	$data->zip,
				'county'			=>	$data->county,
				'state'				=>	$data->state,
				'country'			=>	$data->country,
				'phone'				=>	$data->phone,
				'fax'				=>	$data->fax,
				'email'				=>	$data->email,
				'contact'			=>	$data->contact,
				'title'				=>	$data->title,
				'trial_end_date'	=>	$data->trial_end_date,
				'period_end_date' 	=>	$periodEndDate,
				'deadline_counter' 	=> 	"NULL",
				'status'			=> 	$status
			);
			
			$this->addNewCustomer((int)$companyDetails['company_id'], $companyDetails);
			
			$limits = array();
			// max limit value
			$query = "SELECT lp.limit_id, cl.current_value, cl.max_value FROM vps_customer_limit cl, vps_limit_price lp " .
					"WHERE cl.limit_price_id = lp.limit_price_id AND " .
					"cl.customer_id = ".$companyDetails['company_id'].""; 
			$this->db->query($query);
			if ( $this->db->num_rows() > 0 ) {
				$limitRows = $this->db->fetch_all();
				foreach ($limitRows as $limitRow) {
					$limits[] = array (
						'limit_id' 		=> $limitRow->limit_id,
						'current_value'	=> $limitRow->current_value,
						'max_value'		=> $limitRow->max_value
					);
				}
			}
			
			$query = "SELECT 3 limit_id, bplimit " .
					"FROM vps_customer c, vps_billing b " .
					"WHERE c.billing_id = b.billing_id AND " .
					"c.customer_id = ".$companyDetails['company_id'];				
			$this->db->query($query);
			if ( $this->db->num_rows() > 0 ) {
				$limitRows = $this->db->fetch_all();
				foreach ($limitRows as $limitRow) {
					$bplimit = array (
						'limit_id' 		=> $limitRow->limit_id,
						//'current_value'	=> $limitRow->current_value,
						'max_value'		=> $limitRow->bplimit
					);
				}									
			}
			
			$query = "SELECT COUNT(e.equipment_id) current_value  FROM company c, facility f, department d, equipment e " .
				"WHERE c.company_id = f.company_id AND " .
				"f.facility_id = d.facility_id AND " .
				"d.department_id = e.department_id AND " .
				"c.company_id = ".$companyDetails['company_id']."";
			$this->db->query($query);
			if ( $this->db->num_rows() > 0 ) {
				$bplimit['current_value'] = $this->db->fetch(0)->current_value;
				$limits[] = $bplimit;				
			}
						
			foreach ($limits as $limit) {
				$this->setCustomerLimitByID($companyDetails['company_id'], $limit);
			}						
		}					
		$this->bridgeDOM->save($this->xmlfile);		
	 }
	 
	 
	 
	 /*
	 * function deletes all users from xml and then 
	 * 			copies all users to bridge xml
	 *  was created for combine main system with bridge-vps 
	 */
	 public function CopyAllUsersToBridge() {
	 	
	 	$this->deleteAllUsers();
	 	
	 	//Main system service
	 	//getting all users from db
	 	//$this->db->select_db(DB_NAME);
		$this->db->query('SELECT * FROM '.TB_USER);
		
		$num_rows = $this->db->num_rows();
		$allusers = array();
		for ($i=0; $i < $num_rows; $i++) {
			
				$data=$this->db->fetch($i);
				$userDetails=array (
					'user_id'			=>	$data->user_id,				
					'username'			=>	$data->username,
					'accessname'		=>	$data->accessname,
					'password'			=>	$data->password,
					'phone'				=>	$data->phone,
					'mobile'			=>	$data->mobile,
					'email'				=>	$data->email,
					'accesslevel_id'	=>	$data->accesslevel_id,
					'company_id'		=>	$data->company_id,
					'facility_id'		=>	0, // only company level
					'department_id'		=>	0, // only company level
			
				);
				
				$allusers[] = $userDetails; 
				
			}
			
		foreach ($allusers as $userDetails)
			
			$this->addNewUser((int)$userDetails['user_id'], $userDetails);
			
		$this->bridgeDOM->save($this->xmlfile);		
	 }
	 
	 /*
	  * load all configurations from VPS 
	  */
	 public function loadConfigs() {
	 	
	 	//$this->db->select_db(DB_NAME);				
		$query = "SELECT * FROM ".TB_VPS_CONFIG;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			$numRows = $this->db->num_rows();
			for ($i=0; $i < $numRows; $i++) {
				$data=$this->db->fetch($i);
				$configs[$data->name] = stripslashes($data->value);							
			}
		}
		
		return $configs;
	 }	
	 
    /*
     * XML validation
     * Validating XML Documents Against XML Schema
     * print all errors on screen or print xmlfile." is valid"   
     */
    public function ValidateXML() {
    	
    	libxml_use_internal_errors(true);
    	$bridge = new DOMDocument();
		$bridge->load($this->xmlfile);
		
    	if ($bridge->schemaValidate($this->xmlschema)) {
    		
			return $this->xmlfile." is valid.\n";
			
		} else {
			
		$err_string = '<b>DOMDocument::schemaValidate() Generated Errors!</b><br>';
    	$err_string.= $this->libxml_display_errors(); 
		$err_string.= "<br>".$this->xmlfile." is invalid.\n";
		}
    	
    }
    
    private function libxml_display_error($error) {
    	
    	$return = "<br/>\n";
    	switch ($error->level) {
        	case LIBXML_ERR_WARNING:
            	$return .= "<b>Warning $error->code</b>: ";
            break;
        	case LIBXML_ERR_ERROR:
            	$return .= "<b>Error $error->code</b>: ";
            break;
        	case LIBXML_ERR_FATAL:
            	$return .= "<b>Fatal Error $error->code</b>: ";
            break;
    	}
    	
    	$return .= trim($error->message);
    	if ($error->file) {
        	$return .=    " in <b>$error->file</b>";
    	}
    	
    	$return .= " on line <b>$error->line</b>\n";

    	return $return;
	}

	private function libxml_display_errors() {
    	
    	$errors = libxml_get_errors();
    	$err_string = "";
    	foreach ($errors as $error) {
        	$err_string.= $this->libxml_display_error($error);
    	}
    	libxml_clear_errors();
    	return $err_string;
	}
    // end of XML validation
}
?>