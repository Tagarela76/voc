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
		
	}

	private function actionDeleteItem() {
		
	}

	private function actionViewDetails() {
		//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}

		$this->setNavigationUpNew('department', $this->getFromRequest("departmentID"));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest("departmentID"), array('bookmark' => 'nox'));
		$this->setPermissionsNew('viewData');

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

		// protecting from xss
		$post = $this->getFromPost();

		foreach ($post as $key => $value) {
			$post[$key] = Reform::HtmlEncode($value);
		}

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

					$validation = new Validation($this->db);
					$validStatus = array(
						'summary' => 'true',
						'model' => 'failed',
						'serial' => 'failed',
						'manufacturer_id' => 'failed',
						'input' => 'failed',
						'output' => 'failed',
						'btu' => 'failed'
					);
					if (!$validation->check_name($burnerDetails['model'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['model'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['serial'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['serial'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['manufacturer_id'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['manufacturer_id'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['input'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['input'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['output'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['output'] = 'accept';
					}
					if (!$validation->check_name($burnerDetails['btu'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['btu'] = 'accept';
					}


					if ($validStatus['summary'] == 'true') {
						$noxBurner = new NoxBurner($this->db, $burnerDetails);
						$noxBurner->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=" . $departmentID . "&bookmark=nox&tab={$request['tab']}&notify=41");
						die();
					} else {

						/* 	the modern style */
						$notifyc = new Notify(null, $this->db);
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);

						$this->smarty->assign('validStatus', $validStatus);
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

					$validation = new Validation($this->db);
					$validStatus = array(
						'summary' => 'true',
						'description' => 'failed',
						'gas_unit_used' => 'failed',
						'start_time' => 'failed',
						'end_time' => 'failed',
						'burner_id' => 'failed'
					);

					if (!$validation->check_name($noxDetails['description'])) {
						$validStatus['summary'] = 'false';
					} else {
						// check for duplicate names					
						if ($validStatus['summary'] == 'true' && !$validation->isUniqueName("nox", $noxDetails['description'], $departmentID)) {
							$validStatus['summary'] = 'false';
							$validStatus['description'] = 'alreadyExist';
						} else {
							$validStatus['description'] = 'accept';
						}
					}


					if (!$validation->check_name($noxDetails['gas_unit_used'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['gas_unit_used'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['start_time'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['start_time'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['end_time'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['end_time'] = 'accept';
					}
					if (!$validation->check_name($noxDetails['burner_id'])) {
						$validStatus['summary'] = 'false';
					} else {
						$validStatus['burner_id'] = 'accept';
					}

					if ($validStatus['summary'] == 'true') {
						$startTime = new DateTime($noxDetails['start_time']);
						$endTime = new DateTime($noxDetails['end_time']);

						$noxDetails['start_time'] = $startTime->getTimestamp();
						$noxDetails['end_time'] = $endTime->getTimestamp();
						$nox = new NoxEmission($this->db, $noxDetails);
						$totalNox = $noxManager->calculateNox($nox);
						
						if ($totalNox) {
							$nox->nox = $totalNox;
						}
						$nox->save();
						// redirect
						header("Location: ?action=browseCategory&category=department&id=" . $departmentID . "&bookmark=nox&tab={$request['tab']}&notify=45");
						die();
					} else {

						/* 	the modern style */
						$notifyc = new Notify(null, $this->db);
						$notify = $notifyc->getPopUpNotifyMessage(401);
						$this->smarty->assign("notify", $notify);

						$this->smarty->assign('validStatus', $validStatus);
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
		if (!$this->user->checkAccess('department', $request['departmentID'])) {
			throw new Exception('deny');
		}

		//set permissions							
		$this->setListCategoriesLeftNew('department', $request['departmentID'], array('bookmark' => 'accessory'));
		$this->setNavigationUpNew('department', $request['departmentID']);
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
		
		if ($tab == "burner") {
			$this->bookmarkDburner($vars);
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
						$url = "?action=viewDetails&category=nox&id=" . $noxList[$i]['nox_id'] . "&departmentID=" . $this->getFromRequest('id') . "&tab=" . $this->getFromRequest('tab');
						$noxList[$i]['url'] = $url;
						$burnerDetails = $noxManager->getBurnerDetail($noxList[$i]['burner_id']);
						$noxList[$i]['burner'] = $burnerDetails;

						$noxList[$i]['start_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxList[$i]['start_time']);
						$noxList[$i]['end_time'] = date(VOCApp::get_instance()->getDateFormat() . " g:i:s", $noxList[$i]['end_time']);
					}
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

		$this->smarty->assign('editUrl', '?action=edit&category=nox&id=' . $this->getFromRequest("id") . '&departmentID=' . $this->getFromRequest("departmentID") . "&tab=burner");
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
						
		$this->smarty->assign('noxEmission', $noxEmission);
		$this->smarty->assign('dateFormat', VOCApp::get_instance()->getDateFormat()."  g:i:s");
		$this->smarty->assign('editUrl','?action=edit&category=nox&id='.$this->getFromRequest("id").'&departmentID='.$this->getFromRequest("departmentID")."&tab=nox");
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
			
			$validation = new Validation($this->db);
			$validStatus = $validation->validateNoxEmission($noxEmission);

			if ($validStatus['summary'] == 'true') {
				$startTime = new TypeChain($noxEmission->start_time, 'date', $this->db, $companyID, 'company');
				$endTime = new TypeChain($noxEmission->end_time, 'date', $this->db, $companyID, 'company');
				
				$noxEmission->start_time = $startTime->getTimestamp();
				$noxEmission->end_time = $endTime->getTimestamp();										
				$totalNox = $manager->calculateNox($noxEmission);
				if ($totalNox) {
					$noxEmission->nox = $totalNox;
				}
								
				$noxEmission->save();

				// redirect
				header("Location: ?action=browseCategory&category=department&id=" . $noxEmission->department_id . "&bookmark=nox&tab=nox");
				die();
			} else {
				/* 	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);

				$this->smarty->assign('validStatus', $validStatus);
			}
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

}

?>