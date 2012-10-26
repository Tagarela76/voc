<?php

class LibraryInjection {
	
	public $smarty;
	
	public function __construct($smarty) {
		$this->smarty = $smarty;
	}
	public function injectToolTip() {
		
		// js including
		// I should to get jsSources & cssSources variables and added tooltip scripts
		$templateJsSources = $this->smarty->get_template_vars('jsSources');  
		$toolTipJsScripts = array(
			'modules/lib/jquery-tooltip/js/jquery.bgiframe.js',
			'modules/lib/jquery-tooltip/js/jquery.dimensions.js',
			'modules/lib/jquery-tooltip/js/jquery.tooltip.js');
		if (is_null($templateJsSources)) {
			$jsSources = $toolTipJsScripts;
		} else {
			$jsSources = array_merge($templateJsSources, $toolTipJsScripts);
		}		 
		$this->smarty->assign('jsSources', $jsSources);
		
		// css including
		$templateCssSources = $this->smarty->get_template_vars('cssSources');
		$toolTipCssScripts = array(
			'modules/lib/jquery-tooltip/css/jquery.tooltip.css');
		if (is_null($templateCssSources)) {
			$cssSources = $toolTipCssScripts;
		} else {
			$cssSources = array_merge($templateCssSources, $toolTipCssScripts);
		}
		
		$this->smarty->assign('cssSources', $cssSources);
	}
}

?>
