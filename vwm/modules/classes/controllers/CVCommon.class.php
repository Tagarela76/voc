<?php

class CVCommon extends Controller {

    function CVCommon($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionLogout() {
		$this->user->logout();
	} 
    
    private function actionMain() {
         echo "By default. By default never going here.";
    }
    
    public function showMyInfo($userData) {
		$vps2voc = new VPS2VOC($this->db);
		   //$bridge->CopyAllCustomersToBridge();
		   //$bridge->CopyAllUsersToBridge();		 
		$customerDetails = $vps2voc->getCustomerDetails($userData['company_id'],true);
		
		$this->smarty->assign("companyName",$customerDetails['name']);				
		
		//getting state list ////////need to add to Billing or smth else getStateList() and getCountryList() functions and add to db states and countries  
		$state = new State($this->db);
		$stateList = $state->getStateList();
		$this->smarty->assign("states",$stateList);
		
		//getting country lists
		$country = new Country($this->db);
		$countryList = $country->getCountryList();
		$this->smarty->assign("countries",$countryList);

		if ($userData["showAddUser"]) {
			$this->smarty->assign("action","addUser");	
		} else {
			$this->smarty->assign("action","editCategory");	
		}
		
		$billing = new Billing($this->db);		
		$currenciesList = $billing->getCurrenciesList();
		$this->smarty->assign("currenciesList",$currenciesList);
		if (isset($customerDetails['currency_id'])) {
			$userData['currency_id'] = $customerDetails['currency_id'];
		}		
				
		$title = "My info";
		$this->smarty->assign("title",$title);	
		$this->smarty->assign("action","vps.php?category=myInfo&action=first");
		$this->smarty->assign("userData",$userData);
		$this->smarty->assign("category","myInfo");
		$this->smarty->display("tpls:vps.tpl");	
	}
    
    private function actionAlterVPSTablesToTimestamp(){
        $this->actionAlterCompanyTrialEndDateToTimestamp();
        $this->actionAlterCompanyTrialEndDateToTimestamp();
        $this->actionAlterTablePaymentSetTimestamp();
        $this->actionAlterTableModule2customer();
        $this->actionAlterTableDeactivation();
        $this->actionAlterTableDefined_bp_request();
    }
    
    private function actionAlterCompanyTrialEndDateToTimestamp() {
        
        $this->db->query("select company_id, trial_end_date from ". TB_COMPANY);
        $this->changeTableColumnToTimestamp(TB_COMPANY, array("trial_end_date"), "company_id");
        echo TB_COMPANY . " DONE <br/>";
    }
    
    private function actionAlterTableInvoiceToTimestamp() {
        $this->changeTableColumnToTimestamp(TB_VPS_INVOICE, array("generation_date", "suspension_date", "period_start_date", "period_end_date"), "invoice_id");
        echo TB_VPS_INVOICE . " DONE <br/>";
    }
    
    private function actionAlterTablePaymentSetTimestamp() {
        //$this->db->query("select payment_id, payment_date from ". TB_VPS_PAYMENT);
        $this->changeTableColumnToTimestamp(TB_VPS_PAYMENT, array("payment_date"), "payment_id");
        echo TB_VPS_PAYMENT . " DONE <br/>";
    }
    
    private function actionAlterTableModule2customer() {
        $this->changeTableColumnToTimestamp(TB_VPS_MODULE2CUSTOMER, array("start_date"), "id");
        echo TB_VPS_MODULE2CUSTOMER . " DONE <br/>";
    }
    
    private function actionAlterTableDeactivation(){
        //vps_deactivation
        $this->changeTableColumnToTimestamp(TB_VPS_DEACTIVATION, array("period_end_date","date"), "id");
        echo TB_VPS_DEACTIVATION . " DONE <br/>";
    }
    
    private function actionAlterTableDefined_bp_request() {
        $this->changeTableColumnToTimestamp(TB_VPS_DEFINED_BP_REQUEST, array("date"), "id");
        echo TB_VPS_DEFINED_BP_REQUEST . " DONE <br/>";
    }
    
    /**
     *
     * @param type $tablename
     * @param type $columns array of columns (string) or string
     */
    private function changeTableColumnToTimestamp($tablename,array $columns, $column_id_name) {
        
        if(!is_array($columns)) {
            throw new Exception("columns is not array! Data:".var_dump($columns));
        }
        
        if(empty($column_id_name)) {
            throw new Exception("Incorrect column_id_name: $column_id_name");
        }
        
        if(empty($tablename)) {
            throw new Exception("Incorrect tablename: $tablename");
        }
        
        //If columns already unsigned int, throw exception
        $this->checkTableColumnsException( $columns, $tablename, "int(10) unsigned");
        
        //Build select query
        $q = "SELECT $column_id_name, ";
        
        foreach($columns as $col) {
            $q .= $col . ",";
        }
        $q = substr_replace($q, "", strlen($q)-1, 1);
        
        $q .= " FROM $tablename";
        
        $this->db->query($q);
        
        if(mysql_error()) {
            $err = mysql_error();
            $this->db->rollbackTransaction();
            throw new Exception($err . " by query: <b>$q</b>");
            return false;
        }
        
        $rows = $this->db->fetch_all_array();
        
        $count = count($rows);
        
        //Change datetime to timestamp in array
        for($i=0; $i<$count; $i++) {
            
            foreach($columns as $col){
                $rows[$i][$col] = strtotime($rows[$i][$col]);
                $rows[$i][$col] = $rows[$i][$col] ? $rows[$i][$col] : "NULL";
            }
        }
        
        
        $this->db->beginTransaction();
        
        //Drop target columns and create exacly in timestamp
        foreach($columns as $col) {
            $this->db->query("alter table ".$tablename." drop column `$col`");
            $this->db->query("alter table ".$tablename." add column `$col` int(10) UNSIGNED");
        }
        
        for($i=0; $i<$count; $i++) {
            
            //Build update query
            $uq = "UPDATE ".$tablename. " SET ";
            foreach($columns as $col) {
                $uq .= " {$col} =  {$rows[$i][$col]},";
            }
            $uq = substr_replace($uq, "", strlen($uq)-1, 1);
            $uq .= " WHERE $column_id_name = {$rows[$i][$column_id_name]}";
            
            
            
            $this->db->query($uq);
            if(mysql_error()) {
                $err = mysql_error();
                $this->db->rollbackTransaction();
                throw new Exception($err . " by query: <b>$uq</b>");
                return false;
            }
        }
        
        $this->db->commitTransaction();
        return true;
    }
    
    private function checkTableColumnsException(array $columns, $tablename ,$exceptionColumnType) {
        
        $this->db->query("SHOW FULL COLUMNS FROM $tablename");
        $cols = $this->db->fetch_all_array();
        foreach($cols as $c) {
            foreach($columns as $column){
                if($c['Type'] == $exceptionColumnType and $column == $c['Field']) {
                    throw new Exception("Column {$c['Field']} is already $exceptionColumnType");
                }
            }
            
        }
    }
}
?>
