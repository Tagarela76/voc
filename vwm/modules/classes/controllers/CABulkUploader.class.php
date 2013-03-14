<?php

use VWM\Import\Gom\GomUploaderMapper;
use VWM\Import\Gom\GomUploaderEntityBuilder;
use VWM\Import\Pfp\PfpUploaderMapper;
use VWM\Import\Pfp\PfpUploaderEntityBuilder;
use VWM\Import\Process\ProcessUploaderMapper;
use VWM\Import\Process\ProcessUploaderEntityBuilder;

class CABulkUploader extends Controller
{

    function CABulkUploader($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'bulkUploader';
        $this->parent_category = 'bulkUploader';
    }

    function runAction()
    {
        $this->runCommon('admin');
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }

    private function actionBrowseCategory()
    {
        $bookmark = $this->getFromRequest('bookmark');
        if (is_null($bookmark)) {
            $bookmark = 'bulkUploader';
        }
        $title = new Titles($this->smarty);
        $title->titleBulkUploaderSettings();

        $company = new Company($this->db);
        $companyList = $company->getCompanyList();
        $companyList[] = array('id' => 0, 'name' => 'no company');
        $this->smarty->assign('companyList', $companyList);
        $this->smarty->assign('currentCompany', 0);

        $this->smarty->assign('doNotShowControls', true);
        //	TODO: internal js script left there

        $jsSources = array("modules/js/checkBoxes.js",
            "modules/js/reg_country_state.js",
            "modules/js/bulkUploader.js");

        switch ($bookmark) {
            case 'bulkUploader':
                $this->smarty->assign('tpl', 'tpls/bulkUploader.tpl');
                break;
            case 'processUploader';
                $facility = new VWM\Hierarchy\Facility($this->db);
                $companyList = $company->getCompanyList();
                $this->smarty->assign('companyList', $companyList);
                $this->smarty->assign('tpl', 'tpls/processBulkUploader.tpl');
                break;
            default :
                throw new Exception('there is no such uploader');
        }
        $this->smarty->assign('bookmark', $bookmark);
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->display("tpls:index.tpl");
    }

