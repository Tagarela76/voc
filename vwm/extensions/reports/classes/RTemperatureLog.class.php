<?php

use \VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Hierarchy\Facility;
use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Hierarchy\Department;

class RTemperatureLog extends ReportCreator implements iReportCreator
{

    private $dateBegin;
    private $dateEnd;
    private $dateFormat;
    private $equipment_id;
    /**
     *
     * @var int category id
     */
    private $categoryId;

    function __construct($db, $reportRequest = null)
    {
        $this->db = $db;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    public function setDateBegin($dateBegin)
    {
        $this->dateBegin = $dateBegin;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getEquipmentId()
    {
        return $this->equipment_id;
    }

    public function setEquipmentId($equipmentId)
    {
        $this->equipment_id = $equipmentId;
    }

        public function buildXML($fileName)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $dateBeginObj = $this->getDateBegin();
        $dateEndObj = $this->getDateEnd();
        $temperatureGaugeId = LogbookRecord::TEMPERATURE_GAUGE;

        $query = "SELECT lb.facility_id, i.name, lb.date_time, " .
                "lb.gauge_value_from, lb.gauge_value_to, lb.description " .
                "FROM " . LogbookRecord::TABLE_NAME . " lb " .
                "LEFT JOIN " . LogbookInspectionPerson::TABLE_NAME . " i " .
                "ON lb.inspection_person_id = i.id " .
                "WHERE lb.facility_id = {$db->sqltext($this->getCategoryId())} " .
                "AND lb.gauge_type ={$temperatureGaugeId} " .
                "AND lb.date_time >= " . $dateBeginObj->getTimestamp() . " " .
                "AND lb.date_time <= " . $dateEndObj->getTimestamp() . " ";

        $facility = new Facility($db, $this->getCategoryId());
        $companyId = $facility->getCompanyId();
        $equipmantId = $this->getEquipmentId();
        $equipmant = new Equipment($db);
        $equipmantDetails = $equipmant->getEquipmentDetails($equipmantId);
        
        $orgInfo = array(
            'category' => "Facility",
            'name' => $facility->getName(),
            'equipment' =>$equipmantDetails['equip_desc']
        );

        $db->query($query);
        if ($db->num_rows()) {
            $rows = $db->fetch_all();
        }
        
        $results = $this->group($rows, $companyId);

        $this->createXML($results, $orgInfo, $fileName);
    }

    private function group($rows, $companyId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $results = array();
        $dataChain = new TypeChain(null, 'date', $db, $companyId, 'company');
        $timeFormat = $dataChain->getFromTypeController('getFormat');
        
        foreach ($rows as $row){
            $dateTime = $row->date_time;
            $dateTime = date($timeFormat . ' H:i', $dateTime);
            $dateTime = explode(' ', $dateTime);
            
            $result = array(
                'date' => $dateTime[0],
                'inspectionPerson' => $row->name,
                'tempStart' => $row->gauge_value_from,
                'tempEnd' => $row->gauge_value_to,
                'description' => $row->description,
                'replacedBulbs' => $row->replaced_bulbs
            );
         $results[] = $result;
        }

        return $results;
    }

    private function createXML($results, $orgInfo, $fileName)
    {
        $doc = new DOMDocument();
        $doc->formatOutput = true;

        $page = $doc->createElement("page");
        $doc->appendChild($page);

        //create page attribute orientstion
        $pageOrientation = $doc->createAttribute("orientation");
        $pageOrientation->appendChild(
                $doc->createTextNode("p")
        );
        $page->appendChild($pageOrientation);

        //create page attribute topmargin
        $pageTopMargin = $doc->createAttribute("topmargin");
        $pageTopMargin->appendChild(
                $doc->createTextNode("10")
        );
        $page->appendChild($pageTopMargin);

        //create page attribute leftmargin
        $pageLeftMargin = $doc->createAttribute("leftmargin");
        $pageLeftMargin->appendChild(
                $doc->createTextNode("10")
        );
        $page->appendChild($pageLeftMargin);

        //create page attribute rightmargin
        $pageRightMargin = $doc->createAttribute("rightmargin");
        $pageRightMargin->appendChild(
                $doc->createTextNode("10")
        );
        $page->appendChild($pageRightMargin);

        //create new element meta
        $meta = $doc->createElement("meta");
        $page->appendChild($meta);

        $metaName = $doc->createAttribute("name");
        $metaName->appendChild(
                $doc->createTextNode("basefont")
        );
        $meta->appendChild($metaName);

        $metaValue = $doc->createAttribute("value");
        $metaValue->appendChild(
                $doc->createTextNode("times")
        );
        $meta->appendChild($metaValue);

        //create title tag
        $title = $doc->createElement("title");
        $title->appendChild(
                $doc->createTextNode("DAILY TEMPERATURE LOG")
        );
        $page->appendChild($title);

        //create title tag
        $equimpant = $doc->createElement("equipmentDesc");
        $equimpant->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["equipment"]))
        );
        $page->appendChild($equimpant);
        
        //create description tag
        $description = $doc->createElement("description");
        $description->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["description"]))
        );
        $page->appendChild($description);
        
        //create category 
        $categoryTag = $doc->createElement('category');
        //add company if exist
        if (($orgInfo["category"]) == 'Company') {

            $categoryTag->appendChild(
                    $doc->createTextNode('Company')
            );

            $companyName = $doc->createElement("categoryName");
            $companyName->appendChild(
                    $doc->createTextNode(html_entity_decode($orgInfo["name"]))
            );
            $page->appendChild($companyName);
        }
        //add facility if exist
        if (($orgInfo["category"]) == 'Facility') {

            $categoryTag->appendChild(
                    $doc->createTextNode('Facility')
            );

            $facilityName = $doc->createElement("categoryName");
            $facilityName->appendChild(
                    $doc->createTextNode(html_entity_decode($orgInfo["name"]))
            );
            $page->appendChild($facilityName);
        }
        //add department if exist

        if (($orgInfo["category"]) == 'Department') {

            $categoryTag->appendChild(
                    $doc->createTextNode('Department')
            );

            $departmentName = $doc->createElement("categoryName");
            $departmentName->appendChild(
                    $doc->createTextNode(html_entity_decode($orgInfo["name"]))
            );
            $page->appendChild($departmentName);
        }

        $page->appendChild($categoryTag);
        
        //create table
        $table = $doc->createElement("table");
        $page->appendChild($table);
        
        foreach($results as $result){
            $temperatureLb = $doc->createElement("logbookInspection");
            $table->appendChild($temperatureLb);
            //create date
            $dateTag = $doc->createAttribute('date');
            $dateTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['date']))
                );
           $temperatureLb->appendChild($dateTag);
           //create Start
           $tempStartTag = $doc->createAttribute('tempStart');
           $tempStartTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['tempStart']))
                );
           $temperatureLb->appendChild($tempStartTag);
           
           //create temp End 
           $tempEndTag = $doc->createAttribute('tempEnd');
           $tempEndTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['tempEnd']))
                );
           $temperatureLb->appendChild($tempEndTag);
           
           //create replacedBulbs
           if($result['replacedBulbs'] == 1){
               $replacedBulbs = 'Yes';
           }else{
               $replacedBulbs = 'No';
           }
           $replacedBulbsTag = $doc->createAttribute('replacedBulbs');
           $replacedBulbsTag->appendChild(
                        $doc->createTextNode(html_entity_decode($replacedBulbs))
                );
           $temperatureLb->appendChild($replacedBulbsTag);
           
            
            //create inspection Person
           $inspectedPersonTag = $doc->createAttribute('inspectedPerson');
           $inspectedPersonTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['inspectionPerson']))
                );
           $temperatureLb->appendChild($inspectedPersonTag);
           
        } 

        $doc->save($fileName);
    }
    
   /* public function getReportRequestByGetVars($companyID) {
		//at first lets get data already filtered
		$categoryType = $_REQUEST['categoryLevel'];
		$id = $_REQUEST['id'];
		$reportType = $_REQUEST['reportType'];
		$format = $_REQUEST['format'];
        $equipmentId = $_REQUEST['equipmentId'];

		//and get them too
		$dateBegin = new TypeChain($_GET['date_begin'],'date',$this->db,$companyID,'company');
	    $dateEnd = new TypeChain($_GET['date_end'],'date',$this->db,$companyID,'company');

		$extraVar['rule'] = $_REQUEST['logs'];

		$data['responsiblePerson'] = (($_REQUEST['responsiblePerson'] == "[Responsible Person]") ? "" : $_REQUEST['responsiblePerson']);
		$data['title'] = (($_REQUEST['title'] == "[Title]") ? "" : $_REQUEST['title']);
		$data['notes'] = (($_REQUEST['notes'] == "[Notes]") ? "" : $_REQUEST['notes']);
        $data['spentTime'] = (isset($_REQUEST['spentTime']) ? true : false);
        $data['totalCost'] = (isset($_REQUEST['totalCost']) ? true : false);
		$extraVar['data'] = $data;
        
		//lets set extra vars in case its csv format
		if ($format == "csv") {
			$extraVar['commaSeparator'] = $_REQUEST['commaSeparator'];
			$extraVar['textDelimiter'] = $_REQUEST['textDelimiter'];
			if (strstr($extraVar['commaSeparator'],"\\")) {
				$extraVar['commaSeparator'] = substr(strstr($extraVar['commaSeparator'],"\\"),1);
			}
			if (strstr($extraVar['textDelimiter'],"\\")) {
				$extraVar['textDelimiter'] = str_replace("\\","",$extraVar['textDelimiter']);
			}
		}

		//finally: lets get	reportRequest object!
		$reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id'], $equipmentId);
		return $reportRequest;
	}*/

}
?>
