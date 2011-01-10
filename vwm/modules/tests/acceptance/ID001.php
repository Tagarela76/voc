<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Example extends PHPUnit_Extensions_SeleniumTestCase {
	
  function setUp() {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/");
  }
/*
  function testID001() {
    $this->open("/voc_src/site/voc_web_manager.html");
    
    //	Login
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->mixOperations();
  }
  
  
 
  
  function testID002() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->equipmentViewOperations();
   }
  
  
  
  
  function testID003() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->inventoryOperations();   
  }
  
  


  function testID004() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->productOperations();
  }
  
 
  
    
  function testID005() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	View department
    $this->click("//input[@value='View']");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Dep #1", $this->getText("//div[2]/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("100.00", $this->getText("//div[2]/table/tbody/tr[3]/td[2]/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
 
  function testID006() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->ruleListOperations('department');    
  }
  
  
  
  
  function testID007() {
  	$this->open("/voc_src/site/voc_web_manager.html");
    
    //	Login    
    $this->type("accessname", "user1facility");
    $this->type("password", "user1facility");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to department
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
    $this->mixOperations();
  }
    
  
 
  */
  function testID008() {
  	 $this->open("/voc_src/site/voc_web_manager.html");
  	 
  	 //	Login
  	 $this->type("accessname", "user1facility");
  	 $this->type("password", "user1facility");
  	 $this->click("//input[@value='login']");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Go to department
  	 $this->click("//td[3]/a/div");
  	 $this->waitForPageToLoad("30000");
  	 
  	 $this->equipmentOperations('facility');  	 
  } 
  
 /*
  
  
  function testID009 () {
  	 $this->open("/voc_src/site/voc_web_manager.html");
  	 
  	 //	Login
  	 $this->type("accessname", "user1facility");
  	 $this->type("password", "user1facility");
  	 $this->click("//input[@value='login']");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Go to department
  	 $this->click("//td[3]/a/div");
  	 $this->waitForPageToLoad("30000");
  	 
  	 $this->inventoryOperations();
  }
  
  
  
  
   function testID010 () {
  	 $this->open("/voc_src/site/voc_web_manager.html");
  	 
  	 //	Login
  	 $this->type("accessname", "user1facility");
  	 $this->type("password", "user1facility");
  	 $this->click("//input[@value='login']");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Go to department
  	 $this->click("//td[3]/a/div");
  	 $this->waitForPageToLoad("30000");
  	 
  	 $this->productOperations();
  }
  
  
  
  
  function testID011 () {
  	
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1facility");
    $this->type("password", "user1facility");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Add Department
    $this->click("action");
    $this->waitForPageToLoad("30000");
    $this->type("name", "I WIll Be Deleted");
    $this->type("voc_limit", "9");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Department list
    //$this->assertEquals("Department I WIll Be Deleted was successfully added", $this->getText("//div[2]/table[1]/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("I WIll Be Deleted", $this->getText("//tr[3]/td[3]/a/div"));
    $this->click("//tr[3]/td[3]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	New department
    $this->assertEquals("No mixes in choosen department", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("9.00", $this->getText("//div[1]/b"));
    $this->click("//input[@value='View']");
    $this->waitForPageToLoad("30000");
    
    //	View department details
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	Update department
    $this->type("name", "I WIll Be Deleted [updated]");
    $this->type("voc_limit", "10.00");
    $this->click("save");    
    for ($second = 0; ; $second++) {
    	echo $this->getText("//div[@id='notifyContainer']/table/tbody/tr[1]/td/div/div/div/div");
        if ($second >= 5) $this->fail("timeout");
        try {
            if ("Saved" == $this->getText("//div[@id='notifyContainer']/table/tbody/tr[1]/td/div/div/div/div")) break;
        } catch (Exception $e) {}
        sleep(1);
    }
    //$this->assertEquals("Saved", $this->getText("//div[@id='notifyContainer']/table/tbody/tr[1]/td/div/div/div/div"));    
    $this->click("cancel");
    $this->waitForPageToLoad("30000");
    
    //	Department details
    //$this->assertEquals("Department I WIll Be Deleted [updated] was successfully edited", $this->getText("//table[2]/tbody/tr[1]/td/div/div/div/div"));
    //$this->assertEquals("I WIll Be Deleted [updated]", $this->getText("//div[2]/table/tbody/tr[2]/td[2]/div"));
    //$this->assertEquals("10.00", $this->getText("//div[2]/table/tbody/tr[3]/td[2]/div"));
    //$this->click("//a[2]");
    //$this->waitForPageToLoad("30000");
    
    //	Department inside
    $this->assertEquals("10.00", $this->getText("//div[1]/b"));
    $this->click("//input[@value='Delete']");
    $this->waitForPageToLoad("30000");
    //$this->click("link=Dev Facility");
    //$this->waitForPageToLoad("30000");
    
    //	Departments list
    //$this->click("item_1");
    //$this->click("//input[@name='action' and @value='deleteItem']");
    //$this->waitForPageToLoad("30000");
    
    //	Delete department
    $this->assertEquals("You are about to delete department I WIll Be Deleted [updated]. Are you sure?",$this->getText("//div/div/div/div"));
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Department list
    $this->assertEquals("Department I WIll Be Deleted [updated] was successfully deleted", $this->getText("//div[2]/table[1]/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("Dep #1", $this->getText("//td[3]/a/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
 
  /*
  
  
  public function testID012() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1facility");
    $this->type("password", "user1facility");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Add department
    $this->click("action");
    $this->waitForPageToLoad("30000");
    $this->type("name", "Department =)");
    $this->type("voc_limit", "32");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Add another one
    $this->click("action");
    $this->waitForPageToLoad("30000");
    $this->type("name", "Department =)");
    $this->type("voc_limit", "99");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Name should be unique!
    $this->assertEquals("Entered name is alredy in use!", $this->getText("//font"));
    $this->type("name", "Department =(");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Check department list
    $this->assertEquals("Department =(", $this->getText("//tr[3]/td[3]/a/div"));
    $this->assertEquals("Department =)", $this->getText("//tr[4]/td[3]/a/div"));
    $this->click("item_1");
    $this->click("item_2");
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    //	Delete several departments
    $this->assertEquals("Department =(", $this->getText("//td[3]/div"));
    $this->assertEquals("Department =)", $this->getText("//tr[3]/td[3]/div"));
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Departments deleted
    $this->assertEquals("Departments Department =(, Department =) were successfully deleted", $this->getText("//div[2]/table[1]/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("Dep #1", $this->getText("//td[3]/a/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");	
  }
  
  
  
  
  public function testID013() {
  	//	Login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1facility");
    $this->type("password", "user1facility");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    $this->ruleListOperations('facility');
  }
  
  
  
  
  public function testID014() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to facility
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");    
    
    //	Go to department
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("No usages in the department", $this->getText("//table[4]/tbody/tr[2]/td"));
    $this->assertEquals("No mixes in choosen department", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("100.00", $this->getText("//div[1]/b"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
  }
  
 
  
  
  public function testID015() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to facility
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");    
    
    //	Go to department
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
       
    $this->equipmentViewOperations();   
  }
  
  

  
   public function testID016() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to facility
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");    
    
    //	Go to department
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
       
    //	Go to inventory
    $this->click("//td[2]/a/div/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert inventory list
    $this->assertEquals("CARDINAL 211", $this->getText("//tr[2]/td[2]/a/div"));
    $this->assertEquals("robot Z", $this->getText("//tr[2]/td[3]/a/div"));
    $this->assertEquals("z-z-zzz [in equipment]", $this->getText("//tr[2]/td[4]/a/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
  
   public function testID017() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to facility
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");    
    
    //	Go to department
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
       
    $this->productOperations();
  }
  
  
  
  
  public function testID018() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Facilities list
    $this->assertEquals("Dev Facility", $this->getText("//td[3]/a/div"));
    $this->assertEquals("Address, Denis Tukalenko (5555)", $this->getText("//td[4]/a/div"));
    $this->click("//input[@value='View']");
    $this->waitForPageToLoad("30000");
    
    //	View company details
    $this->assertEquals("VOCWEBMANAGER DEV", $this->getText("//div[2]/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("Address", $this->getText("//div[2]/table/tbody/tr[3]/td[2]/div"));
    $this->assertEquals("City", $this->getText("//tr[4]/td[2]/div"));
    $this->assertEquals("County", $this->getText("//tr[5]/td[2]/div"));
    $this->assertEquals("USA", $this->getText("//tr[6]/td[2]/div"));
    $this->assertEquals("Florida", $this->getText("//tr[7]/td[2]/div"));
    $this->assertEquals("13132", $this->getText("//tr[8]/td[2]/div"));
    $this->assertEquals("5555", $this->getText("//tr[9]/td[2]/div"));
    $this->assertEquals("5555", $this->getText("//tr[10]/td[2]/div"));
    $this->assertEquals("denis.nt@kttsoft.com", $this->getText("//tr[11]/td[2]/div"));
    $this->assertEquals("Denis Tukalenko", $this->getText("//tr[12]/td[2]/div"));
    $this->assertEquals("acceptance test", $this->getText("//tr[13]/td[2]/div"));
    $this->click("link=VOCWEBMANAGER DEV");
    $this->waitForPageToLoad("30000");
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");	
  }
  
  
  
  
  public function testID019() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1");
    $this->type("password", "user1");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Go to settings
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Go to msds uploader
    $this->click("//h2");
    $this->waitForPageToLoad("30000");
    
    //	flash
    $this->assertEquals("basic uploader", $this->getText("link=basic uploader"));
    $this->click("link=basic uploader");
    $this->waitForPageToLoad("30000");
    
    //	basic
    $this->assertEquals("main uploader", $this->getText("link=main uploader"));
    $this->click("link=main uploader");
    
    //	logout
    $this->waitForPageToLoad("30000");
    $this->click("//input[@value=' Logout ']");
  }
  
  
  
  
  public function testID020() {
  	//	login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	go to department
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
  	$this->mixOperations();
  }
  
  
  
  
  public function testID021() {
  	//	login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	go to department
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
  	$this->equipmentOperations('super');  	 
  } 
  
  
  
  
  public function testID022() {
  	//	login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	go to department
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
  	$this->inventoryOperations();  	 
  } 
  
 
  
  
  public function testID023() {
  	//	login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	go to department
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
  	$this->productOperations();  	 
  }
  
 
  
  
  public function testID024() {
		//	Login
	  $this->open("/voc_src/site/voc_web_manager.html");
	  $this->type("accessname", "kttsoft");
	  $this->type("password", "kttsoft");
	  $this->click("//input[@value='login']");
	  $this->waitForPageToLoad("30000");
	  
	  //	Go to company
	  $this->click("//td[3]/a/div");
	  $this->waitForPageToLoad("30000");
	  
	  //	Add facility
	  $this->click("action");
	  $this->waitForPageToLoad("30000");
	  $this->type("epa", "EPA");
	  $this->type("voc_limit", "999");
	  $this->type("name", "Dead facility");
	  $this->type("address", "street");
	  $this->type("city", "Pavlograd");
	  $this->type("county", "Dnipro");
	  $this->select("selectState", "label=Kentucky");
	  $this->type("zip", "55555");
	  $this->type("phone", "212121");
	  $this->type("fax", "321313");
	  $this->type("email", "denis.nt@kttsoft.com");
	  $this->type("contact", "Denis");
	  $this->type("title", "Me");
	  $this->click("save");
	  $this->waitForPageToLoad("30000");
	  
	  //	Facility list
	  $this->assertEquals("Facility Dead facility was successfully added", $this->getText("//td/div/div/div/div"));
	  $this->assertEquals("Dead facility", $this->getText("//td[3]/a/div"));
	  $this->assertEquals("street, Denis (212121)", $this->getText("//td[4]/a/div"));
	  $this->assertEquals("Dev Facility", $this->getText("//tr[3]/td[3]/a/div"));
	  $this->assertEquals("Address, Denis Tukalenko (5555)", $this->getText("//tr[3]/td[4]/a/div"));
	  $this->click("//td[3]/a/div");
	  $this->waitForPageToLoad("30000");
	  
	  //	Go to new facility
	  $this->assertEquals("No departments in chosen facility", $this->getText("//table[2]/tbody/tr[2]/td"));	  	  
	  $this->click("//input[@value='View']");
	  $this->waitForPageToLoad("30000");
	  
	  //	View facility details
	  $this->assertEquals("", $this->getText("//div[2]/table/tbody/tr[1]/td[2]"));
	  $this->assertEquals("999.00", $this->getText("//div[2]/table/tbody/tr[3]/td[2]/div"));
	  $this->assertEquals("Dead facility", $this->getText("//tr[4]/td[2]/div"));
	  $this->assertEquals("street", $this->getText("//tr[5]/td[2]/div"));
	  $this->assertEquals("Pavlograd", $this->getText("//tr[6]/td[2]/div"));
	  $this->assertEquals("Dnipro", $this->getText("//tr[7]/td[2]/div"));
	  $this->assertEquals("Kentucky", $this->getText("//tr[8]/td[2]/div"));
	  $this->assertEquals("55555", $this->getText("//tr[9]/td[2]/div"));
	  $this->assertEquals("USA", $this->getText("//tr[10]/td[2]/div"));
	  $this->assertEquals("212121", $this->getText("//tr[11]/td[2]/div"));
	  $this->assertEquals("321313", $this->getText("//tr[12]/td[2]"));
	  $this->assertEquals("denis.nt@kttsoft.com", $this->getText("//tr[13]/td[2]/div"));
	  $this->assertEquals("Denis", $this->getText("//tr[14]/td[2]/div"));
	  $this->assertEquals("Me", $this->getText("//tr[15]/td[2]/div"));
	  $this->click("//input[@value='Delete']");
	  $this->waitForPageToLoad("30000");	 
	  
	  //	Delete facility from the inside
	  $this->assertEquals("You are about to delete facility Dead facility. Are you sure?", $this->getText("//div/div/div/div"));
	  $this->click("confirm");
	  $this->waitForPageToLoad("30000");
	  
	  //	Facility list
	  $this->assertEquals("Facility Dead facility was successfully deleted", $this->getText("//td/div/div/div/div"));
	  $this->assertEquals("Dev Facility", $this->getText("//td[3]/a/div"));
	  $this->assertEquals("Address, Denis Tukalenko (5555)", $this->getText("//td[4]/a/div"));
	  
	  //	Logout
	  $this->click("//input[@value=' Logout ']");
	  $this->waitForPageToLoad("30000");	
  }
  
  
  
  
  public function testID025() {
  	//	Login
  	$this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Add company
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    $this->type("name", "test");
    $this->type("address", "address");
    $this->type("city", "city");
    $this->type("county", "oooO");
    $this->select("selectState", "label=Indiana");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	ERRORS!!
    $this->assertEquals("There are errors in the form\nCorrect them please!", $this->getText("//div/div/div/div"));
    $this->type("zip", "55555");
    $this->type("phone", "7482364278");
    $this->type("fax", "42342");
    $this->type("email", "denis.nt@kttsoft.com");
    $this->type("contact", "Denis");
    $this->type("title", "fesf");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Ad one more company
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    $this->type("name", "One more company");
    $this->type("address", "cscs");
    $this->type("city", "dad");
    $this->type("county", "fdsf");
    $this->type("zip", "55555");
    $this->type("phone", "4234");
    $this->type("fax", "452342");
    $this->type("email", "denis.nt@kttsoft.com");
    $this->type("contact", "gdgdg");
    $this->type("title", "sgrr");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    // Company list
    $this->assertEquals("One more company", $this->getText("//td[3]/a/div"));
    $this->assertEquals("cscs, gdgdg (4234)", $this->getText("//td[4]/a/div"));
    $this->assertEquals("test", $this->getText("//tr[3]/td[3]/a/div"));
    $this->assertEquals("address, Denis (7482364278)", $this->getText("//tr[3]/td[4]/a/div"));
    $this->assertEquals("VOCWEBMANAGER DEV", $this->getText("//tr[4]/td[3]/a/div"));
    $this->assertEquals("Address, Denis Tukalenko (5555)", $this->getText("//tr[4]/td[4]/a/div"));
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	New company's facilities
    $this->assertEquals("No facilities in chosen company", $this->getText("//table[2]/tbody/tr[2]/td"));
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Add facility
    $this->type("epa", "EPA");
    $this->type("voc_limit", "342");
    $this->type("name", "grdgdh");
    $this->type("address", "hrger");
    $this->type("city", "htrhrh");
    $this->type("county", "gtrgr");
    $this->type("zip", "55555");
    $this->type("phone", "53532");
    $this->type("fax", "4532234");
    $this->type("email", "denis.nt@kttsoft.com");
    $this->type("contact", "Denis Tukalenko");
    $this->type("title", "tertet");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Facility list
    $facilityID = $this->getText("//a/div");
    $this->assertEquals("grdgdh", $this->getText("//td[3]/a/div"));
    $this->assertEquals("hrger, Denis Tukalenko (53532)", $this->getText("//td[4]/a/div"));
    $this->click("//input[@name='item_0' and @value='".$facilityID."']");
    
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    //	Delete facility
    $this->assertEquals("hrger, Denis Tukalenko (53532)", $this->getText("//form/table/tbody/tr[3]/td[2]/div"));
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	No facilities left
    $this->assertEquals("No facilities in chosen company", $this->getText("//table[2]/tbody/tr[2]/td"));
    $this->click("link=All companies");
    $this->waitForPageToLoad("30000");
    
    //	Delete new companies
    $this->click("item_0");
    $this->click("item_1");
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    //	Confirm
    $this->assertEquals("One more company", $this->getText("//td[3]/div"));
    $this->assertEquals("cscs, gdgdg (4234)", $this->getText("//td[4]/div"));
    $this->assertEquals("test", $this->getText("//tr[3]/td[3]/div"));
    $this->assertEquals("address, Denis (7482364278)", $this->getText("//tr[3]/td[4]/div"));
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Company list
    $this->assertEquals("VOCWEBMANAGER DEV", $this->getText("//td[3]/a/div"));
    $this->assertEquals("Address, Denis Tukalenko (5555)", $this->getText("//td[4]/a/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
  }
 
  
  
  public function testID027() {
  	//	Login
 	 $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "kttsoft");
    $this->type("password", "kttsoft");
    $this->click("//input[@value='login']");    
    $this->waitForPageToLoad("30000");
    
    //	Go to company
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Settings
    $this->click("//h2");
    $this->waitForPageToLoad("30000");
    
    //	MSDS uploader flash
    $this->assertEquals("If you have problems with upload, please try basic uploader. \n \n If you have problems with upload, please try basic uploader.", $this->getText("flashPart"));
    $this->click("link=basic uploader");
    $this->waitForPageToLoad("30000");
    
    //	MSDS uploader basic
    $this->assertEquals("Back to main uploader.", $this->getText("//tr[2]/td/table/tbody/tr[1]/td/table/tbody/tr/td[2]/div[1]"));	
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Customize rule list
    $this->click("//tr[2]/td[1]/a/h2");
    $this->click("link=None");
    $this->click("//input[@name='ruleID' and @value='1']");
    $this->click("//input[@value='Save']");    
    
    //	Go to all companies
    $this->click("link=All companies");
    $this->waitForPageToLoad("30000");
    
    //	Go to department
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//td[3]/a/div");
    $this->waitForPageToLoad("30000");
    $this->click("//a/div");
    $this->waitForPageToLoad("30000");
    
    //	Add mix
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Check rule list
    $this->assertEquals("219 - Equipment not requiring a written permit pursuant to regulation II.", $this->getText("rule"));
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Settings
    $this->click("//tr[2]/td[1]/a/h2");
    
    //	Customize rule list
    $this->click("link=All");
    $this->click("//input[@value='Save']");
    $this->click("//a[4]");
    $this->waitForPageToLoad("30000");
    
    //	Inside department
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Assert rules
    $this->assertEquals("219 - Equipment not requiring a written permit pursuant to regulation II. 1171 - Solvent Cleaning Operations 1168 - Adhesive Applications 1164 - Semiconductor Manufacturing 1151 - Motor Vehicle and Mobile Equipment Non-Assembly Line Coating Operations 1145 - Plastic, Rubber, and Glass Coatings 1136 - Wood Products Coatings 1130.1 - Screen Printing Operations 1130 - Graphic Arts 1128 - Paper, Fabric, and Film Coating Operations 1126 - Magnet Wire Coating Operations 1125 - Metal Container, Closure, and Coil Coating Operations 1124 - Aerospace Assembly and Component Manufacturing Operations 1122 - Solvent Degreasers 1115 - Motor Vehicle Assembly Line Coating Operations 1107 - Coating of Metal Parts and Products 1106.1 - Pleasure Craft Coating Operations 1106 - Marine Coating Operations 1104 - Wood Flat Stock Coating Operations 1102 - Petroleum Solvent Dry Cleaners", $this->getText("rule"));
    
    //	logout
    $this->click("//input[@value=' Logout ']");	
  }
  
 
  
  
  public function testID028() {
  	//	login
    $this->open("/voc_src/site/voc_web_manager.html");
    $this->type("accessname", "user1department");
    $this->type("password", "user1department");
    $this->click("//input[@value='login']");
    $this->waitForPageToLoad("30000");
    
    //	Add mix
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    $this->type("description", "a lot of voc");
    $this->select("selectProduct", "label=regexp:CRC\\s+30M-13640\\s+LW27\\sMONO\\sCOAT");
    $this->click("//option[@value='190']");
    $this->type("quantity", "45");
    $this->select("selectUnittypeClass", "label=USA weight");
    $this->click("//select[@id='selectUnittypeClass']/option[3]");
    $this->select("selectUnittype", "label=OZS");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	weight/volume conflict
    $this->assertEquals("Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.", $this->getText("//div[3]/span"));
    $this->select("selectUnittypeClass", "label=USA liquid");
    $this->click("//select[@id='selectUnittypeClass']/option[1]");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	save mix
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	mix list
    $this->assertEquals("100.00", $this->getText("//div[1]/b"));
    $this->click("//tr[2]/td[3]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	mix details
    $this->assertEquals("YES!!!", $this->getText("//div/b"));
    $this->assertEquals("YES!!!", $this->getText("//tr[7]/td[2]/div/b"));        
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	edit mix
    $this->type("wasteValue", "50.00");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	mix details
    $this->assertEquals("22.5", $this->getText("//td[2]/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("106.43", $this->getText("//td[2]/table/tbody/tr[3]/td[2]/div"));
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	edit mix
    $this->type("wasteValue", "44.00");
    $this->select("selectWasteUnittypeClass", "label=USA liquid");
    $this->click("//option[@value='USALiquid']");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	mix details
    $this->assertEquals("44", $this->getText("//td[2]/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("4.73", $this->getText("//td[2]/table/tbody/tr[3]/td[2]/div"));
    $this->assertEquals("5.32", $this->getText("//td[2]/table/tbody/tr[4]/td[2]/div"));
    $this->assertEquals("YES!!!", $this->getText("//td[2]/div/b"));
    $this->assertEquals("no", $this->getText("//td[2]/table/tbody/tr[7]/td[2]/div"));
    $this->assertEquals("no", $this->getText("//tr[8]/td[2]/div"));
    $this->click("link=Dep #1");
    $this->waitForPageToLoad("30000");
    
    //	delete mix
    $this->assertEquals("100.00", $this->getText("//div[1]/b"));
    $mixID = $this->getText("//tr[2]/td[2]/a/div");
    $this->click("//input[@name='item_0' and @value='".$mixID."']");
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	logout
    $this->click("//input[@value=' Logout ']");	
  }
  
  */
  
  
  private function mixOperations () {
  	//	Assert voc gauge
    $this->assertEquals("0/100.00", $this->getText("//td[3]/div/div[1]"));
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Add mix
    $this->type("description", "mix_test");
    $this->type("exemptRule", "(c)");
    
    //	Add product to mix	
    $this->select("selectProduct", "label=regexp:CRC\\s+25S13938\\s+KX2\\sSILVER");
    $this->click("//option[@value='187']");
    $this->type("quantity", "1");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Add product to mix with BLANK QUANTITY
    $this->select("selectProduct", "label=regexp:CRC\\s+25L16247\\s+PMS\\s871C-GOLD\\sPEARL");
    $this->click("//option[@value='385']");
    $this->select("selectUnittypeClass", "label=USA dry");
    $this->click("//select[@id='selectUnittypeClass']/option[2]");
    $this->select("selectUnittype", "label=dry gal");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	assert error BLANK QUANTITY
    try {
        $this->assertTrue($this->isTextPresent("Error!"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    
    //	Add product to mix
    $this->select("selectProduct", "label=regexp:CRC\\s+25S13849V1\\s+PEARL\\sSILVER\\s\\(EAR\\)");
    $this->click("//option[@value='189']");
    $this->type("quantity", "4");
    $this->select("selectUnittypeClass", "label=USA dry");
    $this->click("//select[@id='selectUnittypeClass']/option[2]");
    $this->select("selectUnittype", "label=dry gal");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Assert mix info
    $this->assertEquals("25S13938", $this->getTable("//div[2]/table.1.2"));
    $this->assertEquals("1", $this->getTable("//div[2]/table.1.4"));
    $this->assertEquals("gal", $this->getTable("//div[2]/table.1.5"));
    $this->assertEquals("25S13849V1", $this->getTable("//div[2]/table.2.2"));
    $this->assertEquals("PEARL SILVER (EAR)", $this->getTable("//div[2]/table.2.3"));
    $this->assertEquals("4", $this->getTable("//div[2]/table.2.4"));
    $this->assertEquals("dry gal", $this->getTable("//div[2]/table.2.5"));
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	Assert voc gauge and mix list	
    $this->assertEquals("5.04/100.00", $this->getTable("//div/table.0.3"));
    try {
        $this->assertTrue($this->isTextPresent("mix_test"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    
    $mixID = $this->getTable("//table[4].1.1");
    
    $this->click("//tr[2]/td[2]/a/div");    
    $this->waitForPageToLoad("30000");
    
    //	View mix details
    $this->assertEquals(date("m-d-Y"), $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.4.1"));
    $this->assertEquals("05/21/2010 4:09PM", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.5.1"));
    $this->assertEquals("5.04", $this->getTable("//tr[2]/td[2]/table.2.1"));
    $this->assertEquals("CRC", $this->getTable("//tr[3]/td/table.1.0"));
    $this->assertEquals("25S13849V1", $this->getTable("//tr[3]/td/table.1.1"));
    $this->assertEquals("PEARL SILVER (EAR)", $this->getTable("//tr[3]/td/table.1.2"));
    $this->assertEquals("4.00", $this->getTable("//tr[3]/td/table.1.3"));
    $this->assertEquals("dry gal", $this->getTable("//tr[3]/td/table.1.4"));
    $this->assertEquals("CRC", $this->getTable("//tr[3]/td/table.2.0"));
    $this->assertEquals("25S13938", $this->getTable("//tr[3]/td/table.2.1"));
    $this->assertEquals("KX2 SILVER", $this->getTable("//tr[3]/td/table.2.2"));
    $this->assertEquals("1.00", $this->getTable("//tr[3]/td/table.2.3"));
    $this->assertEquals("gal", $this->getTable("//tr[3]/td/table.2.4"));
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	Edit mix
    $this->type("exemptRule", "");
    $this->type("description", "mix_test_updated");
    $this->type("creationTime", "11-03-2009");
    $this->select("selectProduct", "label=regexp:Water\\s+water\\s+water");
    $this->click("//option[@value='579']");
    $this->type("quantity", "3");
    $this->select("selectUnittype", "label=fl oz");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Assert updates
    $this->assertEquals("water", $this->getTable("//div[2]/table.3.2"));
    try {
        $this->assertEquals("3", $this->getValue("quantity_2"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	Assert and view updated mix details
    $this->assertEquals("11-03-2009", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.4.1"));
    $this->assertEquals("05/21/2010 4:09PM", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.5.1"));
    $this->assertEquals("water", $this->getTable("//tr[3]/td/table.1.1"));
    $this->assertEquals("water", $this->getTable("//tr[3]/td/table.1.2"));
    $this->assertEquals("3.00", $this->getTable("//tr[3]/td/table.1.3"));
    $this->assertEquals("fl oz", $this->getTable("//tr[3]/td/table.1.4"));
    $this->assertEquals("mix_test_updated", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.1.1"));
    $this->assertEquals("5.04", $this->getTable("//tr[2]/td[2]/table.2.1"));
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	Edit mix again (adding waste % and exemtion)
    $this->type("wasteValue", "50");
    $this->type("exemptRule", "(c)");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	Asserting results
    $this->assertEquals("(c)", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.6.1"));
    try {
        $this->assertTrue($this->isTextPresent("(c)"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    $this->assertEquals("2.84", $this->getTable("//tr[2]/td[2]/table.1.1"));
    $this->assertEquals("2.52", $this->getTable("//tr[2]/td[2]/table.2.1"));
    $this->click("link=Dep #1");
    $this->waitForPageToLoad("30000");
    
    //	Delete test mix
    $this->click("//input[@name='item_0' and @value='".$mixID."']");
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    //	Confirm deletion    
    $this->assertEquals("mix_test_updated", $this->getTable("//form/table.2.1"));    
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Mix deleted
    $this->assertEquals("No mixes in choosen department", $this->getTable("//table[3]/tbody/tr[2]/td/table.0.0"));
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Add one more mix
    $this->type("description", "one_more_test_mix");
    $this->type("creationTime", "09-01-2009");
    $this->type("wasteValue", "30");
    $this->select("selectWasteUnittypeClass", "label=USA liquid");
    $this->click("//option[@value='USALiquid']");
    $this->select("selectWasteUnittype", "label=fl oz");
    $this->select("selectProduct", "label=regexp:CRC\\s+25N13832\\s+PMS\\s8004C\\sMETALLIC");
    $this->click("//option[@value='186']");
    $this->select("selectUnittype", "label=fl oz");
    $this->type("quantity", "40");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    //	Assert added product
    $this->assertEquals("CRC", $this->getTable("//div[2]/table.1.1"));
    $this->assertEquals("25N13832", $this->getTable("//div[2]/table.1.2"));
    $this->assertEquals("PMS 8004C METALLIC", $this->getTable("//div[2]/table.1.3"));
    $this->assertEquals("40", $this->getTable("//div[2]/table.1.4"));
    $this->assertEquals("fl oz", $this->getTable("//div[2]/table.1.5"));
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	Assert mix list and voc gauge
    $this->assertEquals("one_more_test_mix", $this->getText("//tr[2]/td[3]/a/div"));
    $this->assertEquals("0/100.00", $this->getTable("//div/table.0.3"));
    $this->click("//tr[2]/td[3]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert mix details
    $this->assertEquals("09-01-2009", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.4.1"));
    $this->assertEquals("05/21/2010 4:09PM", $this->getTable("//div[2]/table/tbody/tr[2]/td[1]/table.5.1"));
    $this->assertEquals("0.23", $this->getTable("//tr[2]/td[2]/table.1.1"));
    $this->assertEquals("0.05", $this->getTable("//tr[2]/td[2]/table.2.1"));
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	Go back to mix list
    $this->click("link=Dep #1");
    $this->waitForPageToLoad("30000");
    
    //	Delete mix
    $mixID = $mixID + 1;
    $this->click("//input[@name='item_0' and @value='".$mixID."']");
    $this->click("//input[@name='action' and @value='deleteItem']");
    $this->waitForPageToLoad("30000");
    
    //	Confirm deletion
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Mix list without test mixes and logout
    $this->assertEquals("No mixes in choosen department", $this->getTable("//table[3]/tbody/tr[2]/td/table.0.0"));
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
  
  private function inventoryOperations () {
  	//	Go to inventory
    $this->click("//td[2]/a/div/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert inventory list
    $this->assertEquals("CARDINAL 211", $this->getText("//tr[2]/td[2]/a/div"));
    $this->assertEquals("robot Z", $this->getText("//tr[2]/td[3]/a/div"));
    $this->assertEquals("z-z-zzz [in equipment]", $this->getText("//tr[2]/td[4]/a/div"));
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Add inventory
    $this->type("inventory_name", "test inventory");
    $this->type("inventory_desc", "fishy fish");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    $this->type("quantity_0", "45");
    $this->type("CSuse_0", "5");
    $this->type("locationStorage_0", "home");
    $this->type("locationUse_0", "street");
    $this->select("selectProduct", "label=regexp:CARDINAL\\s+6SLVH\\s+HARDENER");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    $this->click("product_id_1");
    $this->select("selectProduct", "label=regexp:CRC\\s+25S15334\\s+PEARL\\sBASECOAT");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    $this->type("quantity_1", "13");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	Assert inventory list 
    $this->assertEquals("Inventory test inventory was successfully added", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("test inventory", $this->getText("//tr[3]/td[3]/a/div"));
    $this->assertEquals("fishy fish", $this->getText("//tr[3]/td[4]/a/div"));
    $this->click("//tr[3]/td[2]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	View details
    $this->assertEquals("ALSA", $this->getText("//td/table/tbody/tr[4]/td[1]"));
    $this->assertEquals("DT-101", $this->getText("//tr[4]/td[2]"));
    $this->assertEquals("DYNATONE CRYSTAL CLEAR", $this->getText("//tr[4]/td[3]"));
    $this->assertEquals("45.000", $this->getText("//tr[4]/td[4]"));
    $this->assertEquals("0.000", $this->getText("//tr[4]/td[5]"));
    $this->assertEquals("5.000", $this->getText("//tr[4]/td[6]"));
    $this->assertEquals("home", $this->getText("//tr[4]/td[7]"));
    $this->assertEquals("street", $this->getText("//tr[4]/td[8]"));
    $this->assertEquals("CRC", $this->getText("//tr[5]/td[1]"));
    $this->assertEquals("25S15334", $this->getText("//tr[5]/td[2]"));
    $this->assertEquals("PEARL BASECOAT", $this->getText("//tr[5]/td[3]"));
    $this->assertEquals("13.000", $this->getText("//tr[5]/td[4]"));
    $this->assertEquals("0.000", $this->getText("//tr[5]/td[5]"));
    $this->assertEquals("0.000", $this->getText("//tr[5]/td[6]"));
    $this->click("//input[@value='Edit']");
    $this->waitForPageToLoad("30000");
    
    //	Edit
    $this->type("inventory_name", "updated test inventory");
    $this->type("locationStorage_1", "somewhere");
    $this->type("locationUse_1", "somewhere");
    $this->type("locationUse_0", "");
    $this->type("locationStorage_0", "");
    $this->select("selectProduct", "label=regexp:CRC\\s+13K4424\\s+WATERBASE\\sTO\\sBLACK");
    $this->click("save");
    $this->waitForPageToLoad("30000");
    
    $this->type("inventory_name", "");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    $this->assertEquals("Error!", $this->getText("//div[2]/span"));
    $this->type("inventory_name", "updated test inventory");
    $this->click("//input[@name='save' and @value='Save']");
    $this->waitForPageToLoad("30000");
    
    //	View details
    $this->assertEquals("Inventory updated test inventory was successfully edited", $this->getText("//table[2]/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("updated test inventory", $this->getText("//div[2]/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("ALSA", $this->getText("//td/table/tbody/tr[4]/td[1]"));
    $this->assertEquals("DT-101", $this->getText("//tr[4]/td[2]"));
    $this->assertEquals("DYNATONE CRYSTAL CLEAR", $this->getText("//tr[4]/td[3]"));
    $this->assertEquals("45.000", $this->getText("//tr[4]/td[4]"));
    $this->assertEquals("0.000", $this->getText("//tr[4]/td[5]"));
    $this->assertEquals("5.000", $this->getText("//tr[4]/td[6]"));
    $this->assertEquals("", $this->getText("//tr[4]/td[7]"));
    $this->assertEquals("", $this->getText("//tr[4]/td[8]"));
    $this->assertEquals("CRC", $this->getText("//tr[5]/td[1]"));
    $this->assertEquals("13K4424", $this->getText("//tr[5]/td[2]"));
    $this->assertEquals("WATERBASE TO BLACK", $this->getText("//tr[5]/td[3]"));
    $this->assertEquals("0.000", $this->getText("//tr[5]/td[4]"));
    $this->assertEquals("0.000", $this->getText("//tr[5]/td[5]"));
    $this->assertEquals("0.000", $this->getText("//tr[5]/td[6]"));
    $this->assertEquals("", $this->getText("//tr[5]/td[7]"));
    $this->assertEquals("", $this->getText("//tr[5]/td[8]"));
    $this->assertEquals("CRC", $this->getText("//tr[6]/td[1]"));
    $this->assertEquals("25S15334", $this->getText("//tr[6]/td[2]"));
    $this->assertEquals("PEARL BASECOAT", $this->getText("//tr[6]/td[3]"));
    $this->assertEquals("13.000", $this->getText("//tr[6]/td[4]"));
    $this->assertEquals("0.000", $this->getText("//tr[6]/td[5]"));
    $this->assertEquals("0.000", $this->getText("//tr[6]/td[6]"));
    $this->assertEquals("somewhere", $this->getText("//tr[6]/td[7]"));
    $this->assertEquals("somewhere", $this->getText("//tr[6]/td[8]"));
    $this->click("//input[@value='Delete']");
    $this->waitForPageToLoad("30000");
    
    //	Delete
    $this->assertEquals("You are about to delete inventory updated test inventory. Are you sure?",$this->getText("//div/div/div/div"));
    $this->click("confirm");
    $this->waitForPageToLoad("30000");
    
    //	Inventory list
    $this->assertEquals("Inventory updated test inventory was successfully deleted", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
    $this->assertEquals("CARDINAL 211", $this->getText("//tr[2]/td[2]/a/div"));
    $this->assertEquals("robot Z", $this->getText("//tr[2]/td[3]/a/div"));
    $this->assertEquals("z-z-zzz [in equipment]", $this->getText("//tr[2]/td[4]/a/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
  
  private function productOperations() {
  	//	Go to products
    $this->click("//a/div/div");
    $this->waitForPageToLoad("30000");
    
     //	Click on last page
    $this->click("link=9");
    $this->waitForPageToLoad("30000");
    
    //	Click on product ST2010
    $this->click("//tr[5]/td[4]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert product
    $this->assertEquals("ST2010", $this->getText("//tr[2]/td/table/tbody/tr[1]/td[2]/div"));
    $this->assertEquals("ALSA SOFT CLEAR RUBBER COATING", $this->getText("//tr[2]/td/table/tbody/tr[2]/td[2]/div"));
    $this->assertEquals("5.82", $this->getText("//tr[4]/td[2]/div"));
    $this->assertEquals("5.82", $this->getText("//tr[5]/td[2]/div"));
    $this->assertEquals("0.00", $this->getText("//tr[6]/td[2]/div"));
    $this->assertEquals("SOFT CLEAR-COAT RUBBER", $this->getText("//tr[7]/td[2]/div"));
    $this->assertEquals("no", $this->getText("//tr[8]/td[2]/div"));
    $this->assertEquals("no", $this->getText("//tr[9]/td[2]/div"));
    $this->assertEquals("1.14", $this->getText("//tr[10]/td[2]/div"));
    $this->assertEquals("FL-IB; IRR; OHH; SENS;", $this->getText("//tr[11]/td[2]/div"));
    $this->assertEquals("from 167.00 to 302.00", $this->getText("//tr[12]/td[2]/div"));
    $this->assertEquals("ALSA", $this->getText("//tr[13]/td[2]/div"));
    $this->assertEquals("123-42-2", $this->getText("//tr[3]/td[1]/div"));
    $this->assertEquals("DIACETONE ALCOHOL", $this->getText("//tr[3]/td/table/tbody/tr[3]/td[2]/div"));
    $this->assertEquals("0.00", $this->getText("//tr[3]/td[3]/div"));
    $this->assertEquals("0", $this->getText("//tr[3]/td[4]/div"));
    $this->assertEquals("3.00 %", $this->getText("//tr[3]/td[5]/div"));
    $this->assertEquals("1330-20-7", $this->getText("//tr[4]/td[1]/div"));
    $this->assertEquals("XYLENE", $this->getText("//tr[3]/td/table/tbody/tr[4]/td[2]"));
    $this->assertEquals("0.00", $this->getText("//tr[4]/td[3]/div"));
    $this->assertEquals("0", $this->getText("//tr[4]/td[4]/div"));
    $this->assertEquals("25.00 %", $this->getText("//tr[4]/td[5]/div"));
    $this->assertEquals("123-86-4", $this->getText("//tr[5]/td[1]/div"));
    $this->assertEquals("N-BUTYL ACETATE", $this->getText("//tr[3]/td/table/tbody/tr[5]/td[2]/div"));
    $this->assertEquals("0.00", $this->getText("//tr[5]/td[3]/div"));
    $this->assertEquals("0", $this->getText("//tr[5]/td[4]/div"));
    $this->assertEquals("8.00 %", $this->getText("//tr[5]/td[5]/div"));
    $this->assertEquals("111-15-9", $this->getText("//tr[6]/td[1]/div"));
    $this->assertEquals("GLYCOL ETHER ACETATE", $this->getText("//tr[3]/td/table/tbody/tr[6]/td[2]/div"));
    $this->assertEquals("0.00", $this->getText("//tr[6]/td[3]/div"));
    $this->assertEquals("0", $this->getText("//tr[6]/td[4]/div"));
    $this->assertEquals("3.00 %", $this->getText("//tr[6]/td[5]/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
  
  private function ruleListOperations($level = 'department') {
  	//	Click settings
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Select only rule 219
    $this->click("//a/h2");
    $this->click("link=None");
    $this->click("//input[@name='ruleID' and @value='1']");
    $this->click("//input[@value='Save']");
    
    switch ($level) {
    	case 'department':
    		$this->click("link=Dep #1");
    		$this->waitForPageToLoad("30000");
    		break;
    		
    	case 'facility':
    	  	$this->click("//input[@value='View']");
    		$this->waitForPageToLoad("30000");
    		
    		$this->click("link=Dev Facility");
    		$this->waitForPageToLoad("30000");
    		//	Go to department
  	 		$this->click("//td[3]/a/div");
  	 		$this->waitForPageToLoad("30000");
    		break;
    }        
    
    //	Mix list
    $this->click("action");
    $this->waitForPageToLoad("30000");
    
    //	Add mix
    $this->assertEquals("219 - Equipment not requiring a written permit pursuant to regulation II.", $this->getText("rule"));
    $this->click("//input[@value='Settings']");
    $this->waitForPageToLoad("30000");
    
    //	Settings select all
    $this->click("//a/h2");
    $this->click("link=All");
    $this->click("//input[@value='Save']");
    $this->click("link=Dep #1");
    $this->waitForPageToLoad("30000");
    
    //	mix list
    $this->click("action");    
    $this->waitForPageToLoad("30000");
    $this->assertEquals("219 - Equipment not requiring a written permit pursuant to regulation II. 1171 - Solvent Cleaning Operations 1168 - Adhesive Applications 1164 - Semiconductor Manufacturing 1151 - Motor Vehicle and Mobile Equipment Non-Assembly Line Coating Operations 1145 - Plastic, Rubber, and Glass Coatings 1136 - Wood Products Coatings 1130.1 - Screen Printing Operations 1130 - Graphic Arts 1128 - Paper, Fabric, and Film Coating Operations 1126 - Magnet Wire Coating Operations 1125 - Metal Container, Closure, and Coil Coating Operations 1124 - Aerospace Assembly and Component Manufacturing Operations 1122 - Solvent Degreasers 1115 - Motor Vehicle Assembly Line Coating Operations 1107 - Coating of Metal Parts and Products 1106.1 - Pleasure Craft Coating Operations 1106 - Marine Coating Operations 1104 - Wood Flat Stock Coating Operations 1102 - Petroleum Solvent Dry Cleaners", $this->getText("rule"));
    
    //	logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");	
  }
  
  
  
  
  private function equipmentViewOperations() {
  	//	Go to equipment tab
    $this->click("//td[3]/a/div/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert equipment list
    $this->assertEquals("test1", $this->getText("//table[4]/tbody/tr[2]/td[3]"));
    $this->assertEquals("100.00", $this->getText("//div[1]/b"));
    $this->click("//tr[2]/td[2]/a/div");
    $this->waitForPageToLoad("30000");
    
    //	Assert equipment Details
    $this->assertEquals("test1", $this->getText("//div/b"));
    $this->assertEquals("robot Z", $this->getText("//tr[4]/td[2]/div"));
    $this->assertEquals("blabla", $this->getText("//tr[5]/td[2]/div"));
    $this->assertEquals("05/21/2010 4:09PM", $this->getText("//tr[6]/td[2]/div"));
    $this->assertEquals("1.000", $this->getText("//tr[7]/td[2]/div"));
    $this->assertEquals("yes", $this->getText("//tr[8]/td[2]/div"));
    $this->assertEquals("yes", $this->getText("//tr[9]/td[2]/div"));
    $this->assertEquals("Not Expired", $this->getText("//tr[10]/td[2]/div"));
    
    //	Logout
    $this->click("//input[@value=' Logout ']");
    $this->waitForPageToLoad("30000");
  }
  
  
  
  
  private function equipmentOperations($level = 'facility') {
  	//	Go to ewquipment
  	 $this->click("//td[3]/a/div/div");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Add equipment
  	 $this->click("action");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Set equipment details  	
  	 $this->type("equip_desc", "testTest");
  	 $this->type("calendar1", "");
  	 $this->click("link=31");
  	 $setEquipmentTime = date('g:iA');
  	 $this->type("permit", "yes");
  	 $this->type("daily", "0");
  	 $this->click("save");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	equipment list
  	 $this->assertEquals("You cannot add new equipment according to your Billing Plan", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
  	 $this->assertEquals("testTest", $this->getText("//tr[3]/td[3]/a/div"));
  	 $this->click("//tr[3]/td[3]/a/div");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	view equipment
  	 $this->assertEquals("testTest", $this->getText("//div/b"));
  	 $this->assertEquals("robot Z", $this->getText("//tr[4]/td[2]/div"));
  	 $this->assertEquals("yes", $this->getText("//tr[5]/td[2]/div"));
  	 $this->assertEquals("12/31/2009 ".$setEquipmentTime, $this->getText("//tr[6]/td[2]/div"));
  	 $this->assertEquals("0.000", $this->getText("//tr[7]/td[2]/div"));
  	 $this->assertEquals("yes", $this->getText("//tr[8]/td[2]/div"));
  	 $this->assertEquals("yes", $this->getText("//tr[9]/td[2]/div"));
  	 $this->assertEquals("Expired", $this->getText("//tr[10]/td[2]/div"));
  	 $this->click("//input[@value='Edit']");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Edit equipment
  	 $this->assertEquals("", $this->getText("equip_desc"));
  	 $this->assertEquals("robot Z", $this->getText("selectInventoryID"));
  	 $this->assertEquals("", $this->getText("daily"));
  	 $this->assertEquals("", $this->getText("inventoryDescription"));
  	 $this->type("equip_desc", "");
  	 $this->click("save");
  	 $this->waitForCondition("var value = selenium.getText(\"//div[@id='notifyContainer']/table/tbody/tr[1]/td/div/div/div/div\"); value == \"Errors on form\";", "3000");
  	 
  	 //	OMG! Errors!
  	// $this->assertEquals("There are errors in the form\nCorrect them please!", $this->getText("//div/div/div/div"));
  	 $this->type("equip_desc", "testUpdated");
  	 $this->click("save");
  	 $this->waitForCondition("var value = selenium.getText(\"//div[@id='notifyContainer']/table/tbody/tr[1]/td/div/div/div/div\"); value == \"Saved\";", "3000");
  	 
  	 //	equipment details
  	 //$this->assertEquals("Equipment testUpdated was successfully edited", $this->getText("//table[2]/tbody/tr[1]/td/div/div/div/div"));
  	 //$this->assertEquals("testUpdated", $this->getText("//td[2]/div/b"));
  	 
  	 
  	 //	click Dep1
  	 switch ($level) {
  	 	case "facility":
  	 		$this->click("//a[2]");	
  	 		break;
  	 	case "super":
  	 		$this->click("//a[4]");
  	 }
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	click equipment
  	 $this->click("//td[3]/a/div/div");
     $this->waitForPageToLoad("30000");
     
  	 $this->assertEquals("You cannot add new equipment according to your Billing Plan", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
  	 $this->click("item_1");
  	 $this->click("//input[@name='action' and @value='deleteItem']");
  	 $this->waitForPageToLoad("30000");
  
  	 //	Delete equipment
  	 $this->assertEquals("You are about to delete equipment testUpdated. Are you sure?", $this->getText("//div/div/div/div"));
  	 $this->click("confirm");
  	 $this->waitForPageToLoad("30000");
  	 
  	 //	Equipment list
  	 $this->assertEquals("Equipment testUpdated was successfully deleted", $this->getText("//td/table/tbody/tr[1]/td/div/div/div/div"));
  	 $this->assertEquals("test1", $this->getText("//tr[2]/td[3]/a/div"));
  	 
  	 //	Logout
  	 $this->click("//input[@value=' Logout ']");
  	 $this->waitForPageToLoad("30000");
  } 
}
?>
