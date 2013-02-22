<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CVInvoices
 *
 * @author ilya.iz@kttsoft.com
 */
class CVInvoices extends Controller {

    function CVInvoices($smarty,$xnyo,$db,$user,$action) {
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

    private function actionViewList() {

        $customerID = $_SESSION['customerID'];
        $category = $this->getFromRequest('category');
        $this->setBookmarks($category);


        $subCategory = $this->getFromRequest('subCategory');
        $this->smarty->assign("currentBookmark",$subCategory);
        $invoice = new Invoice($this->db);
		
		$customer = new VPSCustomer($this->db, $customerID);
		
		$invoiceManager = new VPSCustomerInvoices($this->db, $customer);		
		
        switch ($subCategory) {
            case "All":
                //$invoiceList = $invoice->getAllInvoicesList($customerID);

				$totalInvoices = $invoiceManager->getAllInvoices();
				$invoiceList = $invoiceManager->printSplObjectStorage($totalInvoices);
                break;
            case "Paid":
				$paidInvoices = $invoiceManager->getPaidInvoices();
                $invoiceList = $invoiceManager->printSplObjectStorage($paidInvoices);
                break;
            case "Due":
				$dueInvoices = $invoiceManager->getDueInvoices();
                $invoiceList = $invoiceManager->printSplObjectStorage($dueInvoices);

                $size = count($invoiceList);
                for($i=0; $i< $size; $i++)
                {
                    if(($customer->balance - $invoiceList[$i]->total) < 0) //Если денег недостаточно для оплаты инвойса - кнопочка неактивная
                    {
                        $List[$i]['enablePayButton'] = "disabled";
                    }
                    else
                    {
                        $List[$i]['enablePayButton'] = "enabled";
                    }
                }

                break;
            case "Canceled":
               	$canceledInvoices = $invoiceManager->getCanceledInvoices();
                $invoiceList = $invoiceManager->printSplObjectStorage($canceledInvoices);
                break;

        }	//subCategory switch ending

        $dt = new DateTime();
        $dateFormat = VOCApp::getInstance()->getDateFormat();

        $count = count($invoiceList);
        for($i=0; $i< $count; $i++) {


			$generationDate = $invoiceList[$i]->generation_date;

			$invoiceList[$i]->generation_date = $generationDate->format($dateFormat);

			$suspensionDate = $invoiceList[$i]->suspension_date;
            $invoiceList[$i]->suspension_date = $suspensionDate->format($dateFormat);
        }


		$i = 0;
		while($invoiceList[$i]){
			$items = $invoiceList[$i]->getItems()->count();
			$invoiceList[$i] =  get_object_vars($invoiceList[$i]);
			if ($subCategory == 'Due'){$invoiceList[$i]['enablePayButton'] = $List[$i]['enablePayButton'];}
			$invoiceList[$i]['items'] = $items;
			$i++;
		}

        $this->smarty->assign("invoiceList",$invoiceList);
        $this->smarty->assign("invoiceListCount",count($invoiceList));

        $this->smarty->assign("userID",$userID);

        $viewListURL = "vps.php?action=viewList&category=invoices";
        $this->smarty->assign("viewListURL",$viewListURL);

        $billing = new Billing($this->db);
        $groupedCurrencies = $billing->getCurrenciesList(true);	//	grouped
        $this->smarty->assign('currencies', $groupedCurrencies);

        $title = $subCategory." invoices";
        $this->smarty->assign("title",$title);

        $this->smarty->assign("category","invoices");
        $this->smarty->display("tpls:vps.tpl");

    }

    private function actionViewDetails(){
		$customerID = $_SESSION['customerID'];
        $this->smarty->assign("currentBookmark","viewDetails");
		$this->setBookmarks($category);
		$customer = new VPSCustomer($this->db, $customerID);
        $invoiceID = $this->getFromRequest('invoiceID');		
		$customerInvoices = new VPSCustomerInvoices($this->db, $customer);
		$allInvoices = $customerInvoices->getAllInvoices();
		
		$invoiceToShow = false;
		foreach ($allInvoices as $invoice) {
			if ($invoice->invoice_id == $invoiceID) {
				$invoiceToShow = $invoice;
				break;
			}			
		}
		
		if (!$invoiceToShow) {
			throw new Exception('404');
		}
$date = $invoiceToShow->generation_date;
$dateFormat = VOCApp::getInstance()->getDateFormat();
$invoiceDetails = get_object_vars($invoiceToShow);

$invoiceDetails['customerDetails'] = get_object_vars($customer);
$invoiceDetails['generation_date'] = $date->format($dateFormat);

$date = $invoiceToShow->period_start_date;	
$invoiceDetails['period_start_date'] = $date->format($dateFormat);

$date = $invoiceToShow->suspension_date;	
$invoiceDetails['suspension_date'] = $date->format($dateFormat);

$date = $invoiceToShow->period_end_date;	
$invoiceDetails['period_end_date'] = $date->format($dateFormat);

$invoiceItem = $invoiceToShow->getItems();
$tmp = $customerInvoices->printSplObjectStorage($invoiceItem);
$i = 0;
while ($tmp[$i]){
	$invoiceDetails['invoice_items'][$i] = get_object_vars($tmp[$i]);
	$invoiceDetails['invoice_items'][$i]['total'] = $invoiceDetails['invoice_items'][$i]['one_time_charge'] + $invoiceDetails['invoice_items'][$i]['amount'];

	$i++;
}
$getbilling = $customer->getBilling();
$currency = $customer->getCurrency();
$currency = get_object_vars($currency);
$invoiceDetails['billing'] = get_object_vars($getbilling);
       /* $invoice = new Invoice($this->db);

        $invoiceDetails1 = $invoice->getInvoiceItemsDetails($invoiceID,$customerID);
	*/
        $this->smarty->assign("invoiceDetails", $invoiceDetails);

        $payment = new Payment($this->db);

        $paymentHistory = $payment->getHistory($invoiceID);
        $this->smarty->assign("paymentHistory", $paymentHistory);

        $successPayment = $this->getFromRequest('successPayment');
        if (isset($successPayment)) {

            if ($successPayment == 1) { //	success paypal payment
                $this->smarty->assign("success","Thank you for using VOC WEB MANAGER. Your payment is successfully accepted.");
            } elseif ($successPayment == 0) {
                $this->smarty->assign("canceled","Payment was canceled by user.");
            }
        }

		$this->smarty->assign("currentCurrency",$currency);
        $title = "Invoice ".$invoiceID;
        $this->smarty->assign("title",$title);
        $category = $this->getFromRequest('category');
        $this->smarty->assign("category", $category);
        $this->smarty->display("tpls:vps.tpl");
        //$currentCurrency = $billing->getCurrencyDetails($invoiceDetails['currency_id']);
        //$this->smarty->assign("currentCurrency", $currentCurrency);		
        //$groupedCurrencies = $billing->getCurrenciesList(true);
        //$this->smarty->assign("groupedCurrencies",$groupedCurrencies);		
    }


    private function actionPayInvoice() {
	
		$invoiceID = $this->getFromRequest('invoiceID');		
        $invoice = new Invoice($this->db);
        // $details = $invoice->getInvoiceDetails(intval($_GET['invoiceID']),$convert_dates = true);
		$details = $invoice->getInvoiceItemsDetails($invoiceID);
		$balance = $details['customerDetails']['balance'];

		if($balance - $details['total'] <= 0) /*Если денег недостаточно для оплаты инвойса - кнопочка неактивная*/
        {
            $this->smarty->assign("enablePayButton",false);
        }
        else
        {
            $this->smarty->assign("enablePayButton",true);
        }

        $this->smarty->assign("invoice",$details);

        $title = "Pay Invoice";
        $this->smarty->assign("title",$title);

        $this->smarty->assign("areYouSureAction","pay for invoice");
        $this->smarty->assign("category","invoices");
        $this->smarty->assign("subCategory",$subCategory);
        $this->smarty->assign("currentBookmark",$_GET["action"]);
        $this->smarty->display("tpls:vps.tpl");
    }

    private function actionConfirmPayInvoice() {
		$customerID = $_SESSION['customerID'];
		$invoiceID = $this->getFromRequest('invoiceID');
        $invoice = new Invoice($this->db);
		$customer = new VPSCustomer($this->db, $customerID);
        //$idata = $invoice->getInvoiceDetails(intval($_GET['invoiceID']));
        //$balance = $invoice->getBalance($idata['customerID']);
		$idata = $invoice->getInvoiceItemsDetails($invoiceID);
		$balance = $customer->balance;		

		if(($balance - $idata['total']) < 0)
        {
            header ("Location: vps.php?action=viewList&category=invoices&subCategory=Due&error=no_enough_money");
        }
        else ///Все хорошо, денег на оплату хватает
        {
            $this->db->beginTransaction();
            $invoice->payInvoiceFromBalance($idata);
            $this->db->commitTransaction();
            header ("Location: vps.php?action=viewList&category=invoices&subCategory=Due");
        }
    }

}

?>
