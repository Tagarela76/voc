<?php
	
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';


class Example extends PHPUnit_Extensions_SeleniumTestCase {
	
	function setUp() {
		$this->setBrowser("*chrome");
		$this->setBrowserUrl("http://localhost/");
	}
	
	function testMyTestCase()
		{
		$this->open("/voc_src/site/");
		$this->click("//tr[2]/td/table/tbody/tr[1]/td/table/tbody/tr/td[1]/a/img");
		$this->waitForPageToLoad("30000");
		$this->type("accessname", "kttsoft");
		$this->type("password", "kttsoft");
		$this->click("//input[@value='login']");
		$this->waitForPageToLoad("30000");
		try {
			$this->assertTrue($this->isTextPresent("Welcome to VOC-WEB-MANAGER!"));
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			array_push($this->verificationErrors, $e->toString());
		}
		}
}
?>
