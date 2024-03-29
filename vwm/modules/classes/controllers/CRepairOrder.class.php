<?php

use VWM\Label\CompanyLevelLabel;
use VWM\Apps\WorkOrder\Factory\WorkOrderFactory;
use \VWM\Apps\WorkOrder\Entity\WorkOrder;
use VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder;
use VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Apps\Process\ProcessInstance;
use VWM\Apps\Process\StepTemplate;
use VWM\Apps\Process\StepInstance;
use VWM\Apps\UnitType\Manager\UnitTypeManager;


class CRepairOrder extends Controller
{

    const TIME = 1;
    const GOM = 3;

    public function __construct($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'repairOrder';
        $this->parent_category = 'facility';
    }

    protected function actionViewDetails()
    {
        $params = array("bookmark" => "repairOrder");

        if ($this->getFromRequest('departmentID')) {
            $category = 'department';
            $categoryId = $this->getFromRequest('departmentID');
            $department = new VWM\Hierarchy\Department($this->db, $categoryId);
            $facility = $department->getFacility();
            //$this->smarty->assign('departmentID', $this->getFromRequest('departmentID'));
        } elseif ($this->getFromRequest('facilityID')) {
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

        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $company->getIndustryType()->id,$this->getFromRequest('id'));

        $dataChain = new TypeChain(null, 'date', $this->db, $companyId, 'company');
        $woDateFormat = $dataChain->getFromTypeController('getFormat');
        
        //get process information
        $processId = $workOrder->getProcessTemplateId();
        $isHaveProcess = false;

        $materialCost = 0;
        $laborCost = 0;
        $subTotalCost = 0;
        $spentTime = 0;


        if (!is_null($processId)) {
            //get default steps for work order by process Template Id
            $availableSteps = array();
            $isHaveProcess = true;
            $processTemplate = new ProcessTemplate($this->db, $processId);
            $processTemplate->setWorkOrderId($workOrder->getId());
            //get all available steps
            $steps = $processTemplate->getSteps();
            //get used steps
            $processInstance = $workOrder->getProcessInstance();

            //create process Instance if work order have process Template but don't have Process instance yet
            if (!$processInstance) {
                $processInstance = $processTemplate->createProcessInstance();
            }
            $stepInstances = $processInstance->getSteps();
            $this->smarty->assign('processName', $processInstance->getName());
            $this->smarty->assign('processInstanceId', $processInstance->getId());
            //create array of steps numbers
            $usedStepNumbers = array();

            foreach ($stepInstances as $stepInstance) {
                $usedStepNumbers[] = $stepInstance->getNumber();
            }

            //get all available steps for current Repair Order
            foreach ($steps as $step) {
                if (!in_array($step->getNumber(), $usedStepNumbers)) {
                    $availableSteps[] = $step;
                }
            }

            $this->smarty->assign('availableSteps', $availableSteps);
        }
        $mixCount = count($mixes);

        foreach ($mixes as $mix) {
            $mixCosts = array(
                "materialCost" => 0,
                "laborCost" => 0,
                "totalCost" => 0,
                "stepNumber" => '--',
                "stepEmpty" => false,
                "stepId" => 0
            );

            if (!is_null($mix->getStepId())) {
                $step = new StepInstance($this->db, $mix->getStepId());
                $resources = $step->getResources();

                $spentTime = $mix->spent_time;
                $timeResourceCount = 0;

                foreach ($resources as $resource) {

                    if ($resource->getResourceTypeId() == self::GOM) {
                        $mixCosts['materialCost'] = $resource->getMaterialCost();
                        $materialCost += $resource->getMaterialCost();
                    }
                    if ($resource->getResourceTypeId() == self::TIME && $timeResourceCount == 0) {
                        $laborCost += $spentTime * $resource->getRate();
                        $mixCosts['laborCost'] = $spentTime * $resource->getRate();
                        $timeResourceCount = 1;
                    }
                }
                $mixCosts['stepNumber'] = $step->getNumber();
            }
            $mixCosts['totalCost'] = $mixCosts['materialCost'] + $mixCosts['laborCost'] + $mix->price;
            $mixCosts['stepId'] = $mix->getStepId();
            $mixesCosts[$mix->mix_id] = $mixCosts;
        }
        //get url for adding mix to Repair Order button
        //get last mix
        $urlMixAdd = "?action=addItem&category=mix" .
                "&repairOrderId=" . $repairOrder->getId() .
                "&departmentID=" . $departmentId;

        $urlMixEdit = "?action=showEditStep&category=repairOrder" .
                "&repairOrderId=" . $repairOrder->getId() .
                "&departmentId=" . $departmentId;

        if ($mixes && !$mix->hasChild) {
            $urlMixAdd .= "&parentMixID=" . $mix->mix_id;
        }
        if ($this->getFromRequest('facilityID')) {
            $urlMixAdd .= "&facilityID=" . $this->getFromRequest('facilityID');
        }
        // set stepID = 0 if mix do not conect with step
        $urlMixAdd .="&stepID=0";

        $mixList = array();
        $stepsCount = count($steps);
        //array of not empty mixs
        $mixStepsIds = array();

        foreach ($mixes as $mix) {
            if (!is_null($mix->getStepId())) {
                $step = new StepInstance($this->db, $mix->getStepId());
                $stepNumber = $step->getNumber();
                $mixStepsIds[] = $step->getId();
            } else {
                $stepNumber = $stepsCount + $mix->mix_id;
            }
            $mixList[$stepNumber] = $mix;
        }

        //get empty steps
        $emptyMixSteps = array();
        foreach ($stepInstances as $stepInstance) {
            if (!in_array($stepInstance->getId(), $mixStepsIds)) {
                $emptyMixSteps[] = $stepInstance;
            }
        }

        // create empty steps for display
        foreach ($emptyMixSteps as $emptyMixStep) {
            $mixCosts = array(
                "materialCost" => 0,
                "laborCost" => 0,
                "totalCost" => 0,
                "stepNumber" => '--',
                "stepEmpty" => true,
                "stepId" => 0
            );
            $mix = new MixOptimized($this->db);
            $mix->mix_id = $emptyMixStep->getId();
            $mix->setDepartmentId($departmentId);
            $time = $emptyMixStep->getLastUpdateTime();
            $time = explode('-', $time);
            $time = mktime(0, 0, 0, $time[1], $time[2], $time[0]);
            $mix->set_creation_time($time);
            $mix->setDescription($emptyMixStep->getDescription());
            $mix->spent_time = $emptyMixStep->getTotalSpentTime();

            //get labor, material and total cost
            $emptyStepResources = $emptyMixStep->getResources();

            foreach ($emptyStepResources as $emptyStepResource) {
                $mixCosts["materialCost"] += $emptyStepResource->getMaterialCost();
                $mixCosts["laborCost"] += $emptyStepResource->getLaborCost();
            }
            $mixCosts["totalCost"] = $mixCosts["materialCost"] + $mixCosts["laborCost"];
            $mixList[$emptyMixStep->getNumber()] = $mix;
            $mixCosts['stepNumber'] = $emptyMixStep->getNumber();
            $mixCosts['stepId'] = $emptyMixStep->getId();
            $mixesCosts[$mix->mix_id] = $mixCosts;
            //get prices for work order
            $mixTotalSpentTime+=$mix->spent_time;
            $materialCost+=$mixCosts["materialCost"];
            $laborCost+=$mixCosts["laborCost"];
        }
        ksort($mixList);
        $subTotalCost = $workOrder->calculateSubTotalCost($materialCost, $laborCost, $mixTotalPrice);
        $profit = $workOrder->getProfit();
        $overhead = $workOrder->getOverhead();
        
        if($workOrder->getOverheadUnitType() == WorkOrder::PERCENTAGE) {
            //calculate profit taking procent from totalcost
            $overhead = $overhead*$subTotalCost/100;
        }
        if($workOrder->getProfitUnitType() == WorkOrder::PERCENTAGE) {
            //calculate profit taking procent from totalcost
            $profit = $overhead*$profit/100;
        }
        
        $this->smarty->assign('overhead', $overhead);
        $this->smarty->assign('profit', $profit);
        
        //display creation_time
		$woCreationDate = date($woDateFormat, $workOrder->getCreationTime());
        $this->smarty->assign('woCreationDate', $woCreationDate);

        //calculate total
        $total = $workOrder->calculateTotalCost($subTotalCost, $overhead, $profit);

        $this->smarty->assign('urlMixAdd', $urlMixAdd);
        $this->smarty->assign('urlMixEdit', $urlMixEdit);
        $jsSources = array(
            'modules/js/viewRepairOrder.js');
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('materialCost', $materialCost);
        $this->smarty->assign('spentTime', $spentTime);
        $this->smarty->assign('laborCost', $laborCost);

        $this->smarty->assign('totalCost', $subTotalCost);
        $this->smarty->assign('total', $total);

        $this->smarty->assign('backUrl', "?action=browseCategory&category={$category}&id={$categoryId}&bookmark=repairOrder");
        $this->smarty->assign('deleteUrl', "?action=deleteItem&category=repairOrder&id={$this->getFromRequest('id')}&{$category}ID={$categoryId}");
        $this->smarty->assign('editUrl', "?action=edit&category=repairOrder&id={$this->getFromRequest('id')}&{$category}ID={$categoryId}");

        $this->smarty->assign('mixList', $mixList);

        $this->smarty->assign('isHaveProcess', $isHaveProcess);
        $this->smarty->assign('repairOrder', $workOrder);
        $this->smarty->assign('mixTotalPrice', $mixTotalPrice);
        $this->smarty->assign('mixTotalSpentTime', $mixTotalSpentTime);
        $this->smarty->assign('mixesCosts', $mixesCosts);

        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * bookmarkDRepairOrder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails,
     * $bookmarkDRepairOrder, $moduleMap
     */
    protected function bookmarkDRepairOrder($vars)
    {
        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }

        $facility = new Facility($this->db);
        if ($this->getFromRequest('category') == 'department') {
            $department = new VWM\Hierarchy\Department($this->db,
                            $departmentDetails['department_id']);
        }
        //	set search criteria
        if (!is_null($this->getFromRequest('q'))) {
            $facility->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
            $this->smarty->assign('searchQuery', $this->getFromRequest('q'));
        }

        if ($this->getFromRequest('category') == 'department') {
            $repairOrderCount = $department->countRepairOrderInDepartment();
        } else {
            $repairOrderCount = $facility->countRepairOrderInFacility(
                    $facilityDetails['facility_id']);
        }

        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($repairOrderCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);

        if ($this->getFromRequest('category') == 'department') {
            $repairOrderList = $department->getRepairOrdersList($pagination);
        } else {
            $repairOrderList = $facility->getRepairOrdersList(
                    $facilityDetails['facility_id'], $pagination);
        }

        $company = new VWM\Hierarchy\Company($this->db, $facilityDetails["company_id"]);
        $industryTypeId = $company->getIndustryType();
        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $industryTypeId->id);
        $this->smarty->assign('instanceOfWorkOrder', $workOrder);

