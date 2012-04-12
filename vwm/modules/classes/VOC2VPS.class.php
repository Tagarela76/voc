<?php

class VOC2VPS {
	private $db;

    function VOC2VPS($db = false) {
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
    
    //CONFIGS
    public function loadConfigs() {				
		$VPSUser = new VPSUser($this->db);
		return $VPSUser->loadConfigs();
	 }
    
    //CUSTOMER
    
    public function getCustomerDetails($customerID = null, $getWithNotRegistered = false) {    	
    	$vps2voc = new VPS2VOC($this->db);
    	return $vps2voc->getCustomerDetails($customerID,$getWithNotRegistered); //this is a bad thing((   	
    }
    public function getCustomerLimits($customerID) {
    	$VPSUser = new VPSUser($this->db);
    	return $VPSUser->getCustomerLimits($customerID);
    }
    
    public function setCustomerLimitByID($customerID, $limit) {
    	$VPSUser = new VPSUser($this->db);
    	return $VPSUser->setCustomerLimitByID($customerID, $limit);
    }
}
?>