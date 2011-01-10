<?php

interface iInstaller {
	/**
	 * check all needed options to install module
	 */
	function check();
	/**
	 * install module
	 */
	function install();
	/**
	 * check if module was already installed
	 */
	function checkAlreadyInstalled();
}

?>
