<?php

use VWM\Apps\WorkOrder\Entity\Pfp;

class validateCSV
{

    /**
     * @var db
     */
    private $db;
    public $productsError;
    public $productsCorrect;
    public $errorComments;
    protected $processError = array();
    protected $processCorrect = array();
    
    protected $pfpErrorsNames = array();
    protected $pfpCorrectsNames = array();
    

    public function getProcessError()
    {
        return $this->processError;
    }

    public function setProcessError($processError)
    {
        $this->processError[] = $processError;
    }

    public function getProcessCorrect()
    {
        return $this->processCorrect;
    }

    public function setProcessCorrect($processCorrect)
    {
        $this->processCorrect[] = $processCorrect;
    }
    
    public function getProductsCorrect()
    {
        return $this->productsCorrect;
    }

    public function setProductsCorrect($productsCorrect)
    {
        $this->productsCorrect = $productsCorrect;
    }

    public function getProductsError()
    {
        return $this->productsError;
    }

    public function setProductsError($productsError)
    {
        $this->productsError = $productsError;
    }
    
    public function getPfpErrorsNames()
    {
        return $this->pfpErrorsNames;
    }

    public function setPfpErrorsNames($pfpErrorsNames)
    {
        $this->pfpErrorsNames = $pfpErrorsNames;
    }

    public function getPfpCorrectsNames()
    {
        return $this->pfpCorrectsNames;
    }

    public function setPfpCorrectsNames($pfpCorrectsName)
    {
        $this->pfpCorrectsNames = $pfpCorrectsName;
    }

    const TB_UNIT_TYPE = 'unittype';
    const UNIT_TYPE = 6;
    const RATE_UNIT_TYPE = 10;
    const QTY = 8;
    const RATE = 7;

    function validateCSV($db)
    {
        $this->db = $db;

        $this->productsError = array();
        $this->productsCorrect = array();
        $this->errorComments = "";
    }

    // PFP UPLOAD

    public function validatePFP($input)
    {
        $pfpErrorsNames = array();
        $pfpCorrectsNames = array();
        $pfpErrorsProducts = array();
        
        $CSVPath = $input['inputFile'];
        //last row
        $file = fopen($CSVPath, "a");

        fwrite($file, ";;;;;;;;;;;;;;;;;;;;;;;;\n");
        fclose($file);

        $file = fopen($CSVPath, "r");
        //create error log
        $error = "";
        $this->errorComments = "--------------------------------\n";
        $this->errorComments .= "(" . date("m.d.Y H:i:s") . ") Starting validation of " . $input['realFileName'] . "...\n";

        $currentRow = 0;
        $headerEndsRow = 3;
        //	here we'll store rows for single pfp
        
        $currentPfpName = '';
        $isErrorInCurrentPfp = false;
        
        $i = 0;
        while ($dat = fgetcsv($file, 1000, ";")) {
            $i++;

            $currentRow++;
            if ($currentRow < $headerEndsRow) {
                //	skip first $headerEndsRow rows
                continue;
            }
            // get pfp Details if exist
            if (!empty($dat[1])) {
                $currentPfpName = $dat[bulkUploader4PFP::PRODUCTNAME_INDEX];
                
                // check ip correct
                if (!bulkUploader4PFP::isProprietary($dat[bulkUploader4PFP::INTELLECTUAL_PROPRIETARY])) {
                    $this->errorComments .= "incorrect IP :  Row " . $currentRow . ".\n";
                    $isErrorInCurrentPfp = true;
                }
                if ($dat[4] == '') {
                    continue;
                }
            }

            $data = $this->trimAll($dat);

            //	pfp's are splitted by empty row
            if ($this->isEmptyRow($data)) {

                if ($currentPfpName!='') {
                    if ($isErrorInCurrentPfp) {
                        $pfpErrorsNames[] = $currentPfpName;
                    } else {
                        $pfpCorrectsNames[] = $currentPfpName;
                    }
                    //	reset
                    $currentPfpName = '';
                    $isErrorInCurrentPfp = false;
                }
                //	no sence to do the rest of code for this row
                continue;
            }

            $currRowComments = $this->pfpDataCheck($data, $currentRow);

            if (bulkUploader4PFP::isRangeRatio($data[bulkUploader4PFP::PRODUCTRATIO_INDEX])) {
                $ranges = bulkUploader4PFP::splitRangeRatio($data[bulkUploader4PFP::PRODUCTRATIO_INDEX]);
                $data['ratioRangeTo'] = $ranges[1];
            }

            if (bulkUploader4PFP::isRtuOrRtsRatio($data[bulkUploader4PFP::PRODUCTRATIO_INDEX])) {
                $data[bulkUploader4PFP::PRODUCTRATIO_INDEX] = 1;
            }
            
            
            //check product dencity
            $productObj = new \Product($this->db);
            $productId = $productObj->getProductIdByName($data[bulkUploader4PFP::PRODUCTNR_INDEX]);
            if (!$productId) {
                $currRowComments.="There is no products with description " . $dat[bulkUploader4PFP::PRODUCTNAME_INDEX] . ".\n";
            } else {
                $productObj->initializeByID($productId);
                if (is_null($productObj->getDensity())) {
                    $currRowComments.="Product with id " . $productId . " has dencity NULL.\n";
                }
            }
            if ($currRowComments != "") {
                $this->errorComments .= $currRowComments;
                $productError = array(
                    'productId'=>$data[bulkUploader4PFP::PRODUCTNR_INDEX],
                    'errorComments'=>$currRowComments
                );
                $this->productsError[] = $productError;
                $isErrorInCurrentPfp = true;
            }
        }
        $this->setPfpErrorsNames($pfpErrorsNames);
        $this->setPfpCorrectsNames($pfpCorrectsNames);
        fclose($file);
    }

    private function pfpDataCheck($data, $row)
    {
        $comments = "";

        if ($data[2]) {
            $this->db->query("SELECT product_id FROM product WHERE product_nr='" . $this->db->sqltext($data[2]) . "'");

            //product check exist
            if ($this->db->num_rows() == 0) {
                $comments .= "Product with ID : " . $data[2] . " doesn't exist. Row " . $row . ".\n";
                //$this->productsError[]['errorComments'] = "Product with ID value " . $data[2] . " doesn't exist. Row " . $row . ".\n";
            }

            //	check for percent - "10%"
            if (Validation::isPercent($data[bulkUploader4PFP::PRODUCTRATIO_INDEX], true)) {
                return $comments;
            }

            //	check for range "5-10%"
            if (bulkUploader4PFP::isRangeRatio($data[bulkUploader4PFP::PRODUCTRATIO_INDEX])) {
                return $comments;
            }

            //	check for RTU or RTS
            if (bulkUploader4PFP::isRtuOrRtsRatio($data[bulkUploader4PFP::PRODUCTRATIO_INDEX])) {
                return $comments;
            }

            //	check for cumulative "344.32"
            if (Validation::isFloat($data[bulkUploader4PFP::PRODUCTRATIO_INDEX])) {
                return $comments;
            }

            //	no, it's just a validation error
            $comments .= "Product with ID : " . $data[bulkUploader4PFP::PRODUCTRATIO_INDEX] . " has validation error at Ratio. Row " . $row . ".\n";
        }
        return $comments;
    }

