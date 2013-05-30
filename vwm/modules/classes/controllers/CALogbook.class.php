<?php

use VWM\Hierarchy\CompanyManager;
use VWM\Hierarchy\FacilityManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionType;

class CALogbook extends Controller
{

    public function __construct($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'logbook';
        $this->parent_category = 'logbook';
    }

    function runAction()
    {
        $this->runCommon('admin');
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }

    public function actionBrowseCategory()
    {
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);

        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        $facilityList = $facilityManager->getFacilityListByCompanyId();
        $this->smarty->assign('facilityList', $facilityList);

        //get Inspection Types
        $itManager = new InspectionTypeManager();
        $inspectionTypeList = $itManager->getInspectionTypeList();
        $this->smarty->assign('inspectionTypeList', $inspectionTypeList);

        $jsSources = array(
            'modules/js/manageLogbookInspectionType.js'
        );

        $tpl = 'tpls/viewLogbookInspectionList.tpl';
        
        $this->smarty->assign('action', $this->action);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * view add logbookInspection type
     */
    public function actionAddItem()
    {
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);
        
        $companyId = $this->getFromRequest('companyId');
        $facilityId = $this->getFromRequest('facilityId');
        if($companyId == 'null'){
            $companyId = $companyList[0]['id'];
        }
        
        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
        $this->smarty->assign('facilityList', $facilityList);
        
        $jsSources = array(
            'modules/js/manageLogbookInspectionType.js',
            "modules/js/logbookInspectionTypeObject.js",
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
        $tpl = 'tpls/addLogbookInspectionType.tpl';
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->display("tpls:index.tpl");
    }
    /**
     * ajax method for getting facility List
     */
    public function actionGetFacilityList()
    {
        $companyId = $this->getFromPost('companyId');
        $facilityManager = new FacilityManager();
        if ($companyId != 'null') {
            $facilities = $facilityManager->getFacilityListByCompanyId($companyId);
        } else {
            $facilities = $facilityManager->getFacilityListByCompanyId();
        }
        $facilityList = array();
        foreach ($facilities as $facility) {
            $newFacility = array();
            $newFacility['id'] = $facility->getFacilityId();
            $newFacility['name'] = $facility->getName();
            $facilityList[] = $newFacility;
        }
        $facilityList = json_encode($facilityList);
        echo $facilityList;
    }

    public function actionGetInspectionTypeList()
    {
        $companyId = $this->getFromPost('companyId');
        $facilityId = $this->getFromPost('facilityId');
        $itManager = new InspectionTypeManager();

        if ($facilityId == 'null') {
            if ($companyId == 'null') {
                //get all inspection types
                $inspectionTypeList = $itManager->getInspectionTypeList();
            } else {
                $facilityIds = array();
                $facilityManager = new FacilityManager();
                $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
                foreach ($facilityList as $facility) {
                    $facilityIds[] = $facility->getFacilityId();
                }
                $facilityIds = implode(',', $facilityIds);
                //get inspection type by company id
                $inspectionTypeList = $itManager->getInspectionTypeList($facilityIds);
            }
        } else {
            //get inspection type by facility id
            $inspectionTypeList = $itManager->getInspectionTypeList($facilityId);
        }
        
        $this->smarty->assign('inspectionTypeList', $inspectionTypeList);

        $tpl = 'tpls/logbookInspectionList.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    
    public function actionLoadAddLogbookInspectionSubType()
    {
        $tpl = 'tpls/addInspectionSubType.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    
    public function actionSaveInspectionType()
    {
        $inspectionTypeToJson = $this->getFromPost('inspectionTypeToJson');
        $inspectionType = json_decode($inspectionTypeToJson);
        
        $logbookInspectionType = new LogbookInspectionType($this->db);
        
        var_dump($inspectionTypeToJson);
    }

}
?>
