<?php

class VPSUser {
	private $db;
	private $auth;
	private $access;
	private $xnyo;



    function VPSUser($db, $auth=false, $access=false, $xnyo=false) {
    	$this->db = $db;
    	$this->auth = $auth;
    	$this->access = $access;
    	$this->xnyo = $xnyo;
    }



    /*
     * Check for a new user from VOC Users System
     */
    private function isNewUser($username, $password) {
    	//$this->db->select_db(DB_NAME);

    	//	Check if exist in VPS users table
    	$query = "SELECT user_id FROM ".TB_VPS_USER." WHERE accessname='".$username."' && password='".md5($password)."' LIMIT 1";
    	$this->db->query($query);

    	if ($this->db->num_rows() > 0) {
    		//	Not new. User already exist.
    		return false;
    	}

    	//	Check for user's existence in Bridge in company-level
    	$vps2voc = new VPS2VOC($this->db);
    	$return = $vps2voc->CheckUserExistence($username, md5($password));
    	return $return;
    }



    public function authorize($username, $password) {

    	if ($this->isNewUser($username, $password)) {
    		    		  		
    		return $this->convertUserToVPSUserByAuth($username, $password);
    	}

    	//	Try to authorize    user data:
    	if ($this->auth->login($username, $password)) {
    		return true;
    	} else {
    		return false;
    	}
    }




	public function isLoggedIn(){
		if ($this->access->check("required")) {
			return true;
		} else {
			return false;
		}
	}




    public function logout() {
    	$this->access->logout();
		header ('Location: '.$this->xnyo->logout_redirect_url);
    }

    public function addUser($userDetails, $md5 = true) {
    	$md5Password = ($md5) ? $userDetails['password'] : md5($userDetails['password']);

    	$query = "INSERT INTO ".TB_VPS_USER." (accessname, password, accesslevel_id, first_name, last_name, secondary_contact, email, secondary_email" .
    			", company_id, facility_id, department_id, address1, address2, city, state_id, zip, country_id, phone, fax) VALUES (" .
    			"'".$userDetails["accessname"]."'"
    			//. ", " . "'".md5($userDetails["password"])."'"
    			. ", '".$md5Password."'"
    			. ", '" . $userDetails["accesslevel_id"]."'"
    			. ", '".$userDetails["firstName"]."'"
    			. ", '".$userDetails["lastName"]."'"
    			. ", '".$userDetails["secondary_contact"]."'"
    			. ", '".$userDetails["email"]."'"
    			. ", '".$userDetails["secondary_email"]."'"
    			. ", '".$userDetails["company_id"]."'"
    			. ", '".$userDetails["facility_id"]."'"
    			. ", '".$userDetails["department_id"]."'"
    			. ", '".$userDetails["address1"]."'"
    			. ", '".$userDetails["address2"]."'"
    			. ", '".$userDetails["city"]."'"
    			. ", '".$userDetails["state_id"]."'"
    			. ", '".$userDetails["zip"]."'"
    			. ", '".$userDetails["country_id"]."'"
    			. ", '".$userDetails["phone"]."'"
    			. ", '".$userDetails["fax"]."'"
    			.")";

    	$this->db->exec($query);
    	$id = $this->db->getLastInsertedID();
    	return $id;
    }

