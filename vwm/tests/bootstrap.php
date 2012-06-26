<?php

$site_path = getcwd().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
define ('site_path', $site_path);

require('../config/constants.php');
require_once ('../modules/xnyo/xnyo.class.php');

//	Include Class Autoloader
require_once('../modules/classAutoloader.php');

//http://stackoverflow.com/questions/6612413/autoload-not-respected-when-testing-with-phpunit
spl_autoload_register('__autoload');

$xnyo = new Xnyo();
$xnyo->database_type = DB_TYPE;
$xnyo->db_host = DB_HOST;
$xnyo->db_user = DB_USER;
$xnyo->db_passwd = DB_PASS;
$xnyo->start();

$GLOBALS["db"]->select_db(DB_NAME);