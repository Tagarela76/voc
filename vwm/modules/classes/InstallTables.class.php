<?php

class InstallTables {

    function InstallTables($link, $dbName) {
    	$this->dbLink=$link;
    	$this->dbName=$dbName;    
    	mysql_select_db($this->dbName,$this->dbLink) or die(mysql_error());  	   	
    }
    
    private $dbLink;
    private $dbName;
        
    public function createAllTables($update)
    {    	
    	//mysql_query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';",$this->dbLink)or die(mysql_error());
    	$this->createLimit2userTable();
    	$this->createLimitesTable(); 
    	$this->createNotifytimeTable();
    	$this->createModuleTable();
    	$this->createChemicalclassTable();    	
    	$this->createProduct2chemicalclassTable();
    	$this->createProduct2companyTable();
    	$this->createTrashbinTable();
    	$this->createCompanyTable();
    	$this->createFacilityTable();
    	$this->createDepartmentTable();
    	$this->createUserTable();
    	$this->createCountryTable();    	
    	$this->createStateTable();    	
    	//$this->createInventoryTable();
    	$this->createEquipmentTable();
    	$this->createProductTable();
    	$this->createComponentTable();
    	$this->createDensityTable();    	
    	$this->createUnittypeTable();    	
    	$this->createRuleTable();
    	$this->createCoatTable();
    	$this->createSubstrateTable();
    	$this->createApmethodTable();
    	$this->createSupplierTable();
    	$this->createTypeTable();    	
    	$this->createProductgroupTable();
    	$this->createMixTable();
    	$this->createMixgroupTable();
    	$this->createComponentsgroupTable();
    	$this->createAgencyTable();
    	$this->createAgencybelongTable();
    	$this->createHazardousclassTable();
    	$this->createGcglistTable();
    	$this->createIssueTable();
    	$this->createSelectedruleslistTable();
    	$this->createAccessoryTable();
    	//$this->createAccessory2inventoryTable();
    	//$this->createMaterial2inventoryTable();
    	//$this->createUselocation2materialTable();
    	$this->createDefaultTable();
    	$this->createUnitclassTable();    	
    	$this->createWasteTable();
    	$this->createVpsbillingTable();
    	$this->createVpsconfigTable();
    	$this->createVpscustomerTable();
    	$this->createVpscustomerlimitTable();
    	$this->createVpsdeactivationTable();
    	$this->createVpsdefinedbprequestTable();
    	$this->createVpsinvoiceTable();
    	$this->createVpslimitTable();
    	$this->createVpslimitpriceTable();
    	$this->createVpsnotificationscriptTable();
    	$this->createVpspaymentTable();
    	$this->createVpsschedulecustomerplanTable();
    	$this->createVpsschedulelimitTable();
    	$this->createVpsuserTable();    	
    	$this->createVpspaymentmethodTable();
    	$this->createGaclaclTable();    	
    	$this->createGaclaclsectionsTable();    	
    	$this->createGaclaclseqTable();    	
    	$this->createGaclacoTable();    	
    	$this->createGaclacomapTable();    	
    	$this->createGaclacosectionsTable();    	
    	$this->createGaclacosectionsseqTable();    	
    	$this->createGaclacoseqTable();    	
    	$this->createGaclaroTable();    	
    	$this->createGaclarogroupsTable();    	
    	$this->createGaclarogroupsidseqTable();    	
    	$this->createGaclarogroupsmapTable();    	
    	$this->createGaclaromapTable();
    	$this->createGaclarosectionsTable();    	
    	$this->createGaclarosectionsseqTable();    	
    	$this->createGaclaroseqTable();    	
    	$this->createGaclaxoTable();    	
    	$this->createGaclaxogroupsTable();
    	$this->createGaclaxogroupsmapTable();
    	$this->createGaclaxomapTable();    	
    	$this->createGaclaxosectionsTable();    	
    	$this->createGaclaxosectionsseqTable();    	
    	$this->createGaclaxoseqTable();    	
    	$this->createGaclgroupsaromapTable();    	
    	$this->createGaclgroupsaxomapTable();
    	$this->createGaclphpgaclTable();    	
    	
    	//$this->insertIntoUserTable();
    	$this->insertIntoStateTable ();
    	$this->insertIntoDensityTable();
    	$this->insertIntoUnittypeTable();
    	$this->insertIntoTypeTable();
    	$this->insertIntoUnitclassTable();
    	
    	if ($update==false)
    	{
    		$this->insertIntoChemicalclassTable();
    		$this->insertIntoCountryTable();
    		$this->insertIntoGaclaclTable();
    		$this->insertIntoGaclaclsectionsTable();
    		$this->insertIntoGaclaclseqTable();
    		$this->insertIntoGaclacoTable();
    		$this->insertIntoGaclacomapTable();
    		$this->insertIntoGaclacosectionsTable();
    		$this->insertIntoGaclacosectionsseqTable();
    		$this->insertIntoGaclacoseqTable();
    		//$this->insertIntoGaclaroTable();
    		$this->insertIntoGaclarogroupsTable();
    		$this->insertIntoGaclarogroupsidseqTable();
    		$this->insertIntoGaclarogroupsmapTable();
    		$this->insertIntoGaclarosectionsTable();
    		$this->insertIntoGaclarosectionsseqTable();
    		$this->insertIntoGaclaxomapTable();
    		$this->insertIntoGaclaroseqTable();
    		$this->insertIntoGaclaxoTable();
    		$this->insertIntoGaclaxosectionsTable();
    		$this->insertIntoGaclaxosectionsseqTable();
    		$this->insertIntoGaclaxoseqTable();
    		//$this->insertIntoGaclgroupsaromapTable();
    		$this->insertIntoGaclphpgaclTable();
    		
    	}    		
    }
    
    public function createLimit2userTable()
    {
    	$query="CREATE TABLE IF NOT EXISTS `limit2user` (
				  `id` int(11) NOT NULL auto_increment,
				  `user_id` int(11) NOT NULL,
				  `limit_id` int(11) NOT NULL,
				  `on_off` tinyint(1) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
				
		mysql_query($query,$this->dbLink)or die(mysql_error()); 		
    }
    
