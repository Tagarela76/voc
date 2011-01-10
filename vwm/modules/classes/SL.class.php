<?php


/**
 * Project:     VOC WEB MANAGER
 * File:        SL.class.php
 *
 * Simple Localization.
 * It's really simple  
 *
 * @copyright 2010, KaTeT-Software
 */

class SL {	 
	/**
	 * @var string Path to localization files *locale*.php
	 */ 
	public $localizationPath = 'modules/localization/';



    function SL($locale) {
    	
    	$filePath = $this->localizationPath.$locale.".php";    	
    	if (file_exists($filePath)) {
    		require_once($filePath);	
    	} else {
    		throw new Exception('Can not load locale '.$filePath);
    	}    	    	    	    	    	    	    	
    }
}
?>