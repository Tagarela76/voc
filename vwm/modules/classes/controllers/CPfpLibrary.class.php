<?php

class CPfpLibrary extends Controller {

	public function __contstruct($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'pfpLibrary';
		$this->parent_category = 'department';
	}


	protected function bookmarkDPfpLibrary($vars) {
		extract($vars);

		$manager = new PFPManager($this->db);

		$productCategory = ($this->getFromRequest('productCategory')) ? $this->getFromRequest('productCategory') : 0;

		$url = "?".$_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/","", $url);

		//	process search request
		if ($this->getFromRequest('q')) {
			$pfpCount = ($this->getFromRequest('tab') == 'all')
					? $manager->countPFP(0, $this->getFromRequest('q'), $productCategory)
					: $manager->countPFP($companyDetails['company_id'], $this->getFromRequest('q'), $productCategory);

			$pagination = new Pagination((int) $pfpCount);
			$pagination->url = $url;

			$pfps = ($this->getFromRequest('tab') == 'all') ? $manager->searchPFP(0, $pagination,  $this->getFromRequest('q')) : $manager->searchPFP($companyDetails['company_id'], $pagination,  $this->getFromRequest('q'));

			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));

		// or get all
		} else {
			$pfpCount = ($this->getFromRequest('tab') == 'all') ? $manager->countPFP(0, '', $productCategory) : $manager->countPFP($companyDetails['company_id'], '', $productCategory);

			$pagination = new Pagination((int) $pfpCount);
			$pagination->url = $url;

			$pfps = ($this->getFromRequest('tab') == 'all')
					? $manager->getList(null, $pagination, null, $productCategory)
					: $manager->getList($companyDetails['company_id'], $pagination, null, $productCategory);
		}

		//	get list of Industry Types
		$productTypesObj = new ProductTypes($this->db);
		$productTypeList = $productTypesObj->getTypesWithSubTypes();
		$this->smarty->assign("productTypeList", $productTypeList);

		//	tell Smarty where to insert drop down list with industry types
		$this->insertTplBlock('tpls/productTypesDropDown.tpl', self::INSERT_AFTER_SEARCH);

		//	set js assets
		$jsSources = array  ('modules/js/checkBoxes.js',
                                     'modules/js/autocomplete/jquery.autocomplete.js');

		//	send to smarty
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pagination', $pagination);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign('tpl', 'tpls/pfpMixList.tpl');
	}


	protected function actionAssign() {

		$department = new Department($this->db);
		$department->initializeByID($this->getFromRequest('departmentID'));

		$facility = $department->getFacility();
		$manager = new PFPManager($this->db);
		$myPfpList = $manager->getList($facility->getCompanyID());

		//	group by ID to simplify comparison
		$myPfpIDs = array();
		foreach ($myPfpList as $key => $myPFP) {
			$myPfpIDs[$myPFP->getId()] = $myPFP;
		}

		$selectedIDs = $this->getFromRequest('id');
		if($selectedIDs) {
			foreach ($selectedIDs as $selectedID) {
				if (isset($myPfpIDs[$selectedID])) {
					//	this pfp is already assigned
					//	do nothing
				} else {
					$manager->assignPFP2Company($selectedID, $facility->getCompanyID());
				}
			}
		}

		header('Location: ?action=browseCategory&category=department&id='.$department->getDepartmentID().'&bookmark=pfpLibrary&tab=my');
	}


	protected function actionUnassign() {
		$department = new Department($this->db);
		$department->initializeByID($this->getFromRequest('departmentID'));

		$facility = $department->getFacility();

		$manager = new PFPManager($this->db);
		$selectedIDs = $this->getFromRequest('id');
		if($selectedIDs) {
			foreach ($selectedIDs as $selectedID) {
				$manager->unassignPFPFromCompany($selectedID, $facility->getCompanyID());
			}
		}

		header('Location: ?action=browseCategory&category=department&id='.$department->getDepartmentID().'&bookmark=pfpLibrary&tab=my');
	}
}
