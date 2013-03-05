<?php

$site_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
define ('site_path', $site_path);

require($site_path.'config/constants4unitTest.php');
require_once ($site_path.'modules/xnyo/xnyo.class.php');

//	Include Class Autoloader
require $site_path.'../vendor/autoload.php';

$xnyo = new Xnyo();
$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;
$xnyo->start();

$GLOBALS["db"]->select_db(DB_NAME);

$user = new User($GLOBALS["db"]);
VOCApp::getInstance()->setUser($user);
VOCApp::getInstance()->setDB($GLOBALS["db"]);