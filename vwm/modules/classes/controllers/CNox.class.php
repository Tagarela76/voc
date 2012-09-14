<?php


class CNox extends Controller {

	function Cnox($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'nox';
		$this->parent_category = 'department';
	}

	function runAction() {
		$this->runCommon();
		$functionName = 'action' . ucfirst($this->action);
		if (method_exists($this, $functionName))
			$this->$functionName();
	}

	private function actionConfirmDelete() {
		$NOx = new NoxEmissionManager($this->db);
		foreach ($this->itemID as $id) {
			$NOxDetails = $NOx->getNoxEmissionDetails($id);
			$NOx->deleteNoxEmissionsByID($id);
		}

		if ($this->successDeleteInventories) {
			$departmentID = $this->getFromRequest("departmentID");
			$facilityID = $this->getFromRequest("facilityID");
			if ($departmentID) {
				header("Location: ?action=browseCategory&category=department&id=".$departmentID."&bookmark=nox&tab=nox&notify=47");
			} else if ($facilityID) {
				header("Location: ?action=browseCategory&category=facility&id=".$facilityID."&bookmark=nox&tab=nox&notify=47");
			}

		}
	}

	private function actionDeleteItem() {
		$req_id=$this->getFromRequest('id');
		!is_array($req_id) ? $req_id = array($req_id) : "";
		$NOx = new NoxEmissionManager($this->db);
		if (!is_null($this->getFromRequest('id'))) {
		foreach ($req_id as $nox_id) {
				$NOxDetails = $NOx->getNoxEmissionDetails($nox_id);
				$delete["id"] =	$NOxDetails["nox_id"];
				$delete["description"] = $NOxDetails["description"];
				$itemForDelete[] = $delete;
			}
		}

		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID')) && !$this->user->checkAccess('facility', $this->getFromRequest('facilityID'))) {
			throw new Exception('deny');
		}
		$departmentID = $this->getFromRequest('departmentID');
		$facilityID = $this->getFromRequest('facilityID');
		if ($departmentID) {
			$cancelUrl = "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=nox&tab=nox";
			$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign("action", "?action=confirmDelete&departmentID=".$departmentID);
		} else if ($facilityID) {
			$cancelUrl = "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=nox&tab=nox";
			$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'));
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign("action", "?action=confirmDelete&facilityID=".$facilityID);
		}