    public function validateGOM($input)
    {
        $CSVPath = $input['inputFile'];
        //last row
        $file = fopen($CSVPath, "a");

        fwrite($file, ";;;;;;;;;;;;;\n");
        fclose($file);

        $file = fopen($CSVPath, "r");

        $headerKey = $this->tableHeader4GOM($file); //identification columns by their header

        $error = "";
        $this->errorComments = "--------------------------------\n";
        $this->errorComments .= "(" . date("m.d.Y H:i:s") . ") Starting validation of " . $input['realFileName'] . "...\n";

        $current_row = 3;
        $cJobber = new Jobber($this->db);
        while ($dat = fgetcsv($file, 1000, ";")) {
            $current_GOM_data = array();
            $data = $this->trimAll($dat);
            if (!$this->isEmptyGOMRow($data)) {
                foreach ($data as $key => $value) {
                    if (isset($headerKey[$key])) {
                        $current_GOM_data[$headerKey[$key]] = $value;
                    }
                }

                if (!empty($current_GOM_data['gom_code']) && !empty($current_GOM_data['description']) && ($cJobber->getJobberByName($current_GOM_data['jobber']) != 0)) {
                    if (empty($current_GOM_data['unit'])) {
                        $current_GOM_data['unit'] = 'EACH';
                    }
                    if (empty($current_GOM_data['quantity']) || !is_int(intval($current_GOM_data['quantity']))) {
                        $current_GOM_data['quantity'] = '1';
                    }
                    if (empty($current_GOM_data['unit_quantity']) || !is_int(intval($current_GOM_data['unit_quantity']))) {
                        $current_GOM_data['unit_quantity'] = '1';
                    }
                    if (empty($current_GOM_data['sales'])) {
                        $current_GOM_data['sales'] = '0';
                    }
                    $this->productsCorrect[] = $current_GOM_data;
                } else {
                    $this->productsError[] = $current_GOM_data;
                }
            }

            $current_row++;
        }
        fclose($file);
    }

    private function isEmptyGOMRow($row)
    {
        $count = 0;
        foreach ($row as $item) {
            if ($item == "") {
                $count++;
            }
        }

        return (count($row) == $count);
    }

