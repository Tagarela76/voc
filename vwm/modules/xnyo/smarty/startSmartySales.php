<?php
	require_once ('modules/xnyo/smarty/Smarty.class.php');
	$smarty = new Smarty();
 
	$smarty->cache_dir = 'cache/sales';
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = 'design/'.REGION.'/sales';
	} else {
		$smarty->template_dir = 'design/sales';	
	}
		
	$smarty->compile_dir = 'template_c/sales';
	
	//	Register tpl resource for Smarty
	require ('modules/smartyTemplateSource.php');
	register_resource_tpls(&$smarty,'sales');
?>
