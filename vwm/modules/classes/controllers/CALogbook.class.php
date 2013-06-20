<?php
use VWM\Hierarchy\CompanyManager;
use VWM\Hierarchy\FacilityManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionType;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionTypeSetting;
use VWM\Apps\Logbook\Entity\InspectionSubTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionGaugeTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionTypeSettings;
use VWM\Apps\Logbook\Manager\InspectionTypeManager;
use VWM\Apps\Logbook\Entity\LogbookSetupTemplate;
use VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;
use VWM\Apps\Logbook\Manager\LogbookDescriptionManager;
use VWM\Apps\Logbook\Entity\LogbookDescription;

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
/**
 * 
 * action view logbook details
 * 
 * @throws Exception
 */
    public function actionBrowseCategory()
    {
        $bookmark = $this->getFromRequest('bookmark');
        $facilityId = $this->getFromRequest('facilityId');
        $companyId = $this->getFromRequest('companyId');
        $logbookTemplateId = $this->getFromRequest('logbookTemplateId');

        $itManager = new InspectionTypeManager();
        $ltManager = new LogbookSetupTemplateManager();

        if (is_null($bookmark)) {
            $bookmark = 'logbookSetupTemplate';
        }
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);
        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        if ($companyId != 'null' || !isset($companyId)) {
            $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
        } else {
            $facilityList = $facilityManager->getFacilityListByCompanyId();
        }
        $this->smarty->assign('facilityList', $facilityList);
        
        switch ($bookmark) {
            case 'logbookSetupTemplate':
                //get logbook Setup Template List
                if ($facilityId == 'null' || !isset($facilityId)) {
                    if ($companyId == 'null' || !isset($companyId)) {
                        $facilityId = null;
                    } else {
                        $company = new Company();
                        $facilityIds = array();
                        foreach ($facilityList as $facility) {
                            $facilityIds[] = $facility->getFacilityId();
                        }
                        $facilityId = implode(',', $facilityIds);
                    }
                }
                //set pagination
                $countLogbookSetupTemplateList = $ltManager->getCountLogbookTemplateListByFacilityIds($facilityId);
                
                $url = "?" . $_SERVER["QUERY_STRING"];
                $url = preg_replace("/\&page=\d*/", "", $url);
                $pagination = new Pagination($countLogbookSetupTemplateList);
                $pagination->url = $url;
                
                $logbookSetupTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityId,$pagination);
                $this->smarty->assign('pagination', $pagination);
                $this->smarty->assign('logbookSetupTemplateList', $logbookSetupTemplateList);
                $this->smarty->assign('itemsCount', count($logbookSetupTemplateList));
                $tpl = 'tpls/viewLogbookTemplateList.tpl';
                break;
            case 'logbookInspectionType':
                //get logbook template List by Facility Id
                if($facilityId == 'null' || !isset($facilityId)){
                    if ($companyId == 'null' || !isset($companyId)) {
                        //get all templates
                        $logbookTemplateList = $ltManager->getLogbookTemplateListByFacilityIds();
                    }else{
                        //get templates by company
                        foreach ($facilityList as $facility) {
                            $facilityIds[] = $facility->getFacilityId();
                        }
                        $facilityIds = implode(',', $facilityIds);
                        $logbookTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityIds);
                    }
                }else{
                    //get logbookTemplateList by facility
                    $logbookTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityId);
                }
                $this->smarty->assign('logbookTemplateList', $logbookTemplateList);
               
                //get Inspection Types ids
                if ($logbookTemplateId == 'null' || !isset($logbookTemplateId)) {
                    if (($facilityId == 'null' || !isset($facilityId)) && ($companyId == 'null' || !isset($companyId))) {
                        $logbookTemplateId = null;
                    } else {
                        $logbookTemplateId = array();
                        foreach ($logbookTemplateList as $logbookTemplate) {
                            $logbookTemplateId[] = $logbookTemplate->getId();
                        }
                        $logbookTemplateId = implode(',', $logbookTemplateId);
                    }
                }
                
                //set pagination
                $logbookListCount = $itManager->getCountInspectionTypeByTemplateId($logbookTemplateId);
                $url = "?" . $_SERVER["QUERY_STRING"];
                $url = preg_replace("/\&page=\d*/", "", $url);
                
                $pagination = new Pagination($logbookListCount);
                $pagination->url = $url;
                $inspectionTypeList = $itManager->getInspectionTypeList($logbookTemplateId, $pagination);
                
                $this->smarty->assign('inspectionTypeList', $inspectionTypeList);
                $tpl = 'tpls/viewLogbookInspectionList.tpl';
                $this->smarty->assign('pagination', $pagination);
                $this->smarty->assign('itemsCount', count($inspectionTypeList));
                break;
            default :
                throw new Exception('404');
                break;
        }

        $jsSources = array(
            'modules/js/manageLogbookInspectionType.js',
            'modules/js/checkBoxes.js'
        );

        $this->smarty->assign('logbookTemplateId', $logbookTemplateId);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('bookmark', $bookmark);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->assign('action', $this->action);
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * 
     * add item action
     * 
     * @throws Exception
     */
    public function actionAddItem()
    {
        $bookmark = $this->getFromRequest('bookmark');
        if (is_null($bookmark)) {
            $bookmark = 'logbookSetupTemplate';
        }
        switch ($bookmark) {
            case 'logbookSetupTemplate':
                $this->addLogbookSetupTemplate();
                break;
            case 'logbookInspectionType':
                $this->addLogbookInspectionType();
                break;
            default :
                throw new Exception('404');
                break;
        }
        $jsSources = array(
            'modules/js/manageLogbookInspectionType.js',
            "modules/js/logbookInspectionTypeObject.js",
            'modules/js/checkBoxes.js',
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
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * add logbook template
     */
    private function addLogbookSetupTemplate()
    {
        $logbookSetupTemplateId = $this->getFromRequest('logbookTemplateId');
        $logbookSetupTemplate = new LogbookSetupTemplate();
        $ltManager = new LogbookSetupTemplateManager();
        if (!is_null($logbookSetupTemplateId)) {
            $logbookSetupTemplate->setId($logbookSetupTemplateId);
            $logbookSetupTemplate->load();
            $facilityList = $ltManager->getFacilityListByLogbookSetupTemplateId($logbookSetupTemplateId);
            $facilityIds = array();
            $companyIds = array();
            foreach ($facilityList as $facility) {
                $facilityIds[] = $facility->getFacilityId();
                if (!in_array($facility->getCompanyId(), $companyIds)) {
                    $companyIds[] = $facility->getCompanyId();
                }
            }
            $companyIds = implode(',', $companyIds);
            $facilityIds = implode(',', $facilityIds);
            $this->smarty->assign('facilityIds', $facilityIds);
            $this->smarty->assign('companyIds', $companyIds);
        }
        $tpl = 'tpls/addLogbookSetupTemplate.tpl';
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->assign('logbookSetupTemplate', $logbookSetupTemplate);
    }

    /**
     * view add logbook Inspection type
     */
    private function addLogbookInspectionType()
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
        //get type id if exist
        $typeId = $this->getFromRequest('typeId');
        $logbookInspectionType = new LogbookInspectionType();
        if (isset($typeId)) {
            $isEdit = 1;
            $logbookInspectionType->setId($typeId);
            $logbookInspectionType->load();
            $this->smarty->assign('settings', $logbookInspectionType->getInspectionType());
            $this->smarty->assign('json', $logbookInspectionType->getInspectionTypeRaw());
            
            //logbookDescription
            $ldManager = new LogbookDescriptionManager();
            $logbookDescriptionListJson = $ldManager->getDescriptionListByInspectionTypeIdInJson($typeId);
            $logbookDescriptionList = $ldManager->getDescriptionListByInspectionTypeId($typeId);
            $this->smarty->assign('logbookDescriptionListJson', $logbookDescriptionListJson);
            $this->smarty->assign('logbookDescriptionList', $logbookDescriptionList);
        }
        
        $settings = $logbookInspectionType->getInspectionType();
        //get logbook Template ids 
        $ltManager = new LogbookSetupTemplateManager();
        if (!is_null($typeId)) {
            $logbookTemplateList = $ltManager->getLogbookTemplateListByInspectionTypeId($typeId);
            $logbookTemplateIds = array();
            foreach ($logbookTemplateList as $logbookTemplate) {
                $logbookTemplateIds[] = $logbookTemplate->getId();
            }
            $logbookTemplateList = implode(',', $logbookTemplateIds);
        }
        $tpl = 'tpls/addLogbookInspectionType.tpl';
        $this->smarty->assign('isEdit', $isEdit);
        $this->smarty->assign('logbookTemplateList', $logbookTemplateList);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('tpl', $tpl);

        $this->smarty->assign('logbookInspectionType', $logbookInspectionType);
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
            $facilityList[] = array(
                "id" => $facility->getFacilityId(),
                "name" => $facility->getName()
            );
        }
        $facilityList = json_encode($facilityList);
        echo $facilityList;
    }

    /**
     * ajax method get logbook Template List
     */
    public function actionGetLogbookTemplateList()
    {
        $facilityId = $this->getFromPost('facilityId');
        $companyId = $this->getFromPost('companyId');

        if ($facilityId == 'null') {
            if ($companyId == 'null') {
                $facilityId = null;
            } else {
                $facilityId = array();
                $facilityManager = new FacilityManager();
                $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
                foreach ($facilityList as $facility) {
                    $facilityId [] = $facility->getFacilityId();
                }
                $facilityId = implode(',', $facilityId);
            }
        }
        $ltManager = new LogbookSetupTemplateManager();
        $logbookTemplates = $ltManager->getLogbookTemplateListByFacilityIds($facilityId);
        $logbookTemplateList = array();
        foreach ($logbookTemplates as $logbookTemplate) {
            $logbookTemplateList[] = array(
                "id" => $logbookTemplate->getId(),
                "name" => $logbookTemplate->getName()
            );
        }
        $logbookTemplateList = json_encode($logbookTemplateList);
        echo ($logbookTemplateList);
    }

    /**
     * ajax method for loading add logbook sub type dialog window
     */
    public function actionLoadAddLogbookInspectionSubType()
    {
        $gaugeTypeId = $this->getFromPost('gaugeTypeId');
        $this->smarty->assign('gaugeTypeId', $gaugeTypeId);
        $lManager = new LogbookManager();
        $gaugeList = $lManager->getGaugeList();
        $this->smarty->assign('gaugeList', $gaugeList);
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
     * ajax method for loading add logbook gauge type dialog window
     */
    public function actionLoadLogbookTemplateFacility()
    {
        $selectedFacilityIds = $this->getFromRequest('selectedFacilityIds');
        $selectedFacilityIds = explode(',', $selectedFacilityIds);
        $selectedCompanyIds = $this->getFromRequest('selectedCompanyIds');
        $selectedCompanyIds = explode(',', $selectedCompanyIds);
        //get companyList
        $companyManager = new CompanyManager();
        $facilityManager = new FacilityManager();
        $companies = $companyManager->getCompanyList();

        $companyList = array();
        foreach ($companies as $company) {
            $facilities = $facilityManager->getFacilityListByCompanyId($company['id']);
            $facilityList = array();
            foreach ($facilities as $facility) {
                $facilityList[] = array(
                    'id' => $facility->getFacilityId(),
                    'name' => $facility->getName()
                );
            }
            $companyList[] = array(
                'id' => $company['id'],
                'name' => $company['name'],
                'facilityList' => $facilityList
            );
        }
        $this->smarty->assign('companyCount', count($companyList));
        $this->smarty->assign('companyList', $companyList);
        $this->smarty->assign('selectedFacilityIds', $selectedFacilityIds);
        $this->smarty->assign('selectedCompanyIds', $selectedCompanyIds);
        $tpl = 'tpls/viewSetFacilityToLogbookTemplate.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }

    /**
     * save inspection type
     * ajax method
     */
    public function actionSaveInspectionType()
    {
        $db = VOCApp::getInstance()->getService('db');
        $isErrors = false;
        $violationList = '';
        $inspectionTypeToJson = $this->getFromPost('inspectionTypeToJson');
        $inspectionType = json_decode($inspectionTypeToJson);
        
        $companyId = $this->getFromPost('companyId');
        $inspectionTypeId = $inspectionType->id;
        $typeSubTypes = $inspectionType->subtypes;
        $typeGaugeTypes = $inspectionType->additionFieldList;
        $logbookDescriptionList = $inspectionType->logbookDescriptions;
        $id = $this->getFromPost('id');
        $logbookTemplateIds = explode(',', $inspectionType->logbookTemplateIds);
        $itManager = new InspectionTypeManager();
        
        //set subtype settings
        $subTypes = array();
        foreach ($typeSubTypes as $typeSubType) {
            $subType = new InspectionSubTypeSettings();
            $subType->setName($typeSubType->name);
            $subType->setNotes($typeSubType->notes);
            $subType->setQty($typeSubType->qty);
            $subType->setValueGauge($typeSubType->valueGauge);
            if($typeSubType->valueGauge){
                $subType->setGaugeTypeId($typeSubType->gaugeType);
            }
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
        $logbookInspectionType->setInspectionTypeRaw($inspectionTypeSettingsToJson);
        //inspection type validate
        if (count($inspectionTypeSettings->validate()) != 0) {
            $isErrors = true;
            $typeErrors = $inspectionTypeSettings->validate();
            foreach ($typeErrors as $typeError) {
                $violationList .= '<div>Type ' . $typeError->getPropertyPath() . ":" . $typeError->getMessage() . '<div>';
            }
        }
        
        //if we haven't got errors
        if (!$isErrors) {
            //begin transaction
            $db->beginTransaction();
            
            //save inspection type
            $id = $logbookInspectionType->save();
            
            //assign inspection type to template
            $itManager ->unAssignInspectionTypeFromInspectionTemplate($id);
            foreach ($logbookTemplateIds as $logbookTemplateId){
                $itManager->assignInspectionTypeToInspectionTemplate($id, $logbookTemplateId);
            }
            
            //save logbook description
            $ldManager = new LogbookDescriptionManager();
            $ldManager->deleteDescriptionsByInspectionTypeId($id);
            foreach($logbookDescriptionList as $logbookDescription){
              $newLogbookDescription = new LogbookDescription();
              $newLogbookDescription->setDescription($logbookDescription->description);
              $newLogbookDescription->setNotes($logbookDescription->notes);
              $newLogbookDescription->setInspectionTypeId($id);
              //logbook description type validate
              if (count($newLogbookDescription->validate()) != 0) {
                    $isErrors = true;
                    $typeErrors = $newLogbookDescription->validate();
                    foreach ($typeErrors as $typeError) {
                        $violationList .= '<div>Type ' . $typeError->getPropertyPath() . ":" . $typeError->getMessage() . '<div>';
                    }
                } else {
                    $descriptionId = $newLogbookDescription->save();
                    //get errors
                    if (!$descriptionId) {
                        $isErrors = true;
                        $violationList .= '<div> save logbook description error<div>';
                    }
                }
              
            }
            //commit transaction if we haven't got errors
            if (!$isErrors) {
                $errors = false;
                $db->commitTransaction();
            }else{
                //no sense save inspection type without description
                 $errors = $violationList;
                 $db->rollbackTransaction();
            }
        }else{
            $errors = $violationList;
        }
        
        
        $response = array(
            'link' => '?action=browseCategory&category=logbook&bookmark=logbookInspectionType',
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
        $bookmark = $this->getFromRequest('bookmark');
        $itemForDelete = array();
        switch ($bookmark) {
            case 'logbookSetupTemplate':
                for ($i = 0; $i < $itemsCount; $i++) {
                    if (!is_null($this->getFromRequest('item_' . $i))) {
                        $item = array();
                        $logbookSetupTemplate = new LogbookSetupTemplate();
                        $logbookSetupTemplate->setId($this->getFromRequest('item_' . $i));
                        $logbookSetupTemplate->load();
                        $item["id"] = $logbookSetupTemplate->getId();
                        $item["name"] = $logbookSetupTemplate->getName();
                        $itemForDelete [] = $item;
                    }
                }
                break;
            case 'logbookInspectionType':
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
                break;
            default :
                throw new Exception('404');
                break;
        }
        $this->finalDeleteItemACommon($itemForDelete);
    }

    /**
     * confirm delete logbook inspection type
     */
    public function actionConfirmDelete()
    {
        $bookmark = $this->getFromRequest('bookmark');
        $itemsCount = $this->getFromRequest('itemsCount');
        switch ($bookmark) {
            case 'logbookSetupTemplate':
                for ($i = 0; $i < $itemsCount; $i++) {
                    $id = $this->getFromRequest('item_' . $i);
                    $logbookSetupTemplate = new LogbookSetupTemplate();
                    $logbookSetupTemplate->setId($this->getFromRequest('item_' . $i));
                    $logbookSetupTemplate->load();
                    $logbookSetupTemplate->delete();
                }
                break;
            case 'logbookInspectionType':
                for ($i = 0; $i < $itemsCount; $i++) {
                    $id = $this->getFromRequest('item_' . $i);
                    $logbookInspectionType = new LogbookInspectionType();
                    $logbookInspectionType->setId($this->getFromRequest('item_' . $i));
                    $logbookInspectionType->load();
                    $logbookInspectionType->delete();
                }
                break;
            default :
                throw new Exception('404');
                break;
        }
        header('Location: admin.php?action=browseCategory&category=' . $this->getFromRequest('category') . '&bookmark=' . $bookmark);
        die();
    }

    /**
     * save Logbook Template
     */
    public function actionSaveLogbookTemplate()
    {
        $isErrors = false;
        $facilityIds = $this->getFromPost('selectedFacilityIds');
        $templateName = $this->getFromPost('templateName');
        $facilityIds = explode(',', $facilityIds);
        $logbookSetupTemplateId = $this->getFromPost('logbookSetupTemplateId');

        $logbookTemplateManager = new VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager();

        $logbookSetupTemplate = new LogbookSetupTemplate();
        if ($logbookSetupTemplateId != '' && !is_null($logbookSetupTemplateId)) {
            $logbookSetupTemplate->setId($logbookSetupTemplateId);
            $logbookSetupTemplate->load();
        }
        $logbookSetupTemplate->setName($templateName);

        //validate logbookSetupTemplate
        $violationList = $logbookSetupTemplate->validate();
        if (count($violationList) == 0) {
            $id = $logbookSetupTemplate->save();
            $logbookTemplateManager->unAssignLogbookTemplateFromFacility($id);
            foreach ($facilityIds as $facilityId) {
                $logbookTemplateManager->assignLogbookTemplateToFacility($id, $facilityId);
            }
            header('Location: admin.php?action=browseCategory&category=' . $this->getFromRequest('category') . '&bookmark=logbookSetupTemplate');
        } else {
            $this->smarty->assign('logbookSetupTemplate', $logbookSetupTemplate);
            $this->smarty->assign('violationList', $violationList);
        }

        $tpl = 'tpls/addLogbookSetupTemplate.tpl';
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->assign('action', $this->action);
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * ajax method
     * load Set Inspection type Logbook Template
     */
    public function actionLoadInspectionTypeLogbookTemplate()
    {
        $companyId = $this->getFromRequest('companyId');
        $facilityId = $this->getFromRequest('facilityId');
        $logbookTemplatesIds = $this->getFromRequest('logbookTemplatesIds');
        $logbookTemplatesIds = explode(',', $logbookTemplatesIds);
        //get companyList
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        //get Facility List By Company id
        $facilityManager = new FacilityManager();
        if ($companyId != 'null' || !isset($companyId)) {
            $facilityList = $facilityManager->getFacilityListByCompanyId($companyId);
        } else {
            $facilityList = $facilityManager->getFacilityListByCompanyId();
        }
        //get logbook Setup Template List
        $ltManager = new LogbookSetupTemplateManager();
        if ($facilityId == 'null' || !isset($facilityId)) {
            if ($companyId == 'null' || !isset($companyId)) {
                $logbookSetupTemplateList = $ltManager->getLogbookTemplateListByFacilityIds();
            } else {
                $company = new Company();
                $facilityIds = array();
                foreach ($facilityList as $facility) {
                    $facilityIds[] = $facility->getFacilityId();
                }
                $facilityIds = implode(',', $facilityIds);
                $logbookSetupTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityIds);
            }
        } else {
            $logbookSetupTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityId);
        }

        $this->smarty->assign('logbookTemplatesIds', $logbookTemplatesIds);
        $this->smarty->assign('logbookSetupTemplateList', $logbookSetupTemplateList);
        $this->smarty->assign('companyId', $companyId);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('facilityList', $facilityList);
        $this->smarty->assign('companyList', $companyList);
        $tpl = 'tpls/viewSetLogbookTemplateToInspectionType.tpl';
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }
    
    /**
     * ajax method
     * load add Logbook Description to Inspection type
     */
    public function actionLoadAddLogbookDescription()
    {
        $tpl = "tpls/viewAddLogbookDescription.tpl";
        $result = $this->smarty->fetch($tpl);
        echo $result;
    }

}
?>
