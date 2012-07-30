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

		//	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$manager->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

		$pfpCount = ($this->getFromRequest('tab') == 'all') ? $manager->countPFPAllowed($companyDetails['company_id'], '', $productCategory) : $manager->countPFPAssigned($companyDetails['company_id'], '', $productCategory);
		$pagination = new Pagination((int) $pfpCount);
		$pagination->url = $url;

		$pfps = ($this->getFromRequest('tab') == 'all')
				? $manager->getListAllowed($companyDetails['company_id'], $pagination, null, $productCategory)
				: $manager->getListAssigned($companyDetails['company_id'], $pagination, null, $productCategory);

        if($this->getFromRequest('print') == true) {
            //	EXPORT THIS PAGE
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
                'description' => '15',
                'ratio' => '5',
                'mix1' => '5',
                'mix2' => '5',
                'mix3' => '5',
                'mix4' => '5',
                'mix5' => '5',
                'mix6' => '5',
                'mix7' => '5',
                'mix8' => '5',
                'mix9' => '5',
                'mix10' => '5',
                'mix11' => '5',
                'mix12' => '5',
                'workOrder' => '10',
                'date' => '10'
            );
            $header = array(
                'description' => 'PFP Description',
                'ratio' => 'Ratio',
                'mix1' => 'MIX-1',
                'mix2' => 'MIX-2',
                'mix3' => 'MIX-3',
                'mix4' => 'MIX-4',
                'mix5' => 'MIX-5',
                'mix6' => 'MIX-6',
                'mix7' => 'MIX-7',
                'mix8' => 'MIX-8',
                'mix9' => 'MIX-9',
                'mix10' => 'MIX-10',
                'mix11' => 'MIX-11',
                'mix12' => 'MIX-12',
                'workOrder' => 'Work Order',
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
                    'mix6' => '',
                    'mix7' => '',
                    'mix8' => '',
                    'mix9' => '',
                    'mix10' => '',
                    'mix11' => '',
                    'mix12' => '',
                    'workOrder' => '',
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
