<?php

class MSDS {
    /**
     *
     * @var db
     */
    private $db;
    public $totalNumberOfFiles = 5;

    function MSDS($db) {
        $this->db = $db;
    }

    public function upload($type, $companyID = null) {
        //	upload
        $uploads_dir = "../msds";
        $allowedFormats = array('doc', 'pdf');

        //getting files and destruct them
        $filesFromDir = $this->dirContence($allowedFormats, $uploads_dir);
        for ($i = 0; $i < count($filesFromDir); $i++) {
            $_pos = strripos($filesFromDir[$i], "_");
            $alreadyUploadedSheet['index'][$i] = substr($filesFromDir[$i], $_pos + 1, strripos($filesFromDir[$i], ".") - $_pos - 1);
            $alreadyUploadedSheet['ext'][$i] = substr($filesFromDir[$i], strripos($filesFromDir[$i], ".") + 1);
            $alreadyUploadedSheet['name'][$i] = substr($filesFromDir[$i], 0, $_pos) . "." . $alreadyUploadedSheet['ext'][$i];
        }
        //distinct files. keep files with max index
        $distinctFilesName = array_unique($alreadyUploadedSheet['name']);
        foreach ($distinctFilesName as $distinctFileName) {
            $tmp[] = $distinctFileName;
        }
        $distinctFilesName = $tmp;
        for ($i = 0; $i < count($distinctFilesName); $i++) {
            $indexes = array();
            for ($j = 0; $j < count($alreadyUploadedSheet['name']); $j++) {
                if ($distinctFilesName[$i] == $alreadyUploadedSheet['name'][$j]) {
                    $indexes[] = $alreadyUploadedSheet['index'][$j];
                }
            }
            $alreadyUploadedSheets['index'][$i] = max($indexes);
            $alreadyUploadedSheets['ext'][$i] = substr($distinctFilesName[$i], strripos($distinctFilesName[$i], ".") + 1);
            $alreadyUploadedSheets['name'][$i] = $distinctFilesName[$i];
        }
        unset($alreadyUploadedSheet);

        if ($type == 'basic') {
            foreach ($_FILES["inputFile"]["size"] as $key => $size) {
                if ($size > 0) {
                    $VPSError = $this->VPSLimitsExceed($size, $companyID);
                    if (!$VPSError) {
                        $tmp_name = $_FILES["inputFile"]["tmp_name"][$key];

                        $currentFile['name'] = $_FILES["inputFile"]["name"][$key];
                        $ext = substr($currentFile['name'], strripos($currentFile['name'], ".") + 1);
                        $extNumberSymbols = strlen($currentFile['name']) - strripos($currentFile['name'], ".");
                        $currentFile['real_name'] = substr($currentFile['name'], 0, -$extNumberSymbols) . "_0." . $ext;

                        if ($this->checkExtension($allowedFormats, $currentFile['name'])) {
                            for ($i = 0; $i < count($alreadyUploadedSheets['name']); $i++) {
                                if (strtolower($alreadyUploadedSheets['name'][$i]) == strtolower($currentFile['name'])) {
                                    $alreadyUploadedSheets['index'][$i]++;
                                    $_pos = strripos($currentFile['real_name'], "_");
                                    $currentFile['real_name'] = substr($currentFile['real_name'], 0, $_pos);
                                    $currentFile['real_name'] = $currentFile['real_name'] . "_" . $alreadyUploadedSheets['index'][$i] . "." . $ext;
                                }
                            }

                            move_uploaded_file($tmp_name, $uploads_dir . "/" . $currentFile['real_name']);

                            $msdsName['real_name'] = $currentFile['real_name'];
                            $msdsName['name'] = $currentFile['name'];
                            $msdsName['size'] = $size;
                            $msdsNames[] = $msdsName;
                        } else {
                            $fileWithError['name'] = $_FILES["inputFile"]["name"][$key];
                            $fileWithError['error'] = "Only .pdf and .doc files!";
                            $filesWithError[] = $fileWithError;
                        }
                    } else {
                        $fileWithError['name'] = $_FILES["inputFile"]["name"][$key];
                        $fileWithError['error'] = $VPSError;
                        $filesWithError[] = $fileWithError;
                    }
                } else { // errors
                    if ($_FILES["inputFile"]["error"][$key] != 4) {
                        $fileWithError['name'] = $_FILES["inputFile"]["name"][$key];
                        switch ($_FILES["inputFile"]["error"][$key]) {
                            case 1:
                                $fileWithError['error'] = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                                break;
                            case 2:
                                $fileWithError['error'] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                                break;
                            case 3:
                                $fileWithError['error'] = "The uploaded file was only partially uploaded.";
                                break;
                            case 4:
                                $fileWithError['error'] = "No file was uploaded.";
                            case 6:
                                $fileWithError['error'] = "Missing a temporary folder.";
                            case 7:
                                $fileWithError['error'] = "Failed to write file to disk.";
                            case 8:
                                $fileWithError['error'] = "File upload stopped by extension.";
                            default:
                                $fileWithError['error'] = $fileWithError['error'];
                                break;
                        }
                        $filesWithError[] = $fileWithError;
                    }
                }
            }
        } else {
            if ($_FILES["Filedata"]["size"] > 0) {
                $VPSError = $this->VPSLimitsExceed($_FILES["Filedata"]["size"], $companyID);
                if (!$VPSError) {
                    $tmp_name = $_FILES["Filedata"]["tmp_name"];

                    $currentFile['name'] = $_FILES["Filedata"]["name"];
                    $ext = substr($currentFile['name'], strripos($currentFile['name'], ".") + 1);
                    $extNumberSymbols = strlen($currentFile['name']) - strripos($currentFile['name'], ".");
                    $currentFile['real_name'] = substr($currentFile['name'], 0, -$extNumberSymbols) . "_0." . $ext;

                    if ($this->checkExtension($allowedFormats, $currentFile['name'])) {
                        for ($i = 0; $i < count($alreadyUploadedSheets['name']); $i++) {
                            if (strtolower($alreadyUploadedSheets['name'][$i]) == strtolower($currentFile['name'])) {
                                $alreadyUploadedSheets['index'][$i]++;
                                $_pos = strripos($currentFile['real_name'], "_");
                                $currentFile['real_name'] = substr($currentFile['real_name'], 0, $_pos);
                                $currentFile['real_name'] = $currentFile['real_name'] . "_" . $alreadyUploadedSheets['index'][$i] . "." . $ext;
                            }
                        }

                        $path = $uploads_dir . "/" . $currentFile['real_name'];
                        move_uploaded_file($tmp_name, $path);

                        $msdsName['real_name'] = $currentFile['real_name'];
                        $msdsName['name'] = $currentFile['name'];
                        $msdsName['size'] = $_FILES["Filedata"]["size"];
                        $msdsNames[] = $msdsName;
                    } else {
                        $fileWithError['name'] = $_FILES["Filedata"]["name"];
                        $fileWithError['error'] = "Only .pdf and .doc files!";
                        $filesWithError[] = $fileWithError;
                    }
                }
            }
        }

        //	MSDS to Product recognition
        /*
          $msdsNames[] = array (
          "name" => "Ca 224.pdf",
          "real_name" => "Ca224.pdf"
          );

          $msdsNames[] = array (
          "name" => "JHFG34.doc",
          "real_name" => "JHFG34_2.doc"
          );

          $msdsNames[] = array (
          "name" => "V66-V55.doc",
          "real_name" => "V66-V55_1.doc"
          );
         */
        $msdsResult = $this->recognize($msdsNames);

        $output['msdsResult'] = $msdsResult;
        $output['filesWithError'] = $filesWithError;

        return $output;
    }