    public function setUserDetails($userDetails, $fullUpdate=false) {
    	if ($fullUpdate) {	//	update with accessname, password and accesslevel - for ADMINs
	    	$query = "UPDATE ".TB_VPS_USER." SET "

		    	. "accessname='".$userDetails["accessname"]."'"
				. ", password='".md5($userDetails["password"])."'"
				. ", accesslevel_id='".$userDetails["accesslevel_id"]."'"
				. ", first_name='".$userDetails["firstName"]."'"
				. ", last_name='".$userDetails["lastName"]."'"
				. ", secondary_contact='".$userDetails["secondary_contact"]."'"
				. ", email='".$userDetails["email"]."'"
				. ", secondary_email='".$userDetails["secondary_email"]."'"
				. ", company_id='".$userDetails["company_id"]."'"
				. ", facility_id='".$userDetails["facility_id"]."'"
				. ", department_id='".$userDetails["department_id"]."'"
				. ", address1='".$userDetails["address1"]."'"
				. ", address2='".$userDetails["address2"]."'"
				. ", city='".$userDetails["city"]."'"
				. ", state_id='".$userDetails["state_id"]."'"
				. ", zip='".$userDetails["zip"]."'"
				. ", country_id='".$userDetails["country_id"]."'"
				. ", phone='".$userDetails["phone"]."'"
				. ", fax='".$userDetails["fax"]."'"

				. " WHERE user_id=".$userDetails["user_id"]
				. " LIMIT 1";

    	} else { //	update info, that USER edited
    		$query = "UPDATE ".TB_VPS_USER." SET "

				. "first_name='".$userDetails["firstName"]."'"
				. ", last_name='".$userDetails["lastName"]."'"
				. ", secondary_contact='".$userDetails["secondary_contact"]."'"
				. ", email='".$userDetails["email"]."'"
				. ", secondary_email='".$userDetails["secondary_email"]."'"
				. ", company_id='".$userDetails["company_id"]."'"
				. ", facility_id='".$userDetails["facility_id"]."'"
				. ", department_id='".$userDetails["department_id"]."'"
				. ", address1='".$userDetails["address1"]."'"
				. ", address2='".$userDetails["address2"]."'"
				. ", city='".$userDetails["city"]."'"
				. ", state_id='".$userDetails["state_id"]."'"
				. ", zip='".$userDetails["zip"]."'"
				. ", country_id='".$userDetails["country_id"]."'"
				. ", phone='".$userDetails["phone"]."'"
				. ", fax='".$userDetails["fax"]."'"

				. " WHERE user_id=".$userDetails["user_id"]
				. " LIMIT 1";
    	}

    	$this->db->query($query);
    }

    public function deleteUser($userID) {
    	$query = "DELETE FROM ".TB_VPS_USER." WHERE user_id = ".$userID;
    	$this->db->exec($query);
    }


