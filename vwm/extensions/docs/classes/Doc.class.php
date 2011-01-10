<?php

class Doc extends DocContainerItem {
	//properties
	private $link;
	private $description;
	//metods
    function Doc($db) {
    	$this->db = $db;
    }
   
   /**
    * function addNewDoc
    * input: array('name'=>file name,'description'=>file description,'parent_id'=>id of parent,'parent_category'=>folder or facility)
    * Upload new doc and add info to bd
    */ 
    function addNewDoc($info) {
    	    	
		$tmp_name = $_FILES["inputFile"]['tmp_name'];
		$uploads_dir = "../docs";
		$currentFile['name'] = $_FILES["inputFile"]["name"];
		if (strripos($currentFile['name'],".") == false) {
			$ext = "";
			$extNumberSymbols = 0;
		} else {        		
			$ext = substr($currentFile['name'],strripos($currentFile['name'],"."));        		
			$extNumberSymbols = strlen($currentFile['name']) - strripos($currentFile['name'],".");
		}
		$currentFile['real_name'] = substr(md5(substr($currentFile['name'],0,-$extNumberSymbols)),0,5).time().$ext;

		$link = $uploads_dir."/".$currentFile['real_name'];
		move_uploaded_file($tmp_name, $link);
		if (trim($info['name']) == '') {
			if ($extNumberSymbols <> 0) {
			$info['name'] = Reform::HtmlEncode(substr($currentFile['name'],0,-$extNumberSymbols));
			} else {
				$info['name'] = Reform::HtmlEncode($currentFile['name']);
			}
		}
		//screening of quotation marks
		foreach ($info as $key=>$value)
		{
			$info[$key]=mysql_real_escape_string($value);
		}
		
		//$this->db->select_db(DB_NAME);
		$query="INSERT INTO `doc_container` ".
			"(`id`, `name`, `description`, `type`, `link`, `parent_id`, `parent_category`) VALUES (".
			"NULL, '".$info['name']."', '".$info['description']."', '".DocContainerItem::DOC_ITEM."', '".$link."', '".$info['parent_id']."', '".$info['parent_category']."');";    	
    	$this->db->query($query);
    }
    
    function editDoc($info) {
    	
    	//screening of quotation marks
		foreach ($info as $key=>$value)
		{
			$info[$key]=mysql_real_escape_string($value);
		}
    	
    	//$this->db->select_db(DB_NAME);
    	$query = "UPDATE `doc_container` SET ";
    	if (trim($info['name'])!='') {
			$query .= " `name` = '".$info['name']."',";
    	}
    	if (trim($info['description'])!='') {
			$query .= " `description` = '".$info['description']."', ";
    	}
		$query .= " `parent_id` = '".$info['parent_id']."', ".
			"`parent_category` = '".$info['parent_category']."' ".
			" WHERE `doc_container`.`id` ='".$info['file']."' ";
		$this->db->query($query);
    }
        
    function getDocWithInfoById($id) {
    	$id=mysql_real_escape_string($id);
    	$query = "SELECT link, name, description, parent_category, parent_id ".
   			"FROM doc_container ".
   			"WHERE id = ".$id." LIMIT 1";
   		$this->db->query($query);
   		if ($this->db->num_rows()>0) {
   			$data = $this->db->fetch(0);
   			$result = array(
	    		'id' => $id,
	    		'link' => $data->link,
	    		'name' => $data->name,
	    		'description' => $data->description,
	    		'parent_id' => (($data->parent_category == 'facility')?'0':$data->parent_id)
	    	);
	    	return $result;
   		} else {
   			return 0;
   		}
//    	$this->getLinkById($id);
//    	$this->getDescriptionById($id);
//    	$this->getNameById($id);
//    	$result = array(
//    		'id' => $id,
//    		'link' => $this->link,
//    		'name' => $this->name,
//    		'description' => $this->description,
//    		'parent_id' => $this->getParentId($id)
//    	);
    	return $result;
    }
       
    function getLinkById($id) {
    	$id=mysql_real_escape_string($id);
   		$query = "SELECT link ".
   			"FROM doc_container ".
   			"WHERE id = ".$id." LIMIT 1";
   		//$this->db->select_db(DB_NAME);
   		$this->db->query($query);
		if ($this->db->num_rows()==1) {
			$data = $this->db->fetch(0);
			$this->link = $data->link;		
		} else {
			return 0;
		}
    }
    
    function getDescriptionById($id) {
    	
    	$id=mysql_real_escape_string($id);
    	
   		$query = "SELECT description ".
   			"FROM doc_container ".
   			"WHERE id = ".$id." LIMIT 1";
   		//$this->db->select_db(DB_NAME);
   		$this->db->query($query);
		if ($this->db->num_rows()==1) {
			$data = $this->db->fetch(0);
			$this->description = $data->description;		
		} else {
			return 0;
		}    	
    }
    
    function getArrayOfLinksByIds($idArray) {
    	$idListed = '';
    	foreach ($idArray as $id) {
    		$idListed .= " '$id.',";
    	}
    	$idListed = substr($idListed,0,-1);
    	$query = "SELECT name, description, link, id FROM doc_container WHERE id IN ($idListed) ";
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		$data = $this->db->fetch_all();
    		$result = array();
    		foreach($data as $record) {
    			$result [$record->id]= array('name'=>$record->name, 'description'=>$record->description,'link'=>$record->link);
    		}
    		return $result;
    	} else {
    		return 0;
    	}
    }
}
?>