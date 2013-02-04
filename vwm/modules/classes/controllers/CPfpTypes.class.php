<?php

//use \VWM\Hierarchy\Facility;
class CPfpTypes extends Controller {

	function CPfpTypes($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'PfpTypes';
		$this->parent_category = 'company';
	}

	/**
	 * bookmarkRepairOrder($vars)
	 * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
	 */
	protected function bookmarkPfpTypes($vars) {

		extract($vars);
		if (is_null($facilityDetails['facility_id'])) {
			throw new Exception('404');
		}

		$facility = new Facility($this->db);
		$pfpTypesList = $facility->getPfpTypes($facilityDetails['facility_id']);
		if ($pfpTypesList) {
			for ($i = 0; $i < count($pfpTypesList); $i++) {
				$url = "?action=viewDetails&category=pfpTypes&id=" . $pfpTypesList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'] . "&pfpGroup=group";
				$pfpTypesList[$i]->url = $url;
			}
		}
		$this->smarty->assign("childCategoryItems", $pfpTypesList);

		//	set js scripts
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		//	set tpl
		$this->smarty->assign('tpl', 'tpls/pfpTypesList.tpl');
	}

	protected function actionAddItem() {
		//	Access control
		if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
			throw new Exception('deny');
		}

		$request = $this->getFromRequest();
		$request["id"] = $request["facilityID"];
		$request['parent_id'] = $request['facilityID'];
		$request['parent_category'] = 'facility';
		$this->smarty->assign('request', $request);

		$params = array("bookmark" => "repairOrder");

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
		$this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
		$this->setPermissionsNew('viewFacility');

		$facility = new Facility($this->db);
		$department = new Department($this->db);
		$pfpDepartmentsDeafult = $facility->getDepartmentList($request["id"]);
		$pfpDepartmentsIds = implode(',', $pfpDepartmentsDeafult);
		$pfpDepartmentsName = array();
		foreach ($pfpDepartmentsDeafult as $departmentId) {
			$departmentDetails = $department->getDepartmentDetails($departmentId);
			$pfpDepartmentsName[] = $departmentDetails["name"];
		}
		$pfpDepartmentsName = implode(',', $pfpDepartmentsName);

		$this->smarty->assign('pfpDepartments', $pfpDepartmentsIds);
		$this->smarty->assign('pfpDepartmentsName', $pfpDepartmentsName);

		//	set js scripts
		$jsSources = array(
			'modules/js/saveItem.js',
			'modules/js/PopupWindow.js',
			'modules/js/pfpTypesManager.js',
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/checkBoxes.js",
		);
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('pleaseWaitReason', "Please wait.");
		$this->smarty->assign('tpl', 'tpls/addPfpTypes.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionDeleteItem() {

		$req_id = $this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id = array($req_id);
		$itemForDelete = array();
		if (!is_null($this->getFromRequest('id'))) {
			foreach ($req_id as $pfptype) {
				//	Access control
				if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
					throw new Exception('deny');
				}
				$pfptypes = new PfpTypes($this->db, $pfptype);
				$delete = array();
				$delete["id"] = $pfptypes->id;
				$delete["name"] = $pfptypes->name;
				$delete["facility_id"] = $pfptypes->facility_id;
				$itemForDelete[] = $delete;
			}
		}
		if (!is_null($this->getFromRequest('facilityID'))) {
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=pfpTypes");
			//as ShowAddItem
			$params = array("bookmark" => "pfpTypes");

			$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewFacility');
		}