   	function getUserIDbyAccessname($accessname) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT user_id FROM ".TB_VPS_USER." WHERE accessname='".$accessname."'");
		$data=$this->db->fetch(0);
		return $data->user_id;
	}


	function getUserAccessLevel($id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT accesslevel_id FROM ".TB_VPS_USER." WHERE user_id=".$id);
		$data=$this->db->fetch(0);
		$accesslevel_id=$data->accesslevel_id;
		switch ($accesslevel_id) {
			case 3:
				return "SuperuserLevel";
				break;

			case 0:
				return "CompanyLevel";
				break;

			case 1:
				return "FacilityLevel";
				break;

			case 2:
				return "DepartmentLevel";
				break;
		}
	}

	function getCustomerIDbyUserID($userID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT company_id FROM ".TB_VPS_USER." WHERE user_id=".$userID);

		$data=$this->db->fetch(0);
		return $data->company_id;
	}

	public function getUserDetails($userID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_VPS_USER." WHERE user_id=".$userID);
		$data=$this->db->fetch(0);
		$userDetails = array (
			"accessname"		=>	$data->accessname,
			"password"			=>	$data->password,
			"accesslevel_id"	=>	$data->accesslevel_id,
			"firstName"			=>	$data->first_name,
			"lastName"			=>	$data->last_name,
			"secondary_contact"	=>	$data->secondary_contact,
			"email"				=>	$data->email,
			"secondary_email"	=>	$data->secondary_email,
			"company_id"		=>	$data->company_id,
			"facility_id"		=>	$data->facility_id,
			"department_id"		=>	$data->department_id,
			"address1"			=>	$data->address1,
			"address2"			=>	$data->address2,
			"city"				=>	$data->city,
			"state_id"			=>	$data->state_id,
			"zip"				=>	$data->zip,
			"country_id"		=>	$data->country_id,
			"phone"				=>	$data->phone,
			"fax"				=>	$data->fax
		);
		return $userDetails;
	}

	public function getCustomerList() {

		$vps2voc = new VPS2VOC($this->db);
		$customersData = $vps2voc->getCustomerDetails();
		foreach ($customersData as $c) {

			/*$timeData = (date('Y') - date('Y',strtotime($c['trial_end_date'])))*12 +
				(date('n') - date('n',strtotime($c['trial_end_date']))) -
				((date('j') - date('j',strtotime($c['trial_end_date'])) >= 0)? 0 : 1 ); //govnokod */

            $trial_end_date = new DateTime();
            $trial_end_date->setTimestamp(intval($c['trial_end_date']));
            $monthWithUs = $trial_end_date->diff(new DateTime("now"));



            //make cute string
            //echo "diff:";

            $format = "";
            $year = $monthWithUs->y == 1 ? "year" : "years";
            $month = $monthWithUs->m == 1 ? "month" : "monts";
            $day = $monthWithUs->d == 1 ? "day" : "days";

            if($monthWithUs->y) {

                $format = "%y $year, %m $month, %d $day";
            }elseif($monthWithUs->m) {
                $format = "%m $month, %d $day";
            }else {
                $format = "%d $day";
            }
            //var_dump($monthWithUs->format("%y years - %m month"));

            $timeWithUs_formatted = $monthWithUs->format($format);
            if($monthWithUs->invert)
            {
                $timeWithUs_formatted = "Trial period: $timeWithUs_formatted left" ;
            }

            //echo $timeWithUs_formatted . " invert:" . $monthWithUs->invert . "<br/>";


			$customer = array(
				'id'				=>	$c['customer_id'],
				'phone'				=>	$c['phone'],
				'contactPerson'		=>	$c['contact'],
				'email'				=>	$c['email'],
				'name'				=>	$c['name'],
				'trial_end_date'	=>	$c['trial_end_date'],
				'discount'			=>  $c['discount'],
				'status'			=>  $c['status'],
				'balance'			=>  $c['balance'],
				'time_with_us'		=>  $timeWithUs_formatted,
				'currencySign'		=>	$c['currencySign']
			);

			$customers[]=$customer;
		}


		return $customers;
	}


	public function ifCustomerExist($customerID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_VPS_CUSTOMER." WHERE customer_id=".$customerID);
		if ($this->db->num_rows()) {
			return true;
		} else {
			return false;
		}

	}



	public function changeCustomerStatus($customerID,$newStatus) {
	/*
	 * changeCompanyStatus($customerID,$newStatus)
	 *
	 * Activate or deactivate customer accounts.
	 *  Customer didn't pay in time or smth else.
	 *  Function is called by Admin or VPS.
	 *
	 * $newStatus: on, off
	 *
	 * */
		//$this->db->select_db(DB_NAME);

    	$query = "UPDATE ".TB_VPS_CUSTOMER." SET " .
    			"status = '".$newStatus."' " .
    			"WHERE customer_id = ".$customerID;

    	$this->db->query($query);

	}


	public function deactivateCustomer($customerID) {

		//deactivate customer
		$this->changeCustomerStatus($customerID,"off");

		//save deactivation date
		$this->saveDeactivation($customerID);

		//cancel invoices
		$invoice = new Invoice($this->db);
		$currentInvoice = $invoice->getCurrentInvoice($customerID);
		if ($currentInvoice) {

			//cancel invoice for future period if exist
			$invoiceForFutureBP = $invoice->getInvoiceForFuturePeriod($customerID);
			if ($invoiceForFutureBP) {
				$invoice->cancelInvoice($invoiceForFutureBP['invoiceID']);
			}
			$invoice->cancelInvoice($currentInvoice['invoiceID']);

		} else {

			$invoiceForFutureBP = $invoice->getInvoiceWhenTrialPeriod($customerID);
			if ($invoiceForFutureBP) {
				$invoice->cancelInvoice($invoiceForFutureBP['invoiceID']);
			}

		}
		//manage custom invoices
		$customInvoices = $invoice->getCustomDueInvoices($customerID);
		if ($customInvoices) {
			foreach ($customInvoices as $customInvoice) {
				$invoice->cancelInvoice($customInvoice['invoiceID']);
			}
		}

	}


	public function activateCustomer($customerID, $shift) {
		//deactivate customer
		$this->changeCustomerStatus($customerID,"on");

		//restore invoices
		$lastDeactivation = $this->getLastDeactivation($customerID);

		$invoice = new Invoice($this->db);

		//	TODO: check me
		$invoice->restoreDueCustomInvoices($customerID, $shift);

		$lastInvoice = $invoice->getLastInvoice($customerID, false);

		switch (true) {

			//no invoice for future period when was deactivation
			case ($lastDeactivation['period_end_date'] == $lastInvoice['periodEndDate']):

				//restore last invoice with $shift
				$invoice->restoreInvoice($lastInvoice['invoiceID'],$shift);
				break;


			//there where invoices for future period
			case ($lastDeactivation['period_end_date'] == $lastInvoice['periodStartDate']):
				$date = $lastDeactivation['date'];
				$invoiceWhenDeactivation = $invoice->getCurrentInvoice($customerID,$date, false);

				//trial period
				if (!$invoiceWhenDeactivation) {
					//restore last invoice with $shift
					$invoice->restoreInvoice($lastInvoice['invoiceID'],$shift);

				//billing period
				} else {

					//restore original invoice with $shift
					$invoice->restoreInvoice($invoiceWhenDeactivation['invoiceID'],$shift);

					//restore invoice for future period
					$invoice->restoreInvoice($lastInvoice['invoiceID'],$shift, true);
				}
				break;

			//hz
			default:
				break;
		}
	}

	private function saveDeactivation($customerID) {
		//$this->db->select_db(DB_NAME);

		$currentDate = date('Y-m-d H:i:s');

		$invoice = new Invoice($this->db);
		$currentInvoice = $invoice->getCurrentInvoice($customerID);
		if ($currentInvoice) {
		//billing period
			$periodEndDate = $currentInvoice['periodEndDate'];
		} else {
		//trial period
			$invoiceForFutureBP = $invoice->getInvoiceWhenTrialPeriod($customerID);
			if ($invoiceForFutureBP) {
				$periodEndDate = $invoiceForFutureBP['periodStartDate'];
			}

		}

		$query = "INSERT INTO ".TB_VPS_DEACTIVATION." (customer_id, date, period_end_date) VALUES (".$customerID.", '".$currentDate."', '".$periodEndDate."')";
		$this->db->query($query);
	}

	public function getLastDeactivation($customerID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT id FROM ".TB_VPS_DEACTIVATION." WHERE customer_id = ".$customerID." ORDER BY date DESC LIMIT 1");
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			return $this->getDeactivationDetails($data->id);
		} else {
			return false;
		}
	}



	/**
	 * 	function gets trial period customers
	 * 	@return array of 'notRegistered' and 'registered' at VPS customers
	 */
	public function getTrialCustomers() {

		$vps2voc = new VPS2VOC($this->db);
		$customers = array (
			'notRegistered'	=> false,
			'registered'	=> false
		);

		//	get all customers list
		$customersDetails = $vps2voc->getCustomerDetails(null,true); //lets get all customers(with not registered)

		if (is_null($customersDetails)) {
			return false;
		}

		//	find trial customers from all list
		foreach ($customersDetails as $customerDetails) {
			if (strtotime($customerDetails['trial_end_date']) > strtotime('now')) {
				if ($customerDetails ['status'] != 'notReg') {
					//	registered
					$customers['registered'][] = $customerDetails;
				} else {
					//	not registered at VPS
					$customers['notRegistered'][] = $customerDetails;
				}
			}
		}

		return $customers;
	}



	public function copyUserToVPS($userID) {		//	UNIT TEST
		$userDetails = $this->convertUserToVPSUserByUserID($userID);
		$this->addUser($userDetails, true);	//	password already md5
	}



	private function getDeactivationDetails($id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT *, DATEDIFF(period_end_date, date) days_left, DATEDIFF(CURDATE(), date) days_passed FROM ".TB_VPS_DEACTIVATION." WHERE id = ".$id);
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$deactivationDetails = array (
				'id'				=> $data->id,
				'customer_id'		=> $data->customer_id,
				'date'				=> $data->date,
				'period_end_date'	=> $data->period_end_date,
				'daysLeft'			=> $data->days_left,
				'daysPassed'		=> $data->days_passed
			);
			return $deactivationDetails;
		} else {
			return false;
		}
	}



	private function convertUserToVPSUserByAuth($username, $password, $md5 = false) {
		//	Prepare user details for registration
		//get data about company and user from Bridge XML
		$md5Password = ($md5) ? $password :  md5($password);
//		$bridge = new Bridge($this->db);
//		$userID = $bridge->getUserID($username, $md5Password);
//		$userData = $bridge->getUserDetails($userID); customer details:
		$vps2voc = new VPS2VOC($this->db);
		$userID = $vps2voc->getUserID($username);
		$userData = $vps2voc->getUserDetails($userID);
		$customerData = $vps2voc->getCustomerDetails($userData['company_id'],true);
		return $this->convertUserToVPSUser($userData, $customerData);
	}



	private function convertUserToVPSUserByUserID ($userID) {
		//	Prepare user details for registration
		//get data about company and user from Bridge XML
		$vps2voc = new VPS2VOC($this->db);
		$userData = $vps2voc->getUserDetails($userID);
		$customerData = $vps2voc->getCustomerDetails($userData['company_id']);

		return $this->convertUserToVPSUser($userData, $customerData);
	}



	private function convertUserToVPSUser($userData, $customerData) {
		//	Try to convert Username to First name & Last name
		$name = trim($userData['username']);
		$spaceIndex = strpos($name, " ");
		if ($spaceIndex == -1) {
			$firstName = $name;
			$lastName = "";
		} else {
			$details = split(" ", $name, 2);
			$firstName = $details[0];
			$lastName = $details[1];
		}

		//	Fill in data array
		$userDetails = array (
			"showAddUser"		=>	true,
			"accessname"		=>	$userData['accessname'],
			"password"			=>	$userData['password'],
			"accesslevel_id"	=>	$userData['accesslevel_id'],
			"firstName"			=>	$firstName,
			"lastName"			=>	$lastName,
			"secondary_contact"	=>	"",
			"email"				=>	$userData['email'],
			"secondary_email"	=>	"",
			"company_id"		=>	$userData['company_id'],
			"facility_id"		=>	$userData['facility_id'],
			"department_id"		=>	$userData['department_id'],
			"address1"			=>	$customerData['address'],
			"address2"			=>	"",
			"city"				=>	$customerData['city'],
			"state_id"			=>	$customerData['state'],
			"zip"				=>	$customerData['zip'],
			"country_id"		=>	$customerData['country'],
			"phone"				=>	$userData['phone'],
			"fax"				=>	$customerData['fax']
		);

		return $userDetails;
	}

	/*private function isTrialPeriod($customerID, $date = 'today') {
		//$this->db->select_db(DB_NAME);

    	$date = ($date == 'today') ? date('Y-m-d') : $date;

    	$query = "SELECT * " .
    			"FROM ".TB_VPS_INVOICE." " .
    			"WHERE customer_id = ".$customerID." " .
    			"AND '".$date."' BETWEEN period_start_date AND period_end_date";
    			echo $query;
    	$this->db->query($query);
	}*/

	public function getCustomerLimits($customerID) {
    	$customerID = (int)$customerID;

		$query = "SELECT vl.name, vcl.current_value, vcl.max_value " .
				"FROM `".TB_VPS_LIMIT."` vl, `".TB_VPS_CUSTOMER_LIMIT."` vcl, `".TB_VPS_LIMIT_PRICE."` vlp " .
				"WHERE vl.limit_id = vlp.limit_id AND vlp.limit_price_id = vcl.limit_price_id " .
				"AND vcl.customer_id = '$customerID'";
    	$this->db->query($query);
    	$customer_limits = $this->db->fetch_all();

    	foreach ($customer_limits as $limit) {
    		$return_limits [$limit->name] = array(
    			'current_value' => $limit->current_value,
    			'max_value' => $limit->max_value,
    		);
    	}

    	$query = "SELECT vb.bplimit, vc.bplimit_current FROM `".TB_VPS_CUSTOMER."` vc, `".TB_VPS_BILLING."` vb " .
    			"WHERE vc.billing_id = vb.billing_id " .
    			"AND vc.customer_id = '$customerID' " .
    			"LIMIT 1";
    	$this->db->query($query);
    	$bplimit = $this->db->fetch(0);
    	$return_limits ['Source count'] = array(
    		'current_value' => $bplimit->bplimit_current, //TODO track current value here!
    		'max_value' => ((!is_null($bplimit->bplimit)) ? ($bplimit->bplimit) : (1)) //1 - equipment count for trial(not registered) user
    	);
    	if (is_null($return_limits ['Source count']['current_value'])) {
    		//if its null - customer is not registered, but we should get current value of equipments
    		$vps2voc = new VPS2VOC($this->db);
    		$return_limits ['Source count']['current_value'] = $vps2voc->getCurrentEquipmentCount($customerID);
    	}

		return $return_limits;
    }

	public function setCustomerLimitByID ($customerID, $limit) {//is it needed?!YES!
		$query = "SELECT lp.limit_price_id FROM vps_limit_price lp, vps_customer_limit cl " .
				"WHERE lp.limit_id = '".$limit['limit_id']."' " .
				"AND lp.limit_price_id = cl.limit_price_id " .
				"AND cl.customer_id = '$customerID' LIMIT 1 ";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			//memory + MSDS
			$limit_price_id = $this->db->fetch(0)->limit_price_id;
			$query = "UPDATE vps_customer_limit " .
					"SET current_value = '".$limit['current_value']."', max_value = '".$limit['max_value']."' " .
					"WHERE customer_id = '$customerID' AND limit_price_id = '$limit_price_id'";
		} else {
			//bplimit
			$query = "UPDATE vps_customer SET bplimit_current = '".$limit['current_value']."' " .
					"WHERE customer_id = '$customerID'";
		}
		$this->db->query($query);
	}

	public function loadConfigs() {
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

}
?>