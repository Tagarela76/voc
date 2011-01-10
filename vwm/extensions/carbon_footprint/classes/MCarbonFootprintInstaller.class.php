<?php
require_once "extensions/iInstaller.php";

class MCarbonFootprintInstaller implements iInstaller {
	
	private $db;
	public $errors;

    function MCarbonFootprintInstaller($db) {
    	$this->db = $db;
    }
    
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM '.TB_CARBON_EMISSIONS.', '.TB_EMISSION_FACTOR.', '.TB_CARBON_FOOTPRINT.' LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'carbon_footprint\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	//create table carbon_emissions
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_CARBON_EMISSIONS."` (  " .
    			"`id` int(11) NOT NULL auto_increment,  " .
		  		"`emission_factor_id` int(11) NOT NULL,  " .
		  		"`description` varchar(225) NOT NULL,  " .
		  		"`adjustment` decimal(20,4) NOT NULL default '0.0000',  " .
		  		"`quantity` decimal(20,4) NOT NULL default '0.0000',  " .
		  		"`unittype_id` int(11) NOT NULL,  " .
		  		"`tco2` decimal(20,4) NOT NULL default '0.0000',  " .
		  		"`facility_id` int(11) NOT NULL,  " .
		  		"`month` tinyint(2) NOT NULL,  " .
		  		"`year` year(4) NOT NULL,  " .
		  		"`certificate_value` decimal(20,4) NOT NULL default '0.0000',  " .
		  		"`credit_value` decimal(20,4) NOT NULL default '0.0000',  " .
		  		"PRIMARY KEY  (`id`)" .
		  		") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;  ";
		$this->db->query($query);
		//create table carbon footprint
		$query = "CREATE TABLE IF NOT EXISTS `".TB_CARBON_FOOTPRINT."` (  " .
				"`id` int(11) NOT NULL auto_increment,  " .
		  		"`facility_id` int(11) NOT NULL,  " .
		  		"`monthly_value` decimal(20,2) NOT NULL,  " .
		  		"`monthly_show` tinyint(1) NOT NULL,  " .
		  		"`annual_value` decimal(20,2) NOT NULL,  " .
		  		"`annual_show` tinyint(1) NOT NULL,  " .
		  		"PRIMARY KEY  (`id`)" .
		  		") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;  ";
		$this->db->query($query);
		//create table emission factor
		$query = "CREATE TABLE IF NOT EXISTS `".TB_EMISSION_FACTOR."` (  " .
				"`id` int(11) NOT NULL auto_increment,  " .
		  		"`name` varchar(120) NOT NULL,  " .
		  		"`unittype_id` int(11) NOT NULL,  " .
		  		"`emission_factor` decimal(20,4) NOT NULL,  " .
		  		"PRIMARY KEY  (`id`)" .
		  		") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;   ";
		$this->db->query($query);
		//insert emission factors
		$query = "INSERT IGNORE INTO `".TB_EMISSION_FACTOR."` (`id`, `name`, `unittype_id`, `emission_factor`) VALUES " .
				"(1, 'Aviation spirit', 5, 3128.0000)," .
				"(2, 'Aviation turbine fuel', 5, 3150.0000)," .
				"(3, 'Blast furnace gas', 34, 0.9700)," .
				"(4, 'Burning oil/kerosene/paraffin', 4, 2.5180)," .
				"(5, 'Coke oven gas', 34, 0.1500)," .
				"(6, 'Coking coal', 5, 2810.0000)," .
				"(7, 'Colliery methane', 34, 0.1800)," .
				"(8, 'Diesel', 4, 2.6300)," .
				"(9, 'Fuel oil', 5, 3223.0000)," .
				"(10, 'Gas oil', 4, 2.6740)," .
				"(11, 'Industrial coal', 5, 2457.0000)," .
				"(12, 'Liquid petroleum gas (LPG)', 4, 1.4950)," .
				"(13, 'Lubricants', 5, 3171.0000)," .
				"(14, 'Waste', 5, 275.0000)," .
				"(15, 'Naphtha', 5, 3131.0000)," .
				"(16, 'Natural gas', 34, 0.1850)," .
				"(17, 'Other petroleum gas', 34, 0.2100)," .
				"(18, 'Petrol', 4, 2.3150)," .
				"(19, 'Petroleum coke', 5, 3410.0000)," .
				"(20, 'Refinery miscellaneous', 34, 0.2450)," .
				"(21, 'Scrap tyres', 5, 2003.0000)," .
				"(22, 'Solid smokeless fuel', 5, 2810.0000)," .
				"(23, 'Sour gas', 34, 0.2400)," .
				"(24, 'Waste solvents', 5, 1597.0000)," .
				"(25, 'Electricity', 34, 0.5370);  ";
		$this->db->query($query);
		//check if carbon_footprint exists in table module
		$query = "SELECT * FROM module WHERE name = 'carbon_footprint' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('carbon_footprint')";
			$this->db->query($query);
		}
    }
    
    public function check() {
    	$classlist = array( "CarbonEmissions.class.php",
    			"CarbonFootprint.class.php",
    			"EmissionFactor.class.php",
				"MCarbonFootprint.class.php",
				"MCarbonFootprintInstaller.class.php"
			);
    	$templatelist = array("addDirectEmission.tpl",
				"addEmissionFactorClass.tpl",
				"bookmark.tpl",
				"carbonFootprintView.tpl",
				"editIndirectEmission.tpl",
				"emissionFactor.tpl",
				"monthlyTco2Indicator.tpl",
				"setLimit.tpl",
				"viewEmissionFactor.tpl",
				"yearlyTco2Indicator.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/carbon_footprint/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/carbon_footprint/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
    	return $validation;
    }
}
?>