    public function validate($input)
    {
        $unitTypeClass = new Unittype($this->db);

        $CSVPath = $input['inputFile'];
        //last row
        $file = fopen($CSVPath, "a");

        fwrite($file, ";;;;;;;;;;;;;;;;;;;;;;;;\n");
        fclose($file);

        $file = fopen($CSVPath, "r");

        $headerKey = $this->tableHeader($file); //identification columns by their header

        $row = 3;
        $lastNotEmptyRow = 4;
        $inProduct = false;
        $error = "";
        $this->errorComments = "--------------------------------\n";
        $this->errorComments .= "(" . date("m.d.Y H:i:s") . ") Starting validation of " . $input['realFileName'] . "...\n";
        while ($dat = fgetcsv($file, 1000, ";")) {

            $data = Array();
            foreach ($dat as $val) {
                $data[] = mysql_real_escape_string($val);
            }

            $data = $this->trimAll($data);

            $data_tmp[0] = $data[$headerKey['productID']];
            $data_tmp[1] = $data[$headerKey['mfg']];
            $data_tmp[2] = $data[$headerKey['productName']];
            $data_tmp[3] = $data[$headerKey['type']];
            $data_tmp[4] = $data[$headerKey['scoating']];
            $data_tmp[5] = $data[$headerKey['aerosol']];
            $data_tmp[6] = $data[$headerKey['substrate']];
            $data_tmp[7] = $data[$headerKey['rule']];
            $data_tmp[8] = $data[$headerKey['vocwx']];
            $data_tmp[9] = $data[$headerKey['voclx']];
            $data_tmp[10] = $data[$headerKey['case']];
            $data_tmp[11] = $data[$headerKey['description']];
            $data_tmp[12] = $data[$headerKey['mmhg']];
            $data_tmp[13] = $data[$headerKey['temp']];
            $data_tmp[14] = $data[$headerKey['weightFrom']];
            $data_tmp[15] = $data[$headerKey['weightTo']];
            $data_tmp[16] = $data[$headerKey['density']];
            $data_tmp[17] = $data[$headerKey['gavity']];
            $data_tmp[18] = $data[$headerKey['boilingRangeFrom']];
            $data_tmp[19] = $data[$headerKey['boilingRangeTo']];
            $data_tmp[20] = $data[$headerKey['class']];
            $data_tmp[21] = $data[$headerKey['irr']];
            $data_tmp[22] = $data[$headerKey['ohh']];
            $data_tmp[23] = $data[$headerKey['sens']];
            $data_tmp[24] = $data[$headerKey['oxy1']];
            $data_tmp[25] = $data[$headerKey['VOCPM']];
            $data_tmp[26] = $data[$headerKey['einecsElincs']];
            $data_tmp[27] = $data[$headerKey['substanceSymbol']];
            $data_tmp[28] = $data[$headerKey['substanceR']];
            $data_tmp[29] = $data[$headerKey['percentVolatileWeight']];
            $data_tmp[30] = $data[$headerKey['percentVolatileVolume']];
            $data_tmp[31] = $data[$headerKey['waste']];
            $data_tmp[32] = $data[$headerKey['industryType']];
            $data_tmp[33] = $data[$headerKey['industrySubType']];
            $data_tmp[34] = $data[$headerKey['paintOrChemical']];
            $data_tmp[35] = $data[$headerKey['flashPoint']];
            $data_tmp[36] = $data[$headerKey['health']];
            $data_tmp[37] = 0; // set place for discontinued product marker
            $data_tmp[38] = $data[$headerKey['productPricing']]; // product price(from manufacturer)
            $data_tmp[39] = $data[$headerKey['unitType']]; // unit type
            $data_tmp[40] = $data[$headerKey['QTY']]; // product quantity
            $data_tmp[41] = $data[$headerKey['libraryTypes']]; // product library type

            $data = $data_tmp;

            if (!empty($data[0])) {

                $componentKey = -1;

                if ($inProduct) {
                    if ($error != "") {
                        $product['errorComments'] = $error;
                        $this->productsError[] = $product;
                    } else {
                        $this->productsCorrect[] = $product;
                    }
                    $inProduct = false;
                    $error = "";
                }

                $inProduct = true;
                // check product to be discontinued
                if ($data[8] == 'DISC' || $data[8] == 'discontinued' || $data[9] == 'DISC' || $data[9] == 'discontinued') {
                    $data[8] = '0';
                    $data[9] = '0';
                    $data[37] = 1;
                }

                $currRowComments = $this->productDataCheck($data, $row);
                if ($currRowComments != "") {
                    //$error = TRUE;
                    $error .= $currRowComments;
                }
                $this->errorComments .= $currRowComments;

                if (!preg_match("/^[0-9.]*$/", $data[29]) || (substr_count($data[29], ".") > 1)) {
                    $data[29] = '';
                }
                if ($data[29] == '') {
                    $data[29] = '0';
                }

                if (!preg_match("/^[0-9.]*$/", $data[30]) || (substr_count($data[30], ".") > 1)) {
                    $data[30] = '';
                }
                if ($data[30] == '') {
                    $data[30] = '0';
                }

                if ($data[35] == '') {
                    $data[35] = '0';
                }

                // unit type clear
                $data[39] = trim($data[39]);
                // prepare unit type
                switch ($data[39]) {
                    case "QUART":
                    case "quart":
                        $unitType = "qt";
                        break;
                    case "GALLONS":
                    case "GALLON":
                    case "gallon":
                    case "gallons":
                    case "GAL":
                    case "gal":
                        $unitType = "gal";
                        break;
                    case "PINT":
                    case "pint":
                        $unitType = "pt";
                        break;
                    case "LITRE":
                    case "litre":
                        $unitType = "L";
                        break;
                    case "KG":
                    case "kg":
                        $unitType = "KG";
                        break;
                    case "ML":
                    case "ml":
                        $unitType = "ml";
                        break;
                    case "GRAMS":
                    case "grams":
                        $unitType = "GRAM";
                        break;
                    case "OUNCES":
                    case "ounces":
                    case "OZS":
                    case "ozs":
                        $unitType = "OZS";
                        break;
                    default:
                        $unitType = "LBS";
                        break;
                }
                $unitType = $unitTypeClass->getUnittypeByName($unitType);
                $unitType = $unitType['unittype_id']; // get unit type id
                //	product processing
                $product = array(
                    "productID" => $data[0],
                    "MFG" => $data[1],
                    "productName" => $data[2],
                    "coating" => $data[3],
                    "specialtyCoating" => $data[4],
                    "aerosol" => $data[5],
                    "vocwx" => $this->convertDensity($data[8]),
                    "voclx" => $this->convertDensity($data[9]),
                    "density" => $data[16],
                    "gavity" => $data[17],
                    "boilingRangeFrom" => $this->toCelsius($data[18]),
                    "boilingRangeTo" => $this->toCelsius($data[19]),
                    "hazardousClass" => $data[20],
                    "hazardousIRR" => $data[21],
                    "hazardousOHH" => $data[22],
                    "hazardousSENS" => $data[23],
                    "hazardousOXY" => $data[24],
                    "percentVolatileWeight" => $data[29],
                    "percentVolatileVolume" => $data[30],
                    "waste" => $data[31],
                    //"industryType" => $data[32],
                    //"industrySubType" => $data[33],
                    "paintOrChemical" => $data[34],
                    "flashPoint" => $this->toCelsius($data[35]),
                    "health" => $data[36],
                    "discontinued" => $data[37],
                    "productPricing" => $this->calculateProductPrice($data[38], $data[40]),
                    "unitType" => $unitType,
                    "QTY" => $data[40],
                    "libraryTypes" => $data[41]
                );
            }

            if ($inProduct) {
                if (!empty($data[32])) {
                    $industryType = array(
                        "industryType" => $data[32],
                        "industrySubType" => $data[33]
                    );
                    $product["industryType"][] = $industryType;
                    $industryTypeEnd = FALSE;
                } elseif (empty($data[33])) {
                    $industryTypeEnd = TRUE;
                }
            } else {
                $industryTypeEnd = TRUE;
            }

            //	components processing
            if (!empty($data[11]) && $inProduct) {
                if ($lastNotEmptyRow == $row - 1) {
                    $productKeys = $this->productsKeys();
                    foreach ($data as $key => $field) {
                        foreach ($productKeys as $productKey) {
                            if ($productKey == $key) {
                                if ($field != "") {
                                    $map = $this->map();
                                    $lastNotEmptyRow = $row;
                                    $brokenLine = true;
                                    if ($map[$key] == 'substanceR' || $map[$key] == 'hazardousIRR' || $map[$key] == 'waste') {
                                        $product[$map[$key]] .= "," . $data[$key];
                                    } else {
                                        $product[$map[$key]] .= " " . $data[$key];
                                    }
                                }
                            }
                        }
                    }
                }

                $lastNotEmptyRow = $row;

                $currRowComments = $this->componentDataCheck($data, $row);
                if ($currRowComments != "") {
                    $error .= $currRowComments;
                }
                $this->errorComments .= $currRowComments;

                if (!preg_match("/^[0-9.]*$/", $data[12]) || (substr_count($data[12], ".") > 1)) {
                    $data[12] = '';
                }
                if ($data[12] == '') {
                    $data[12] = '0';
                }

                if ($data[13] == '') {
                    $data[13] = '0';
                }

                $component = array(
                    "substrate" => $data[6],
                    "rule" => $data[7],
                    "caseNumber" => $data[10],
                    "description" => $data[11],
                    "mmhg" => $data[12],
                    "temp" => $this->toCelsius($data[13]),
                    "weightFrom" => $data[14],
                    "weightTo" => $data[15],
                    "vocpm" => $data[25],
                    "einecsElincs" => $data[26],
                    "substanceSymbol" => $data[27],
                    "substanceR" => $data[28]
                );

                $product["component"][] = $component;
                $componentKey++;
            } else {
                if ($industryTypeEnd) {
                    $brokenLine = false;

                    if ($lastNotEmptyRow == $row - 1) {
                        foreach ($data as $key => $field) {
                            if ($field != "") {
                                $map = $this->map();
                                $productKeys = $this->productsKeys();
                                $componentsKeys = $this->componentsKeys();
                                $lastNotEmptyRow = $row;
                                $brokenLine = true;
                                if ($map[$key] == 'substanceR' || $map[$key] == 'hazardousIRR' || $map[$key] == 'waste') {
                                    if (array_search($key, $productKeys)) {
                                        $product[$map[$key]] .= "," . $data[$key];
                                    } else {
                                        $product['component'][$componentKey][$map[$key]] .= "," . $data[$key];
                                    }
                                } else {
                                    if (array_search($key, $productKeys)) {
                                        $product[$map[$key]] .= " " . $data[$key];
                                    } else {
                                        $product['component'][$componentKey][$map[$key]] .= " " . $data[$key];
                                    }
                                }
                            }
                        }
                    }


                    if (!$brokenLine) {
                        $tmpArray = $this->componentsKeys();
                        $column = "";
                        foreach ($tmpArray as $key) {
                            if (!empty($data[$key])) {
                                if ($column != "") {
                                    $column.=", ";
                                }
                                switch ($key) {
                                    case 6:
                                        $column.="substrate";
                                        break;
                                    case 7:
                                        $column.="rule";
                                        break;
                                    case 10:
                                        $column.="caseNumber";
                                        break;
                                    case 11:
                                        $column.="description";
                                        break;
                                    case 12:
                                        $column.="mmhg";
                                        break;
                                    case 13:
                                        $column.="temp";
                                        break;
                                    case 14:
                                        $column.="weightFrom";
                                        break;
                                    case 15:
                                        $column.="weightTo";
                                        break;
                                    case 25:
                                        $column.="vocpm";
                                        break;

                                    case 26:
                                        $column.="einecsElincs";
                                        break;
                                    case 27:
                                        $column.="substanceSymbol";
                                        break;
                                    case 28:
                                        $column.="substanceR";
                                        break;
                                }
                            }
                        }

                        if ($column != "") {
                            $error .= "	Can't assign component to product: Undefined data " . $column . ". Row " . $row . ".\n";
                            $this->errorComments .= "	Can't assign component to product: Undefined data " . $column . ". Row " . $row . ".\n";
                        }

                        if ($error != "" && $inProduct) {
                            $product['errorComments'] = $error;
                            $product['closed'] = 'YES';
                            $this->productsCorrect[] = $product;
                            $this->productsError[] = $product;
                        } else {
                            if ($inProduct) {
                                $product['closed'] = 'NO';
                                $this->productsCorrect[] = $product;
                            }
                        }
                        $inProduct = false;
                        $error = "";
                    }
                }
            }
            $row++;
        }
        fclose($file);
    }

    private function toCelsius($data)
    {
        $cUnitTypeConvertor = new UnitTypeConverter();
        $data = trim($data);
        //boiling range clear
        $data = str_replace(",", ".", $data);
        //if Boiling Range is empty or N/A put 0
        if (empty($data) || $data == 'N/A')
            $data = '0';

        if (preg_match("/^\d+\.{0,1}\d*\s*[FCfc]{1}$/", $data)) {
            if (strtoupper(substr($data, strlen($data) - 1)) == "F") {
                $data = str_replace('F', '', $data);
                $data = str_replace('f', '', $data);
                $dataTemp = trim($data);
                $result = $cUnitTypeConvertor->convertFahrenheitToCelsius($dataTemp);
            } else {
                $data = str_replace('C', '', $data);
                $data = str_replace('c', '', $data);
                $result = trim($data);
            }
        } else {
            $result = trim($data);
        }
        $result = strval($result);

        return $result;
    }

