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
        if ($this->getFromRequest('facilityID')) {
            $category = 'facility';
            $categoryId = $this->getFromRequest('facilityID');
            $facility = new Facility($this->db, $categoryId);
            $companyId = $facility->getCompanyId();
            $facilityId = $categoryId;
        } else {
            throw new Exception('404');
        }

        //save logBook if we need
        $post = $this->getFromPost();
        if (count($post) > 0) {
            //transfer time to unix type
            $dateTime = explode(' ', $post['dateTime']);
            $date = explode('/', $dateTime[0]);
            $time = explode(':', $dateTime[1]);
            $dateTime = mktime($time[0], $time[1], 0, $date[0], $date[1], $date[2]);
            $logbook = new LogbookRecord();
            $logbook->setDateTime($dateTime);
            $violationList = $logbook->validate();
        }

        //set left menu
        $this->setListCategoriesLeftNew($category, $categoryId);
        $this->setPermissionsNew($category);

        //get inspection types list
        $lbmanager = new LogbookManager();
        $jsonInspectionalTypeList = $lbmanager->getInspectionTypeListInJson();
        $this->smarty->assign('jsonInspectionalTypeList', $jsonInspectionalTypeList);
        $inspectionTypesList = $lbmanager->getInspectionType();
        $inspectionSubTypesList = $lbmanager->getInspectionSubTypeByTypeNumber(1);

        //get description
        $logbookDescriptionsList = $lbmanager->getLogbookDescriptionsList();
        
        //get inspection person list
        $inspectionPersonList = $lbmanager->getLogbookInspectionPersonListByFacilityId($facilityId);
        
        $this->smarty->assign('violationList', $violationList);
        $this->smarty->assign('inspectionPersonList', $inspectionPersonList);
        $this->smarty->assign('inspectionTypesList', $inspectionTypesList);
        $this->smarty->assign('inspectionSubTypesList', $inspectionSubTypesList);
        $this->smarty->assign('logbookDescriptionsList', $logbookDescriptionsList);

        //get dateChain
        $dataChain = new TypeChain(null, 'date', $this->db, $companyId, 'company');
        $this->smarty->assign('dataChain', $dataChain);

        $tpl = 'tpls/addLogbookRecord.tpl';
        $jsSources = array(
            "modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js",
            "modules/js/manageLogbookRecord.js",
        );

        $cssSources = array(
            'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css'
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
        //check for facility_id
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
        $jsSources = array(
            'modules/js/autocomplete/jquery.autocomplete.js',
        );
        $this->smarty->assign('jsSources', $jsSources);
        $tpl = 'tpls/viewLogbook.tpl';
        $this->smarty->assign('tpl', $tpl);
    }

    //ajax method
    protected function actionLoadAddInspectionPersonDetails()
    {
        $facilityId = $this->getFromPost('facilityId');
        $this->smarty->assign('facilityId', $facilityId);
        echo $this->smarty->fetch('tpls/viewAddInspectionPerson.tpl');
    }

    protected function actionSaveInspectionPerson()
    {
        $inspectionPerson = new LogbookInspectionPerson();
        $inspectionPerson->setName($this->getFromPost('personName'));
        $inspectionPerson->setFacilityId($this->getFromPost('facilityId'));
        $id = $inspectionPerson->save();
        echo $id;
    }

}
?>
