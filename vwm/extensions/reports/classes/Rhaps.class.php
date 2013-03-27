<?php

use VWM\Hierarchy\Department;
use VWM\Hierarchy\Facility;
use \VWM\Hierarchy\Company;

class Rhaps extends ReportCreator implements iReportCreator
{

    private $dateBegin;
    private $dateEnd;
    private $dateFormat;

    function __construct($db, ReportRequest $reportRequest)
    {
        $this->db = $db;
        $this->categoryType = $reportRequest->getCategoryType();
        $this->categoryID = $reportRequest->getCategoryID();
        $this->dateBegin = $reportRequest->getDateBegin();
        $this->dateEnd = $reportRequest->getDateEnd();
        $this->dateFormat = $reportRequest->getDateFormat();
    }

    public function buildXML($fileName)
    {
        $dateBeginObj = DateTime::createFromFormat($this->dateFormat, $this->dateBegin);
        $dateEndObj = DateTime::createFromFormat($this->dateFormat, $this->dateEnd);

        switch ($this->categoryType) {
            case "company":
                $query = "SELECT p.product_nr, c.cas, c.description, " .
                        "p.density, cg.weight_from, p.product_id, p.specific_gravity " .
                        "FROM " . TB_PRODUCT . " p " .
                        "LEFT JOIN " . TB_MIXGROUP . " mg " .
                        "ON p.product_id=mg.product_id " .
                        "LEFT JOIN " . TB_USAGE . " m " .
                        "ON m.mix_id=mg.mix_id " .
                        "LEFT JOIN " . TB_COMPONENTGROUP . " cg " .
                        "ON p.product_id = cg.product_id " .
                        "LEFT JOIN " . TB_COMPONENT . " c " .
                        "ON cg.component_id = c.component_id " .
                        "LEFT JOIN " . TB_COMPONENT . " d " .
                        "ON d.department_id = m.department_id " .
                        "LEFT JOIN " . TB_FACILITY . " f " .
                        "ON d.facility_id = f.facility_id " .
                        "WHERE m.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND m.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND f.company_id = " . $this->categoryID . " " .
                        "AND c.HAPs = 1 " .
                        "GROUP BY p.product_id,  c.component_id";

                $company = new Company($this->db, $this->categoryID);
                $orgInfo = array(
                    'details' => $company,
                    'category' => "Company",
                    'name' => $company->getName(),
                    'notes' => ""
                );
                break;

            case "facility":

                $query = "SELECT p.product_nr, c.cas, c.description, " .
                        "p.density, cg.weight_from, p.product_id, p.specific_gravity " .
                        "FROM " . TB_PRODUCT . " p " .
                        "LEFT JOIN " . TB_MIXGROUP . " mg " .
                        "ON p.product_id=mg.product_id " .
                        "LEFT JOIN " . TB_USAGE . " m " .
                        "ON m.mix_id=mg.mix_id " .
                        "LEFT JOIN " . TB_COMPONENTGROUP . " cg " .
                        "ON p.product_id = cg.product_id " .
                        "LEFT JOIN " . TB_COMPONENT . " c " .
                        "ON cg.component_id = c.component_id " .
                        "LEFT JOIN " . TB_COMPONENT . " d " .
                        "ON d.department_id = m.department_id " .
                        "WHERE m.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND m.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND d.facility_id = " . $this->categoryID . " " .
                        "AND c.HAPs = 1 " .
                        "GROUP BY p.product_id,  c.component_id";

                $facility = new Facility($this->db, $this->categoryID);

                $orgInfo = array(
                    'details' => $facility,
                    'category' => "Facility",
                    'name' => $facility->getName(),
                    'notes' => ""
                );
                break;

            case "department":
                //get information wich we need
                $query = "SELECT p.product_nr, c.cas, c.description, " .
                        "p.density, cg.weight_from, p.product_id, p.specific_gravity " .
                        "FROM " . TB_PRODUCT . " p " .
                        "LEFT JOIN " . TB_MIXGROUP . " mg " .
                        "ON p.product_id=mg.product_id " .
                        "LEFT JOIN " . TB_USAGE . " m " .
                        "ON m.mix_id=mg.mix_id " .
                        "LEFT JOIN " . TB_COMPONENTGROUP . " cg " .
                        "ON p.product_id = cg.product_id " .
                        "LEFT JOIN " . TB_COMPONENT . " c " .
                        "ON cg.component_id = c.component_id " .
                        "WHERE m.creation_time >= " . $dateBeginObj->getTimestamp() . " " .
                        "AND m.creation_time <= " . $dateEndObj->getTimestamp() . " " .
                        "AND m.department_id = " . $this->categoryID . " " .
                        "AND c.HAPs = 1 " .
                        "GROUP BY p.product_id,  c.component_id";

                $department = new Department($this->db, $this->categoryID);
                $facilityIDS = array();
                $facility = new Facility($this->db, $department->getFacilityId());

                $orgInfo = array(
                    'details' => $facility,
                    'category' => "Department",
                    'name' => $department->getName(),
                    'notes' => ""
                );

                break;
        }
        $this->db->query($query);
        if ($this->db->num_rows()) {
            $rows = $this->db->fetch_all();
        }

        $in = $this->group($rows);
        $totalUsages = $this->groupComponents($rows);
        $this->createXML($in, $orgInfo, $this->dateBegin, $this->dateEnd, $fileName, $totalUsages);
    }

