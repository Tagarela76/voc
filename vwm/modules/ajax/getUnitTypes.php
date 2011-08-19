<?php
	
	chdir('../..');	

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	$xnyo = new Xnyo;
	
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	
	$xnyo->start();
	
	$xnyo->filter_get_var('sysType', 'text');
	$xnyo->filter_get_var('companyID', 'text');
	$xnyo->filter_get_var('companyEx', 'text');
	
	$sysType = $_GET['sysType'];
	$companyID = $_GET['companyID'];
	$companyEx = $_GET['companyEx'];
	
	$db->select_db(DB_NAME);
        
        //this function change unittype if CompanyEx
        function setsql_1($sysType, $companyID){
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
            $queryData = "SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
			  "WHERE ut.type_id = t.type_id " .
			  "AND ut.system = {$sqlSystem} " .
			  "AND t.type_desc in {$sqlTypedesc} " .
			  "AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype')".
			  "ORDER BY ut.unittype_id";
                  return $queryData;
        }
        
        //this function change unittype if not CompanyEx
        function setsql_2($sysType, $companyID){
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
            $queryData = "SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
			  "WHERE ut.type_id = t.type_id " .
			  "AND ut.system = {$sqlSystem} " .
			  "AND t.type_desc in {$sqlTypedesc} " .
			  "ORDER BY ut.unittype_id";
                  return $queryData;           
        }

	/*switch ($sysType){
		//case 'USAVlm':
		//	$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
		//			"WHERE ut.type_id = t.type_id " .
		//			"AND ut.system = 'USA' " .
		//			"AND t.type_desc = 'Volume'";	
		//	break;
		case 'USALiquid':
			if ($companyEx) {
				$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc in ('Volume Liquid', 'Volume') " .
					"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype')".
					"ORDER BY ut.unittype_id";	
			}
			else {
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc in ('Volume Liquid','Volume') " .
					"ORDER BY ut.unittype_id";
			}
			break;
		case 'USADry':
			if ($companyEx) {
				$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc = 'Volume Dry' " .
					"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype')".
					"ORDER BY ut.unittype_id";
			}
			else {
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc = 'Volume Dry' " .
					"ORDER BY ut.unittype_id";
			}	
			break;
		case 'USAWght':
			if ($companyEx) {
				$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc = 'Weight' " .
					"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype')".
					"ORDER BY ut.unittype_id";
			}
			else {
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc = 'Weight' " .
					"ORDER BY ut.unittype_id";
			}	
			break;
		case 'MetricVlm':
			if ($companyEx) {		
				$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Volume' " .
					"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype') ".
					"ORDER BY ut.unittype_id";
			}
			else {
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Volume' " .
					"ORDER BY ut.unittype_id";
			}
			break;
		case 'MetricWght':
			if ($companyEx) {
				$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Weight' " .
					"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."' AND d.subject='unittype')".
					"ORDER BY ut.unittype_id";
			}
			else {
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Weight' " .
					"ORDER BY ut.unittype_id";
			}	
			break;
	}*/

        if ($companyEx){
            $query = setsql_1($sysType, $companyID);
        }else{
            $query = setsql_2($sysType, $companyID);
        }
        
	$db->query($query);
	if($db->num_rows() == 0)
	{
            $query = setsql_2($sysType, $companyID);	
            $db->query($query);
	}
        
        $data=$db->fetch_all_array();
        echo json_encode($data);	
?>