    private function actionUpload()
    {
        $input = array(
            "maxNumber" => $this->getFromPost('maxNumber'),
            "threshold" => $this->getFromPost('threshold'),
            "update" => $this->getFromPost('update'),
            "companyID" => $this->getFromPost('companyID')
        );

        // for PFP
        if ($this->getFromPost('pfp') == 'Startpfp' && $input['size'] < 1024000) {
            $insertedPfps = 0;
            $updatedPfps = 0;
            $this->smarty->assign("isPFP", 'Startpfp');
            $input['inputFile'] = $_FILES['inputFile']['tmp_name'];
            $input['realFileName'] = basename($_FILES['inputFile']['name']);

            //check if file have correct format
            if (!in_array($_FILES['inputFile']['type'], array('text/comma-separated-values', 'text/csv'))) {
                throw new Exception('Input file should be CSV format');
            }

            //validate csv file
            $validation = new validateCSV($this->db);
            $validation->validatePFP($input);

            //	path to the uploaded file
            $tmpName = $_FILES['inputFile']['tmp_name'];
            $mapper = new PfpUploaderMapper();
            $mapper->doMapping($tmpName);

            $eb = new PfpUploaderEntityBuilder($this->db, $mapper);
            $eb->setCompanyId($input['companyId']);
            $eb->buildEntities($tmpName);

            $correctPfpsNames = $validation->getPfpCorrectsNames();
            $errorPfpsNames = $validation->getPfpErrorsNames();
            $pfps = $eb->getPfps();

            $actionLog = "--------------------------------\n";
            $actionLog .= "(" . date("m.d.Y H:i:s") . ") Starting uploading of " . $input['realFileName'] . "...\n";
            $i = 0;
            
            foreach ($pfps as $pfp) {
                
                $productErrors = false;
                if (in_array($pfp->getDescription(), $correctPfpsNames)) {
                     //check ratio errors
                    $products = $pfp->getProducts();
                    if(empty($products)){
                      $actionLog .= " Pfp " . $product->getName() . " has ratio less than 1 \n"; 
                      $productErrors = true;
                    }
                    
                    foreach($products as $product){
                        if($product->getRatio()<=0){
                          $actionLog .= " Product " . $pfp->getDescription() . " is empty \n";
                          $productErrors = true;
                        }
                    }
                    
                    if (!$productErrors) {
                        //check % in form
                        $i=0;
                        $productsCount = count($products);
                        while ($i != ($productsCount - 1)) {
                            if ($products[$i]->getUnitType() == '%') {
                                $products[$i] = $this->convertRatioToPercent($products[$i], $products[0]->getRatio());
                                $products[$i]->setUnitType('VOL');
                            }
                            $i++;
                        }
                        //$products = $this->convertFromCumulativeQty($products);
                        
                        if (count($products) == 1) {
                            // RDU or RTS
                            //	keep ratio as 1
                        } else {
                            $products = $this->calcRatioVolume($products);
                        }
                        
                        $pfp->setProducts($products);
                        if ($pfp->getId()) {
                            $updatedPfps++;
                            
                        } else {
                            $insertedPfps++;
                        }
                        
                        $pfpId = $pfp->save();
                        //save products
                        $products = $pfp->getProducts();
                        $pfp->deleteProducts();
                        foreach ($products as $product) {
                            $product->setPreformulatedProductsId($pfpId);
                            $product->save();
                        }
                    }else{
                        $errorCnt++;
                        $correctCnt--;
                    }
                }
            }
            $errorCnt = count($errorPfpsNames);
            $correctCnt = count($correctPfpsNames);
            $total = $errorCnt + $correctCnt;
            $percent = round($errorCnt * 100 / ($correctCnt + $errorCnt), 2);

            $errorLog = $validation->errorComments;
            $errorLog .= "	Percent of errors is " . $percent . "%. Threshold is " . $input['threshold'] . "%.\n";

            $validationLogFile = fopen(DIR_PATH_LOGS . "validation.log", "a");
            fwrite($validationLogFile, $errorLog);
            fclose($validationLogFile);
            
            //action log
            $actionLog .= "--------------------------------\n";
            $actionLog .= "(" . date("m.d.Y H:i:s") . ") Uploading of " . $input['realFileName'] . " is successfuly finished.\n";
            $actionLog .= "	Number of inserted pfps is " . $insertedPfps . "\n";
            $actionLog .= "	Number of updated pfps is " .  $updatedPfps . "\n";
            $actionLogFile = fopen(DIR_PATH_LOGS . "actions.log", "a");
            fwrite($actionLogFile, $actionLog);
            fclose($actionLogFile);
            $actions = str_replace("\n", "<br>", $actionLog);
            $actions = str_replace("	", "&nbsp;&nbsp;", $this->actions);

            $title = new Titles($this->smarty);
            $title->titleBulkUploadResults();

            
            $this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
            $this->smarty->assign("productsError", $validation->productsError);
            $this->smarty->assign("errorCnt", $errorCnt);
            $this->smarty->assign("correctCnt", $correctCnt);
            $this->smarty->assign("total", $total);
            $this->smarty->assign("input", $input);
            $this->smarty->assign("insertedCnt", $insertedPfps);
            $this->smarty->assign("updatedCnt", $updatedPfps);
            $this->smarty->assign("validationResult", $validation->validationResult);
            $this->smarty->assign("actions", $actions);
            $this->smarty->assign("parent", $this->parent_category);

            $jsSources = array("modules/js/checkBoxes.js",
                "modules/js/reg_country_state.js");

            $this->smarty->assign('jsSources', $jsSources);
            $this->smarty->assign('tpl', "tpls/uploadResults.tpl");
            $this->smarty->display("tpls:index.tpl");
        } else if ($this->getFromPost('gom') == 'StartGOM' && $input['size'] < 1024000) {
            // for GOM library
            $input['inputFile'] = $_FILES['inputFile']['tmp_name'];
            $input['realFileName'] = basename($_FILES['inputFile']['name']);
            $validation = new validateCSV($this->db);
            $validation->validateGOM($input);

            $bu = new bulkUploader4GOM($this->db, $input, $validation);

            $errorCnt = count($validation->productsError);
            $correctCnt = count($validation->productsCorrect);
            $total = $errorCnt + $correctCnt;
            $percent = round($errorCnt * 100 / ($correctCnt + $errorCnt), 2);
            //
            $errorLog = $validation->errorComments;
            $errorLog .= "	Percent of errors is " . $percent . "%. Threshold is " . $input['threshold'] . "%.\n";

            $validationLogFile = fopen(DIR_PATH_LOGS . "validation.log", "a");
            fwrite($validationLogFile, $errorLog);
            fclose($validationLogFile);

            $title = new Titles($this->smarty);
            $title->titleBulkUploadResults();

            $this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
            $this->smarty->assign("productsError", $validation->productsError);
            $this->smarty->assign("errorCnt", $errorCnt);
            $this->smarty->assign("correctCnt", $correctCnt);
            $this->smarty->assign("total", $total);
            $this->smarty->assign("input", $input);
            $this->smarty->assign("insertedCnt", $bu->insertedCnt);
            $this->smarty->assign("updatedCnt", $bu->updatedCnt);
            $this->smarty->assign("validationResult", $validation->validationResult);
            $this->smarty->assign("actions", $bu->actions);
            $this->smarty->assign("parent", $this->parent_category);

            $jsSources = array("modules/js/checkBoxes.js",
                "modules/js/reg_country_state.js");

            $this->smarty->assign('jsSources', $jsSources);
            $this->smarty->assign('tpl', "tpls/uploadResults.tpl");
            $this->smarty->display("tpls:index.tpl");
        } else {
            //for PRODUCT
            //we should check input file!
            if ($input['size'] < 1024000) {
                $input['inputFile'] = $_FILES['inputFile']['tmp_name'];
                $input['realFileName'] = basename($_FILES['inputFile']['name']);
                $bu = new bulkUploader($this->db, $input);

                $errorCnt = count($bu->productsError);
                $correctCnt = count($bu->productsCorrect);
                $total = $errorCnt + $correctCnt;

                $title = new Titles($this->smarty);
                $title->titleBulkUploadResults();

                $this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
                $this->smarty->assign("productsError", $bu->productsError);
                $this->smarty->assign("errorCnt", $errorCnt);
                $this->smarty->assign("correctCnt", $correctCnt);
                $this->smarty->assign("total", $total);
                $this->smarty->assign("input", $input);
                $this->smarty->assign("insertedCnt", $bu->insertedCnt);
                $this->smarty->assign("updatedCnt", $bu->updatedCnt);
                $this->smarty->assign("validationResult", $bu->validationResult);
                $this->smarty->assign("actions", $bu->actions);
                $this->smarty->assign("parent", $this->parent_category);

                //$smarty->display('tpls:bulkUploader.tpl');
                $jsSources = array("modules/js/checkBoxes.js",
                    "modules/js/reg_country_state.js");

                $this->smarty->assign('jsSources', $jsSources);
                $this->smarty->assign('tpl', "tpls/uploadResults.tpl");
                $this->smarty->display("tpls:index.tpl");
            }
        }
    }