		$this->smarty->assign("cancelUrl", $cancelUrl);

		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}

	private function actionViewDetails() {
		$parentCategory = '';
		$parentCategoryID = 0;

		if($this->getFromRequest('facilityID')) {
			//	request from facility
			$parentCategory = 'facility';
			$parentCategoryID = $this->getFromRequest('facilityID');
		} elseif ($this->getFromRequest('departmentID')) {
			//	request from department
			$parentCategory = 'department';
			$parentCategoryID = $this->getFromRequest('departmentID');
		} else {
			throw new Exception('404');
		}

		//	Access control
		if (!$this->user->checkAccess($parentCategory, $parentCategoryID)) {
			throw new Exception('deny');
		}

		$this->setNavigationUpNew($parentCategory, $parentCategoryID);
		$this->setListCategoriesLeftNew($parentCategory, $parentCategoryID, array('bookmark' => 'nox'));
		$this->setPermissionsNew('viewData');

		/*
		 * 404 error because controller byrner doesn't exist
		 * so change part of edit url and set nox category instead burner
		 */
		$this->smarty->assign('editUrl', '?action=edit&category='.$this->category
				.'&id='.$this->getFromRequest("id")
				.'&'.urlencode($parentCategory).'ID='.urlencode($parentCategoryID)
				."&tab=".  urlencode($this->getFromRequest('tab')));

		if ($this->getFromRequest('tab')) {
			$functionName = 'viewDetails' . ucfirst($this->getFromRequest('tab'));
			if (method_exists($this, $functionName)) {
				$this->$functionName();
			} else {
				throw new Exception('404');
			}
		}
	}

	private function actionAddItem() {			
		$request = $this->getFromRequest();
		$request['parent_category'] = 'department';

		//	Access control
	 	if (!$this->user->checkAccess('department', $request["departmentID"])) {
			throw new Exception('deny');
		}

		//set permissions
		$this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark' => 'nox'));
		$this->setNavigationUpNew('department', $request['departmentID']);
		$this->setPermissionsNew('viewData');

		$noxManager = new NoxEmissionManager($this->db);
		if ($request['tab'] == 'nox') {
			$burnerList = $noxManager->getBurnerListByDepartment($request['departmentID']);
			$this->smarty->assign("burners", $burnerList);

			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));


			$this->smarty->assign('dataChain', new TypeChain(null, 'date', $this->db, $companyID, 'company'));
		} elseif ($request['tab'] == 'burner') {
			$burnerManufacturerList = $noxManager->getBurnerManufacturerList();
			$this->smarty->assign('burnerManufacturers',$burnerManufacturerList);
		}
		
		$post = $this->getFromPost();		

		if (count($post) > 0) {
			$departmentID = $post['department_id'];
			switch ($post['tab']) {
				case ('burner'):
					$burnerDetails = array(
						'burner_id' => $this->getFromPost('burner_id'),
						'department_id' => $this->getFromPost('department_id'),
						'model' => $this->getFromPost('model'),
						'serial' => $this->getFromPost('serial'),
						'manufacturer_id' => $this->getFromPost('manufacturer_id'),
						'input' => $this->getFromPost('input'),
						'output' => $this->getFromPost('output'),
						'btu' => $this->getFromPost('btu')
					);
					
					$noxBurner = new NoxBurner($this->db, $burnerDetails);											
					$violationList = $noxBurner->validate();
					if(count($violationList) == 0) {
						$noxBurner->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=" . $departmentID . "&bookmark=nox&tab={$request['tab']}&notify=41");
					} else {
						/* 	the modern style */
						$notifyc = new Notify(null, $this->db);
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);

						$this->smarty->assign('violationList', $violationList);
						$this->smarty->assign('data', $burnerDetails);
					}																	
					break;

				case ('nox'):
					$noxDetails = array(
						'nox_id' => $this->getFromPost('nox_id'),
						'department_id' => $this->getFromPost('department_id'),
						'description' => $this->getFromPost('description'),
						'gas_unit_used' => $this->getFromPost('gas_unit_used'),
						'start_time' => $this->getFromPost('start_time'),
						'end_time' => $this->getFromPost('end_time'),
						'burner_id' => $this->getFromPost('burner_id'),
						'note' => $this->getFromPost('note')
					);
					
					$nox = new NoxEmission($this->db, $noxDetails);
					$nox->set_start_time(new DateTime($noxDetails['start_time']));
					$nox->set_end_time(new DateTime($noxDetails['end_time']));
					$totalNox = $noxManager->calculateNox($nox);
					if ($totalNox) {
						$nox->nox = $totalNox;
					}
					$violationList = $nox->validate();
					if(count($violationList) == 0) {								
						$nox->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=" . $departmentID . "&bookmark=nox&tab={$request['tab']}&notify=45");
					} else {						
						$notifyc = new Notify(null, $this->db);
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);						
						$this->smarty->assign('violationList', $violationList);
						$this->smarty->assign('data', $noxDetails);
					}								
					break;
			}
		}
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('sendFormAction', '?action=addItem&category=' . $request['category'] . '&departmentID=' . $request['departmentID'] . '&tab=' . $request['tab']);
		$this->smarty->assign('tpl', 'tpls/addBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit() { 
		$request = $this->getFromRequest();

		//	Access control
        if (!is_null($request['departmentID'])) {
            if (!$this->user->checkAccess('department', $request['departmentID'])) {
                throw new Exception('deny');
            }
        } else {
            if (!$this->user->checkAccess('facility', $request['facilityID'])) {
                throw new Exception('deny');
            }
        }
		

		//set permissions
        if (!is_null($request['departmentID'])) {
            $this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark' => 'accessory'));
        	$this->setNavigationUpNew('department', $request['departmentID']);
        } else {
            $this->setListCategoriesLeftNew('facility', $request['facilityID'], array('bookmark' => 'burnerRatio'));
        	$this->setNavigationUpNew('facility', $request['facilityID']);
        }
		
		$this->setPermissionsNew('viewData');

		if ($this->getFromRequest('tab')) {
			$functionName = 'edit' . ucfirst($this->getFromRequest('tab')); 
			if (method_exists($this, $functionName)) {
				$this->$functionName();
			} else {
				throw new Exception('404');
			}
		}
	}

	/**
	 * bookmarkDNox($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDNox($vars) {
		if (!isset($_GET['tab'])) {
			header("Location: {$_SERVER['REQUEST_URI']}&tab=nox");
		}
		extract($vars);
		$tab = $this->getFromRequest('tab');

		if ($tab == "burner") {
			$this->bookmarkDburner($vars);
		} elseif($tab == "burnerRatio") {
			$this->bookmarkDburnerRatio($vars);
		} else {
			$noxList = false;
			$sortStr = $this->sortList('nox', 3);

			$noxManager = new NoxEmissionManager($this->db);

			// autocomplete
			//$accessory->accessoryAutocomplete($_GET['query'],$jobberIdList);
			// search
			if (!is_null($this->getFromRequest('q'))) {
				/*
				  $accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));
				  $accessoryList = $accessory->searchAccessory($accessoryToFind);
				  $this->smarty->assign('searchQuery', $this->getFromRequest('q'));
				 */
			} else {
				switch ($this->getFromRequest('category')) {
					case 'facility':
						$noxList = $noxManager->getNoxListByFacility(
								$facilityDetails['facility_id'],
								$sortStr);
						break;
					case 'department':
						$noxList = $noxManager->getNoxListByDepartment(
								$departmentDetails['department_id'],
								$sortStr);
						break;
					default:
						throw new Exception('404');
						break;
				}
			}


			if (!is_null($this->getFromRequest('export'))) {
				//	EXPORT THIS PAGE
				$exporter = new Exporter(Exporter::PDF);
				$exporter->company = $companyDetails['name'];
				$exporter->facility = $facilityDetails['name'];
				$exporter->department = $departmentDetails['name'];
				$exporter->title = "NOx Emissions of department " . $departmentDetails['name'];
				if ($this->getFromRequest('searchAction') == 'search') {
					$exporter->search_term = $this->getFromRequest('q');
				} else {
					$exporter->field = $this->getFromRequest('filterField');
					$exporter->condition = $this->getFromRequest('filterCondition');
					$exporter->value = $this->getFromRequest('filterValue');
				}
				$widths = array(
					'id' => '30',
					'description' => '70'
				);
				$header = array(
					'id' => 'ID Number',
					'description' => 'Nox Emission Description',
				);
				$exporter->setColumnsWidth($widths);
				$exporter->setThead($header);
				$exporter->setTbody($noxList);
				$exporter->export();
				die();
			} else {

				if ($noxList) {

					for ($i = 0; $i < count($noxList); $i++) {
						$url = "?action=viewDetails&category=nox&id=" . $noxList[$i]['nox_id'] . "&".urlencode($this->getFromRequest('category'))."ID=" . $this->getFromRequest('id') . "&tab=" . $this->getFromRequest('tab');
						$noxList[$i]['url'] = $url;
						$burnerDetails = $noxManager->getBurnerDetail($noxList[$i]['burner_id']);
						$noxList[$i]['burner'] = $burnerDetails;

						$noxList[$i]['start_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxList[$i]['start_time']);
						$noxList[$i]['end_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxList[$i]['end_time']);
					}

					//	nox indicator
					// choice category facility or department
					if (isset($departmentDetails)) {
						$category = "department";
						$totalSumNox = $noxManager->getCurrentUsageOptimizedByDepartment($departmentDetails['department_id'], $category);
					} else {
						$category = "facility";
						$totalSumNox = $noxManager->getCurrentUsageOptimizedByDepartment($facilityDetails['facility_id'], $category);
					}
					$this->setNoxIndicator($facilityDetails['monthly_nox_limit'], $totalSumNox);
					// insert nox indicator bar into tpl
					$this->insertTplBlock('tpls/noxIndicator.tpl', self::INSERT_AFTER_VOC_GAUGE);
					// insert nox log into tpl
					$this->insertTplBlock('tpls/noxLogPopup.tpl', self::INSERT_NOX_LOG_BEFORE_NOX_GAUGE);
				}

				$this->smarty->assign("childCategoryItems", $noxList);

				//	set js scripts
				$jsSources = array(
					'modules/js/checkBoxes.js',
					'modules/js/autocomplete/jquery.autocomplete.js',
				);
				$this->smarty->assign('jsSources', $jsSources);
				//	set tpl
				$this->smarty->assign('tpl', 'tpls/noxList.tpl');
			}
		}
	}

	/**
	 * bookmarkDNox($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDburner($vars) {
		extract($vars);
		$departmentID = $departmentDetails['department_id'];
		$sortStr = $this->sortList('burner', 1);

		$noxManager = new NoxEmissionManager($this->db);

		// autocomplete
		//$accessory->accessoryAutocomplete($_GET['query'],$jobberIdList);
		// search
		if (!is_null($this->getFromRequest('q'))) {
			/*
			  $accessoryToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			  $accessoryList = $accessory->searchAccessory($accessoryToFind);
			  $this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			 */
		} else {
			$burnerList = $noxManager->getBurnerListByDepartment($departmentID, $sortStr, $pagination);
		}


		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Burners of department " . $departmentDetails['name'];
			if ($this->getFromRequest('searchAction') == 'search') {
				$exporter->search_term = $this->getFromRequest('q');
			} else {
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
				'id' => '30',
				'description' => '70'
			);
			$header = array(
				'id' => 'ID Number',
				'description' => 'Burner Model',
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($burnerList);
			$exporter->export();
			die();
		} else {

			if ($burnerList) {
				for ($i = 0; $i < count($burnerList); $i++) {
					$url = "?action=viewDetails&category=nox&id=" . $burnerList[$i]['burner_id'] . "&departmentID=" . $this->getFromRequest('id') . "&tab=" . $this->getFromRequest('tab');
					$burnerManufacturer = $noxManager->getBurnerManfucaturer($burnerList[$i]['manufacturer_id']);
					$burnerList[$i]['url'] = $url;
					$burnerList[$i]['manufacturer'] = $burnerManufacturer['name'];
				}
			}

			$this->smarty->assign("childCategoryItems", $burnerList);
			//	set js scripts
			$jsSources = array(
				'modules/js/checkBoxes.js',
				'modules/js/autocomplete/jquery.autocomplete.js',
			);
			$this->smarty->assign('jsSources', $jsSources);
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/burnerList.tpl');
		}
	}

	private function viewDetailsBurner() {

		$manager = new NoxEmissionManager($this->db);
		$burnerDetails = $manager->getBurnerDetail($this->getFromRequest("id"));
		if (!$burnerDetails) {
			throw new Exception('404');
		}
		$burner = new NoxBurner($this->db, $burnerDetails);
		$this->smarty->assign('burner', $burner);

		$manufacturer = $manager->getBurnerManfucaturer($burner->manufacturer_id);
		$this->smarty->assign('manufacturer', $manufacturer);

		$this->smarty->assign('tpl', 'tpls/viewNoxBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function viewDetailsNox() {

		$manager = new NoxEmissionManager($this->db);
		$noxEmissionDetails = $manager->getNoxEmissionDetails($this->getFromRequest('id'));

		if (!$noxEmissionDetails) {
			throw new Exception('404');
		}

		$noxEmission = new NoxEmission($this->db, $noxEmissionDetails);
		$noxEmission->start_time = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxEmission->get_start_time());
		$noxEmission->end_time = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxEmission->get_end_time());

		$manager->calculateNox($noxEmission);

		$burnerDetails = $manager->getBurnerDetail($noxEmission->burner_id);

		$this->smarty->assign('noxEmission', $noxEmission);
		$this->smarty->assign('burnerDetails', $burnerDetails);
		$this->smarty->assign('dateFormat', VOCApp::get_instance()->getDateFormat()."  g:i:s");
		$this->smarty->assign('tpl', 'tpls/viewNoxEmission.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function editBurner() {
		$manager = new NoxEmissionManager($this->db);
		$burnerDetails = $manager->getBurnerDetail($this->getFromRequest("id"));
		if (!$burnerDetails) {
			throw new Exception('404');
		}

		$form = $this->getFromPost();

		if (count($form) > 0) {
			$burnerDetails = $form;
			$burner = new NoxBurner($this->db, $form);

			$validation = new Validation($this->db);
			$validStatus = $validation->validateNoxBurner($burner);

			if ($validStatus['summary'] == 'true') {
				$burner->save();

				// redirect
				header("Location: ?action=browseCategory&category=department&id=" . $burner->department_id . "&bookmark=nox&tab=burner");
				die();
			} else {
				/* 	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);

				$this->smarty->assign('validStatus', $validStatus);
			}
		}

		$burnerManufacturerList = $manager->getBurnerManufacturerList();
		$this->smarty->assign('burnerManufacturers', $burnerManufacturerList);

		$this->smarty->assign('data', $burnerDetails);
		//	$this->smarty->assign('sendFormAction', '?action=edit&category='.$request['category'].'&departmentID='.$departmentID);
		$this->smarty->assign('tpl', 'tpls/addBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}


	private function editNox() {
		$request = $this->getFromRequest();

		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));

		$manager = new NoxEmissionManager($this->db);
		$noxEmissionDetails = $manager->getNoxEmissionDetails($this->getFromRequest("id"));

		if (!$noxEmissionDetails) {
			throw new Exception('404');
		}

		$form = $this->getFromPost();

		if (count($form) > 0) {
			$noxEmissionDetails = $form;
			$noxEmission = new NoxEmission($this->db, $noxEmissionDetails);
			$totalNox = $manager->calculateNox($noxEmission);
			if ($totalNox) {
				$noxEmission->nox = $totalNox;
			}
			$violationList = $noxEmission->validate();
			if(count($violationList) == 0) {
				$noxEmission->save();

				// redirect
				header("Location: ?action=browseCategory&category=department&id=" . $noxEmission->department_id . "&bookmark=nox&tab=nox");
				die();
			} else {
				/* 	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);
				$this->smarty->assign('violationList', $violationList);
			}
			
			//	convert time to timestamp
			$startTime = new TypeChain($noxEmissionDetails['start_time'], 'date', $this->db, $companyID, 'company');
			$endTime = new TypeChain($noxEmissionDetails['end_time'], 'date', $this->db, $companyID, 'company');
			$noxEmission->start_time = $noxEmissionDetails['start_time'] = $startTime->getTimestamp();
			$noxEmission->end_time = $noxEmissionDetails['end_time'] = $endTime->getTimestamp();	
		}

		$burnerList = $manager->getBurnerListByDepartment($request['departmentID']);
		$this->smarty->assign("burners", $burnerList);

		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		$this->smarty->assign('dataChain', new TypeChain(null, 'date', $this->db, $companyID, 'company'));

		$noxEmissionDetails['start_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxEmissionDetails['start_time']);
		$noxEmissionDetails['end_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxEmissionDetails['end_time']);

		$this->smarty->assign('data', $noxEmissionDetails);
		//	$this->smarty->assign('sendFormAction', '?action=edit&category='.$request['category'].'&departmentID='.$departmentID);
		$this->smarty->assign('tpl', 'tpls/addBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	/**
	 * bookmarkDNox($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDburnerRatio($vars) {
        
		extract($vars);

		$noxManager = new NoxEmissionManager($this->db);
		$department = new Department($this->db);
		$departmentList = $department->getDepartmentListByFacility($this->getFromRequest('id'));
		$burnerList = array();
		foreach ($departmentList as $departmentItem) {
			$burnerList[$departmentItem['name']] = $noxManager->getBurnerListByDepartment($departmentItem['id']);
		}
		//	set js scripts
		$jsSources = array(
			'modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js',
		);
		$this->smarty->assign('jsSources', $jsSources);
        
        $totalSumNox = $noxManager->getCurrentUsageOptimizedByDepartment($facilityDetails['facility_id'], "facility");

        $this->setNoxIndicator($facilityDetails['monthly_nox_limit'], $totalSumNox);
        // insert nox indicator bar into tpl
        $this->insertTplBlock('tpls/noxIndicator.tpl', self::INSERT_AFTER_VOC_GAUGE);
        // insert nox log into tpl
        $this->insertTplBlock('tpls/noxLogPopup.tpl', self::INSERT_NOX_LOG_BEFORE_NOX_GAUGE);
        $this->smarty->assign("childCategoryItems", $burnerList);
            
		//	set tpl
		$this->smarty->assign('tpl', 'tpls/viewEditBurnerRatio.tpl');

	}
	private function editBurnerRatio() { 
		
		$request = $this->getFromRequest();
		
        //set permissions
		$this->setListCategoriesLeftNew('facility', $request['facilityID'], array('bookmark' => 'nox'));
		$this->setNavigationUpNew('facility', $request['facilityID']);
		$this->setPermissionsNew('viewData');
		
        $noxManager = new NoxEmissionManager($this->db);
        $department = new Department($this->db);
		$departmentList = $department->getDepartmentListByFacility($request['facilityID']);
		$burnerList = array();
		foreach ($departmentList as $departmentItem) {
			$burnerList[$departmentItem['name']] = $noxManager->getBurnerListByDepartment($departmentItem['id']);
		}
        
		$form = $this->getFromPost();

		if (count($form) > 0) {
			$burners = $form;
            $noxBurner = new NoxBurner($this->db);
            $burners4save = array();
            foreach ($burners as $key => $burner) {
                if (preg_match('/ratio_(.*)/', $key, $burnerId)) {
                    $burnerDetails["burner_id"] = $burnerId[1];
                    $burnerDetails["ratio"] = $burner;
                    $burners4save[] = $burnerDetails;
                }
            }
			$error = false;
			$this->db->beginTransaction();
			$violationList = array(); 
            foreach ($burners4save as $burner) {
				$noxBurner = new NoxBurner($this->db);
				$noxBurner->burner_id = $burner["burner_id"];
				$noxBurner->ratio = $burner["ratio"];
				$noxBurner->model = 'test4validation'; // test data (only for validate burner ratio)
				$noxBurner->serial = 'test4validation'; // test data (only for validate burner ratio)
				$noxBurner->input = 100; // test data (only for validate burner ratio)
				$noxBurner->output = 80; // test data (only for validate burner ratio)
				$noxBurner->btu = 1180; // test data (only for validate burner ratio)

				$violation = $noxBurner->validate();
				foreach ($violation as $violationData) {
					if ($violationData->getPropertyPath() == "ratio") {
						$violationList[$noxBurner->burner_id] = $violationData->getMessage();
					}
				}
				if(count($violationList[$noxBurner->burner_id]) == 0) {
					$noxBurner->setRatio2Burner($noxBurner->burner_id, $noxBurner->ratio);
				} else {
					$error = true;
				}
            }

			if ($error) {
				$this->db->rollbackTransaction();
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);
				$this->smarty->assign('violationList', $violationList); //print_r($violationList); die();
			} else {
				$this->db->commitTransaction();
				// redirect
				header("Location: ?action=browseCategory&category=facility&id=" . $request['facilityID'] . "&bookmark=nox&tab=burnerRatio");
				die();
			}
		}

		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

        $this->smarty->assign("childCategoryItems", $burnerList);
        
		$this->smarty->assign('tpl', 'tpls/viewEditBurnerRatio.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddNoxEmissionsByFacLevel() {	
		
		$noxManager = new NoxEmissionManager($this->db);
		$facility = new Facility($this->db);
        $noxBurner = new NoxBurner($this->db);
        
		$request = $this->getFromRequest();
		$request['parent_category'] = 'facility';

		//	Access control
	 	if (!$this->user->checkAccess('facility', $request["facilityID"])) {
			throw new Exception('deny');
		}

		//set permissions
		$this->setListCategoriesLeftNew('facility', $request['facilityID'], array('bookmark' => 'nox'));
		$this->setNavigationUpNew('facility', $request['facilityID']);
		$this->setPermissionsNew('viewData');

		$noxManager = new NoxEmissionManager($this->db);
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest("departmentID"));
		$this->smarty->assign('dataChain', new TypeChain(null, 'date', $this->db, $companyID, 'company'));

		$post = $this->getFromPost();		

		if (count($post) > 0) {
			//nox form validation 
			$nox = new NoxEmission($this->db);
			$nox->department_id = 0;  // test data (only for client input data validation)
			$nox->description = $this->getFromPost('description');
			$nox->gas_unit_used = $this->getFromPost('gas_unit_used');
			$nox->burner_id = 0; // test data (only for client input data validation)
			$nox->note =  $this->getFromPost('note');
			$nox->set_start_time(new DateTime($this->getFromPost('start_time')));
			$nox->set_end_time(new DateTime($this->getFromPost('end_time'))); 
			$totalNox = $noxManager->calculateNox($nox);
			if ($totalNox) {
				$nox->nox = $totalNox;
			} 
			// we should save nox data as array for return to form (if error)
			$noxDetails = array(
				'description' => $nox->description,
				'gas_unit_used' => $nox->gas_unit_used,
				'start_time' => $this->getFromPost('start_time'),
				'end_time' => $this->getFromPost('end_time'),
				'note' => $nox->note
			);
			$violationList = $nox->validate(); 
			if(count($violationList) == 0) {								
				// we create a few nox emissions (for every burner)
				$facilityID = $request['facilityID'];
				$facilityRatio = $noxBurner->getCommonRatio4Facility($facilityID);
				$departmentList = $facility->getDepartmentList($facilityID);
				$burnerList = array();
				
				$this->db->beginTransaction();
				$error = false;
				foreach ($departmentList as $departmentID) {                
					
					$burnerList = $noxManager->getBurnerListByDepartment($departmentID);               
					foreach ($burnerList as $burner) {
						$gasUnitUsed = $this->getFromPost('gas_unit_used') * ($burner['ratio'] / $facilityRatio);
						$nox = new NoxEmission($this->db);
						$nox->department_id = $departmentID;
						$nox->description = $this->getFromPost('description');
						$nox->gas_unit_used = $gasUnitUsed;
						$nox->start_time = $this->getFromPost('start_time');
						$nox->end_time = $this->getFromPost('end_time');
						$nox->burner_id = $burner['burner_id'];
						$nox->note =  $this->getFromPost('note');
						$nox->set_start_time(new DateTime($this->getFromPost('start_time')));
						$nox->set_end_time(new DateTime($this->getFromPost('end_time')));
						$totalNox = $noxManager->calculateNox($nox);
						if ($totalNox) {
							$nox->nox = $totalNox;
						} 
						
						// validation (for every nox emission wich we will create)
						$violationList = $nox->validate();
						if(count($violationList) == 0) {								
							$nox->save();
						} else {
							throw new Exception("Error while validate form <br> $violationList");
						}
					}
				}
				if ($error) {
					$this->db->rollbackTransaction();
				} else {
					$this->db->commitTransaction();
					header("Location: ?action=browseCategory&category=facility&id=" . $facilityID . "&bookmark=nox&tab={$request['tab']}&notify=45");
				}

			} else {
				$error = true;
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $noxDetails);
			}								
		}
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('sendFormAction', '?action=addNoxEmissionsByFacLevel&category=' . $request['category'] . '&facilityID=' . $request['facilityID'] . '&tab=' . $request['tab']);
		$this->smarty->assign('tpl', 'tpls/addBurner.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

}

?>