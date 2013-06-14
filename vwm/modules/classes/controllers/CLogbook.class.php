<?php

use VWM\Hierarchy\Facility;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Apps\UnitType\Manager\UnitTypeManager;

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
                $logbook->setInspectionType($post['inspectionType']);
                $logbook->setInspectionSubType($post['inspectionSubType']);
                $logbook->setDescription($post['logBookDescription']);
                $logbook->setPermit($permit);
                $logbook->setEquipmantId($post['equipmantId']);
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
                    header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=logbook&tab=".$tab);
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
            if(empty($inspectionTypesList)){
                throw new \Exception('There is no any inspection type. Create inspection type for this facility first.');
            }
            //if add action get subtypes of first inspection type
            if(is_null($logbook->getInspectionType())){
                $inspectionTypesDescription = $inspectionTypesList[0]->typeName;
            }else{
                $inspectionTypesDescription = $logbook->getInspectionType();
            }
            
            $inspectionSubTypesList = $itmanager->getInspectionSubTypesByTypeDescription($inspectionTypesDescription);
            $inspectionAdditionTypesList = $itmanager->getInspectionAdditionTypesByTypeDescription($logbook->getInspectionType());
            
            //get description
            $logbookDescriptionsList = json_decode($jsonDescriptionTypeList);

            //get inspection person list
            $inspectionPersonList = $lbmanager->getLogbookInspectionPersonListByFacilityId($facilityId);

            //get gauges
            $gaugeList = $lbmanager->getGaugeList($facilityId);
            
            //getEquipmantList
            $equipmant = new Equipment($this->db);
            $equipmantDetails = $equipmant->getEquipmentDetails($logbook->getEquipmantId());

            //get temperature dimension
            $utManager = new UnitTypeManager($this->db);
            $temperatureUnitTypeList = $utManager->getUnitTypeListByUnitClassId(UnitTypeManager::TEMPERATURE_UNIT_CLASS);
            $this->smarty->assign('temperatureUnitTypeList', $temperatureUnitTypeList);
            
            $this->smarty->assign('gaugeList', $gaugeList);
            $this->smarty->assign('gaugeListJson', json_encode($gaugeList));
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

        if ($tab == 'inspectionPerson') {
            $inspectionPersonId = $this->getFromRequest('inspectionPersonId');
            $inspectionPerson = new LogbookInspectionPerson($this->db);
            if(!is_null($inspectionPersonId)){
                $inspectionPerson->setId($inspectionPersonId);
                $inspectionPerson->load();
            }
            $this->smarty->assign('inspectionPerson', $inspectionPerson);

            $tpl = 'tpls/viewAddInspectionPerson.tpl';
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
        if(!isset($tab)){
            $tab = 'logbook';
        }
        //check tab
        if ($tab == 'logbook') {

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
                $creationDateTime = $logbookRecord->getDateTime();
                $creationDateTime = date($timeFormat . ' h:i a', $creationDateTime);
                $creationDateTime = explode(' ', $creationDateTime);
                //initialize inspection person
                $inspectionPerson = new LogbookInspectionPerson();
                $inspectionPerson->setId($logbookRecord->getInspectionPersonId());
                $inspectionPerson->load();

                //get notes subtype or description
                $notes ='NONE';
                $subtypeDesc = $logbookRecord->getSubTypeNotes();
                if ($subtypeDesc == 'NONE' || is_null($subtypeDesc) || $subtypeDesc == 'NULL') {
                    $notes = $logbookRecord->getDescriptionNotes();
                } else {
                    $notes = $subtypeDesc;
                }

                $logbook = array(
                    'logbookId' => $logbookRecord->getId(),
                    'inspectionType' => $logbookRecord->getInspectionType(),
                    'creationDate' => $creationDateTime[0],
                    'creationTime' => $creationDateTime[1] . ' ' . $creationDateTime[2],
                    'inspectionPersonName' => $inspectionPerson->getName(),
                    'condition' => $logbookRecord->getDescription(),
                    'notes' => $notes
                );

                $logbookList[] = $logbook;
            }
            $tpl = 'tpls/viewLogbook.tpl';
            $this->smarty->assign('pagination', $pagination);
            $this->smarty->assign('logbookList', $logbookList);
        } elseif ($tab == 'inspectionPerson') {
            $inspectionPerson = $lbManager->getLogbookInspectionPersonListByFacilityId($facilityId);
            $this->smarty->assign('inspectionPerson', $inspectionPerson);
            $tpl = 'tpls/viewInspectionPerson.tpl';
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
        $personId = $this->getFromRequest('personId');
        $facilityId = $this->getFromRequest('facilityId');
        $inspectionPerson = new LogbookInspectionPerson();
        if($personId != ''){
           $inspectionPerson->setId($personId);
           $inspectionPerson->load();
        }
        $inspectionPerson->setName($this->getFromRequest('personName'));
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

        //initialize equipmant
        $equipmant = new Equipment($this->db);
        $equipmantDetails = $equipmant->getEquipmentDetails($logbook->getEquipmantId());
        $this->smarty->assign('equipmantDetails', $equipmantDetails);

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
        //delete logbooks
        if ($tab == 'logbook') {
            $idArray = $this->getFromRequest('checkLogbook');
            $itemsForDelete = array();
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
        }
        //delete inspection Person
        if($tab == 'inspectionPerson'){
            $idArray = $this->getFromRequest('checkInspectionPerson');
            $itemsForDelete = array();
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

        //confirm delete logbooks
        if ($tab == 'logbook') {
            $logbooksIds = $this->itemID;
            foreach ($logbooksIds as $id) {
                $logbook = new LogbookRecord($this->db);
                $logbook->setId($id);
                $logbook->delete();
            }
        }

        if($tab == 'inspectionPerson'){
           $inspectionPersonsIds = $this->itemID;
           foreach ($inspectionPersonsIds as $id) {
                $inspectionPerson = new LogbookInspectionPerson($this->db);
                $inspectionPerson->setId($id);
                $inspectionPerson->delete();
            }
        }

        header("Location: ?action=browseCategory&category=facility&id=" . $facility->getFacilityId() . "&bookmark=logbook&tab=".$tab);
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

}
?>
