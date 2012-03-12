<?php
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
	require_once('modules/Reform.inc.php');
		
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	
	$xnyo->filter_vars = false;
	
	$xnyo->start();
	
	$db->select_db(DB_NAME);
	
	//	filter action var	
	$xnyo->filter_post_var('action', 'text');
	$action = $_POST['action'];
	
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die();
	}
	
$jobberID = $_POST['id'];

$jobbweManager = new JobberManager($db);

$supplierList = $jobbweManager->getJobbersSupplierList($jobberID);

if ($supplierList){
	foreach($supplierList as $supplierID){
		$result = $jobbweManager->getMoreJobbersWithSameSupplierID($supplierID['supplier_id'],$jobberID);

		if ($result){
			foreach($result as $id){			
				$moreJobbers[] = $id['jobber_id'];
			}
		}
		
	}
}

if ($moreJobbers){
	$jobbers2remove = $moreJobbers;
}else{
	$jobbers2remove = false;
}

echo json_encode($jobbers2remove);		

?>
