<?php
class CPfpLibrary extends Controller
{

	public function __contstruct($smarty, $xnyo, $db, $user, $action)
	{
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'pfpLibrary';
		$this->parent_category = 'department';
	}

	/**
	 * Department level PFP library
	 * @param array $vars - $departmentDetails, $facilityDetails,
	 * $companyDetails, $moduleMap, $tab
	 */
	protected function bookmarkDPfpLibrary($vars)
	{
		$facility = new Facility($this->db);
		$productCategory = ($this->getFromRequest('productCategory')) ? $this->getFromRequest('productCategory') : 0;

		extract($vars);

		$url = "?" . $_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/", "", $url);

		$pfpManager = VOCApp::getInstance()->getService('pfp');
		$department = new \VWM\Hierarchy\Department($this->db,
						$this->getFromRequest('id'));
		//get pfpTypes
		$pfpTypes = $department->getPfpTypes();

		//set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$pfpManager->setCriteria('search', $this->convertSearchItemsToArray($this->getFromRequest('q')));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

		$selectedPfpType = $this->getFromRequest('pfpType');
		if ($selectedPfpType == '0') {
			$selectedPfpType = $pfpTypes[0]->id;
		}

		$companyId = $department->getFacility()->getCompanyId();
		$pfpManager->setCriteria('companyId', $companyId);

		if ($productCategory != '0') {

			$pfpManager->setCriteria('industryType', $productCategory);
		}
		
		//check pfp type;
		if (!is_null($selectedPfpType)) {
			$pfpManager->setCriteria('pfpType', $selectedPfpType);
		}
		// get Allowed or Assigned PFP
		if ($this->getFromRequest('tab') == 'all') {
			$pfpCount = $pfpManager->getPfpAllowedCount();
		} else {
			$pfpCount = $pfpManager->getPfpAssignedCount();
		}

		// get Allowed or Assigned PFP
		$pagination = new Pagination((int) $pfpCount);
		$pagination->url = $url;
		
		if ($this->getFromRequest('tab') == 'all') {
			$pfps = $pfpManager->findAllPfps(1, $pagination);
		} else {
			$pfps = $pfpManager->findAllPfps(0, $pagination);
		}

		if ($this->getFromRequest('print') == true) {
			//EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Mixes of department " . $departmentDetails['name'];
			if ($this->getFromRequest('searchAction') == 'search') {
				$exporter->search_term = $this->getFromRequest('q');
			} else {
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}

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
			return;
		}

		//get list of Industry Types
		$industryType = new IndustryType($this->db);
		$productIndustryTypeList = $industryType->getTypesWithSubTypes();
		$this->smarty->assign("productTypeList", $productIndustryTypeList);

		//tell Smarty where to insert drop down list with industry types
		$this->insertTplBlock('tpls/productTypesDropDown.tpl', self::INSERT_AFTER_SEARCH);

		//tell Smarty where to insert PFP Types Filter
		$this->smarty->assign('tab', $this->getFromRequest('tab'));
		$this->insertTplBlock('tpls/filterPfpByPfpTypes.tpl', self::INSERT_AFTER_INDUSTRY_TYPES);


		//set js assets
		$jsSources = array('modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js');

		//send to smarty
		$allUrl = "?action=browseCategory&category=department&id=" . $department->getDepartmentId() . "&bookmark=pfpLibrary&tab=all&productCategory=$productCategory";
		$this->smarty->assign('allUrl', $allUrl);
		$this->smarty->assign("pfpTypes", $pfpTypes);

		$this->smarty->assign("selectedPfpType", $selectedPfpType);
		$this->smarty->assign("productCategory", $productCategory);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pagination', $pagination);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign("productCategory", $productCategory);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign('tpl', 'tpls/pfpMixList.tpl');
	}

	protected function actionAssign()
	{
		$department = new Department($this->db);
		$department->initializeByID($this->getFromRequest('departmentID'));
		$facility = $department->getFacility();
		$manager = new PFPManager($this->db);
		$myPfpList = $manager->getList($facility->getCompanyID());

		//group by ID to simplify comparison
		$myPfpIDs = array();
		foreach ($myPfpList as $key => $myPFP) {
			$myPfpIDs[$myPFP->getId()] = $myPFP;
		}
		$selectedIDs = $this->getFromRequest('id');
		if ($selectedIDs) {
			foreach ($selectedIDs as $selectedID) {
				if (isset($myPfpIDs[$selectedID])) {
					//this pfp is already assigned
					//do nothing
				} else {
					$manager->assignPFP2Company($selectedID, $facility->getCompanyID());
				}
			}
		}

		header('Location: ?action=browseCategory&category=department&id=' . $department->getDepartmentID() . '&bookmark=pfpLibrary&tab=my');
	}

	protected function actionUnassign()
	{
		$department = new Department($this->db);
		$department->initializeByID($this->getFromRequest('departmentID'));
		$facility = $department->getFacility();
		$manager = new PFPManager($this->db);
		$selectedIDs = $this->getFromRequest('id');
		if ($selectedIDs) {
			foreach ($selectedIDs as $selectedID) {
				$manager->unassignPFPFromCompany($selectedID, $facility->getCompanyID());
			}
		}
		header('Location: ?action=browseCategory&category=department&id=' . $department->getDepartmentID() . '&bookmark=pfpLibrary&tab=my');
	}

}