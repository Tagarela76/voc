<?php
require_once "extensions/iInstaller.php";

class MReductionSchemeInstaller implements iInstaller {
	
	private $db;
	public $errors;	

    function MReductionSchemeInstaller($db) {
		$this->db = $db;
    }
    
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM '.TB_REDUCTION.', '.TB_SOLVENT_MANAGEMENT.', '.TB_SOLVENT_OUTPUT.' LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'reduction\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	//create table reduction
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_REDUCTION."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `facility_id` int(11) NOT NULL," .
    			"  `factor_are` decimal(10,2) NOT NULL default '1.50'," .
    			"  `factor_te` decimal(10,2) NOT NULL default '0.25'," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table for solvent management
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_SOLVENT_MANAGEMENT."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `facility_id` int(11) NOT NULL," .
    			"  `month` tinyint(2) NOT NULL," .
    			"  `year` year(4) NOT NULL," .
    			"  `output_id` varchar(10) NOT NULL," .
    			"  `value` decimal(20,2) NOT NULL," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table solvent outputs
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_SOLVENT_OUTPUT."` (" .
    			"  `output_id` varchar(10) NOT NULL," .
    			"  `name` varchar(225) NOT NULL," .
    			"  PRIMARY KEY  (`output_id`)" .
    			") ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    	$this->db->query($query);
    	//insert solvent outputs names
    	$query = "INSERT INTO `".TB_SOLVENT_OUTPUT."` (`output_id`, `name`) VALUES" .
    			"('o1', 'Emission waste gases ')," .
    			"('o2', 'Lost in water ')," .
    			"('o3', 'Residual in products ')," .
    			"('o4', 'Venting ')," .
    			"('o5', 'Abatement ')," .
    			"('o6', 'Collected waste ')," .
    			"('o7', 'Sold solvents to 3rd party ')," .
    			"('o8', 'Sent to recovery ')," .
    			"('o9', 'Other');";
    	$this->db->query($query);
		//check if reduction exists in table module
		$query = "SELECT * FROM module WHERE name = 'reduction' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('reduction')";
			$this->db->query($query);    
		}	
    }
    
    public function check() {
    	$classlist = array( "MReductionScheme.class.php",
				"MReductionSchemeInstaller.class.php",
				"ReductionScheme.class.php",
				"SolventManagement.class.php"
			);
    	$templatelist = array("bookmark.tpl",
				"bookmarkSolventPlan.tpl",
				"reduction.tpl",
				"solventPlanEdit.tpl",
				"solventPlanView.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/reduction/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/reduction/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
    	return $validation;
    }
}
?>