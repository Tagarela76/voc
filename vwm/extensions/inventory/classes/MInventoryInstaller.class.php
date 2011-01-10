<?php
require_once "extensions/iInstaller.php";

class MInventoryInstaller implements iInstaller {
	
	private $db;
	public $errors;

    function MInventoryInstaller($db) {
    	$this->db = $db;
    }
        
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM '.TB_INVENTORY.', '.TB_ACCESSORY2INVENTORY.', '.TB_MATERIAL2INVENTORY.', '.TB_USE_LOCATION2MATERIAL.' LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'inventory\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
    	//create table for inventory
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_INVENTORY."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `name` varchar(75) NOT NULL," .
    			"  `description` varchar(300) NOT NULL," .
    			"  `type` varchar(12) NOT NULL," .
    			"  `facility_id` int(11) NOT NULL," .
    			"  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=246 ;";
    	$this->db->query($query);
    	//create table for accessory to inventory
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_ACCESSORY2INVENTORY."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `accessory_id` int(11) NOT NULL," .
    			"  `inventory_id` int(11) NOT NULL," .
    			"  `unit_amount` decimal(20,3) NOT NULL," .
    			"  `unit_count` varchar(120) default NULL," .
    			"  `unit_qty` decimal(20,3) NOT NULL," .
    			"  `total_qty` decimal(20,3) NOT NULL," .
    			"  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table for material to inventory
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_MATERIAL2INVENTORY."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `product_id` int(11) NOT NULL," .
    			"  `inventory_id` int(11) NOT NULL," .
    			"  `os_use` decimal(20,3) default NULL," .
    			"  `cs_use` decimal(20,3) default NULL," .
    			"  `storage_location` varchar(128) default NULL," .
    			"  `total_qty` decimal(20,3) NOT NULL," .
    			"  `last_update` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//create table for location use to material
    	$query = "CREATE TABLE IF NOT EXISTS `".TB_USE_LOCATION2MATERIAL."` (" .
    			"  `id` int(11) NOT NULL auto_increment," .
    			"  `department_id` int(11) NOT NULL," .
    			"  `material2inventory_id` int(11) NOT NULL," .
    			"  `total_qty` decimal(20,3) NOT NULL," .
    			"  PRIMARY KEY  (`id`)" .
    			") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    	$this->db->query($query);
    	//check if carbon_footprint exists in table module
		$query = "SELECT * FROM module WHERE name = 'inventory' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('inventory')";
			$this->db->query($query);
		}
    }
    
    public function check() {
    	$classlist = array( "Inventory.class.php",
				"MInventory.class.php",
				"MInventoryInstaller.class.php",
				"PaintAccessory.class.php",
				"PaintMaterial.class.php"
			);
    	$templatelist = array("addEquipment.tpl",
				"addInventoryNew.tpl",
				"addInventoryRow.tpl",
				"bookmark.tpl",
				"depBookmark.tpl",
				"inventoryListoop.tpl",
				"manageDepInventory.tpl",
				"viewEquipment.tpl",
				"viewInventoryNew.tpl"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/inventory/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/inventory/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
    	return $validation;
    }
}
?>