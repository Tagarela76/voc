<?php
class Bookmark {    
    
        private $id;
        private $name;
        private $type;
        private $db;
        public $errors;
    
	function __construct($db, Array $array = null) {
                $this->db=$db;
		
		if(isset($array)) {
			$this->initByArray($array);
		}
        }
        
	private function initByArray($array) {
		
		foreach($array as $key => $value) {
			try {
				$this->__set($key, $value);
			}catch(Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}        
        
        private function set_id($value) {
		try {
			$this->id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!");
		}
	}
	
	private function set_name($value) {
		try {
			$value = $this->escapeValue($value);
			$this->name = $value;
		} catch(Exception $e) {
			$this->errors['name'] = $e->getMessage();
			throw new Exception("Name cannot be empty!");
		}
		
		$this->name = $value;
	}
        
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
        
	public function saveBookmark() {

		$query = "UPDATE " . TB_CONTACTS_TYPE . " SET 
					id = '{$this->id}',
					name = '{$this->name}',
					controller = '{$this->type}'
					WHERE id = {$this->id}";
		
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
        
        public function addBookmark() {
		if(!$b->errors) {

			$query = "INSERT INTO " . TB_CONTACTS_TYPE . " (id, name, controller) VALUES (
						'{$this->id}', '{$this->name}', '{$this->type}')";

			$this->db->query($query);
			
			if(mysql_error() == '') {
				return true;
			} else {
				throw new Exception(mysql_error());
			}
		}
	}
        
	public function deleteBookmark($bookmarkID) {
		$query = "DELETE FROM ". TB_CONTACTS_TYPE . " WHERE id = $bookmarkID";
		$query = mysql_escape_string($query);
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
    
}

?>