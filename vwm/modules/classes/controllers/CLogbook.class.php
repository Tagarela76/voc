<?php

use VWM\Hierarchy\Facility;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;

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
        } else {
            throw new Exception('404');
        }

        //get data format 
        $dataChain = new TypeChain(null, 'date', $this->db, $facilityId, 'facility');
        $timeFormat = $dataChain->getFromTypeController('getFormat');

        $post = $this->getFromPost();
        //get id if exist
        $logbookId = $this->getFromRequest('logbookId');
        $logbook = new LogbookRecord();
        $logbook->setId($logbookId);

        //add or update logbook if we need
        if (count($post) > 0) {
            
            //transfer time to unix type
            if ($post['dateTime'] != '') {
                $dateTime = explode(' ', $post['dateTime']);
                $date = explode('/', $dateTime[0]);
                $time = explode(':', $dateTime[1]);
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
            //set addition fields
            if ($post['qty'] != '') {
                $logbook->setQty($post['qty']);
            }
            if ($post['logBookDescriptionNotes'] != '') {
                $logbook->setDescriptionNotes($post['logBookDescriptionNotes']);
            }
            if ($post['subTypeNotes'] != '') {
                $logbook->setSubTypeNotes($post['subTypeNotes']);
            }
            
            if($post['gaugeType'] != 'null'){
                $gaugeValue = explode(';', $post['gaugeValue']);
                $logbook->setValueGaugeType($post['gaugeType']);
                $logbook->setGaugeValueFrom($gaugeValue[0]);
                $logbook->setGaugeValueTo($gaugeValue[1]);
            }
            
            if (isset($dateTime)) {
                $logbook->setDateTime($dateTime);
            }
            $violationList = $logbook->validate();
            if (count($violationList) == 0) {
                $id = $logbook->save();
                header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=logbook");
            } else {
                $this->smarty->assign('creationTime', $post['dateTime']);
            }
        }else{
            $logbook->load();
            //check for add or edit
            $creationTime = $logbook->getDateTime();
            if (!is_null($creationTime)) {
                $creationTime = date($timeFormat . ' H:i', $creationTime);
                $this->smarty->assign('creationTime', $creationTime);
            }
        }
        
        $this->smarty->assign('logbook', $logbook);

        //set left menu
        $this->setListCategoriesLeftNew($category, $categoryId);
        $this->setPermissionsNew($category);

        //get inspection types list
        $lbmanager = new LogbookManager();
        $jsonInspectionalTypeList = $lbmanager->getInspectionTypeListInJson();
        $this->smarty->assign('jsonInspectionalTypeList', $jsonInspectionalTypeList);
        $inspectionTypesList = $lbmanager->getInspectionType();

        $inspectionSubTypesList = $lbmanager->getInspectionSubTypeByTypeDescription($logbook->getInspectionType());

        //get description
        $logbookDescriptionsList = $lbmanager->getLogbookDescriptionsList();

        //get inspection person list
        $inspectionPersonList = $lbmanager->getLogbookInspectionPersonListByFacilityId($facilityId);
        
        //get gauges
        $gaugeList = $lbmanager->getGaugeList();

        $this->smarty->assign('gaugeList', $gaugeList);
        $this->smarty->assign('violationList', $violationList);
        $this->smarty->assign('inspectionPersonList', $inspectionPersonList);
        $this->smarty->assign('inspectionTypesList', $inspectionTypesList);
        $this->smarty->assign('inspectionSubTypesList', $inspectionSubTypesList);
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
            "modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/jquery.slider.js",
            "modules/js/manageLogbookRecord.js",
        );

        $cssSources = array(
            'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css',
            'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/css/jslider.css'
        );

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

        $jsSources = array(
            'modules/js/autocomplete/jquery.autocomplete.js',
            'modules/js/checkBoxes.js',
        );
        $lbManager = new LogbookManager();
        
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
            $creationDateTime = date($timeFormat . ' H:i', $creationDateTime);
            $creationDateTime = explode(' ', $creationDateTime);
            //initialize inspection person
            $inspectionPerson = new LogbookInspectionPerson();
            $inspectionPerson->setId($logbookRecord->getInspectionPersonId());
            $inspectionPerson->load();

            $logbook = array(
                'logbookId' => $logbookRecord->getId(),
                'inspectionType' => $logbookRecord->getInspectionType(),
                'creationDate' => $creationDateTime[0],
                'creationTime' => $creationDateTime[1],
                'inspectionPersonName' => $inspectionPerson->getName()
            );

            $logbookList[] = $logbook;
        }
        
        $this->smarty->assign('pagination', $pagination);
        $this->smarty->assign('facilityId', $facilityId);
        $this->smarty->assign('logbookList', $logbookList);
        $this->smarty->assign('jsSources', $jsSources);
        $tpl = 'tpls/viewLogbook.tpl';
        $this->smarty->assign('tpl', $tpl);
    }

    /**
     * ajax method for loading Dialog Inspection Person
     */
    protected function actionLoadAddInspectionPersonDetails()
    {
        $facilityId = $this->getFromPost('facilityId');
        $this->smarty->assign('facilityId', $facilityId);
        echo $this->smarty->fetch('tpls/viewAddInspectionPerson.tpl');
    }

    /**
     *  ajax method for saving Inspection Person
     */
    protected function actionSaveInspectionPerson()
    {
        $inspectionPerson = new LogbookInspectionPerson();
        $inspectionPerson->setName($this->getFromPost('personName'));
        $inspectionPerson->setFacilityId($this->getFromPost('facilityId'));
        $id = $inspectionPerson->save();
        echo $id;
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
        $creationTime = date($timeFormat . ' H:i', $creationTime);
        $this->smarty->assign('creationTime', $creationTime);

        // set left menu
        $this->setListCategoriesLeftNew($category, $categoryId);
        $this->setPermissionsNew($category);

        $tpl = 'tpls/viewLogbookDetails.tpl';
        $this->smarty->assign('logbook', $logbook);
        $this->smarty->assign('tpl', $tpl);
        $this->smarty->display('tpls:index.tpl');
    }

    public function actionDeleteItem()
    {
        $facility = new Facility($this->db, $this->getFromRequest('facilityID'));

        if (!$this->user->checkAccess('logbook', $facility->getCompanyId())) {
            throw new Exception('deny');
        }

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

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark' => 'logbook'));
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $this->setPermissionsNew('facility');

        $this->smarty->assign('facilityID', $this->getFromRequest('facilityID'));
        $this->smarty->assign('cancelUrl', "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=logbook");
        $this->smarty->assign('notViewChildCategory', true);
        $this->finalDeleteItemCommon($itemsForDelete, $linkedNotify, $count, $info);
    }

    public function actionConfirmDelete()
    {
        $facility = new Facility($this->db, $this->getFromPost('facilityID'));
        
        if (!$this->user->checkAccess('logbook', $facility->getCompanyId())) {
            throw new Exception('deny');
        }
        $logbooksIds = $this->itemID;
        
        foreach ($logbooksIds as $id) {
            $logbook = new LogbookRecord($this->db);
            $logbook->setId($id);
            $logbook->delete();
        }

        header("Location: ?action=browseCategory&category=facility&id=" . $facility->getFacilityId() . "&bookmark=logbook");
    }

}
?>
