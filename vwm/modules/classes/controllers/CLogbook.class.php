<?php

use VWM\Hierarchy\Facility;
use VWM\Apps\Logbook\Manager\LogbookManager;

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
        } else {
            throw new Exception('404');
        }

        //$this->setNavigationUpNew($category, $categoryId);
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

        $this->smarty->assign('inspectionTypesList', $inspectionTypesList);
        $this->smarty->assign('inspectionSubTypesList', $inspectionSubTypesList);
        $this->smarty->assign('logbookDescriptionsList', $logbookDescriptionsList);

        //get dateChain
        $dataChain = new TypeChain(null, 'date', $this->db, $companyId, 'company');
        $this->smarty->assign('dataChain', $dataChain);

        $tpl = 'tpls/addLogbookRecord.tpl';
        $jsSources = array(
            "modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
            "modules/js/manageInspectionTypeList.js",
        );

        $cssSources = array(
            'modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css'
        );
        
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

}
?>