    private function createXML($results, $orgInfo, $dateBegin, $dateEnd, $fileName, $totalUsages)
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
                $doc->createTextNode("SWEEP Report \nSustainable Waste, Emissions and Energy Performance")
        );
        $page->appendChild($title);

        //crteate period tag
        $periodTag = $doc->createElement("period");
        $periodTag->appendChild(
                $doc->createTextNode("From " . $this->dateBegin . " To " . $this->dateEnd)
        );
        $page->appendChild($periodTag);

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

        //set address
        $adressTag = $doc->createElement("address");
        $adressTag->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo['details']->getAddress()))
        );
        $page->appendChild($adressTag);

        //set city zip
        $cityStateZipTag = $doc->createElement("cityStateZip");
        $cityStateZipTag->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo['details']->getCity() . ", " . $orgInfo['details']->getState() .
                                ", " . $orgInfo['details']->getZip()))
        );
        $page->appendChild($cityStateZipTag);

        //set country
        $countyTag = $doc->createElement("county");
        $countyTag->appendChild(
                $doc->createTextNode($orgInfo['details']->getCountry())
        );
        $page->appendChild($countyTag);

        //set phone
        $phoneTag = $doc->createElement("phone");
        $phoneTag->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo['details']->getPhone()))
        );
        $page->appendChild($phoneTag);

        //set fax
        $faxTag = $doc->createElement("fax");
        $faxTag->appendChild(
                $doc->createTextNode(html_entity_decode($orgInfo['details']->getFax()))
        );
        $page->appendChild($faxTag);


        if ($orgInfo['category'] == 'Company') {
            //set company id
            $companyIdTag = $doc->createElement("facilityId");
            $companyIdTag->appendChild(
                    $doc->createTextNode(html_entity_decode($orgInfo['details']->getCompanyId()))
            );
            $page->appendChild($companyIdTag);
        } else {
            //set facility id
            $facilityIdTag = $doc->createElement("facilityId");
            $facilityIdTag->appendChild(
                    $doc->createTextNode(html_entity_decode($orgInfo['details']->getFacilityId()))
            );
            $page->appendChild($facilityIdTag);
        }
        //create table
        $table = $doc->createElement("table");
        $page->appendChild($table);

        foreach ($results as $key => $value) {
            //create first product
            $product = $doc->createElement("productGroup");

            $equipmentNameTag = $doc->createAttribute("name");
            $equipmentNameTag->appendChild(
                    $doc->createTextNode(html_entity_decode($key))
            );
            $product->appendChild($equipmentNameTag);
            $table->appendChild($product);

            //get components
            foreach ($value as $component) {
                //create product component tag
                $productComponent = $doc->createElement('component');

                //create attribute cas Number
                $casNumberTag = $doc->createAttribute('casNumber');
                $casNumberTag->appendChild(
                        $doc->createTextNode(html_entity_decode($component->cas))
                );
                $productComponent->appendChild($casNumberTag);

                //create component description tag
                $componentDescriptionTag = $doc->createAttribute('description');
                $componentDescriptionTag->appendChild(
                        $doc->createTextNode(html_entity_decode($component->description))
                );
                $productComponent->appendChild($componentDescriptionTag);

                //create component weight
                //2. Density of the product in pounds per gallon. 
                //If density is not provided, multiply the specific gravity by 8.34 pounds per gallon;
                if ($component->density == '' || $component->density == 0 || is_null($component->density)) {
                    $density = $component->specific_gravity * 8.34;
                } else {
                    $density = $component->density;
                }

                $componentWeightTag = $doc->createAttribute('weight');
                $componentWeightTag->appendChild(
                        $doc->createTextNode(html_entity_decode($density))
                );
                $productComponent->appendChild($componentWeightTag);

                //create component amount
                if ($component->weight_from == '') {
                    $productAmount = 0;
                } else {
                    $productAmount = $component->weight_from;
                }

                $componentAmountTag = $doc->createAttribute('amount');
                $componentAmountTag->appendChild(
                        $doc->createTextNode(html_entity_decode($productAmount))
                );
                $productComponent->appendChild($componentAmountTag);

                //create component amount
                $componentEmissionTag = $doc->createAttribute('emissions');
                $componentEmissionTag->appendChild(
                        $doc->createTextNode(html_entity_decode($productAmount * $density))
                );
                $productComponent->appendChild($componentEmissionTag);

                //add component to product
                $product->appendChild($productComponent);
            }
        }
        //set  total usage
        //create usage summary
        $usageSummary = $doc->createElement("usageSummary");
        $page->appendChild($usageSummary);
        $totalSummaryAmount = 0;
        $totalSummaryEmissions = 0;

        foreach ($totalUsages as $totalUsage) {
            //create product summary usage
            $summaryProduct = $doc->createElement("summaryComponent");

            //create casNumber attribute
            $summaryNameTag = $doc->createAttribute("name");
            $summaryNameTag->appendChild(
                    $doc->createTextNode(html_entity_decode($totalUsage['casNumber']))
            );
            $summaryProduct->appendChild($summaryNameTag);

            //create description attribute
            $componentSummaryDescriptionTag = $doc->createAttribute("componentDescription");
            $componentSummaryDescriptionTag->appendChild(
                    $doc->createTextNode(html_entity_decode($totalUsage['description']))
            );
            $summaryProduct->appendChild($componentSummaryDescriptionTag);

            //create % usage attribute
            $componentSummaryUsageTag = $doc->createAttribute("componentSummaryUsage");
            $componentSummaryUsageTag->appendChild(
                    $doc->createTextNode(html_entity_decode($totalUsage['usage']))
            );
            $summaryProduct->appendChild($componentSummaryUsageTag);

            //create summary amount attribute
            $componentSummaryAmountTag = $doc->createAttribute("componentSummaryAmount");
            $componentSummaryAmountTag->appendChild(
                    $doc->createTextNode(html_entity_decode($totalUsage['amount']))
            );
            $summaryProduct->appendChild($componentSummaryAmountTag);
            $totalSummaryAmount += $totalUsage['amount'];

            //create summary total emission attribute
            $componentSummaryTotalEmissionTag = $doc->createAttribute("componentSummaryTotalEmission");
            $componentSummaryTotalEmissionTag->appendChild(
                    $doc->createTextNode(html_entity_decode($totalUsage['totalEmission']))
            );
            $summaryProduct->appendChild($componentSummaryTotalEmissionTag);
            $totalSummaryEmissions += $totalUsage['totalEmission'];

            $usageSummary->appendChild($summaryProduct);
        }

        //create elemet total Usage
        $totalSummaryUsage = $doc->createElement("totalSummaryUsage");
        $page->appendChild($totalSummaryUsage);

        //set period
        $period = $this->dateBegin . " through " . $this->dateEnd;

        $totalSummaryUsagePeriodTag = $doc->createAttribute('totalSummaryUsagePeriod');
        $totalSummaryUsagePeriodTag->appendChild(
                $doc->createTextNode(html_entity_decode($period))
        );
        $totalSummaryUsage->appendChild($totalSummaryUsagePeriodTag);
        //set total amount
        $totalSummaryAmountTag = $doc->createAttribute('totalSummaryAmount');
        $totalSummaryAmountTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalSummaryAmount))
        );
        $totalSummaryUsage->appendChild($totalSummaryAmountTag);

        //set total emission
        $totalSummaryEmissionsTag = $doc->createAttribute('totalSummaryEmissions');
        $totalSummaryEmissionsTag->appendChild(
                $doc->createTextNode(html_entity_decode($totalSummaryEmissions))
        );
        $totalSummaryUsage->appendChild($totalSummaryEmissionsTag);

        //create total Usage 
        $doc->save($fileName);
    }

    private function group($rows)
    {
        $results = array();
        //get array of product_nr 
        $prNumbers = array();
        foreach ($rows as $row) {
            if (!in_array($row->product_nr, $prNumber)) {
                $prNumbers [] = $row->product_nr;
            }
        }
        //group elements by product number
        foreach ($prNumbers as $prNumber) {
            $product = array();
            foreach ($rows as $row) {
                if ($row->product_nr == $prNumber) {
                    $product[] = $row;
                }
            }
            $results[$prNumber] = $product;
        }
        return $results;
    }

    public function groupComponents($rows)
    {
        $componentsUsage = array();
        foreach ($rows as $row) {
            if (!in_array($row->cas, $componentUsage)) {
                $component = array(
                    'amount' => 0,
                    'description' => $row->description,
                    'casNumber' => $row->cas,
                    'totalEmission' => 0,
                    'usage' => 0
                );
                $componentUsages[$row->cas] = $component;
            }
        }

        $totalHabs = array();
        $summaryAmount = 0;
        foreach ($componentUsages as $key => $value) {
            foreach ($rows as $row) {
                if ($key == $row->cas) {
                    $value['amount']+=$row->weight_from;
                    $summaryAmount+=$row->weight_from;
                }
            }
            $value['totalEmission'] = $value['amount'] * $row->density;
            $totalHabs[] = $value;
        }
        //calculate total usage
        for ($i = 0; $i < count($totalHabs); $i++) {
            $totalHabs[$i]['usage'] = round($totalHabs[$i]['amount'] * 100 / $summaryAmount);
        }
        return $totalHabs;
    }

}
?>
