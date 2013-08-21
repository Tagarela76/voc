<?php

use VWM\Apps\WorkOrder\Entity\WorkOrder;
use VWM\Apps\Process\ProcessInstance;
use VWM\Apps\Process\StepInstance;
use VWM\Apps\Process\ResourceInstance;
use VWM\Hierarchy\Department;
use VWM\Hierarchy\Company;
use VWM\Hierarchy\Facility;

class RcostingProduct extends ReportCreator implements iReportCreator
{

    /**
     * 
     * date begit
     * 
     * @var string 
     * 
     */
    private $dateBegin;

    /**
     *
     * date end
     * 
     * @var string 
     * 
     */
    private $dateEnd;

    /**
     *
     * resource type of mix in workOrder
     * 
     * @var int 
     * 
     */
    private $type;

    /**
     *
     * date format
     * 
     * @var string
     *  
     */
    private $dateFormat;

    const TIME = 1;
    const VOLUME = 2;
    const GOM = 3;

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

    public function getType()
    {
        return $this->type;
    }

    public function setType($data)
    {
        $this->type = $data;
    }

    public function __construct($db, $reportRequest = null)
    {
        $this->db = $db;
        if (!is_null($reportRequest)) {
            $this->categoryType = $reportRequest->getCategoryType();
            $this->categoryID = $reportRequest->getCategoryID();
            $this->setDateBegin($reportRequest->getDateBegin());
            $this->setDateEnd($reportRequest->getDateEnd());
            $extraVar = $reportRequest->getExtraVar();
            $this->setType($extraVar['type']);
            $this->setDateFormat($reportRequest->getDateFormat());
        }
    }

    /**
     * 
     * build xml function
     * 
     * @param string $fileName
     * 
     */
    public function buildXML($fileName)
    {
        $db = VOCApp::getInstance()->getService('db');
        $dateBeginObj = DateTime::createFromFormat($this->getDateFormat(), $this->getDateBegin());
        $dateEndObj = DateTime::createFromFormat($this->getDateFormat(), $this->getDateEnd());

        $categoryId = $this->categoryID;
        $categoryType = $this->categoryType;
        $resourceType = $this->getType();
        
        /*
         * Work order attached to facility. 
         * So we generate report by facility on department level
         */
        if ($categoryType == 'department') {
            $categoryType = 'facility';
            $department = new Department($db);
            $department->setDepartmentId($categoryId);
            $department->load();
            $categoryId = $department->getFacilityId();
        }
        
        switch ($categoryType) {
            case "company":
                $company = new Company($db, $categoryId);
                $facilityList = $company->getFacilities();
                $facilityIds = array();
                if(empty($facilityIds)){
                    throw new Exception("Create Facility first");
                }
                foreach($facilityList as $facility){
                    $facilityIds[] = $facility->getFacilityId();
                }
                $facilityIds = implode(',', $facilityIds);
                $query = "SELECT r.description, w.number, s.description stepDescription, w.creation_time, r.rate_qty, r.total_cost FROM " . WorkOrder::TABLE_NAME . " w " .
                        "RIGHT JOIN " . ProcessInstance::TABLE_NAME . " p " .
                        "ON p.work_order_id = w.id " .
                        "LEFT JOIN " . StepInstance::TABLE_NAME . " s " .
                        "ON s.process_id = p.id " .
                        "RIGHT JOIN " . ResourceInstance::TABLE_NAME . " r " .
                        "ON r.step_id = s.id " .
                        "WHERE w.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND  w.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND  w.facility_id IN ({$db->sqltext($facilityIds)}) ".
                        "AND  r.resource_type_id = {$db->sqltext($resourceType)}";
                        
                 $category = "Company";
                 $categoryName = $company->getName();
                 $categoryDetails = $company->getAttributes();
                break;
            case "facility":
                $facility = new Facility($db, $categoryId);
                $query = "SELECT r.description, w.number, s.description stepDescription, w.creation_time, r.rate_qty, r.total_cost ".
                        "FROM " . WorkOrder::TABLE_NAME . " w " .
                        "RIGHT JOIN " . ProcessInstance::TABLE_NAME . " p " .
                        "ON p.work_order_id = w.id " .
                        "LEFT JOIN " . StepInstance::TABLE_NAME . " s " .
                        "ON s.process_id = p.id " .
                        "RIGHT JOIN " . ResourceInstance::TABLE_NAME . " r " .
                        "ON r.step_id = s.id " .
                        "WHERE w.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND  w.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND  w.facility_id = {$db->sqltext($categoryId)} ".
                        "AND  r.resource_type_id = {$db->sqltext($resourceType)}";
                 $category = "Facility";
                 $categoryName = $facility->getName();
                 $categoryDetails = $facility->getAttributes();
                break;
            default :
                throw new Exception('Unknown Category');
                break;
        }
     
        $db->query($query);
        $rows = $db->fetch_all_array();
        $productTypes = $this->groupComponents($rows);
        
        $country = new Country($db);
        $countryDetails = $country->getCountryDetails($categoryDetails['country']);
        
        // get title
        $orgInfo = array(
            'category' => $category,
            'categoryName' => $categoryName,
            'address' => $categoryDetails['address'],
            'country' => $countryDetails['country_name'],
            'city' => $categoryDetails['city'],
            'phone' => $categoryDetails['phone'],
            'fax' => $categoryDetails['fax'],
            'zip' => $categoryDetails['zip']
        );
        
        $this->createXML($this->getDateBegin(), $this->getDateEnd(), $fileName, $productTypes, $orgInfo);
    }

