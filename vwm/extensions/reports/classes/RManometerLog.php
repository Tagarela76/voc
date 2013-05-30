<?php

use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Hierarchy\Facility;

class RManometerLog extends ReportCreator implements iReportCreator
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
        $gaugeId = LogbookRecord::MANOMETER_GAUGE;

        $query = "SELECT lb.facility_id, i.name, lb.date_time, " .
                "lb.gauge_value_from, lb.gauge_value_to, lb.description " .
                "FROM " . LogbookRecord::TABLE_NAME . " lb " .
                "LEFT JOIN " . LogbookInspectionPerson::TABLE_NAME . " i " .
                "ON lb.inspection_person_id = i.id " .
                "WHERE lb.equipmant_id = {$db->sqltext($this->getEquipmentId())} " .
                "AND lb.gauge_type ={$gaugeId} " .
                "AND lb.date_time >= " . $dateBeginObj->getTimestamp() . " " .
                "AND lb.date_time <= " . $dateEndObj->getTimestamp() . " ";

        $facility = new Facility($db, $this->getCategoryId());
        $companyId = $facility->getCompanyId();
        $equipment = new Equipment($db);
        $equipmentDetails = $equipment->getEquipmentDetails($this->getEquipmentId());

        $orgInfo = array(
            'category' => "Facility",
            'name' => $facility->getName(),
            'equipment' =>$equipmentDetails['equip_desc']
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

        foreach ($rows as $row) {
            $dateTime = $row->date_time;
            $dateTime = date($timeFormat . ' H:i', $dateTime);
            $dateTime = explode(' ', $dateTime);

            $result = array(
                'date' => $dateTime[0],
                'time' => $dateTime[1],
                'inspectionPerson' => $row->name,
                'valueFrom' => $row->gauge_value_from,
                'valueTo' => $row->gauge_value_to,
                'description' => $row->description
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

        //create title tagbijiben
        $title = $doc->createElement("title");
        $title->appendChild(
                $doc->createTextNode("MANOMETER READINGS LOG")
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
            $record = $doc->createElement("logbookInspection");
            $table->appendChild($record);
            //create date
            $dateTag = $doc->createAttribute('date');
            $dateTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['date']))
                );
            $record->appendChild($dateTag);

            //time
            $timeTag = $doc->createAttribute('time');
            $timeTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['time']))
                );
            $record->appendChild($timeTag);

            //create Start
            $readingValue = $doc->createAttribute('readingValue');
            $readingValue->appendChild(
                         $doc->createTextNode(html_entity_decode($result['valueTo']))
                );
            $record->appendChild($readingValue);

            //create inspection Person
           $inspectedPersonTag = $doc->createAttribute('inspectedPerson');
           $inspectedPersonTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['inspectionPerson']))
                );
           $record->appendChild($inspectedPersonTag);

        }

        $doc->save($fileName);
    }
}
