<?php
require_once "extensions/iInstaller.php";

class MLogbookInstaller implements iInstaller {
	
	private $db;
	public $errors;

    function MLogbookInstaller($db) {
    	$this->db = $db;
    }
    
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM logbook LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'logbook\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	$query = "CREATE TABLE IF NOT EXISTS `logbook` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `facility_id` int(11) NOT NULL," .
    			"  `date` date NOT NULL," .
    			"  `description` text," .
    			"  `operator` varchar(254) default NULL," .
    			"  `action` varchar(254) default NULL," .
    			"  `reason` varchar(254) default NULL," .
    			"  `filter_type` varchar(254) default NULL," .
    			"  `filter_size` varchar(254) default NULL," .
    			"  `department_id` int(11) default NULL," .
    			"  `equipment_id` varchar(11) default NULL," .
    			"  `link` varchar(360) default NULL," .
    			"  `type` varchar(64) NOT NULL," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$this->db->query($query);
		//check if logbook exists in table module
		$query = "SELECT * FROM module WHERE name = 'logbook' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('logbook')";
			$this->db->query($query);
		}    	
    }
    
    public function check() {
    	$classlist = array( "Logbook.class.php",
				"LogbookAccidentPlan.class.php",
				"LogbookAction.class.php",
				"LogbookFilter.class.php",
				"LogbookInspection.class.php",
				"LogbookMalfunction.class.php",
				"LogbookSampling.class.php",
				"MLogbook.class.php",
				"MLogbookInstaller.class.php"
			);
    	$templatelist = array("addLogbookRecord.tpl",
				"bookmark.tpl",
				"listOfLastRecords.tpl",
				"viewLogbook.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/logbook/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/logbook/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
		$uploads_dir = '../docs';
		if (!is_writable($uploads_dir)) {
			$validation = false;
			$this->errors []= 'Directory for docs upload('.$uploads_dir.') without permissions to write';
		}
    	return $validation;
    }
}
?>