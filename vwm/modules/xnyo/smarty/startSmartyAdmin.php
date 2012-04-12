<?php
	require_once ('modules/xnyo/smarty/Smarty.class.php');
	$smarty = new Smarty();
 
	$smarty->cache_dir = 'cache/admin';
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = 'design/'.REGION.'/admin';
	} else {
		$smarty->template_dir = 'design/admin';	
	}
		
	$smarty->compile_dir = 'template_c/admin';
	
	//	Register tpl resource for Smarty
	require ('modules/smartyTemplateSource.php');
	register_resource_tpls(&$smarty,'admin');
?>
