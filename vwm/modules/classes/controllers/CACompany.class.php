<?php

class CACompany extends Controller {

	function __construct($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='company';
		$this->parent_category='root';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}


	private function actionBrowseCategory() {
        
		$company = new Company($this->db);
        $productCategory = $this->getFromRequest('productCategory');
		$companies = $company->getCompanyList($productCategory);
		$itemsCount = count($companies);
		
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=company&id=".$companies[$i]['id'];
			$companies[$i]['url']=$url;
		}

        $industryType = new IndustryType($this->db);
		$productIndustryTypeList = $industryType->getTypesWithSubTypes();
		$this->smarty->assign("productTypeList", $productIndustryTypeList);
        
		$this->smarty->assign("category", $companies);
		$this->smarty->assign("itemsCount", $itemsCount);
		
		$jsSources = array('modules/js/checkBoxes.js');
		
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/companyList.tpl');
        $this->smarty->assign('parent', 'company');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionViewDetails() {
        
		$company = new Company($this->db);
		$companyDetails = $company->getCompanyDetails($this->getFromRequest('id'));
        $industrytypesIds = $company->getIndustryTypes($this->getFromRequest('id'));
        $industrytypes = array();
        foreach ($industrytypesIds as $id) { 
            $industrytype = new IndustryType($this->db, $id["industry_type_id"]);
            $industrytypes[] = $industrytype->type;
        }
        
        $industrytypes = implode(",", $industrytypes);
        $this->smarty->assign("industrytypes", $industrytypes);
		$this->smarty->assign("companyDetails", $companyDetails);
		$this->smarty->assign('tpl', 'tpls/viewCompany.tpl');
        $this->smarty->assign('parent', 'company');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$company = new Company($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
                $companyDetails = $company->getCompanyDetails($this->getFromRequest('item_'.$i));
				$item["id"] = $companyDetails["company_id"];
				$item["name"] = $companyDetails["name"];
				$itemForDelete []= $item;
			}
		}
		$this->smarty->assign('page', $this->getFromRequest('page'));
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}

	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$company = new Company($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
            $company->deleteCompany($id);
		}
		header ('Location: admin.php?action=browseCategory&category=company');
		die();
	}
    
  protected function actionAddItem() {

		$form = $_POST;
		if (count($form) > 0) {
			$registration = new Registration($this->db);
			if ($registration->isOwnState($form["country"])) {
				$state = $form["selectState"];
			} else {
				$state = $form["textState"];
			}

			$companyData = array(
				"name" => $form["name"],
				"address" => $form["address"],
				"city" => $form["city"],
				"zip" => $form["zip"],
				"county" => $form["county"],
				"state" => $state,
				"country" => $form["country"],
				"phone" => $form["phone"],
				"fax" => $form["fax"],
				"email" => $form["email"],
				"contact" => $form["contact"],
				"title" => $form["title"],
				"creater_id" => $_SESSION['user_id'],
				"voc_unittype_id" => $form["selectVocUnitType"]
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

                // set company to industry type
                $industrytypemanager = new IndustryTypeManager($this->db);
                $industryTypes = $form["industryType"]; 
                foreach ($industryTypes as $id) {
                    $industrytypemanager->setCompanyToIndustryType($companyID, $id);
                }
				if (isset($companyID)) {
					$unittype = new Unittype($this->db);
					$unittype->setDefaultUnitTypelist($form['unitTypeID'], $this->category, $companyID);
					$apmethod = new Apmethod($this->db);
					$apmethod->setDefaultAPMethodlist($form['APMethodID'], $this->category, $companyID);
				}

				//	redirect
				header("Location: ?action=browseCategory&category=company&notify=3");
				die();

			} else {
				//	Errors on validation of adding for a new company
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

		switch (REGION) {
			case 'eu_uk':
				$usaID = $country->getCountryIDByName('United Kingdom');
				break;
			default:
				$usaID = $country->getCountryIDByName('USA');
				break;
		}

		$companyData['country'] = $companyData['country'] ? $companyData['country'] : $usaID;
		$this->smarty->assign('data', $companyData);
		$state = new State($this->db);
		$stateList = $state->getStateList($companyData['country']);
		if (empty($stateList)) {
			$this->smarty->assign("selectMode", false);
		} else {
			$this->smarty->assign("state", $stateList);
			$this->smarty->assign("selectMode", true);
		}

		$this->smarty->assign("country", $countries);
		$this->smarty->assign("state", $stateList);

		//	set permissions
		$this->setListCategoriesLeftNew($this->category, $this->getFromRequest('id'));
		$this->setNavigationUpNew('root', $this->getFromRequest('id'));
		$this->setPermissionsNew('viewRoot');

		//	Get UnitType list
		$unitType = new Unittype($this->db);
		$unitTypelist = $unitType->getClassesOfUnits();
		$classlist = $unitType->getAllClassesOfUnitTypes();

		//Get APMethods list
		$apmethodObject = new Apmethod($this->db);
		$APMethodList = $apmethodObject->getApmethodList();

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
			'modules/js/addCompanyPopups.js',
            'modules/js/manageCompanies.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('APMethodList', $APMethodList);
		$this->smarty->assign('unitTypelist', $unitTypelist);
		$this->smarty->assign('classlist', $classlist);

		$this->smarty->assign('sendFormAction', '?action=addItem&category=company');
		$this->smarty->assign('tpl', 'tpls/addCompany.tpl');
        $this->smarty->assign('parent', 'company');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionEdit() {

		$form = $_POST;
		if (count($form) > 0) {
			$registration = new Registration($this->db);
			$state = ($registration->isOwnState($form["country"])) ? $form["selectState"] : $form["textState"];

			$companyData = array(
				"company_id" => $this->getFromRequest('id'),
				"name" => $form["name"],
				"address" => $form["address"],
				"city" => $form["city"],
				"zip" => $form["zip"],
				"county" => $form["county"],
				"state" => $state,
				"country" => $form["country"],
				"phone" => $form["phone"],
				"fax" => $form["fax"],
				"email" => $form["email"],
				"contact" => $form["contact"],
				"title" => $form["title"],
				"creater_id" => 0,
				"voc_unittype_id" => $form["selectVocUnitType"]
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

                // set company to industry type
                $industrytypemanager = new IndustryTypeManager($this->db);
                $industryTypes = $form["industryType"]; 
                // unset all industry types
                $industrytypemanager->unSetCompanyToIndustryType($this->getFromRequest('id'));
                foreach ($industryTypes as $id) {
                    $industrytypemanager->setCompanyToIndustryType($this->getFromRequest('id'), $id);
                }
                
				//	redirect
				header("Location: ?action=browseCategory&category=company&id=" . $this->getFromRequest('id') . "&notify=5");
				die();

			} else {
				//	Errors on validation of adding for a new company
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
		$selectMode = false;
		if ($companyDetails['country'] === $usaID) {
			$selectMode = true;
			$state = new State($this->db);
			$stateList = $state->getStateList($usaID);
			$this->smarty->assign("state", $stateList);
		}

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
		$APMethodList = $apmethodObject->getApmethodList();
		if (!isset($defaultAPMethodList)) {
			$defaultAPMethodList = $apmethodObject->getDefaultApmethodlist($companyDetails['company_id']);
		}

		// Get VOC unittype list
		foreach ($unitTypelist as $ut) {
			if ($ut['type_id'] == 2) {
				$vocUnitTypeList[] = $ut;
			}
		}
		$this->smarty->assign('vocUnitTypeList', $vocUnitTypeList);

        $industryType = array();
        $industryTypeId = array(); 
        $industryTypeManager = new IndustryTypeManager($this->db);
        $companiesIndustryTypes = $industryTypeManager->getIndustrytypesByCompanyId($companyDetails['company_id']);
        foreach ($companiesIndustryTypes as $companiesIndustryType) {
            $industryType[] = $companiesIndustryType["type"];
            $industryTypeId[] = $companiesIndustryType["id"];
        }
        $industryTypeList = implode(",", $industryType);	
        $this->smarty->assign('industryTypeList', $industryTypeList); 
        $this->smarty->assign('industryTypeId', $industryTypeId); 
                
		//	set js scripts
		$jsSources = array(
			'modules/js/form.js',
			'modules/js/reg_country_state.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/addCompanyPopups.js',
            'modules/js/manageCompanies.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('unitTypelist', $unitTypelist);
		$this->smarty->assign('APMethodList', $APMethodList);
		$this->smarty->assign('classlist', $classlist);
		$this->smarty->assign('defaultUnitTypelist', $defaultUnitTypelist);
		$this->smarty->assign('defaultAPMethodlist', $defaultAPMethodList);
		$this->smarty->assign('categoryName', $this->category);
		$this->smarty->assign('companyID', $companyDetails['company_id']);

		$this->smarty->assign('sendFormAction', '?action=edit&category=company&id=' . $this->getFromRequest('id'));
		$this->smarty->assign('tpl', 'tpls/addCompany.tpl');
        $this->smarty->assign('parent', 'company');
		$this->smarty->display("tpls:index.tpl");
	}
    
    protected function actionLoadIndustryTypes() {

        $industryType = new IndustryType($this->db);
		$productIndustryTypeList = $industryType->getTypesWithSubTypes();
        
        $industryTypeManager = new IndustryTypeManager($this->db);
        $productTypes = $industryTypeManager->getIndustrytypesByCompanyId($this->getFromRequest('companyId')); 

		$this->smarty->assign("productTypeList", $productIndustryTypeList);
        $this->smarty->assign("productTypes", $productTypes);
		$this->smarty->assign('companyId', $this->getFromRequest('companyId'));
		echo $this->smarty->fetch('tpls/manageIndustryTypes.tpl');
    }
    
    protected function actionSetCompanyToIndustryType() {

        $rowsToSet = $this->getFromRequest('rowsToSet');
		$response = "";
		$industryTypes = array();
		foreach ($rowsToSet as $id) {
			$industryType = new IndustryType($this->db, $id);
			$industryTypes[] = $industryType->type;
			$response .= "<input type='hidden' name='industryType[]' id='industryType[]' value='$industryType->id' />";
		}
		$response .= implode(",", $industryTypes);

		echo $response;
				
    }
}
?>