		$this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
	}

	protected function actionViewDetails() {

		$request = $this->getFromRequest();
		$this->smarty->assign('request', $request);

		$facility = new Facility($this->db);
		$pfpTypes = new PfpTypes($this->db, $this->getFromRequest('id'));
		$url = "?" . $_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/", "", $url);
		// we have a two ways
		$pfpGroup = $this->getFromRequest('pfpGroup');
		if ($pfpGroup == 'all') {
			$isAllPFP = true;
		} else {
			$isAllPFP = false;
		}
		$this->smarty->assign('isAllPFP', $isAllPFP);
		$facilityDet = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyId = $facilityDet["company_id"];
		// TODO: add count() for pagination

		if ($isAllPFP) {
			// we show an all pfp's list
			$pfp = new PFPManager($this->db);
			//	set search criteria
			if (!is_null($this->getFromRequest('q'))) {
				$pfp->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
				$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			}
			$pfps = $pfp->getUnAssignPFP2Type($companyId, $this->getFromRequest('id'));

			$pagination = new Pagination(count($pfps));
			$pagination->url = $url;
			$pfps = $pfp->getUnAssignPFP2Type($companyId, $this->getFromRequest('id'), $pagination);
		} else {
			//	set search criteria
			if (!is_null($this->getFromRequest('q'))) {
				$pfpTypes->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
				$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			}
			$pfpProducts = $pfpTypes->getPfpProducts();

			$url = "?" . $_SERVER["QUERY_STRING"];
			$url = preg_replace("/\&page=\d*/", "", $url);
			$pagination = new Pagination(count($pfpProducts));
			$pagination->url = $url;
			$pfps = $pfpTypes->getPfpProducts($pagination);
		}

		$this->smarty->assign('pagination', $pagination);
		$this->smarty->assign('pfpTypes', $pfpTypes);
		$this->smarty->assign('pfps', $pfps);

		//	show which departments are allowed to use pfpType
		$departments = $pfpTypes->getDepartments();
		$pfpDepartments = array();
		foreach ($departments as $department) {
			$pfpDepartments[] = $department->getName();
		}
		$pfpDepartmentsToString = (count($pfpDepartments) > 0)
				? implode(', ', $pfpDepartments)
				: '--';
		$this->smarty->assign('pfpDepartmentsName', $pfpDepartmentsToString);

		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$params = array("bookmark" => "pfpTypes");
		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);

		$this->setPermissionsNew('viewData');
		$this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=pfpTypes');
		$this->smarty->assign('deleteUrl', '?action=deleteItem&category=pfpTypes&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));

		//set js scripts
		$jsSources = array('modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js',
			'modules/js/pfpTypes.js');
		$this->smarty->assign('jsSources', $jsSources);


		//set tpl
		$this->smarty->assign('tpl', 'tpls/viewPfpType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionConfirmDelete() {

		foreach ($this->itemID as $ID) {

			$pfpTypes = new PfpTypes($this->db, $ID);
			$facilityId = $pfpTypes->facility_id;
			$pfpTypes->delete();
		}

		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=pfpTypes&notify=50");
	}

	protected function actionAssign() {
		$pfpIDs = $this->getFromRequest('pfpIDs');
		$pfpTypeid = $this->getFromRequest('id');
		$facilityID = $this->getFromRequest('facilityID');
		$pfp = new PFPManager($this->db);
		foreach ($pfpIDs as $pfpID) {
			$pfp->assignPFP2Type($pfpID, $pfpTypeid);
		}
		$url = "?action=viewDetails&category=pfpTypes&id={$pfpTypeid}&facilityID={$facilityID}&pfpGroup=all";
		echo $url;
	}

	protected function actionUnassign() {

		$pfpIDs = $this->getFromRequest('pfpIDs');
		$pfpTypeid = $this->getFromRequest('id');
		$facilityID = $this->getFromRequest('facilityID');
		$pfp = new PFPManager($this->db);
		foreach ($pfpIDs as $pfpID) {
			$pfp->unAssignPFP2Type($pfpID, $pfpTypeid);
		}
		$url = "?action=viewDetails&category=pfpTypes&id={$pfpTypeid}&facilityID={$facilityID}&pfpGroup=group";
		echo $url;
	}

	protected function actionLoadBriefPfps() {
		$pfpTypes = new PfpTypes($this->db, $this->getFromRequest('pfpTypeId'));
		
        $response = new stdClass;
		$response->id = $pfpTypes->id;
        $response->name = $pfpTypes->name;
        $response->facility_id = $pfpTypes->facility_id;
		$response->pfps = $pfpTypes->getPfpProducts();

		echo json_encode($response);
		die;
	}

	protected function actionCreateLabel() {
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$company = new Company($this->db);
		$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

		$pfpTypes = new PfpTypes($this->db, $this->getFromRequest('id'));
		$pfps = $pfpTypes->getPfpProducts();

		$exporter = new Exporter(Exporter::PDF);
		$exporter->company = $companyDetails['name'];
		$exporter->facility = $facilityDetails['name'];
		$exporter->title = "{$pfpTypes->name} mixes";

		$widths = array(
			'description' => '25',
			'ratio' => '8',
			'mix1' => '11',
			'mix2' => '11',
			'mix3' => '11',
			'mix4' => '11',
			'mix5' => '11',
			'date' => '12'
		);
		$header = array(
			'description' => 'PFP Description',
			'ratio' => 'Ratio',
			'mix1' => array('R/O', array('P/U', 'WASTE')),
			'mix2' => array('R/O', array('P/U', 'WASTE')),
			'mix3' => array('R/O', array('P/U', 'WASTE')),
			'mix4' => array('R/O', array('P/U', 'WASTE')),
			'mix5' => array('R/O', array('P/U', 'WASTE')),
			'date' => 'Date'
		);
		$goodList = array();
		foreach ($pfps as $pfp) {
			$tmp = array(
				'description' => $pfp->getDescription(),
				'ratio' => $pfp->getRatio(false),
				'mix1' => '',
				'mix2' => '',
				'mix3' => '',
				'mix4' => '',
				'mix5' => '',
				'date' => '',
			);
			$goodList[] = $tmp;
		}

		$exporter->setColumnsWidth($widths);
		$exporter->setThead($header);
		$exporter->setTbody($goodList);
		$exporter->export();
	}

	public function actionLoadDepartments() {

		$facilityId = $this->getFromRequest('facilityId');
		$departmentsId = $this->getFromRequest('departmentsId');
		$departmentsId = explode(',', $departmentsId);
		$department = new Department($this->db);
		$facility = new Facility($this->db);
		$pfpDepartmentsDeafult = $facility->getDepartmentList($facilityId);


		// if we add new wo we cannot knew wo id so
		$departmentsDeafult = array();
		foreach ($pfpDepartmentsDeafult as $departmentId) {
			$departmentDetails = $department->getDepartmentDetails($departmentId);
			$departmentsDeafult[$departmentId] = $departmentDetails["name"];
		}

		$this->smarty->assign('departmentsId', $departmentsId);
		$this->smarty->assign('departmentsDeafult', $departmentsDeafult);
		echo $this->smarty->fetch('tpls/setDepartmentToPfpTypes.tpl');
	}

	public function actionSetDepartmentToPfpTypes() {
		$department = new Department($this->db);
		$rowsToSave = $this->getFromRequest('rowsToSave');
		$value = implode(",", $rowsToSave);
		$departmentName = array();
		foreach ($rowsToSave as $departmentId) {
			$departmentDetails = $department->getDepartmentDetails($departmentId);
			$departmentName[] = $departmentDetails["name"];
		}
		$response = implode(",", $departmentName);
		$response .= "<input type='hidden' name='pfpDepartments_id' id='pfpDepartments_id' value='$value' />";

		echo $response;
	}

	public function actionEditPfpTypes() {

		$id = $this->getFromRequest('id');
		$facilityId = $this->getFromRequest('facilityID');
		$pfpType = new PfpTypes($this->db, $id);
		$pfpTypeName = $pfpType->name;
		$departments = $pfpType->getDepartments();
		$departmentsId = array();
		$pfpDepartmentsName = array();

		foreach ($departments as $department) {
			$departmentsId[] = $department->getDepartmentId();
			$pfpDepartmentsName[] = $department->getName();
		}

		$departmentsId = implode(',', $departmentsId);
		$pfpDepartmentsName = implode(',', $pfpDepartmentsName);

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'));
		$this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
		$this->setPermissionsNew('viewFacility');

		$jsSources = array(
			'modules/js/saveItem.js',
			'modules/js/PopupWindow.js',
			'modules/js/pfpTypesManager.js',
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/checkBoxes.js",
		);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('pfpId', $id);
		$this->smarty->assign('pfpDepartmentsName', $pfpDepartmentsName);
		$this->smarty->assign('pfpDepartments', $departmentsId);
		$this->smarty->assign('facilityId', $facilityId);
		$this->smarty->assign('pfpTypeName', $pfpTypeName);
		$this->smarty->assign('tpl', 'tpls/editPfpTypes.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

}

?>