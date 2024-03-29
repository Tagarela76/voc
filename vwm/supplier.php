<?php
require('config/constants.php');

define ('DIRSEP', DIRECTORY_SEPARATOR);

$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP;
define ('site_path', $site_path);

require $site_path.'../vendor/autoload.php';

//	Start xnyo Framework
require ('modules/xnyo/startXnyo.php');

$xnyo->load_plugin('auth');
$xnyo->logout_redirect_url='supplier.php';

require ('modules/xnyo/smarty/startSmartySupplier.php');
//var_dump($smarty);
$db->select_db(DB_NAME);

//	deny access to system while updating jobs
if (MAINTENANCE) {
	$smarty->display('tpls:errors/maintenance.tpl');
	die();
}

try {
	//	Load localization file
	$sl = new SL(REGION, $db);
// USER LOGGING
if (isset($_SESSION['user_id'])){
	$loggingManager = new UserLoggingManager($db);
	$loggingManager->MakeLog($_GET, $_POST, $_SESSION['user_id']);
}
//
	if (!isset($_GET["action"])) {

		if (isset($_POST["action"])) {

			$user = new User($db, $xnyo, $access, $auth);

			if ((!($user->isLoggedIn()) || $user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 5 || $user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 3) && $_POST["action"] != 'auth') {
				header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
			}
			switch ($_POST["action"]) {

				case 'auth':
					$xnyo->filter_post_var('accessname', 'text');
					$xnyo->filter_post_var('password', 'text');

					$accessLevel=$user->getUserAccessLevelIDByAccessname($_POST["accessname"]);

					if ($user->auth($_POST["accessname"], $_POST["password"]) && ($accessLevel==5 || $accessLevel==3)) {
						if ($access->check('required')) {
							//	authorized
							session_start();
							$_SESSION['user_id'] = $user->getUserIDbyAccessname($_POST["accessname"]);
							$_SESSION['accessname'] = $_POST['accessname'];
							$_SESSION['username'] = $user->getUsernamebyAccessname($_POST["accessname"]);
							$accessLevelName = $user->getUserAccessLevel($_SESSION['user_id']);
							if ($accessLevel==5){
								$inventoryManager = new InventoryManager($db);
								$jobberID = $inventoryManager->getSaleUserJobberID($_SESSION['user_id']);
								$supplierIDS = $inventoryManager->getSuppliersByJobberID($jobberID['jobber_id']);

							}
							$smarty->assign("accessName", $accessLevelName);
							switch ($accessLevelName) {
								case "SuperuserLevel":
									header("Location: supplier.php?action=browseCategory&category=root");
								break;
								default:
									header("Location: supplier.php?action=browseCategory&category=sales&bookmark=clients&jobberID={$jobberID['jobber_id']}&supplierID={$supplierIDS[0]['supplier_id']}");
								break;
							}

						} else {
							//	not authorized
							header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
						}
					} else {
						//echo "Authorization failed!<br>";
						header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
					}
					break;
			}
		} else {
			$smarty->display('tpls:supplierLogin.tpl');
		}
	} else {

		$smarty->assign("action", $_GET["action"]);
		$user=new User($db, $xnyo, $access, $auth);

		if (!($user->isLoggedIn()) || ($user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 5 && $user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 3)) {

			header ('Location: '.$xnyo->logout_redirect_url.'?error=timeout');
		}

		//TODO validate next:
		if (isset($_GET['action'])) {
			$action=$_GET['action'];
		} else {
			$action='main';
		}


		$smarty->assign("accessname", $xnyo->user['username']);

		if (isset($_GET['category']) || isset($_POST['itemID']))
		{
			//if(isset($_GET['category']))
				$className="CSup".ucfirst($_GET['category']);
			//else
			//	$className="C".ucfirst($_POST['itemID']);//???

			if (class_exists($className))
			{
				$controllerObj=new $className($smarty,$xnyo,$db,$user,$action);
				$controllerObj->runAction();
			}
			else
				throw new Exception('404');
		} elseif ($_GET["action"] == 'vps') {
			$smarty->assign('parent','vps');
			require ('vps_admin.php');
		} else {
			$controllerObj=new CSupCommon($smarty,$xnyo,$db,$user,$action);
			$controllerObj->runAction();
		}
	}
} catch (Exception $e) {
	switch ($e->getMessage()) {
		case '404':
			$smarty->display('tpls:errors/404.tpl');
			break;
		case 'deny':
			$smarty->display('tpls:errors/deny.tpl');
			break;
		default:
			$smarty->assign('message', $e->getMessage());
		$smarty->display('tpls:errors/other.tpl');
	}
}
?>