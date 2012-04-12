<?php
class CCompany extends Controller
{
	function CCompany($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='company';
		$this->parent_category='root';
	}

	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	private function actionConfirmDelete()
	{
		$company=new Company($this->db);

		//Set Title
//		$title=new Titles($smarty);
//		$title->titleCategoryList($overCategoryType);

		foreach ($this->itemID as $ID)
		{
			$companyDetails=$company->getCompanyDetails($ID);
			$itemForDeleteName[]=$companyDetails["name"];

			//	setter injection
			$company->setTrashRecord(new Trash($this->db));
			$company->deleteCompany($ID);
		}
		//post redirect
		//$returnURL = "?action=browseCategory&categoryID=company";
		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=root&notify=4");
	}

	private function actionViewDetails()
	{
		if (!$this->user->checkAccess($this->getFromRequest('category'), $this->getFromRequest('id'))) {
			throw new Exception('deny');
		}
		$this->setListCategoriesLeftNew($this->getFromRequest('category'), $this->getFromRequest('id'),$this->paramsForListLeft);
		$this->setNavigationUpNew ($this->getFromRequest('category'), $this->getFromRequest('id'));
		$this->setPermissionsNew('viewCompany');

		$companies = new Company($this->db);
		$companyDetails = $companies->getCompanyDetails($this->getFromRequest("id"));
		$this->smarty->assign("company", $companyDetails);

		// Get VOC unittype description
		$unittype = new Unittype($this->db);
		$unitTypeDetails = $unittype->getUnittypeDetails($companyDetails['voc_unittype_id']);
		$this->smarty->assign('voc_unittype_desc', $unitTypeDetails['description']);
		$this->smarty->assign('backUrl','?action=browseCategory&category=company&id='.$this->getFromRequest('id'));
		$this->smarty->assign('tpl', 'tpls/viewCompany.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionDeleteItem()
	{
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);

		$company = new Company($this->db);
		if (!is_null($this->getFromRequest('id'))) {
		foreach ($req_id as $companyID)
			{
			//	Access control
				if (!$this->user->checkAccess($this->getFromRequest('category'), $companyID))
				{
					throw new Exception('deny');
				}

				$companyDetails = $company->getCompanyDetails($companyID);

				if ($companyDetails['company_id'] == null) {
					throw new Exception('404');
				}

				$delete["id"]		=	$companyDetails["company_id"];
				$delete["name"]		=	$companyDetails["name"];
				$delete["address"]	=	$companyDetails["address"];
				$delete["phone"]	=	$companyDetails["phone"];
				$delete["contact"]	=	$companyDetails["contact"];
				$itemForDelete[] 	=	$delete;
			}
		}
		$this->setListCategoriesLeftNew('root', '');
		$this->setNavigationUpNew ('root', '');
		$this->setPermissionsNew('viewCompany');
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=root");
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}

	private function actionBrowseCategory()
	{
		//this user already read terms and conditions
		if (!empty($_POST['agree'])){
			$query = "UPDATE ".TB_USER." SET terms_conditions = 1 WHERE user_id = ".$_SESSION['user_id'];
			$this->db->query($query);
		}
		
		$bookmark=$this->getFromRequest('bookmark');
		if ($bookmark !== null) {
			$this->forward($bookmark,'bookmarkC'.ucfirst($bookmark));
			$this->smarty->display("tpls:index.tpl");
			return false;
		}

		$facilities = new Facility($this->db);
		$facilityList = $facilities->getFacilityListByCompany($this->getFromRequest('id'));

		//Set Title
		$company = new Company($this->db);
		$companyDetails = $company->getCompanyDetails($this->getFromRequest('id'));

		if (is_null($companyDetails['company_id'])) {
			throw new Exception('404');
		}

		$usages4indicator = array();
		for ($i=0; $i<count($facilityList); $i++) {
			$url="?action=browseCategory&category=facility&id=".$facilityList[$i]['id']."&bookmark=department";
			$facilityList[$i]['url']=$url;

			$facility = new Facility($this->db);
			$facility->initializeByID($facilityList[$i]["id"]);

			if ($facility->isOverLimit()) {
				$facilityList[$i]["valid"] = "invalid";
			} else {
				$facilityList[$i]["valid"] = "valid";
			}

			//	set gauge foreach facility
			$currentUsage = $facility->getCurrentUsage();
			$limit = $facility->getMonthlyLimit();
			$pxCount = round(200 * $currentUsage / $limit);
			if ($pxCount > 200) {
				$pxCount = 200;
			}
			$facilityList[$i]['gauge'] = array (
				'currentUsage'	=>round($currentUsage, 2),
				'vocLimit'		=>$limit,
				'pxCount'		=> $pxCount
			);
		}
		$this->smarty->assign('childCategoryItems', $facilityList);
		$this->smarty->assign('childCategory', 'facility');

		//Set Payment Notify
		/*$checkResult = $this->checkPaymentNotify($this->getFromRequest('id'), $this->db);

		if ($checkResult['shouldPay']) {
			$notify = new Notify($this->smarty);
			$this->smarty->assign('notify', $notify->paymentNotify($checkResult['daysLeft']));
		}*/



		//	set js
		$jsSources = array('modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);

		$text = "The payment period is coming to end in -2 days. Please, go to VOC Payment System pay for the next period.";
		/*$notify = array("text" => "Test notify!",
							"params" => array(
									"color" => "White",
									"backgroundColor" => "Red"
		));*/

		//$notify = new Notify(null,$this->db);
		//$notify = $notify->getPopUpNotifyMessage(null,array("backgroundColor"=>"Yellow", "fontSize" => "14px"),"The payment period is coming to end in -2 days. Please, go to VOC Payment System pay for the next period.");
		//$this->smarty->assign("notify",$notify);
		//	set tpl
		$this->smarty->assign('tpl','tpls/company.tpl');
		$this->smarty->display("tpls:index.tpl");

	}

	private function actionAddItem() {
		//	Access control
		if (!$this->user->checkAccess('root', null)) {
			throw new Exception('deny');
		}

		// protecting from xss
		foreach ($_POST as $key=>$value)
		{
			switch ($key)
			{
				case "unitTypeID": break;
				case "APMethodID": break;
				default:
				{
					$_POST[$key]=Reform::HtmlEncode($value);
					break;
				}
			}
		}


		$form = $_POST;

		if (count($form) > 0) {
			
			//	"Init state" dances
			$registration = new Registration($this->db);
			if($registration->isOwnState($form["country"]))
			{
				$state = $form["selectState"];
			}
			else
			{
				$state = $form["textState"];
			}

			$companyData = array(
				"name"				=>	$form["name"],
				"address"			=>	$form["address"],
				"city"				=>	$form["city"],
				"zip"				=>	$form["zip"],
				"county"			=>	$form["county"],
				"state"				=>	$state,
				"country"			=>	$form["country"],
				"phone"				=>	$form["phone"],
				"fax"				=>	$form["fax"],
				"email"				=>	$form["email"],
				"contact"			=>	$form["contact"],
				"title"				=>	$form["title"],
				"creater_id"		=>	$_SESSION['user_id'],
				"voc_unittype_id"	=>  $form["selectVocUnitType"]
			);

			$validation = new Validation($this->db);
			$validStatus = $validation->validateRegData($companyData);
			//	check for duplicate names
			if (!($validation->isUniqueName("company", $companyData["name"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}

			if ($validStatus['summary'] == 'true') {
				$companies = new Company($this->db);
				//	setter injection
				$companies->setTrashRecord(new Trash($this->db));
				
				$companyID = $companies->addNewCompany($companyData);

				if (isset($companyID)) {
					$unittype = new Unittype($this->db);
					$unittype->setDefaultUnitTypelist($form['unitTypeID'], $this->category, $companyID);
					$apmethod = new Apmethod($this->db);
					$apmethod->setDefaultAPMethodlist($form['APMethodID'], $this->category, $companyID);
				}


				//	redirect
				header("Location: ?action=browseCategory&category=root&notify=3");
				die();

			} else {
				//	Errors on validation of adding for a new company
				/* old school style */
				//$notify = new Notify($this->smarty);
				//$notify->formErrors();

				/*	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);

				$this->smarty->assign('validStatus', $validStatus);

				$this->smarty->assign('defaultUnitTypelist', $form['unitTypeID']);
				$this->smarty->assign('defaultAPMethodlist', $form['APMethodID']);
			}
		}

		//	IF ERRORS OR NO POST REQUEST

		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		$country = new Country($this->db);

		switch (REGION)
		{
			case 'eu_uk':
			{
				$usaID = $country->getCountryIDByName('United Kingdom');
				break;
			}
			default:
			{
				$usaID = $country->getCountryIDByName('USA');
				break;
			}
		}


		$companyData['country'] = $companyData['country'] ? $companyData['country'] : $usaID;
		//$this->smarty->assign("data", $data);
		$this->smarty->assign('data', $companyData);
		$state = new State($this->db);
		$stateList = $state->getStateList($companyData['country']);
		if(empty($stateList)){
			$this->smarty->assign("selectMode", false);
		}
		else{
			$this->smarty->assign("state", $stateList);
			$this->smarty->assign("selectMode", true);
		}

		/*if (!isset($companyData)) {
			$data['country'] = $usaID;

			$this->smarty->assign("data", $data);
			$state = new State($this->db);
			$stateList = $state->getStateList($usaID);

			if(empty($stateList)){
			$this->smarty->assign("selectMode", false);
			}
		} else {
			if ($companyData['country'] == $usaID) {
				$selectMode = true;
				$state = new State($this->db);
				$stateList = $state->getStateList($usaID);
				$this->smarty->assign("state", $stateList);
				$this->smarty->assign("selectMode", true);
			}
		}	*/

		//							$state = new State($db);
		//							$stateList = $state->getStateList($usaID);
		//							$smarty->assign("selectMode", true);
		$this->smarty->assign("country", $countries);
		$this->smarty->assign("state", $stateList);

		//	set permissions

		$this->setListCategoriesLeftNew($this->category, $this->getFromRequest('id'));
		$this->setNavigationUpNew ('root', $this->getFromRequest('id'));
		$this->setPermissionsNew('viewRoot');


		//	Get UnitType list
		$unitType = new Unittype($this->db);
		$unitTypelist = $unitType->getClassesOfUnits();
		$classlist = $unitType->getAllClassesOfUnitTypes();

		//Get APMethods list
		$apmethodObject = new Apmethod($this->db);
		$APMethodList=$apmethodObject->getApmethodList();

		// Get VOC unittype list
		foreach ($unitTypelist as $ut) {
			if ($ut['type_id'] == 2) {
				$vocUnitTypeList[] = $ut;
			}
		}
		$this->smarty->assign('vocUnitTypeList', $vocUnitTypeList);

		//	set js scripts
		$jsSources = array(
			'modules/js/form.js',
			'modules/js/reg_country_state.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/addCompanyPopups.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('APMethodList',$APMethodList);
		$this->smarty->assign('unitTypelist', $unitTypelist);
		$this->smarty->assign('classlist', $classlist);

		$this->smarty->assign('sendFormAction', '?action=addItem&category='.$this->category);
		$this->smarty->assign('tpl','tpls/addCompany.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit() {
		if (!$this->user->checkAccess($this->category, $this->getFromRequest('id'))) {
			throw new Exception('deny');
		}

		// protecting from xss
		foreach ($_POST as $key=>$value)
		{
			switch ($key)
			{
				case "unitTypeID": break;
				case "APMethodID": break;
				default:
				{
					$_POST[$key]=Reform::HtmlEncode($value);
					break;
				}
			}
		}

		$form = $_POST;

		if (count($form) > 0) {
			//	"Init state" dances
			$registration = new Registration($this->db);
			$state = ($registration->isOwnState($form["country"])) ? $form["selectState"] : $form["textState"];

			$companyData = array(
				"company_id"		=>	$this->getFromRequest('id'),
				"name"				=>	$form["name"],
				"address"			=>	$form["address"],
				"city"				=>	$form["city"],
				"zip"				=>	$form["zip"],
				"county"			=>	$form["county"],
				"state"				=>	$state,
				"country"			=>	$form["country"],
				"phone"				=>	$form["phone"],
				"fax"				=>	$form["fax"],
				"email"				=>	$form["email"],
				"contact"			=>	$form["contact"],
				"title"				=>	$form["title"],
				"creater_id"		=>	0,
				"voc_unittype_id" 	=>  $form["selectVocUnitType"]
			);

			$validation = new Validation($this->db);
			$validStatus = $validation->validateRegData($companyData);

			if ($validStatus['summary'] == 'true') {
				$companies = new Company($this->db);

				//	setter injection
				$companies->setTrashRecord(new Trash($this->db));
				$companies->setCompanyDetails($companyData);

				$unittype = new Unittype($this->db);
				$unittype->setDefaultUnitTypelist($form['unitTypeID'], $this->category, $this->getFromRequest('id'));
				$apmethod = new Apmethod($this->db);
				$apmethod->setDefaultAPMethodlist($form['APMethodID'], $this->category, $this->getFromRequest('id'));


				//	redirect
				header("Location: ?action=browseCategory&category=".$this->category."&id=".$this->getFromRequest('id') . "&notify=5");
				die();

			} else {
				//	Errors on validation of adding for a new company

				/* old school style */
				//$notify = new Notify($this->smarty);
				//$notify->formErrors();

				/*	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);

				$this->smarty->assign('validStatus', $validStatus);
				$this->smarty->assign('data', $companyData);
				$defaultUnitTypelist = $form['unitTypeID'];
				$defaultAPMethodList = $form['APMethodID'];
			}
		}

		//	IF ERRORS OR NO POST REQUEST
		if (!isset($companyData)) {
			$company = new Company($this->db);
			$companyDetails = $company->getCompanyDetails($this->getFromRequest('id'), true);
			$this->smarty->assign('data', $companyDetails);
		} else {
			$companyDetails = $companyData;
		}

		$country = new Country($this->db);
		$registration = new Registration($this->db);
		$this->smarty->assign("country", $registration->getCountryList());

		$usaID = $country->getCountryIDByName('USA');

		/*switch (REGION)
		{
			case 'eu_uk':
			{
				$usaID = $country->getCountryIDByName('United Kingdom');
				break;
			}
			default:
			{
				$usaID = $country->getCountryIDByName('USA');
				break;
			}
		}*/

		$selectMode = false;
		if ($companyDetails['country'] === $usaID) {
			$selectMode = true;
			$state = new State($this->db);
			$stateList = $state->getStateList($usaID);
			$this->smarty->assign("state", $stateList);
		}
		//var_dump($selectMode);
		//var_dump($companyDetails);
		$this->smarty->assign("selectMode", $selectMode);

		$this->setNavigationUpNew($this->category, $this->getFromRequest('id'));
		$this->setListCategoriesLeftNew($this->category, $this->getFromRequest('id'));
		$this->setPermissionsNew('viewCompany');

		//	Get UnitType list
		$unitType = new Unittype($this->db);
		$unitTypelist = $unitType->getClassesOfUnits();
		$classlist = $unitType->getAllClassesOfUnitTypes();
		if (!isset($defaultUnitTypelist)) {
			$defaultUnitTypelist = $unitType->getDefaultUnitTypelist($companyDetails['company_id']);
		}

		//Get APMethods list
		$apmethodObject = new Apmethod($this->db);
		$APMethodList=$apmethodObject->getApmethodList();
		if (!isset($defaultAPMethodList)) {
			$defaultAPMethodList=$apmethodObject->getDefaultApmethodlist($companyDetails['company_id']);
		}

		// Get VOC unittype list
		foreach ($unitTypelist as $ut) {
			if ($ut['type_id'] == 2) {
				$vocUnitTypeList[] = $ut;
			}
		}
		$this->smarty->assign('vocUnitTypeList', $vocUnitTypeList);

		//	set js scripts
		$jsSources = array(
			'modules/js/form.js',
			'modules/js/reg_country_state.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/addCompanyPopups.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('unitTypelist', $unitTypelist);
		$this->smarty->assign('APMethodList',$APMethodList);
		$this->smarty->assign('classlist', $classlist);
		$this->smarty->assign('defaultUnitTypelist', $defaultUnitTypelist);
		$this->smarty->assign('defaultAPMethodlist',$defaultAPMethodList);
		$this->smarty->assign('categoryName', $this->category);
		$this->smarty->assign('companyID', $companyDetails['company_id']);

		$this->smarty->assign('sendFormAction', '?action=edit&category='.$this->category.'&id='.$this->getFromRequest('id'));
		$this->smarty->assign('tpl', 'tpls/addCompany.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function checkPaymentNotify($customerID, $db) {
		$shouldPay = false;
		$daysLeft = null;

		$currentDate = date('Y-m-d');

		$voc2vps = new VOC2VPS($db);
		$configs = $voc2vps->loadConfigs();
		$vpsRegistrationPeriod = intval($configs['vps_registration_period']);

        //var_dump($vpsRegistrationPeriod);

		$vps_customer = $voc2vps->getCustomerDetails($customerID,true);

        //var_dump($vps_customer);

        $trial_end_date = new DateTime();
        $trial_end_date->setTimestamp($vps_customer['trial_end_date']);

        $diff = $trial_end_date->diff(new DateTime());

        //Trial period ended
        if($diff->invert == false){
            //var_dump($diff);
        }else{ //Trial period

        }

		$timeStampTrialDaysLeft = $vps_customer['trial_end_date'] - strtotime();

        //var_dump($timeStampTrialDaysLeft);

		if ( $timeStampTrialDaysLeft < $vpsRegistrationPeriod*24*60*60 && $timeStampTrialDaysLeft > 0 && $vps_customer['status'] == "notReg") {
            //echo "_____";
			$shouldPay = true;
			$daysLeft = $diff->format("%d"); //round($timeStampTrialDaysLeft/60/60/24);
		} else {

			//get min of daysLeft from DueInvoices
			$daysLeft = $vps_customer['deadline_counter'];
			if (trim($daysLeft) != "NULL" && $daysLeft < $vpsRegistrationPeriod) {
				$shouldPay = true;
				$daysLeft = (int)$daysLeft;
			}
		}
		$result = array (
			'shouldPay'	=> $shouldPay,
			'daysLeft'	=> $daysLeft
		);

		return $result;
	}

    public function to_seconds(DateInterval $di)
      {

        return ($di->y * 365 * 24 * 60 * 60) +
               ($di->m * 30 * 24 * 60 * 60) +
               ($di->d * 24 * 60 * 60) +
               ($di->h * 60 *60) +
               $di->s;
      }
}
?>