    public function assign() {
        
    }

    public function show() {
        
    }

    private function recognize($msdsNames) {
        foreach ($msdsNames as $msds) {
            $msdsProduct["name"] = $msds["name"];
            $msdsProduct["real_name"] = $msds["real_name"];
            $msdsProduct["size"] = $msds["size"];

            //	Recognize product for MSDS name
            $msdsProduct["product_id"] = $this->getProductIDByMSDS($msdsProduct["name"]);

            if ($msdsProduct["product_id"] == "") {
                $msdsProduct["isRecognized"] = false;
            } else {
                $msdsProduct["isRecognized"] = true;
            }

            //	Put MSDS-Product into result
            $msdsProducts[] = $msdsProduct;
        }

        return $msdsProducts;
    }

    private function getProductIDByMSDS($msdsName) {
        $msdsName = $this->cutFileExtension($msdsName);

        //$this->db->select_db(DB_NAME);

        $query = "SELECT product_id, product_nr FROM " . TB_PRODUCT . " WHERE 1";
        $this->db->query($query);

        $maxRank = 0;
        $productID = "";
        foreach ($this->db->fetch_all() as $product) {
            $productRank = $this->getProductRank($product->product_nr, $msdsName);

            if ($productRank > $maxRank) {
                $maxRank = $productRank;
                $productID = $product->product_id;

                if ($productRank == 1) {
                    break;
                }
            }
        }

        return $productID;
    }

