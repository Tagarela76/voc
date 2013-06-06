<?php

use VWM\Hierarchy\CompanyManager;
use VWM\Hierarchy\FacilityManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionType;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionTypeSetting;
use VWM\Apps\Logbook\Entity\InspectionSubTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionGaugeTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionTypeSettings;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;

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
        $itManager = \VOCApp::getInstance()->getService('inspectionType');
        $facilityId = $this->getFromRequest('facilityId');
        $companyId = $this->getFromRequest('companyId');
        
        if ($facilityId == 'null' || !isset($facilityId)) {
            if ($companyId == 'null' || !isset($companyId)) {
                //get all inspection types
                $facilityId = null;
                $companyId = null;
            } else {
                $facilityIds = array();
                $facilityManager = new FacilityManager();
                $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
                foreach ($facilityList as $facility) {
                    $facilityIds[] = $facility->getFacilityId();
                }
                //get inspection type by company id
                $facilityId = implode(',', $facilityIds);
            }
        } 
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);

        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        if($companyId!='null' || !isset($companyId)){
            $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
        }else{
            $facilityList = $facilityManager->getFacilityListByCompanyId();
        }
        $this->smarty->assign('facilityList', $facilityList);

        //set pagination
        $logbookListCount = $itManager->getCountInspectionTypeByFacilityId($facilityId);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        
        $pagination = new Pagination($logbookListCount);
        $pagination->url = $url;

        //get Inspection Types
        $inspectionTypeList = $itManager->getInspectionTypeList($facilityId, $pagination);

        $this->smarty->assign('inspectionTypeList', $inspectionTypeList);
        $jsSources = array(
            'modules/js/manageLogbookInspectionType.js',
            'modules/js/checkBoxes.js'
        );
        $tpl = 'tpls/viewLogbookInspectionList.tpl';
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('pagination', $pagination);
        $this->smarty->assign('itemsCount', count($inspectionTypeList));
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
        $isEdit = 0;
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);

        $companyId = $this->getFromRequest('companyId');
        $facilityId = $this->getFromRequest('facilityId');
        if ($companyId == 'null') {
            $companyId = $companyList[0]['id'];
        }
        //get gauge list
        $lManager = new LogbookManager();
        $gaugeList = $lManager->getGaugeList();
        $this->smarty->assign('gaugeList', $gaugeList);
        //get type id uf exist
        $typeId = $this->getFromRequest('typeId');
        $logbookInspectionType = new LogbookInspectionType();
        if (isset($typeId)) {
            $isEdit = 1;
            $logbookInspectionType->setId($typeId);
            $logbookInspectionType->load();
            $facilityIds = $logbookInspectionType->getFacilityIds();
            //if false show all company and all facility
            if ($facilityIds) {
                //show defined company and defined facility
                $facilityIds = explode(',', $facilityIds);
                $facilityId = $facilityIds[0];
                $facility = new \VWM\Hierarchy\Facility($this->db, $facilityId);
                $companyId = $facility->getCompanyId();
            } else {
                $facilityId = 'null';
            }

            //all facility and defined company
            if (count($facilityIds) > 1) {
                $facilityId = 'null';
            }

            $this->smarty->assign('settings', $logbookInspectionType->getInspectionType());
            $this->smarty->assign('json', $logbookInspectionType->getInspectionTypeRaw());
        }
        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
        $this->smarty->assign('facilityList', $facilityList);
        $settings = $logbookInspectionType->getInspectionType();
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
        $this->smarty->assign('isEdit', $isEdit);
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('logbookInspectionType', $logbookInspectionType);
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

        $this->smarty->assign('gaugeList', $gaugeList);
        $tpl = 'tpls/addInspectionGaugeType.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    
    /**
     * save inspection type
     */
    public function actionSaveInspectionType()
    {
        $isErrors = false;
        $violationList = '';
        $inspectionTypeToJson = $this->getFromPost('inspectionTypeToJson');
        $inspectionType = json_decode($inspectionTypeToJson);
        $facilityId = $inspectionType->facilityId;
        $companyId = $this->getFromPost('companyId');
        $inspectionTypeId = $inspectionType->id;
        $typeSubTypes = $inspectionType->subtypes;
        $typeGaugeTypes = $inspectionType->additionFieldList;
        $id = $this->getFromPost('id');
        $ltManager = \VOCApp::getInstance()->getService('inspectionType');
        //get Facilities Ids
        $facilityIds = array();
        if ($facilityId == 'null') {
            $facilityManager = new FacilityManager();
            if ($companyId == 'null') {
                $facilityList = $facilityManager->getFacilityListByCompanyId();
            } else {
                $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
            }
            foreach ($facilityList as $facility) {
                $facilityIds[] = $facility->getFacilityId();
            }
        } else {
            $facilityIds[] = $facilityId;
        }
        //set subtype settings
        $subTypes = array();
        foreach ($typeSubTypes as $typeSubType) {
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
        if ($id != '') {
            $logbookInspectionType->setId($id);
        }
        //$logbookInspectionType->setFacilityIds($facilityId);
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
        } else {
            $errors = false;
            $id = $logbookInspectionType->save();
            $ltManager->unAssignInspectionTypeToFacility($id);
            foreach ($facilityIds as $facilityId) {
                $ltManager->assignInspectionTypeToFacility($id, $facilityId);
            }
        }
        $response = array(
            'link' => '?action=browseCategory&category=logbook',
            'errors' => $errors
        );
        $response = json_encode($response);
        echo $response;
    }
    
    /**
     * delete logbook inspection types
     */
    public function actionDeleteItem()
    {
        $itemsCount = $this->getFromRequest('itemsCount');
        $itemForDelete = array();
        for ($i = 0; $i < $itemsCount; $i++) {
            if (!is_null($this->getFromRequest('item_' . $i))) {
                $item = array();
                $logbookInspectionType = new LogbookInspectionType();
                $logbookInspectionType->setId($this->getFromRequest('item_' . $i));
                $logbookInspectionType->load();
                $item["id"] = $logbookInspectionType->getId();
                $item["name"] = $logbookInspectionType->getInspectionType()->typeName;
                $itemForDelete [] = $item;
            }
        }
        $this->finalDeleteItemACommon($itemForDelete);
    }

    /**
     * confirm delete logbook inspection type
     */
    public function actionConfirmDelete()
    {
        $itemsCount = $this->getFromRequest('itemsCount');
        for ($i = 0; $i < $itemsCount; $i++) {
            $id = $this->getFromRequest('item_' . $i);
            $logbookInspectionType = new LogbookInspectionType();
            $logbookInspectionType->setId($this->getFromRequest('item_' . $i));
            $logbookInspectionType->load();
            $logbookInspectionType->delete();
        }
        header('Location: admin.php?action=browseCategory&category=' . $this->getFromRequest('category'));
        die();
    }

}
?>
