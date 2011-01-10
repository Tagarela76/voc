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
$db->select_db(DB_NAME);
 
$xnyo->filter_post_var('action', 'text');
$wasteStreamsObj=new WasteStreams($db);

$action = $_POST['action'];

switch ($action)
{
	case 'selectWasteStreams':
	{
		$wasteStreamsList= $wasteStreamsObj->getWasteStreamsFullList();		
		echo json_encode($wasteStreamsList);
		break;	
	}
	
	case 'selectPollutions':
	{
		$xnyo->filter_post_var('wasteStream', 'text');
		$wasteStreamId=$_POST['wasteStream'];
		$pollutions=$wasteStreamsObj->getPolutionList($wasteStreamId);		
		echo json_encode($pollutions);
		break;
	}
	
	case 'WasteStreamToPollutionList':
	{
		$WasteStreamToPollutionList=$wasteStreamsObj->getWasteStreamsToPollutionsList();
		echo json_encode($WasteStreamToPollutionList);
		break;
	}
	
	case 'unittypeList':
	{
		$xnyo->filter_post_var('selectedClassValue', 'text');
		$xnyo->filter_post_var('companyId', 'text');
		$xnyo->filter_post_var('companyEx', 'text');
		$sysType = $_POST['selectedClassValue'];
		$companyID=$_POST['companyId'];
		$companyEx=$_POST['companyEx'];
		
		switch ($sysType){
			
			case 'USALiquid':
				if ($companyEx) {
					$query="SELECT ut.unittype_id, ut.name FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
						"WHERE ut.type_id = t.type_id " .
						"AND ut.system = 'USA' " .
						"AND t.type_desc in ('Volume Liquid', 'Volume') " .
						"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."')".
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
						"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."')".
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
						"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."')".
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
						"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."') ".
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
						"AND ut.unittype_id IN (SELECT d.id_of_subject FROM ".TB_DEFAULT." d WHERE d.id_of_object='".$companyID."')".
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
		}
		$db->query($query);		
		$data=$db->fetch_all();
		echo json_encode($data);
		break;	
	}
}


?>