    private function cutFileExtension($fileName) {
        $spotIndex = strripos($fileName, ".");

        if (!($spotIndex === false)) {
            $fileName = substr($fileName, 0, $spotIndex);
        }

        return $fileName;
    }

    private function getProductRank($productNR, $msdsName) {
        $msdsName = strtolower(trim($msdsName));
        $productNR = strtolower(trim($productNR));

        if ($msdsName == $productNR) {
            return 1;
        } elseif (str_replace(" ", "", $msdsName) == str_replace(" ", "", $productNR)) {
            return 0.9;
        } elseif (str_replace(array("-", " "), "", $msdsName) == str_replace(array("-", " "), "", $productNR)) {
            return 0.85;
        }

        return 0;
    }

    private function checkExtension($allowedFormats, $fileName) {
        $result = FALSE;
        foreach ($allowedFormats as $format) {
            $fileName = strtolower($fileName);
            if (substr($fileName, -3) == $format) {
                $result = TRUE;
            }
        }

        return $result;
    }

    private function dirContence($allowedFormats, $uploads_dir) {

        if ($folderHandler = opendir($uploads_dir)) {
            while (false !== $file = readdir($folderHandler)) {
                if (!($file == "." || $file == "..")) {
                    if ($this->checkExtension($allowedFormats, $file)) {
                        $filesFromDir[] = $file;
                    }
                }
            }
            closedir($folderHandler);
        }
        return $filesFromDir;
    }

    public function validateAssignments($assignments) {
        /*
         * Assignment structure:
         * 
         * - msdsName
         * - productID
         */
        //$this->db->select_db(DB_NAME);

        foreach ($assignments as $assignment) {
            //	Default settings
            $result = array();
            $result["status"] = "ok";
            $result["msdsName"] = $assignment["msdsName"];
            $result["productID"] = $assignment["productID"];

            //	Check if product is already assigned by MSDS
            //	WARNING! - COMPANY ID SEPARATOR NOT IMPLEMENTED YET
            if ($this->isAlreadyAssigned($assignment["productID"])) {
                $result["status"] = "failed";
                $result["reason"] = "alreadyAssigned";
                $result["assignedMSDS"] = $this->getAssignedMSDS($assignment["productID"]);
            }

            //	Check for multiple assignments
            if ($assignment["product"] != "" && $this->isMultipleAssignment($assignment["productID"], $assignments)) {
                $result["status"] = "failed";
                $result["reason"] = "multiple";
            }

            $results[] = $result;
        }

        return $results;
    }

