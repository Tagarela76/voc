<?php

use VWM\Hierarchy\Department;
use VWM\Hierarchy\Facility;
use \VWM\Hierarchy\Company;
use VWM\Apps\WorkOrder\Entity\WorkOrder;
use VWM\Hierarchy\FacilityManager;
use VWM\Apps\Process\ProcessInstance;
use VWM\Apps\Process\StepInstance;
use VWM\Apps\Process\ResourceInstance;

class Rcosting extends ReportCreator implements iReportCreator
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
     * date Format
     * 
     * @var string 
     */
    private $dateFormat;

    /**
     *
     * category type
     * 
     * @var string 
     */
    protected $categoryType;

    /**
     *
     * category id
     * 
     * @var int 
     */
    protected $categoryId;

    const TIME = 1;
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

    public function getCategoryType()
    {
        return $this->categoryType;
    }

    public function setCategoryType($categoryType)
    {
        $this->categoryType = $categoryType;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    function __construct($db, ReportRequest $reportRequest)
    {
        $this->db = $db;
        $this->setCategoryType($reportRequest->getCategoryType());
        $this->setCategoryId($reportRequest->getCategoryID());
        $this->setDateBegin($reportRequest->getDateBegin());
        $this->setDateEnd($reportRequest->getDateEnd());
        $this->setDateFormat($reportRequest->getDateFormat());
    }

    /**
     * 
     * build xml
     * 
     * @param string $fileName
     */
    public function buildXML($fileName)
    {
        $db = VOCApp::getInstance()->getService('db');
        $dateBeginObj = DateTime::createFromFormat($this->getDateFormat(), $this->getDateBegin());
        $dateEndObj = DateTime::createFromFormat($this->getDateFormat(), $this->getDateEnd());

        $categoryId = $this->getCategoryId();
        $category = $this->getCategoryType();

        /*
         * Work order attached to facility. 
         * So we generate report by facility on department level
         */
        if ($category == 'department') {
            $category = 'facility';
            $department = new Department($db);
            $department->setDepartmentId($categoryId);
            $department->load();
            $categoryId = $department->getFacilityId();
        }

        switch ($category) {
            case "company":
                $company = new Company($db, $categoryId);
                $fManager = new FacilityManager($db, $categoryId);
                $facilityList = $fManager->getFacilityListByCompanyId($categoryId);
                $facilityIds = array();
                foreach ($facilityList as $facility) {
                    $facilityIds[] = $facility->getFacilityId();
                }
                if(empty($facilityIds)){
                    throw new Exception('create facility first');
                }
                $facilityIds = implode(',', $facilityIds);
                $query = "SELECT p.id, p.work_order_id, w.number, w.profit, w.overhead, w.overhead_unit_type, w.profit_unit_type FROM " . WorkOrder::TABLE_NAME . " w " .
                        "RIGHT JOIN " . ProcessInstance::TABLE_NAME . " p " .
                        "ON p.work_order_id = w.id " .
                        "WHERE w.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND  w.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND  w.facility_id IN ({$facilityIds})";
                $categoryName = $company->getName();
                $category = 'Company';
                $categoryDetails = $company->getAttributes();
                break;
            case "facility":
                $facility = new Facility($db, $categoryId);
                $query = "SELECT p.id, p.work_order_id, w.number, w.profit, w.overhead, w.overhead_unit_type, w.profit_unit_type FROM " . WorkOrder::TABLE_NAME . " w " .
                        "RIGHT JOIN " . ProcessInstance::TABLE_NAME . " p " .
                        "ON p.work_order_id = w.id " .
                        "WHERE w.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND  w.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND  w.facility_id = {$db->sqltext($categoryId)} ";
                $categoryName = $facility->getName();
                $category = 'Facility';
                $categoryDetails = $facility->getAttributes();
                break;
        }

        $country = new Country($db);
        $countryDetails = $country->getCountryDetails($categoryDetails['country']);
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

        $db->query($query);
        $rows = $db->fetch_all_array();
        $workOrderList = $this->groupComponents($rows);
        $this->createXML($this->dateBegin, $this->dateEnd, $fileName, $workOrderList, $orgInfo);
    }

    private function createXML($dateBegin, $dateEnd, $fileName, $workOrderList, $orgInfo)
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
                $doc->createTextNode("Costing Report")
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
        //initialze total costs
        $totalLaborCost = 0;
        $totalPaintCost = 0;
        $totalMaterialCost = 0;
        $totalTotalCost = 0;
        foreach ($workOrderList as $workOrder) {
            //create work Order
            $workOrderComponent = $doc->createElement("workOrder");
            $table->appendChild($workOrderComponent);
            //create wo number attribute
            $workOrderNumberTag = $doc->createAttribute('number');
            $workOrderNumberTag->appendChild(
                    $doc->createTextNode(html_entity_decode($workOrder['number']))
            );
            $workOrderComponent->appendChild($workOrderNumberTag);

            //create wo profit attribute
            $workOrderProfitTag = $doc->createAttribute('profit');
            $workOrderProfitTag->appendChild(
                    $doc->createTextNode(html_entity_decode($workOrder['profit']))
            );
            $workOrderComponent->appendChild($workOrderProfitTag);

            //create wo overhead attribute
            $workOrderOverheadTag = $doc->createAttribute('overhead');
            $workOrderOverheadTag->appendChild(
                    $doc->createTextNode(html_entity_decode($workOrder['overhead']))
            );
            $workOrderComponent->appendChild($workOrderOverheadTag);

            //create mixes component
            $mixList = $workOrder['mixList'];
            foreach ($mixList as $mix) {
                $mixComponent = $doc->createElement("mix");
                $workOrderComponent->appendChild($mixComponent);

                //create material cost
                $mixMaterialCostTag = $doc->createAttribute('materialCost');
                $mixMaterialCostTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['material']))
                );
                $mixComponent->appendChild($mixMaterialCostTag);
                $totalMaterialCost += $mix['material'];
                //create labor cost
                $mixLaborCostTag = $doc->createAttribute('laborCost');
                $mixLaborCostTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['labor']))
                );
                $mixComponent->appendChild($mixLaborCostTag);
                $totalLaborCost+=$mix['labor'];

                //create paint cost
                $mixPaintCostTag = $doc->createAttribute('paintCost');
                $mixPaintCostTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['paint']))
                );
                $mixComponent->appendChild($mixPaintCostTag);
                $totalPaintCost+=$mix['paint'];

                //create step Number
                $mixStepTag = $doc->createAttribute('step');
                $mixStepTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['stepNumber']))
                );
                $mixComponent->appendChild($mixStepTag);

                //create total cost
                $mixTotalCostTag = $doc->createAttribute('totalCost');
                $mixTotalCostTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['total']))
                );
                $mixComponent->appendChild($mixTotalCostTag);

                //create mix description
                $mixDescriptionTag = $doc->createAttribute('mixDescription');
                $mixDescriptionTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['description']))
                );
                $mixComponent->appendChild($mixDescriptionTag);

                //create mix creation time
                $mixCreationTimeTag = $doc->createAttribute('creationTime');
                $mixCreationTimeTag->appendChild(
                        $doc->createTextNode(html_entity_decode($mix['creationTime']))
                );
                $mixComponent->appendChild($mixCreationTimeTag);
            }
            //create wo total attribute
            $workOrderTotalTag = $doc->createAttribute('total');
            $workOrderTotalTag->appendChild(
                    $doc->createTextNode(html_entity_decode($workOrder['workOrderTotal']))
            );
            $workOrderComponent->appendChild($workOrderTotalTag);
            $totalTotalCost += $workOrder['workOrderTotal'];
        }

        //create summary
        $summary = $doc->createElement("summary");
        $table->appendChild($summary);

        //create mix total labor cost
        $totalLaborCostTag = $doc->createAttribute('totalLaborCost');
        $totalLaborCostTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalLaborCost))
        );
        $summary->appendChild($totalLaborCostTag);

        //create mix total material cost
        $totalMaterialCostTag = $doc->createAttribute('totalMaterialCost');
        $totalMaterialCostTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalMaterialCost))
        );
        $summary->appendChild($totalMaterialCostTag);

        //create mix total paint cost
        $totalPaintCostTag = $doc->createAttribute('totalPaintCost');
        $totalPaintCostTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalPaintCost))
        );
        $summary->appendChild($totalPaintCostTag);

        //create mix total total cost
        $totalTatolCostTag = $doc->createAttribute('totalTotalCost');
        $totalTatolCostTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalTotalCost))
        );
        $summary->appendChild($totalTatolCostTag);

        $doc->save($fileName);
    }

    public function groupComponents($rows)
    {
        $db = VOCApp::getInstance()->getService('db');
        $workOderList = array();
        $dataChain = new TypeChain(null, 'date', $db, $facilityId, 'facility');
        $timeFormat = $dataChain->getFromTypeController('getFormat');
        if(!is_array($rows)){
            return array();
        }
        foreach ($rows as $row) {
            //total of Work Order
            $workOrderTotal = 0;
            $mixList = array();
            $query = "SELECT * FROM " . Mix::TABLE_NAME .
                    " WHERE wo_id={$db->sqltext($row['work_order_id'])}";

            $db->query($query);
            $results = $db->fetch_all_array();

            $woMixes = array();
            foreach ($results as $result) {
                $mix = new \MixOptimized($db);
                foreach ($result as $key => $value) {
                    if (property_exists($mix, $key)) {
                        $mix->$key = $value;
                    }
                }
                $woMixes[] = $mix;
            }
            // get mix step id, if we have, for correct sorting
            $mixStepsIds = array();

            foreach ($woMixes as $woMix) {
                //get mix cost
                $paint = $woMix->getMixPrice();
                //check if mix has step
                if (!is_null($woMix->step_id)) {
                    //get step
                    $step = new StepInstance($db, $woMix->step_id);
                    $stepNumber = $step->getNumber();
                    //add to step id array
                    $mixStepsIds[] = $woMix->step_id;
                    //count resource cost for step id
                    $resources = $step->getResources();
                    $spentTime = $woMix->spent_time;
                    //use timeResourceCount to check if we have more than one time resource
                    $timeResourceCount = 0;
                    $material = 0;
                    $labor = 0;
                    foreach ($resources as $resource) {
                        //get material cost
                        if ($resource->getResourceTypeId() == self::GOM) {
                            $material += $resource->getMaterialCost();
                        }
                        //get labor cost
                        if ($resource->getResourceTypeId() == self::TIME && $timeResourceCount == 0) {
                            $labor = $spentTime * $resource->getRate();
                            $timeResourceCount = 1;
                        }
                    }
                } else {
                    $stepNumber = $woMix->mix_id;
                }
                $time = $woMix->getCreationTimeInUnixType();
                $time = date($timeFormat, $time);
                $mixTotal = $paint + $labor + $material;
                $mix = array(
                    'stepNumber' => $stepNumber,
                    'material' => $material,
                    'labor' => $labor,
                    'paint' => $paint,
                    'total' => $mixTotal,
                    'description' => $woMix->description,
                    'creationTime' => $time
                );
                $mixList[$stepNumber] = $mix;
                $workOrderTotal += $mixTotal;
            }

            $mixStepsIds = implode(',', $mixStepsIds);

            //get all empty mix Steps Instance (steps in Wo with out mixes)
            $query = "SELECT * FROM " . StepInstance::TABLE_NAME . " " .
                    "WHERE process_id = {$row['id']}";
            if ($mixStepsIds != '') {
                $query.= " AND id NOT IN ({$db->sqltext($mixStepsIds)})";
            }
            $db->query($query);
            $emptyMixSteps = $db->fetch_all_array();
            foreach ($emptyMixSteps as $emptyMixStep) {
                //get resources by empty Step
                $query = "SELECT * FROM " . ResourceInstance::TABLE_NAME . " " .
                        "WHERE step_id = {$db->sqltext($emptyMixStep['id'])}";
                $db->query($query);
                $resources = $db->fetch_all_array();
                //count labor and material cost
                $material = 0;
                $labor = 0;
                foreach ($resources as $resource) {
                    $material+=$resource['material_cost'];
                    $labor+=$resource['labor_cost'];
                }
                $emptyMixStepTotal = $material + $labor;
                //get Creation time
                $creationTime = explode('-', $emptyMixStep['last_update_time']);
                $creationTime = mktime(0, 0, 0, $creationTime[1], $creationTime[2], $creationTime[0]);
                //get data format
                $creationTime = date($timeFormat, $creationTime);

                $mix = array(
                    'stepNumber' => $emptyMixStep['number'],
                    'material' => $material,
                    'labor' => $labor,
                    'paint' => 0,
                    'total' => $emptyMixStepTotal,
                    'description' => $emptyMixStep['description'],
                    'creationTime' => $creationTime
                );
                $mixList[$emptyMixStep['number']] = $mix;
                $workOrderTotal += $emptyMixStepTotal;
            }
            ksort($mixList);

            if ($row['overhead_unit_type'] == WorkOrder::PERCENTAGE) {
                $row['overhead'] = $row['overhead'] * $workOrderTotal / 100;
            }

            if ($row['profit_unit_type'] == WorkOrder::PERCENTAGE) {
                $row['profit'] = $row['overhead'] * $row['profit'] / 100;
            }
            //get new WorkOrder
            $workOrderTotal+=$row['overhead'];
            $workOrderTotal+=$row['profit'];
            $workOrder = array(
                'number' => $row['number'],
                'overhead' => $row['overhead'],
                'profit' => $row['profit'],
                'mixList' => $mixList,
                "workOrderTotal" => $workOrderTotal
            );
            $workOderList[] = $workOrder;
        }

        return $workOderList;
    }

}
?>
