<?php
require_once "extensions/iInstaller.php";

class MDocContainerInstaller implements iInstaller {
	
	private $db;
	public $errors;

    function MDocContainerInstaller($db) {
    	$this->db = $db;
    }
        
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM `doc_container` LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'docs\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	//create table for document container
    	$query = "CREATE TABLE IF NOT EXISTS `doc_container` (" .
    			"  `id` int(11) NOT NULL AUTO_INCREMENT," .
		  		"  `name` varchar(120) NOT NULL," .
		  		"  `description` varchar(120) DEFAULT NULL," .
		  		"  `type` varchar(10) NOT NULL DEFAULT 'file'," .
		  		"  `link` varchar(360) DEFAULT NULL," .
		  		"  `parent_id` int(11) NOT NULL," .
		  		"  `parent_category` varchar(11) NOT NULL DEFAULT 'folder'," .
		  		"  PRIMARY KEY (`id`)" .
		  		") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$this->db->query($query);
		//check if documents exists in table module
		$query = "SELECT * FROM ".TB_MODULE." WHERE name = 'docs' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('docs')";
			$this->db->query($query);
		}
    }
    
    public function check() {
    	$classlist = array( "Doc.class.php",
				"DocContainerItem.class.php",
				"Folder.class.php",
				"MDocContainer.class.php",
				"MDocContainerInstaller.class.php"
			);
    	$templatelist = array("addDocItem.tpl",
				"bookmark.tpl",
				"deleteDocItem.tpl",
				"documentsList.tpl",
				"editDocItem.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/docs/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/docs/design/'.$templateFile)) {
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