    protected function actionBrowseCategoryGomWithBins()
    {

        //	form submitted
        if ($this->getFromPost() && $_FILES) {
            //	path to the uploaded file
            $tmpName = $_FILES['inputFile']['tmp_name'];

            //	real file name
            $realFileName = basename($_FILES['inputFile']['name']);

            $mapper = new GomUploaderMapper();
            $mapper->doMapping($tmpName);

            $eb = new GomUploaderEntityBuilder($this->db, $mapper);
            $eb->buildEntities($tmpName);

            $goms = $eb->getGoms();
            $cribs = $eb->getCribs();
            $bins = $eb->getBins();

            ////....
        }

        $title = new Titles($this->smarty);
        $title->titleBulkUploaderSettings();

        $this->smarty->assign('uploaderName', VOCApp::get_instance()->t('general', 'GOM with Bins'));

        $this->smarty->assign('doNotShowControls', true);
        $this->smarty->assign('tpl', 'tpls/bulkUploaderNew.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    // GET RATIO FOR PRODUCTS IN PFP
    private function rate($ar)
    {

        if (count($ar) > 1) {
            $first = array_shift($ar);

            $result[0] = $first * 1000;
            foreach ($ar as $num) {
                $tmp = ($num ) * 1000;

                $result[] = round($tmp, 2);
            }
            $nod = $this->gcd_array($result);
            foreach ($result as $res) {
                $goodArr[] = $res / $nod;
            }

            return $goodArr;
        }
    }

    private function gcd($a, $b)
    {
        if ($a == 0 || $b == 0)
            return abs(max(abs($a), abs($b)));

        $r = $a % $b;
        return ($r != 0) ?
                $this->gcd($b, $r) :
                abs($b);
    }

    private function gcd_array($array, $a = 0)
    {
        $b = array_pop($array);
        return ($b === null) ?
                (int) $a :
                $this->gcd_array($array, $this->gcd($a, $b));
    }

    private function calcRatioVolume($products)
    {
        //	save ratio of base product
        $firstNum = $products[0]->getRatio();
        
        $quan = array($firstNum);

        for ($i = 1; $i < count($products); $i++) {

            if ($products[$i]->getRatio() != null) { // no ratio but product quantity exist
                $reverse = strrev($products[$i]->getRatio());

                if ($reverse[0] == "%") {

                    $ranges = array();
                    $ratioRangeTo = 0;

                    if (bulkUploader4PFP::isRangeRatio($products[$i]->getRatio())) {
                        $ranges = bulkUploader4PFP::splitRangeRatio($products[$i]->getRatio());
                        $number = $ranges[0];
                    } else {
                        $number = substr($products[$i]->getRatio(), 0, -1);
                        $ranges = array($number);
                    }

                    //$number = substr($products[$i][$ratioFieldIndex], 0, -1);
                    $number = $firstNum * $number / 100;
                    $products[$i]->setRatio($number);

                    if (isset($ranges[1])) {
                        $ratioRangeTo = $firstNum * $ranges[1] / 100;
                        $products[$i]->setRatio($number);
                        $products[$i]->setRatioTo($ratioRangeTo);

                        $products[$i]->setRatioFromOriginal($ranges[0]);
                        $products[$i]->setRatioToOriginal($ranges[1]);
                    }
                }
                $quan[] = $products[$i]->getRatio();
            }
        }

        if ($quan) {
            $lcm = $this->rate($quan); //make ratio

            for ($i = 0; $i < count($products); $i++) {

                if (!is_null($products[$i]->getRatioTo())) {
                    $products[$i]->setRatio($lcm[$i]);
                    /*
                     * We need to calculate ratio for TO range value
                     * according to this
                     * $lcm[$i] <------> $products[$i][$ratioFieldIndex]
                     * X		<------> $products[$i]['ratioRangeTo']
                     *
                     * X = ($lcm[$i] * $products[$i]['ratioRangeTo']) / $products[$i][$ratioFieldIndex]
                     */
                    $products[$i]->setRatioTo(($lcm[$i] * $products[$i]->getRatioTo()) / $products[$i]->getRatio());
                }
                $products[$i]->setRatio($lcm[$i]);
            }
        }
        return $products;
    }
    
    private function convertOzRatioToVolume($product)
    {

        $unitTypeConverter = new UnitTypeConverter();

        $productObj = new Product($this->db);
        $productID = $productObj->getProductIdByName($product[bulkUploader4PFP::PRODUCTNR_INDEX]);
        if (!$productID) {
            throw new Exception('This is no product in database' . $product[bulkUploader4PFP::PRODUCTNR_INDEX]);
        }
        $productObj->initializeByID($productID);

        $density = new Density($this->db, $productObj->getDensityUnitID());
        if (!$density->getNumerator()) {
            throw new Exception("Failed to load Density with id " . $productObj->getDensityUnitID());
        }
        $densityType = array(
            'numerator' => $density->getNumerator(),
            'denominator' => $density->getDenominator()
        );

        $volumeQty = $unitTypeConverter->convertToDefault($product[bulkUploader4PFP::PRODUCTRATIO_INDEX], 'oz', $productObj->getDensity(), $densityType);

        $product[bulkUploader4PFP::PRODUCTRATIO_INDEX] = $volumeQty;
        $product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] = 'VOL-CUMULATIVE';

        return $product;
    }

    private function convertRatioToVolume($product)
    {
        $unitTypeConverter = new UnitTypeConverter();
        $productObj = new Product($this->db);
        $productID = $productObj->getProductIdByName($product[bulkUploader4PFP::PRODUCTNR_INDEX]);
        if (!$productID) {
            throw new Exception('This is no product in database' . $product[bulkUploader4PFP::PRODUCTNR_INDEX]);
        }
        $productObj->initializeByID($productID);
        $density = new Density($this->db, $productObj->getDensityUnitID());

        if (!$density->getNumerator()) {
            throw new Exception("Failed to load Density with id " . $productObj->getDensityUnitID());
        }

        $densityType = array(
            'numerator' => $density->getNumerator(),
            'denominator' => $density->getDenominator()
        );

        if ($product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] == "GRAMS") {
            $volumeQty = $unitTypeConverter->convertToDefault($product[bulkUploader4PFP::PRODUCTRATIO_INDEX], 'gram', $productObj->getDensity(), $densityType);
        } else {
            $volumeQty = $unitTypeConverter->convertToDefault($product[bulkUploader4PFP::PRODUCTRATIO_INDEX], 'oz', $productObj->getDensity(), $densityType);
        }
        $product[bulkUploader4PFP::PRODUCTRATIO_INDEX] = $volumeQty;
        //$product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] = 'VOL-CUMULATIVE';
        $product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] = 'VOL';

