<?php

class CVDashboard extends Controller {

    function CVDashboard($smarty,$xnyo,$db,$user,$action) {
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

    private function actionViewDetails() {
	$customerID = $_SESSION['customerID'];

	$config = $this->loadConfig();

	$customer = new VPSCustomer($this->db, $customerID);
	$invoiceManager = new VPSCustomerInvoices($this->db, $customer);
	

	//	get all possible invoces to customer
	$totalInvoices = $invoiceManager->getAllInvoices();
	$paidInvoices = $invoiceManager->getPaidInvoices();
	$dueInvoices = $invoiceManager->getDueInvoices();
	$currentInvoice = $invoiceManager->getCurrentInvoice();
	$nextPeriodInvoice = $invoiceManager->getNextPeriodInvoice();

	//	get next invoice generation date
	$nextInvoiceDate = ($nextPeriodInvoice === false) ? clone $currentInvoice->period_end_date : $nextPeriodInvoice->period_end_date;
	//	next period start date
	$nextInvoiceDate->add(new DateInterval('P1D'));
	//	minus invoice generation period
	$diff = new DateInterval("P{$config['invoice_generation_period']}D");
        $nextInvoiceDate->sub($diff);

	$nextInvoiceDateFormat = $nextInvoiceDate->format(VOCApp::get_instance()->getDateFormat()); 	

	//	TODO: finish view part
	/*****
	echo "total invoice count - {$totalInvoices->count()}</br>";	 
	echo "paid invoice count - {$paidInvoices->count()}</br>";
	echo "due invoice count - {$dueInvoices->count()}</br>";
	echo "next invocie date - {$nextInvoiceDate->format(VOCApp::get_instance()->getDateFormat())}";
	 *	
	 */

$paidarr = $invoiceManager->printSplObjectStorage($paidInvoices);
$paid['count'] = count($paidarr);
$paid['total'] = $paidarr[0]->total;

$totalarr = $invoiceManager->printSplObjectStorage($totalInvoices);
$getcurrency = new VPSCurrency($this->db, $customer->currency_id);
$currency['sign'] = $getcurrency->sign;

$duearr = $invoiceManager->printSplObjectStorage($dueInvoices);
$due['count'] = count($duearr);

if ($due['count'] > 1){
$i = 0;
while ($duearr[$i]){
	$invoices[$i]['currency_id'] = $duearr[$i]->currency_id;
	$invoices[$i]['total'] = $duearr[$i]->total;
	$i++;	
}
$due['total'] = $this->calculateInvoicesSum($invoices,$getcurrency->iso);
}else{$due['total'] = $duearr[0]->total;}

$balance = $currency['sign'].' '.$customer->balance;

/*        die();
        $customerID = $_SESSION['customerID'];

        $billing = new Billing($this->db);
        $currency = $billing->getCurrencyByCustomer($customerID);

        $invoice = new Invoice($this->db);
        $allInvoices = $invoice->getAllInvoicesList($customerID);

        $curentCurrency = $billing->getCurrencyByCustomer($customerID);


        $paidInvoices = $invoice->getPaidInvoicesList($customerID);
        $paidInvoicesSum = $this->calculateInvoicesSum($paidInvoices,$curentCurrency['iso']);



        $dueInvoices = $invoice->getDueInvoicesList($customerID);


        $dueInvoicesSum = $this->calculateInvoicesSum($dueInvoices,$curentCurrency['iso']);


        $canceledInvoices = $invoice->getCanceledInvoicesList($customerID);
        $canceledInvoicesSum = $this->calculateInvoicesSum($canceledInvoices,$curentCurrency['iso']);

        $balance = $currency['sign'].' '.number_format($invoice->getBalance($customerID),2);

        $all['count'] = count($allInvoices);

        $paid['count'] = count($paidInvoices);
        $paid['total'] = $paidInvoicesSum;//number_format($invoice->totalPaid,2);

        //count total paid without custom invoices


        $due['count'] = count($dueInvoices);
        $due['total'] = $dueInvoicesSum;//number_format($invoice->totalDue,2);


        $canceled['count'] = count($canceledInvoices);
        $canceled['total'] = $canceledInvoicesSum;//number_format($invoice->totalCanceled,2);

        $config = $this->loadConfig();
        $lastInvoice = $invoice->getLastInvoice($customerID);

        $lastInvoiceDate = DateTime::createFromFormat('Y-m-d', $lastInvoice['periodEndDate']);
//		$lastInvoiceDate = new DateTime();
//      $lastInvoiceDate->setTimestamp(intval($lastInvoice['periodEndDate']));

        $diff = new DateInterval("P{$config['invoice_generation_period']}D");
        $lastInvoiceDate->sub($diff);

        $dateFormat = VOCApp::get_instance()->getDateFormat();
        $nextInvoiceDate = $lastInvoiceDate->format($dateFormat);

        //$nextInvoiceDate = date('Y-m-d', strtotime($lastInvoice['periodEndDate']." - ".$config['invoice_generation_period']." days"));
*/

        $this->smarty->assign("currency",$currency);
        $this->smarty->assign("allInvoices",$totalInvoices->count());

        $this->smarty->assign("paidInvoices",$paid);
        $this->smarty->assign("dueInvoices",$due);
        $this->smarty->assign("canceledInvoices",$canceled);
        $this->smarty->assign("balance",$balance);
        $this->smarty->assign("nextInvoiceDate",$nextInvoiceDateFormat);
        $this->smarty->assign("currentCurrency",$currency);

        $viewListURL = "vps.php?action=viewList&category=invoices";
        $this->smarty->assign("viewListURL",$viewListURL);

        $title = "Dashboard";
        //setTitle($title, $smarty);
        $this->smarty->assign("title", $title);

        $this->smarty->assign("category","dashboard");
        $this->smarty->display("tpls:vps.tpl");
    }

    private function calculateInvoicesSum($invoices,$currencyISO){
		
		if(empty($invoices) or !is_array($invoices)){
			return 0;
		}
		global $db;

		$billing = new Billing($db);
		$currencies = $billing->getCurrenciesList(true);



		$invoice2currency = array();

		foreach( $invoices as $i ) {

			$iso = $currencies[$i['currency_id']];
			$invoice2currency[$iso['iso']] += $i['total'] < 0 ? $i['total'] * -1 : $i['total']; //If total < 0 - make it positive

		}

		 return $invoice2currency[$currencyISO];//NEXT code does not work :)
		 
		try
		{
			$currencyConvertor = new CurrencyConvertor();
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
			//global $smarty;
			//$smarty->assign("message",$msg);
			//$smarty->display('tpls:errors/other.tpl');
			return "[error]";
		}

		$sum = $currencyConvertor->Sum($invoice2currency,$currencyISO);
		$sum = round($sum,2);

		if(!$sum or $sum == 0)
		{
			$sum = 0;
		}
		//echo "$currencyISO = $sum";
		return $sum;
		//$billing->getCurrencyDetails($currencyID)
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
}
?>
