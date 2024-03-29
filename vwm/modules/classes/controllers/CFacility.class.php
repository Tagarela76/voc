<?php
class CFacility extends Controller {
	function CFacility($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'facility';
		$this->parent_category = 'company';
	}

	protected  function actionConfirmDelete() {
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->itemID[0]);

		$company = new Company($this->db);
		$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

		foreach ($this->itemID as $ID) {
			$facilityDetails = $facility->getFacilityDetails($ID);
			$itemForDeleteName[] = $facilityDetails["name"];

			//	setter injection
			$facility->setTrashRecord(new Trash($this->db));
			$facility->deleteFacility($ID);

		}
		$overCategoryType = "facility";

		//post redirect
		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=company&id=" . $companyDetails['company_id'] . "&notify=6");
	}

	protected function actionDeleteItem() {
		$req_id = $this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id = array($req_id);

		$facility = new Facility($this->db);
		if (!is_null($this->getFromRequest('id'))) {
			foreach ($req_id as $facilityID) {
				//	Access control
				if (!$this->user->checkAccess($this->getFromRequest('category'), $facilityID)) {
					throw new Exception('deny');
				}

				$facilityDetails = $facility->getFacilityDetails($facilityID);

				if ($facilityDetails['facility_id'] == null) {
					throw new Exception('404');
				}

				$delete["id"] = $facilityDetails["facility_id"];
				$delete["name"] = $facilityDetails["name"];
				$delete["address"] = $facilityDetails["address"];
				$delete["phone"] = $facilityDetails["phone"];
				$delete["contact"] = $facilityDetails["contact"];
				$itemForDelete[] = $delete;
			}
		}
		if (!is_null($this->getFromRequest('companyID'))) {
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=company&id=" . $this->getFromRequest('companyID'));
			//as ShowAddItem
			$this->setListCategoriesLeftNew('company', $this->getFromRequest('companyID'));
			$this->setNavigationUpNew('company', $this->getFromRequest('companyID'));
			$this->setPermissionsNew('viewCompany');
		} else {
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $req_id[0] . "&bookmark=department");
			//as ViewDetails
			$this->setNavigationUpNew('facility', $facilityDetails['facility_id']);
			$this->setListCategoriesLeftNew('facility', $facilityDetails['facility_id']);
			$this->setPermissionsNew('viewFacility');
		}
		$this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
	}

	protected function actionViewDetails() {
		if (!$this->user->checkAccess($this->getFromRequest('category'), $this->getFromRequest('id'))) {
			throw new Exception('deny');
		}
		$this->setListCategoriesLeftNew($this->getFromRequest('category'), $this->getFromRequest('id'), $this->paramsForListLeft);
		$this->setNavigationUpNew($this->getFromRequest('category'), $this->getFromRequest('id'));
		$this->setPermissionsNew('viewFacility');

		$facilities = new Facility($this->db);
		$facilityDetails = $facilities->getFacilityDetails($this->getFromRequest("id"));

		$jobberManager = new JobberManager($this->db);
		$jobberList = $jobberManager->getFacilityJobberList($this->getFromRequest("id"));
		foreach ($jobberList as $jobber) {
			$jobberDetails[] = $jobberManager->getJobberDetails($jobber['jobber_id']);
		}
		
		
		$this->smarty->assign("jobberDetails", $jobberDetails);
		$this->smarty->assign("facility", $facilityDetails);
		$this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest("id") . '&bookmark=department');
		$this->smarty->assign('tpl', 'tpls/viewFacility.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionBrowseCategory() {
		//this user already read terms and conditions
		if (!empty($_POST['agree'])) {
			$query = "UPDATE " . TB_USER . " SET terms_conditions = 1 WHERE user_id = " . $_SESSION['user_id'];
			$this->db->query($query);
		}

		//  TODO: move voc indicator here from child controllers
		$bookmark = $this->getFromRequest('bookmark');

		$this->smarty->assign("childCategory", $bookmark ? $bookmark : 'department');
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('id'));

		$company = new Company($this->db);
		$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

		$this->smarty->assign('popup_category', 'facility');
		$this->smarty->assign('popup_category_id', $this->getFromRequest('id'));
		$date = getdate();
		$this->smarty->assign('curYear', $date['year']);

		$facility->initializeByID($this->getFromRequest('id'));
		$this->setIndicator($facilityDetails['voc_limit'], $facility->getCurrentUsage()); //

		// for displaying voc unit type
		$unittype = new Unittype($this->db);
		$vocUnitType = $unittype->getNameByID($companyDetails["voc_unittype_id"]);
		$this->smarty->assign('vocUnitType', $vocUnitType);
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();

		foreach ($moduleMap as $key => $module) {
			$showModules[$key] = $this->user->checkAccess($key, $facilityDetails['company_id']);
		}

		$this->smarty->assign('show', $showModules);

		if ($showModules['regupdate']) {
			$mRegAct = new $moduleMap['regupdate'];
			$result = $mRegAct->prepareCountUnread(array(
				'db' => $this->db,
				'userID' => $this->user->xnyo->user['user_id']
			));
			$this->smarty->assign('unreadedRegUpdatesCount', $result);
		}

		$vars = array(
			'facility' => $facility,
			'facilityDetails' => $facilityDetails,
			'companyDetails' => $companyDetails,
			'moduleMap' => $moduleMap
		);

		if ($bookmark == 'product' || $bookmark == 'nox' || $bookmark == 'repairOrder') {
			//  We need to show the product and nox tab at both facility and department levels
			//  that is why we are forwarding to Product controller, action bookmarkDProduct OR
			//  we are forwarding to Nox controller, action bookmarkDNox OR			
			$this->forward($bookmark, 'bookmarkD' . ucfirst($bookmark), $vars);
		} else {
			$this->forward($bookmark, 'bookmark' . ucfirst($bookmark), $vars);
		}
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionAddItem() {
		//	Access control
		if (!$this->user->checkAccess('company', $this->getFromRequest('companyID'))) {
			throw new Exception('deny');
		}
		$jobberManager = new JobberManager($this->db);
		$jobberList = $jobberManager->getJobberList();
		$this->smarty->assign("jobberList", $jobberList);

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

		if (!isset($facilityData)) {
			$data['country'] = $usaID;
			$this->smarty->assign("data", $data);
		}

		$state = new State($this->db);
		$stateList = $state->getStateList($usaID);
		$this->smarty->assign("selectMode", true);
		$this->smarty->assign("country", $countries);
		$this->smarty->assign("state", $stateList);

		//	set permissions
		$this->setListCategoriesLeftNew('company', $this->getFromRequest('companyID'));
		$this->setNavigationUpNew('company', $this->getFromRequest('companyID'));
		$this->setPermissionsNew('facility');

		//	set js scripts
		$jsSources = array(
			'modules/js/reg_country_state.js',
			'modules/js/saveItem.js',
			'modules/js/PopupWindow.js',
			'modules/js/addJobberPopups.js',
			'modules/js/checkBoxes.js'

		);
		$this->smarty->assign('jsSources', $jsSources);

		$viewURL = "?action=viewDetails&category=company&id=" . $this->getFromRequest('companyID');
		$this->smarty->assign('viewURL', $viewURL);

		//	modules/ajax/saveFacility.php - for more details
		$request = $this->getFromRequest();
		$request['id'] = $this->getFromRequest('companyID');
		$request['parent_id'] = $this->getFromRequest('companyID');
		$request['parent_category'] = 'company';
		$this->smarty->assign('request', $request);
		$this->smarty->assign('tpl', 'tpls/addFacility.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionEdit() {
		if (!$this->user->checkAccess($this->category, $this->getFromRequest('id'))) {
			throw new Exception('deny');
		}
		$jobberManager = new JobberManager($this->db);
		$jobberList = $jobberManager->getJobberList();
		$this->smarty->assign("jobberList", $jobberList);
		$facilityJobber = $jobberManager->getFacilityJobberList($this->getFromRequest("id"));
		$this->smarty->assign("facilityJobber", $facilityJobber);

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('id'), true);
		$this->smarty->assign('data', $facilityDetails);
		
		$unitType = new Unittype($this->db);
		$unittype =  $unitType->getDefaultCategoryUnitTypeList($this->getFromRequest('id'), $this->category);
		$unittype = implode(',', $unittype);
		$this->smarty->assign("unittype", $unittype);
		
		$apmethodObject = new Apmethod($this->db);
		$defaultAPMethodList = $apmethodObject->getDefaultCategoryApmethodlist($this->getFromRequest('id'), $this->category);
		$defaultAPMethodList = implode(',', $defaultAPMethodList);
		$this->smarty->assign("defaultAPMethodList", $defaultAPMethodList);
		
		$registration = new Registration($this->db);
		$this->smarty->assign("country", $registration->getCountryList());
		$country = new Country($this->db);
		switch (REGION) {
			case 'eu_uk':
				$usaID = $country->getCountryIDByName('United Kingdom');
				break;
			default:
				$usaID = $country->getCountryIDByName('USA');
				break;
		}
		$selectMode = false;

		if ($facilityDetails['country'] === $usaID) {
			$selectMode = true;
			$state = new State($this->db);
			$stateList = $state->getStateList($usaID);
			$this->smarty->assign("state", $stateList);
		}
		$this->smarty->assign("selectMode", $selectMode);
		$this->setNavigationUpNew($this->category, $this->getFromRequest('id'));
		$this->setListCategoriesLeftNew($this->category, $this->getFromRequest('id'));
		$this->setPermissionsNew('facility');

		//	set js scripts
		$jsSources = array(
			'modules/js/reg_country_state.js',
			'modules/js/saveItem.js',
			'modules/js/PopupWindow.js',
			'modules/js/addJobberPopups.js',
			'modules/js/checkBoxes.js',
		);
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('tpl', 'tpls/addFacility.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}

?>