    /**
     * 
     * create xml function
     * 
     * @param string $dateBegin
     * @param string $dateEnd
     * @param string $fileName
     * @param string $productTypes[][]
     * @param string $orgInfo[]
     */
    private function createXML($dateBegin, $dateEnd, $fileName, $productTypes, $orgInfo)
    {
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        
        //create page element
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
        
        //create title tag
        $title = $doc->createElement("title");
        $title->appendChild(
                $doc->createTextNode("Costing Report by Product")
        );
        $page->appendChild($title);

        //create period tag
        $periodTag = $doc->createElement("period");
        $periodTag->appendChild(
                $doc->createTextNode("From " . $dateBegin . " To " . $dateEnd)
        );
        $page->appendChild($periodTag);
        
        //create category
        $categoryTag = $doc->createElement('category');
        $categoryTag->appendChild(
                $doc->createTextNode($orgInfo['category'])
        );
        $page->appendChild($categoryTag);

        //create country
        $countryTag = $doc->createElement('country');
        $countryTag->appendChild(
                $doc->createTextNode($orgInfo['country'])
        );
        $page->appendChild($countryTag);

        //create address
        $addressTag = $doc->createElement('address');
        $addressTag->appendChild(
                $doc->createTextNode($orgInfo['address'])
        );
        $page->appendChild($addressTag);

        //create phone
        $phoneTag = $doc->createElement('phone');
        $phoneTag->appendChild(
                $doc->createTextNode($orgInfo['phone'])
        );
        $page->appendChild($phoneTag);

        //create fax
        $faxTag = $doc->createElement('fax');
        $faxTag->appendChild(
                $doc->createTextNode($orgInfo['fax'])
        );
        $page->appendChild($faxTag);

        //create zip
        $zipTag = $doc->createElement('zip');
        $zipTag->appendChild(
                $doc->createTextNode($orgInfo['zip'])
        );
        $page->appendChild($zipTag);

        //create city
        $cityTag = $doc->createElement('city');
        $cityTag->appendChild(
                $doc->createTextNode($orgInfo['city'])
        );
        $page->appendChild($cityTag);

        //create category name
        $categoryNameTag = $doc->createElement('categoryName');
        $categoryNameTag->appendChild(
                $doc->createTextNode($orgInfo['categoryName'])
        );
        $page->appendChild($categoryNameTag);
        
        //create table
        $table = $doc->createElement("table");
        $page->appendChild($table);
        
        
        foreach ($productTypes as $description => $product) {
            $totalResourceCost = 0;
            
            $productTypeComponent = $doc->createElement("productType");
            $table->appendChild($productTypeComponent);

            //create product type description attribute
            $productTypeDescriptionAttribute = $doc->createAttribute('description');
            $productTypeDescriptionAttribute->appendChild(
                    $doc->createTextNode(html_entity_decode($description))
            );
            $productTypeComponent->appendChild($productTypeDescriptionAttribute);

            //get mix resources
            foreach ($product as $resource) {
                $resourceComponent = $doc->createElement("resource");
                $productTypeComponent->appendChild($resourceComponent);
                
                //create Work order number attribute
                $woNumberAttribute = $doc->createAttribute('number');
                $woNumberAttribute->appendChild(
                        $doc->createTextNode(html_entity_decode($resource['number']))
                );
                $resourceComponent->appendChild($woNumberAttribute);
                
                //create step description attribute
                $stepDescriptionAttribute = $doc->createAttribute('stepDescription');
                $stepDescriptionAttribute->appendChild(
                        $doc->createTextNode(html_entity_decode($resource['stepDescription']))
                );
                $resourceComponent->appendChild($stepDescriptionAttribute);
                
                $date = date($this->getDateFormat(), $resource['creation_time']);
                
                //create date attribute
                $dateAttribute = $doc->createAttribute('date');
                $dateAttribute->appendChild(
                        $doc->createTextNode(html_entity_decode($date))
                );
                $resourceComponent->appendChild($dateAttribute);
                
                //create qty attribute
                $qtyAttribute = $doc->createAttribute('qty');
                $qtyAttribute->appendChild(
                        $doc->createTextNode(html_entity_decode($resource['rate_qty']))
                );
                $resourceComponent->appendChild($qtyAttribute);
                
                //create cost attribute
                $costAttribute = $doc->createAttribute('cost');
                $costAttribute->appendChild(
                        $doc->createTextNode(html_entity_decode($resource['total_cost']))
                );
                $resourceComponent->appendChild($costAttribute);
                
                $totalResourceCost+=$resource['total_cost'];
            }
            $totalResourceCost = round($totalResourceCost, 2);
            //create total cost attribute
            $productTypeTotalCostAttribute = $doc->createAttribute('TotalCost');
            $productTypeTotalCostAttribute->appendChild(
                    $doc->createTextNode(html_entity_decode($totalResourceCost))
            );
            $productTypeComponent->appendChild($productTypeTotalCostAttribute);
            
        }
        $doc->save($fileName);
    }
    /**
     * 
     * get request by vars
     * 
     * @param int $companyID
     * @return \ReportRequest
     * 
     */
    public function getReportRequestByGetVars($companyID)
    {
        $db = VOCApp::getInstance()->getService('db');
        //at first lets get data already filtered
        $categoryType = $_REQUEST['categoryLevel'];
        $id = $_REQUEST['id'];
        $reportType = $_REQUEST['reportType'];
        $format = $_REQUEST['format'];
        $extraVar['type'] = $_REQUEST['type'];

        //and get them too
        $dateBegin = new TypeChain($_GET['date_begin'], 'date', $db, $companyID, 'company');
        $dateEnd = new TypeChain($_GET['date_end'], 'date', $db, $companyID, 'company');

        //finally: lets get	reportRequest object!
        $reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id']);

        return $reportRequest;
    }

    public function groupComponents($rows)
    {
        $productTypes = array();
        foreach($rows as $row){
            $productTypes[$row['description']][] = $row;
        }
        return $productTypes;
    }

}
?>
