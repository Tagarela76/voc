<?php
/**
 * VOC WEB Manager Installer for modules
*/

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');
	
$xnyo = new Xnyo();
$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;
$xnyo->start();

//	Start Smarty templates engine	
require_once ('modules/xnyo/smarty/Smarty.class.php');

$site_path = getcwd().DIRECTORY_SEPARATOR; 
define ('site_path', $site_path);
	
//	Include Class Autoloader
require_once('modules/classAutoloader.php');
	
$smarty = new Smarty();		 
$smarty->caching = false;
$smarty->template_dir = 'design/install/';	
$smarty->compile_dir = 'template_c/install/';

$db->select_db(DB_NAME);

$xnyo->filter_post_var('modulesToInstall', array('text'));
$xnyo->filter_post_var('modules', 'text');
$xnyo->filter_post_var('currentModule', 'text');
$xnyo->filter_post_var('step','text');

$ms = new ModuleSystem($db);
$map = $ms->getModulesMap();

if (is_null($_POST['step']) || $_POST['step'] == 'main') {
	//MAIN INSTALLER PAGE
	$modules2install = array();
	$installedModules = array();
	foreach($map as $key => $value) {
		$className = $value.'Installer';
		if (class_exists($className)) {
			$module = new $className($db);
			if($module->checkAlreadyInstalled()) {
				$installedModules []= $key;
			} else {
				$modules2install []= $key;
			}
		}
	}
	$smarty->assign('modules2install',$modules2install);
	$smarty->assign('installedModules',$installedModules);
	$smarty->assign('title', VOCNAME.' Module Installer: Choose modules to install.');
	$smarty->display('installModulesMain.tpl');
} elseif ($_POST['step'] == 'install' || $_POST['step'] == 'installConfirmed') {
	$modules2install = (!is_null($_POST['modulesToInstall']) || count($_POST['modulesToInstall']) > 0)?$_POST['modulesToInstall']:json_decode(stripslashes($_POST['modules']));
	
	if (is_null($modules2install) || count($modules2install) == 0){
		header("Location: ".$_SERVER["REQUEST_URI"]);
		die();
	}
	$validation = array();
	foreach($modules2install as $moduleName) {
		$className = $map[$moduleName].'Installer';
		if (class_exists($className)) {
			$module = new $className($db);
			if($module->check()) {
				if ($_POST['step'] == 'installConfirmed') {
					$module->install();
				} else {
					$checkedModules2install []= $moduleName;
				}
			} else {
				$validation [$moduleName]= $module->errors;
			}
		}
	}
	if ($_POST['step'] == 'installConfirmed'){
		header("Location: ".$_SERVER["REQUEST_URI"]);
		die();
	}
	$smarty->assign('modules2install',$checkedModules2install);
	$smarty->assign('modules',json_encode($modules2install));
	$smarty->assign('validation', $validation);
	$smarty->display('confirmModulesInstall.tpl');
}
?>
