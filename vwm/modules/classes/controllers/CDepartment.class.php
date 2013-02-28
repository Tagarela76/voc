<?php

use VWM\Apps\Gauge\Entity\QtyProductGauge;
use VWM\Apps\Gauge\Entity\SpentTimeGauge;
use VWM\Apps\Gauge\Entity\NoxGauge;
use VWM\Apps\Gauge\Entity\Gauge;

//use VWM\Hierarchy\Department;

class CDepartment extends Controller
{

    function CDepartment($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'department';
        $this->parent_category = 'facility';
    }

    protected function actionConfirmDelete()
    {
        $department = new Department($this->db);
        $departmentDet = $department->getDepartmentDetails($this->itemID[0]);

        foreach ($this->itemID as $ID) {
            //	setter injection
            $department->setTrashRecord(new Trash($this->db));
            $departmentDetails = $department->getDepartmentDetails($ID);
            $itemForDeleteName[] = $departmentDetails["name"];
            $department->deleteDepartment($ID);
        }

        //Set Title
        $facility = new Facility($this->db);
        $facilityDetails = $facility->getFacilityDetails($departmentDet['facility_id']);

        $company = new Company($this->db);
        $companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

        if ($this->successDeleteInventories)
            header("Location: ?action=browseCategory&category=facility&id=" . $facilityDetails['facility_id'] . "&bookmark=department&notify=7");
    }

    protected function actionDeleteItem()
    {
        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);