        if ($repairOrderList) {
            for ($i = 0; $i < count($repairOrderList); $i++) {
                if ($this->getFromRequest('category') == 'department') {
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

    protected function actionAddItem()
    {
        $request = $this->getFromRequest();
        $request["id"] = "false";

        $facility = new Facility($this->db);
        $params = array("bookmark" => "repairOrder");

        if ($this->getFromRequest('departmentID')) {
            $category = "department";
            $categoryId = $this->getFromRequest('departmentID');

            $department = new Department($this->db);
            $departmentDetails = $department->getDepartmentDetails(
                    $this->getFromRequest("departmentID"));

            $facilityDetails = $facility->getFacilityDetails(
                    $departmentDetails['facility_id']);
            $this->setPermissionsNew('viewDepartment');
        } elseif ($this->getFromRequest('facilityID')) {
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
        if ($category == 'department' && !$departmentDetails['share_wo']) {
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
        array_unshift($processList, new ProcessTemplate($this->db));

        $this->smarty->assign('processList', $processList);

        $dataChain = new TypeChain(null, 'date', $this->db, $companyId, 'company');
        
        if (count($post) > 0) {
            $woDateFormat = $dataChain->getFromTypeController('getFormat');
            
            $creationTime = $this->getFromPost('creationTime');
            $facilityID = $post['facility_id'];
            $woDepartments_id = $post['woDepartments_id'];
            $woProcessId = $post['woProcessId'];
            $workOrder->setNumber($post['number']);
            $workOrder->setCustomerName($post['repairOrderCustomerName']);
            $workOrder->setStatus($post['repairOrderStatus']);
            $workOrder->setDescription($post['repairOrderDescription']);
            $workOrder->setFacilityId($facilityID);
            $workOrder->setOverhead($post['repairOrderOverhead']);
            $workOrder->setProfit($post['repairOrderProfit']);
            $workOrder->setOverheadUnitType($post['overheadUnitType']);
            $workOrder->setProfitUnitType($post['profitUnitType']);

            if ($woProcessId != '') {
                $workOrder->setProcessTemplateId($woProcessId);
            }

            if ($workOrder instanceof AutomotiveWorkOrder) {
                $workOrder->setVin($post['repairOrderVin']);
            }
            
            //set creation time 
        if (strlen($creationTime) == 10 && is_numeric($creationTime)) {
            $workOrder->setCreationTime($creationTime);
        } else {
            $date = \VWM\Framework\Utils\DateTime::createFromFormat($woDateFormat, $creationTime);
            
            $timestamp = $date->getTimestamp();
            $workOrder->setCreationTime($timestamp);
        }
            $violationList = $workOrder->validate();
            
            if (count($violationList) == 0 && $woDepartments_id != '') {
                $this->db->beginTransaction();

                $woID = $workOrder->save();
                if ($woProcessId != '' && $woID) {
                    //initialize process Templale
                    $processTemplate = new ProcessTemplate($this->db, $woProcessId);
                    //create process Instance for Wo
                    $processTemplate->setWorkOrderId($woID);
                    $processInstance = $processTemplate->createProcessInstance();
                    $processInstance->save();
                }

                if (!$woID) {
                    $this->db->rollbackTransaction();
                    throw new Exception("Failed to save Work Order");
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
                if ($this->getFromRequest('departmentID')) {
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
        }else{
            $creationTime = date('m/d/Y', time());
        }

        $this->smarty->assign('dataChain', $dataChain);
        $this->smarty->assign('woDepartments', $woDepartments_id);
        $this->smarty->assign('creationTime', $creationTime);

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
        $this->smarty->assign('action', 'add');
        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionDeleteItem()
    {
        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $repairOrderID) {
                //	Access control
                
                if(!is_null($this->getFromRequest("facilityID"))){
                    $category = 'facility';
                    $categoryId = $this->getFromRequest("facilityID");
                }else{
                    $category = 'department';
                    $categoryId = $this->getFromRequest("departmentID");
                }
                
                if (!$this->user->checkAccess($category, $categoryId)) {
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

    protected function actionConfirmDelete()
    {
        foreach ($this->itemID as $ID) {
            $repairOrder = new RepairOrder($this->db, $ID);
            //delete process
            $processInstance = $repairOrder->getProcessInstance();
            if($processInstance){
                $processInstance->deleteCurrentProcess();
            }
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

    protected function actionEdit()
    {
        $request = $this->getFromRequest();
        $facility = new Facility($this->db);
        $department = new Department($this->db);

        if ($this->getFromRequest('departmentID')) {
            $category = 'department';
            $categoryId = $this->getFromRequest('departmentID');
            $department = new Department($this->db);
            $departmentDetails = $department->getDepartmentDetails(
                    $this->getFromRequest("departmentID"));

            $facilityDetails = $facility->getFacilityDetails(
                    $departmentDetails['facility_id']);
        } elseif ($this->getFromRequest('facilityID')) {
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
        $dataChain = new TypeChain(null, 'date', $this->db, $companyId, 'company');
        $woDateFormat = $dataChain->getFromTypeController('getFormat');
        
        $creationTime = date($woDateFormat, $workOrder->getCreationTime());
        $woOldDesc = $workOrder->number;
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
            $workOrder->setCustomerName($post['repairOrderCustomerName']);
            $workOrder->setStatus($post['repairOrderStatus']);
            $workOrder->setDescription($post['repairOrderDescription']);
            $workOrder->setFacilityId($facilityID);
            $workOrder->setOverhead($post['repairOrderOverhead']);
            $workOrder->setProfit($post['repairOrderProfit']);
            $workOrder->setOverheadUnitType($post['overheadUnitType']);
            $workOrder->setProfitUnitType($post['profitUnitType']);
            $creationTime = $post['creationTime'];
            
            if (strlen($creationTime) == 10 && is_numeric($creationTime)) {
                $workOrder->setCreationTime($creationTime);
            } else {
                $date = \VWM\Framework\Utils\DateTime::createFromFormat($woDateFormat, $creationTime);
                $timestamp = $date->getTimestamp();
                $workOrder->setCreationTime($timestamp);
            }

            if ($workOrder instanceof AutomotiveWorkOrder) {
                $workOrder->setVin($post['repairOrderVin']);
            }
            
            $violationList = $workOrder->validate();
            
            if (count($violationList) == 0 && $woDepartments_id != '') {
                $woID = $workOrder->save();
                // get work order mix id
                $mixIDs = $workOrder->getMixes();
                // now we should update child work order mix (don't touch iteration suffix)
                foreach ($mixIDs as $mixID) {
                    // add empty mix for each facility department
                    $mixOptimized = new MixOptimized($this->db, $mixID->mix_id);
                    preg_match("/$woOldDesc(.*)/", $mixOptimized->description, $suffix);

                    $mixOptimized->description = $post['number'];
                    if (!empty($suffix[1])) {
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
            $departmentDetails = $department->getDepartmentDetails($departmentId);
            $departmetsName[] = $departmentDetails["name"];
        }
        $woDepartmentsName = implode(",", $departmetsName);
        $woDepartments = implode(",", $woDepartments);
        
        $this->smarty->assign('dataChain', $dataChain);
        $this->smarty->assign('woDepartments', $woDepartments);
        $this->smarty->assign('woDepartmentsName', $woDepartmentsName);
        $this->smarty->assign('creationTime', $creationTime);
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
        $this->smarty->assign('action', 'edit');
        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * print Work Order
     */
    protected function actionCreateLabel()
    {
        //get WorkOrder
        $repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
        
        // get Date Format on Facility level
        $dataChain = new TypeChain(null, 'date', $this->db, $repairOrder->facility_id, 'facility');
        $woDateFormat = $dataChain->getFromTypeController('getFormat');
        //array of mix list
        $mixList = array();
        // get child mixes
        $mixTotalPrice = 0;
        // work order material cost
        $materialCost = 0;
        // work order labor cost
        $laborCost = 0;
        // work order paint cost
        $paintCost = 0;
        //get all mixes in work Order
        $mixes = $repairOrder->getMixes();
        
        $mixStepsIds = array();
        //get All Mixes with and without step in Work Order
         foreach ($mixes as $mix) {
             $mixCosts = array(
                "materialCost" => 0,
                "laborCost" => 0,
                "totalCost" => 0,
                "paintCost" => 0,
                "stepNumber" => '--',
                "stepEmpty" => false,
                "stepId" => 0
            );
            // count mix price
            $mixOptimized = new MixOptimized($this->db, $mix->mix_id);
            $mixCosts["paintCost"] = $mixOptimized->getMixPrice();
            $paintCost += $mixCosts["paintCost"];
            //count mix material and labor prices if has step id
            if (!is_null($mix->getStepId())) {
                $step = new StepInstance($this->db, $mix->getStepId());
                //get mix resources
                $resources = $step->getResources();
                $stepNumber = $step->getNumber();
                //get step Ids
                $mixStepsIds[] = $step->getId();
                //count mix price by resource
                $spentTime = $mix->spent_time;
                // flag for getting first time price for counting mix time price
                $timeResourceCount = 0;
                foreach ($resources as $resource) {
                    //count material mix cost
                    if ($resource->getResourceTypeId() == self::GOM) {
                        $mixCosts['materialCost'] = $resource->getMaterialCost();
                        $materialCost += $resource->getMaterialCost();
                    }
                    //count labor mix cost
                    if ($resource->getResourceTypeId() == self::TIME && $timeResourceCount == 0) {
                        $laborCost += $spentTime * $resource->getRate();
                        $mixCosts['laborCost'] = $spentTime * $resource->getRate();
                        $timeResourceCount = 1;
                    }
                }
                $mixCosts['stepNumber'] = $step->getNumber();
            } else {
                $stepNumber = $mix->mix_id;
                $mixCosts['stepNumber'] = $mix->mix_id;
            }
            
            $mixList[$stepNumber] = $mix;
            //set mix costs
            $mixCosts['totalCost'] = $mixCosts['materialCost'] + $mixCosts['laborCost'] + $mixCosts["paintCost"];
            //count total work order count
            $mixTotalPrice += $mixCosts['totalCost'];
            $mixCosts['stepId'] = $mix->getStepId();
            $mixesCosts[$mix->mix_id] = $mixCosts;
        }
        
        //GET EMPTY STEPS FOR WORK ORDER
        //get Work order Process  
        $processInstance = $repairOrder->getProcessInstance();
        //check if Work order has process
        if ($processInstance) {
            //get all creating steps in Work Order
            $stepInstances = $processInstance->getSteps();
            //array of steps with out mixes
            $emptyMixSteps = array();
            //get steps in work order with out mixes, as we have already considered steps with mixes
            foreach ($stepInstances as $stepInstance) {
                if (!in_array($stepInstance->getId(), $mixStepsIds)) {
                    $emptyMixSteps[] = $stepInstance;
                }
            }
            // create empty steps for display
            foreach ($emptyMixSteps as $emptyMixStep) {
                //cost of Mix (labor total paint)
                $mixCosts = array(
                    "materialCost" => 0,
                    "laborCost" => 0,
                    "totalCost" => 0,
                    "paintCost" => '0.00',
                    "stepNumber" => '--',
                    "stepEmpty" => true,
                    "stepId" => 0
                );
                //create mix
                $mix = new MixOptimized($this->db);
                $mix->mix_id = $emptyMixStep->getId();
                //$mix->setDepartmentId($departmentId);
                //count mix creating time by step time
                $time = $emptyMixStep->getLastUpdateTime();
                $time = explode('-', $time);
                $time = mktime(0, 0, 0, $time[1], $time[2], $time[0]);
                //set date format for correct display creation time with out department Id
                $mix->dateFormat = $woDateFormat;
                $mix->set_creation_time($time);
                $mix->setDescription($emptyMixStep->getDescription());
                $mix->spent_time = $emptyMixStep->getTotalSpentTime();

                //get labor, material and total cost
                $emptyStepResources = $emptyMixStep->getResources();
                //count Step Resource
                foreach ($emptyStepResources as $emptyStepResource) {
                    $mixCosts["materialCost"] += $emptyStepResource->getMaterialCost();
                    $mixCosts["laborCost"] += $emptyStepResource->getLaborCost();
                }
                //count mix total price. empty steps can't have paint cost as thay haven't got mixes
                $mixCosts["totalCost"] = $mixCosts["materialCost"] + $mixCosts["laborCost"];
                //add mix to mix list
                $mixList[$emptyMixStep->getNumber()] = $mix;
                //add mix cost to mix List cost
                $mixCosts['stepNumber'] = $emptyMixStep->getNumber();
                $mixCosts['stepId'] = $emptyMixStep->getId();
                $mixesCosts[$mix->mix_id] = $mixCosts;
                //count total work order count
                $mixTotalPrice += $mixCosts["totalCost"];
                $materialCost += $mixCosts["materialCost"];
                $laborCost += $mixCosts["laborCost"];
            }
        }
        //sort mix list by key(by step number)
        ksort($mixList);
        
        //get facility information
        $facility = new Facility($this->db);
        $company = new Company($this->db);
        $facilityDetails = $facility->getFacilityDetails($repairOrder->facility_id);
        $companyId = $facilityDetails["company_id"];                
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();
        $company = new VWM\Hierarchy\Company($this->db, $companyId);
        $repairOrderLabel = $company->getIndustryType()->getLabelManager()->getLabel($companyLevelLabelRepairOrder->label_id)->getLabelText();
        
        //get process name
        $processTemplate = new ProcessTemplate($this->db);
        $processTemplate->setId($repairOrder->getProcessTemplateId());
        $processTemplate->load();
        
        //count work order overhead and profit
        $workOrder = WorkOrderFactory::createWorkOrder($this->db, $company->getIndustryType()->id, $this->getFromRequest('id'));
        //calculate sub total cost
        $subTotalCost = $workOrder->calculateSubTotalCost($materialCost, $laborCost, $paintCost);
        $profit = $workOrder->getProfit();
        $overhead = $workOrder->getOverhead();
        
        if($workOrder->getOverheadUnitType() == WorkOrder::PERCENTAGE) {
            //calculate profit taking procent from totalcost
            $overhead = $overhead*$subTotalCost/100;
            $overhead = (round($overhead,2));
        }
        if($workOrder->getProfitUnitType() == WorkOrder::PERCENTAGE) {
            //calculate profit taking procent from totalcost
            $profit = $overhead*$profit/100;
            $profit = (round($profit,2));
        }
        $workOrdetTotalPrice = $mixTotalPrice + $profit + $overhead;
        
        //get Work order creation time
        $creationTime = $workOrder->getCreationTime();
        $creationTime = date($woDateFormat, $creationTime);
        
        $this->smarty->assign('processName', $processTemplate->getName());
        $this->smarty->assign('creationTime', $creationTime);
        $this->smarty->assign('repairOrderLabel', $repairOrderLabel);
        $this->smarty->assign('overhead', $overhead);
        $this->smarty->assign('profit', $profit);
        $this->smarty->assign('mixTotalPrice', $mixTotalPrice);
        $this->smarty->assign('repairOrder', $repairOrder);
        $this->smarty->assign('mixList', $mixList);
        $this->smarty->assign('mixesCosts', $mixesCosts);
        $this->smarty->assign('totalMaterialCost', $materialCost);
        $this->smarty->assign('totalLaborCost', $laborCost);
        $this->smarty->assign('totalPaintCost', $paintCost);
        $this->smarty->assign('workOrdetTotalPrice', $workOrdetTotalPrice);

        $this->smarty->display("tpls/repairOrderLabel.tpl");
    }

    protected function actionLoadDepartments()
    {
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
            $departmentDetails = $department->getDepartmentDetails($departmentId);
            $departmentsDeafult[$departmentId] = $departmentDetails["name"];
        }

        $this->smarty->assign('woDepartments', $woDepartments);
        $this->smarty->assign('departmentsDeafult', $departmentsDeafult);
        echo $this->smarty->fetch('tpls/setDepartmentToWo.tpl');
    }

    protected function actionSetDepartmentToWo()
    {
        $department = new Department($this->db);
        $rowsToSave = $this->getFromRequest('rowsToSave');
        $value = implode(",", $rowsToSave);
        $departmentName = array();
        foreach ($rowsToSave as $departmentId) {
            $departmentDetails = $department->getDepartmentDetails($departmentId);
            $departmentName[] = $departmentDetails["name"];
        }
        $response = implode(",", $departmentName);
        $response .= "<input type='hidden' name='woDepartments_id' id='woDepartments_id' value='$value' />";

        echo $response;
    }

    /**
     * method for adding step with out mix colling by ajax
     */
    protected function actionAddStepWithOutMix()
    {
        $responce = '';

        $stepId = $this->getFromPost('stepId');
        $processInstanceId = $this->getFromPost('processInstanceId');

        $stepTemplate = new StepTemplate($this->db, $stepId);
        $stepTemplateResources = $stepTemplate->getResources();
        $stepInstance = $stepTemplate->createInstanceStep($processInstanceId);

        $stepInstanceId = $stepInstance->save();
        $stepInstance->setId($stepInstanceId);

        if ($stepInstance) {
            foreach ($stepTemplateResources as $stepTemplateResource) {
                $stepInstanceResource = $stepTemplateResource->createInstanceResource($stepInstance->getId());
                $stepInstanceResource->save();
            }
            $responce = 1;
        } else {
            $responce = 0;
        }
        echo $responce;
    }

    /**
     * function edit step dispay
     */
    protected function actionShowEditStep()
    {

        $params = array("bookmark" => "repairOrder");

        if ($this->getFromRequest('departmentId')) {
            $category = 'department';
            $categoryId = $this->getFromRequest('departmentId');
            $department = new VWM\Hierarchy\Department($this->db, $categoryId);
            $facility = $department->getFacility();
        } elseif ($this->getFromRequest('facilityID')) {
            $category = 'facility';
            $categoryId = $this->getFromRequest('facilityID');
            $facility = new VWM\Hierarchy\Facility($this->db, $categoryId);
        } else {
            throw new Exception('404');
        }
        $this->setNavigationUpNew($category, $categoryId);
        $this->setListCategoriesLeftNew($category, $categoryId, $params);
        $this->setPermissionsNew('viewRepairOrder');

        $repaitOrderId = $this->getFromRequest('repairOrderId');
        $stepId = $this->getFromRequest('stepId');
        $departmentId = $this->getFromRequest('departmentId');
        
        $category = "department";
        $categoryId = $departmentId;
        
        $stepInstance = new StepInstance($this->db);
        //check if we edit or create step. If create set step id 0
        if (is_null($stepId)) {
            //get steps count
            //count template Steps
            $wo = new RepairOrder($this->db, $repaitOrderId);
            $processTemplateId = $wo->getProcessTemplateId();
            $processTemplate = new ProcessTemplate($this->db);
            $processTemplate->setId($processTemplateId);
            $processTemplate->load();
            $stepsTemplateCount = count($processTemplate->getSteps());
            //count steps creating by user
            $processInstance = new ProcessInstance($this->db);
            $processInstance->setId($this->getFromRequest('processId'));
            $processInstance->load();
            $stepsInstanceCreatingByUserCount = count($processInstance->getStepsCreatingByUser($stepsTemplateCount));
            //initialize new Step 
            $stepInstance->setId(0);
            $stepInstance->setProcessId($this->getFromRequest('processId'));
            $stepInstanceNumber = $stepsTemplateCount + $stepsInstanceCreatingByUserCount + 1;
            $stepInstance->setNumber($stepInstanceNumber);
        } else {
            $stepInstance->setId($stepId);
            $stepInstance->load();
        }
        
        $jsSources = array(
            "modules/js/stepObject.js",
            "modules/js/editStepSettings.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
            "modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/json2.js",
        );

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');

        $request['id'] = $categoryId;
        $request['category'] = $category;
        
        $this->smarty->assign('request', $request);
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('departmentId', $departmentId);
        $this->smarty->assign('stepInstance', $stepInstance);
        $this->smarty->assign('repaitOrderId', $repaitOrderId);

        $this->smarty->assign('tpl', 'tpls/viewEditStep.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionLoadResourceDetails()
    {
        $stepTemplate = new StepTemplate($this->db);
        $resourceUnitTypeId = $this->getFromPost('resourceUnitTypeId');
        
        $resourceTypes = VWM\Apps\Process\Resource::getResourceTypes();

        //delete volume resource type as we can't edit such resource.
        unset($resourceTypes[2]);
        $unitTypeList = VWM\Apps\Process\Resource::getResourceUnitTypeByResourceType($resourceUnitTypeId);

        $this->smarty->assign('unitTypeList', $unitTypeList);
        $this->smarty->assign('resourceType', $resourceTypes);
        echo $this->smarty->fetch('tpls/viewEditResourceDetails.tpl');
    }

    //ajax function for getting resource cost
    protected function actionGetResourceCostsInformation()
    {
        $qty = $this->getFromPost('resourceQty');
        $rate = $this->getFromPost('resourceRate');
        $resourceUnittypeId = $this->getFromPost('resourceUnittypeId');
        $resourceResourceUnittypeId = $this->getFromPost('resourceResourceUnittypeId');
        
        $resoutceInstance = new VWM\Apps\Process\ResourceInstance($this->db);

        $resoutceInstance->setQty($qty);
        $resoutceInstance->setRate($rate);
        $resoutceInstance->setUnittypeId($resourceUnittypeId);
        //$resoutceInstance->setRateUnittypeId($resourceUnittypeId);

        $resoutceInstance->setResourceTypeId($resourceResourceUnittypeId);
        $resoutceInstance->calculateTotalCost();

        $costs = array(
            'laborCost' => $resoutceInstance->getLaborCost(),
            'materialCost' => $resoutceInstance->getMaterialCost(),
            'totalCost' => $resoutceInstance->getTotalCost()
        );

        $costs = json_encode($costs);
        echo $costs;
    }

    protected function actionSaveStep()
    {
        $isErrors = false;
        $resourcesAttributes = json_decode($this->getFromPost('resourcesAttributes'));
        $stepAttributes = json_decode($this->getFromPost('stepAttributes'));
        
        $stepInstance = new \VWM\Apps\Process\StepInstance($this->db);
        
        if ($stepAttributes->stepId != 0) {
            $stepInstance->setId($stepAttributes->stepId);
            $stepInstance->load();
        }else{
            $stepInstance->setProcessId($stepAttributes->processId);
            $stepInstance->setNumber($stepAttributes->number);
        }
        $stepInstance->setDescription($stepAttributes->stepDescription);
        
        
        foreach ($resourcesAttributes as $resourceAttributes) {
            $resources[] = json_decode($resourceAttributes);
        }

        $resourceInstanceArray = array();
        foreach ($resources as $resource) {

            $resourceInstance = new \VWM\Apps\Process\ResourceInstance($this->db);
            $resourceInstance->setDescription($resource->description);
            if ($resource->qty == '') {
                $resourceInstance->setQty(0);
            } else {
                $resourceInstance->setQty($resource->qty);
            }
            if ($resource->rate == '') {
                $resourceInstance->setRate(0);
            } else {
                $resourceInstance->setRate($resource->rate);
            }
            $resourceInstance->setUnittypeId($resource->unittypeId);
            $resourceInstance->setResourceTypeId($resource->resourceTypeId);
            $resourceInstance->setStepId($stepInstance->getId());
            $resourceInstanceArray[] = $resourceInstance;
        }

        $violationList = '';
        $errorsMessage = array();
        //step validate
        if (count($stepInstance->validate()) != 0) {
            $isErrors = true;
            $stepErrors = $stepInstance->validate();
            foreach ($stepErrors as $stepError) {
                $violationList .= '<div>Step ' . $stepError->getPropertyPath() . ":" . $stepError->getMessage() . '<div>';
            }
        }

        foreach ($resourceInstanceArray as $resourceInstance) {
            //get resource errors
            $resourceErrors = $resourceInstance->validate();
            if (count($resourceErrors) != 0) {
                $isErrors = true;
                foreach ($resourceErrors as $resourceError) {
                    $violationList .= '<div>Resource ' . $resourceError->getPropertyPath() . ":" . $resourceError->getMessage() . '</div>';
                }
            }
        }
        
        if ($isErrors) {
            $errors = $violationList;
        }else{
            $errors = false;
            $stepInstance->setResources($resourceInstanceArray);
            $stepId = $stepInstance->save();
        }

        $responce = array(
            'link' => '?action=viewDetails&category=repairOrder',
            'errors' => $errors
        );
        $responce = json_encode($responce);
        echo $responce;
    }

    /**
     * function return unittype list by ajax request 
     */
    protected function actionGetUnittypeListForResourceEdit()
    {
        $resourceUnitTypeId = $this->getFromRequest('sysType');
        $resourceTypes = VWM\Apps\Process\Resource::getResourceTypes();
        //delete volume resource type as we can't edit such resource.
        unset($resourceTypes[2]);
        $unitTypeClasses = VWM\Apps\Process\Resource::getResourceUnitTypeByResourceType($resourceUnitTypeId);
        
        $data = array();
        foreach ($unitTypeClasses as $unitType) {
            $type = array(
                'unittype_id' => $unitType->getUnitTypeId(),
                'name' => $unitType->getName()
            );
            $data[] = $type;
        }
        echo json_encode($data);
    }
    
     /**
     * function for getting Paint Cost to understand if step can have mix 
     * ajax function
     */
    protected function actionIsStepCanHaveMix(){
        $isStepCanHaveMix = 0;
        $stepId = $this->getFromPost('stepId');
        $stepTemplate = new StepTemplate($this->db);
        $stepTemplate->setId($stepId);
        $stepTemplate->load();
        $resources = $stepTemplate->getResources();
        foreach($resources as $resource){
            if($resource->getResourceTypeId()==2){
                $isStepCanHaveMix = 1;
                break;
            }
        }
        echo $isStepCanHaveMix;
    }

}
?>
