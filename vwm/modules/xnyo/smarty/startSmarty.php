<?php
	require_once ('modules/xnyo/smarty/Smarty.class.php');
	
	$smarty = new Smarty();		 
	$smarty->cache_dir = 'cache/user';	
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = 'design/'.REGION.'/user';
	} else {
		$smarty->template_dir = 'design/user';	
	}
	
	$smarty->compile_dir = 'template_c/user';
	
	//	Register tpl resource for Smarty
	require ('modules/smartyTemplateSource.php');
	register_resource_tpls($smarty,'user');
?>
