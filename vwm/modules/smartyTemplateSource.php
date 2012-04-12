<?php
/*
 * Created on Sep 3, 2010
 *
 * This file consist function register_resource_tpls - this function register 
 * resource 'tpls' that generate templates for smarty. Other 4 functions needed to register resource:
 * tpls_get_template - generete template that include template with chosen name from correct dorectory if its exist
 * tpls_get_timeshtamp - return time of template's last modify
 * tpls_get_secure - always returns true, we generate this templates by ourselves, so they all are secure
 * tpls_get_trusted - dont used to register template resource, but we still need it tp register 'tpls' resource, always
 */
 	$file = '';
 	$parent = '';
 
    function tpls_get_template($tpl_name, &$tpl_source, &$smarty_obj) {
    	global $parent;
    	global $file;
    	
    	//to include needed tpl try to load tpls
    	$filePath = site_path.'design'.DIRSEP.(($parent != '')?$parent.DIRSEP:'').$tpl_name;
    	if (REGION != DEFAULT_REGION) {		
			$localizedFilePath .= site_path.'design'.DIRSEP.REGION.DIRSEP.(($parent != '')?$parent.DIRSEP:'').$tpl_name; 
		}
    	$modulePath = site_path.'extensions'.DIRSEP.$tpl_name;
	    	//	TRY TO LOAD LOCALIZED TPL
		if (file_exists($localizedFilePath) == false) {
			//	TRY TO LOAD ORIGINAL TPL	
			if (file_exists($filePath) == false) {
				// TRY TO LOAD MODULE TPL
				if (file_exists($modulePath)) {
					$tpl_source = '{include file=\''. $modulePath .'\'} ';
					$file = $tpl_source;
				} else {
					return false;
					$tpl_source = ' no template '.$tpl_name.' to display ';
				}
			} else {
				$tpl_source = '{include file=\''. ($filePath) .'\'} ';	
					$file = $tpl_source;		
			}				
		} else {
			$tpl_source = '{include file=\''. ($localizedFilePath) .'\'} ';	
					$file = $tpl_source;
		}
    	return true;
    }
    
    function tpls_get_timeshtamp($tpl_name, &$tpl_timeshtamp, &$smarty_obj) {
    	global $file;
    	
    	// get timeshtamp of last modify from chosen template
    	$tpl_timeshtamp = filemtime($file);
    	return true;
    }
    
    function tpls_get_secure($tpl_name, &$smarty_obj) {
    	return true; // our resource is secure in any case
    }
    
    function tpls_get_trusted($tpl_name, &$smarty_obj) {
    	return true; // not used for templates, but we still need it
    }
    
    /**
     * function register_resource_tpls - register resource 'tpls' for smarty generating templates, set parent fplder to load templates from it
     * @param &$smarty_obj, $parentFolder
     */
    function register_resource_tpls(&$smarty_obj, $parentFolder) {
    	global $parent;
    	
    	//register smarty resource for templates
    	$smarty_obj->register_resource('tpls', array(
                                'tpls_get_template',
                                'tpls_get_timeshtamp',
                                'tpls_get_secure',
                                'tpls_get_trusted')
                                );
    	$parent = $parentFolder;
    }
?>
