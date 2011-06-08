<?php

class Payment {
	
	private $db;

	public $currentDate;

    function Payment($db) {
    	$this->db = $db;
    	$this->currentDate = date("Y-m-d H:i:s");
    }
    
    
    
	public function getHistory($invoiceID) {		
		$history = array();		
		
		//first action is always invoice generation
		$invoice = new Invoice($this->db);
		$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
		
		$invoiceGeneration = array (
			'date'			=> $invoiceDetails['generationDate'],
			'description'	=> "Generated Invoice",
			'invoiceID'		=> $invoiceDetails['invoiceID'],
			'due'			=> $invoiceDetails['total'],
			'paid'			=> "0.00",
			'balance'		=> $invoiceDetails['balance'],
			'status'		=> "--",
			'sign'			=> $invoiceDetails['sign']
		);
		$history[] = $invoiceGeneration;
								
		//now payments actions
    	$query = "SELECT * " .
    			 "FROM ".TB_VPS_PAYMENT." " .
    			 "WHERE invoice_id = ".$invoiceID." " .
    			 "ORDER BY payment_date";
    			 
    	$this->db->query($query);
    	
    	if ($this->db->num_rows() > 0) {
    		$rows = $this->db->fetch_all();
    		foreach ($rows as $row) {
    			$description = $row->txn_id;
				$payment = array (
					'date'				=> $row->payment_date,
					'description'		=> $description,
					'invoiceID'			=> $row->invoice_id,
					'due'				=> $row->due,
					'paid'				=> $row->paid,
					'balance'			=> $row->balance,
					'status'			=> $row->status						
				);
				$history[] = $payment;
    		}	
    	}    	
				    	
    	return $history;
	}   
	
	
	
	public function createPayment($invoiceID, $userID, $txnID, $status, $paid = null, $paymentMethodID = "NULL") {	//$paymentMethodID = 1 - PayPal
		
		$invoice = new Invoice($this->db);
		$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
		
		$paid = ($paid === null) ? $invoiceDetails['total'] : $paid;	//if customer pay entire amount
		$due = $invoiceDetails['total'] - $paid;
		$balance = $invoice->getBalance($invoiceDetails['customerID']);
		
		$paymentData = array(
			"invoiceID"			=> $invoiceDetails['invoiceID'],
			"userID"			=> $userID,
			"txnID"				=> $txnID,
			"paid"				=> $paid,	//if customer pay entire amount
			"due"				=> $due,
			"balance"			=> $balance,
			"paymentDate"		=> $this->currentDate,
			"status"			=> $status,
			"paymentMethodID"	=> $paymentMethodID
		);		
		
		$this->insertPayment($paymentData);
    	
    	return $paid;
	}
	
	public function cancelInvoicePayment ($invoiceID, $userID, $type='canceled') {
		$invoice = new Invoice($this->db);
		$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
				
		$balance = $invoice->getBalance($invoiceDetails['customerID']);
		
		$paymentData = array(
			"invoiceID"			=> $invoiceDetails['invoiceID'],
			"userID"			=> $userID,
			"txnID"				=> ucfirst($type),
			"paid"				=> $invoiceDetails['paid'],	//if customer pay entire amount
			"due"				=> $invoiceDetails['due'],
			"balance"			=> $balance,
			"paymentDate"		=> mktime(),
			"status"			=> "Completed",
			"paymentMethodID"	=> "NULL"	
		);				
		
		$this->insertPayment($paymentData);
	}

	
	
	public function restoreInvoicePayment ($invoiceID, $userID) {
		$invoice = new Invoice($this->db);
		$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
				
		$balance = $invoice->getBalance($invoiceDetails['customerID']);
		
		$paymentData = array(
			"invoiceID"			=> $invoiceDetails['invoiceID'],
			"userID"			=> $userID,
			"txnID"				=> "Restored",
			"paid"				=> $invoiceDetails['paid'],	//if customer pay entire amount
			"due"				=> $invoiceDetails['due'],
			"balance"			=> $balance,
			"paymentDate"		=> $this->currentDate,
			"status"			=> "Completed",
			"paymentMethodID"	=> "NULL"	
		);				
		
		$this->insertPayment($paymentData);
	} 
	
	
	public function clonePayments($fromInvoiceID, $toInvoiceID) {
		$query = "INSERT INTO ".TB_VPS_PAYMENT." (invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) " .
				"(SELECT ".$toInvoiceID.", user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id " .
					"FROM ".TB_VPS_PAYMENT." " .
					"WHERE invoice_id = ".$fromInvoiceID." )";
		$this->db->query($query);		
	}
    
    
    
    
    public function getLastPayment($invoiceID) {
    	//$this->db->select_db(DB_NAME);
    	$query = "SELECT * FROM ".TB_VPS_PAYMENT." WHERE invoice_id = ".$invoiceID." AND status = 'Completed' ORDER BY payment_date DESC LIMIT 1";
    	$this->db->query($query); 
    	
    	if ($this->db->num_rows()) {
    		$data = $this->db->fetch(0);
    		$payment = array (
    			'date'				=> $data->payment_date,				
				'invoiceID'			=> $data->invoice_id,
				'userID'			=> $data->user_id,
				'description'		=> $data->txn_id,
				'due'				=> $data->due,
				'paid'				=> $data->paid,
				'balance'			=> $data->balance,
				'status'			=> $data->status,
				'paymentMethodID'	=> $data->payment_method_id
    		);
    		return $payment;
    	} else {
    		return false;
    	}
    }
    
    
    
        
    private function insertPayment($paymentData) {
    	//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO ".TB_VPS_PAYMENT." (invoice_id, user_id, txn_id, paid, due, balance, payment_date, status, payment_method_id) VALUES ( " .
    			 "".$paymentData['invoiceID'].", " .
    			 "".$paymentData['userID'].", " .
    			 "'".$paymentData['txnID']."', " .
    			 "'".$paymentData['paid']."', " .
    			 "'".$paymentData['due']."', " .
    			 "'".$paymentData['balance']."', " .    			 
    			 "".$paymentData['paymentDate'].", " .
    			 "'".$paymentData['status']."', " .
    			 "".$paymentData['paymentMethodID'].")";
    			     	
    	$this->db->query($query);    	
    }
}
?>