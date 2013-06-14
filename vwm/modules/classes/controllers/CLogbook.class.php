<?php

use VWM\Hierarchy\Facility;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Apps\UnitType\Manager\UnitTypeManager;
use VWM\Apps\Logbook\Entity\LogbookEquipment;
use VWM\Apps\Logbook\Manager\LogbookEquipmentManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionType;

class CLogbook extends Controller
{

    public function __construct($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'logbook';
    }

    /**
     * add new logbook record
     * @throws Exception
     */
    protected function actionAddItem()
    {
        //select category
        if ($this->getFromRequest('facilityID')) {
            $category = 'facility';
            $categoryId = $this->getFromRequest('facilityID');
            $facility = new Facility($this->db, $categoryId);
            $companyId = $facility->getCompanyId();
            $facilityId = $categoryId;
            $departments = $facility->getDepartments();
            $this->smarty->assign('departments', $departments);
        } else {
            throw new Exception('404');
        }
        $tab = $this->getFromRequest('tab');
        /*         * **** VIEW ADD LOGBOOK **** */
        if ($tab == 'logbook') {
            //get data format
            $dataChain = new TypeChain(null, 'date', $this->db, $facilityId, 'facility');
            $timeFormat = $dataChain->getFromTypeController('getFormat');

            $post = $this->getFromPost();
            //get id if exist
            $logbookId = $this->getFromRequest('logbookId');
            $logbook = new LogbookRecord();
            $logbook->setId($logbookId);

            //save add or update logbook if we need
            if (count($post) > 0) {
                //transfer time to unix type
                if ($post['dateTime'] != '') {
                    $dateTime = explode(' ', $post['dateTime']);

                    $date = explode('/', $dateTime[0]);
                    $time = explode(':', $dateTime[1]);
                    if ($dateTime[2] == 'pm') {
                        $time[0] = $time[0] + 12;
                    }
                    $dateTime = mktime($time[0], $time[1], 0, $date[0], $date[1], $date[2]);
                }
                //transfer permit
                if ($post['permit'] == 'on') {
                    $permit = 1;
                } else {
                    $permit = 0;
                }
                
                //init logbook
                $logbook->setFacilityId($facilityId);
                $logbook->setInspectionPersonId($post['InspectionPersons']);
                //$logbook->setInspectionType($post['inspectionType']);
                $logbook->setInspectionTypeId($post['inspectionType']);
                $logbook->setInspectionSubType($post['inspectionSubType']);
                $logbook->setDescription($post['logBookDescription']);
                $logbook->setPermit($permit);
                $logbook->setEquipmantId($post['logbookEquipmentId']);
                $logbook->setDepartmentId($post['departmentId']);
                $logbook->setMinGaugeRange($post['gaugeRangeFrom']);
                $logbook->setMaxGaugeRange($post['gaugeRangeTo']);
                
                //set addition fields
                if (!is_null($post['inspectionAdditionListType'])) {
                    $logbook->setInspectionAdditionType($post['inspectionAdditionListType']);
                }
                if ($post['qty'] != '') {
                    $logbook->setQty($post['qty']);
                }
                if ($post['logBookDescriptionNotes'] != '') {
                    $logbook->setDescriptionNotes($post['logBookDescriptionNotes']);
                }
                if ($post['subTypeNotes'] != '') {
                    $logbook->setSubTypeNotes($post['subTypeNotes']);
                }
                if($post['gaugeUnitType'] != ''){
                    $logbook->setUnittypeId($post['gaugeUnitType']);
                }

                if ($post['gaugeType'] != 'null') {
                    $gaugeValue = explode(';', $post['gaugeValue']);
                    $logbook->setValueGaugeType($post['gaugeType']);
                    $logbook->setGaugeValueFrom($gaugeValue[0]);
                    $logbook->setGaugeValueTo($gaugeValue[1]);
                    if ($post['replacedBulbs'] == 'on') {
                        $logbook->setReplacedBulbs(1);
                    }
                }

                if (isset($dateTime)) {
                    $logbook->setDateTime($dateTime);
                }
                $violationList = $logbook->validate();
                if (count($violationList) == 0) {
                    $id = $logbook->save();
                    header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=logbook&tab=" . $tab);
                } else {
                    $this->smarty->assign('creationTime', $post['dateTime']);
                }
            } else {
                
                $logbook->load();
                //check for add or edit
                $creationTime = $logbook->getDateTime();
                if (is_null($creationTime)) {
                    $creationTime = time();
                }
                $creationTime = date($timeFormat . ' h:i a', $creationTime);
                $this->smarty->assign('creationTime', $creationTime);
            }

            $this->smarty->assign('logbook', $logbook);

            //get inspection types list
            //$itmanager = \VOCApp::getInstance()->getService('inspectionType');
            $itmanager = new VWM\Apps\Logbook\Manager\InspectionTypeManager();
            $jsonInspectionalTypeList = $itmanager->getInspectionTypeListInJson($facilityId);

            $this->smarty->assign('jsonInspectionalTypeList', $jsonInspectionalTypeList);

            $jsonDescriptionTypeList = $itmanager->getLogbookDescriptionListInJson();
            $this->smarty->assign('jsonDescriptionTypeList', $jsonDescriptionTypeList);

            $lbmanager = new LogbookManager();
            $inspectionTypesList = json_decode($jsonInspectionalTypeList);
           
            //check if this facility has inspection type
            if (empty($inspectionTypesList)) {
                throw new \Exception('There is no any inspection type. Create inspection type for this facility first.');
            }
            
            $inspectionType = new LogbookInspectionType($this->db);
            $inspectionType->setId($logbook->getInspectionTypeId());
            $inspectionType->load();
            //if add action get subtypes of first inspection type

            if(is_null($logbook->getInspectionTypeId())){
                $inspectionTypesDescription = $inspectionTypesList[0]->typeName;
            }else{
                $inspectionTypesDescription = $inspectionType->getInspectionType()->typeName;
            }

            $inspectionSubTypesList = $itmanager->getInspectionSubTypesByTypeDescription($inspectionTypesDescription);
            $inspectionAdditionTypesList = $itmanager->getInspectionAdditionTypesByTypeDescription($inspectionType->getInspectionType()->typeName);

            //get description
            $logbookDescriptionsList = json_decode($jsonDescriptionTypeList);

            //get inspection person list
            $inspectionPersonList = $lbmanager->getLogbookInspectionPersonListByFacilityId($facilityId);

            //get gauges
            $gaugeList = $lbmanager->getGaugeList($facilityId);
            
            //getEquipmantList

            $leManager = new LogbookEquipmentManager();
            $logbookEquipmentList = $leManager->getLogbookEquipmentListByFacilityId($facilityId);

            //get temperature dimension
            $utManager = new UnitTypeManager($this->db);
            $temperatureUnitTypeList = $utManager->getUnitTypeListByUnitClassId(UnitTypeManager::TEMPERATURE_UNIT_CLASS);
            $this->smarty->assign('temperatureUnitTypeList', $temperatureUnitTypeList);
            $this->smarty->assign('gaugeList', $gaugeList);
            $this->smarty->assign('gaugeListJson', json_encode($gaugeList));
            $this->smarty->assign('logbookEquipmentList', $logbookEquipmentList);
            $this->smarty->assign('violationList', $violationList);
            $this->smarty->assign('inspectionPersonList', $inspectionPersonList);
            $this->smarty->assign('inspectionTypesList', $inspectionTypesList);
            $this->smarty->assign('inspectionSubTypesList', $inspectionSubTypesList);
            $this->smarty->assign('inspectionAdditionTypesList', $inspectionAdditionTypesList);
            $this->smarty->assign('logbookDescriptionsList', $logbookDescriptionsList);

            //get dateChain
            $this->smarty->assign('dataChain', $dataChain);

            $tpl = 'tpls/addLogbookRecord.tpl';
            $jsSources = array(
                "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/jshashtable-2.1_src.js",
                "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/jquery.numberformatter-1.2.3.js",
                "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/tmpl.js",
                "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/jquery.dependClass-0.1.js",
                "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/draggable-0.1.js",
            );

            $cssSources = array(
                'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css',
                'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/css/jslider.css'
            );
        }

        /*         * **** VIEW ADD INSPECTION PERSON **** */
        if ($tab == 'inspectionPerson') {
            $inspectionPersonId = $this->getFromRequest('inspectionPersonId');
            $inspectionPerson = new LogbookInspectionPerson($this->db);
            if (!is_null($inspectionPersonId)) {
                $inspectionPerson->setId($inspectionPersonId);
                $inspectionPerson->load();
            }
            $this->smarty->assign('inspectionPerson', $inspectionPerson);

            $tpl = 'tpls/viewAddInspectionPerson.tpl';
        }

        /*         * **** VIEW ADD LOGBOOK EQUIPMENT**** */
        if ($tab == 'logbookEquipment') {
            $logbookEquipmentId = $this->getFromRequest('logbookEquipmentId');
            $logbookEquipment = new LogbookEquipment();
             if (!is_null($logbookEquipmentId)) {
                $logbookEquipment->setId($logbookEquipmentId);
                $logbookEquipment->load();
            }
            $this->smarty->assign('logbookEquipment', $logbookEquipment);
            $tpl = 'tpls/viewAddLogbookEquipment.tpl';
        }
        //set left menu
        $this->setListCategoriesLeftNew($category, $categoryId);
        $this->setPermissionsNew($category);

        $this->smarty->assign('tab', $tab);
        $this->smarty->assign('action', 'addItem');
        $this->smarty->assign('category', 'logbook');
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->display('tpls:index.tpl');
    }