        return $product;
    }

    private function convertFromCumulativeQty($products)
    {
        $productsCount = count($products);
        $i = 0;
        while ($i != ($productsCount - 1)) {
        //check % in form
            if($products[$i]->getUnitType()=='%'){
                $products[$i] = $this->convertRatioToPercent($products[$i], $products[0]->getRatio());
            }
            $ratio = $products[$i]->getRatio() - $products[$i + 1]->getRatio();
            $products[$i]->setRatio($ratio);
            $i++;
           $products[$i]->setUnitType('VOL');
        }
        return $products;
    }
    
    /**
     * 
     * getting percent from value
     * 
     * @param int $percent
     * @param int $value
     * 
     * @return \VWM\Apps\WorkOrder\Entity\PfpProduct
     */
    
    private function convertRatioToPercent($pfpProduct, $value){
        $percent = $pfpProduct->getRatio();
        $value = $percent*$value/100;
        $pfpProduct->setRatio($value);
        $pfpProduct->setUnitType('VOL');
        return $pfpProduct;
    }

   
    protected function actionBrowseCategoryProcessNew()
    {
        $input = array(
            "maxNumber" => $this->getFromPost('facilityID'),
        );

        //	form submitted
        if ($_FILES['inputFile']['error'] != 0) {
            throw new Exception('error of file number ' . $_FILES['inputFile']['error']);
        }

        if (!in_array($_FILES['inputFile']['type'], array('text/comma-separated-values', 'text/csv'))) {
            throw new Exception('Input file should be CSV format');
        }
        $facilityID = $this->getFromPost('facilityID');

        //Validate CSV file
        $input['inputFile'] = $_FILES['inputFile']['tmp_name'];
        $input['realFileName'] = basename($_FILES['inputFile']['name']);


        $validation = new validateCSV($this->db);
        $validation->validateProcess($input); // array from csv


        $errorCnt = count($validation->getProcessError());
        $correctCnt = count($validation->getProcessCorrect());
        $total = $errorCnt + $correctCnt;
        $percent = round($errorCnt * 100 / ($correctCnt + $errorCnt), 2);
        //
        $processesErrorsNames = implode(',', $validation->getProcessError());

        $errorLog = $validation->errorComments;
        $errorLog .= "	Percent of errors is " . $percent . "\n";

        $errorLog .= "Errors in processes: " . $processesErrorsNames;


        $validationLogFile = fopen(DIR_PATH_LOGS . "validation.log", "a");
        fwrite($validationLogFile, $errorLog);
        fclose($validationLogFile);

        $processesErrorsNames = explode(',', $processesErrorsNames);

        //SAVE new Processes
        //	path to the uploaded file
        $tmpName = $_FILES['inputFile']['tmp_name'];

        $mapper = new ProcessUploaderMapper();
        $mapper->doMapping($tmpName);

        $eb = new ProcessUploaderEntityBuilder($this->db, $mapper);
        $eb->buildEntities($tmpName);
        $processes = $eb->getProcesses();
        $processAction = array(
            "savedProcesses" => array(),
            "notSavedProcess" => array(),
            "updateProcess" => array()
        );

        foreach ($processes as $process) {
            $processName = $process->getName();

            //check process for errors
            if (in_array($processName, $processesErrorsNames)) {
                $processAction['notSavedProcess'][] = $processName;
                continue;
            }

            $process->setFacilityId($facilityID);
            //check is process exist
            $processId = $process->getProcessIdByNameAndFacilityId();
            if ($processId) {
                //update process
                $process->setId($processId);
                $process->deleteProcessSteps();
                $processAction['updateProcess'][] = $processName;
            } else {
                //insert process
                $processAction['savedProcesses'][] = $processName;
            }
            $processId = $process->save();
            $steps = array();
            $steps = $process->getProcessSteps();

            foreach ($steps as $step) {
                $step->setProcessId($processId);
                $stepId = $step->save();
                $resources = $step->getResources();

                foreach ($resources as $resource) {

                    $resource->setStepId($stepId);
                    $resource->save();
                }
            }
        }

        $title = new Titles($this->smarty);
        $title->titleBulkUploadResults();

        $processesErrorsNames = implode('<br />', $processesErrorsNames);

        $this->smarty->assign('processAction', $processAction);
        $this->smarty->assign('errorCnt', $errorCnt);
        $this->smarty->assign('correctCnt', $correctCnt);
        $this->smarty->assign('total', $total);
        $this->smarty->assign('processErrorNames', $processesErrorsNames);
        $this->smarty->assign('errorComents', nl2br($validation->errorComments));
        $this->smarty->assign('tpl', "tpls/uploadProcessResults.tpl");
        $this->smarty->display("tpls:index.tpl");
    }
}
?>