    private function calculateProductPrice($price, $qty)
    {
        // preparate data
        $price = trim($price); // for one unit
        $qty = trim($qty);
        $price = str_replace(",", ".", $price);
        $price = str_replace("$", "", $price);
        // to float value
        $fullPrice = (float) $price * (float) $qty;
        $result = strval($fullPrice);

        return $result;
    }

    private function productDataCheck($data, $row)
    {
        $comments = "";
        //product id check
        if (strlen($data[0]) > 50) {
            $comments .= "	Product ID value is too long. Row " . $row . ".\n";
        }

        //supplier check
        if (strlen($data[1]) > 200) {
            $comments .= "	MFG value is too long. Row " . $row . ".\n";
        }

        //PRODUCT NAME/COLOR check
        if (strlen($data[2]) > 200) {
            $comments .= "	Product name value is too long. Row " . $row . ".\n";
        }

        //coating check
        if (strlen($data[3]) > 50) {
            $comments .= "	Coating value is too long. Row " . $row . ".\n";
        }

        //specialty coating check
        //$sc = trim($data[4]);
        if (!(strtoupper($data[4]) == "YES" || strtoupper($data[4]) == "NO" || empty($data[4]))) {
            $comments .= "	Specialty coating value is undefined. Row " . $row . ".\n";
        }

        //aerosol check
        //$aerosol = trim($data[5]);
        if (!(strtoupper($data[5]) == "YES" || strtoupper($data[5]) == "NO" || empty($data[5]))) {
            $comments .= "	Aerosol value is undefined. Row " . $row . ".\n";
        }

        //vocwx check
        $data[8] = str_replace(",", ".", $data[8]);
        if ((!preg_match("/^[0-9.]*$/", $data[8]) && !preg_match("/^\d+\.{0,1}\d*\s*[A-Za-z]{1}\s*\/\s*[A-Za-z]{1}$/", $data[8])) || (substr_count($data[8], ".") > 1)) {
            $comments .= "	VOCWX is undefined. Row " . $row . ".\n";
        }

        //voclx check
        $data[9] = str_replace(",", ".", $data[9]);
        if ((!preg_match("/^[0-9.]*$/", $data[9]) && !preg_match("/^\d+\.{0,1}\d*\s*[A-Za-z]{1}\s*\/\s*[A-Za-z]{1}$/", $data[9]) ) || (substr_count($data[9], ".") > 1)) {
            $comments .= "	VOCLX is undefined. Row " . $row . ".\n";
        }

        //density check
        $data[16] = str_replace(",", ".", $data[16]);
        if (!preg_match("/^[0-9.]*$/", $data[16]) || (substr_count($data[16], ".") > 1)) {
            $comments .= "	Density is undefined. Row " . $row . ".\n";
        }

        //gavity check
        $data[17] = str_replace(",", ".", $data[17]);
        if (!preg_match("/^[0-9.]*$/", $data[17]) || (substr_count($data[17], ".") > 1)) {
            $comments .= "	Specific Gavity is undefined. Row " . $row . ".\n";
        }

        //boiling range check
        $data[18] = str_replace(",", ".", $data[18]);
        $data[18] = str_replace("C", "", $data[18]);
        $data[18] = str_replace("F", "", $data[18]);
        $data[18] = trim($data[18]);
        //if Boiling Range From is empty or N/A put 0
        if (empty($data[18]) || $data[18] == 'N/A')
            $data[18] = '0';
        if (!preg_match("/^[0-9.]*$/", $data[18]) || (substr_count($data[18], ".") > 1)) {
            $comments .= "	Boiling Range From is undefined. Row " . $row . ".\n";
        }

        $data[19] = str_replace(",", ".", $data[19]);
        $data[19] = str_replace("C", "", $data[19]);
        $data[19] = str_replace("F", "", $data[19]);
        $data[19] = trim($data[19]);
        //if Boiling Range To is empty or N/A put 0
        if (empty($data[19]) || $data[19] == 'N/A')
            $data[19] = '0';

        if (!preg_match("/^[0-9.]*$/", $data[19]) || (substr_count($data[19], ".") > 1)) {
            $comments .= "	Boiling Range To is undefined. Row " . $row . ".\n";
        }

        //hazardous class check
        if (strlen($data[20]) > 64) {
            $comments .= "	Hazardous class value is too long. Row " . $row . ".\n";
        }
        //if (empty($data[20]) && ($data[20] !== '0')){
        //	$comments .= "	Hazardous class is empty. Row " . $row . ".\n";
        //}
        //percent volatile by weight
        $data[29] = str_replace(",", ".", $data[29]);
        $data[29] = str_replace("%", "", $data[29]);
        if (!preg_match("/^[0-9.]*$/", $data[29]) || (substr_count($data[29], ".") > 1) || $data[29] > 100) {
            $comments .= "	Percent Volatile by Weight is undefined. Row " . $row . ".\n";
        }
        //percent volatile by volume
        $data[30] = str_replace(",", ".", $data[30]);
        $data[30] = str_replace("%", "", $data[30]);
        if (!preg_match("/^[0-9.]*$/", $data[30]) || (substr_count($data[30], ".") > 1) || $data[30] > 100) {
            $comments .= "	Percent Volatile by Weight is undefined. Row " . $row . ".\n";
        }

        //product pricing
        $data[38] = str_replace(",", ".", $data[38]);
        $data[38] = str_replace("$", "", $data[38]);
        if (!preg_match("/^[0-9.]*$/", $data[38]) || (substr_count($data[38], ".") > 1)) {
            $comments .= "	Product pricing is undefined. Row " . $row . ".\n";
        }
        //unit type
        $data[39] = trim($data[39]);
        if ($data[39] != "") {
            // isn't empty
            if ($data[39] != "QUART" && $data[39] != "quart" && $data[39] != "GALLON" &&
                    $data[39] != "gallon" && $data[39] != "GALLONS" && $data[39] != "gallons" && $data[39] != "GAL" && $data[39] != "gal" && $data[39] != "PINT" && $data[39] != "pint" && $data[39] != "LITRE" && $data[39] != "litre" && $data[39] != "KG" && $data[39] != "kg" && $data[39] != "ML" && $data[39] != "ml" && $data[39] != "GRAMS" && $data[39] != "grams" && $data[39] != "OUNCES" && $data[39] != "ounces" && $data[39] != "OZS" && $data[39] != "ozs") {
                $comments .= "	Unit type is undefined. Row " . $row . ".\n";
            }
        }
        //QTY
        $data[40] = str_replace(",", ".", $data[40]);
        if (!preg_match("/^[0-9.]*$/", $data[40]) || (substr_count($data[40], ".") > 1)) {
            $comments .= "	Quantity is undefined. Row " . $row . ".\n";
        }

        //percent volatile by volume
        $data[30] = str_replace(",", ".", $data[30]);
        $data[30] = str_replace("%", "", $data[30]);
        if (!preg_match("/^[0-9.]*$/", $data[30]) || (substr_count($data[30], ".") > 1) || $data[30] > 100) {
            $comments .= "	Percent Volatile by Weight is undefined. Row " . $row . ".\n";
        }

        //waste class
        //$data[31] = str_replace("/",",R",$data[31]);
        $wasteArray = explode(",", $data[31]);
        foreach ($wasteArray as $waste) {
            $waste = trim($waste);
            if (strlen($waste) > 20) {
                $comments .= "	Waste value is too long. Row " . $row . ".\n";
            }
        }

        //product library type
        $data[41] = trim($data[41]);
        if ($data[41] != "") {
            // isn't empty
            $libraryTypes = explode(',', $data[41]); // we get array of library types
            foreach ($libraryTypes as $libraryType) {
                if (trim($libraryType) != "PAINT SHOP" &&
                        trim($libraryType) != "paint shop" &&
                        trim($libraryType) != "PAINT SUPPLIES" &&
                        trim($libraryType) != "paint supplies" &&
                        trim($libraryType) != "BODY SHOP" &&
                        trim($libraryType) != "body shop" &&
                        trim($libraryType) != "DETAILING" &&
                        trim($libraryType) != "detailing" &&
                        trim($libraryType) != "PAINT PRODUCTS" &&
                        trim($libraryType) != "paint products" &&
                        trim($libraryType) != "POWDER COATING" &&
                        trim($libraryType) != "powder coating" &&
                        trim($libraryType) != "FUEL PRODUCTS" &&
                        trim($libraryType) != "fuel products") {
                    $comments .= "	Product Library type is undefined. Row " . $row . ".\n";
                    break;
                }
            }
        }

        return $comments;
    }

