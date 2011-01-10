<?php
require_once "extensions/iInstaller.php";

class MWasteStreamsInstaller implements iInstaller {
	
	private $db;
	public $errors;
	
    function MWasteStreamsInstaller($db) {
		$this->db = $db;
    }
    
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM '.TB_WASTE_STREAMS.', '.TB_POLLUTION.', '.TB_STORAGE.', '.TB_STORAGE_EMPTY.', '.TB_STORAGE_DELETED.'  LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'waste_streams\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	//create table waste_streams
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_WASTE_STREAMS."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `name` varchar(25) NOT NULL," .
    			"  `density` decimal(5,2) default NULL," .
    			"  `density_unit_id` int(11) NOT NULL default '1'," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;";
    	$this->db->query($query);
    	//create table pollutions
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_POLLUTION."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `name` varchar(25) NOT NULL," .
    			"  `waste_stream_id` int(11) NOT NULL," .
    			"  `density` decimal(5,2) NOT NULL," .
    			"  `density_unit_id` int(11) NOT NULL default '1'," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;";
    	$this->db->query($query);
    	//create table for storages
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_STORAGE."` (" .
    			"  `storage_id` int(11) NOT NULL auto_increment," .
    			"  `facility_id` int(11) NOT NULL," .
    			"  `name` varchar(225) NOT NULL," .
    			"  `capacity_volume` decimal(20,2) NOT NULL," .
    			"  `capacity_weight` decimal(20,2) NOT NULL," .
    			"  `max_period` int(11) NOT NULL," .
    			"  `suitability` int(11) NOT NULL," .
    			"  `use_date` date default NULL," .
    			"  `active` tinyint(1) NOT NULL default '1'," .
    			"  `volume_unittype` int(11) NOT NULL," .
    			"  `weight_unittype` int(11) NOT NULL," .
    			"  PRIMARY KEY  (`storage_id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table for storage's empty dates
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_STORAGE_EMPTY."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `storage_id` int(11) NOT NULL," .
    			"  `date` date NOT NULL," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table for storage's delete dates
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_STORAGE_DELETED."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `storage_id` int(11) NOT NULL," .
    			"  `date` date NOT NULL," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//insert values for waste_streams
    	$query = "INSERT INTO `".TB_WASTE_STREAMS."` (`id`, `name`, `density`, `density_unit_id`) VALUES" .
    			"(1, 'water', 2.00, 1)," .
    			"(2, 'solvent', 3.00, 1)," .
    			"(3, 'chemicals', 5.00, 1)," .
    			"(4, 'oils', NULL, 1)," .
    			"(5, 'acid', NULL, 1)," .
    			"(6, 'toxic', NULL, 1)," .
    			"(7, 'chemical', NULL, 1)," .
    			"(8, 'other hazardous', NULL, 1);";
    	$this->db->query($query);
    	//insert pollutions
    	$query = "INSERT INTO `".TB_POLLUTION."` (`id`, `name`, `waste_stream_id`, `density`, `density_unit_id`) VALUES" .
    			"(1, 'paint pigments', 1, 5.00, 1)," .
    			"(2, 'acids', 1, 3.45, 1)," .
    			"(3, 'toxics', 2, 3.20, 1)," .
    			"(4, 'oils', 2, 7.90, 1)," .
    			"(5, 'chemicals', 1, 0.00, 1)," .
    			"(6, 'toxics', 1, 0.00, 1)," .
    			"(7, 'chemicals', 2, 0.00, 1)," .
    			"(8, 'acids', 2, 0.00, 1);";
    	$this->db->query($query);
    	//check if wate streams exists in table module
		$query = "SELECT * FROM module WHERE name = 'waste_streams' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('waste_streams')";
			$this->db->query($query);
		}
    }
    
    public function check() {
    	$classlist = array( "MWasteStreams.class.php",
			"MWasteStreamsInstaller.class.php",
			"Storage.class.php",
			"WasteStreams.class.php"
			);
    	$templatelist = array("bookmarkWasteStorage.tpl",
			"editStorage.tpl",
			"indicator.tpl",
			"viewWasteStorageDetails.tpl",
			"wasteStorageView.tpl",
			"wasteStreams.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/waste_streams/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/waste_streams/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
    	return $validation;
    }
}
?>