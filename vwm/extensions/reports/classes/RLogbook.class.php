<?php

use \VWM\Hierarchy\Facility;
use \VWM\Hierarchy\Company;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\UnitType\Entity\UnitType;
use VWM\Apps\Logbook\Entity\LogbookDescription;
use VWM\Apps\Logbook\Entity\LogbookEquipment;

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
    
    /**
     *
     * inspection type id
     * 
     * @var int|string 
     */
    private $inspection_type_id;
    
    /**
     *
     * gauge_id
     * 
     * @var int 
     */
    private $gauge_Id;
    
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
    
    public function getInspectionTypeId()
    {
        return $this->inspection_type_id;
    }

    public function setInspectionTypeId($inspectionTypeId)
    {
        $this->inspection_type_id = $inspectionTypeId;
    }

    public function getGaugeId()
    {
        return $this->gauge_Id;
    }

    public function setGaugeId($gaugeId)
    {
        $this->gauge_Id = $gaugeId;
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
        $equipment = $this->getEquipmentId();
        
        $query = "SELECT lb.facility_id, lb.date_time, lb.inspection_type_id, i.name, " .
                 "lb.gauge_type, lb.gauge_value_from, lb.gauge_value_to, ld.description, lb.unittype_id ".
                 "FROM " . LogbookRecord::TABLE_NAME . " lb " .
                 "LEFT JOIN " . LogbookInspectionPerson::TABLE_NAME . " i " .
                 "ON lb.inspection_person_id = i.id " .
                 "LEFT JOIN " . LogbookDescription::TABLE_NAME . " ld " .
                 "ON lb.description_id = ld.id " .
                 "WHERE lb.facility_id = {$db->sqltext($this->getCategoryId())} " .
                 "AND lb.date_time >= " . $dateBeginObj->getTimestamp() . " " .
                 "AND lb.date_time <= " . $dateEndObj->getTimestamp();
        if($equipment != 'all' ){
           $query.= " AND equipment_id = {$db->sqltext($equipment)}";
        }                 
                      
        if($this->getInspectionTypeId() != 'all'){
            $query.= " AND lb.inspection_type_id = {$db->sqltext($this->getInspectionTypeId())} ";
        }
                 
        if($this->getGaugeId() !='all'){
            $query.= " AND lb.gauge_type = {$db->sqltext($this->getGaugeId())}";
        }
        
        $db->query($query);
        if ($db->num_rows()) {
            $rows = $db->fetch_all();
        }
        
        $results = $this->group($rows, $companyId);
        
        $facility = new Facility($db, $categoryId);
        $companyId = $facility->getCompanyId();
        $company = new Company($db, $companyId);
        
        $country = new Country($db);
        $countryDetails = $country->getCountryDetails($company->getCountry());
        
        //getEquipment
        $equipmentName = 'All Equipments';
        
        if($equipment != 'all'){
            $logbookEquipment = new LogbookEquipment();
            $logbookEquipment->setId($equipment);
            $logbookEquipment->load();
            $equipmentName = $logbookEquipment->getEquipDesc();
        }
        
        $orgInfo = array(
            'category' => "Facility",
            'facilityName' => $facility->getName(),
            'companyName' => $company->getName(),
            'companyAddress' => $company->getAddress(),
            'cityStateZip'=>$company->getCity().','.$company->getState().','.$company->getZip(),
            'country' => $countryDetails['country_name'],
            'phone' => $company->getPhone(),
            'fax' => $company->getFax(),
            'equipment' => $equipmentName
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
            $unittypeId = $row->unittype_id;
            
            if(!is_null($row->gauge_type)){
               $gauge =  $gaugeList[$row->gauge_type]['name'];
            }else{
                $gauge = 'NONE';
            }
                    
            $dateTime = $row->date_time;
            $dateTime = date($timeFormat . ' H:i', $dateTime);
            $dateTime = explode(' ', $dateTime);
            
            $unittype = new UnitType($db);
            $unittype->setUnitTypeId($unittypeId);
            $unittype->load();
            $unitTypeName = $unittype->getName();
            if(!is_null($unittype->getName())){
                $unitTypeName = "(".$unittype->getName().")";
            }
            
            $result = array(
                'date' => $dateTime[0],
                'inspectionPerson' => $row->name,
                'gauge' => $gauge,
                'start' => $row->gauge_value_from,
                'end' => $row->gauge_value_to,
                'description' => $row->description,
                'unittype' => $unitTypeName
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
        
        //add facility 
        $facilityName = $doc->createElement("facilityName");
        $facilityName->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["facilityName"]))
        );
        $page->appendChild($facilityName);
        
        //add company
        $companyName = $doc->createElement("companyName");
        $companyName->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["companyName"]))
        );
        $page->appendChild($companyName);
        
        
        //add equipment
        $equipment = $doc->createElement("equipment");
        $equipment->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo["equipment"]))
        );
        $page->appendChild($equipment);
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
            $tempStartTag = $doc->createAttribute('start');
            $tempStartTag->appendChild(
                         $doc->createTextNode(html_entity_decode($result['start']))
                );
           $logbookInspection->appendChild($tempStartTag);

           //create temp End
           $tempEndTag = $doc->createAttribute('end');
           $tempEndTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['end']))
                );
           $logbookInspection->appendChild($tempEndTag);
           
           //create temp End
           $descriptionTag = $doc->createAttribute('description');
           $descriptionTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['description']))
                );
           $logbookInspection->appendChild($descriptionTag);
           
           //create temp End
           $unittypeTag = $doc->createAttribute('unittype');
           $unittypeTag->appendChild(
                        $doc->createTextNode(html_entity_decode($result['unittype']))
                );
           $logbookInspection->appendChild($unittypeTag);
        }
        $doc->save($fileName);
    }
}
?>
