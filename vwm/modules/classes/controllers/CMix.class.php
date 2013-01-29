<?php

use VWM\Framework\Cache\DbCacheDependency;
use VWM\Label\CompanyLevelLabel;
use VWM\Label\CompanyLabelManager;
use VWM\ManageColumns\BrowseCategoryEntity;
use VWM\ManageColumns\DisplayColumnsSettings;
use VWM\Apps\Process\Process;
use VWM\Apps\WorkOrder\Factory\WorkOrderFactory;
use VWM\Apps\Process\ProcessInstance;
use VWM\Apps\Process\Step;



class CMix extends Controller {

	function CMix($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'mix';
		$this->parent_category = 'department';
	}

	protected function actionConfirmDelete() {
		foreach ($this->itemID as $ID) {
			$mix = new MixOptimized($this->db, $ID);
			$mix->delete();
		}
		header("Location: ?action=browseCategory&category=department&id=" . $mix->department_id . "&bookmark=mix&notify=" . (count($this->itemID) > 1 ? "32" : "33" ));
	}

	protected function actionDeleteItem() {
		$req_id = $this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id = array($req_id);
		$usage = new Mix($this->db);
		if (!is_null($this->getFromRequest('id'))) {
			foreach ($req_id as $mixID) {
				$usageDetails = $usage->getMixDetails($mixID);
				$delete["id"] = $usageDetails["mix_id"];
				$delete["description"] = $usageDetails["description"];
				$itemForDelete[] = $delete;
			}
		}
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=" . $this->getFromRequest('departmentID') . "&bookmark=mix");
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}
		//set permissions
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		$this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
	}

	protected function actionViewDetails() {
		$usage = new Mix($this->db);
		//$usageDetails = $usage->getMixDetails($this->getFromRequest('id')); No usage
		$mixID = $this->getFromRequest('id');
		$mixOptimized = new MixOptimized($this->db, $mixID);
		//	Access control
		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}
		$mixOptimized->getRule();

		$this->smarty->assign("usage", $mixOptimized);
		$apMethodObject = new Apmethod($this->db);
		$apMethodDetails = $apMethodObject->getApmethodDetails($mixOptimized->apmethod_id);
		$this->smarty->assign('apMethodDetails', $apMethodDetails);
		$unittype = new Unittype($this->db);
		//	TODO: что за хрень с рулами?
		$k = 0;
		for ($i = 0; $i < count($mixOptimized->products); $i++) {
			$product = $mixOptimized->products[$i];
			$unittypeDetails[$i] = $unittype->getUnittypeDetails($product->unit_type);
			$productDetails[$i] = $product->getProductDetails($product->product_id);
			for ($j = 0; $j < count($productDetails[$i]['components']); $j++) {
				if (!empty($productDetails[$i]['components'][$j]['rule'])) {
					$rules[$k] = $productDetails[$i]['components'][$j]['rule'];
					$k++;
				}
			}
		}
		$rules = array_keys(array_count_values($rules));
		$rulesCount = count($rules);
		$this->smarty->assign('rules', $rules);
		$this->smarty->assign('rulesCount', $rulesCount);

		$this->smarty->assign('unitTypeName', $unittypeDetails);
		$mixValidatorOptimized = new MixValidatorOptimized();
		$mixOptimizedValidatorResponce = $mixValidatorOptimized->isValidMix($mixOptimized);
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		$companyDetails = $company->getCompanyDetails($companyID);
		$this->smarty->assign('companyDetails', $companyDetails);
		$this->smarty->assign('unittypeObj', $unittype);
		$this->smarty->assign('dailyLimitExceeded', $mixOptimizedValidatorResponce->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixOptimizedValidatorResponce->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixOptimizedValidatorResponce->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixOptimizedValidatorResponce->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixOptimizedValidatorResponce->getFacilityAnnualLimitExceeded());
		$this->smarty->assign('expired', $mixOptimizedValidatorResponce->isExpired());
		$this->smarty->assign('preExpired', $mixOptimizedValidatorResponce->isPreExpired());
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		$this->smarty->assign('backUrl', '?action=browseCategory&category=department&id=' . $this->getFromRequest('departmentID') . '&bookmark=mix');
		$this->smarty->assign('tpl', 'tpls/viewUsage.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionAddPFPItem() {
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		$departmentID = $this->getFromRequest("departmentID");
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		//	Getting Product list
		$productsListGrouped = $this->getProductsListGrouped($companyID);

		$this->smarty->assign('products', $productsListGrouped);
		$jsSources = array('modules/js/flot/jquery.flot.js',
			'modules/js/addPFP.js',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("sendFormAction", "?action=confirmAddPFP&category=mix&departmentID=$departmentID");
		$this->smarty->assign("request", $_GET);
		$this->smarty->assign('tpl', 'tpls/addPFP.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionConfirmAddPFP() {
		$formGet = $this->getFromRequest();
		$form = $this->getFromPost();
		$pfp_primary_product_id = $form['pfp_primary'];
		$productCount = intval($form['productCount']);
		$departmentID = intval($formGet['departmentID']);
		$descr = $form['pfp_description'];
		$products = array();

		for ($i = 0; $i < $productCount; $i++) {
			$productID = $form["product_{$i}_id"];
			$ratio = $form["product_{$i}_ratio"];

			$product = new PFPProduct($this->db);
			$product->setRatio($ratio);
			$product->initializeByID($productID);
			if ($productID == $pfp_primary_product_id) {
				$product->setIsPrimary(true);
			} else {
				$product->setIsPrimary(false);
			}

			$products[] = $product;
		}

		$pfp = new PFP($products);
		$pfp->setDescription($descr);
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		$manager = new PFPManager($this->db);
		$manager->add($pfp, $companyID);
		header("Location: ?action=browseCategory&category=department&id=$departmentID&bookmark=mix&tab=pfp");
	}

	protected function actionGetPFPDetailsAjax() {
		$manager = new PFPManager($this->db);
		$pfp = $manager->getPFP($this->getFromRequest("pfp_id"));
		
		//	uncomment this code when return JSON
		/*$output = new stdClass();
		$output->id = $pfp->id;
		$output->pfp = $pfp;
		echo json_encode($output);*/
		
		$this->smarty->assign("pfp", $pfp);
		echo $this->smarty->fetch("tpls/pfpMini.tpl");
		exit;
	}

	protected function actionViewPFPDetails() {
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}

		$manager = new PFPManager($this->db);
		$pfp = $manager->getPFP($this->getFromRequest("id"));
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		$this->smarty->assign("deleteUrl", "?action=deletePFPItem&category=mix&id={$this->getFromRequest("id")}&departmentID={$this->getFromRequest('departmentID')}");
		$this->smarty->assign("editUrl", "?action=editPFP&category=mix&id={$_GET['id']}&departmentID={$_GET['departmentID']}");
		$this->smarty->assign("pfp", $pfp);
		$this->smarty->assign("request", $this->getFromRequest());
		$this->smarty->assign('tpl', 'tpls/viewPFPDetails.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	/* protected function actionGetPFPProductInfo() {
	  $id = $this->getFromRequest("id");
	  $pfpProduct = new PFPProduct($this->db,$id);
	  if($this->getFromRequest("json")) {
	  echo $pfpProduct->toJson();
	  } else {

	  }
	  exit;
	  } */

	protected function actionEditPFP() {
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');

		$departmentID = $this->getFromRequest("departmentID");
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		$manager = new PFPManager($this->db);
		$id = $this->getFromRequest("id");
		$pfp = $manager->getPFP($id);

		//	Getting Product list
		$productsIDArray = array();
		foreach ($pfp->getProducts() as $p) {
			$productsIDArray[] = $p->product_id;
		}

		if ($this->getFromRequest('reassignError')) {
			$this->smarty->assign('reassignError', 'error');
		}

		$productsListGrouped = $this->getProductsListGrouped($companyID, $productsIDArray);
		$this->smarty->assign('products', $productsListGrouped);

		$jsSources = array('modules/js/flot/jquery.flot.js',
			'modules/js/addPFP.js',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("productCount", $pfp->getProductsCount());
		$this->smarty->assign("pfp", $pfp);
		$this->smarty->assign("edit", true);
		$this->smarty->assign("sendFormAction", "?action=confirmEditPFP&category=mix&departmentID=$departmentID&id=$id");
		$this->smarty->assign("request", $_GET);
		$this->smarty->assign('tpl', 'tpls/addPFP.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	protected function actionConfirmEditPFP() {
		$formGet = $this->getFromRequest();
		$form = $this->getFromPost();

		$pfp_primary_product_id = $form['pfp_primary'];
		$productCount = intval($form['productCount']);
		$departmentID = intval($formGet['departmentID']);
		$descr = $form['pfp_description'];
		$products = array();

		for ($j = 0; $j < $productCount; $j++) {
			if ($form["product_{$i}_id"] == $pfp_primary_product_id) {
				$pfp_primary_ratio = $form["product_{$i}_ratio"];
				break;
			}
		}

		for ($i = 0; $i < $productCount; $i++) {
			$productID = $form["product_{$i}_id"];
			if (isset($form["product_{$i}_ratio"])) {
				$ratio = $form["product_{$i}_ratio"];
			} else if (isset($form["product_{$i}_ratio_from"]) && isset($form["product_{$i}_ratio_to"])) {
				$ratio = ceil($form["product_{$i}_ratio_from"] * $pfp_primary_ratio / 100) + 1;
				$ratio_to = ceil($form["product_{$i}_ratio_to"] * $pfp_primary_ratio / 100) + 1;
				$range_ratio = $form["product_{$i}_ratio_from"] . "-" . $form["product_{$i}_ratio_to"];
			}

			$product = new PFPProduct($this->db);
			$product->setRatio($ratio);
			$product->ratio_to = isset($ratio_to) ? $ratio_to : null;
			isset($range_ratio) ? $product->setRangeRatio($range_ratio) : "";
			isset($range_ratio) ? $product->setIsRange(true) : $product->setIsRange(false);
			$product->initializeByID($productID);
			if ($productID == $pfp_primary_product_id) {
				$product->setIsPrimary(true);
			} else {
				$product->setIsPrimary(false);
			}

			$products[] = $product;
		}

		$manager = new PFPManager($this->db);
		$pfpOld = $manager->getPFP($this->getFromRequest('id'));
		$pfp = new PFP($products);
		$pfp->setDescription($descr);
		$pfp->setID($this->getFromRequest('id'));
		$isModified = $manager->isPFPModified($pfpOld, $pfp);
		if ($isModified) {
			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
			if ($manager->isCreaterPFP($this->getFromRequest('id'), $companyID)) {
				$manager->update($pfpOld, $pfp);
			} else {
				if ($pfp->getDescription() !== $pfpOld->getDescription()) {
					$manager->add($pfp, $companyID);
				} else {
					header("Location: ?action=editPFP&category=mix&departmentID=" . $departmentID . "&id=" . $pfp->getId() . "&reassignError=error");
					die();
				}
			}
		}
		header("Location: ?action=browseCategory&category=department&id=$departmentID&bookmark=mix&tab=pfp");
	}

	protected function actionConfirmDeletePFP() {

		if (!$this->user->checkAccess('department', $this->getFromPost('departmentID'))) {
			throw new Exception('deny');
		}

		$departmentID = $this->getFromPost("departmentID");
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);

		$itemsCount = $this->getFromPost('itemsCount');



		if ($itemsCount) {

			for ($i = 0; $i < $itemsCount; $i++) {
				if (!is_null($this->getFromPost('item_' . $i))) {
					$itemID[] = $this->getFromPost('item_' . $i);
				}
			}
		} else {
			$id = $this->getFromRequest('id');
			$itemID[] = $id;
		}

		$manager = new PFPManager($this->db);
		$pfpList = $manager->getList($companyID, null, $itemID);
		$manager->removeList($pfpList);
		header("Location: ?action=browseCategory&category=department&id=$departmentID&bookmark=mix&tab=pfp");
	}

	protected function actionDeletePFPItem() {
		//var_dump($_GET);

		$departmentID = $this->getFromRequest("departmentID");
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);

		$manager = new PFPManager($this->db);
		$idArray = is_array($this->getFromRequest("id")) ? $this->getFromRequest("id") : array($this->getFromRequest("id"));

		$pfps = $manager->getList($companyID, null, $idArray);

		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id={$this->getFromRequest('departmentID')}&bookmark=mix&tab=pfp");

		if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}

		foreach ($pfps as $p) {
			$delete["id"] = $p->getId();
			$delete["description"] = $p->getDescription();
			$itemForDelete[] = $delete;
		}

		//set permissions
		$this->smarty->assign("departmentID", $departmentID);
		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');
		$count = count($itemForDelete);
		$this->smarty->assign("action", "?action=confirmDeletePFP");
		$this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, "pfp");
	}

	protected function actionIsPFPUnique() {
		$form = $this->getFromRequest();
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($form['departmentID']);
		$manager = new PFPManager($this->db);
		$isUnique = $manager->isUnique($form['descr'], $companyID);
		echo $isUnique ? "TRUE" : "FALSE";
		exit;
	}

	//Calls from bookmarkDMix
	protected function bookmarkDpfp($vars) {
		$departmentID = $this->getFromRequest("id");
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		$manager = new PFPManager($this->db);
		$pfpCount = $manager->countPFP($companyID);
		$pagination = new Pagination((int) $pfpCount);
		$pagination->url = "?action=browseCategory&category=department&id=" . $this->getFromRequest('id') . "&bookmark=" . $this->getFromRequest('bookmark') . "&tab=pfp";
		$pfps = $manager->getList($companyID, $pagination);
		$jsSources = array('modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign('tpl', 'tpls/pfpMixList.tpl');
	}

	/**	 
	 * bookmarkDMix($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDMix($vars) {
		if (!isset($_GET['tab'])) {
			header("Location: {$_SERVER['REQUEST_URI']}&tab=mixes");
		}
		extract($vars);
		
		if ($tab == "pfp") {
			$this->bookmarkDpfp($vars);
		} else {

			$chain = new TypeChain(null, 'Date', $this->db, $departmentDetails['departmentID'], 'department');
			$dateFormatForCalendar = $chain->getFromTypeController('getFormatForCalendar');
			$this->smarty->assign("dateFormat", $dateFormatForCalendar);
			$dateFormat = $chain->getFromTypeController('getFormat');
			$sortStr = $this->sortList('mix', 2);
			$filterStr = $this->filterList('mix', $dateFormat);

			$mixManager = new MixManager($this->db, $this->getFromRequest('id'));
			$departmentNew = new VWM\Hierarchy\Department($this->db, $this->getFromRequest('id'));			

			//	set search criteria
			if (!is_null($this->getFromRequest('q'))) {
				$mixManager->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
				$departmentNew->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
				$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
			}

			$url = "?".$_SERVER["QUERY_STRING"];
			$url = preg_replace("/\&page=\d*/","", $url);
			$url = preg_replace("/\&sort=\d*/","", $url);

			$mixCount = $mixManager->countMixesInFacility($departmentDetails['facility_id']);

			if ($this->getFromRequest('export')) {
				$pagination = null;
			} else {
				$pagination = new Pagination($mixCount);
				$pagination->url = $url;
			}
			$this->smarty->assign('pagination', $pagination);

			//	Try to get mix list from cache
			$mixList = false;
			$cache = VOCApp::get_instance()->getCache();
			$key = md5('mixListByFacility'.$_SERVER["QUERY_STRING"]);
			if ($cache) {
				$mixList = $cache->get($key);
			}

			if(!$mixList) {
				// no mix list in cache  - so we need to calculate it
				$facility = new Facility($this->db);
				$facility->initializeByID($departmentDetails['departmentID']);
				$departments = array();
				$equipments = array();

				$mixValidator = new MixValidatorOptimized();
				$mixHover = new Hover();

				//$mixList = $mixManager->getMixListInFacility($departmentDetails['facility_id'], $pagination, $filterStr);
				$mixList = $departmentNew->getMixList($pagination);
				if (!$mixList) {
					$mixList = array();
				}
                // filter mix list - we should display wo only for accepted department
                $mixFiltered = array(); // array for new mix list
                $repairOrderManager = new RepairOrderManager($this->db);
                foreach ($mixList as $mix) {
                    $acceptedDepartments = $repairOrderManager->getDepartmentsByWo($mix->wo_id); 
                    if (!$acceptedDepartments) {
                        // accept in all departments
                        $mixFiltered[] = $mix;
                    } else { 
                        if (in_array($departmentDetails['department_id'], $acceptedDepartments)) {
                            $mixFiltered[] = $mix;
                        }
                    }
                }
                $mixList = $mixFiltered;
				foreach ($mixList as $mix) {
					$mix->url = "?action=viewDetails&category=mix&id=" . urlencode($mix->mix_id) . "&departmentID=" .
						urlencode($this->getFromRequest('id'));

					if(!$departments[$mix->department_id]) {
						$department = new Department($this->db);
						$department->initializeByID($mix->department_id);
						$departments[$mix->department_id] = $department;
					}

					if (!$equipments[$mix->equipment_id]) {
						$equipment = new Equipment($this->db);
						$equipment->initializeByID($mix->equipment_id);
						$equipments[$mix->equipment_id] = $equipment;
					}

					$mix->setDepartment($departments[$mix->department_id]);
					$mix->setFacility($facility);
					$mix->setEquipment($equipments[$mix->equipment_id]);

					// validate them
					$validatorResponse = $mixValidator->isValidMix($mix);

					if ($validatorResponse->isValid()) {
						$mix->valid = MixOptimized::MIX_IS_VALID;
						$mix->hoverMessage = $mixHover->mixValid();
					} else {
						if ($validatorResponse->isPreExpired()) {
							$mix->valid = MixOptimized::MIX_IS_PREEXPIRED;
							$mix->hoverMessage = $mixHover->mixPreExpired();
						}
						if ($validatorResponse->isSomeLimitExceeded() or $validatorResponse->isExpired()) {
							$mix->valid = MixOptimized::MIX_IS_INVALID;
							$mix->hoverMessage = $mixHover->mixInvalid();
						}
					}

					$mix->getHasChild(); 
				}

				//save to cache
				if ($cache) {
					$sqlDependency = "SELECT MAX(m.last_update_time) " .
						"FROM ".TB_USAGE." m " .
						"JOIN ".TB_DEPARTMENT." d ON m.department_id = d.department_id " .
						"WHERE d.facility_id = {$this->db->sqltext($departmentDetails['facility_id'])}";
					$cache->set($key, $mixList, 86400, new DbCacheDependency($this->db, $sqlDependency));
				}
			}


			if (is_array($mixList) && count($mixList) > 0 && !is_null($this->getFromRequest('export'))) { 
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
					'mix_id' => '10',
					'description' => '60',
					'voc' => '10',
					'creation_time' => '20'
				);
				$header = array(
					'mix_id' => 'ID Number',
					'description' => 'Description',
					'voc' => 'VOC',
					'creation_time' => 'Creation Date'
				);

				$goodUsageList = array();
				foreach ($mixList as $m) {
					$tmp = array("mix_id" => $m->mix_id, "description" => $m->description, "voc" => $m->voc, "creation_time" => $m->creation_time);
					$goodUsageList[] = $tmp;
				}

				$exporter->setColumnsWidth($widths);
				$exporter->setThead($header);
				$exporter->setTbody($goodUsageList);
				$exporter->export();
				die();
				return ;
			}
			// I need get company's industry type 
            // we should get browse category entity for mix
			$browseCategoryEntity = new BrowseCategoryEntity($this->db);
			$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix();
			$companyId = $companyDetails["company_id"];
			$company = new VWM\Hierarchy\Company($this->db, $companyId);
            $mixColumn4Display = $company->getIndustryType()->getDisplayColumnsManager()->getDisplayColumnsSettings($browseCategoryMix->name)->getValue();
            $mixColumn4Display = explode(",", $mixColumn4Display);

            // for displaying voc unit type
			$unittype = new Unittype($this->db);
			$vocUnitType = $unittype->getNameByID($companyDetails["voc_unittype_id"]);
            $widths4column = array(
                    "r_o_description" => "15%",
                    "add_job" => "10%",
                    "product_name" => "12%",
                    "description" => "15%",
                    "contact" => "13%",
                    "r_o_vin_number" => "20%",
					"spent_time" => "15%",
                    "voc" => "5%",
                    "unit_type" => "15%",
                    "creation_date" => "15%",
                );
			$mixFormatObjList = array();
			foreach ($mixList as $mix) {
                $mixFormatObj = array();
                $mixObj = array();
                $widths = array();
				// create new mix object	
				$repairOrder = $mix->getRepairOrder();
                if (in_array("r_o_description", $mixColumn4Display)) {
					$mixObj["r_o_description"] = $repairOrder->description;
				}
				if (in_array("add_job", $mixColumn4Display)) {
					$mixObj["add_job"] = (!$mix->hasChild && $mix->wo_id)?
					"<a href='?action=addItem&category=mix&departmentID=" . $this->getFromRequest('id') . 
						"&parentMixID=" . $mix->mix_id . "&repairOrderId=" . $mix->wo_id . "'
							title='Add child job'>add</a> &nbsp" : "";
				}
                if (in_array("product_name", $mixColumn4Display)) {
                    $productName = "";
                    $products = $mix->getProducts();
                    foreach ($products as $item) {
                        if ($item->is_primary) {
                            $productName = $item->name;
                        }
                    }
					$mixObj["product_name"] = $productName; 
				}
				if (in_array("description", $mixColumn4Display)) {
					$mixObj["description"] = $mix->description;
				}
				if (in_array("contact", $mixColumn4Display)) {
					$mixObj["contact"] = $repairOrder->customer_name;
				}
				if (in_array("r_o_vin_number", $mixColumn4Display)) {
					$mixObj["r_o_vin_number"] = $repairOrder->vin;
				}
				if (in_array("voc", $mixColumn4Display)) {
					$mixObj["voc"] = $mix->voc;
				}
                if (in_array("unit_type", $mixColumn4Display)) {
					$mixObj["unit_type"] = $vocUnitType; 
				}
				if (in_array("creation_date", $mixColumn4Display)) {
					$mixObj["creation_date"] = $mix->creation_time; 
				}
				if (in_array("spent_time", $mixColumn4Display)) {
					$mixObj["spent_time"] = $mix->spent_time; 
				}
                // sort values
                foreach ($mixColumn4Display as $columnId) {
                    $mixFormatObj[$columnId] = $mixObj[$columnId];
                    $widths[] = $widths4column["$columnId"];
                }
				$mixObjList["mixObject"] = $mixFormatObj;
				$mixObjList["valid"] = $mix->valid; 
				$mixObjList["url"] = $mix->url; // it is fix value (always display
				$mixObjList["mix_id"] = $mix->mix_id; // it is fix value (always display
				$mixFormatObjList[] = $mixObjList;
			}
       
            $mixColumn4DisplayFormat = array();
            foreach ($mixColumn4Display as $columnId) {
                $mixColumn4DisplayFormat[] = $company->getIndustryType()->getLabelManager()->getLabel($columnId)->getLabelText();
            }
       

			$this->smarty->assign('widths', $widths);
			$this->smarty->assign('columnCount', count($mixColumn4DisplayFormat));
			$this->smarty->assign('mixColumn4Display', $mixColumn4DisplayFormat);
			$this->smarty->assign('mixFormatObjList', $mixFormatObjList);
			
			$this->smarty->assign('vocUnitType', $vocUnitType);
			$this->smarty->assign('childCategoryItems', $mixList);
			//set js scripts
			$jsSources = array('modules/js/checkBoxes.js',
				'modules/js/autocomplete/jquery.autocomplete.js');
			$this->smarty->assign('jsSources', $jsSources);
			//set tpl
			$this->smarty->assign('tpl', 'tpls/mixListNew.tpl');

		}
	}

	protected function actionCalculateVOCAjax() {
		if ($_REQUEST['debug']) {
			$debug = true;
		}

		if ($debug) {
			var_dump('$_REQUEST', $_REQUEST);
		}

		$form = $_REQUEST;
		if (!isset($form['products'])) {
			echo json_encode(array('products_error' => 'Products are not set!'));
			exit;
		}

		$jmix = json_decode($form['mix']);
		$mix = $this->buildMix($jmix);
		$jproducts = json_decode($form['products']);

		if ($debug) {
			var_dump($jproducts, '+++', json_decode($form['wasteJson']));
		}
		$mix->products = $this->buildProducts($jproducts);
		/* Get FAcility id */
		$departmentID = $form['departmentID'];
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];
		$mix->facility_id = $facilityID;
		$mix->department_id = $departmentID;
		$mix->getEquipment();
		$mix->getFacility();


		if ($debug) {
			echo'<h1>MIX</h1>';
			var_dump($mix);
		}
		/*
		  if (!is_array($form['wasteJson'])){
		  $warr = json_decode($form['wasteJson']);
		  $i=0;
		  while($warr[$i]){
		  $arr = get_object_vars($warr[$i]);
		  $wasteArray[$i] = $arr;
		  if (isset($wasteArray[$i]['pollutions'])){
		  $j = 0;
		  while ($wasteArray[$i]['pollutions'][$j]){
		  var_dump('*',get_object_vars($wasteArray[$i]['pollutions'][$j]));
		  $pollutionsarr[$j] = get_object_vars($wasteArray[$i]['pollutions'][$j]);
		  $pollutionsarr[$j]['value'] = $pollutionsarr[$j]['quantity'];
		  $j++;
		  }
		  $wasteArray[$i] = $pollutionsarr;
		  }else{$wasteArray[$i]['value'] = $wasteArray[$i]['quantity'];
		  }
		  $i++;
		  }
		  var_dump('$wasteArray',$wasteArray);
		  $ttt = get_object_vars($tt[0]);
		  $ttt['value'] = $ttt['quantity'];
		  $wastearray = $ttt;
		  }else{
		  $wastearray = $form['wasteJson'];
		  } */


		$w = $form['wasteJson'];
		$r = $form['recycleJson'];
		$mix->iniWaste(false);
		$mix->iniRecycle(false);
		$mix->waste['value'] = $w['value'];
		$mix->recycle['value'] = $r['value'];
		$mix->waste['unitttypeID'] = $w['unittype'];
		$mix->recycle['unitttypeID'] = $r['unittype'];
		if ($debug) {
			echo'<h1>calculateCurrentUsage</h1>';
			var_dump('*******', $mix->waste, $mix->recycle);
		}
		$mix->calculateCurrentUsage();


		if ($debug) {
			echo'<h1>MIX</h1>';

			var_dump($mix, '===========');
		}

		$mixValidator = new MixValidatorOptimized();
		//TODO: stopped here Denis April 4, 2011  -->

		$mixValidatorResponse = $mixValidator->isValidMix($mix);

		if ($debug) {
			var_dump($mixValidatorResponse, $validationRes);
			echo "<h2>VOCs:</h2>";
			var_dump($mix->voc, $mix->voclx, $mix->vocwx);
		}

		$responce = array(
			"currentUsage" => round($mix->currentUsage, 2),
			"dailyLimitExcess" => $mixValidatorResponse->isDailyLimitExceeded(),
			"departmentLimitExceeded" => $mixValidatorResponse->isDepartmentLimitExceeded(),
			"facilityLimitExceeded" => $mixValidatorResponse->isFacilityLimitExceeded(),
			"facilityAnnualLimitExceeded" => $mixValidatorResponse->getFacilityAnnualLimitExceeded(),
			"departmentAnnualLimitExceeded" => $mixValidatorResponse->getDepartmentAnnualLimitExceeded(),
			"REQUEST_URI_LEN" => strlen($_SERVER['QUERY_STRING'])
		);

		if ($debug) {
			var_Dump($responce);
		}

		echo json_encode($responce);
		exit;
	}

	protected function actionEditItemAjax() {

		if ($_REQUEST['debug']) {
			$debug = true;
		}

		if ($debug) {
			var_dump($_REQUEST);
		}

		$form = $_REQUEST;		

		if (!isset($form['id'])) {
			echo json_encode(array('mix_error' => 'No mix ID!!'));
			exit;
		}

		$mix = new MixOptimized($this->db, $form['id']);
		$productsOldVal = $mix->products;

		if ($debug) {
			var_dump('!!!!!!!!!', $mix);
		}

		$departmentID = $mix->department_id;
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);

		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];

		//	Extractt from json
		$jmix = json_decode($form['mix']);

		$chain = new TypeChain(null, 'Date', $this->db, $departmentID, 'department');
		$mixDateFormat = $chain->getFromTypeController('getFormat');

		$jmix->dateFormat = $mixDateFormat;

		$wastes = json_decode($form['wasteJson']);
		$recycle = json_decode($form['recycleJson']);
		//	Start processing waste
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach ($moduleMap as $key => $module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show', $showModules);
		$isMWS = false;

		if ($showModules['waste_streams']) {
			//	OK, this company has access to waste streams module, so let's setup..
			$mWasteStreams = new $moduleMap['waste_streams'];
			$isMWS = true; //shows that we can access module Waste Streams
			if ($debug) {
				echo "<h1>Wastes:</h1>";
				var_dump($wastes);
			}
			$params = array(
				'db' => $this->db,
				'xnyo' => $this->xnyo,
				'isForm' => (count($form) > 0),
				'facilityID' => $facilityID,
				'companyID' => $companyID,
				'jmix' => $jmix,
				'wastes' => $wastes,
				'recycle' => $recycle
			);

			$result = $mWasteStreams->prepare4mixAdd($params);

			if ($debug) {
				var_dump('$result', $result);
			}
			foreach ($result as $key => $value) {
				$this->smarty->assign($key, $value);
			}

			if (isset($result['storageError']) || ($result['isDeletedStorageError'] == 'true')) {
				$storagesFailed = true;
			}
			$wasteArr = $mWasteStreams->resultParams['waste'];
			if ($debug) {
				echo "<h1>mWasteStreams->resultParams</h1>";
				var_dump($mWasteStreams->resultParams['waste']);
			}


			//...........
			//	OK, this company has access to waste streams module, so let's setup..
			//$mWasteStreams = new MWasteStreams();

			$result = $mWasteStreams->validateWastes($this->db, $this->xnyo, $facilityID, $companyID, date("Y-m-d"), $wastes);
			if ($debug) {
				echo "<h1>validateWastes</h1>";
				var_Dump($result);
				//var_dump($wastes);
			}
			if ($result != false) {
				echo json_encode($result);
				exit;
			} else {
				//echo "<p>Waste stream validation failed</p>";
			}
		}
		
		if (!isset($form['mix']) || !$this->validateInputMix($form['mix'])) {
			echo json_encode(array('mix_error' => 'Mix error!'));
			exit;
		}

		if (!isset($form['products'])) {
			echo json_encode(array('products_error' => 'Products are not set!'));
			exit;
		}

		$jproducts = json_decode($form['products']);
		if ($debug) {
			var_dump('$jproducts', $jproducts);
		}

		$valProductsRes = $this->validateProducts2($jproducts);

		if ($valProductsRes === true) {
			if ($debug) {
				echo "<p>Products are OK</p>";
			}
		} else {
			if ($debug) {
				echo "<p>Products Error</p>";
				var_dump($valProductsRes);
			}
			echo json_encode(array('products_error' => 'Products error!'));
			exit;
		}

		//	Start to ini onbjects
		$this->updateMixByForm($mix, $jmix);
		$mix->isMWS = $isMWS;
		$mix->products = $this->buildProducts($jproducts);
		$mix->getEquipment();
		$mix->getFacility();
		if ($debug) {
			echo "<h1>DATA</h1>";
			var_dump($wastes, $recycle);
		}
		$this->AddOrEditAjax($facilityID, $companyID, $isMWS, $mix, $mWasteStreams, $wastes, $recycle, $debug, $productsOldVal);
	}

	protected function actionAddItemAjax() {
		$form = $_REQUEST; 
		
//$debug = true;
		if ($form['debug']) {
			$debug = true;
			echo "add mix";
			var_dump($form);
		}

		$departmentID = $form['departmentID'];
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);

		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);
		$facilityID = $departmentDetails['facility_id'];

		$chain = new TypeChain(null, 'Date', $this->db, $departmentID, 'department');
		$mixDateFormat = $chain->getFromTypeController('getFormat');

		//	Extractt from json
		$jmix = json_decode($form['mix']);
		$jmix->dateFormat = $mixDateFormat;
		$wastes = json_decode($form['wasteJson']);
		$recycle = json_decode($form['recycleJson']);

		//	Start processing waste
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach ($moduleMap as $key => $module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show', $showModules);
		$isMWS = false;

		if ($showModules['waste_streams']) {
			if ($debug) {
				echo "<h1>MWS MODULE</h1>";
				var_dump($recycle);
			}
			//	OK, this company has access to waste streams module, so let's setup..
			$mWasteStreams = new $moduleMap['waste_streams'];
			$isMWS = true; //shows that we can access module Waste Streams
			$params = array(
				'db' => $this->db,
				'xnyo' => $this->xnyo,
				'isForm' => (count($form) > 0),
				'facilityID' => $facilityID,
				'companyID' => $companyID,
				'jmix' => $jmix,
				'wastes' => $wastes,
				'recycle' => $recycle
			);

			$result = $mWasteStreams->prepare4mixAdd($params);

			foreach ($result as $key => $value) {
				$this->smarty->assign($key, $value);
			}

			if (isset($result['storageError']) || ($result['isDeletedStorageError'] == 'true')) {
				$storagesFailed = true;
			}
			if ($debug) {
				echo "<h1>OLOLO</h1>";
				var_dump($mWasteStreams->resultParams['waste']);
				$wasteArr = $mWasteStreams->resultParams['waste'];
			}

			//...........
			//	OK, this company has access to waste streams module, so let's setup..
			//$mWasteStreams = new MWasteStreams();
			if ($debug)
				echo "<h1>new MWasteStreams</h1>";

			$result = $mWasteStreams->validateWastes($this->db, $this->xnyo, $facilityID, $companyID, '03-29-2011', $wastes);
			if ($debug) {
				echo "<h1>validateWastes</h1>";
				var_Dump($mWasteStreams, $result);
			}
			if ($result != false) {
				echo json_encode($result);
				exit;
			} else {
				if ($debug) {
					echo "<p>Waste stream validation failed</p>";
				}
			}
		} else {
			if ($debug) {

				echo "<h1>NO MWS MODULE</h1>";
			}
		}

		if (!isset($form['mix']) or !$this->validateInputMix($form['mix'])) {
			echo json_encode(array('mix_error' => 'Mix error!'));
			exit;
		}

		if (!isset($form['products'])) {
			echo json_encode(array('products_error' => 'Products are not set!'));
			exit;
		}

		$jproducts = json_decode($form['products']);
		if ($debug) {
			var_dump($jproducts);
		}

		$valProductsRes = $this->validateProducts2($jproducts);

		if ($valProductsRes === true) {
			if ($debug) {
				echo "<p>Products are OK</p>";
			}
		} else {
			if ($debug) {
				echo "<p>Products Error</p>";
				var_dump($valProductsRes);
			}
			echo json_encode(array('products_error' => 'Products error!'));
			exit;
		}

		//	Start to ini onbjects
		$mix = $this->buildMix($jmix);
		$mix->facility_id = $facilityID;
		$mix->isMWS = $isMWS;
		if($jmix->step_id!=''){
			$mix->setStepId($jmix->step_id);
		}
		
		$mix->products = $this->buildProducts($jproducts);
		$mix->getEquipment();
		$mix->getFacility();
		if ($debug) {
			var_dump('AddOrEditAjax', $params);
		}
		$this->AddOrEditAjax($facilityID, $companyID, $isMWS, $mix, $mWasteStreams, $wastes, $recycle, $debug);
	}

	protected function AddOrEditAjax($facilityID, $companyID, $isMWS, MixOptimized $mix, MWasteStreams $mWasteStreams, $jwaste, $jrecycle, $debug = false, $productsOldVal = null) {
//$debug =true;
		
		if ($isMWS) {
			//here we calculate total waste for voc calculations
			$params = array(
				'products' => $mix->products,
				'db' => $this->db
			);
			if ($debug) {
				echo "<h3>calculateWaste1</h3>";
				var_dump($mWasteStreams->resultParams);
			}
			$result = $mWasteStreams->calculateWaste($params);

			extract($result); //here extracted $wasteData, $wasteArr and $ws_error
			if ($ws_error) {
				if ($debug) {
					echo "<p>Waste Error</p>";
					var_dump($ws_error);
				}
				echo json_encode(array('waste_error' => $ws_error));
				exit;
			}

			$mix->waste = $wasteData;
			if ($debug) {
				echo "<p>calculateWaste STREAM</p>";
				var_dump($mWasteStreams, $mix->waste);
			}
		} else {
			$w = $jwaste;

			$mix->iniWaste(false);

			$mix->waste['value'] = $w->value;

			$mix->waste['unitttypeID'] = $w->unittype;

			$u = new Unittype($this->db);
			$unittypeDescr = $u->getUnittypeDetails($w->unittype);
		}
		$r = $jrecycle;
		$mix->iniRecycle(false);
		$mix->recycle['value'] = $r->value;
		$mix->recycle['unitttypeID'] = $r->unittype;

		$mixValidator = new MixValidatorOptimized();
		$mix->calculateCurrentUsage();
		$mixValidatorResponse = $mixValidator->isValidMix($mix);

		if ($debug) {
			//var_dump($mixValidatorResponse);

			echo "<h2>VOCs:</h2>";
			var_dump($mix->voc, $mix->voclx, $mix->vocwx);
		}

		if ($debug) {
			echo "MWasteStreams:";
			var_dump($mWasteStreams->resultParams['waste']);
		}

		$mix->waste = $jwaste;
		$mix->debug = $debug;
		$mix->recycle = $jrecycle;
		//$mix->setWoId(777);
		
		//Create Repair Order if not exist
		if (is_null($mix->getWoId())) {
			$repairOrder = new RepairOrder($this->db);
			$repairOrderManager = new RepairOrderManager($this->db);
			$repairOrder->setNumber($mix->getDescription());
			$repairOrder->setFacilityId($facilityID);
			$woId = $repairOrder->save();

			$mix->setWoId($woId);
			$repairOrderManager->setDepartmentToWo($woId, $mix->getDepartmentId());
		}
		
		$newMixID = $mix->save($isMWS, $optMix);
		
		if (!$newMixID) {
			echo "Failed to save Mix. Please contact system administrator";
			exit;
		}

		//If module 'Waste Streams' is disabled, waste is already saved in mix->save func
		if ($isMWS) {
			//echo "prepareSaveWasteStreams";
			$mWasteStreams->prepareSaveWasteStreams(array('id' => $newMixID, 'db' => $this->db));
			if ($debug) {
				echo "<h1>Waste Streams saved to mix #$newMixID!</h1>";
			}
		}

		//Increment department usage

		$departmentID = $_REQUEST['departmentID'];
		$department = new Department($this->db);
		$department->incrementUsage(date("m"), date("Y"), $mix->voc, $departmentID);

		if ($debug) {
			echo "<h1>DONE!</h1>";
			var_dump($mix);
		}
		/* INVENTORY CREATING ORDER */
		$InventoryManager = new InventoryManager($this->db);
		//$InventoryManager->inventoryInstockDegreece($productsOldVal, $mix);
		$result = $InventoryManager->runInventoryOrderingSystem($mix);

		/* */
		echo "DONE";
		exit;
	}

	protected function buildProducts($ps) {

		$products = array();
		foreach ($ps as $p) {

			$product = new MixProduct($this->db);

			$product->initializeByID($p->productID);
			$product->quantity = $p->quantity;

			$unittype = new Unittype($this->db);
			$unittypeDetails = $unittype->getUnittypeDetails($p->selectUnittype);
			$product->unit_type = $unittypeDetails['name'];
			$product->unittypeDetails = $unittypeDetails;

			$product->json = json_encode($product);

			$product->is_primary = ($p->isPrimary) ? 1 : 0;
			$product->ratio_to_save = (isset($p->ratio)) ? $p->ratio : null;
			$products[] = $product;
		}

		return $products;
	}

	protected function buildMix($m) {

		$optMix = new MixOptimized($this->db);
		
		$optMix->department_id = $_REQUEST['departmentID'];
		$optMix->equipment_id = $m->equipment;
		$optMix->description = $m->description;
		$optMix->rule = $m->rule;
		$optMix->rule_id = $m->rule;
		$optMix->exempt_rule = $m->excemptRule;
		$optMix->creation_time = $m->creationTime;
		$optMix->spent_time = $m->spent_time;
		$optMix->apmethod_id = $m->APMethod;
		$optMix->notes = $m->notes;
		$optMix->valid = true;
		$optMix->unittypeClass = $m->selectUnittypeClass;
		$optMix->iteration = $m->iteration;
		$optMix->parent_id = (empty($m->parentID)) ? null : $m->parentID;
		$optMix->wo_id = $m->wo_id;
		$optMix->work_order_iteration = $m->work_order_iteration;

		return $optMix;
	}

	protected function updateMixByForm(MixOptimized $basemix, $formMix) {



		$basemix->equipment_id = $formMix->equipment;
		$basemix->description = $formMix->description;
		$basemix->rule = $formMix->rule;
		$basemix->rule_id = $formMix->rule;
		$basemix->exempt_rule = $formMix->excemptRule;
		$basemix->creation_time = $formMix->creationTime;
		$basemix->spent_time = $formMix->spent_time;
		$basemix->notes = $formMix->notes;
		$basemix->apmethod_id = $formMix->APMethod;
		$basemix->valid = true;
		$basemix->unittypeClass = $formMix->selectUnittypeClass;
	}

	protected function validateInputMix($m) {				
		$m = json_decode($m);				
		if (empty($m->description) || empty($m->creationTime)) {
			return false;
		}

		return true;
	}

	protected function validateProducts2($products) {

		$validation = new Validation($this->db);
		$productConflitcs = array();

		foreach ($products as $product) {
			$isProdConflict = $validation->checkWeight2Volume($product->productID, $product->selectUnittype);

			if ($isProdConflict !== true) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = $isProdConflict;
				$productConflitcs[$product->productID] = $product->selectUnittype;
			}
		}

		return empty($productConflitcs) ? true : $productConflitcs;
	}

	protected function actionAddItem() {		
		//	Access control
		if (!$this->user->checkAccess($this->parent_category, $this->getFromRequest('departmentID'))) {
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
		$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
		$this->setPermissionsNew('viewData');

		if ($this->getFromPost('save')) {
			$action = $this->getFromPost('save') == "Add product to list" ? "addItem" : "saveMix";
		} else {
			$action = "addItem";
		}

		$this->addEdit($action, $this->getFromRequest('departmentID'));
	}

	protected function actionEdit() {

		$mix = new Mix($this->db);
		$departmentID = $mix->getMixDepartment($this->getFromRequest('id'));
		//	Access control
		if (!$this->user->checkAccess('department', $departmentID)) {
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('department', $departmentID);
		$this->setNavigationUpNew('department', $departmentID);
		$this->setPermissionsNew('viewData');

		if (isset($_POST['save'])) {
			$action = $_POST['save'] == "Add product to list" ? "EditAddItem" : "edit";
		} else {
			$action = "edit";
		}

		$this->addEdit($action, $departmentID);
	}

	protected function actionCreateLabel() {
		$usage = new Mix($this->db);
		$usageDetails = $usage->getMixDetails($this->getFromRequest('id'));
		$mixID = $this->getFromRequest('id');
		$mixOptimized = new MixOptimized($this->db, $mixID);
		$mixOptimized->getRule();

		$component = new Component($this->db);
		$hz = new Hazardous($this->db);
		$totalMixComponents = array();
		$totalChemicalClassification = array();
		$totalHealthHazardous = array();
		foreach ($mixOptimized->products as $product) {
			$components = $component->getComponentDetailsByProduct($product->product_id);
			$product->setComponents($components);

			//	collect chemicals into one array
			foreach ($components as $oneComponent) {
				if ($totalMixComponents[$oneComponent->component_id] === null) {
					$totalMixComponents[$oneComponent->component_id] = $oneComponent;
				}
			}

			//	chemical classification is stord in the same table with health hazard requirements
			$chemicalClassification = $hz->getChemicalClassification($product->product_id);
			foreach ($chemicalClassification as $chemicalClassificationItem) {
				if ($hz->isChemicalClassification($chemicalClassificationItem['id'])) {
					$totalChemicalClassification[$chemicalClassificationItem['id']] = $chemicalClassificationItem['name'];
				} else {
					$totalHealthHazardous[$chemicalClassificationItem['id']] = $chemicalClassificationItem['name'];
				}
			}
		}
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($mixOptimized->department_id);
		$companyDetails = $company->getCompanyDetails($companyID);
		$this->smarty->assign('companyDetails', $companyDetails);

		$unittype = new Unittype($this->db);
		$this->smarty->assign('unittypeObj', $unittype);

		$this->smarty->assign("usage", $mixOptimized);
		$this->smarty->assign("components", $totalMixComponents);
		$this->smarty->assign("chemicalClassification", implode('/', $totalChemicalClassification));
		$this->smarty->assign("healthHazardous", implode('/', $totalHealthHazardous));
		$this->smarty->display("tpls/mixLabel.tpl");
		die();

		$this->smarty->assign("usage", $mixOptimized);

		$apMethodObject = new Apmethod($this->db);
		$apMethodDetails = $apMethodObject->getApmethodDetails($mixOptimized->apmethod_id);
		$this->smarty->assign('apMethodDetails', $apMethodDetails);
		$unittype = new Unittype($this->db);

		$k = 0;
		for ($i = 0; $i < count($mixOptimized->products); $i++) {
			$product = $mixOptimized->products[$i];
			$unittypeDetails[$i] = $unittype->getUnittypeDetails($product->unit_type);
			$productDetails[$i] = $product->getProductDetails($product->product_id);
			for ($j = 0; $j < count($productDetails[$i]['components']); $j++) {
				if (!empty($productDetails[$i]['components'][$j]['rule'])) {
					$rules[$k] = $productDetails[$i]['components'][$j]['rule'];
					$k++;
				}
			}
		}
		$rules = array_keys(array_count_values($rules));
		$rulesCount = count($rules);
		$this->smarty->assign('rules', $rules);
		$this->smarty->assign('rulesCount', $rulesCount);

		$this->smarty->assign('unitTypeName', $unittypeDetails);
		$mixValidatorOptimized = new MixValidatorOptimized();
		$mixOptimizedValidatorResponce = $mixValidatorOptimized->isValidMix($mixOptimized);
		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));
		$companyDetails = $company->getCompanyDetails($companyID);
		$this->smarty->assign('companyDetails', $companyDetails);
		$this->smarty->assign('unittypeObj', $unittype);
		$this->smarty->assign('dailyLimitExceeded', $mixOptimizedValidatorResponce->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixOptimizedValidatorResponce->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixOptimizedValidatorResponce->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixOptimizedValidatorResponce->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixOptimizedValidatorResponce->getFacilityAnnualLimitExceeded());
		$this->smarty->assign('expired', $mixOptimizedValidatorResponce->isExpired());
		$this->smarty->assign('preExpired', $mixOptimizedValidatorResponce->isPreExpired());

		$this->smarty->display("tpls/mixLabel.tpl");
	}

	protected function addEdit($action, $departmentID) {
		$form = $this->getFromPost();
		
		/** protecting from xss * */
		foreach ($form as $key => $value) {
			$form[$key] = Reform::HtmlEncode($value);
		}

		/** show modules * */
		$company = new Company($this->db);
		$facility = new Facility($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);
		
		$pfpmanager = new PFPManager($this->db);
		
		
		
		$currentPfpType = new PfpTypes($this->db);
		$currentPfpType->id = 0;
		$currentPfpType->name = 'all';
		$currentPfpType->facility_id = 0;
		

		//$department = new Department($this->db);
		$department = new \VWM\Hierarchy\Department($this->db, $departmentID);
		$facilityID = $department->getFacilityId();
		$pfpTypes = $department->getPfpTypes();
		
		$pfps = $pfpmanager->getListAssigned($companyID, null, null, 0, 0, $selectedPfpType);		
		
		$this->smarty->assign("pfps", json_encode($pfps));
		
		$selectedPfpType = $this->getFromRequest("selectedPfpType");
		if(is_null($selectedPfpType)){
			$selectedPfpType = $pfpTypes[0]->name;
		}
		
		$this->smarty->assign('selectedPfpType', $selectedPfpType);
		$this->smarty->assign('currentPfpType', $currentPfpType);
		$this->smarty->assign('pfpTypes', $pfpTypes);
		
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach ($moduleMap as $key => $module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show', $showModules);


		if ($this->isModuleWasteStream($companyID)) { //	OK, this company has access to waste streams module, so let's setup..
			$isMWS = true;
		} else {
			$isMWS = false;
		}

		/** Mix need to create only if edit. when add new mix or product - mix is not created yet * */
		if ($action == "edit" or $action == "EditAddItem") {
			$mixID = $this->getFromRequest("id");
			$optMix = new MixOptimized($this->db, $mixID);

			$optMix->isMWS = $isMWS;
			$optMix->getDepartment();

			/** Initialize facility, company and equipment */
			$optMix->getFacility();
			$optMix->getEquipment();
			$optMix->getCompany();

			if ($isMWS === true) {
				$result = $this->prepareShowWasteForSmarty((count($form) > 0), $optMix->facility_id, $optMix->company->company_id, $mixID);

				foreach ($result['waste_streams'] as $key => $value) {	//Assign to smarty: storageOverflow,deletedStorageValidation,isDeletedStorageError,wasteStreamsList,wasteStreamsWithPollutions,storages
					$this->smarty->assign($key, $value);
				}

				if (isset($result['waste_streams']['storageError']) || ($result['waste_streams']['isDeletedStorageError'] == 'true')) {
					$storagesFailed = true;
				}
				$wasteArr = $result['waste_arr'];
			}

			/** Initialize waste * */
			$optMix->iniWaste($isMWS); // TODO: Доделать если MWS выключен
			$optMix->iniRecycle($isMWS);
		}
		//Init all wastes list for smarty
		if ($isMWS) {
			$result = $this->prepare4MixAdd((count($form) > 0) /* IsForm */, $facilityID, $companyID);
			foreach ($result as $key => $value) {
				$this->smarty->assign($key, $value);
			}

			if (isset($result['storageError']) || ($result['isDeletedStorageError'] == 'true')) {
				$storagesFailed = true;
			}
		}

		if (count($form) > 0) {

			switch ($action) {
				case "edit":
					$this->confirmSaveMix($form, $storagesFailed, $isMWS, $optMix, $checkIsUnique = false);
					break;
				case "addItem":
					$this->confirmAdd($form, $isMWS);
					break;
				case "saveMix":
					$this->confirmSaveMix($form, $storagesFailed, $isMWS);
					break;
				case "EditAddItem":
					$this->editMix_AddProduct($form, $optMix);
					break;
			}
		} else {

			switch ($action) {
				case "edit":
					$this->showEdit($optMix, $isMWS);
					break;
				case "addItem":
					$this->showAdd($departmentID);
					break;
			}
		}

		$jsSources = array(
			//TODO: why? tooltip will be injected with special object
			'modules/js/jquery.simpletip-1.3.1.pack.js',
			'modules/js/flot/jquery.flot.js',
			'modules/js/mixValidator.js?rev=jun22',
			'modules/js/productObj.js',
			'modules/js/productCollection.js',
			'modules/js/mixObj.js?rev=jun22',
			'modules/js/addUsage.js?rev=sep06',
			'modules/js/Utils.js?rev=sep06',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js');
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array(
			'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css',
			);
		$this->smarty->assign('cssSources', $cssSources);

		if ($action == "EditAddItem") {
			$action = "edit";
		} else if ($action == "saveMix") {
			$action = "addItem";
		}

		if ($_GET['debug']) {
			$this->smarty->assign('debug', true);
		}
		
		// i want to add tooltip plugin
		$libraryInjection = new LibraryInjection($this->smarty);
		$libraryInjection->injectToolTip(); 
		
		//TODO: inject libraries like this
		// $this->getLibraryInjection()->injectToolTip();
		
		// Repair order or Working Order
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();     
        $companyNew = new VWM\Hierarchy\Company($this->db, $companyID);
        $repairOrderLabel = $companyNew->getIndustryType()->getLabelManager()->getLabel($companyLevelLabelRepairOrder->label_id)->getLabelText(); 
            
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		
		// for displaying voc unit type
		$unittype = new Unittype($this->db);
		$companyDetails = $company->getCompanyDetails($companyID);
		$vocUnitType = $unittype->getNameByID($companyDetails["voc_unittype_id"]);
		$this->smarty->assign('vocUnitType', $vocUnitType);
		$this->smarty->assign('sendFormAction', 
				'?action=' . $action . '&category=' . $this->category . (($action == 'addItem') ? '&departmentID=' . $departmentID : '&id=' . $this->getFromRequest('id')));
		$this->smarty->assign('tpl', 'tpls/addUsageNew.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	/**
	 *
	 *
	 * @param unknown_type $form
	 * @param unknown_type $storagesFailed
	 * @param unknown_type $isMWS
	 * @param unknown_type $optMix = null. If not null - edit mix. Is null - add new mix
	 */
	protected function confirmSaveMix($form, $storagesFailed, $isMWS, $optMix = null, $checkIsUnique = true) {

		// valide reg data
		$mix = $this->getMixByPOSTData($form);

		if (!isset($mix->department_id)) {
			$mix->department_id = $optMix->department_id;
			echo "<h1>Set department_id: {$optMix->department_id}</h1>";
		}

		if (isset($optMix)) {
			$mix->mix_id = $optMix->mix_id;
		}

		$validationRes['summary'] = true;
		$validation = new Validation($this->db);
		$dataForValidate = $this->prepareDataForValidation($mix);
		$validationRes = $validation->validateRegData($dataForValidate);

		if ($storagesFailed === true) {
			$validationRes['summary'] = 'false';
		}

		if ($checkIsUnique) {
			if (!$validation->isUniqueUsage(Array("description" => $mix->description, "department_id" => $mix->department_id))) {
				$validationRes['summary'] = 'false';
				$validationRes['description'] = 'alredyExist';
			}
		}

		if ($mix->equipment_id == null) {
			$validationRes['summary'] = 'false';
			$validationRes['equipment'] = 'noEquipment';
		}

		$products = $this->getProductsByPOST();

		if (!$products) {
			$products = null;
		}

		$validProductsResult = $this->validateProducts($products);

		if (!$validProductsResult) {
			//products error!!
			$validationRes['summary'] = 'false';
		}

		if ($products == null or $products === false or count($products) == 0 or !$validProductsResult) {
			$validationRes['summary'] = 'false';
			$validationRes['products'] = 'noProducts';
		}

		$count = count($products);

		for ($i = 0; $i < $count; $i++) {
			$products[$i]->initializeByID($products[$i]->product_id);
		}

		$mix->products = $products;

		if ($isMWS) {
			$mWasteStreams = new MWasteStreams();

			$params = array(
				'db' => $this->db,
				'xnyo' => $this->xnyo,
				'isForm' => true,
				'facilityID' => $mix->getDepartment()->getFacilityID(),
				'companyID' => $mix->getCompany()->company_id //DIFFERENCE!!
			);

			$result = $mWasteStreams->prepare4mixAdd($params);

			$wastesFromPost = $this->getWastesFromPost($form);
			echo "<h1>Wastes from post:</h1>";
			var_dump($wastesFromPost);
			echo "<h1>Wastes from MWasteStreams->prepare4mixAdd:</h1>";
			var_dump($mWasteStreams->resultParams['waste']);
			$mix->waste_json = json_encode($mWasteStreams->resultParams['waste']);
			echo "<h1>WASTE JSON</h1>";
			var_dump($mix->waste_json);
		} else {
			$wasteFromPost = $this->getSingleWasteFromPost($form);
			$mix->waste = $wasteFromPost;
		}

		$mixCalcError = $mix->calculateCurrentUsage($isMWS);

		if ($mixCalcError != null) {

			if ($mixCalcError['isDensityToVolumeError']) {
				$validationRes['conflict'] = 'density2volume';
				$validationRes['summary'] = 'false';
			}
			if ($mixCalcError['isDensityToWeightError']) {
				$validationRes['conflict'] = 'density2weight';
				$validationRes['summary'] = 'false';
			}
			$validationRes['warning'] = false;
			foreach ($mixCalcError['isVocwxOrPercentWarning'] as $productID => $productWarning) {
				if ($productWarning) {
					$validationRes['warning'] = true;
				}
			}
			if ($validationRes['warning']) {
				$validationRes['warnings2products'] = $mixCalcError['isVocwxOrPercentWarning'];
			}
			if ($mixCalcError['isWastePercentAbove100']) {
				$validationRes['summary'] = 'false';
				$validationRes['waste']['percent'] = 'failed';
			}
		}

		$mixValidator = new MixValidatorOptimized();
		$mixValidatorResponse = $mixValidator->isValidMix($mix);

		if ($validationRes['summary'] != 'false') { // Add Mix - No validating errors
			$newMixID = $mix->save($isMWS, $optMix);
			echo "<h1>mix #$newMixID saved!</h1>";
			//If module 'Waste Streams' is disabled, waste is already saved in mix->save func
			if ($isMWS) {
				echo "<h1>Waste Streams saved to mix #$newMixID!</h1>";
				$mWasteStreams->prepareSaveWasteStreams(array('id' => $newMixID, 'db' => $this->db));
			}

			header("Location: /vwm/?action=browseCategory&category=department&id={$mix->department_id}&bookmark=mix");
		} else {
			/* Validation erors */
			echo "<h1>Validation errors {$mix->department_id}</h1>";
			//Display all again with validation errors



			/** Load equipment list, cause confirmAdd doesnot do that */
			$equipment = new Equipment($this->db);

			var_dump($optMix);
			$equipmentList = $equipment->getEquipmentList($mix->department_id);
			var_dump($equipmentList);

			$this->confirmAdd($form);

			$this->smarty->assign('equipment', $equipmentList);
		}

		$this->smarty->assign('validStatus', $validationRes);
	}

	protected function prepareDataForValidation($mix) {

		$data = Array(
			'voc' => $mix->voc,
			'voclx' => $mix->voclx,
			'vocwx' => $mix->vocwx,
			'description' => $mix->description,
			'creationTime' => $mix->creation_time
		);
		return $data;
	}


	/**
	 * Prepares everything when showing add mix page
	 * @param int $departmentID
	 */
	protected function showAdd($departmentID) {

		$request = $this->getFromRequest();
		
		$request['id'] = $departmentID;
		$request['parent_category'] = $this->parent_category;

		$department = new Department($this->db);
		$departmentDetails = $department->getDepartmentDetails($departmentID);

		$company = new Company($this->db);
		$companyID = $company->getCompanyIDbyDepartmentID($departmentID);

		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($departmentID);
		$this->smarty->assign('equipment', $equipmentList);
		
		$department = new VWM\Hierarchy\Department($this->db, $departmentID);
		
		$apmethodObject = new Apmethod($this->db);
		$APMethod = $department->getDefaultAPMethod();
		//$APMethod = $apmethodObject->getDefaultApmethodDescriptions($companyID);
		if (!isset($APMethod) or empty($APMethod)) {
			$APMethod = $apmethodObject->getApmethodList(null);
		}

		//	Get rule list
		$rule = new Rule($this->db);
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $companyID, $departmentDetails['facility_id'], $departmentID);
		
		$this->smarty->assign('rules', $customizedRuleList);

		//	Getting Product list
		$productsListGrouped = $this->getProductsListGrouped($companyID);

		$this->smarty->assign('products', $productsListGrouped);

		$product = new Product($this->db);
		$productInfo = $product->getProductInfoInMixes($productList[0]['product_id']);

		$res = $this->getDefaultTypesAndUnitTypes($companyID);
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];

		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('companyID', $companyID);

		$this->smarty->assign('APMethod', $APMethod);
		$this->smarty->assign('defaultAPMethod', $defaultAPMethod);

		$data = $this->getClearDataForAddItem();
		$data->product_desc = $productInfo['desc'];
		$data->coating = $productInfo['coat'];

		$unittype = new Unittype($this->db);
		$unitTypeClass = $unittype->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		
		
		$department->setUnitTypeClass($unitTypeClass);
		$unittypeListDefault = $department->getUnitTypeList();
		
		if (empty($unittypeListDefault)) {
			$unittypeListDefault = $unittype->getUnittypeListDefault($unitTypeClass);
		}
		
		$data->unitTypeClass = $unitTypeClass;
		$mix = new MixOptimized($this->db);
		$mix->iniWaste(false, $unittypeListDefault);
		$mix->iniRecycle(false, $unittypeListDefault);
		$mix->department_id = $departmentID;
		$mix->creation_time = strtotime("now");
		
		if($request['stepID']!=0){
			$mix->setStepId($request['stepID']);
		}
		
		$data->creation_time = $mix->creation_time;
		$data->dateFormatForCalendar = $mix->dateFormatForCalendar;
		$data->waste = $mix->waste;
		$data->recycle = $mix->recycle;
 
		//	do I need to add work order suffix?
		$repairOrderIteration = 0;
		$mixParentID = $this->getFromRequest('parentMixID');
		
		// this mix was added to WO , add suffix ?
		$repairOrderId = $this->getFromRequest('repairOrderId');

		//get procces for mix
		$companyNew = new VWM\Hierarchy\Company($this->db, $companyID);
        $industryTypeId = $companyNew->getIndustryType();
		
		$workOrder = WorkOrderFactory::createWorkOrder($this->db, $industryTypeId->id, $repairOrderId);
		
		if($mixParentID) {
			$parentMix = new MixOptimized($this->db, $mixParentID);
			$repairOrderIteration = $parentMix->iteration + 1;
			$data->description = $parentMix->generateNextIterationDescription();
		}else{
			$data->description = $workOrder->getNumber();
		}
		
		$process = $workOrder->getProcess();
				
		if (!is_null($process->getId()) && $request['stepID']!=0) {
			$step = new \VWM\Apps\Process\Step($this->db, $request['stepID']);
			if($step) {
				$data->spent_time = $step->getTotalSpentTime();
				$resources = $step->getResources();
				if ($resources) {
					$data->notes = $resources[0]->getDescription();
				}
				$this->smarty->assign('stepID', $step->getId());
			} else {
				// that is ok, just continue
			}					
		}

		
		$this->smarty->assign('repairOrderIteration', $repairOrderIteration);
		$this->smarty->assign('mixParentID', $mixParentID);
		
		$this->smarty->assign('repairOrderId', $repairOrderId);
		
		$this->smarty->assign('data', $data);
		$this->smarty->assign('unittype', $unittypeListDefault);
	}

	protected function getClearDataForAddItem() {
		$data->voc = '0.00';
		$data->voclx = '0.00';
		$data->vocwx = '0.00';
		$data->creation_time = date("m-d-Y");
		$data->waste = false;
		$data->recycle = false;
		return $data;
	}

	/**
	 * Prepares everything when showing edit mix page
	 * @param MixOptimized $optMix
	 * @param boolean $isMWS
	 */
	protected function showEdit(MixOptimized $optMix, $isMWS) {
		$optMix->setTrashRecord(new Trash($this->db));
		//	Get rule list
		$rule = new Rule($this->db);
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $optMix->company->company_id, $optMix->facility_id, $optMix->department_id);
		$this->smarty->assign('rules', $customizedRuleList);

		/** Collect unittypeDetails for smarty * */
		foreach ($optMix->products as $p) {
			$unittypeDetails2[] = $p->unittypeDetails;
		}
		$this->smarty->assign('unitTypeName', $unittypeDetails2);

		$mixCalcError = $optMix->calculateCurrentUsage();

		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($optMix->department_id);

		$productsListGrouped = $this->getProductsListGrouped($optMix->company->company_id);

		$unittypeList = $this->getUnitTypeList($optMix->company->company_id);

		$apmethodObject = new Apmethod($this->db);
		$APMethod = $apmethodObject->getDefaultApmethodDescriptions($optMix->company->company_id);
		if (!isset($APMethod) or empty($APMethod)) {
			$APMethod = $apmethodObject->getApmethodList(null);
		}

		$res = $this->getDefaultTypesAndUnitTypes($optMix->company->company_id);
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];

		$this->smarty->assign('repairOrderIteration', $optMix->iteration);
		$this->smarty->assign('mixParentID', $optMix->parent_id);

		$this->smarty->assign('data', $optMix);
		$this->smarty->assign('equipment', $equipmentList);
		$this->smarty->assign('products', $productsListGrouped);
		$this->smarty->assign('unittype', $unittypeList);
		$this->smarty->assign('productCount', count($optMix->products));
		$this->smarty->assign('productsAdded', $optMix->products);
		$this->smarty->assign('APMethod', $APMethod);
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		$this->smarty->assign('companyID', $optMix->company->company_id);
	}

	protected function editMix_AddProduct($form, $optMix) {

		$this->confirmAdd($form);

		$res = $this->getDefaultTypesAndUnitTypes($optMix->company->company_id);
		$typeEx = $res['typeEx'];
		$companyEx = $res['companyEx'];
		$unitTypeEx = $res['unitTypeEx'];

		$equipment = new Equipment($this->db);
		$equipmentList = $equipment->getEquipmentList($optMix->department_id);

		$this->smarty->assign('equipment', $equipmentList);
		$this->smarty->assign('typeEx', $typeEx);
		$this->smarty->assign('jsTypeEx', json_encode($typeEx));
		$this->smarty->assign('unitTypeEx', $unitTypeEx);
		$this->smarty->assign('companyEx', $companyEx);
		echo "CompanyID: {$optMix->company->company_id}";
		$this->smarty->assign('companyID', $optMix->company->company_id);
	}

	protected function confirmAdd($form, $isMWS) {
		$optMix = $this->getMixByPOSTData($form);

		if ($isMWS) {
			$wastesFromPost = $this->getWastesFromPost($form);
			$optMix->waste_json = json_encode($wastesFromPost);
		} else {
			$wasteFromPost = $this->getSingleWasteFromPost($form);
			$optMix->waste = $wasteFromPost;
		}

		$validResult = $this->validateProductByForm($form);
		$this->smarty->assign('validStatus', $validResult);
		$this->showAdd($form['department_id']);
		$data = $this->smarty->get_template_vars('data');
		$data->description = $_POST['description'];
		$data->exempt_rule = $_POST['exemptRule'];
		$products = $this->getProductsByPOST();

		if (!$products) {
			$products = Array();
		}

		if ($validResult['summary'] != 'false') { // If adding product valid, add to cart
			$product = new MixProduct($this->db);
			$product->initializeByID($form['selectProduct']);
			$product->quantity = $form['quantity'];
			$unittype = new Unittype($this->db);
			$unittypeDetails = $unittype->getUnittypeDetails($form['selectUnittype']);
			$product->unit_type = $unittypeDetails['name'];
			$product->unittypeDetails = $unittypeDetails;
			$product->json = json_encode($product);
			$products[] = $product;
		}

		$optMix->products = $products;

		$unittype = new Unittype($this->db);

		$count = count($optMix->products);
		for ($i = 0; $i < $count; $i++) {
			$optMix->products[$i]->initializeByID($optMix->products[$i]->product_id);
			$optMix->products[$i]->initUnittypeList($unittype);
		}

		$calcMixResult = $optMix->calculateCurrentUsage($isMWS);
		/** From Post* */
		$optMix->description = $_POST['description'];
		$optMix->exempt_rule = $_POST['exemptRule'];
		$mixValidatorOptimized = new MixValidatorOptimized(true);
		$mixValidatorResponse = $mixValidatorOptimized->isValidMix($optMix);
		$this->smarty->assign('dailyLimitExceeded', $mixValidatorResponse->isDailyLimitExceeded());
		$this->smarty->assign('departmentLimitExceeded', $mixValidatorResponse->isDepartmentLimitExceeded());
		$this->smarty->assign('facilityLimitExceeded', $mixValidatorResponse->isFacilityLimitExceeded());
		$this->smarty->assign('departmentAnnualLimitExceeded', $mixValidatorResponse->getDepartmentAnnualLimitExceeded());
		$this->smarty->assign('facilityAnnualLimitExceeded', $mixValidatorResponse->getFacilityAnnualLimitExceeded());
		$this->smarty->assign('data', $optMix);
		$this->smarty->assign('productCount', count($products));
		$this->smarty->assign('productsAdded', $products);
	}

	protected function getSingleWasteFromPost($form) {
		$waste['value'] = $form['wasteValue'];
		$waste['unittypeClass'] = $form['selectWasteUnittypeClass'];
		$waste['unittypeID'] = $form['selectWasteUnittype'];
		$unittype = new Unittype($this->db);
		$waste['unitTypeList'] = $unittype->getUnittypeListDefault($waste['unittypeClass']);
		return $waste;
	}

	protected function getWastesFromPost($form) {

		$wasteCount = $_POST['wasteStreamCount'];
		$wastes = Array();

		for ($i = 0; $i < $wasteCount; $i++) {

			$waste['id'] = $_POST["wasteStreamSelect_$i"];
			$waste['storage_id'] = $_POST["selectStorage_$i"];

			$pollutionCount = $_POST["pollutionCount_$i"];
			$waste['count'] = $pollutionCount;
			//echo "<h3> pollutionCount $pollutionCount</h3>";

			$quantityWithoutPollutions = $_POST["quantityWithoutPollutions_$i"];
			if (isset($quantityWithoutPollutions)) {

				$waste['value'] = $quantityWithoutPollutions;
				$waste['unittypeClass'] = $_POST["selectWasteUnittypeClassWithoutPollutions_$i"];
				$waste['unittypeID'] = $_POST["selectWasteUnittypeWithoutPollutions_$i"];
			} else {
				$pollutions = Array();
				for ($j = 0; $j < $pollutionCount; $j++) { /* Adding pollutions */
					$pollution['id'] = $_POST["selectPollution_{$i}_{$j}"];
					$pollution['unittypeClass'] = $_POST["selectWasteUnittypeClass_{$i}_{$j}"];
					$pollution['unittypeID'] = $_POST["selectWasteUnittype_{$i}_{$j}"];
					$pollution['value'] = $_POST["quantity_{$i}_{$j}"];
					$waste[$j] = $pollution;
				}
			}

			$wastes[] = $waste;
			unset($waste);
		}
		return $wastes;
	}

	protected function prepare4MixAdd($isForm, $facilityID, $companyID) {
		$mWasteStreams = new MWasteStreams();
		$params = array(
			'db' => $this->db,
			'xnyo' => $this->xnyo,
			'isForm' => $isForm,
			'facilityID' => $facilityID,
			'companyID' => $companyID  //DIFFERENCE!!
		);
		if (isset($_GET['id'])) {
			$params['id'] = $this->getFromRequest('id');
		}
		$result = $mWasteStreams->prepare4mixAdd($params);
		return $result;
	}

	public function actionValidateProductAjax() {
		$unittypeID = $_REQUEST['unittypeID'];
		$productID = $_REQUEST['productID'];

		if ($_REQUEST['debug']) {
			var_dump($product);
		}

		$validation = new Validation($this->db);
		$productConflitcs = array();

		$isProdConflict = $validation->checkWeight2Volume($productID, $unittypeID);
		if ($isProdConflict !== true) {
			$validStatus['summary'] = 'false';
			$validStatus['description'] = $isProdConflict;
		} else {
			$validStatus['summary'] = 'true';
		}

		echo json_encode($validStatus);
		exit;
	}

	protected function validateProductByForm($form) {

		$validation = new Validation($this->db);

		$validStatus = $validation->validateRegData(Array("quantity" => $form['quantity']));
		// we have new validation in Mix->calculateCurrentUsage
		//but we still need those - to disallow product adding

		$isProdConflict = $validation->checkWeight2Volume($form['selectProduct'], $form['selectUnittype']);
		if ($isProdConflict !== true) {
			$validStatus['summary'] = 'false';
			$validStatus['description'] = $isProdConflict;
		}
		return $validStatus;
	}

	protected function validateProducts($products) {

		$validation = new Validation($this->db);
		$count = count($products);
		$validSummary = true;
		for ($i = 0; $i < $count; $i++) {

			$products[$i]->valid = true;
			$validStatus = $validation->validateRegData(Array("quantity" => $products[$i]->quantity));
			if ($validStatus['summary'] != 'true') {
				$products[$i]->valid = false;
				$validSummary = false;
			}
			// we have new validation in Mix->calculateCurrentUsage
			//but we still need those - to disallow product adding
			$isProdConflict = $validation->checkWeight2Volume($products[$i]->product_id, $products[$i]->unittypeDetails->unittype_id);

			if ($isProdConflict) {
				$products[$i]->valid = false;
				$validSummary = false;
			}
		}
		return $validSummary;
	}

	protected function getProductsByPOST() {

		if ($_POST['addingProductsArr']) {

			foreach ($_POST['addingProductsArr'] as $jsProduct) {
				$product = json_decode($jsProduct);
				$product->json = $jsProduct;

				//Convert std class (product) to MixProduct ($mixProduct)
				$mixProduct = new MixProduct($this->db);
				// set public properties
				foreach ($product as $key => $value) {
					if (property_exists($mixProduct, $key)) {
						try {// Property may be not public
							$mixProduct->$key = $value;
						} catch (Exception $e) {

						}
					}
				}

				$mixProduct->json = $product->json;

				/** Convert unittypeDetails to array, cause of json_decode make it object =( * */
				$mixProduct->unittypeDetails = Array(
					"unittype_id" => $product->unittypeDetails->unittype_id,
					"name" => $product->unittypeDetails->name,
					"description" => $product->unittypeDetails->description,
					"formula" => $product->unittypeDetails->formula,
					"type_id" => $product->unittypeDetails->type_id,
					"type" => $product->unittypeDetails->type
						);

				$products[] = $mixProduct;
			}

			return $products;
		} else {
			return false;
		}
	}

	protected function getMixByPOSTData($form) {
		$optMix = new MixOptimized($this->db);

		$optMix->department_id = $form['department_id'];
		$optMix->equipment_id = $form['selectEquipment'];
		$optMix->voc = $form['voc'] ? $form['voc'] : "0.00";
		$optMix->voclx = $form['voclx'] ? $form['voclx'] : "0.00";
		$optMix->vocwx = $form['vocwx'] ? $form['vocwx'] : "0.00";
		$optMix->description = $form['description'];
		$optMix->unittype = $form['selectUnittype']; // not exists
		$optMix->rule = $form['rule'];
		$optMix->user_id = 0; //not exists
		$optMix->exempt_rule = $form['exemptRule'];
		$optMix->creation_time = $form['creationTime'];
		$optMix->apmethod_id = $form['selectAPMethod'];
		$optMix->valid = true;
		$optMix->unittypeClass = $form['selectUnittypeClass'];

		return $optMix;
	}

	protected function getDefaultTypesAndUnitTypes($companyID) {

		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		$companyEx = 1;
		if (!$unitTypeEx) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
			$companyEx = 0;
		}

		$k = 1;
		$count = 1;
		$flag = 1;
		$typeEx = Array();

		// 80% of U.S. customers use the system USAWeight, so make it default
		//$usWgt = Array('OZS', 'LBS', 'GRAIN', 'CWT');
		$usWgt = Array('7', '2', '12', '20');
		for ($ii = 0; $ii < count($unitTypeEx); $ii++) {
			for ($jj = 0; $jj < count($usWgt); $jj++) {
				if ($unitTypeEx[$ii]['unittype_id'] == $usWgt[$jj]) {
					$typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$ii]['unittype_id']);
				}
			}
		}
		if ($typeEx[0] == '') {
			$typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		}

		while ($unitTypeEx[$k]) {
			$idn = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$k]['unittype_id']);

			for ($j = 0; $j < $count; $j++) {
				if ($idn == $typeEx[$j]) {
					$flag = 0;
					break;
				}
			}
			if ($flag) {
				$typeEx[$count] = $idn;
				$count++;
			}
			$k++;
			$flag = 1;
		}

		return Array("typeEx" => $typeEx, "companyEx" => $companyEx, "unitTypeEx" => $unitTypeEx);
	}

	protected function getDefaultApMethod($companyID) {

		$apmethodObject = new Apmethod($this->db);
		$APMethod = $apmethodObject->getDefaultApmethodDescriptions($companyID);
		return $APMethod;
	}

	/**
	 * Loads products grouped by supplier for dropdown
	 * @param integer $companyID
	 * @param array $excludedProducts - array of product id to exclude (for smarty in addPFP.tpl)
	 */
	protected function getProductsListGrouped($companyID, $excludedProducts = false) {
		$product = new Product($this->db);
		return $product->getFormatedProductList($companyID, $excludedProducts);
	}

	protected function getUnitTypeList($companyID) {
		$unittype = new Unittype($this->db);
		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		if ($unitTypeEx === null) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
		}
		$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
		return $unittypeList;
	}

	protected function prepareShowWasteForSmarty($isForm, $facilityID, $companyID, $mixID) {//shows that we can access madule Waste Streams
		$mWasteStreams = new MWasteStreams();
		$params = array(
			'db' => $this->db,
			'xnyo' => $this->xnyo,
			'isForm' => $isForm,
			'facilityID' => $facilityID,
			'companyID' => $companyID,
			'id' => $mixID
		);
		$result['waste_streams'] = $mWasteStreams->prepare4mixAdd($params);
		$result['waste_arr'] = $mWasteStreams->resultParams['waste'];
		return $result;
	}

	protected function isModuleWasteStream($companyID) {

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach ($moduleMap as $key => $module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show', $showModules);
		$isMWS = false;
		if ($showModules['waste_streams']) {
			return $showModules['waste_streams'];
		} else {
			return false;
		}
	}

}

?>
