<?php
	
	chdir('../..');		
	
	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');
		
	$site_path = getcwd().DIRECTORY_SEPARATOR; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');
	
	/*$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();*/
	
	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php');
	$db->select_db(DB_NAME);
	
	require ('modules/xnyo/smarty/startSmartyAdmin.php');
	
	//	filter action var	

	$xnyo->filter_post_var('action', 'text');

	$action = $_REQUEST['action']; 
		
	//	logged in?	
	$user = new User($db, $xnyo, $access, $auth);
	if (!$user->isLoggedIn()) {
		die("user not logined");
	}	
			
	
	
	function showConfirmRollback($smarty, array $parentTrashRecord, array $dependencies = array()) {
		$smarty->assign("trashRecordLabel",$parentTrashRecord['type']." ".$parentTrashRecord['item']." ".$parentTrashRecord['itemName']);
		$smarty->assign("parentTrashRecord",$parentTrashRecord);
		$smarty->assign("trashRecords", $dependencies);		
		$smarty->display("tpls:tpls/track/confirm.tpl");
	}
	
	
	
			
	switch ($action) {		
		case "rollback":
			$xnyo->filter_post_var('id','int');
			
			$integrityError = false;
			$juniorTrashError = false;
			
			$trackManager = new TrackManager($db);
			$trashRecord = TrackingSystem::getTrashByID($db, $_POST['id']);			
			switch ($trashRecord->getCRUD()) {
				case "C":					    							
    				//$dependedTrashRecords = $trashRecord->getDependencies('backUnlinked');    					    					
    				//$dependedTrashRecords = $trashRecord->getDependencies('directback');
    				$youngerRecords = $trashRecord->areJuniorTrashesSet();
    				if ($youngerRecords['result']) {
    					$dependedTrashRecords = $youngerRecords['trashRecords'];
    					$juniorTrashError = true;
    				} else {
	    				$dependedTrashRecords = $trashRecord->getDependencies(TrackingSystem::ALL_DEPENDENCIES,'directback');	    				   
    				}
    				break;
    			case "U":
    				$youngerRecords = $trashRecord->areJuniorTrashesSet();
    				if ($youngerRecords['result']) {
    					$dependedTrashRecords = $youngerRecords['trashRecords'];
    					$juniorTrashError = true;
    				} else {
	    				$validation = $trashRecord->validateIntegrity();
	    				if (!$validation['result']) {    					
		    				$dependedTrashRecords = $validation['records'];
		    				$integrityError = true;    					
	    				} else {
		    				$dependedTrashRecords = $trashRecord->getDependencies(TrackingSystem::ALL_DEPENDENCIES, 'back');
	    				}    
    				}   				    								     				    								    				  	   
    				break;
    			case "D":
    				$validation = $trashRecord->validateIntegrity();
    				if (!$validation['result']) {
    					$dependedTrashRecords = $validation['records'];
    					$integrityError = true;
    				} else {
    					$dependedTrashRecords = $trashRecord->getDependencies(TrackingSystem::ALL_DEPENDENCIES, 'back');	
    				}    							    				
    				break;
			}			
			
			//	make trash records user friendly			
			$friendlyRecord = $trackManager->makeUserFriendly($trashRecord, $user);
			$friendlyDependedTrashRecords = array();			
			foreach($dependedTrashRecords as $dependedTrashRecord) {	
				$friendlyDependedTrashRecord = $trackManager->makeUserFriendly($dependedTrashRecord, $user);
				//	do not show system tables (like mixgroup)
				if (!empty($friendlyDependedTrashRecord['itemName'])) {
					$friendlyDependedTrashRecords[] = $friendlyDependedTrashRecord;
				}				 				
			}									
			
			if (count($friendlyDependedTrashRecords) == 0) {
				//	no dependencies. Just confirm
				showConfirmRollback($smarty, $friendlyRecord);				
			} else {
				
				//showConfirmRollback($smarty, $friendlyRecord);	
				
				$smarty->assign("showDependencies", true);
				$smarty->assign("trashRecordLabel",$friendlyRecord['type']." ".$friendlyRecord['item']." ".$friendlyRecord['itemName']);
				$smarty->assign("trashRecords",$friendlyDependedTrashRecords);
				$smarty->assign("integrityError",$integrityError);
				$smarty->assign("juniorTrashError",$juniorTrashError);
				
					
				$smarty->display("tpls:tpls/track/index.tpl");
			}				
			break;
			
			
			
		case "confirm":
			$xnyo->filter_post_var('id','int');
			$trackManager = new TrackManager($db);
			$trashRecord = TrackingSystem::getTrashByID($db, $_POST['id']);	  			
			$friendlyRecord = $trackManager->makeUserFriendly($trashRecord, $user);
			showConfirmRollback($smarty, $friendlyRecord);
			break;
			
			
			
		case "rollbackConfirmed":
			$xnyo->filter_post_var('id','int');
			$trackManager = new TrackManager($db);
			$trashRecord = TrackingSystem::getTrashByID($db, $_POST['id']);
			if ($trackManager->rollback($trashRecord)) {
				//	sync bridge
    			$bridge = new Bridge($db);    		
				$bridge->CopyAllCustomersToBridge();
				$bridge->CopyAllUsersToBridge();
				
				echo json_encode(array('result'=>true));
			} else {
				echo json_encode(array('result'=>false));
			}	
			break;
			
		case "searchTpl":		
			$smarty->display("tpls:tpls/track/search.tpl");
			break;				
				
		case "searchTpl":		
			$smarty->display("tpls:tpls/track/search.tpl");
			break;				
				
		case "index":		
			
			$xnyo->filter_post_var('searchText', 'text');
			
			$searchText = $_REQUEST['searchText'];
			
			
			$trackManager = new TrackManager($db);
			$trashRecords = $trackManager->getTrackList($searchText);	
			
			//	make trash records user friendly 	
			foreach($trashRecords as $trashRecord) {
				$friendlyRecords[] = $trackManager->makeUserFriendly($trashRecord, $user);				
			}
			
				
			$smarty->assign("trashRecords",$friendlyRecords);			
			$smarty->display("tpls:tpls/track/index.tpl");
			break;
		
	}
?>