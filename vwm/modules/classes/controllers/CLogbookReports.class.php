<?php

use VWM\Hierarchy\Facility;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;
use \VWM\Apps\Logbook\Manager\LogbookManager;
use \VWM\Apps\Logbook\Manager\LogbookEquipmentManager;

class CLogbookReports extends Controller
{
    public function __construct($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'logbook';
    }
    /**
     * 
     * display logbook report settings 
     * 
     */
    public function actionSendLogbookReport()
    {
        $request = $this->getFromRequest();
        $this->smarty->assign("request", $request);
        $this->noname();

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('id'), array('bookmark' => 'logbook'));
        $this->setNavigationUpNew('facility', $this->getFromRequest('id'));
        $this->setPermissionsNew('facility');

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);

        $facility = new Facility($this->db, $request['id']);

        $facilityID = $request['id'];
        $companyID = $facility->getCompanyId();

        $reportType = $request['reportType'];


        $ms = new ModuleSystem($this->db); //	TODO: show?
        $moduleMap = $ms->getModulesMap();

        $mReport = new $moduleMap['reports'];

        $params = array(
            'db' => $this->db,
            'reportType' => $reportType,
            'companyID' => $companyID,
            'request' => $request,
            'facilityID' => $facilityID,
            'reportType' => $reportType,
        );

        $result = $this->prepareSendReport($params);

        foreach ($result as $key => $data) {
            $this->smarty->assign($key, $data);
        }

        //	set js scripts
        $jsSources = array(
            'modules/js/reports.js',
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
        );
        $this->smarty->assign('jsSources', $jsSources);
        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('backUrl',
                '?action=viewLogbookReports&category=logbook&facilityId='.$request['id']);

        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * function prepareSendReport($params) - prepare params for smarty
     * 
     * @param array $params - $db, $reportType, $companyID, $request
     * 
     * @return array params prepared for smarty
     */
    public function prepareSendReport($params)
    {
        extract($params);
        $reportType = $params['reportType'];
        $result["reportName"] = $reportType;

        $result["subReport"] = $reportType;
        $result["tpl"] = $this->getInputTPLfileName($reportType);
        $result["dataChain"] = new TypeChain(null, 'date', $db, $companyID, 'company');

        $dateObj = new DateTime;
        $clone = clone $dateObj;

        while (( $dateObj->format('Y') - $clone->format('Y') ) <= 2) {
            $listdate['text'] = $clone->format('m/y');
            $listdate['value'] = $clone->format('m/01/y');
            $mas[] = $listdate;
            $clone->sub(new DateInterval('P1M'));
        }
        $result["monthes"] = $mas;

        //get Equipmant list
        $leManager = new LogbookEquipmentManager();
        $logbookEquipmentList = $leManager->getLogbookEquipmentListByFacilityId($params['facilityID']);
        $result['equipments'] = $logbookEquipmentList;
        
        //get inspection Type List
        $itManager = new InspectionTypeManager();
        $inspectionTypeList = $itManager->getInspectionTypeListByFacilityId($params['facilityID']);
        $result['inspectionTypeList'] = $inspectionTypeList;
        
        $lbManager = new LogbookManager();
        $gaugeList = $lbManager->getGaugeList($facilityId);
        $result['gaugeList'] = $gaugeList;
        
        return $result;
    }

    /**
     * Get path to report template
     *
     * @param string $reportType
     *
     * @return string path to template
     */
    public function getInputTPLfileName($reportType)
    {
        return 'reports/design/logbookInput.tpl';
    }

    /**
     * 
     * preapare logbook report information
     * 
     * @throws Exception
     */
    public function actionPrepareSendLogbookReport()
    {
        $reportType = $this->getFromRequest('reportType');
        $format = $this->getFromRequest('format');
        $facilityId = $this->getFromRequest('id');
        $userId = $_SESSION['user_id'];
        $equipmentId = $this->getFromRequest('equipmentId');
        $gaugeId = $this->getFromRequest('gaugeId');
        $inspectionTypeIds = $this->getFromRequest('inspectionTypeId');
        $itManager = new InspectionTypeManager();
        $inspectionTypeList = $itManager->getInspectionTypeListByFacilityId($facilityId);

        $facility = new Facility($this->db, $facilityId);
        $companyId = $facility->getCompanyId();

        $standartInputTPL = 'reports/design/standartInput.tpl';
        $currentTpl = $this->getInputTPLfileName($reportType);

        $dateBegin = new TypeChain($this->getFromRequest('date_begin'), 'date', $this->db, $companyId, 'company');
        $dateEnd = new TypeChain($this->getFromRequest('date_end'), 'date', $this->db, $companyId, 'company');

        $frequency = null;

        //create xml
        $xmlFileName = 'tmp/reportByUser' . $userId . '.xml';
        $reportClassName = 'R'.$reportType;
        
        if (class_exists($reportClassName)) {
            $xml = new $reportClassName($this->db);
        } else {
            throw new Exception('Cannot find report of type '.$reportType);
        }
        
        $xml->setCategoryId($facilityId);
        $xml->setDateBegin($dateBegin);
        $xml->setDateEnd($dateEnd);
        $xml->setEquipmentId($equipmentId);
        $xml->setInspectionTypeId($inspectionTypeIds);
        $xml->setGaugeId($gaugeId);
        $xml->BuildXML($xmlFileName);

        $pdf = new PDFBuilder($xmlFileName, $reportType, $extraVar);
    }

}
?>