    /**
     * bookmarkLogbook($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkLogbook($vars)
    {
        extract($vars);

        $facilityId = $facilityDetails['facility_id'];
        //check for facility_id
        if (is_null($facilityId)) {
            throw new Exception('404');
        }
        $lbManager = new LogbookManager();

        $tab = $this->getFromRequest('tab');
        if (!isset($tab)) {
            $tab = 'logbook';
        }

        switch ($tab) {
            case 'logbook':
                //set pagination
                $logbookListCount = $lbManager->getCountLogbooksByFacilityId($facilityId);
                $url = "?" . $_SERVER["QUERY_STRING"];
                $url = preg_replace("/\&page=\d*/", "", $url);
                $pagination = new Pagination($logbookListCount);
                $pagination->url = $url;

                $logbookRecordList = $lbManager->getLogbookListByFacilityId($facilityId, $pagination);

                $dataChain = new TypeChain(null, 'date', $this->db, $facilityId, 'facility');
                $timeFormat = $dataChain->getFromTypeController('getFormat');

                $logbookList = array();
                foreach ($logbookRecordList as $logbookRecord) {
                    //get date and time
                    $creationDateTime = $logbookRecord->getDateTime();
                    $creationDateTime = date($timeFormat . ' h:i a', $creationDateTime);
                    $creationDateTime = explode(' ', $creationDateTime);
                    //initialize inspection person
                    $inspectionPerson = new LogbookInspectionPerson();
                    $inspectionPerson->setId($logbookRecord->getInspectionPersonId());
                    $inspectionPerson->load();
                    //create logbook array for diplay and sort
                    $logbook = array(
                        'logbookId' => $logbookRecord->getId(),
                        'inspectionType' => $logbookRecord->getInspectionType(),
                        //add date for sorting
                        'creationDate' => $creationDateTime[0],
                        //add time for sorting
                        'creationTime' => $creationDateTime[1] . ' ' . $creationDateTime[2],
                        'inspectionPersonName' => $inspectionPerson->getName()
                    );

                    $logbookList[] = $logbook;
                }
                $tpl = 'tpls/viewLogbook.tpl';
                $this->smarty->assign('pagination', $pagination);
                $this->smarty->assign('logbookList', $logbookList);
                break;
            case 'inspectionPerson':
                $inspectionPerson = $lbManager->getLogbookInspectionPersonListByFacilityId($facilityId);
                $this->smarty->assign('inspectionPerson', $inspectionPerson);
                $tpl = 'tpls/viewInspectionPerson.tpl';
                break;
            case 'logbookEquipment':
                $leManager = new LogbookEquipmentManager();
                
