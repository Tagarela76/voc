<?php

use VWM\Hierarchy\Facility;

class CLogbookReports extends Controller
{

    public function __construct($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'logbook';
    }

    public function actionSendLogbookReport()
    {
        $request = $this->getFromRequest();
        $this->smarty->assign("request", $request);
        $this->noname();

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

        //exit;
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
        $this->smarty->assign('backUrl', '?action=createReport&category=' . $request['category'] . '&id=' . $request['id']);

        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * function prepareSendReport($params) - prepare params for smarty
     * @param array $params - $db, $reportType, $companyID, $request
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

        //getting supplier list for projectCoat report
        if ($reportType == "projectCoat") {
            $supplierObj = new Supplier($db);
            $supplierList = $supplierObj->getSupplierList();
            $result["supplierList"] = $supplierList;
        }

        //get Equipmant list
        $equipmants = new Equipment($db);
        $result['equipments'] = $equipmants->getEquipmentListByFacilityId($params['facilityID']);

        return $result;
    }

    public function getInputTPLfileName($reportType)
    {
        if (file_exists('extensions/reports/design/' . $reportType . 'Input.tpl')) {
            return 'reports/design/' . $reportType . 'Input.tpl';
        } else {
            return 'reports/design/standartInput.tpl';
        }
    }

    public function actionPrepareSendLogbookReport()
    {
        $reportType = $this->getFromRequest('reportType');
        $format = $this->getFromRequest('format');
        $facilityId = $this->getFromRequest('id');
        $userId = $_SESSION['user_id'];
        $equipmentId = $this->getFromRequest('equipmentId');

        $facility = new Facility($this->db, $facilityId);
        $companyId = $facility->getCompanyId();

        $standartInputTPL = 'reports/design/standartInput.tpl';
        $currentTpl = $this->getInputTPLfileName($reportType);

        $dateBegin = new TypeChain($this->getFromRequest('date_begin'), 'date', $this->db, $companyId, 'company');
        $dateEnd = new TypeChain($this->getFromRequest('date_end'), 'date', $this->db, $companyId, 'company');


        $frequency = null;

        //create xml
        $xmlFileName = 'tmp/reportByUser' . $userId . '.xml';
        $xml = new RTemperatureLog($this->db);

        $xml->setCategoryId($facilityId);
        $xml->setDateBegin($dateBegin);
        $xml->setDateEnd($dateEnd);
        $xml->setEquipmentId($equipmentId);

        $xml->BuildXML($xmlFileName);

        $pdf = new PDFBuilder($xmlFileName, $reportType, $extraVar);
    }

}
?>
