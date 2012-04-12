<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVScriptNewBilling
 *
 * @author max.ia@kttsoft.com
 */

class CVScriptNewBilling extends Controller {

    function CVScriptNewBilling($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='common';
		$this->parent_category='common';		
	}
	
    public function runAction() {
		$this->runCommon('vps');
		
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}
	
    private function actionScriptNewBilling() {
		$config = $this->loadConfig();
		$interval = new DateInterval('P'.$config['invoice_generation_period'].'D');
		$dt = new DateTime();
		
		$dt->setDate(1983,01,21);	

						$billingManager = new VPSBillingManager($this->db, new VPSCurrency($this->db, 1));
						$billingPlans = $billingManager->getAll();
		
		$customerManager = new VPSCustomerManager($this->db);
		$customerList = $customerManager->getCustomerList();

		$i=0;
		foreach ($customerList as $i=>$icustomer){
			
			$customer = new VPSCustomer($this->db,$icustomer->id);
			
			
			//if ($customer->status == 'on'){
			  if ($customer->id == 129){
				$invoiceManager = new VPSCustomerInvoices($this->db, $customer);

				$totalInvoices = $invoiceManager->getAllInvoices();
				$invoiceList = $invoiceManager->printSplObjectStorage($totalInvoices);		

				$currentInvoice = $invoiceManager->getCurrentInvoice();
				
				$currentinvoiceEndDate = clone $currentInvoice->period_end_date;
			    $currentinvoiceEndDate->sub($interval);
				
				if ($dt == $currentinvoiceEndDate){

						$this->db->beginTransaction();
						$currency = new VPSCurrency($this->db, $customer->currency_id);

						if ($customer->next_billing_id != null){ 
							$selectedBilling = new VPSBilling($this->db, $customer->next_billing_id, $currency);
						}else{
							$selectedBilling = new VPSBilling($this->db, $customer->billing_id, $currency);
						}

						$invoiceItem = new VPSInvoiceItem($this->db);
						$invoiceItem->setup4paidPeriod($selectedBilling);

						$invoice = new VPSInvoice($this->db);
						$invoice->setCustomer($customer);
						$invoice->addItem($invoiceItem);
						$invoice->period_start_date = clone $currentInvoice->period_end_date;
						$invoice->period_start_date->add(new DateInterval("P1D"));
						$invoice->period_end_date = clone $invoice->period_start_date;
						$invoice->period_end_date->add(new DateInterval("P{$selectedBilling->months_count}M"));
						$invoice->suspension_date = clone $invoice->period_start_date;

						$invoice->status = VPSInvoice::STATUS_DUE;
						$invoice->suspension_disable = true;
						$invoice->currency_id = $customer->currency_id;
						$invoice->save();

						$this->db->commitTransaction();

				}				
				$nextInvoice = $invoiceManager->getNextPeriodInvoice();
var_dump($nextInvoice);
				if ($dt >= $currentInvoice->period_end_date){
				
					var_dump($i.'='.$icustomer->id.'status='.$customer->status);
					
					var_dump('$nextInvoice->status == '.$nextInvoice->status);
					if ($nextInvoice->status != 2){
						echo 'UPDATE status';
						$customer->status = 'off';
						$customer->save();
					}else{
						echo 'UPDATE billing';
					
						$customer->billing_id = $customer->next_billing_id;
						$customer->next_billing_id = $customer->next_billing_id;
						$customer->save();						
					}

				}
			}$i++;
		}
    }

    function loadConfig() {
		//$db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_VPS_CONFIG;
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$numRows = $this->db->num_rows();
			for ($i=0; $i < $numRows; $i++) {
				$data=$this->db->fetch($i);
				$config[$data->name] = stripslashes($data->value);
			}
		}

		return $config;
	}
/*
    private function UpdateCustomerBilling($customer_id,$billing_id) {

		$sql = "UPDATE ".TB_VPS_CUSTOMER." SET billing_id= ".$billing_id.", next_billing_id = ".$billing_id." WHERE customer_id = ".$customer_id."";
		return;	
		if (!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to update VPS customer Billing: ".$sql);
		}		
		
	}	
	
    private function UpdateCustomerStatus($customer_id) {

		$sql = "UPDATE ".TB_VPS_CUSTOMER." SET status='off' WHERE customer_id = ".$customer_id."";
		return;
		if (!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to update VPS customer Status: ".$sql);
		}		
		
	}*/	
}
?>