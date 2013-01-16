<?php
use VWM\Label\CompanyLevelLabel;
use VWM\Apps\WorkOrder\Factory\WorkOrderFactory;
use VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder;
use VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder;
use \VWM\Apps\Process\Process;
use VWM\Apps\Process\StepInstance;

class CRepairOrder extends Controller {

    public function __construct ($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'repairOrder';
        $this->parent_category = 'facility';
    }

    protected function actionViewDetails() {

		$params = array("bookmark" => "repairOrder");
		
		if($this->getFromRequest('departmentID')) {
			$category = 'department';
			$categoryId = $this->getFromRequest('departmentID');
			$department = new VWM\Hierarchy\Department($this->db, $categoryId);
			$facility = $department->getFacility();
		} elseif($this->getFromRequest('facilityID')) {
			$category = 'facility';
			$categoryId = $this->getFromRequest('facilityID');
			$facility = new VWM\Hierarchy\Facility($this->db, $categoryId);
		} else {
			throw new Exception('404');
		}
		$this->setNavigationUpNew($category, $categoryId);
		$this->setListCategoriesLeftNew($category, $categoryId, $params);
		$this->setPermissionsNew('viewRepairOrder');

        $repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));                

		// get child mixes		  
        $mixTotalPrice = 0;
		$mixTotalSpentTime = 0;
        $mixes = $repairOrder->getMixes(); 
		foreach ($mixes as $mix) {
			//TODO: this is not correct
			$mix->price = $mix->getMixPrice();
			$mixTotalPrice += $mix->price;
			$mixTotalSpentTime += $mix->spent_time;			
		}
				
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();     
        $company = $facility->getCompany();
        $repairOrderLabel = $company->getIndustryType()->getLabelManager()
				->getLabel($companyLevelLabelRepairOrder->label_id)
				->getLabelText();
            
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);

		// get wo departments
        $repairOrderManager = new RepairOrderManager($this->db);
        $woDepartments = $repairOrderManager->getDepartmentsByWo($this->getFromRequest('id'));
        if (!$woDepartments) {
            // we shoul get all department list
			//	TODO: sorry old school here
			$facilityOldObj = new Facility($this->db);
            $woDepartments = $facilityOldObj->getDepartmentList($facility->getFacilityId());
        }
		
        $departmetsName = array();
        foreach ($woDepartments as $departmentId) {
            $departmentDetails = new VWM\Hierarchy\Department($this->db, $departmentId);
            $departmetsName[] = $departmentDetails->getName();
        }
        $woDepartments = implode(",", $departmetsName);
        $this->smarty->assign('woDepartments', $woDepartments);
        
        $workOrder = WorkOrderFactory::createWorkOrder($this->db,
				$company->getIndustryType()->id);
		
		
        $this->smarty->assign('instanceOfWorkOrder', $workOrder);
           		
        $this->smarty->assign('backUrl', "?action=browseCategory&category={$category}&id={$categoryId}&bookmark=repairOrder");
        $this->smarty->assign('deleteUrl', "?action=deleteItem&category=repairOrder&id={$this->getFromRequest('id')}&{$category}ID={$categoryId}");
        $this->smarty->assign('editUrl', "?action=edit&category=repairOrder&id={$this->getFromRequest('id')}&{$category}ID={$categoryId}");
        $this->smarty->assign('mixList', $mixes);
		
		$this->smarty->assign('processName', $repairOrder->getRepairOrderProcessName());
		$this->smarty->assign('repairOrder', $repairOrder);
		$this->smarty->assign('mixTotalPrice', $mixTotalPrice);
		$this->smarty->assign('mixTotalSpentTime', $mixTotalSpentTime);
                
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * bookmarkDRepairOrder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails,
	 * $bookmarkDRepairOrder, $moduleMap
     */
    protected function bookmarkDRepairOrder($vars) {
		
        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
            
        $facility = new Facility($this->db);
		if($this->getFromRequest('category') == 'department') {
			$department = new VWM\Hierarchy\Department($this->db,
					$departmentDetails['department_id']);
		}
        //	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$facility->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

		if($this->getFromRequest('category') == 'department') {
			$repairOrderCount = $department->countRepairOrderInDepartment();
		} else {
			$repairOrderCount = $facility->countRepairOrderInFacility(
					$facilityDetails['facility_id']);
		}
        
        $url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($repairOrderCount);
		$pagination->url = $url; 
        $this->smarty->assign('pagination', $pagination);

		if($this->getFromRequest('category') == 'department') {
			$repairOrderList = $department->getRepairOrdersList($pagination);
		} else {
			$repairOrderList = $facility->getRepairOrdersList(
					$facilityDetails['facility_id'],$pagination);
		}
        
        $company = new VWM\Hierarchy\Company($this->db, $facilityDetails["company_id"]);
        $industryTypeId = $company->getIndustryType();
        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $industryTypeId->id);
        $this->smarty->assign('instanceOfWorkOrder', $workOrder);
        
        if ($repairOrderList) {
            for ($i = 0; $i < count($repairOrderList); $i++) {
				if($this->getFromRequest('category') == 'department') {
					$url = "?action=viewDetails&category=repairOrder&id=" . $repairOrderList[$i]->id . "&departmentID=" . $department->getDepartmentId();					
				} else {
					$url = "?action=viewDetails&category=repairOrder&id=" . $repairOrderList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];				                	
				}
				$repairOrderList[$i]->url = $url;
            }
        }
		
        $this->smarty->assign("childCategoryItems", $repairOrderList);

        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
        $this->smarty->assign('tpl', 'tpls/repairOrderList.tpl');
    }

    protected function actionAddItem() {
		$request = $this->getFromRequest();
        $request["id"] = "false";                

		$facility = new Facility($this->db);        
		$params = array("bookmark" => "repairOrder");

		if($this->getFromRequest('departmentID')) {
			$category = "department";
			$categoryId = $this->getFromRequest('departmentID');
			
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails(
					$this->getFromRequest("departmentID"));

			$facilityDetails = $facility->getFacilityDetails(
					$departmentDetails['facility_id']);
			$this->setPermissionsNew('viewDepartment');
			
		} elseif($this->getFromRequest('facilityID')) {
			$category = "facility";
			$categoryId = $this->getFromRequest('facilityID');
						
			$facilityDetails = $facility->getFacilityDetails(
					$this->getFromRequest("facilityID"));
			$this->setPermissionsNew('viewFacility');
		} else {
			throw new Exception('404');
		}

		//	Access control
		if (!$this->user->checkAccess($category, $categoryId)) {
			throw new Exception('deny');
		}
		$request['parent_id'] = $categoryId;
		$request['parent_category'] = $category;

		$this->setListCategoriesLeftNew($category, $categoryId, $params);
		$this->setNavigationUpNew($category, $categoryId);		

        $this->smarty->assign('request', $request);
		$this->smarty->assign('facilityDetails', $facilityDetails);
                      
		$companyId = $facilityDetails["company_id"];
        $companyNew = new VWM\Hierarchy\Company($this->db, $companyId);
        $industryTypeId = $companyNew->getIndustryType();
		
        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $industryTypeId->id);
		if($category == 'department' && !$departmentDetails['share_wo']) {
			$woDepartments_id = $departmentDetails['department_id'];
			$departmentIds = array($woDepartments_id);
		} else {			
			$departmentIds = $facility->getDepartmentList($facilityDetails['facility_id']);			
			$woDepartments_id = implode(",", $departmentIds);
		}
		
        $this->smarty->assign('data', $workOrder);
        $post = $this->getFromPost();
		
		//Get all Process
		$newFacility = new \VWM\Hierarchy\Facility($this->db, $facilityDetails['facility_id']);
		$processList = $newFacility->getProcessList();
		array_unshift($processList, new Process($this->db));
		
		$this->smarty->assign('processList', $processList);
		
		if (count($post) > 0) { 

            $facilityID = $post['facility_id'];
            $woDepartments_id = $post['woDepartments_id'];
			$woProcessId = $post['woProcessId'];
            $workOrder->setNumber($post['number']);
            $workOrder->setCustomer_name($post['repairOrderCustomerName']);
            $workOrder->setStatus($post['repairOrderStatus']);
            $workOrder->setDescription($post['repairOrderDescription']);
            $workOrder->setFacilityId($facilityID);
			if ($woProcessId != '') {
				$workOrder->setProcessID($woProcessId);
			}
			
			
			
            if ($workOrder instanceof AutomotiveWorkOrder) {
                $workOrder->setVin($post['repairOrderVin']);
            }
			$violationList = $workOrder->validate(); 
			if(count($violationList) == 0 && $woDepartments_id != '') {
				$this->db->beginTransaction();
				
				$woID = $workOrder->save();
				
				if (!empty($departmentIds)) {
					
                    // add empty mix for each facility department
                    $mixOptimized = new MixOptimized($this->db);
                    $mixOptimized->description = $post["number"];
                    $mixOptimized->wo_id = $woID;
                    $mixOptimized->iteration = 0;
                    $mixOptimized->facility_id = $facilityID; 
                    $mixOptimized->department_id = $departmentIds[0];
					
					//create new mix with process 
					if ($woProcessId != '') {
						$process = new \VWM\Apps\Process\Process($this->db);
						$process->setId($woProcessId);
						$process->load();
						$process->setCurrentStepNumber(1);
						$step = $process->getCurrentStep();
						$mixOptimized->spent_time = $step->getTotalSpentTime();
						$resources = $step->getResources();
						$mixOptimized->notes = $resources[0]->getDescription();
					}
                    $mixOptimized->save();
                }
                // set department to wo
                $woDepartments_id = explode(",", $woDepartments_id);
                $repairOrderManager = new RepairOrderManager($this->db);
                // i should unset all departments from wo at first
                $repairOrderManager->unSetDepartmentToWo($woID);
                // set departments to wo
                foreach ($woDepartments_id as $departmentId) {
                   $repairOrderManager->setDepartmentToWo($woID, $departmentId); 
                }
				
				$this->db->commitTransaction();
				// redirect
				if($this->getFromRequest('departmentID')) {
					header("Location: ?action=viewDetails&category=repairOrder&id=" . $workOrder->id . "&departmentID=" . $this->getFromRequest('departmentID') . "&notify=59");
				} else {
					header("Location: ?action=viewDetails&category=repairOrder&id=" . $workOrder->id . "&facilityID=" . $facilityID . "&notify=59");
				}
				
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $workOrder);
                if ($woDepartments_id == '') {
                    $this->smarty->assign('woDepartmentsError', true);
                }
			}																	
		}
		
        $this->smarty->assign('woDepartments', $woDepartments_id);

		

		
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();     
        $repairOrderLabel = $companyNew->getIndustryType()->getLabelManager()
				->getLabel($companyLevelLabelRepairOrder->label_id)->getLabelText();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
        
        //	set js scripts
        $jsSources = array(
            "modules/js/reg_country_state.js",
            "modules/js/saveItem.js",
            "modules/js/PopupWindow.js",
            "modules/js/addJobberPopups.js",
            "modules/js/checkBoxes.js",
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
            'modules/js/repairOrderManager.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

        $this->smarty->assign('pleaseWaitReason', "Recalculating repair orders at facility.");
        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionDeleteItem() {

        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $repairOrderID) {
                //	Access control
                if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
                    throw new Exception('deny');
                }
                $repairOrder = new RepairOrder($this->db, $repairOrderID);
                $delete = array();
                $delete["id"] = $repairOrder->id;
                $delete["number"] = $repairOrder->number;
                $delete["description"] = $repairOrder->description;
                $delete["customer_name"] = $repairOrder->customer_name;
                $delete["status"] = $repairOrder->status;
                $delete["facility_id"] = $repairOrder->facility_id;
				$delete["vin"] = $repairOrder->vin;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=repairOrder");
            //as ShowAddItem
            $params = array("bookmark" => "repairOrder");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    protected function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $repairOrder = new RepairOrder($this->db, $ID);
            $facilityId = $repairOrder->facility_id;
            // get work order mix id, we check if work order already has any mixes
            $mixOptimized = new MixOptimized($this->db);
            $mixIDs = $repairOrder->getMixes();
            if (count($mixIDs) < 2) {
                // we can delete only empty work order
                $repairOrder->delete();
                // delete empty mix
                $mixOptimized = new MixOptimized($this->db, $woId);
                $mixOptimized->delete();
            } else {
                header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=repairOrder&notify=49");
                die();
            }
        }

        if ($this->successDeleteInventories)
            header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=repairOrder&notify=48");
    }

    protected function actionEdit() {
		$request = $this->getFromRequest();
		$facility = new Facility($this->db);
        $department = new Department($this->db);
		
		if($this->getFromRequest('departmentID')) {
			$category = 'department';
			$categoryId = $this->getFromRequest('departmentID');
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails(
					$this->getFromRequest("departmentID"));

			$facilityDetails = $facility->getFacilityDetails(
					$departmentDetails['facility_id']);
		} elseif($this->getFromRequest('facilityID')) {
			$category = 'facility';
			$categoryId = $this->getFromRequest('facilityID');
			$facilityDetails = $facility->getFacilityDetails(
					$this->getFromRequest("facilityID"));
		} else {
			throw new Exception('404');
		}

		//	Access control
		if (!$this->user->checkAccess($category, $categoryId)) {
			throw new Exception('deny');
		}
		$request['parent_id'] = $categoryId;
		$request['parent_category'] = $category;
				
        $this->smarty->assign('request', $request);
		$this->smarty->assign('facilityDetails', $facilityDetails);
		
		$companyId = $facilityDetails["company_id"];
        $companyNew = new VWM\Hierarchy\Company($this->db, $companyId);
        $industryTypeId = $companyNew->getIndustryType(); 
        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $industryTypeId->id, $this->getFromRequest('id'));
        $woOldDesc = $workOrder->number;
   //     $repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
        $this->smarty->assign('data', $workOrder);

        $this->setNavigationUpNew($category, $categoryId);
        $params = array("bookmark" => "repairOrder");
        $this->setListCategoriesLeftNew($category, $categoryId, $params);
        $this->setPermissionsNew('viewRepairOrder');

        $post = $this->getFromPost();

		if (count($post) > 0) { 
            $facilityID = $post['id'];
            $woDepartments_id = $post['woDepartments_id'];
            $workOrder->setNumber($post['number']);
            $workOrder->setCustomer_name($post['repairOrderCustomerName']);
            $workOrder->setStatus($post['repairOrderStatus']);
            $workOrder->setDescription($post['repairOrderDescription']);
            $workOrder->setFacilityId($facilityID);
            if ($workOrder instanceof AutomotiveWorkOrder) {
                $workOrder->setVin($post['repairOrderVin']);
            }
			$violationList = $workOrder->validate(); 
			if(count($violationList) == 0 && $woDepartments_id != '') { 
				$woID = $workOrder->save(); 
                // get work order mix id
                $mixIDs = $workOrder->getMixes();   
                // now we should update child work order mix (don't touch iteration suffix)
                foreach ($mixIDs as $mixID) {
                    // add empty mix for each facility department
                    $mixOptimized = new MixOptimized($this->db, $mixID->mix_id);
                    preg_match("/$woOldDesc(.*)/", $mixOptimized->description, $suffix);  

                    $mixOptimized->description = $post['number'];
                    if(!empty($suffix[1]) ) {
                        $mixOptimized->description .= $suffix[1]; 
                    }
                    $mixOptimized->save(); 
                }
                // set department to wo
                $woDepartments_id = explode(",", $woDepartments_id);
                $repairOrderManager = new RepairOrderManager($this->db);
                // i should unset all departments from wo at first
                $repairOrderManager->unSetDepartmentToWo($woID);
                // set departments to wo
                foreach ($woDepartments_id as $departmentId) {
                   $repairOrderManager->setDepartmentToWo($woID, $departmentId); 
                }
				// redirect
				header("Location: ?action=viewDetails&category=repairOrder&id={$workOrder->id}&{$category}ID={$categoryId}&notify=58");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $workOrder);
                if ($woDepartments_id == '') {
                    $this->smarty->assign('woDepartmentsError', true);
                }
			}																	
		}

        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();     
        
        $repairOrderLabel = $companyNew->getIndustryType()->getLabelManager()->getLabel($companyLevelLabelRepairOrder->label_id)->getLabelText();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		// get wo departments
        $repairOrderManager = new RepairOrderManager($this->db);
        $woDepartments = $repairOrderManager->getDepartmentsByWo($this->getFromRequest('id'));
        if (!$woDepartments) {
            // we shoul get all department list
            $woDepartments = $facility->getDepartmentList($this->getFromRequest("facilityID"));
        }
        $departmetsName = array();
        foreach ($woDepartments as $departmentId) {
            $departmentDetails =  $department->getDepartmentDetails($departmentId);
            $departmetsName[] = $departmentDetails["name"];
        }
        $woDepartmentsName = implode(",", $departmetsName);
        $woDepartments = implode(",", $woDepartments);
        $this->smarty->assign('woDepartments', $woDepartments);
        $this->smarty->assign('woDepartmentsName', $woDepartmentsName);
        //	set js scripts
        $jsSources = array(
            "modules/js/reg_country_state.js",
            "modules/js/saveItem.js",
            "modules/js/PopupWindow.js",
            "modules/js/addJobberPopups.js",
            "modules/js/checkBoxes.js",
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
            'modules/js/repairOrderManager.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

	protected function actionCreateLabel() {
		
		$repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
		$mixList = array();
        // get child mixes 
		$mixTotalPrice = 0;
        $mixes = $repairOrder->getMixes();
		foreach ($mixes as $mix) {
			$mixOptimized = new MixOptimized($this->db, $mix->mix_id);
			$mix->price = $mixOptimized->getMixPrice();
			$mixTotalPrice += $mix->price;
			$mixList[] = $mix;
		}

		$facility = new Facility($this->db);
        $company = new Company($this->db);
		$facilityDetails = $facility->getFacilityDetails($repairOrder->facility_id);
		$companyId = $facilityDetails["company_id"];
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();     
        $company = new VWM\Hierarchy\Company($this->db, $companyId);
        $repairOrderLabel = $company->getIndustryType()->getLabelManager()->getLabel($companyLevelLabelRepairOrder->label_id)->getLabelText();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		
		$this->smarty->assign('mixTotalPrice', $mixTotalPrice);
        $this->smarty->assign('repairOrder', $repairOrder);
        $this->smarty->assign('mixList', $mixList);

		$this->smarty->display("tpls/repairOrderLabel.tpl");
	}
    
    protected function actionLoadDepartments() {
        
        $facilityId = $this->getFromRequest('facilityId');
        $department = new Department($this->db);
        $facility = new Facility($this->db);
        $woDepartmentsDeafult = $facility->getDepartmentList($facilityId);
        $woId = $this->getFromRequest('woId'); 
        // if we add new wo we cannot knew wo id so
        if ($woId != "false") {
            $repairOrderManager = new RepairOrderManager($this->db);
            $woDepartments = $repairOrderManager->getDepartmentsByWo($woId);
            if (!$woDepartments) {
                // we shoul get all department list
                $woDepartments = $woDepartmentsDeafult;
            }
        }
        
        $departmentsDeafult = array();
        foreach ($woDepartmentsDeafult as $departmentId) {
            $departmentDetails =  $department->getDepartmentDetails($departmentId);
            $departmentsDeafult[$departmentId] = $departmentDetails["name"];
        }
		
        $this->smarty->assign('woDepartments', $woDepartments);
        $this->smarty->assign('departmentsDeafult', $departmentsDeafult);
		echo $this->smarty->fetch('tpls/setDepartmentToWo.tpl');
    }
    
    protected function actionSetDepartmentToWo() {
	
        $department = new Department($this->db);
		$rowsToSave = $this->getFromRequest('rowsToSave'); 
        $value = implode(",", $rowsToSave);
        $departmentName = array();
        foreach ($rowsToSave as $departmentId) {
            $departmentDetails =  $department->getDepartmentDetails($departmentId);
            $departmentName[] = $departmentDetails["name"];
        }
        $response = implode(",", $departmentName);
        $response .= "<input type='hidden' name='woDepartments_id' id='woDepartments_id' value='$value' />";

		echo $response;
    }

}

?>
