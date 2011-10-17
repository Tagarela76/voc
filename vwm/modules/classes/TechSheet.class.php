<?php

class TechSheet {
    /**
     *
     * @var db
     */
    private $db;
    public $totalNumberOfFiles = 5;

    function TechSheet($db) {
        $this->db = $db;
    }

    public function upload($type, $companyID = null) {
        //	upload
        $uploads_dir = "../tech_sheet";
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
                    //$VPSError = $this->VPSLimitsExceed($size, $companyID);
                    //if (!$VPSError) {
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

                            $techSheetName['real_name'] = $currentFile['real_name'];
                            $techSheetName['name'] = $currentFile['name'];
                            $techSheetName['size'] = $size;
                            $techSheetNames[] = $techSheetName;
                        } else {
                            $fileWithError['name'] = $_FILES["inputFile"]["name"][$key];
                            $fileWithError['error'] = "Only .pdf and .doc files!";
                            $filesWithError[] = $fileWithError;
                        }
                   /* } else {
                        $fileWithError['name'] = $_FILES["inputFile"]["name"][$key];
                        $fileWithError['error'] = $VPSError;
                        $filesWithError[] = $fileWithError;
                    }*/
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

                        $techSheetName['real_name'] = $currentFile['real_name'];
                        $techSheetName['name'] = $currentFile['name'];
                        $techSheetName['size'] = $_FILES["Filedata"]["size"];
                        $techSheetNames[] = $techSheetName;
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
        $techSheetResult = $this->recognize($techSheetNames);

        $output['techSheetResult'] = $techSheetResult;
        $output['filesWithError'] = $filesWithError;