    private function componentDataCheck($data, $row)
    {
        $comments = "";

        //substrate check
        if (strlen($data[6]) > 200) {
            $comments .= "	Substrate value is too long. Row " . $row . ".\n";
        }

        //rule check
        if (empty($data[7])) {
            //$comments .= "Rule is empty. Row " . $row . "\n";  //check for NULL value is not required?
        } elseif (!preg_match("/^[0-9]+$/", $data[7])) {
            $comments .= "	Rule is undefined. Row " . $row . ".\n";
        }

        //cas check
        /* if ( !preg_match("/^[0-9\-]*$/",$data[10]) ){ //check by pattern is not required?
          $comments .= "CASE number is undefined. Row " . $row . "\n";
          } */
        if (strlen($data[10]) > 128) {
            $comments .= "	CASE number value is too long. Row " . $row . ".\n";
        }
        if (empty($data[10]) && ($data[10] !== '0')) {
            $comments .= "	CASE number is empty. Row " . $row . ".\n";
        }

        //comp description check
        if (strlen($data[11]) > 500) {
            $comments .= "	Description value is too long. Row " . $row . ".\n";
        }
        if (empty($data[11]) && ($data[11] !== '0')) {
            $comments .= "	Description is empty. Row " . $row . ".\n";
        }

        // mm/hg check
        $data[12] = str_replace(",", ".", $data[12]);
        if (!preg_match("/^[0-9.]*$/", $data[12]) || (substr_count($data[12], ".") > 1)) {
            $comments .= "	MM/HG is undefined. Row " . $row . ".\n";
        }

        //temp check
        $data[13] = $this->toCelsius($data[13]);
        $data[13] = str_replace(",", ".", $data[13]);
        $data[13] = str_replace("C", "", $data[13]);
        $data[13] = str_replace("c", "", $data[13]);
        $data[13] = trim($data[13]);
        if (!preg_match("/^[0-9]*\.*[0-9]*$/", $data[13]) || (substr_count($data[13], ".") > 1)) {
            $comments .= "	Temp is undefined. Row " . $row . ".\n";
        }

        //weight check
        $data[14] = str_replace(",", ".", $data[14]);
        $data[14] = str_replace("%", "", $data[14]);
        $data[14] = trim($data[14]);
        if (!preg_match("/^[0-9]*\.*[0-9]*$/", $data[14]) || (substr_count($data[14], ".") > 1)) {
            $comments .= "	WeightFrom is undefined. Row " . $row . ".\n";
        }

        //weight check
        $data[15] = str_replace(",", ".", $data[15]);
        $data[15] = str_replace("%", "", $data[15]);
        $data[15] = trim($data[15]);
        if (!preg_match("/^[0-9]*\.*[0-9]*$/", $data[15]) || (substr_count($data[15], ".") > 1)) {
            $comments .= "	WeightTo is undefined. Row " . $row . ".\n";
        }

        //vocpm check
        if (!(strtoupper($data[25]) == "VOC" || strtoupper($data[25]) == "PM" || empty($data[25]))) {
            $comments .= "	VOC/PM value is undefined. Row " . $row . ".\n";
        }

        //einecs check
        if (strlen($data[26]) > 128) {
            $comments .= "	Einecs/elincs value is too long. Row " . $row . ".\n";
        }

        //substance symbol check
        if (strlen($data[27]) > 10) {
            $comments .= "	Symbol of substance value is too long. Row " . $row . ".\n";
        }

        //substance r check
        //$data[28] = str_replace("/",",R",$data[28]);
        $rArray = explode(",", $data[28]);
        foreach ($rArray as $r) {
            $r = trim($r);
            if (strlen($r) > 32) {
                $comments .= "	R(*) of substance value is too long. Row " . $row . ".\n";
            }
        }

        return $comments;
    }

