<?php

chdir('../..');

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');


$site_path = getcwd() . DIRECTORY_SEPARATOR;
define('site_path', $site_path);

//	Include Class Autoloader
require_once('modules/classAutoloader.php');

$xnyo = new Xnyo;

$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;

$xnyo->start();
$db->select_db(DB_NAME);

$xnyo->filter_post_var('action', 'text');
$wasteStreamsObj = new WasteStreams($db);

$action = $_POST['action'];

switch ($action) {
    case 'selectWasteStreams': {
            $wasteStreamsList = $wasteStreamsObj->getWasteStreamsFullList();
            echo json_encode($wasteStreamsList);
            break;
        }

    case 'selectPollutions': {
            $xnyo->filter_post_var('wasteStream', 'text');
            $wasteStreamId = $_POST['wasteStream'];
            $pollutions = $wasteStreamsObj->getPolutionList($wasteStreamId);
            echo json_encode($pollutions);
            break;
        }

    case 'WasteStreamToPollutionList': {
            $WasteStreamToPollutionList = $wasteStreamsObj->getWasteStreamsToPollutionsList();
            echo json_encode($WasteStreamToPollutionList);
            break;
        }

    case 'unittypeList': {
            $xnyo->filter_post_var('selectedClassValue', 'text');
            $xnyo->filter_post_var('companyId', 'text');
            $xnyo->filter_post_var('companyEx', 'text');
            $sysType = $_POST['selectedClassValue'];
            $companyID = $_POST['companyId'];
            $companyEx = $_POST['companyEx'];

            //this function change unittype if CompanyEx
            function setsql_1($sysType, $companyID) {
                switch ($sysType) {
                    case 'USALiquid':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Volume Liquid', 'Volume')";
                        break;
                    case 'USADry':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Volume Dry')";
                        break;
                    case 'USAWght':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Weight')";
                        break;
                    case 'MetricVlm':
                        $sqlSystem = "'metric'";
                        $sqlTypedesc = "('Volume')";
                        break;
                    case 'MetricWght':
                        $sqlSystem = "'metric'";
                        $sqlTypedesc = "('Weight')";
                        break;
                }
                $queryData = "SELECT ut.unittype_id, ut.name FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = {$sqlSystem} " .
                        "AND t.type_desc in {$sqlTypedesc} " .
                        "AND ut.unittype_id IN (SELECT d.id_of_subject FROM " . TB_DEFAULT . " d WHERE d.id_of_object='" . $companyID . "' AND d.subject='unittype')" .
                        "ORDER BY ut.unittype_id";
                return $queryData;
            }

            //this function change unittype if not CompanyEx
            function setsql_2($sysType, $companyID) {
                switch ($sysType) {
                    case 'USALiquid':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Volume Liquid', 'Volume')";
                        break;
                    case 'USADry':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Volume Dry')";
                        break;
                    case 'USAWght':
                        $sqlSystem = "'USA'";
                        $sqlTypedesc = "('Weight')";
                        break;
                    case 'MetricVlm':
                        $sqlSystem = "'metric'";
                        $sqlTypedesc = "('Volume')";
                        break;
                    case 'MetricWght':
                        $sqlSystem = "'metric'";
                        $sqlTypedesc = "('Weight')";
                        break;
                }
                $queryData = "SELECT ut.unittype_id, ut.name FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = {$sqlSystem} " .
                        "AND t.type_desc in {$sqlTypedesc} " .
                        "ORDER BY ut.unittype_id";
                return $queryData;
            }

            if ($companyEx) {
                $query = setsql_1($sysType, $companyID);
            } else {
                $query = setsql_2($sysType, $companyID);
            }

            $db->query($query);
            if ($db->num_rows() == 0) {
                $query = setsql_2($sysType, $companyID);
                $db->query($query);
            }
            $data = $db->fetch_all();
            echo json_encode($data);
            break;
        }
}
?>
