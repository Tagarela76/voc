<?php

class Folder extends DocContainerItem {
	//propeties
	private $itemCount;
	//metods
    function Folder($db) {
    	$this->db = $db;
    }
    
	/**
	 * function getTreeWithInfo
	 * input: category, id
	 * output: array of elements(each element contain 'info', 'type', 'level')
	 *     'type' - 'doc' or 'folder'
	 *     'info' - if 'type'='doc': link, name, description; if 'type'='folder': id, name, count
	 *     'level' - the main level of tree 
	 */       
    function getTreeWithInfo($category,$id,$level = 0) {
    	$data = $this->getAllChild($category,$id);
		if ($data==0) {
			return 0;
		}
		$result = array();
		foreach ($data as $element) {
			if ($element['type'] == DocContainerItem::DOC_ITEM) {
				$doc = new Doc($this->db);
				$elem = array(
					'info' => $doc->getDocWithInfoById($element['id']),
					'type' => DocContainerItem::DOC_ITEM,
					'level' => $level
				);
				$result [] = $elem;
			} else {
				$elem = array(
					'info' => $this->getFolderWithInfoById($element['id']),
					'type' => DocContainerItem::FOLDER_ITEM,
					'level' => $level
				);
				$result [] = $elem;
				$elements = $this->getTreeWithInfo(DocContainerItem::FOLDER_ITEM,$element['id'],$level+1);
				foreach($elements as $elem) {
					$result [] = $elem;
				}
				//var_dump($elem);
			}
		}
		return $result;	
    }
    
    function addNewFolder($info) {
    	
    	//screening of quotation marks
		foreach ($info as $key=>$value)
		{
			$info[$key]=mysql_escape_string($value);
		}
    	
    	//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO `doc_container` ".
			"(`id`, `name`, `type`, `parent_id`, `parent_category`) VALUES (".
			"NULL, '".$info['name']."', '".DocContainerItem::FOLDER_ITEM."', '".$info['parent_id']."', '".$info['parent_category']."');";    	
    	$this->db->query($query);
    }
    
    function editFolder($info) {
    	
    	//screening of quotation marks
		foreach ($info as $key=>$value)
		{
			$info[$key]=mysql_escape_string($value);
		}
    	
    	$this->db->selecy_db(DB_NAME);
    	$query = "UPDATE `doc_container` SET ";
    	if (trim($info['name'])!='') {
			$query .= " `name` = '".$info['name']."',";
    	}
		$query .= " `parent_id` = '".$info['parent_id']."', ".
			"`parent_category` = '".$info['parent_category']."' ".
			" WHERE `doc_container`.`id` ='".$info['file']."' ";
		$this->db->query($query);
    }
    
    function getFolderWithInfoById($id) {
    	$count = $this->getItemCountById($id);
    	$this->getNameById($id);
    	$result = array(
    		'id' => $id,
    		'name' => $this->name,
    		'count' => $count,
    		'parent_id' => $this->getParentId($id)
    	);
    	return $result;
    }

    function getItemCountById($id) {
    	$data = $this->getAllChild(DocContainerItem::FOLDER_ITEM,$id);
    	if ($data!=0) {
    		return count($data);
    	} else {
    		return 0;
    	}
    }
}
?>