        $department = new Department($this->db);
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $departmentID) {
                //	Access control
                if (!$this->user->checkAccess($this->getFromRequest('category'), $departmentID)) {
                    throw new Exception('deny');
                }

                $departmentDetails = $department->getDepartmentDetails($departmentID);

                if ($departmentDetails['department_id'] == null) {
                    throw new Exception('404');
                }

                $delete["id"] = $departmentDetails["department_id"];
                $delete["name"] = $departmentDetails["name"];
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=department");
            //as ShowAddItem
            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'));
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        } else {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=" . $req_id[0] . "&bookmark=mix");
            //as ViewDetails
            $this->setNavigationUpNew('department', $departmentDetails['department_id']);
            $this->setListCategoriesLeftNew('department', $departmentDetails['department_id']);
            $this->setPermissionsNew('viewDepartment');
        }
        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    protected function actionViewDetails()
    {
        if (!$this->user->checkAccess($this->getFromRequest('category'), $this->getFromRequest('id'))) {
            throw new Exception('deny');
        }

        $this->setListCategoriesLeftNew($this->getFromRequest('category'), $this->getFromRequest('id'), $this->paramsForListLeft);
        $this->setNavigationUpNew($this->getFromRequest('category'), $this->getFromRequest('id'));
        $this->setPermissionsNew('viewDepartment');

        $this->smarty->assign("department", new VWM\Hierarchy\Department(
                $this->db, $this->getFromRequest('id')));
        $this->smarty->assign('backUrl', '?action=browseCategory&category=department&id=' . $this->getFromRequest("id") . '&bookmark=mix');
        $this->smarty->assign('tpl', 'tpls/viewDepartment.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionBrowseCategory()
    {
        $departments = new Department($this->db);
        $departmentDetails = $departments->getDepartmentDetails($this->getFromRequest('id'));

        $facility = new Facility($this->db);
        $facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);

        $company = new Company($this->db);
        $companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

        $this->smarty->assign("childCategory", $this->getFromRequest('bookmark'));

        $ms = new ModuleSystem($this->db);
        $moduleMap = $ms->getModulesMap();
        foreach ($moduleMap as $key => $module) {
            $showModules[$key] = $this->user->checkAccess($key, $facilityDetails['company_id']);
        }

        $this->smarty->assign('show', $showModules);

        $this->smarty->assign('popup_category', 'department');
        $this->smarty->assign('popup_category_id', $this->getFromRequest('id'));
        $date = getdate();
        $this->smarty->assign('curYear', $date['year']);

        //	voc indicator
        $departments->initializeByID($this->getFromRequest('id'));
        $usage = $departments->getCurrentUsage();

        $this->setIndicator($departmentDetails['voc_limit'], $usage);

        // for displaying voc unit type
        $unittype = new Unittype($this->db);
        $vocUnitType = $unittype->getNameByID($companyDetails["voc_unittype_id"]);
        $this->smarty->assign('vocUnitType', $vocUnitType);

        $vars = array(
            'departmentDetails' => $departmentDetails,
            'facilityDetails' => $facilityDetails,
            'companyDetails' => $companyDetails,
            'moduleMap' => $moduleMap,
            'tab' => $this->getFromRequest("tab")
        );
        // we should new - show nox emissions tab
        $noxManager = new NoxEmissionManager($this->db);
        $noxList = $noxManager->getNoxListByDepartment($departmentDetails['department_id']);
        if (!$noxList) {
            // we shouldn't show nox emissions tab
            $displayNoxEmissionsTab = false;
        } else {
            $displayNoxEmissionsTab = true;
        }
        $this->smarty->assign('displayNoxEmissionsTab', $displayNoxEmissionsTab);

        $departmentClass = new VWM\Hierarchy\Department($this->db, $departmentDetails['department_id']);


        $allDepartmentAvailableGauges = $departmentClass->getAllAvailableGauges();

        $departmentGauges = array();
        foreach ($allDepartmentAvailableGauges as $departmentGauge) {
            $unitType = $departmentGauge->getUnitType();

            if ($departmentGauge->getGaugeType() == Gauge::NOX_GAUGE) {
                $departmentGauge->setUnitTypeName('');
            } else {
                $departmentGauge->setUnitTypeName($unittype->getNameByID($unitType));
            }
            $departmentGauges[$departmentGauge->getGaugePriority()] = $departmentGauge;
        }
        ksort($departmentGauges);

        $this->smarty->assign("departmentGauges", $departmentGauges);

        $this->forward($this->getFromRequest('bookmark'), 'bookmarkD' . ucfirst($this->getFromRequest('bookmark')), $vars);
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionAddItem()
    {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }

        //	modules/ajax/saveDepartment.php - for more details
        $request = $this->getFromRequest();
        $request["id"] = $request["facilityID"];
        $request['parent_id'] = $request['facilityID'];
        $request['parent_category'] = 'facility';
        $this->smarty->assign('request', $request);

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest("facilityID"));
        $this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
        $this->setPermissionsNew('viewFacility');

        $department = new VWM\Hierarchy\Department($this->db);
        $department->setFacilityId($this->getFromRequest("facilityID"));

        $this->smarty->assign('department', $department);

        $woLabel = $department->getFacility()
                ->getCompany()
                ->getIndustryType()
                ->getLabelManager()
                ->getLabel(VWM\Label\CompanyLevelLabel::LABEL_ID_REPAIR_ORDER)
                ->getLabelText();
        $this->smarty->assign('woLabel', $woLabel);



        //	set js scripts
        $jsSources = array(
            'modules/js/saveItem.js',
            'modules/js/PopupWindow.js',
            'modules/js/checkBoxes.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js',
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('pleaseWaitReason', "Recalculating mixes at department.");
        $this->smarty->assign('tpl', 'tpls/addDepartment.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionEdit()
    {
        if (!$this->user->checkAccess($this->category, $this->getFromRequest('id'))) {
            throw new Exception('deny');
        }
        $department = new VWM\Hierarchy\Department($this->db, $this->getFromRequest('id'));

        $this->smarty->assign('department', $department);
//var_dump($department);die();
        $woLabel = $department->getFacility()
                ->getCompany()
                ->getIndustryType()
                ->getLabelManager()
                ->getLabel(VWM\Label\CompanyLevelLabel::LABEL_ID_REPAIR_ORDER)
                ->getLabelText();
        $this->smarty->assign('woLabel', $woLabel);

        $this->setNavigationUpNew($this->category, $department->getDepartmentId());
        $this->setListCategoriesLeftNew($this->category, $department->getDepartmentId());
        $this->setPermissionsNew('viewDepartment');

        $unitType = new Unittype($this->db);
        $unittype = $unitType->getDefaultCategoryUnitTypeList($this->getFromRequest('id'), $this->category);
        $unittype = implode(',', $unittype);

        $apmethodObject = new Apmethod($this->db);
        $defaultAPMethodList = $apmethodObject->getDefaultCategoryApmethodlist($this->getFromRequest('id'), $this->category);
        $defaultAPMethodList = implode(',', $defaultAPMethodList);
        $this->smarty->assign("defaultAPMethodList", $defaultAPMethodList);

        $this->smarty->assign("unittype", $unittype);
        //	set js scripts
        $jsSources = array(
            'modules/js/saveItem.js?rev=feb07',
            'modules/js/PopupWindow.js?rev=feb07',
            'modules/js/checkBoxes.js?rev=feb07',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js',
            'modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js',
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('pleaseWaitReason', "Recalculating mixes at department.");
        $this->smarty->assign('tpl', 'tpls/addDepartment.tpl');

        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * bookmarkDepartment($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkDepartment($vars)
    {
        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
        $sortStr = $this->sortList('department', 3);
        $filterStr = $this->filterList('department');

        $departments = new Department($this->db);
        $facility = new Facility($this->db);

        $pagination = new Pagination($departments->countDepartments($this->getFromRequest('id'), $filterStr));
        $pagination->url = "?action=browseCategory&category=" . $this->getFromRequest("category") . "&id=" . $this->getFromRequest("id") . "&bookmark=" . $this->getFromRequest("bookmark");
        $departmentList = $departments->getDepartmentListByFacility($this->getFromRequest('id'), $pagination, $filterStr, $sortStr);


        for ($i = 0; $i < count($departmentList); $i++) {
            $url = "?action=browseCategory&category=department&id=" . $departmentList[$i]['id'] . "&bookmark=mix";
            $departmentList[$i]['url'] = $url;

            $department = new Department($this->db);
            $department->initializeByID($departmentList[$i]["id"]);

            if ($department->isOverLimit()) {
                $departmentList[$i]["valid"] = "invalid";
            } else {
                $departmentList[$i]["valid"] = "valid";
            }


            //Create gauges for departments
            $departmentClass = new VWM\Hierarchy\Department($this->db, $departmentList[$i]['id']);


            $allDepartmentAvailableGauges = $departmentClass->getAllAvailableGauges();

            $departmentGauges = array();
            $gaugeTypeNames = Gauge::getGaugeTypes();
            $unittype = new Unittype($this->db);
            foreach ($allDepartmentAvailableGauges as $departmentGauge) {
                $unitType = $departmentGauge->getUnitType();

                if ($departmentGauge->getGaugeType() == 4) {
                    $departmentGauge->setUnitTypeName('');
                } else {
                    $departmentGauge->setUnitTypeName($unittype->getNameByID($unitType));
                }
                $departmentGauges[$departmentGauge->getGaugePriority()] = $departmentGauge;
            }
            ksort($departmentGauges);
            $departmentList[$i]['gauges'] = $departmentGauges;
        }

        $this->smarty->assign("childCategoryItems", $departmentList);
        //	voc indicator
        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);


        $facilityClass = new VWM\Hierarchy\Facility($this->db, $facilityDetails['facility_id']);


        $allFacilityAvailableGauges = $facilityClass->getAllAvailableGauges();

        $facilityGauges = array();
        $gaugeTypeNames = Gauge::getGaugeTypes();
        $unittype = new Unittype($this->db);
        foreach ($allFacilityAvailableGauges as $facilityGauge) {
            $unitType = $facilityGauge->getUnitType();

            if ($facilityGauge->getGaugeType() == 4) {
                $facilityGauge->setUnitTypeName('');
            } else {
                $facilityGauge->setUnitTypeName($unittype->getNameByID($unitType));
            }
            $facilityGauges[$facilityGauge->getGaugePriority()] = $facilityGauge;
        }
        ksort($facilityGauges);
        //var_dump($facilityGauges);die();
        $this->smarty->assign("departmentGauges", $facilityGauges);
        //	set tpl
        $this->smarty->assign('tpl', 'tpls/departmentList.tpl');
        $this->smarty->assign('pagination', $pagination);
    }

}
