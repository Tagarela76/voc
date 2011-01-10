<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     display_error
 * Purpose:  display an error
 * -------------------------------------------------------------
 */
function smarty_function_display_error($params, &$smarty)
{
	global $xnyo_parent;
	
	// are we getting a certain error?
	if (!empty($params['id']))
	{
		$err = $xnyo_parent->dump_errors(CLIENT, $params['id']);
		if (!empty($params['assign']))
		{
			$smarty->assign($params['assign'], (empty($err) ? '' : $err));
			return NULL;
		}
		return (empty($err) ? '' : $err);
	}

	// dump all errors, as an array
	if (!empty($params['assign']))
	{
		$smarty->assign($params['assign'], $xnyo_parent->dump_errors(CLIENT));
		return NULL;
	}
		
}

/* vim: set expandtab: */

?>
