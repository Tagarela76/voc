<?php
	require_once ('modules/xnyo/smarty/Smarty.class.php');
	$smarty = new Smarty();
 
	$smarty->cache_dir = 'cache/vps';	
	
	if (REGION != DEFAULT_REGION) {
		$smarty->template_dir = 'design/'.REGION.'/vps';
	} else {
		$smarty->template_dir = 'design/vps';	
	}
	
	$smarty->compile_dir = 'template_c/vps';
	
	//	Register tpl resource for Smarty
	require ('modules/smartyTemplateSource.php');
	register_resource_tpls(&$smarty,'vps');
?>
