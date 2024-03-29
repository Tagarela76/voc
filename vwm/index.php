<?php

define("USERS_TABLE", "user");

require('config/constants.php');

define('DIRSEP', DIRECTORY_SEPARATOR);
$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP;
define('site_path', $site_path);

//	Include Class Autoloader
require $site_path.'../vendor/autoload.php';

//	Start xnyo Framework
require ('modules/xnyo/startXnyo.php');
//v
//	Start Smarty templates engine
require ('modules/xnyo/smarty/startSmarty.php');

require_once('modules/lib/Reform.inc.php');

$queryStr = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

$xnyo->load_plugin('auth');

$xnyo->logout_redirect_url = '../voc_web_manager.html';

$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();

//  Start VOC app
VOCApp::getInstance()->setDB($db);
VOCApp::getInstance()->setSmarty($smarty);
VOCApp::getInstance()->setEventDispatcher($eventDispatcher);

$xnyo->filter_get_var('action', 'text');
$xnyo->filter_post_var('action', 'text');


//	deny access to system while updating jobs
if (MAINTENANCE) {
	$smarty->display('tpls:errors/maintenance.tpl');
	die();
}
$db->select_db(DB_NAME);

$stats = new Statistics($db);
if ($stats->validate()) {
	$stats->save();
}