                $logbookEquipmentListCount = $leManager->getCountLogbookEquipmentByFacilityId($facilityId);
                
                $url = "?" . $_SERVER["QUERY_STRING"];
                $url = preg_replace("/\&page=\d*/", "", $url);
                $pagination = new Pagination($logbookEquipmentListCount);
                $pagination->url = $url;
                
                $logbookEquipmantList = $leManager->getLogbookEquipmentListByFacilityId($facilityId, $pagination);
                $this->smarty->assign('pagination', $pagination);
                $this->smarty->assign('logbookEquipmantList', $logbookEquipmantList);
                $tpl = 'tpls/viewLogbookEquipment.tpl';
                break;
            default:
                throw new Exception('tab is not exist');
                break;
        }

        $jsSources = array(
            'modules/js/autocomplete/jquery.autocomplete.js',
            'modules/js/checkBoxes.js',
        );

        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('tab', $tab);
        $this->smarty->assign('tpl', $tpl);
    }

    /**
     * ajax method for loading Dialog Inspection Person
     */
    protected function actionLoadAddInspectionPersonDetails()
    {
        $facilityId = $this->getFromPost('facilityId');
        $this->smarty->assign('facilityId', $facilityId);
        echo $this->smarty->fetch('tpls/addDialogInspectionPerson.tpl');
    }

    /**
     *  ajax method for saving Inspection Person
     */
    protected function actionSaveDialogInspectionPerson()
    {
        $inspectionPerson = new LogbookInspectionPerson();
        $inspectionPerson->setName($this->getFromPost('personName'));
        $inspectionPerson->setFacilityId($this->getFromPost('facilityId'));
        $id = $inspectionPerson->save();
        echo $id;
    }

    /**
     * add person
     */
    protected function actionSaveInspectionPerson()
    {
        $personId = $this->getFromPost('personId');
        $facilityId = $this->getFromPost('facilityId');
        $inspectionPerson = new LogbookInspectionPerson();
        if ($personId != '') {
            $inspectionPerson->setId($personId);
            $inspectionPerson->load();
        }
        $inspectionPerson->setName($this->getFromPost('personName'));
        $inspectionPerson->setFacilityId($facilityId);
        $id = $inspectionPerson->save();
        header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=logbook&tab=inspectionPerson");
    }

    /**
     * view logbook Detail
     */
    protected function actionViewLogbookDetails()
    {
        if ($this->getFromRequest('facilityID')) {
            $category = 'facility';
            $categoryId = $this->getFromRequest('facilityID');
            $facility = new Facility($this->db, $categoryId);
            $companyId = $facility->getCompanyId();
            $facilityId = $categoryId;
        } else {
            throw new Exception('404');
        }
        // initialize logbook
        $logbookId = $this->getFromRequest('id');
        $logbook = new LogbookRecord();
        $logbook->setId($logbookId);
        $logbook->load();

        //Initialize inspection Person
        $inspectionPerson = new LogbookInspectionPerson();
        $inspectionPerson->setId($logbook->getInspectionPersonId());
        $inspectionPerson->load();
        $this->smarty->assign('inspectionPerson', $inspectionPerson);

        //initialize date time
        $dataChain = new TypeChain(null, 'date', $this->db, $facilityId, 'facility');
        $timeFormat = $dataChain->getFromTypeController('getFormat');
        $creationTime = $logbook->getDateTime();
        $creationTime = date($timeFormat . ' h:i a', $creationTime);
        $this->smarty->assign('creationTime', $creationTime);

        //initialize equipment
        $logbookEquipment = new LogbookEquipment();
        $logbookEquipment->setId($logbook->getEquipmantId());
        $logbookEquipment->load();
        $this->smarty->assign('logbookEquipment', $logbookEquipment);
        // set left menu
        $this->setListCategoriesLeftNew($category, $categoryId);
        $this->setPermissionsNew($category);

        $tpl = 'tpls/viewLogbookDetails.tpl';
        $this->smarty->assign('tab', 'logbook');
        $this->smarty->assign('logbook', $logbook);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->display('tpls:index.tpl');
    }

    /**
     *
     * delete logbooks
     *
     * @throws Exception
     */
    public function actionDeleteItem()
    {
        $facility = new Facility($this->db, $this->getFromRequest('facilityID'));

        if (!$this->user->checkAccess('logbook', $facility->getCompanyId())) {
            throw new Exception('deny');
        }

        $tab = $this->getFromRequest('tab');
        $itemsForDelete = array();
        switch ($tab) {
            //delete logbooks
            case 'logbook':
                $idArray = $this->getFromRequest('checkLogbook');
                foreach ($idArray as $id) {
                    $logbook = new LogbookRecord();
                    $logbook->setId($id);
                    $logbook->load();
                    $itemForDelete = array(
                        'id' => $id,
                        'name' => $logbook->getDescription()
                    );
                    $itemsForDelete[] = $itemForDelete;
                }
                break;
            case 'inspectionPerson':
                $idArray = $this->getFromRequest('checkInspectionPerson');
                foreach ($idArray as $id) {
                    $inspectionPerson = new LogbookInspectionPerson();
                    $inspectionPerson->setId($id);
                    $inspectionPerson->load();
                    $itemForDelete = array(
                        'id' => $id,
                        'name' => $inspectionPerson->getName()
                    );
                    $itemsForDelete[] = $itemForDelete;
                }
                break;
            case 'logbookEquipment':
                $idArray = $this->getFromRequest('checkLogbookEquipmant');
                foreach ($idArray as $id) {
                    $logbookEquipment = new LogbookEquipment();
                    $logbookEquipment->setId($id);
                    $logbookEquipment->load();
                    $itemForDelete = array(
                        'id' => $id,
                        'name' => $logbookEquipment->getName()
                    );
                    $itemsForDelete[] = $itemForDelete;
                }
                break;
        }
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark' => 'logbook'));
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $this->setPermissionsNew('facility');

        $this->smarty->assign('facilityID', $this->getFromRequest('facilityID'));
        $this->smarty->assign('cancelUrl', "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=logbook");
        $this->smarty->assign('notViewChildCategory', true);
        $this->finalDeleteItemCommon($itemsForDelete, $linkedNotify, $count, $info);
    }

    /**
     *
     * confirm delete logbooks
     *
     * @throws Exception
     */
    public function actionConfirmDelete()
    {
        $facility = new Facility($this->db, $this->getFromPost('facilityID'));

        if (!$this->user->checkAccess('logbook', $facility->getCompanyId())) {
            throw new Exception('deny');
        }
        $tab = $this->getFromPost('tab');

        switch ($tab) {
            //confirm delete logbooks
            case 'logbook':
                $logbooksIds = $this->itemID;
                foreach ($logbooksIds as $id) {
                    $logbook = new LogbookRecord($this->db);
                    $logbook->setId($id);
                    $logbook->delete();
                }
                break;
            //confirm delete inspection Person
            case 'inspectionPerson':
                $inspectionPersonsIds = $this->itemID;
                foreach ($inspectionPersonsIds as $id) {
                    $inspectionPerson = new LogbookInspectionPerson($this->db);
                    $inspectionPerson->setId($id);
                    $inspectionPerson->delete();
                }
                break;
                //confirm delete logbook Equipment
            case 'logbookEquipment':
                $logbookEquipmentIds = $this->itemID;
                foreach ($logbookEquipmentIds as $id) {
                    $logbookEquipment = new LogbookEquipment($this->db);
                    $logbookEquipment->setId($id);
                    $logbookEquipment->delete();
                }
                break;
        }
        
        header("Location: ?action=browseCategory&category=facility&id=" . $facility->getFacilityId() . "&bookmark=logbook&tab=" . $tab);
    }

    /**
     * ajax method
     */
    public function actionGetEquipmantList()
    {
        $departmentId = $this->getFromPost('departmentId');
        $equipmant = new Equipment($this->db);
        $equipmantList = $equipmant->getEquipmentList($departmentId);
        echo json_encode($equipmantList);
    }

    /**
     * create Logbook Report
     */
    public function actionViewLogbookReports()
    {
        $facilityId = $this->getFromRequest('facilityId');
        $category = 'facility';

        $this->setListCategoriesLeftNew($category, $facilityId);
        $this->setPermissionsNew($category);

        $tpl = 'tpls/viewLogbookReports.tpl';
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->display('tpls:index.tpl');
    }

    /**
     * action save logbook equipment
     */
    public function actionSaveLogbookEquipment()
    {
        $facilityId = $this->getFromPost('facilityId');
        $logbookEquipmentName = $this->getFromPost('logbookEquipmentName');
        $logbookEquipmentId = $this->getFromPost('logbookEquipmentId');
        $logbookEquipment = new LogbookEquipment();
        if($logbookEquipmentId != ''){
            $logbookEquipment->setId($logbookEquipmentId);
            $logbookEquipment->load();
        }
        $logbookEquipment->setFacilityId($facilityId);
        $logbookEquipment->setName($logbookEquipmentName);
        
        $violationList = $logbookEquipment->validate();
        
        if (count($violationList) == 0) {
            $logbookEquipment->save();
            header("Location:?action=browseCategory&category=facility&id={$facilityId}&bookmark=logbook&tab=logbookEquipment");
        } else {
            $this->smarty->assign('facilityId', $facilityId);
            $this->smarty->assign('violationList', $violationList);
            $this->smarty->assign('logbookEquipment', $logbookEquipment);
            $tpl = 'tpls/viewAddLogbookEquipment.tpl';
            $this->smarty->assign('tpl', $tpl);
            $this->smarty->display('tpls:index.tpl');
        }
        die();
    }

}
?>
