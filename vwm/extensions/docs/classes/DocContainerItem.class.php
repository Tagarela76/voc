<?php

class DocContainerItem {
	protected $id;
	protected $name;
	protected $type;
	protected $parentId;
	protected $parentCategory;
	protected $db;
	
	const DOC_ITEM = 'file';
	const FOLDER_ITEM = 'folder';

    function DocContainerItem() {
    }
    
    function deleteDocs($info) {
    	
    	//screening of quotation marks
		foreach ($info as $key=>$value)
		{
			$info[$key]=mysql_real_escape_string($value);
		}
    	
    	//$this->db->select_db(DB_NAME);
    	$query = "SELECT `doc_container`.`parent_id`, `doc_container`.`parent_category` FROM `doc_container`".
			" WHERE `doc_container`.`id` = '".$info['id']."' ";
		$this->db->query($query);
		if ($this->db->num_rows()==1) {
			$data = $this->db->fetch(0);
			$this->parent_id = $data->parent_id;
			$this->parent_category = $data->parent_category;		
		}
    	$query = "DELETE FROM `doc_container` WHERE `doc_container`.`id` = '".$info['id']."'";
    	$this->db->query($query);
    	if ($info['delete_type']=='all') {
    		$id_list = $this->getIdList($info['id']);
    		foreach ($id_list as $id) {
    			$query = "DELETE FROM `doc_container` WHERE `doc_container`.`id` = '".$id."'";
    		}
    	} else {
    		$query = "UPDATE `doc_container` SET `parent_id` = '".$this->parent_id."', ".
    			" `parent_category` = '".$this->parent_category."' ".
    			" WHERE `doc_container`.`parent_id` = '".$info['id']."' ";
    	}
    	$this->db->query($query);
    }
      
    function getAllChild($parent_Category, $parent_Id) {
    	
    	$parent_Category=mysql_real_escape_string($parent_Category);
    	$parent_Id=mysql_real_escape_string($parent_Id);
    	
    	$query = "SELECT id, type ". 
			"FROM doc_container ".
			"WHERE parent_category = '".$parent_Category."' ".
			"AND  parent_id = ".$parent_Id." ";   
		//$this->db->select_db(DB_NAME);	
		$this->db->query($query);
		$result = array();
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				//creation of an index tree
				$result [] = array(
					'id' => $data->id,
					'type' => $data->type
				);
			}
			return $result;
		} else {
			return 0;
		}
    }

    function getFullInfoById($id) {
    	
    	$id=mysql_real_escape_string($id);
    	
   		$query = "SELECT id, name, type, parent_id, parent_category ".
			"FROM doc_container ".
			"WHERE id = ".$id." LIMIT 1"; 
		//$this->db->select_db(DB_NAME);
		$this->db->query($query);
		if ($this->db->num_rows()==1) {
			$data = $this->db->fetch(0);
			return $data;		
		} else {
			return 0;
		}
    }
    
    function getNameById($id) {
    	
    	$id=mysql_real_escape_string($id);
    	
    	$query = "SELECT name ".
    		"FROM doc_container ".
    		"WHERE id = ".$id." LIMIT 1";
    	//$this->db->select_db(DB_NAME);
    	$this->db->query($query);
		if ($this->db->num_rows()==1) {
			$data = $this->db->fetch(0);
			$this->name = $data->name;		
		} else {
			return 0;
		}       	
    }
     
    function getIdList($parent_Id, $type = "folder") {
    	
    	$parent_Id=mysql_real_escape_string($parent_Id);
    	$type=mysql_real_escape_string($type);
    	
    	$query = "SELECT id ".
    		"FROM doc_container ".
    		"WHERE parent_id = ".$parent_Id;
//    	if ($type == 'facility') {
    		$query .= " AND parent_category = '".$type."' ";
//    	}

    	//$this->db->select_db(DB_NAME);
    	$this->db->query($query);
    	$id_list = array();
    	if ($this->db->num_rows()>0) {
    		for ($i=0; $i<$this->db->num_rows(); $i++) {
    			$data = $this->db->fetch($i);
    			$id_list []= $data->id;
    		}
    		$n = count($id_list);
    		for ($i=0; $i<$n; $i++) {
    			$sub_list = $this->getIdList($id_list[$i]);
    			if(is_array($sub_list)) {
    				foreach($sub_list as $list_item) {
    					$id_list []= $list_item;
    				}
    			}    			
    		}
    	} else {
    		return 0;
    	}
    	return $id_list;
    }
    
    function getParentId($id) {
    	
    	$id=mysql_real_escape_string($id);
    	
    	$query = "SELECT parent_id, parent_category ".
    		"FROM doc_container ".
    		"WHERE id = '".$id."' LIMIT 1";

    	//$this->db->select_db(DB_NAME);
    	$this->db->query($query);
    	$id_list = array();
    	if ($this->db->num_rows()>0) {
    			$data = $this->db->fetch(0);
    			if ($data->parent_category == 'facility') {
    				return 0;
    			} else {
    				return $data->parent_id;
    			}

    	} 
    }
}
?>