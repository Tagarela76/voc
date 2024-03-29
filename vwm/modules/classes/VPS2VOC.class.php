<?php

class VPS2VOC {
	private $db;


	public $currentDate;

    function VPS2VOC($db = false, $currentDate = null) {
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

	    $this->currentDate = (is_null($currentDate)) ? date('Y-m-d') : $currentDate;
    }

    //MODULES

    public function getModuleNameByID($moduleID) {
    	$query = "SELECT name FROM ".TB_MODULE." WHERE id=$moduleID LIMIT 1";
    	$this->db->query($query);
    	echo "<br/>$query<br/>";
    	$name=$this->db->fetch(0)->name;
    	return $name;
    }

    public function getAllModulesDetails() {
    	$this->db->query("SELECT * FROM ".TB_MODULE);
    	return $this->db->fetch_all_array();
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

    //CUSTOMER

    public function getCustomerDetails($customerID = null, $getWithNotRegistered = false, $getDeadline = true) {
    	$customerDetails = array();

    	$query = "SELECT c . * , vc . * , cur.sign AS currencySign
				FROM ".TB_COMPANY." c, ".TB_VPS_CUSTOMER." vc, ".TB_VPS_CURRENCY." cur
				WHERE c.company_id = vc.customer_id
				AND cur.id = vc.currency_id ";

    	/*$query = "SELECT * FROM `".TB_COMPANY."` c, `".TB_VPS_CUSTOMER."` vc " .
    			"WHERE c.company_id = vc.customer_id ";*/
    	if(!is_null($customerID)) {
    		$customerID = (int)$customerID;
    		$query .= "AND vc.customer_id = '$customerID' LIMIT 1";
    	}


    	$this->db->query($query);
    	$customerDetails = $this->db->fetch_all_array();


    	$companyList = array();
    	foreach($customerDetails as $details) {
    		$companyList []= $details['company_id'];
    	}

    	if ($getDeadline && $customerDetails) {

    		//echo "<h1>IF</h1>";
	    	$invoice = new Invoice($this->db);
	    	if (method_exists($invoice,'getDatesForCustomerList')) {
	    		$dates = $invoice->getDatesForCustomerList($companyList);
	    		//echo "if exists";
	    	} else {
	    		//echo "else";
	    		//no need in deadline counter if we dont have any info from invoice!
	    		foreach ( $companyList as $value ) {
	    			$dates [$value]['status']= 'PAID';
	    		}
	    	}

	    	foreach($customerDetails as $key => $details) {


	    		$customerDetails [$key]['period_end_date']= $dates[$details['company_id']]['period_end_date'];

                $dt = new DateTime();


	    		if (strtoupper($dates[$details['company_id']]['status']) == 'PAID') {
                    $dt->setTimestamp($dates [$details['company_id']]['period_end_date']);
                    $diff = $dt->diff(new DateTime());
	    			$customerDetails [$key]['deadline_counter'] = $diff->format("%d"); //(strtotime($dates [$details['company_id']]['period_end_date']) - strtotime($this->currentDate))/ (60 * 60 * 24);
	    		} else {
	    			$dt->setTimestamp($dates [$details['company_id']]['suspension_date']);
                    $diff = $dt->diff(new DateTime());
	    			$customerDetails [$key]['deadline_counter']= $diff->format("%d");//(strtotime($dates [$details['company_id']]['suspension_date']) - strtotime($this->currentDate))/ (60 * 60 * 24);
	    		}
	    	}
    	}

    	//elseif ($getWithNotRegistered) {
    	if ($getWithNotRegistered) {

    		if(is_null($customerID))
    		{
    			$query = "SELECT * FROM `company` c " .
    				"WHERE c.company_id NOT IN " .
    				"(SELECT customer_id FROM `vps_customer`) ";
    		}
    		else
    		{
    			$query = "SELECT * FROM `company` c " .
    				"WHERE c.company_id = '$customerID' LIMIT 1 ";
    		}

    		$this->db->query($query);
    		$notRegisteredCustomers = $this->db->fetch_all_array();
    		foreach ($notRegisteredCustomers as $customer) {
    			$customer ['status']= 'notReg';

    			$customer ['deadline_counter']= (strtotime($customer['trial_end_date']) - strtotime($this->currentDate))/ (60 * 60 * 24);
    			$customerDetails []= $customer;
    		}
    	}
    	else
    	{

    	}

    	if (!is_null($customerID)) {
    		$customerDetails = $customerDetails[0];
    	}
    	
    	return $customerDetails;
    }


    //LIMITS
    public function getCurrentEquipmentCount($customerID) {
    	$equipment = new Equipment($this->db);
    	return $equipment->getEquipmentCountForCompany($customerID);
    }

    //USER
    public function CheckUserExistence($accessname, $password, $accesslevel_id = 0) {
    	$user = new User($this->db);
    	return $user->isUserExists($accessname, $password, $accesslevel_id);
    }

    public function getUserID($accessname) {
    	$user = new User($this->db);
    	return $user->getUserIDbyAccessname($accessname);
    }

    public function getUserDetails($userID) {
    	$user = new User($this->db);
    	return $user->getUserDetails($userID);
    }

    public function getCompanyLevelUserByCompanyID($companyID) {
    	$user = new User($this->db);
    	$users = $user->getUserListByCompany($companyID);
    	return $users[0];
    }

}
?>