    private function trimAll($data)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = trim($data[$i]);
        }
        return $data;
    }

    private function tableHeader($file)
    {
        $dat = fgetcsv($file, 1000, ";");
        $firstRowData = Array();
        foreach ($dat as $val) {
            $firstRowData[] = mysql_real_escape_string($val);
        }
        $dat = fgetcsv($file, 1000, ";");
        $secondRowData = Array();
        foreach ($dat as $val) {
            $secondRowData[] = mysql_real_escape_string($val);
        }

        //possible headers variations
        $possibleProductID = array('PRODUCT ID', 'PRODUCTID', 'PRODUCT_ID');
        $possibleMFG = array('MFG', 'MANUFACTURER', 'SUPPLIER', 'PRODUCER');
        $possibleProductName = array('PRODUCT NAME/COLOR', 'PRODUCT NAME', 'COLOR', 'PRODUCTNAME/COLOR', 'PRODUCT_NAME/COLOR');
        $possibleType = array('COATING', 'COAT', 'TYPE');
        $possibleSpecCoating = array('SPECIALTY COATING', 'SPECIALTY_COATING', 'COATING (SPECIALTY)', 'COATING SPECIALTY');
        $possibleAerosol = array('AEROSOL', 'AIROSOL');
        $possibleSubstrate = array('SUBSTRATE', 'SUB STRATE', 'SUB_STRATE');
        $possibleRule = array('RULE', 'RULES');
        $possibleVocwx = array('VOCWX', 'MATERIAL VOC', 'MATERIAL_VOC', 'MATERIAL VOCWX', 'VOCWX MATERIAL');
        $possibleVoclx = array('VOCLX', 'COATING VOC', 'COATING_VOC', 'COATING VOCLX', 'VOCLX COATING');
        $possibleCase = array('CASE NUMBER', 'CAS NUMBER', 'CASE_NUMBER', 'NUMBER CASE', 'NUMBER CAS');
        $possibleDesription = array('DESCRIPTION', 'DESC');
        $possibleMMHG = array('MMHG', 'MM/HG', 'MM\\HG');
        $possibleTemp = array('TEMP', 'TMP', 'TEMPERATURE');
        $possibleWeight = array('WEIGHT', 'WEIGHT %', 'WEIGHT,%', 'WEIGHT, %');
        $possibleDensity = array('DENSITY', 'DENSITY LBS/GAL', 'DENSITY, LBS/GAL', 'DENSITY LBS/GAL US', 'US DENSITY LBS/GAL');
        $possibleVOCPM = array('VOC/PM', 'VOCPM', 'VOC PM', 'VOC\\PM');
        $possibleIndustryType = array('INDUSTRY TYPE', 'INDUSTRY TYPES');
        $possibleIndustrySubType = array('INDUSTRY SUB-CATEGORY', 'INDUSTRY SUB-CATEGORIES', 'INDUSTRY SUB- CATEGORIES');
        $possibleFlashPoint = array('FLASH POINT', 'FLASH-POINT');
        $possiblePaintOrChemical = array('PAINT COATING CHEMICAL PRODUCTS');
        $possibleEinecsElinks = array('IENECS', 'ELINCS', 'IENECS/ELINCS', 'IENECS / ELINCS', 'IENECS/ ELINCS', 'IENECS /ELINCS',
            'IENECS\\ELINCS', 'IENECS \\ ELINCS', 'IENECS\\ ELINCS', 'IENECS \\ELINCS');
        $possibleSubstanceSymbol = array('SYMBOL OF SUBSTANCE', 'SYMBOL', 'SYMBOL OF');
        $possibleSubstanceR = array('R(*) OF SUBSTANCE', 'RULE OF SUBSTANCE', 'R OF', 'R(*) OF', 'R (*) OF', 'R', 'R(*)', 'R (*)');
        $possiblePercentVolatile = array();
        $possibleHealth = array('HEALTH');

        $possibleProductPricing = array('PRODUCT PRICING');
        $possibleUnitType = array('UNIT TYPE');
        $possibleQTY = array('QTY');

        $possibleLibraryTypes = array('LIBRARY TYPE');

        $columnIndex = array();

        for ($i = 0; $i < count($secondRowData); $i++) {
            $columnIndex[$i] = FALSE;
            //PRODUCT ID mapping
            if (!isset($key['productID'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'PRODUCT' && strtoupper(trim($secondRowData[$i])) == 'ID') {
                    $key['productID'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleProductID as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['productID'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['productID'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //MFG mapping
            if (!isset($key['mfg'])) {
                foreach ($possibleMFG as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['mfg'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['mfg'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //HEALTH mapping
            if (!isset($key['health'])) {
                foreach ($possibleHealth as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['health'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['health'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //PRODUCT NAME/COLOR mapping
            if (!isset($key['productName'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'PRODUCT NAME' && strtoupper(trim($secondRowData[$i])) == 'COLOR') {
                    $key['productName'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleProductName as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['productName'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['productName'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //INDUSTRY TYPE mapping
            if (!isset($key['industryType'])) {
                foreach ($possibleIndustryType as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['industryType'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['industryType'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //INDUSTRY SUBTYPE mapping
            if (!isset($key['industrySubType'])) {
                foreach ($possibleIndustrySubType as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['industrySubType'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['industrySubType'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //Paint or Chemical mapping
            if (!isset($key['paintOrChemical'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'PAINT COATING' && strtoupper(trim($secondRowData[$i])) == 'CHEMICAL PRODUCTS') {
                    $key['paintOrChemical'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possiblePaintOrChemical as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['paintOrChemical'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['paintOrChemical'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //FLASH POINT mapping
            if (!isset($key['flashPoint'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'FLASH' && strtoupper(trim($secondRowData[$i])) == 'POINT') {
                    $key['flashPoint'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleFlashPoint as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['flashPoint'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['flashPoint'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //TYPE mapping
            if (!isset($key['type'])) {
                foreach ($possibleType as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header && empty($secondRowData[$i])) {
                        $key['type'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header && empty($firstRowData[$i])) {
                        $key['type'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //Spec Coating mapping
            if (!isset($key['scoating'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'SPECIALTY' && strtoupper(trim($secondRowData[$i])) == 'COATING') {
                    $key['scoating'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                if (strtoupper(trim($firstRowData[$i])) == 'COATING' && strtoupper(trim($secondRowData[$i])) == 'SPECIALTY') {
                    $key['scoating'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleSpecCoating as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['scoating'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['scoating'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //aerosol mapping
            if (!isset($key['aerosol'])) {
                foreach ($possibleAerosol as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['aerosol'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['aerosol'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //substrate mapping
            if (!isset($key['substrate'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'SUB' && strtoupper(trim($secondRowData[$i])) == 'STRATE') {
                    $key['substrate'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleSubstrate as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['substrate'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['substrate'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //rule mapping
            if (!isset($key['rule'])) {
                foreach ($possibleRule as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['rule'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['rule'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //vocwx mapping
            if (!isset($key['vocwx'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'MATERIAL' &&
                        (strtoupper(trim($secondRowData[$i])) == 'VOC' || strtoupper(trim($secondRowData[$i])) == 'VOCWX')) {
                    $key['vocwx'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleVocwx as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['vocwx'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['vocwx'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //voclx mapping
            if (!isset($key['voclx'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'COATING' &&
                        (strtoupper(trim($secondRowData[$i])) == 'VOC' || strtoupper(trim($secondRowData[$i])) == 'VOCWX')) {
                    $key['voclx'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleVoclx as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['voclx'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['voclx'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //case number mapping
            if (!isset($key['case'])) {
                if ((strtoupper(trim($firstRowData[$i])) == 'CASE' || strtoupper(trim($firstRowData[$i])) == 'CAS' ) &&
                        strtoupper(trim($secondRowData[$i])) == 'NUMBER') {
                    $key['case'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleCase as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['case'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['case'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //description number mapping
            if (!isset($key['description'])) {
                foreach ($possibleDesription as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['description'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['description'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //mm/hg number mapping
            if (!isset($key['mmhg'])) {
                foreach ($possibleMMHG as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['mmhg'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['mmhg'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //temp number mapping
            if (!isset($key['temp'])) {
                foreach ($possibleTemp as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['temp'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['temp'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //weight from/to mapping
            if (!isset($key['weightFrom'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'WEIGHT' && strtoupper(trim($secondRowData[$i])) == 'FROM') {
                    $key['weightFrom'] = $i;
                    $key['weightTo'] = $i + 1;
                    $columnIndex[$i] = TRUE;
                    $columnIndex[$i + 1] = TRUE;
                } elseif (strtoupper(trim($firstRowData[$i])) == 'WEIGHT' && strtoupper(trim($secondRowData[$i])) == 'TO') {
                    $key['weightFrom'] = $i - 1;
                    $key['weightTo'] = $i;
                    $columnIndex[$i - 1] = TRUE;
                    $columnIndex[$i] = TRUE;
                }
            }

            //density mapping
            if (!isset($key['density'])) {
                foreach ($possibleDensity as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['density'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['density'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //s gavity mapping
            if (!isset($key['gavity'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'GAVITY') {
                    $key['gavity'] = $i;
                    $columnIndex[$i] = TRUE;
                } elseif (strtoupper(trim($secondRowData[$i])) == 'GAVITY') {
                    $key['gavity'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //boiling range from/to mapping
            if (!isset($key['boilingRangeFrom'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'BOILING RANGE' && strtoupper(trim($secondRowData[$i])) == 'FROM') {
                    $key['boilingRangeFrom'] = $i;
                    $key['boilingRangeTo'] = $i + 1;
                    $columnIndex[$i] = TRUE;
                    $columnIndex[$i + 1] = TRUE;
                } elseif (strtoupper(trim($firstRowData[$i])) == 'BOILING RANGE' && strtoupper(trim($secondRowData[$i])) == 'TO') {
                    $key['boilingRangeFrom'] = $i - 1;
                    $key['boilingRangeTo'] = $i;
                    $columnIndex[$i - 1] = TRUE;
                    $columnIndex[$i] = TRUE;
                }
            }

            //class mapping
            if (!isset($key['class'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'CLASS') {
                    $key['class'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //irr mapping
            if (!isset($key['irr'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'IRR') {
                    $key['irr'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //ohh mapping
            if (!isset($key['ohh'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'OHH') {
                    $key['ohh'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //sens mapping
            if (!isset($key['sens'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'SENS') {
                    $key['sens'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //sens mapping
            if (!isset($key['oxy1'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'OXY-1') {
                    $key['oxy1'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //voc/pm mapping
            if (!isset($key['VOCPM'])) {
                foreach ($possibleVOCPM as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['VOCPM'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['VOCPM'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //	einecs/elincs mapping
            if (!isset($key['einecsElincs'])) {
                foreach ($possibleEinecsElinks as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['einecsElincs'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['einecsElincs'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //	substance symbol mapping
            if (!isset($key['substanceSymbol'])) {
                foreach ($possibleSubstanceSymbol as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['substanceSymbol'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['substanceSymbol'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //	substance r mapping
            if (!isset($key['substanceR'])) {
                foreach ($possibleSubstanceR as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['substanceR'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['substanceR'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //	Percent Volatile mapping
            if (!isset($key['percentVolatileWeight'])) {
                if (strtoupper(trim($firstRowData[$i])) == 'PERCENT VOLATILE' && strtoupper(trim($secondRowData[$i])) == 'BY WEIGHT') {
                    $key['percentVolatileWeight'] = $i;
                    $key['percentVolatileVolume'] = $i + 1;
                    $columnIndex[$i] = TRUE;
                    $columnIndex[$i + 1] = TRUE;
                } elseif (strtoupper(trim($firstRowData[$i])) == 'PERCENT VOLATILE' && strtoupper(trim($secondRowData[$i])) == 'BY VOLUME') {
                    $key['percentVolatileWeight'] = $i - 1;
                    $key['percentVolatileVolume'] = $i;
                    $columnIndex[$i - 1] = TRUE;
                    $columnIndex[$i] = TRUE;
                }
            }


            //waste mapping
            if (!isset($key['waste'])) {
                if (strtoupper(trim($secondRowData[$i])) == 'WASTE') {
                    $key['waste'] = $i;
                    $columnIndex[$i] = TRUE;
                }
            }

            //product pricing mapping
            if (!isset($key['productPricing'])) {
                if ((strtoupper(trim($firstRowData[$i])) == 'PRODUCT') &&
                        strtoupper(trim($secondRowData[$i])) == 'PRICING') {
                    $key['productPricing'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleProductPricing as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['possibleProductPricing'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['possibleProductPricing'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }
            // unit type mapping
            if (!isset($key['unitType'])) {
                if ((strtoupper(trim($firstRowData[$i])) == 'UNIT') &&
                        strtoupper(trim($secondRowData[$i])) == 'TYPE') {
                    $key['unitType'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleUnitType as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['unitType'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['unitType'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //QTY mapping
            if (!isset($key['QTY'])) {
                foreach ($possibleQTY as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['QTY'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['QTY'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }

            //library types mapping
            if (!isset($key['libraryTypes'])) {
                if ((strtoupper(trim($firstRowData[$i])) == 'LIBRARY') &&
                        strtoupper(trim($secondRowData[$i])) == 'TYPE') {
                    $key['libraryTypes'] = $i;
                    $columnIndex[$i] = TRUE;
                }
                foreach ($possibleLibraryTypes as $header) {
                    if (strtoupper(trim($firstRowData[$i])) == $header) {
                        $key['possibleLibraryTypes'] = $i;
                        $columnIndex[$i] = TRUE;
                    } elseif (strtoupper(trim($secondRowData[$i])) == $header) {
                        $key['possibleLibraryTypes'] = $i;
                        $columnIndex[$i] = TRUE;
                    }
                }
            }
        }

        $columnsArray = array('productID', 'mfg', 'productName', 'type', 'scoating', 'aerosol', 'substrate',
            'rule', 'vocwx', 'voclx', 'case', 'description', 'mmhg', 'temp', 'weight',
            'density', 'gavity', 'boilingRangeFrom', 'boilingRangeTo', 'class', 'irr',
            'ohh', 'sens', 'oxy1', 'VOCPM', 'einecsElincs', 'substanceSymbol', 'substanceR',
            'percentVolatileWeight', 'percentVolatileVolume', 'waste', 'productPricing', 'unitType', 'QTY', 'libraryTypes');
        for ($i = 0; $i < count($columnsArray); $i++) {
            if (!isset($key[$columnsArray[$i]]) && !$columnIndex[$i]) {
                //$key[$columnsArray[$i]] = $i;
            } elseif (!isset($key[$columnsArray[$i]])) {
                for ($j = 0; $j < count($secondRowData); $j++) {
                    if (!$columnIndex[$j]) {
                        //$key[$columnsArray[$i]] = $j;
                        break;
                    }
                }
            }
        }

        return $key;
    }

    private function tableHeader4GOM($file)
    {
        $headerRowData = array();
        $data = fgetcsv($file, 1000, ";");
        foreach ($data as $val) {
            $headerRowData[] = mysql_real_escape_string(trim($val));
        }
        $data = fgetcsv($file, 1000, ";");
        for ($j = 0; $j < count($data); $j++) {
            if (!empty($data[$j])) {
                $headerRowData[$j] .= " " . mysql_real_escape_string(trim($data[$j]));
            }
        }

        //possible headers variations
        $possibleLocation = array('LOCATION:');
        $possibleInv = array('INV#');
        $possibleCust = array('CUST#');
        $possibleJobberClient = array('JOBBER CLIENT', 'OPTIONAL JOBBER CLIENT');
        $possibleJobber = array('JOBBER');
        $possibleAssignToAll = array('ALL');
        $possibleVendor = array('VENDOR', 'VENDER');
        $possibleName = array('NAME');
        $possibleGOMCode = array('PART#', 'PART No.');
        $possibleDescription = array('DESCRIPTION');
        $possibleCategory = array('CAT', 'CATEGORY');
        $possibleSubCategory = array('SCAT', 'SUB-CATEGORY');
        $possibleInvDate = array('INV DT');
        $possibleUnit = array('UNIT TYPE', 'UNIT');
        $possibleUnitQuantity = array('UNIT QTY');
        $possibleQuantity = array('QTY', 'QUANTITY');
        $possibleSales = array('SALES$', 'PRICING');

        $columnIndex = array();

        $is_index2name = true;

        for ($i = 0; $i < count($headerRowData); $i++) {
            $columnIndex[$i] = false;
            if (!isset($key['location'])) {
                foreach ($possibleLocation as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'location' : $key['location'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['inv'])) {
                foreach ($possibleInv as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'inv' : $key['inv'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['cust'])) {
                foreach ($possibleCust as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'cust' : $key['cust'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['jobber_client'])) {
                foreach ($possibleJobberClient as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'jobber_client' : $key['jobber_client'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['jobber'])) {
                foreach ($possibleJobber as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'jobber' : $key['jobber'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['assign_to_all'])) {
                foreach ($possibleAssignToAll as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'assign_to_all' : $key['assign_to_all'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['vendor'])) {
                foreach ($possibleVendor as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'vendor' : $key['vendor'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['name'])) {
                foreach ($possibleName as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'name' : $key['name'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['gom_code'])) {
                foreach ($possibleGOMCode as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'gom_code' : $key['gom_code'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['description'])) {
                foreach ($possibleDescription as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'description' : $key['description'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['category'])) {
                foreach ($possibleCategory as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'category' : $key['category'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['sub_category'])) {
                foreach ($possibleSubCategory as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'sub_category' : $key['sub_category'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['inv_date'])) {
                foreach ($possibleInvDate as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'inv_date' : $key['inv_date'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['unit'])) {
                foreach ($possibleUnit as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'unit' : $key['unit'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['unit_quantity'])) {
                foreach ($possibleUnitQuantity as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'unit_quantity' : $key['unit_quantity'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['quantity'])) {
                foreach ($possibleQuantity as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'quantity' : $key['quantity'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
            if (!isset($key['sales'])) {
                foreach ($possibleSales as $header) {
                    if (strtoupper(trim($headerRowData[$i])) == strtoupper(trim($header))) {
                        $is_index2name ? $key[$i] = 'sales' : $key['sales'] = $i;
                        $columnIndex[$i] = true;
                    }
                }
            }
        }

        $columnsArray = array('location', 'inv', 'cust', 'jobber_client', 'jobber', 'assign_to_all', 'vendor', 'name', 'gom_code',
            'description', 'category', 'sub_category', 'inv_date', 'unit', 'quantity', 'sales');
        for ($i = 0; $i < count($columnsArray); $i++) {
            if (!isset($key[$columnsArray[$i]]) && !$columnIndex[$i]) {
                //$key[$columnsArray[$i]] = $i;
            }
        }

        return $key;
    }

    private function map()
    {
        return array(
            0 => "productID",
            1 => "MFG",
            2 => "productName",
            3 => "coating",
            4 => "specialtyCoating",
            5 => "aerosol",
            6 => "substrate",
            7 => "rule",
            8 => "vocwx",
            9 => "voclx",
            10 => "caseNumber",
            11 => "description",
            12 => "mmhg",
            13 => "temp",
            14 => "weightFrom",
            15 => "weightTo",
            16 => "density",
            17 => "gavity",
            18 => "boilingRangeFrom",
            19 => "boilingRangeTo",
            20 => "hazardousClass",
            21 => "hazardousIRR",
            22 => "hazardousOHH",
            23 => "hazardousSENS",
            24 => "hazardousOXY",
            25 => "vocpm",
            26 => "einecsElincs",
            27 => "substanceSymbol",
            28 => "substanceR",
            29 => "percentVolatileWeight",
            30 => "percentVolatileVolume",
            31 => "waste",
        );
    }

    private function componentsKeys()
    {
        return array(6, 7, 10, 11, 12, 13, 14, 15, 25, 26, 27, 28);
    }

    private function productsKeys()
    {
        return array(0, 1, 2, 3, 4, 5, 8, 9, 16, 17, 18, 19, 20, 21, 22, 23, 24, 29, 30, 31);
    }

    /**
     * Check array if it has at least one non empty string
     * @param array $row
     * @return boolean
     */
    private function isEmptyRow($row)
    {
        foreach ($row as $item) {
            if ($item != "") {
                return false;
            }
        }

        return true;
    }

    /*
     *
     */

    private function convertDensity($data)
    {
        $data = trim($data);
        if (preg_match("/^\d+\.{0,1}\d*\s*[A-Za-z]{1}\s*\/\s*[A-Za-z]{1}$/", $data)) {
            $data = preg_replace('/[A-Za-z]/', '', $data);
            $data = preg_replace('/\//', '', $data);
            $data = trim($data);

            //	gram per liter
            $from = new Density($this->db);
            $from->setNumerator(Unittype::UNIT_G_ID);
            $from->setDenominator(Unittype::UNIT_L_ID);

            // lbs per gallon
            $to = new Density($this->db);
            $to->setNumerator(Unittype::UNIT_LBS_ID);
            $to->setDenominator(Unittype::UNIT_GAL_ID);

            $converter = new UnitTypeConverter();

            $result = $converter->convertDensity($data, $from, $to, new Unittype($this->db));
        } else {
            $result = trim($data);
        }
        $result = strval($result);

        return $result;
    }

    public function validateProcess($input)
    {
        $CSVPath = $input['inputFile'];

        //last row
        $file = fopen($CSVPath, "a");

        fwrite($file, ";;;;;;;;;;;;;;;;;;;;;;;;\n");
        fclose($file);

        $file = fopen($CSVPath, "r");

        $error = "";
        $this->errorComments = "--------------------------------\n";
        $this->errorComments .= "(" . date("m.d.Y H:i:s") . ") Starting validation of " . $input['realFileName'] . "...\n";

        $currentRow = 0;
        $headerEndsRow = 4;
        //	here we'll store rows for single pfp
        $currentProcessName = '';
        $isErrorInCurrentProcess = false;

        $i = 0;

        while ($dat = fgetcsv($file, 1000, ";")) {
            $currentRow++;
            $i++;

            if ($currentRow < $headerEndsRow) {
                //	skip first $headerEndsRow rows
                continue;
            }

            $data = $this->trimAll($dat);

            //	check empty row
            if ($this->isEmptyRow($data)) {
                continue;
            }

            //get a process in which the error

            if ($data[0] != '') {

                if ($isErrorInCurrentProcess) {
                    $this->setProcessError($currentProcessName);
                    $isErrorInCurrentProcess = false;
                } elseif ($currentProcessName != '') {
                    $this->setProcessCorrect($currentProcessName);
                }

                $currentProcessName = $data[0];
            }

            /* if($currentRow==4){
              die(var_dump($data));
              } */

            $currRowComments = $this->processDataCheck($data, $currentRow);

            if ($currRowComments != "") {
                $this->errorComments .= $currRowComments;
                $isErrorInCurrentProcess = true;
            }
        }
        //get last process
        if ($isErrorInCurrentProcess) {
            $this->setProcessError($currentProcessName);
        } else {
            $this->setProcessCorrect($currentProcessName);
        }

        fclose($file);
    }

    private function processDataCheck($data, $row)
    {
        $comments = "";

        //Check Rate Unit Type
        if (!is_null($data[self::RATE_UNIT_TYPE])) {
            if ($data[self::RATE_UNIT_TYPE] != '') {
                $query = "SELECT unittype_id FROM " . self::TB_UNIT_TYPE . " " .
                        "WHERE name = '" . $data[self::RATE_UNIT_TYPE] . "'";
                $this->db->query($query);
                if ($this->db->num_rows() == 0) {
                    $comments .= "Rate unit type with name : " . $data[self::RATE_UNIT_TYPE] . " doesn't exist. Row " . $row . ".\n";
                }
            }
        } else {
            $comments .= "Rate unit type doesn't exist. Row " . $row . ".\n";
        }
        //Check Unit Type
        if ($data[self::UNIT_TYPE] == '') {
            $comments.="You haven't entered Unit Type. Row " . $row . "\n";
        } else {
            $query = "SELECT unittype_id FROM " . self::TB_UNIT_TYPE . " " .
                    "WHERE name = '" . $data[self::UNIT_TYPE] . "'";
            $this->db->query($query);
            if ($this->db->num_rows() == 0) {
                $comments .= "Rate unit type with name : " . $data[self::UNIT_TYPE] . " doesn't exist. Row " . $row . ".\n";
            }
        }
        //check QTY
        if ($data[self::QTY] == '') {
            $comments.="You haven't entered QTY. Row " . $row . "\n";
        } else {
            if (!Validation::isFloat($data[self::QTY])) {
                $comments.="QTY " . $data[self::QTY] . " is not a number. Row " . $row . "\n";
            }
        }
        //check RATE
        if ($data[self::RATE] == '') {
            $comments.="You haven't entered QTY. Row " . $row . "\n";
        } else {
            if (!Validation::isFloat($data[self::RATE])) {
                $comments.="Rate " . $data[self::RATE] . " is not a number. Row " . $row . "\n";
            }
        }
        return $comments;
    }

}
?>