        return $output;
    }

    public function assign() {
        
    }

    public function show() {
        
    }

    private function recognize($techSheetNames) {
        foreach ($techSheetNames as $techSheet) {
            $techSheetProduct["name"] = $techSheet["name"];
            $techSheetProduct["real_name"] = $techSheet["real_name"];
            $techSheetProduct["size"] = $techSheet["size"];

            //	Recognize product for MSDS name
            $techSheetProduct["product_id"] = $this->getProductIDByTechSheet($techSheetProduct["name"]);

            if ($techSheetProduct["product_id"] == "") {
                $techSheetProduct["isRecognized"] = false;
            } else {
                $techSheetProduct["isRecognized"] = true;
            }

            //	Put MSDS-Product into result
            $techSheetProducts[] = $techSheetProduct;
        }

        return $techSheetProducts;
    }

    private function getProductIDByTechSheet($techSheetName) {
        $techSheetName = $this->cutFileExtension($techSheetName);

        //$this->db->select_db(DB_NAME);

        $query = "SELECT product_id, product_nr FROM " . TB_PRODUCT . " WHERE 1";
        $this->db->query($query);

        $maxRank = 0;
        $productID = "";
        foreach ($this->db->fetch_all() as $product) {
            $productRank = $this->getProductRank($product->product_nr, $techSheetName);

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

    private function getProductRank($productNR, $techSheetName) {
        $techSheetName = strtolower(trim($techSheetName));
        $productNR = strtolower(trim($productNR));

        if ($techSheetName == $productNR) {
            return 1;
        } elseif (str_replace(" ", "", $techSheetName) == str_replace(" ", "", $productNR)) {
            return 0.9;
        } elseif (str_replace(array("-", " "), "", $techSheetName) == str_replace(array("-", " "), "", $productNR)) {
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
            $result["techSheetName"] = $assignment["techSheetName"];
            $result["productID"] = $assignment["productID"];

            //	Check if product is already assigned by MSDS
            //	WARNING! - COMPANY ID SEPARATOR NOT IMPLEMENTED YET
            if ($this->isAlreadyAssigned($assignment["productID"])) {
                $result["status"] = "failed";
                $result["reason"] = "alreadyAssigned";
                $result["assignedTechSheet"] = $this->getAssignedTechSheet($assignment["productID"]);
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

    private function getAssignedTechSheet($productID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT tech_sheet_file_id FROM " . TB_TECH_SHEET_FILE . " WHERE product_id=" . $productID . " LIMIT 1";
        $this->db->query($query);

        if ($this->db->num_rows()) {
            return $this->db->fetch(0)->tech_sheet_file_id;
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


		
        foreach ($sheets["techSheets"] as $techSheet) {
            if (empty($techSheet["productID"])) {
                $techSheet["productID"] = "NULL";
            }



            $query = "INSERT INTO " . TB_TECH_SHEET_FILE . " (name, real_name, company_id, facility_id, department_id, product_id) VALUES ("
                    . "'" . $techSheet["name"] . "'"
                    . ", '" . $techSheet["real_name"] . "'"
                    . ", $companyID"
                    . ", $facilityID"
                    . ", $departmentID"
                    . ", " . $techSheet["productID"]
                    . ")";
			
            $this->db->query($query);
        }
    }

    //	WARNING! NOT DEBUGGED
    public function updateSheet($techSheet) {
        $this->db->select_DB(DB_NAME);

        $query = "UPDATE " . TB_TECH_SHEET_FILE . " SET "
                . "name='" . $techSheet["name"] . "'"
                . ", real_name='" . $techSheet["real_name"] . "'"
                . ", company_id=" . $techSheet["companyID"]
                . ", facility_id=" . $techSheet["facilityID"]
                . ", department_id=" . $techSheet["departmentID"]
                . ", product_id=" . $techSheet["productID"]
                . " WHERE tech_sheet_file_id=" . $techSheet["techSheetFileID"];

        $this->db->query($query);
    }

    public function getSheetByProduct($productID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT tech_sheet_file_id as 'id',name,real_name as 'realName', company_id as 'companyID', facility_id as 'facilityID', department_id as 'departmentID', product_id as 'productID' " .
                "FROM tech_sheet_files " .
                "WHERE product_id = " . $productID;

        $this->db->query($query);

        if ($this->db->num_rows()) {
            $techSheetSheet = $this->db->fetch_array(0);

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

        return $techSheetSheet;
    }

    public function unlinkTechSheet($techSheetID) {
        //$this->db->select_db(DB_NAME);

        $query = "UPDATE tech_sheet_files " .
                "SET product_id = NULL " .
                "WHERE tech_sheet_file_id = " . $techSheetID;
        $this->db->query($query);
    }

    public function getSheetDetails($techSheetID) {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT tech_sheet_file_id as 'id', name, real_name as 'realName', company_id as 'companyID', facility_id as 'facilityID', department_id as 'departmentID', product_id as 'productID' " .
                "FROM tech_sheet_files " .
                "WHERE tech_sheet_file_id = " . $techSheetID;

        $this->db->query($query);

        if ($this->db->num_rows()) {
            $techSheet = $this->db->fetch_array(0);

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

        return $techSheet;
    }

    public function getUnlinkedTechSheets() {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT * " .
                "FROM tech_sheet_files " .
                "WHERE product_id is NULL";

        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);

                $techSheet = array(
                    'id' => $data->tech_sheet_file_id,
                    'name' => $data->name,
                    'realName' => $data->real_name,
                    'companyID' => $data->company_id,
                    'facilityID' => $data->facility_id,
                    'departmentID' => $data->department_id,
                    'productID' => $data->product_id
                );
                $techSheet["techSheetLink"] = "../tech_sheet/" . $data->real_name;
                $techSheets[] = $techSheet;
            }
        }

        return $techSheets;
    }

    public function linkSheetToProduct($techSheetID, $productID) {
        //$this->db->select_db(DB_NAME);

        $query = "UPDATE tech_sheet_files " .
                "SET product_id = " . $productID . " " .
                "WHERE tech_sheet_file_id = " . $techSheetID;

        $this->db->query($query);
    }

}
?>