    public function createLimitesTable()
    {
    	$query="CREATE TABLE IF NOT EXISTS `limites` (
			  `limit_id` int(11) NOT NULL auto_increment,
			  `limit_name` varchar(25) NOT NULL,
			  PRIMARY KEY  (`limit_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			
		mysql_query($query,$this->dbLink)or die(mysql_error()); 
    }
    
    public function createNotifytimeTable()
    {
    	$query= "CREATE TABLE IF NOT EXISTS `notify_time` (
				  `id` int(11) NOT NULL auto_increment,
				  `user_id` int(11) NOT NULL,
				  `login_time` varchar(15) default NULL,
				  `notify_time` varchar(15) default NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";
				
		mysql_query($query,$this->dbLink)or die(mysql_error()); 
    }
    
    public function createModuleTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `module` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(64) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}    
        
    public function createChemicalclassTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `chemical_class` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(64) NOT NULL,
				  `description` varchar(120) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	 public function insertIntoChemicalclassTable ()
	{		
		$query="INSERT IGNORE INTO `chemical_class` (`id`, `name`, `description`) VALUES
				(1, 'IRR', 'Irritant'),(2, 'OHH', 'Other Health Hazard'),
				(3, 'SENS', 'Sensitizer'),(4, 'EX', 'Explosives'),
				(5, 'CorCG', 'Corrosive compressed gas'),(6, 'HtoxCG', 'High Toxic Compressed Gas'),
				(7, 'ToxCG', 'Toxic Compressed Gas'),(8, 'ICG', 'Inert compressed Gas'),
				(9, 'OxCG', 'Oxidizing Compressed Gas'),(10, 'FLG', 'Flammable Compressed Gas'),
				(11, 'LPG', 'Liquefied Petroleum Gas'),	(12, 'FLS', 'Flammable Solids'),
				(13, 'OP-1', 'Organic Peroxides Class I'),(14, 'OP-2', 'Organic Peroxides Class II'),
				(15, 'OP-3', 'Organic Peroxides Class III'),(16, 'OP-4', 'Organic Peroxides Class IV'),
				(17, 'OP-5', 'Organic Peroxides Class V'),(18, 'OXY-4', 'Oxidizers Class 4'),
				(19, 'OXY-3', 'Oxidizers Class 3'),	(20, 'OXY-2', 'Oxidizers Class 2'),
				(21, 'OXY-1', 'Oxidizers Class 1'),(22, 'PYRO', 'Pyrophoric Materials'),
				(23, 'UR-4', 'Unstable (Reactive) Class 4'),(24, 'UR-3', 'Unstable (Reactive) Class 3'),
				(25, 'UR-2', 'Unstable (Reactive) Class 2'),(26, 'UR-1', 'Unstable (Reactive) Class 1'),
				(27, 'WR-3', 'Water-Reactive Class 3'),	(28, 'WR-2', 'Water-Reactive Class 2'),
				(29, 'WR-1', 'Water-Reactive Class 1'),	(30, 'CRY', 'Cryogenic Fluids'),
				(31, 'HTOX', 'Highly Toxic materials'),	(32, 'COR', 'Corrosive'),
				(33, 'AERO-1', 'Aerosols Class 1'),	(34, 'AERO-2', 'Aerosols Class 2'),
				(35, 'AERO-3', 'Aerosols Class 3'),(36, 'RAD', 'Radioactive Material'),
				(37, 'CAR', 'Carcinogen'),(38, '1C', NULL),	(39, 'CL-II', NULL),(40, 'CL-IIIA', NULL),
				(41, 'CL-IIIB', NULL),(42, 'FL-1B', NULL),(43, 'FL-IA', NULL),(44, 'FL-IB', NULL),
				(45, 'FL-IC', NULL),(46, 'IB', NULL),(47, 'FL-1C', NULL),(48, 'FL-1A', NULL),
				(49, 'UK', NULL),(50, 'UNDEFINED', NULL);";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createProduct2chemicalclassTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `product2chemical_class` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `product_id` int(11) NOT NULL,
				  `chemical_class_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createProduct2companyTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `product2company` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `product_id` int(11) NOT NULL,
				  `company_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createTrashbinTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `trash_bin` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `table_name` varchar(32) NOT NULL,
				  `data` text NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `CRUD` varchar(1) NOT NULL DEFAULT 'U',
				  `date` int(11) NOT NULL,
				  `referrer` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
    
    public function createCompanyTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `company` (
		  `company_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(96) DEFAULT NULL,
		  `address` varchar(384) DEFAULT NULL,
		  `city` varchar(192) DEFAULT NULL,
		  `zip` varchar(32) DEFAULT NULL,
		  `county` varchar(192) DEFAULT NULL,
		  `state` varchar(96) DEFAULT NULL,
		  `phone` varchar(32) DEFAULT NULL,
		  `fax` varchar(32) DEFAULT NULL,
		  `email` varchar(128) DEFAULT NULL,
		  `contact` varchar(384) DEFAULT NULL,
		  `title` varchar(192) DEFAULT NULL,
		  `creater_id` int(11) DEFAULT NULL,
		  `country` int(3) unsigned DEFAULT NULL,
		  `gcg_id` int(11) NOT NULL,
		  `trial_end_date` date DEFAULT NULL,
		  `voc_unittype_id` int(11) NOT NULL DEFAULT '2',
		  PRIMARY KEY (`company_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createFacilityTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `facility` (
				  `facility_id` int(11) NOT NULL auto_increment,
				  `company_id` int(11) NOT NULL default '0',
				  `name` varchar(96) default '0',
				  `epa` varchar(75) default NULL,
				  `address` varchar(196) default NULL,
				  `city` varchar(196) default NULL,
				  `zip` varchar(48) default NULL,
				  `county` varchar(96) default NULL,
				  `state` varchar(96) default NULL,
				  `country` int(3) default NULL,
				  `phone` varchar(32) default NULL,
				  `fax` varchar(32) default NULL,
				  `email` varchar(128) default NULL,
				  `contact` varchar(96) default NULL,
				  `title` varchar(96) default NULL,
				  `creater_id` int(11) default NULL,
				  `voc_limit` decimal(20,2) NOT NULL,
				  `voc_annual_limit` decimal(20,2) NOT NULL,
				  `gcg_id` int(11) NOT NULL,
				  PRIMARY KEY  (`facility_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
		
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createDepartmentTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `department` (
				  `department_id` int(11) NOT NULL auto_increment,
				  `name` varchar(64) default '0',
				  `facility_id` int(11) default NULL,
				  `creater_id` int(11) default NULL,
				  `voc_limit` decimal(20,2) NOT NULL,
				  `voc_annual_limit` decimal(20,2) NOT NULL,
				  PRIMARY KEY  (`department_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createUserTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `user` (
				  `user_id` int(11) NOT NULL auto_increment,
				  `username` varchar(250) default '0',
				  `accessname` varchar(64) default '0',
				  `password` varchar(256) default '0',
				  `phone` varchar(32) default '0',
				  `mobile` varchar(32) default '0',
				  `email` varchar(256) default '0',
				  `accesslevel_id` int(1) default '0',
				  `company_id` int(11) default '0',
				  `facility_id` int(11) default '0',
				  `department_id` int(11) default '0',
				  `grace` int(3) default '0',
				  `creater_id` int(11) default '0',
				  PRIMARY KEY  (`user_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	/*public function insertIntoUserTable()
	{		
		$query="INSERT IGNORE INTO `user` (`user_id`, `username`, `accessname`, `password`, `phone`, `mobile`, `email`, `accesslevel_id`, `company_id`, `facility_id`, `department_id`, `grace`, `creater_id`) VALUES
				(121, 'root', 'root', 'eb9ced85d7963012f353789bd29ac2f9', '000000', '000000', 'root@root.root', 3, 0, 0, 0, 14, 0);";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}*/
	
	public function createCountryTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `country` (
				  `country_id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(32) DEFAULT NULL,
				  `date_type` varchar(30) DEFAULT NULL,
				  PRIMARY KEY (`country_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=225 ;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   	
	}
	
	public function insertIntoCountryTable ()
	{		
		$query="INSERT IGNORE INTO `country` (`country_id`, `name`, `date_type`) VALUES
				(1, 'Afghanistan', 'd-m-Y g:iA'),(2, 'Albania', 'd-m-Y g:iA'),(3, 'Algeria', 'd-m-Y g:iA'),(4, 'American Samoa', 'd-m-Y g:iA'),
				(5, 'Andorra', 'd-m-Y g:iA'),(6, 'Angola', 'd-m-Y g:iA'),(7, 'Anguilla', 'd-m-Y g:iA'),(8, 'Antigua and Barbuda', 'd-m-Y g:iA'),
				(9, 'Argentina', 'd-m-Y g:iA'),(10, 'Armenia', 'd-m-Y g:iA'),(11, 'Aruba', 'd-m-Y g:iA'),(12, 'Australia', 'd-m-Y g:iA'),
				(13, 'Austria', 'd-m-Y g:iA'),(14, 'Azerbaijan', 'd-m-Y g:iA'),	(15, 'Bahamas', 'd-m-Y g:iA'),(16, 'Bahrain', 'd-m-Y g:iA'),
				(17, 'Bangladesh', 'd-m-Y g:iA'),(18, 'Barbados', 'd-m-Y g:iA'),(19, 'Belarus', 'd-m-Y g:iA'),(20, 'Belgium', 'd-m-Y g:iA'),
				(21, 'Belize', 'd-m-Y g:iA'),(22, 'Benin', 'd-m-Y g:iA'),(23, 'Bermuda', 'd-m-Y g:iA'),	(24, 'Bhutan', 'd-m-Y g:iA'),
				(25, 'Bolivia', 'd-m-Y g:iA'),(26, 'Bosnia and Herzegovina', 'd-m-Y g:iA'),(27, 'Botswana', 'd-m-Y g:iA'),(28, 'Brazil', 'd-m-Y g:iA'),
				(29, 'British Virgin Islands', 'd-m-Y g:iA'),(30, 'Brunei Darussalam', 'd-m-Y g:iA'),(31, 'Bulgaria', 'd-m-Y g:iA'),
				(32, 'Burkina Faso', 'd-m-Y g:iA'),(33, 'Burundi', 'd-m-Y g:iA'),(34, 'Cambodia', 'd-m-Y g:iA'),(35, 'Cameroon', 'd-m-Y g:iA'),
				(36, 'Canada', 'm/d/Y g:iA'),(37, 'Cape Verde', 'd-m-Y g:iA'),(38, 'Cayman Islands', 'd-m-Y g:iA'),(39, 'Central African Republic', 'd-m-Y g:iA'),
				(40, 'Chad', 'd-m-Y g:iA'),(41, 'Chile', 'd-m-Y g:iA'),(42, 'China', 'd-m-Y g:iA'),(43, 'Colombia', 'd-m-Y g:iA'),(44, 'Comoros', 'd-m-Y g:iA'),
				(45, 'Cook Islands', 'd-m-Y g:iA'),(46, 'Costa Rica', 'd-m-Y g:iA'),(47, 'Croatia', 'd-m-Y g:iA'),(48, 'Cuba', 'd-m-Y g:iA'),
				(49, 'Cyprus', 'd-m-Y g:iA'),(50, 'Czech Republic', 'd-m-Y g:iA'),(51, 'Denmark', 'd-m-Y g:iA'),(52, 'Djibouti', 'd-m-Y g:iA'),
				(53, 'Dominica', 'd-m-Y g:iA'),(54, 'Dominican Republic', 'd-m-Y g:iA'),(55, 'Ecuador', 'd-m-Y g:iA'),(56, 'Egypt', 'd-m-Y g:iA'),
				(57, 'El Salvador', 'd-m-Y g:iA'),(58, 'Equatorial Guinea', 'd-m-Y g:iA'),(59, 'Eritrea', 'd-m-Y g:iA'),(60, 'Estonia', 'd-m-Y g:iA'),
				(61, 'Ethiopia', 'd-m-Y g:iA'),	(62, 'Faroe Islands', 'd-m-Y g:iA'),(63, 'Fiji', 'd-m-Y g:iA'),	(64, 'Finland', 'd-m-Y g:iA'),
				(65, 'France', 'd-m-Y g:iA'),(66, 'French Guiana', 'd-m-Y g:iA'),(67, 'French Polynesia', 'd-m-Y g:iA'),(68, 'Gabon', 'd-m-Y g:iA'),
				(69, 'Gambia', 'd-m-Y g:iA'),(70, 'Georgia', 'd-m-Y g:iA'),	(71, 'Germany', 'd-m-Y g:iA'),(72, 'Ghana', 'd-m-Y g:iA'),
				(73, 'Gibraltar', 'd-m-Y g:iA'),(74, 'Greece', 'd-m-Y g:iA'),(75, 'Greenland', 'd-m-Y g:iA'),(76, 'Grenada', 'd-m-Y g:iA'),
				(77, 'Guadeloupe', 'd-m-Y g:iA'),(78, 'Guam', 'd-m-Y g:iA'),(79, 'Guatemala', 'd-m-Y g:iA'),(80, 'Guernsey', 'd-m-Y g:iA'),
				(81, 'Guinea', 'd-m-Y g:iA'),(82, 'Guyana', 'd-m-Y g:iA'),(83, 'Haiti', 'd-m-Y g:iA'),(84, 'Honduras', 'd-m-Y g:iA'),
				(85, 'Hong Kong', 'd-m-Y g:iA'),(86, 'Hungary', 'd-m-Y g:iA'),(87, 'Iceland', 'd-m-Y g:iA'),(88, 'India', 'd-m-Y g:iA'),
				(89, 'Indonesia', 'd-m-Y g:iA'),(90, 'Iran', 'd-m-Y g:iA'),(91, 'Iraq', 'd-m-Y g:iA'),(92, 'Ireland', 'd-m-Y g:iA'),
				(93, 'Isle of Man', 'd-m-Y g:iA'),(94, 'Israel', 'd-m-Y g:iA'),(95, 'Italy', 'd-m-Y g:iA'),(96, 'Jamaica', 'd-m-Y g:iA'),
				(97, 'Japan', 'd-m-Y g:iA'),(98, 'Jersey', 'd-m-Y g:iA'),(99, 'Jordan', 'd-m-Y g:iA'),(100, 'Kazakhstan', 'd-m-Y g:iA'),
				(101, 'Kenya', 'd-m-Y g:iA'),(102, 'Kiribati', 'd-m-Y g:iA'),(103, 'Kuwait', 'd-m-Y g:iA'),(104, 'Kyrgyzstan', 'd-m-Y g:iA'),
				(105, 'Laos', 'd-m-Y g:iA'),(106, 'Latvia', 'd-m-Y g:iA'),(107, 'Lebanon', 'd-m-Y g:iA'),(108, 'Lesotho', 'd-m-Y g:iA'),
				(109, 'Liberia', 'd-m-Y g:iA'),	(110, 'Libya', 'd-m-Y g:iA'),(111, 'Liechtenstein', 'd-m-Y g:iA'),(112, 'Lithuania', 'd-m-Y g:iA'),
				(113, 'Luxembourg', 'd-m-Y g:iA'),(114, 'Macau', 'd-m-Y g:iA'),(115, 'Macedonia', 'd-m-Y g:iA'),(116, 'Madagascar', 'd-m-Y g:iA'),
				(117, 'Malawi', 'd-m-Y g:iA'),(118, 'Malaysia', 'd-m-Y g:iA'),(119, 'Maldives', 'd-m-Y g:iA'),(120, 'Mali', 'd-m-Y g:iA'),
				(121, 'Malta', 'd-m-Y g:iA'),(122, 'Marshall Islands', 'd-m-Y g:iA'),(123, 'Martinique', 'd-m-Y g:iA'),(124, 'Mauritania', 'd-m-Y g:iA'),
				(125, 'Mauritius', 'd-m-Y g:iA'),(126, 'Mayotte', 'd-m-Y g:iA'),(127, 'Mexico', 'd-m-Y g:iA'),(128, 'Micronesia', 'd-m-Y g:iA'),
				(129, 'Moldova', 'd-m-Y g:iA'),(130, 'Monaco', 'd-m-Y g:iA'),(131, 'Mongolia', 'd-m-Y g:iA'),(132, 'Montserrat', 'd-m-Y g:iA'),
				(133, 'Morocco', 'd-m-Y g:iA'),	(134, 'Mozambique', 'd-m-Y g:iA'),(135, 'Myanmar', 'd-m-Y g:iA'),(136, 'Namibia', 'd-m-Y g:iA'),
				(137, 'Nauru', 'd-m-Y g:iA'),(138, 'Nepal', 'd-m-Y g:iA'),(139, 'Netherlands', 'd-m-Y g:iA'),(140, 'Netherlands Antilles', 'd-m-Y g:iA'),
				(141, 'New Caledonia', 'd-m-Y g:iA'),(142, 'New Zealand', 'd-m-Y g:iA'),(143, 'Nicaragua', 'd-m-Y g:iA'),(144, 'Niue', 'd-m-Y g:iA'),
				(145, 'Niger', 'd-m-Y g:iA'),(146, 'Nigeria', 'd-m-Y g:iA'),(147, 'Norfolk Island', 'd-m-Y g:iA'),(148, 'Northern Mariana Islands', 'd-m-Y g:iA'),
				(149, 'Norway', 'd-m-Y g:iA'),(150, 'Oman', 'd-m-Y g:iA'),(151, 'Pakistan', 'd-m-Y g:iA'),(152, 'Palau', 'd-m-Y g:iA'),
				(153, 'Panama', 'd-m-Y g:iA'),(154, 'Papua New Guinea', 'd-m-Y g:iA'),(155, 'Paraguay', 'd-m-Y g:iA'),(156, 'Peru', 'd-m-Y g:iA'),
				(157, 'Philippines', 'd-m-Y g:iA'),(158, 'Pitcairn Island', 'd-m-Y g:iA'),(159, 'Poland', 'd-m-Y g:iA'),(160, 'Portugal', 'd-m-Y g:iA'),
				(161, 'Puerto Rico', 'd-m-Y g:iA'),(162, 'Qatar', 'd-m-Y g:iA'),(163, 'Reunion', 'd-m-Y g:iA'),	(164, 'Romania', 'd-m-Y g:iA'),
				(165, 'Russia', 'd-m-Y g:iA'),(166, 'Rwanda', 'd-m-Y g:iA'),(167, 'Saint Barthelemy', 'd-m-Y g:iA'),(168, 'Saint Helena', 'd-m-Y g:iA'),
				(169, 'Saint Kitts and Nevis', 'd-m-Y g:iA'),(170, 'Saint Lucia', 'd-m-Y g:iA'),(171, 'Saint Martin', 'd-m-Y g:iA'),(172, 'Saint Pierre and Miquelon', 'd-m-Y g:iA'),
				(173, 'Saint Vincent and the Grenadines', 'd-m-Y g:iA'),(174, 'Samoa', 'd-m-Y g:iA'),(175, 'San Marino', 'd-m-Y g:iA'),
				(176, 'Sao Tome and Principe', 'd-m-Y g:iA'),(177, 'Saudia Arabia', 'd-m-Y g:iA'),(178, 'Senegal', 'd-m-Y g:iA'),(179, 'Serbia', 'd-m-Y g:iA'),
				(180, 'Seychelles', 'd-m-Y g:iA'),(181, 'Sierra Leone', 'd-m-Y g:iA'),(182, 'Singapore', 'd-m-Y g:iA'),	(183, 'Slovakia', 'd-m-Y g:iA'),
				(184, 'Slovenia', 'd-m-Y g:iA'),(185, 'Solomon Islands', 'd-m-Y g:iA'),(186, 'Somalia', 'd-m-Y g:iA'),(187, 'South Africa', 'd-m-Y g:iA'),
				(188, 'Spain', 'd-m-Y g:iA'),(189, 'Sri Lanka', 'd-m-Y g:iA'),(190, 'Sudan', 'd-m-Y g:iA'),(191, 'Suriname', 'd-m-Y g:iA'),
				(192, 'Svalbard and Jan Mayen Islands', 'd-m-Y g:iA'),(193, 'Swaziland', 'd-m-Y g:iA'),	(194, 'Sweden', 'd-m-Y g:iA'),
				(195, 'Switzerland', 'd-m-Y g:iA'),(196, 'Syria', 'd-m-Y g:iA'),(197, 'Taiwan', 'd-m-Y g:iA'),(198, 'Tajikistan', 'd-m-Y g:iA'),
				(199, 'Tanzania', 'd-m-Y g:iA'),(200, 'Thailand', 'd-m-Y g:iA'),(201, 'Togo', 'd-m-Y g:iA'),(202, 'Tonga', 'd-m-Y g:iA'),
				(203, 'Trinidad and Tobago', 'd-m-Y g:iA'),	(204, 'Tunisia', 'd-m-Y g:iA'),	(205, 'Turkey', 'd-m-Y g:iA'),
				(206, 'Turkmenistan', 'd-m-Y g:iA'),(207, 'Turks and Caicos Islands', 'd-m-Y g:iA'),(208, 'Tuvalu', 'd-m-Y g:iA'),
				(209, 'Uganda', 'd-m-Y g:iA'),	(210, 'Ukraine', 'd-m-Y g:iA'),	(211, 'United Arab Emirates', 'd-m-Y g:iA'),(212, 'United Kingdom', 'd-m-Y g:iA'),
				(213, 'United States Virgin Islands', 'd-m-Y g:iA'),(214, 'Uruguay', 'd-m-Y g:iA'),	(215, 'USA', 'm/d/Y g:iA'),
				(216, 'Uzbekistan', 'd-m-Y g:iA'),(217, 'Vanuatu', 'd-m-Y g:iA'),(218, 'Venezuela', 'd-m-Y g:iA'),(219, 'Vietnam', 'd-m-Y g:iA'),
				(220, 'Wallis and Futuna Islands', 'd-m-Y g:iA'),(221, 'Western Sahara', 'd-m-Y g:iA'),(222, 'Yemen', 'd-m-Y g:iA'),
				(223, 'Zambia', 'd-m-Y g:iA'),(224, 'Zimbabwe', 'd-m-Y g:iA');
				";			
		mysql_query($query,$this->dbLink)or die(mysql_error());   	
	}
	
	public function createStateTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `state` (
				  `state_id` int(11) NOT NULL auto_increment,
				  `name` varchar(32) NOT NULL,
				  `country_id` int(11) NOT NULL,
				  PRIMARY KEY  (`state_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=339 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function insertIntoStateTable ()
	{		
		$query="INSERT IGNORE INTO `state` (`state_id`, `name`, `country_id`) VALUES
				(328, 'Wisconsin', 215),(327, 'West Virginia', 215),(326, 'Washington', 215),
				(325, 'Virginia', 215),(324, 'Vermont', 215),(323, 'Utah', 215),(322, 'Texas', 215),
				(321, 'Tennessee', 215),(320, 'South Dakota', 215),	(319, 'South Carolina', 215),
				(318, 'Rhode Island', 215),(317, 'Pennsylvania', 215),(316, 'Oregon', 215),(315, 'Oklahoma', 215),
				(314, 'Ohio', 215),(313, 'North Dakota', 215),(312, 'North Carolina', 215),
				(311, 'New York', 215),(310, 'New Mexico', 215),(309, 'New Jersey', 215),(308, 'New Hampshire', 215),
				(307, 'Nevada', 215),(306, 'Nebraska', 215),(305, 'Montana', 215),(304, 'Missouri', 215),
				(303, 'Mississippi', 215),(302, 'Minnesota', 215),(301, 'Michigan', 215),(300, 'Massachusetts', 215),
				(299, 'Maryland', 215),(298, 'Maine', 215),(297, 'Louisiana', 215),(296, 'Kentucky', 215),
				(295, 'Kansas', 215),(294, 'Iowa', 215),(293, 'Indiana', 215),(292, 'Illinois', 215),(291, 'Idaho', 215),
				(290, 'Hawaii', 215),(289, 'Georgia', 215),	(288, 'Florida', 215),(287, 'Delaware', 215),(286, 'Connecticut', 215),
				(285, 'Colorado', 215),(284, 'California', 215),(283, 'Arkansas', 215),(282, 'Arizona', 215),(329, 'Wyoming', 215),
				(280, 'Alabama', 215),(281, 'Alaska', 215);";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   	
	}
	
	/*public function createInventoryTable ()
	{		
		$query="CREATE TABLE IF NOT EXISTS `inventory` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(75) NOT NULL,
				  `description` varchar(300) NOT NULL,
				  `type` varchar(12) NOT NULL,
				  `facility_id` int(11) NOT NULL,
				  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;				
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   	
	}*/
	
	public function createEquipmentTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `equipment` (
				  `equipment_id` int(11) NOT NULL auto_increment,
				  `department_id` int(11) default '0',
				  `equipment_nr` varchar(5) default '0',
				  `equip_desc` varchar(30) default NULL,
				  `inventory_id` int(11) default '0',
				  `permit` varchar(30) default NULL,
				  `expire` int(11) default NULL,
				  `daily` decimal(10,3) default '0.000',
				  `dept_track` varchar(3) default NULL,
				  `facility_track` varchar(3) default NULL,
				  `creater_id` int(11) default '0',
				  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`equipment_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;			
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   		
	}
	
	public function createProductTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `product` (
				  `product_id` int(11) NOT NULL auto_increment,
				  `product_nr` varchar(50) default NULL,
				  `name` varchar(200) default NULL,
				  `inventory_id` int(11) default '0',
				  `voclx` decimal(10,2) default NULL,
				  `vocwx` decimal(10,2) default NULL,
				  `density` decimal(5,2) default '0.00',
				  `density_unit_id` int(11) default NULL,
				  `coating_id` int(11) default NULL,
				  `specialty_coating` varchar(3) default 'no',
				  `aerosol` varchar(3) default 'no',
				  `specific_gravity` decimal(5,2) default NULL,
				  `boiling_range_from` decimal(5,2) NOT NULL,
				  `boiling_range_to` decimal(5,2) NOT NULL,
				  `supplier_id` int(11) NOT NULL,
				  `percent_volatile_weight` decimal(5,3) NOT NULL default '0.000',
				  `percent_volatile_volume` decimal(5,3) NOT NULL default '0.000',
				  PRIMARY KEY  (`product_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   		
	}
		
	public function createComponentTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `component` (
				  `component_id` int(11) NOT NULL AUTO_INCREMENT,
				  `cas` varchar(128) NOT NULL,
				  `description` varchar(128) NOT NULL,
				  `sara313` varchar(5) DEFAULT NULL,
				  `caab2588` varchar(5) DEFAULT NULL,
				  PRIMARY KEY (`component_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createDensityTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `density` (
				  `id` int(11) NOT NULL auto_increment,
				  `numerator` int(11) NOT NULL,
				  `denominator` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function insertIntoDensityTable()
	{		
		$query="INSERT IGNORE INTO `density` (`id`, `numerator`, `denominator`) VALUES (1, 2, 1),(2, 11, 31);";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createUnittypeTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `unittype` (
				  `unittype_id` int(11) NOT NULL auto_increment,
				  `name` varchar(50) default NULL,
				  `unittype_desc` varchar(200) default NULL,
				  `formula` varchar(200) default NULL,
				  `type_id` tinyint(4) NOT NULL,
				  `system` varchar(32) default NULL,
				  `unit_class_id` int(11) default NULL,
				  PRIMARY KEY  (`unittype_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;					
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function insertIntoUnittypeTable()
	{		
		$query="INSERT IGNORE INTO `unittype` (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) VALUES
				(9, 'ml', 'milliliter', '', 1, 'metric', 4),
				(8, 'IMP. GAL', 'uk gallon', '', 1, 'USA', 1),
				(7, 'OZS', 'oz', '', 2, 'USA', 3),
				(6, 'M', 'meter', '', 3, 'metric', NULL),
				(5, 'T', 'ton', '1', 2, 'metric', 5),
				(4, 'L', 'liter', '1', 1, 'metric', 4),
				(3, 'KG', 'kilogram', '1', 2, 'metric', 5),
				(2, 'LBS', 'lb', '1', 2, 'USA', 3),
				(1, 'gal', 'us gallon', '', 4, 'USA', 1),
				(10, 'mg', 'milligram', '', 2, 'metric', 5),
				(11, 'GRAM', 'gram', '', 2, 'metric', 5),
				(12, 'GRAIN', 'grain', '', 2, 'USA', 3),
				(13, 'dry gal', 'U.S. dry gallon', NULL, 5, 'USA', 2),
				(14, 'imp fl oz', 'Imperial fluid ounce', NULL, 4, 'USA', 1),
				(15, 'fl oz', 'U.S. customary fluid ounce', NULL, 4, 'USA', 1),
				(16, 'pt', 'Pint', '', 4, 'USA', 1),
				(17, 'qt', 'Quart', '', 4, 'USA', 1),
				(18, 'Barrel', 'Barrel', '1', 4, 'USA', 1),
				(19, 'Mil Gram', 'Mil Gram', '', 2, NULL, NULL),
				(20, 'CWT', 'U.S. CWT', '', 2, 'USA', 3),
				(21, 'Metric Ton', 'Metric Ton', '', 2, NULL, NULL),
				(22, 'dr', 'Dram', '', 2, 'metric', 5),
				(23, 'hg', 'Hectogram', '', 2, 'metric', 5),
				(24, 'cl', 'Centiliter', '', 1, 'metric', 4),
				(25, 'dl', 'Deciliter', '', 1, 'metric', 4),
				(26, 'Dekaliter', 'Dekaliter', '', 1, 'metric', 4),
				(27, 'hl', 'Hectoliter', '', 1, 'metric', 4),
				(28, 'Kiloliter', 'kiloliter', '', 1, 'metric', 4),
				(29, 'Millilter', 'Millileter', '', 4, NULL, NULL),
				(30, 'dry bu', 'U.S. Dry Bushel', '', 5, 'USA', 2),
				(31, 'cm3', 'cm3', '1', 1, 'metric', 4),
				(32, 'bu', 'British bushel', NULL, 1, 'metric', 4),
				(33, 'British CWT', 'British CWT', NULL, 2, 'metric', 5),
				(34, 'kWh', 'Kilowatt hour', NULL, 6, NULL, NULL);		
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createRuleTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `rule` (
				  `rule_id` int(11) NOT NULL auto_increment,
				  `country` varchar(32) default NULL,
				  `state` varchar(32) default NULL,
				  `county` varchar(32) default NULL,
				  `city` varchar(32) default NULL,
				  `zip` varchar(32) default NULL,
				  `rule_nr` varchar(32) default NULL,
				  `rule_desc` varchar(200) default NULL,
				  PRIMARY KEY  (`rule_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createCoatTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `coat` (
			  `coat_id` int(11) NOT NULL auto_increment,
			  `coat_desc` varchar(50) default NULL,
			  PRIMARY KEY  (`coat_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createSubstrateTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `substrate` (
				  `substrate_id` int(11) NOT NULL auto_increment,
				  `substrate_desc` varchar(200) default NULL,
				  PRIMARY KEY  (`substrate_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createApmethodTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `apmethod` (
				  `apmethod_id` int(11) NOT NULL auto_increment,
				  `apmethod_desc` varchar(200) default NULL,
				  PRIMARY KEY  (`apmethod_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createSupplierTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `supplier` (
				  `supplier_id` int(11) NOT NULL auto_increment,
				  `supplier` varchar(200) default NULL,
				  `contact_person` varchar(64) default NULL,
				  `phone` varchar(32) default NULL,
				  `address` varchar(256) default NULL,
				  PRIMARY KEY  (`supplier_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createTypeTable()
	{		
		$query="CREATE TABLE IF NOT EXISTS `type` (
				  `type_id` int(11) NOT NULL auto_increment,
				  `type_desc` varchar(50) default NULL,
				  PRIMARY KEY  (`type_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;				
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function insertIntoTypeTable()
	{		
		$query="INSERT IGNORE INTO `type` (`type_id`, `type_desc`) VALUES
				(3, 'Distance'),
				(2, 'Weight'),
				(1, 'Volume'),
				(4, 'Volume Liquid'),
				(5, 'Volume Dry'),
				(6, 'Energy');
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createProductgroupTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `productgroup` (
					  `productgroup_id` int(11) NOT NULL auto_increment,
					  `inventory_id` int(11) NOT NULL,
					  `product_id` int(11) NOT NULL,
					  `quantity` decimal(20,3) NOT NULL,
					  `productgroup_nr` int(11) NOT NULL,
					  `OSuse` decimal(20,3) NOT NULL,
					  `CSuse` decimal(20,3) NOT NULL,
					  `location_storage` varchar(128) NOT NULL,
					  `location_use` varchar(128) NOT NULL,
					  PRIMARY KEY  (`productgroup_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
					";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createMixTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `mix` (
					  `mix_id` int(11) NOT NULL auto_increment,
					  `equipment_id` int(11) NOT NULL,
					  `department_id` int(11) NOT NULL,
					  `description` varchar(48) NOT NULL,
					  `voc` decimal(20,2) NOT NULL,
					  `voclx` decimal(20,2) NOT NULL,
					  `vocwx` decimal(20,2) NOT NULL,
					  `creation_time` date default NULL,
					  `rule_id` int(11) NOT NULL,
					  `exempt_rule` varchar(25) default NULL,
					  `apmethod_id` int(11) default NULL,
					  `waste_percent` decimal(5,2) default NULL,
					  PRIMARY KEY  (`mix_id`),
					  KEY `dep_time` (`department_id`,`creation_time`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
					";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
		
	public function createMixgroupTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `mixgroup` (
				  `mixgroup_id` int(11) NOT NULL auto_increment,
				  `mix_id` int(11) NOT NULL,
				  `product_id` int(11) NOT NULL,
				  `quantity` decimal(20,2) NOT NULL,
				  `unit_type` int(11) NOT NULL,
				  `quantity_lbs` decimal(20,2) default NULL,
				  PRIMARY KEY  (`mixgroup_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createComponentsgroupTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `components_group` (
				  `component_group_id` int(11) NOT NULL auto_increment,
				  `component_id` int(11) NOT NULL,
				  `product_id` int(11) NOT NULL,
				  `substrate_id` int(11) default NULL,
				  `rule_id` int(11) default NULL,
				  `mm_hg` decimal(10,2) default NULL,
				  `temp` int(11) default NULL,
				  `weight` decimal(10,2) default NULL,
				  `type` varchar(5) NOT NULL,
				  PRIMARY KEY  (`component_group_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";
						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
		
	public function createAgencyTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `agency` (
				  `agency_id` int(11) NOT NULL auto_increment,
				  `name` varchar(64) NOT NULL,
				  PRIMARY KEY  (`agency_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;	
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createAgencybelongTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `agency_belong` (
				  `agency_belong_id` int(11) NOT NULL auto_increment,
				  `agency_id` int(11) NOT NULL,
				  `component_id` int(11) NOT NULL,
				  PRIMARY KEY  (`agency_belong_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;	
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createHazardousclassTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `hazardous_class` (
				  `hazardous_class_id` int(11) NOT NULL auto_increment,
				  `class` varchar(64) NOT NULL,
				  `irr` varchar(3) NOT NULL,
				  `ohh` varchar(3) NOT NULL,
				  `sens` varchar(3) NOT NULL,
				  `oxy_1` varchar(3) NOT NULL,
				  PRIMARY KEY  (`hazardous_class_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
	
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createGcglistTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gcg_list` (
				  `gcg_id` int(11) NOT NULL auto_increment,
				  `year` int(2) NOT NULL,
				  `number` int(6) NOT NULL,
				  `rev_number` int(2) NOT NULL,
				  PRIMARY KEY  (`gcg_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;	
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
		
	public function createIssueTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `issue` (
				  `issue_id` int(11) NOT NULL auto_increment,
				  `title` varchar(40) NOT NULL,
				  `description` text NOT NULL,
				  `creator_id` int(11) NOT NULL,
				  `referer` varchar(1000) NOT NULL,
				  `priority` varchar(20) NOT NULL,
				  `status` varchar(20) NOT NULL,
				  PRIMARY KEY  (`issue_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createSelectedruleslistTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `selected_rules_list` (
				  `id` int(11) NOT NULL auto_increment,
				  `rule_id` int(11) NOT NULL,
				  `category` varchar(24) NOT NULL,
				  `category_id` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	public function createAccessoryTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `accessory` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(96) NOT NULL,
				  `company_id` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}
	
	/*public function createAccessory2inventoryTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `accessory2inventory` (
				  `id` int(11) NOT NULL auto_increment,
				  `accessory_id` int(11) NOT NULL,
				  `inventory_id` int(11) NOT NULL,
				  `unit_amount` decimal(20,3) NOT NULL,
				  `unit_count` varchar(120) default NULL,
				  `unit_qty` decimal(20,3) NOT NULL,
				  `total_qty` decimal(20,3) NOT NULL,
				  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}*/
	
	/*public function createMaterial2inventoryTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `material2inventory` (
				  `id` int(11) NOT NULL auto_increment,
				  `product_id` int(11) NOT NULL,
				  `inventory_id` int(11) NOT NULL,
				  `os_use` decimal(20,3) default NULL,
				  `cs_use` decimal(20,3) default NULL,
				  `storage_location` varchar(128) default NULL,
				  `total_qty` decimal(20,3) NOT NULL,
				  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}*/
	
	/*public function createUselocation2materialTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `use_location2material` (
				  `id` int(11) NOT NULL auto_increment,
				  `department_id` int(11) NOT NULL,
				  `material2inventory_id` int(11) NOT NULL,
				  `total_qty` decimal(20,3) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	*/
	
	public function createDefaultTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `default` (
				  `id` int(11) NOT NULL auto_increment,
				  `subject` varchar(64) NOT NULL,
				  `id_of_subject` int(11) NOT NULL,
				  `object` varchar(64) NOT NULL,
				  `id_of_object` varchar(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createUnitclassTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `unit_class` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(64) NOT NULL,
				  `description` varchar(120) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;					
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoUnitclassTable()
	{
		$query="INSERT IGNORE INTO `unit_class` (`id`, `name`, `description`) VALUES
				(1, 'USALiquid', 'USA Liquid'),
				(2, 'USADry', 'USA Dry'),
				(3, 'USAWght', 'USA Weight'),
				(4, 'MetricVlm', 'Metric Volume'),
				(5, 'MetricWght', 'Metric Weight');";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createWasteTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `waste` (
				  `id` int(11) NOT NULL auto_increment,
				  `mix_id` int(11) NOT NULL,
				  `method` varchar(8) NOT NULL default 'percent',
				  `unittype_id` int(11) default NULL,
				  `value` decimal(20,2) NOT NULL default '0.00',
				  `waste_stream_id` int(11) default NULL,
				  `pollution_id` int(11) default NULL,
				  `storage_id` int(11) default NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsbillingTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_billing` (
				  `billing_id` int(11) NOT NULL auto_increment,
				  `name` varchar(128) NOT NULL,
				  `description` varchar(256) NOT NULL,
				  `one_time_charge` decimal(10,2) NOT NULL default '0.00',
				  `bplimit` int(5) NOT NULL,
				  `months_count` int(3) NOT NULL,
				  `price` decimal(10,2) NOT NULL default '0.00',
				  `type` varchar(10) NOT NULL,
				  `defined` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`billing_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsconfigTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_config` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(64) NOT NULL,
				  `value` text NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpscustomerTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_customer` (
				  `customer_id` int(11) NOT NULL auto_increment,
				  `billing_id` int(11) default NULL,
				  `status` varchar(20) NOT NULL default 'on',
				  `discount` decimal(10,2) NOT NULL default '0.00',
				  `balance` decimal(10,2) NOT NULL default '0.00',
				  PRIMARY KEY  (`customer_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpscustomerlimitTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_customer_limit` (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `limit_price_id` int(11) NOT NULL,
				  `current_value` int(5) NOT NULL,
				  `max_value` int(5) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsdeactivationTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_deactivation` (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `date` datetime NOT NULL,
				  `period_end_date` date NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsdefinedbprequestTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_defined_bp_request` (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `bplimit` int(5) NOT NULL,
				  `months_count` int(3) NOT NULL,
				  `type` varchar(10) NOT NULL,
				  `MSDS_limit` int(5) default NULL,
				  `memory_limit` int(5) default NULL,
				  `description` text,
				  `date` date NOT NULL,
				  `status` varchar(12) NOT NULL default 'unprocessed',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsinvoiceTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_invoice` (
				  `invoice_id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `one_time_charge` decimal(10,2) NOT NULL default '0.00',
				  `amount` decimal(10,2) NOT NULL default '0.00',
				  `discount` decimal(10,2) NOT NULL default '0.00',
				  `total` decimal(10,2) NOT NULL default '0.00',
				  `paid` decimal(10,2) NOT NULL default '0.00',
				  `due` decimal(10,2) NOT NULL default '0.00',
				  `balance` decimal(10,2) NOT NULL default '0.00',
				  `generation_date` date NOT NULL,
				  `suspension_date` date NOT NULL,
				  `period_start_date` date default NULL,
				  `period_end_date` date default NULL,
				  `billing_info` varchar(120) default NULL,
				  `limit_info` varchar(120) default NULL,
				  `custom_info` varchar(120) default NULL,
				  `status` varchar(12) NOT NULL default 'due',
				  `suspension_disable` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`invoice_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpslimitTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_limit` (
				  `limit_id` int(11) NOT NULL auto_increment,
				  `name` varchar(32) NOT NULL,
				  `increase_step` int(5) NOT NULL default '1',
				  `unit_type` varchar(32) default NULL,
				  PRIMARY KEY  (`limit_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpslimitpriceTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_limit_price` (
				  `limit_price_id` int(11) NOT NULL auto_increment,
				  `limit_id` int(11) NOT NULL,
				  `bplimit` int(5) NOT NULL,
				  `default_limit` int(5) NOT NULL,
				  `increase_cost` decimal(10,2) NOT NULL default '0.00',
				  `type` varchar(10) NOT NULL,
				  `defined` tinyint(1) NOT NULL default '0',
				  PRIMARY KEY  (`limit_price_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsnotificationscriptTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_notification_script` (
				  `id` int(11) NOT NULL auto_increment,
				  `run_date` datetime NOT NULL,
				  `mode` varchar(12) NOT NULL default 'self',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpspaymentTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_payment` (
				  `payment_id` int(11) NOT NULL auto_increment,
				  `invoice_id` int(11) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `txn_id` varchar(120) NOT NULL,
				  `paid` decimal(10,2) NOT NULL default '0.00',
				  `due` decimal(10,2) NOT NULL default '0.00',
				  `balance` decimal(10,2) NOT NULL default '0.00',
				  `payment_date` datetime NOT NULL,
				  `status` varchar(20) NOT NULL,
				  `payment_method_id` int(11) default NULL,
				  PRIMARY KEY  (`payment_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
		
	public function createVpsschedulecustomerplanTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_schedule_customer_plan` (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `billing_id` int(11) NOT NULL,
				  `type` varchar(5) NOT NULL default 'bpEnd',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsschedulelimitTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_schedule_limit` (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(11) NOT NULL,
				  `limit_price_id` int(11) NOT NULL,
				  `type` varchar(5) NOT NULL default 'bpEnd',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpsuserTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_user` (
				  `user_id` int(11) NOT NULL auto_increment,
				  `accessname` varchar(128) NOT NULL,
				  `password` varchar(128) NOT NULL,
				  `accesslevel_id` int(11) NOT NULL,
				  `first_name` varchar(128) NOT NULL,
				  `last_name` varchar(128) NOT NULL,
				  `secondary_contact` varchar(256) default NULL,
				  `email` varchar(128) NOT NULL,
				  `secondary_email` varchar(128) default NULL,
				  `company_id` int(11) NOT NULL,
				  `facility_id` int(11) default NULL,
				  `department_id` int(11) default NULL,
				  `address1` varchar(256) NOT NULL,
				  `address2` varchar(256) default NULL,
				  `city` varchar(128) NOT NULL,
				  `state_id` int(11) NOT NULL,
				  `zip` int(5) NOT NULL,
				  `country_id` int(11) NOT NULL,
				  `phone` varchar(64) NOT NULL,
				  `fax` varchar(64) NOT NULL,
				  PRIMARY KEY  (`user_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createVpspaymentmethodTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `vps_payment_method` (
				  `id` int(11) NOT NULL auto_increment,
				  `payment_method` varchar(64) NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
//-----------------------------------GACL Tables----------------------------------------
	
	public function createGaclaclTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_acl` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(230) NOT NULL DEFAULT 'system',
				  `allow` int(11) NOT NULL DEFAULT '0',
				  `enabled` int(11) NOT NULL DEFAULT '0',
				  `return_value` text,
				  `note` text,
				  `updated_date` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `gacl_enabled_acl` (`enabled`),
				  KEY `gacl_section_value_acl` (`section_value`),
				  KEY `gacl_updated_date_acl` (`updated_date`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;				
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaclTable()
	{
		$query="INSERT IGNORE INTO `gacl_acl` (`id`, `section_value`, `allow`, `enabled`, `return_value`, `note`, `updated_date`) VALUES
				(21, 'user', 1, 1, '', '', 1227632213),
				(20, 'user', 1, 1, '', '', 1227632202),
				(19, 'user', 1, 1, '', '', 1227632188),
				(18, 'user', 1, 1, '', '', 1227632173),
				(17, 'user', 1, 1, '', '', 1277104868),
				(16, 'user', 1, 1, '', '', 1279095576),
				(22, 'user', 1, 1, '', '', 1276678583),
				(9427, 'system', 1, 1, '', '', 1283937903);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaclsectionsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_acl_sections` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(230) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_value_acl_sections` (`value`),
				  KEY `gacl_hidden_acl_sections` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;				
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
		
	public function insertIntoGaclaclsectionsTable()
	{
		$query="INSERT IGNORE INTO `gacl_acl_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
				(1, 'system', 1, 'System', 0),
				(2, 'user', 2, 'User', 0);
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaclseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_acl_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaclseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_acl_seq` (`id`) VALUES
				(9427);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclacoTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aco` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(240) NOT NULL DEFAULT '0',
				  `value` varchar(240) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(255) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
				  KEY `gacl_hidden_aco` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclacoTable()
	{
		$query="INSERT IGNORE INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
				(10, 'access', 'company', 10, 'Company', 0),
				(11, 'access', 'facility', 11, 'Facility', 0),
				(12, 'access', 'department', 12, 'Department', 0),
				(13, 'access', 'user', 13, 'User', 0),
				(14, 'access', 'equipment', 14, 'Equipment', 0),
				(15, 'access', 'data', 15, 'Data', 0),
				(16, 'access', 'root', 16, 'Root', 0);
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclacomapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aco_map` (
				  `acl_id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(230) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  PRIMARY KEY (`acl_id`,`section_value`,`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;				
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclacomapTable()
	{
		$query="INSERT IGNORE INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES
				(16, 'access', 'company'),(16, 'access', 'data'),(16, 'access', 'department'),
				(16, 'access', 'equipment'),(16, 'access', 'facility'),(16, 'access', 'root'),
				(16, 'access', 'user'),(17, 'access', 'company'),(17, 'access', 'data'),
				(17, 'access', 'department'),(17, 'access', 'equipment'),(17, 'access', 'facility'),
				(17, 'access', 'user'),(18, 'access', 'data'),(18, 'access', 'department'),
				(18, 'access', 'equipment'),(18, 'access', 'user'),(19, 'access', 'facility'),
				(20, 'access', 'data'),(21, 'access', 'department'),(21, 'access', 'equipment'),
				(21, 'access', 'user'),(22, 'access', 'company'),(22, 'access', 'data'),
				(22, 'access', 'department'),(22, 'access', 'equipment'),(22, 'access', 'facility'),
				(22, 'access', 'root'),(22, 'access', 'user'),(9427, 'access', 'root');";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclacosectionsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aco_sections` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(230) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_value_aco_sections` (`value`),
				  KEY `gacl_hidden_aco_sections` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;	";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclacosectionsTable()
	{
		$query="INSERT IGNORE INTO `gacl_aco_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
				(10, 'access', 10, 'Access', 0);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclacosectionsseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aco_sections_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclacosectionsseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_aco_sections_seq` (`id`) VALUES
				(10);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclacoseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aco_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;	";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclacoseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_aco_seq` (`id`) VALUES (192);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaroTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro` (
				`id` int(11) NOT NULL DEFAULT '0',
				`section_value` varchar(240) NOT NULL DEFAULT '0',
				`value` varchar(240) NOT NULL,
				`order_value` int(11) NOT NULL DEFAULT '0',
				`name` varchar(255) NOT NULL,
				`hidden` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
				KEY `gacl_hidden_aro` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	/*public function insertIntoGaclaroTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
				(102, 'users', 'root', 0, 'root', 0);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	*/
	
	
	public function createGaclarogroupsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_groups` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `parent_id` int(11) NOT NULL DEFAULT '0',
				  `lft` int(11) NOT NULL DEFAULT '0',
				  `rgt` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(255) NOT NULL,
				  `value` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`,`value`),
				  UNIQUE KEY `gacl_value_aro_groups` (`value`),
				  KEY `gacl_parent_id_aro_groups` (`parent_id`),
				  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclarogroupsTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES
				(10, 0, 1, 12, 'Giant Compliance', 'Giant Compliance'),
				(11, 10, 2, 3, 'Company level', 'Company level'),
				(13, 10, 6, 7, 'Department Level', 'Department Level'),
				(12, 10, 4, 5, 'Facility Level', 'Facility Level'),
				(14, 10, 8, 9, 'Super Users', 'Super Users'),
				(532, 10, 10, 11, 'root', 'root');	";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclarogroupsidseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_groups_id_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclarogroupsidseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_groups_id_seq` (`id`) VALUES
				(532);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclarogroupsmapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_groups_map` (
				  `acl_id` int(11) NOT NULL DEFAULT '0',
				  `group_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`acl_id`,`group_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclarogroupsmapTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES
				(16, 14),
				(17, 11),
				(18, 12),
				(19, 12),
				(20, 13),
				(21, 13),
				(9427, 532);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaromapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_map` (
				  `acl_id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(230) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  PRIMARY KEY (`acl_id`,`section_value`,`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclarosectionsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_sections` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(230) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_value_aro_sections` (`value`),
				  KEY `gacl_hidden_aro_sections` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclarosectionsTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
				(10, 'users', 10, 'Users', 0);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclarosectionsseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_sections_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclarosectionsseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_sections_seq` (`id`) VALUES
				(10);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaroseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_aro_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaroseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_aro_seq` (`id`) VALUES
				(103);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaxoTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(240) NOT NULL DEFAULT '0',
				  `value` varchar(240) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(255) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
				  KEY `gacl_hidden_axo` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaxoTable()
	{
		$query="INSERT IGNORE INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
				(10, 'action', 'view', 10, 'View', 0),
				(11, 'action', 'edit', 11, 'Edit', 0),
				(12, 'action', 'add', 12, 'Add', 0),
				(13, 'action', 'delete', 13, 'Delete', 0);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaxogroupsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_groups` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `parent_id` int(11) NOT NULL DEFAULT '0',
				  `lft` int(11) NOT NULL DEFAULT '0',
				  `rgt` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(255) NOT NULL,
				  `value` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`,`value`),
				  UNIQUE KEY `gacl_value_axo_groups` (`value`),
				  KEY `gacl_parent_id_axo_groups` (`parent_id`),
				  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaxogroupsmapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_groups_map` (
				  `acl_id` int(11) NOT NULL DEFAULT '0',
				  `group_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`acl_id`,`group_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaxomapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_map` (
				  `acl_id` int(11) NOT NULL DEFAULT '0',
				  `section_value` varchar(230) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  PRIMARY KEY (`acl_id`,`section_value`,`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}		
	
	public function insertIntoGaclaxomapTable()
	{
		$query="INSERT IGNORE INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES
				(16, 'action', 'add'),(16, 'action', 'delete'),	(16, 'action', 'edit'),
				(16, 'action', 'view'),(17, 'action', 'add'),(17, 'action', 'delete'),
				(17, 'action', 'edit'),(17, 'action', 'view'),(18, 'action', 'add'),
				(18, 'action', 'delete'),(18, 'action', 'edit'),(18, 'action', 'view'),
				(19, 'action', 'view'),(20, 'action', 'add'),(20, 'action', 'delete'),
				(20, 'action', 'edit'),(20, 'action', 'view'),(21, 'action', 'view');";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}		
	
	public function createGaclaxosectionsTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_sections` (
				  `id` int(11) NOT NULL DEFAULT '0',
				  `value` varchar(230) NOT NULL,
				  `order_value` int(11) NOT NULL DEFAULT '0',
				  `name` varchar(230) NOT NULL,
				  `hidden` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `gacl_value_axo_sections` (`value`),
				  KEY `gacl_hidden_axo_sections` (`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}		
	
	public function insertIntoGaclaxosectionsTable()
	{
		$query="INSERT IGNORE INTO `gacl_axo_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
				(10, 'action', 10, 'Action', 0);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclaxosectionsseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_sections_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaxosectionsseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_axo_sections_seq` (`id`) VALUES
				(10);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}		
	
	public function createGaclaxoseqTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_axo_seq` (
				  `id` int(11) NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclaxoseqTable()
	{
		$query="INSERT IGNORE INTO `gacl_axo_seq` (`id`) VALUES (13);";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function createGaclgroupsaromapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_groups_aro_map` (
				  `group_id` int(11) NOT NULL DEFAULT '0',
				  `aro_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`group_id`,`aro_id`),
				  KEY `gacl_aro_id` (`aro_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}					
	
	
	/*public function insertIntoGaclgroupsaromapTable()
	{
		$query="INSERT IGNORE INTO `gacl_groups_aro_map` (`group_id`, `aro_id`) VALUES
				(14, 102),
				(532, 102);	";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   */
	
	
	public function createGaclgroupsaxomapTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_groups_axo_map` (
				  `group_id` int(11) NOT NULL DEFAULT '0',
				  `axo_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`group_id`,`axo_id`),
				  KEY `gacl_axo_id` (`axo_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;
				";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}					
	
	public function createGaclphpgaclTable()
	{
		$query="CREATE TABLE IF NOT EXISTS `gacl_phpgacl` (
				  `name` varchar(230) NOT NULL,
				  `value` varchar(230) NOT NULL,
				  PRIMARY KEY (`name`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1;	";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}	
	
	public function insertIntoGaclphpgaclTable()
	{
		$query="INSERT IGNORE INTO `gacl_phpgacl` (`name`, `value`) VALUES
				('version', '3.3.7'),
				('schema_version', '2.1');";						
		mysql_query($query,$this->dbLink)or die(mysql_error());   
	}							
}
?>