    private function isMultipleAssignment($productID, $assignments) {
        $count = 0;

        foreach ($assignments as $assignment) {
            if ($productID == $assignment["productID"]) {
                $count++;

                if ($count > 1) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isAlreadyAssigned($productID) {
        if ($this->getAssignedMSDS($productID) != "") {
            return true;
        } else {
            return false;
        }
    }

    private function getAssignedMSDS($productID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT msds_file_id FROM " . TB_MSDS_FILE . " WHERE product_id=" . $productID . " LIMIT 1";
        $this->db->query($query);

        if ($this->db->num_rows()) {
            return $this->db->fetch(0)->msds_file_id;
        } else {
            return "";
        }
    }

    public function addSheets($sheets) {

        //$this->db->select_db(DB_NAME);

        $companyID = $sheets["companyID"];
        if (empty($companyID)) {
            $companyID = "NULL";
        }

        $facilityID = $sheets["facilityID"];
        if (empty($facilityID)) {
            $facilityID = "NULL";
        }

        $departmentID = $sheets["departmentID"];
        if (empty($departmentID)) {
            $departmentID = "NULL";
        }



        foreach ($sheets["msds"] as $msds) {
            if (empty($msds["productID"])) {
                $msds["productID"] = "NULL";
            }



            $query = "INSERT INTO " . TB_MSDS_FILE . " (name, real_name, company_id, facility_id, department_id, product_id) VALUES ("
                    . "'" . $msds["name"] . "'"
                    . ", '" . $msds["real_name"] . "'"
                    . ", $companyID"
                    . ", $facilityID"
                    . ", $departmentID"
                    . ", " . $msds["productID"]
                    . ")";
            $this->db->query($query);
        }
    }

    //	WARNING! NOT DEBUGGED
    public function updateSheet($msds) {
        $this->db->select_DB(DB_NAME);

        $query = "UPDATE " . TB_MSDS_FILE . " SET "
                . "name='" . $msds["name"] . "'"
                . ", real_name='" . $msds["real_name"] . "'"
                . ", company_id=" . $msds["companyID"]
                . ", facility_id=" . $msds["facilityID"]
                . ", department_id=" . $msds["departmentID"]
                . ", product_id=" . $msds["productID"]
                . " WHERE msds_file_id=" . $msds["msdsFileID"];

        $this->db->query($query);
    }

    public function getSheetByProduct($productID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT msds_file_id as 'id',name,real_name as 'realName', company_id as 'companyID', facility_id as 'facilityID', department_id as 'departmentID', product_id as 'productID' " .
                "FROM msds_files " .
                "WHERE product_id = " . $productID;

        $this->db->query($query);

        if ($this->db->num_rows()) {
            $msdsSheet = $this->db->fetch_array(0);

            /* $msdsSheet = array (
              'id' 			=> $data->msds_file_id,
              'name' 			=> $data->name,
              'realName' 		=> $data->real_name,
              'companyID'		=> $data->company_id,
              'facilityID' 	=> $data->facility_id,
              'departmentID' 	=> $data->department_id,
              'productID'	=> $data->product_id
              ); */
        }

        return $msdsSheet;
    }

    public function unlinkMsdsSheet($msdsSheetID) {
        //$this->db->select_db(DB_NAME);

        $query = "UPDATE msds_files " .
                "SET product_id = NULL " .
                "WHERE msds_file_id = " . $msdsSheetID;
        $this->db->query($query);
    }

    public function getSheetDetails($msdsSheetID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT msds_file_id as 'id', name, real_name as 'realName', company_id as 'companyID', facility_id as 'facilityID', department_id as 'departmentID', product_id as 'productID' " .
                "FROM msds_files " .
                "WHERE msds_file_id = " . $msdsSheetID;

        $this->db->query($query);

        if ($this->db->num_rows()) {
            $msdsSheet = $this->db->fetch_array(0);

            /* $msdsSheet = array (
              'id' 			=> $data->msds_file_id,
              'name' 			=> $data->name,
              'realName' 		=> $data->real_name,
              'companyID'		=> $data->company_id,
              'facilityID' 	=> $data->facility_id,
              'departmentID' 	=> $data->department_id,
              'productID'	=> $data->product_id
              ); */
        }

        return $msdsSheet;
    }

    public function getUnlinkedMsdsSheets() {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT * " .
                "FROM msds_files " .
                "WHERE product_id is NULL";

        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);

                $msdsSheet = array(
                    'id' => $data->msds_file_id,
                    'name' => $data->name,
                    'realName' => $data->real_name,
                    'companyID' => $data->company_id,
                    'facilityID' => $data->facility_id,
                    'departmentID' => $data->department_id,
                    'productID' => $data->product_id
                );
                $msdsSheet["msdsLink"] = "../msds/" . $data->real_name;
                $msdsSheets[] = $msdsSheet;
            }
        }

        return $msdsSheets;
    }

    public function linkSheetToProduct($msdsSheetID, $productID) {
        //$this->db->select_db(DB_NAME);

        $query = "UPDATE msds_files " .
                "SET product_id = " . $productID . " " .
                "WHERE msds_file_id = " . $msdsSheetID;

        $this->db->query($query);
    }

    private function VPSLimitsExceed($fileSize, $companyID) {
        $error = "";
        if ($companyID === null) {
            $user = new User($this->db);
            $userDetails = $user->getUserDetails($_SESSION['user_id'], true);
            $companyID = ($userDetails['accesslevel_id'] == 3) ? 0 : $userDetails['company_id'];
        }

        if ($companyID != 0) {
            $voc2vps = new VOC2VPS($this->db);
            $customerLimits = $voc2vps->getCustomerLimits($companyID);

            //MSDS check
            if ($customerLimits['MSDS']['max_value'] < $customerLimits['MSDS']['current_value'] + 1) {
                $error .= "You cannot add new MSDS according to your Billing Plan (count limit exceeds)<br>";
            }
            //memory check
            $sizeMb = round($fileSize / 1024 / 1024, 2);
            if ($customerLimits['memory']['max_value'] < $customerLimits['memory']['current_value'] + $sizeMb) {
                $error .= "You cannot add new MSDS according to your Billing Plan (memory limit exceeds)<br>";
            }
        }

        return ($error === "") ? false : $error;
    }

}
?>