<?php

use \VWM\Hierarchy\Facility;
use \VWM\Hierarchy\Company;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Apps\Logbook\Manager\LogbookManager;

class RLogbook extends ReportCreator implements iReportCreator
{
    /**
     *
     * date begin
     * 
     * @var string 
     */
    private $dateBegin;
    
    /**
     *
     * date end
     * 
     * @var string
     */
    private $dateEnd;
    
    /**
     *
     * @var date Format
     */
    private $dateFormat;
    
    /**
     *
     * equimmant id
     * 
     * @var int
     */
    private $equipment_id;
    
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
    
    public function getEquipmentId()
    {
        return $this->equipment_id;
    }

    public function setEquipmentId($equipmentId)
    {
        $this->equipment_id = $equipmentId;
    }
    
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }
    
    /*
     * get information for xml file
     */
    public function buildXML($fileName)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $categoryId = $this->getCategoryId();
        $dateBeginObj = $this->getDateBegin();
        $dateEndObj = $this->getDateEnd();
        
        $query = "SELECT lb.facility_id, lb.date_time, lb.inspection_type_id, i.name, " .
                 "lb.gauge_type, lb.gauge_value_from, lb.gauge_value_to	".
                 "FROM " . LogbookRecord::TABLE_NAME . " lb " .
                 "LEFT JOIN " . LogbookInspectionPerson::TABLE_NAME . " i " .
                 "ON lb.inspection_person_id = i.id " .
                 "WHERE lb.facility_id = {$db->sqltext($this->getCategoryId())} " .
                 "AND lb.date_time >= " . $dateBeginObj->getTimestamp() . " " .
                 "AND lb.date_time <= " . $dateEndObj->getTimestamp() . " ";
        
                
        $db->query($query);
        if ($db->num_rows()) {
            $rows = $db->fetch_all();
        }
        
        $results = $this->group($rows, $companyId);
        
        $facility = new Facility($db, $categoryId);
        $companyId = $facility->getCompanyId();
        $company = new Company($db, $companyId);
        
        $orgInfo = array(
            'category' => "Facility",
            'categoryName' => $facility->getName(),
            'companyAddress' => $company->getAddress(),
            'cityStateZip'=>$company->getCity().','.$company->getState().','.$company->getZip(),
            'country' => $company->getCountry(),
            'phone' => $company->getPhone(),
            'fax' => $company->getFax(),
        );
        
        $this->createXML($results, $orgInfo, $fileName);
    }
    /*
     * group select result
     */
    private function group($rows, $companyId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $itManager = new LogbookManager();
        $gaugeList = $itManager->getGaugeList($facilityId);
        $results = array();
        $dataChain = new TypeChain(null, 'date', $db, $companyId, 'company');
        $timeFormat = $dataChain->getFromTypeController('getFormat');
        $gauge = '';
        foreach ($rows as $row){
            if(!is_null($row->gauge_type)){
               $gauge =  $gaugeList[$row->gauge_type]['name'];
            }else{
                $gauge = 'NONE';
            }
            var_dump($gauge);
                    
            $dateTime = $row->date_time;
            $dateTime = date($timeFormat . ' H:i', $dateTime);
            $dateTime = explode(' ', $dateTime);
            
            $logbookInspectionType = new LogbookInspectionType($db);
            $logbookInspectionType->setId($row->inspection_type_id);
            $logbookInspectionType->load();
            
            $inspectionType = $logbookInspectionType->getInspectionType();
            
            $result = array(
                'date' => $dateTime[0],
                'inspectionPerson' => $row->name,
                'gauge' => $gauge,
                'tempStart' => $row->gauge_value_from,
                'tempEnd' => $row->gauge_value_to,
                'replacedBulbs' => $row->replaced_bulbs
            );
         $results[] = $result;
        }
        
        return $results;
    }
    /*
     * create xml file
     */
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
                $doc->createTextNode("DAILY LOGBOOK REPORT")
        );
        $page->appendChild($title);
        
        //create category
        $categoryTag = $doc->createElement('category');
        $categoryTag->appendChild(
                $doc->createTextNode('Facility')
        );
        $page->appendChild($categoryTag);
        
        //create address tag 
        $address = $doc->createElement("companyAddress");
        $address->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["companyAddress"]))
        );
        $page->appendChild($address);
        
        //create city_State_Zip tag
        $cityStateZip = $doc->createElement("cityStateZip");
        $cityStateZip->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["cityStateZip"]))
        );
        $page->appendChild($cityStateZip);
        
        //create country tag
        $country = $doc->createElement("country");
        $country->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["country"]))
        );
        $page->appendChild($country);
        //create phone tag
        $phone = $doc->createElement("phone");
        $phone->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["phone"]))
        );
        $page->appendChild($phone);
        
        //create fax tag
        $fax = $doc->createElement("fax");
        $fax->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["fax"]))
        );
        $page->appendChild($fax);
        
        //create description tag
        $description = $doc->createElement("description");
        $description->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["description"]))
        );
        $page->appendChild($description);
        
        //add facility if exist
        $facilityName = $doc->createElement("categoryName");
        $facilityName->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["categoryName"]))
        );
        $page->appendChild($facilityName);
        
        //CREATE TABLE ELEMENT
        //create table
        $table = $doc->createElement("table");
        $page->appendChild($table);
        
        foreach($results as $result){
            $logbookInspection = $doc->createElement("logbookInspection");
            $table->appendChild($logbookInspection);
            
            //create date
            $dateTag = $doc->createAttribute('date');
            $dateTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['date']))
                );
           $logbookInspection->appendChild($dateTag);
           
            //create gauge type
           $gaugeTag = $doc->createAttribute('gauge');
           $gaugeTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['gauge']))
                );
           $logbookInspection->appendChild($gaugeTag);
           
           //create inspection Person
           $inspectedPersonTag = $doc->createAttribute('inspectedPerson');
           $inspectedPersonTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['inspectionPerson']))
                );
           $logbookInspection->appendChild($inspectedPersonTag);
           
           //create Start
            $tempStartTag = $doc->createAttribute('tempStart');
            $tempStartTag->appendChild(
                         $doc->createTextNode(html_entity_decode($result['tempStart']))
                );
           $logbookInspection->appendChild($tempStartTag);

           //create temp End
           $tempEndTag = $doc->createAttribute('tempEnd');
           $tempEndTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['tempEnd']))
                );
           $logbookInspection->appendChild($tempEndTag);
           
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
           $logbookInspection->appendChild($replacedBulbsTag);
           
        }
        $doc->save($fileName);
    }
}
?>
