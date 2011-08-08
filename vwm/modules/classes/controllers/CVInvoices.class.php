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
        switch ($subCategory) {
            case "All":
                $invoiceList = $invoice->getAllInvoicesList($customerID);
                break;
            case "Paid":
                $invoiceList = $invoice->getPaidInvoicesList($customerID);
                break;
            case "Due":

                $invoiceList = $invoice->getDueInvoicesList($customerID);

                $size = count($invoiceList);
                for($i=0; $i< $size; $i++)
                {
                    if(($invoiceList[$i]['customer_balance'] - $invoiceList[$i]['total']) < 0) //Если денег недостаточно для оплаты инвойса - кнопочка неактивная
                    {
                        $invoiceList[$i]['enablePayButton'] = "disabled";
                    }
                    else
                    {
                        $invoiceList[$i]['enablePayButton'] = "enabled";
                    }
                }

                break;
            case "Canceled":
                $invoiceList = $invoice->getCanceledInvoicesList($customerID);
                break;

        }	//subCategory switch ending

        $dt = new DateTime();
        $dateFormat = VOCApp::get_instance()->getDateFormat();
        $count = count($invoiceList);
        for($i=0; $i< $count; $i++) {

            //$dt->setTimestamp(intval($invoiceList[$i]['generationDate']));
            $generationDate = DateTime::createFromFormat('Y-m-d', $invoiceList[$i]['generationDate']);
            $invoiceList[$i]['generationDate'] = $generationDate->format($dateFormat);

            //$dt->setTimestamp(intval($invoiceList[$i]['suspensionDate']));
            $suspensionDate = DateTime::createFromFormat('Y-m-d', $invoiceList[$i]['suspensionDate']);
            $invoiceList[$i]['suspensionDate'] = $suspensionDate->format($dateFormat);
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

        $this->smarty->assign("currentBookmark","viewDetails");


        $invoiceID = $this->getFromRequest('invoiceID');

        $invoice = new Invoice($this->db);

        /**
         * DEPRECATED возвращает старый инвойс
        //$invoiceDetails = $invoice->getInvoiceDetails($invoiceID);
         */

        $invoiceDetails = $invoice->getInvoiceItemsDetails($invoiceID);

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

        $billing = new Billing($this->db);
        $currentCurrency = $billing->getCurrencyDetails($invoiceDetails['currency_id']);
        $this->smarty->assign("currentCurrency", $currentCurrency);

        $title = "Invoice ".$invoiceID;
        $this->smarty->assign("title",$title);

        $category = $this->getFromRequest('category');
        $this->smarty->assign("category", $category);

        $this->setBookmarks($category);



        $customerID = $_SESSION['customerID'];

        $currency = $billing->getCurrencyByCustomer($customerID);
        $groupedCurrencies = $billing->getCurrenciesList(true);
        $this->smarty->assign("currentCurrency",$currency);
        $this->smarty->assign("groupedCurrencies",$groupedCurrencies);

        $this->smarty->display("tpls:vps.tpl");
    }


    private function actionPayInvoice() {
        $invoice = new Invoice($this->db);
        $details = $invoice->getInvoiceDetails(intval($_GET['invoiceID']),$convert_dates = true);

        $balance = $invoice->getBalance(intval($details['customerID']));

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

        $invoice = new Invoice($this->db);
        $idata = $invoice->getInvoiceDetails(intval($_GET['invoiceID']));
        $balance = $invoice->getBalance($idata['customerID']);


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