try {
	//	Load localization file
	$sl = new SL(REGION, $db);
	// USER LOGGING
	if (isset($_SESSION['user_id'])) {
		$loggingManager = new UserLoggingManager($db);
		$loggingManager->MakeLog($_GET, $_POST, $_SESSION['user_id']);

		//	also update all needed info for VOCApp
		VOCApp::getInstance()->setUserID($_SESSION['user_id']);
		$companyID = ($_SESSION['auth']['company_id']) ? $_SESSION['auth']['company_id'] : 0;	//	0 is for SuperUser
		VOCApp::getInstance()->setCustomerID($companyID);
	}

	if (!isset($_GET["action"])) {
		if (isset($_POST["action"])) {
			$smarty->assign("action", $_POST["action"]);
			$xnyo->filter_post_var('url', 'text');
			$queryStrPost = $_POST["url"];
			for ($l = 0; $l < strlen($queryStrPost); $l++) {
				if ($queryStrPost[$l] == '!')
					$queryStrPost[$l] = '&';
			}

			$user = new User($db, $xnyo, $access, $auth);
            VOCApp::getInstance()->setUser($user);


			if (!($user->isLoggedIn()) && !($_POST["action"] == 'auth' || $_POST["action"] == "msdsUploaderMain")) {
				echo 'Location ' . $xnyo->logout_redirect_url;
				header('Location: ' . $xnyo->logout_redirect_url . '?error=timeout');
			}

			switch ($_POST["action"]) {
				//	AUTH HERE!
				case 'auth':
					$xnyo->filter_post_var('accessname', 'text');
					$xnyo->filter_post_var('password', 'text');

					//$db->select_db(DB_NAME);
					if ($user->auth($_POST["accessname"], $_POST["password"])) {
						$userDetails = $user->getUserDetails($user->getUserIDbyAccessname($_POST["accessname"]), true);
						//	disable vps check
						/*if ($userDetails['accesslevel_id'] != 3) {
							$voc2vps = new VOC2VPS($db);
							$customerDetails = $voc2vps->getCustomerDetails($userDetails['company_id'], true);

							if (VERSION != 'standalone' &&
									($customerDetails['status'] == "off" || $customerDetails['status'] == "notReg") &&
									(strtotime($customerDetails['trial_end_date']) <= strtotime(date('Y-m-d')))) {

								for ($l = 0; $l < strlen($queryStrPost); $l++) {
									if ($queryStrPost[$l] == '&')
										$queryStrPost[$l] = '!';
								}
								header('Location: ' . $xnyo->logout_redirect_url . '?error=auth&url=' . $queryStrPost);
								break;
							}
						}*/

						session_start();

						$_SESSION['user_id'] = $user->getUserIDbyAccessname($_POST["accessname"]);
						$_SESSION['accessname'] = $_POST['accessname'];
						$_SESSION['username'] = $user->getUsernamebyAccessname($_POST["accessname"]);
						$accessLevel = $user->getUserAccessLevel($_SESSION['user_id']);
						$_SESSION['accessLevel'] = $accessLevel;

						//	Set Notify
						$notify = new Notify($smarty);
						$notify->loginSuccess($accessLevel);

						//save time of user login for email notifications
						$emailNotifications = new EmailNotifications($db);
						$emailNotifications->saveTime('login', $_SESSION['user_id']);


						//	E-mail notification about successful logining
						if (ENVIRONMENT == "server") {
							$email = new EMail();

							$to = array("dmitry.vd@kttsoft.com",
								"denis.nt@kttsoft.com"
							);

							//$from = "authentification@vocwebmanager.com";
							$from = AUTH_SENDER . "@" . DOMAIN;

							$theme = "Success authentification from user: " . $_POST["accessname"];

							$message = "Username: " . $_POST["accessname"] . "\n";
							$message .= "Browser: " . $_SESSION["_browser"];

							$email->sendMail($from, $to, $theme, $message);
						}

						switch ($accessLevel) {
							case "SuperuserLevel":

								if ($queryStrPost[0] == 'h') {
									header("Location: " . $queryStrPost);
								} else {
									header("Location: ?action=browseCategory&category=root");
								}
								break;

							case "CompanyLevel":

								$company_id = $user->getUserDetails($_SESSION['user_id'], true);
								$terms_conditions = $company_id['terms_conditions'];
								$company_id = $company_id['company_id'];

								if ($queryStrPost[0] == 'h') {
									header("Location: " . $queryStrPost);
								} else {
									if ($terms_conditions == 1) {
										header("Location: ?action=browseCategory&category=company&id=" . $company_id);
									} else {
										$smarty->assign('companyID', $company_id);
										$smarty->display('tpls:tpls/termsConditions.tpl');
										die();
									}
								}
								break;

							case "FacilityLevel":
								//echo "FacilityUser<br>";

								$facility_id = $user->getUserDetails($_SESSION['user_id'], true);
								$terms_conditions = $facility_id['terms_conditions'];
								$facility_id = $facility_id['facility_id'];
								//header("Location: ?action=browseCategory&categoryID=department&facilityID=".$facility_id);
								if ($queryStrPost[0] == 'h') {
									header("Location: " . $queryStrPost);
								} else {
									if ($terms_conditions == 1) {
										header("Location: ?action=browseCategory&category=facility&id=" . $facility_id . "&bookmark=department");
									} else {
										$smarty->assign('facilityID', $facility_id);
										$smarty->display('tpls:tpls/termsConditions.tpl');
										die();
									}
								}
								break;

							case "DepartmentLevel":
								//echo "DepartmentUser<br>";

								$department_id = $user->getUserDetails($_SESSION['user_id'], true);
								$department_id = $department_id['department_id'];
								//header("Location: ?action=browseCategory&categoryID=usage&departmentID=".$department_id);
								if ($queryStrPost[0] == 'h') {
									header("Location: " . $queryStrPost);
								} else {
									header("Location: ?action=browseCategory&category=department&id=" . $department_id . "&bookmark=mix");
								}
								break;
							default:
								header('Location: ' . $xnyo->logout_redirect_url . '?error=auth&url=' . $queryStrPost);
								break;
						}
					} else {
						for ($l = 0; $l < strlen($queryStrPost); $l++) {
							if ($queryStrPost[$l] == '&')
								$queryStrPost[$l] = '!';
						}
						header('Location: ' . $xnyo->logout_redirect_url . '?error=auth');
					}
					break;


				case "msdsUploaderMain":
					$xnyo->filter_post_var("companyID", "int");
					$companyID = $_POST['companyID'];
					$msds = new MSDS($db);
					$result = $msds->upload('main', $companyID);
					break;
			}
		} else {
			//	No action
			throw new Exception('404');
		}
	} else {

		//$smarty->assign("action", $_GET["action"]);
		$user = new User($db, $xnyo, $access, $auth);
        VOCApp::getInstance()->setUser($user);

		// Filter for free access to class
		if (!$user->isLoggedIn() && $_GET["action"] != 'auth'
				&& !(($_GET['action'] == "sendContactEmail" || $_GET['action'] == "requestRepresentativeForms") && $_GET['category'] == "common")
				&& !(($_GET['action'] == 'processororder' || $_GET['action'] == 'processororderResult') && $_GET['category'] == "inventory")) {
			for ($l = 0; $l < strlen($queryStr); $l++) {
				if ($queryStr[$l] == '&')
					$queryStr[$l] = '!';
			}

			echo 'Location ' . $xnyo->logout_redirect_url;
			header('Location: ' . $xnyo->logout_redirect_url . '?error=timeout&url=http://' . $queryStr);
		}

		//	Global notify system
		if (isset($_GET["message"]) && isset($_GET["color"])) {
			$notify = new Notify($smarty);
			$notify->showMessage($_GET["message"], $_GET["color"]);
		}

		if (isset($_GET['action']))
			$action = $_GET['action'];
		else
			$action = 'main';


		if (isset($_GET['category']) || isset($_POST['itemID'])) {
			if (isset($_GET['category']))
				$className = "C" . ucfirst($_GET['category']);
			else
				$className = "C" . ucfirst($_POST['itemID']);



			if (class_exists($className)) {
				$controllerObj = new $className($smarty, $xnyo, $db, $user, $action);
				$controllerObj->runAction();
			}
			else
				throw new Exception('404');
		}
		else {
			$controllerObj = new CCommon($smarty, $xnyo, $db, $user, $action);
			$controllerObj->runAction();
		}
	}
} catch (Exception $e) {
	//var_dump($e);
	//var_dump(xdebug_get_function_stack());
	//exit;

	ob_start();
	var_dump($e->getTrace());
	$trace = ob_get_clean();


	$additionalMessage = "<br/> File:  " . $e->getFile() . "<br/> Line: " . $e->getLine() . " <br/> <fieldset style='background-color:White;color:Black;'> <legend><h2>Trace:</h2></legend> " . $trace . "</fieldset>";

	switch ($e->getMessage()) {
		case '404':
			$smarty->display('tpls:errors/404.tpl');
			break;
		case 'deny':
			$smarty->display('tpls:errors/deny.tpl');
			break;
		default:
			$smarty->assign('message', $e->getMessage() . $additionalMessage);
			$smarty->display('tpls:errors/other.tpl');
	}
}
?>