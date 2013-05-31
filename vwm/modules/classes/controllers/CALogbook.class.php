<?php

use VWM\Hierarchy\CompanyManager;
use VWM\Hierarchy\FacilityManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Manager\LogbookManager;
use \VWM\Apps\Logbook\Entity\LogbookInspectionTypeSetting;
use \VWM\Apps\Logbook\Entity\InspectionSubTypeSettings;
use \VWM\Apps\Logbook\Entity\InspectionGaugeTypeSettings;
use \VWM\Apps\Logbook\Entity\InspectionTypeSettings;

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
    
    /**
     * ajax method for loading add logbook sub type dialog window
     */
    public function actionLoadAddLogbookInspectionSubType()
    {
        $tpl = 'tpls/addInspectionSubType.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    
     /**
     * ajax method for loading add logbook gauge type dialog window
     */
    public function actionLoadInspectionGaugeType()
    {
        $lManager = new LogbookManager();
        $gaugeList = $lManager->getGaugeList();
        
        $this->smarty->assign('gaugeList',$gaugeList);
        $tpl = 'tpls/addInspectionGaugeType.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    public function actionSaveInspectionType()
    {
        $isErrors = false;
        $violationList = '';
        $inspectionTypeToJson = $this->getFromPost('inspectionTypeToJson');
        $inspectionType = json_decode($inspectionTypeToJson);
        $facilityId = $inspectionType->facilityId;
        $inspectionTypeId = $inspectionType->id;
        $typeSubTypes = $inspectionType->subtypes;
        $typeGaugeTypes = $inspectionType->additionFieldList;
        
        //set subtype settings
        $subTypes = array();
        foreach($typeSubTypes as $typeSubType){
            $subType = new InspectionSubTypeSettings();
            $subType->setName($typeSubType->name);
            $subType->setNotes($typeSubType->notes);
            $subType->setQty($typeSubType->qty);
            $subType->setValueGauge($typeSubType->valueGauge);
            $subTypes[] = $subType->getAttributes();
            //sub type type validate
            if (count($subType->validate()) != 0) {
                $isErrors = true;
                $typeErrors = $subType->validate();
                foreach ($typeErrors as $typeError) {
                    $violationList .= '<div>Sub Type ' . $typeError->getPropertyPath() . ":" . $typeError->getMessage() . '<div>';
                }
            }
            
        }
        
        //set gauge settings
        $gaugeTypes = array();
        foreach ($typeGaugeTypes as $typeGaugeType) {
            $gaugeType = new InspectionGaugeTypeSettings();
            $gaugeType->setName($typeGaugeType->name);
            $gaugeType->setGaugeType($typeGaugeType->gaugeType);
            $gaugeTypes[] = $gaugeType->getAttributes();
            
            //gauge type validate
            if (count($gaugeType->validate()) != 0) {
                $isErrors = true;
                $typeErrors = $gaugeType->validate();
                foreach ($typeErrors as $typeError) {
                    $violationList .= '<div>Gauge Type ' . $typeError->getPropertyPath() . ":" . $typeError->getMessage() . '<div>';
                }
            }
            
        }
        
        //create inspection setting
        $inspectionTypeSettings = new InspectionTypeSettings();
        $inspectionTypeSettings->setTypeName($inspectionType->typeName);
        $inspectionTypeSettings->setPermit($inspectionType->permit);
        $inspectionTypeSettings->setSubtypes($subTypes);
        $inspectionTypeSettings->setAdditionFieldList($gaugeTypes);
        $inspectionTypeSettingsToJson = $inspectionTypeSettings->toJson();
        //save inspection type;        
        $logbookInspectionType = new LogbookInspectionType();
        $logbookInspectionType->setFacilityId($facilityId);
        $logbookInspectionType->setInspectionTypeRaw($inspectionTypeSettingsToJson);
        
        
        //inspection type validate
        if (count($inspectionTypeSettings->validate()) != 0) {
            $isErrors = true;
            $typeErrors = $inspectionTypeSettings->validate();
            foreach ($typeErrors as $typeError) {
                $violationList .= '<div>Type ' . $typeError->getPropertyPath() . ":" . $typeError->getMessage() . '<div>';
            }
        }
        
        
        if ($isErrors) {
            $errors = $violationList;
        }else{
            $errors = false;
            $id = $logbookInspectionType->save();
        }
        
        $response = array(
            'link' => '?action=browseCategory&category=logbook',
            'errors' => $errors
        );
        $response = json_encode($response);
        echo $response;
    }
    

}
?>
