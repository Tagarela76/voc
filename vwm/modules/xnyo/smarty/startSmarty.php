<?php
	require_once (dirname(__FILE__).'/../../../modules/xnyo/smarty/Smarty.class.php');
	
	$smarty = new Smarty();		 
	$smarty->cache_dir = dirname(__FILE__).'/../../../cache/user';	
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = dirname(__FILE__).'/../../../design/'.REGION.'/user';
	} else {
		$smarty->template_dir = dirname(__FILE__).'/../../../design/user';	
	}
	
	$smarty->compile_dir = dirname(__FILE__).'/../../../template_c/user';
	
	//	Register tpl resource for Smarty
	require (dirname(__FILE__).'/../../../modules/smartyTemplateSource.php');
	register_resource_tpls($smarty,'user');
?>
