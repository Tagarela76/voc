<?php
	require_once ('modules/xnyo/smarty/Smarty.class.php');
	$smarty = new Smarty();
 
	$smarty->cache_dir = 'cache/supplier';
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = 'design/'.REGION.'/supplier';
	} else {
		$smarty->template_dir = 'design/supplier';	
	}
		
	$smarty->compile_dir = 'template_c/supplier';
	
	//	Register tpl resource for Smarty
	require ('modules/smartyTemplateSource.php');
	register_resource_tpls(&$smarty,'supplier');
?>
