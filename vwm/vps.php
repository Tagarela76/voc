<?php

/* * *
 * VWM VPS - VOC Payment System.
 * Powered by kttsoft.com
 */
define("USERS_TABLE", "vps_user");


require('config/constants.php');

define('DIRSEP', DIRECTORY_SEPARATOR);
$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP;
define('site_path', $site_path);

//	Include Class Autoloader
require $site_path.'../vendor/autoload.php';

//	Start xnyo Framework
require ('modules/xnyo/startXnyo.php');

//	Start Smarty templates engine
require ('modules/xnyo/smarty/startSmartyVPS.php');
//12345678901234567890123456789012345678901234567890123456789012345678901234567890
//	deny access to system while updating jobs
if (MAINTENANCE) {
	$smarty->display('tpls:errors/maintenance.tpl');
	die();
}

$db->select_db(DB_NAME);


$xnyo->load_plugin('auth');
$xnyo->logout_redirect_url = "vps.php";

$xnyo->filter_get_var('action', 'text');
$xnyo->filter_post_var('action', 'text');


VOCApp::getInstance()->setDB($db);

$smarty->assign("VOCApp_instance", VOCApp::getInstance());

try {



	//$user = new VPSUser($db, $auth, $access, $xnyo);
	$user = new VPSUser($db, $auth, $access, $xnyo);
	//Admin login
	//	Load localization file
	$sl = new SL(REGION, $db);



	if (!isset($_GET["action"])) {

		if (isset($_POST["action"]) && $_POST["action"] == "auth") {
			//	Try to authorize
			$xnyo->filter_post_var("username", "text");
			$xnyo->filter_post_var("password", "text");

			$username = $_POST["username"];
			$password = $_POST["password"];

			$authResult = $user->authorize($username, $password);

			if ($authResult) {

				if ($authResult["showAddUser"]) {

					//new user registration flag
					$newUserRegistration = true;
					$smarty->assign("newUserRegistration", $newUserRegistration);

					$_SESSION['registration'] = true;
					$_SESSION['originalPassword'] = $password;
					//showMyInfo($db,$smarty,$authResult);
					//show my info
					//include_once 'CVCommon.php';
					$controller = new CVCommon($smarty, $xnyo, $db, $user, $action);
					$controller->showMyInfo($authResult);
				} else {
					//	Redirect user to dashboard
					session_start();

					$user_id = $user->getUserIDbyAccessname($username);
					$_SESSION['userID'] = $user_id;
					$_SESSION['accessname'] = $username;
					$accessLevel = $user->getUserAccessLevel($_SESSION['userID']);
					$_SESSION['accessLevel'] = $accessLevel;
					$customer_id = $user->getCustomerIDbyUserID($_SESSION['userID']);
					$_SESSION['customerID'] = $customer_id;
					$_SESSION["username"] = strtoupper($username);

					VOCApp::getInstance()->setUserID($user_id);
					VOCApp::getInstance()->setCustomerID($customer_id);

					if ($_POST['backUrl']) {
						$backUrl = $_POST['backUrl'];
					} else {
						$backUrl = "vps.php?action=viewDetails&category=dashboard";
					}
					header("Location: $backUrl");
				}
			} else {
				$smarty->assign("status", "fail");

				$smarty->display("tpls:authorization.tpl");
			}
		} else {
			if ($user->isLoggedIn()) {
				$accessLevel = $user->getUserAccessLevel($_SESSION['user_id']);
				$_SESSION['accessLevel'] = $accessLevel;
				$userDetails = $user->getUserDetails($_SESSION['user_id']);
				$customer_id = $userDetails['company_id'];

				//$customer_id
				$_SESSION['customerID'] = $customer_id;

				VOCApp::getInstance()->setUserID($_SESSION['user_id']);
				VOCApp::getInstance()->setCustomerID($customer_id);

				header("Location: vps.php?action=viewDetails&category=dashboard");
			} else {
				//	Show login page
				if (isset($_GET['backUrl'])) {
					$smarty->assign("backUrl", urldecode($_GET['backUrl']));
				}
				$smarty->display("tpls:authorization.tpl");
			}
		}
	} else {
		$userID = $_SESSION['userID'];
		$customerID = $_SESSION['customerID'];



		VOCApp::getInstance()->setUserID($userID);
		VOCApp::getInstance()->setCustomerID($customerID);
		VOCApp::getInstance()->getDateFormat();

		//	logged in?
		if ((!($user->isLoggedIn() && $_SESSION['auth']['company_id'] == $customerID) && !$_SESSION['registration'] && isset($_GET["category"]) && $_GET["category"] != 'scriptNewBilling')) {
			$backUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$backUrl = urlencode($backUrl);
			header("Location: vps.php?backUrl=$backUrl");
		}

		if (!$_SESSION['registration']) {
			$vps2voc = new VPS2VOC($db);
			$customerDetails = $vps2voc->getCustomerDetails($customerID);
			$isTrial = (strtotime($customerDetails['trial_end_date']) >= strtotime(date('Y-m-d'))) ? true : false;
			$smarty->assign("isTrial", $isTrial);
		}
		$smarty->assign("accessname", strtoupper($_SESSION['accessname']));

		//echo 'accessname: ' .$_SESSION['accessname'];


		/**
		 * Start Controllers
		 */
		//TODO validate next:
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		} else {
			$action = 'main';
		}


		//$smarty->assign("accessname", $xnyo->user['username']);


		if (isset($_GET['category']) || isset($_POST['itemID'])) {
			//if(isset($_GET['category']))
			$className = "CV" . ucfirst($_GET['category']);
			//else
			//	$className="C".ucfirst($_POST['itemID']);//???
			//echo $className."<br/>";

			if (class_exists($className)) {
				$controllerObj = new $className($smarty, $xnyo, $db, $user, $action);
				$controllerObj->runAction();
			} else {
				throw new Exception('404');
			}
		} elseif ($_GET["action"] == 'vps') {
			$smarty->assign('parent', 'vps');
			require ('vps_admin.php');
		} else {
			$controllerObj = new CACommon($smarty, $xnyo, $db, $user, $action);
			$controllerObj->runAction();
		}


		/**
		 * End Controllers
		 */
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
	throw $e;
}
?>