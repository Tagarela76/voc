<?php
/*
 * Created on Dec 8, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	define("USERS_TABLE", "user");
	require('config/constants.php'); 			
	
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
	define ('site_path', $site_path);
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php'); 
	//v
	//	Start Smarty templates engine
	require ('modules/xnyo/smarty/startSmarty.php');
	
	require_once('modules/Reform.inc.php');
	
	$queryStr = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	$xnyo->load_plugin('auth');

	$db->select_db(DB_NAME);
	
	/* HERE TEST CODE: */
	
	//update EPA Regulations
	//$rag = new RegAgency($db);
	//$rag->loadAgencyFromXML();
	
	//$ram = new RegActManager($db);
	//$ram->parseXML();
	
	//$ms = new ModuleSystem($db);
	//var_dump($ms->setModule2company('regupdate', 1, 111));

	$companyID = 1;//AniSoft
	$value = '03-02-2011';//company format = d-m-Y
	$tc = new TypeChain($value,'date',$db,$companyID,'facility');
	var_dump($tc->formatInput(),$tc);

	//$ctdate = new CTDate($db);
	//var_dump($ctdate->convert('00-45-12',true), $ctdate->getErrorsForConvertedValue('2500-45-12'));
	

	//send periodic Notifiers
	//$eNotificator = new EmailNotifications($db);
	//$eNotificator->checkPeriodicNotifiers();
	//var_dump($ram->getRegActsList(44));
	//var_dump($ram->getUnreadList(44));
	//$ram->markRIN(44,'readed',array('2060-AN99','2070-AJ52'));
	//$ram->markRIN(44,'mailed',array('2060-AQ16'));
	//echo("<pre>".$ram->getMessageForNotificator(44)."</pre>");
	
//	$ragency = new RegAgency($db,2);
////	$ragency->code = '2003';
////	$ragency->name = 'name';
////	$ragency->acronym = 'acronum';
////	$ragency->save();
//	$ragency->delete();
//	
//	$ra = new RegAct($db);
//	
//	$ra->rin = 'sdfs2';
//	$ra->reg_agency_id = 1;
//	$ra->title = 'title4';
//	$ra->stage = 'state';
//	$ra->significant = 'significant6';
//	$ra->date_received = '01/01/11';
//	$ra->legal_deadline = 'No';
//	$ra->category = 'review';
//	$ra->save();
//	$ra2 = new RegAct($db,'sdfs');var_dump($ra2);
//	$ra->delete();
?>
