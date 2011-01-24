<?php
//	Start Smarty templates engine
include "config/constants.php";
require ('modules/xnyo/smarty/startSmarty.php');

define ('DIRSEP', DIRECTORY_SEPARATOR);
$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
define ('site_path', $site_path);

$smarty->assign("message",$_GET['message']);
$smarty->display('tpls:errors/other.tpl');
?>