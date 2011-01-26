<?php

class MDocContainer extends Module {

    function MDocs() {
    }
    
    public function getNewObject(array $params) {
    	if ($params['type'] == DocContainerItem::DOC_ITEM) {
    		return new Doc($params['db']);
    	} elseif ($params['type'] == DocContainerItem::FOLDER_ITEM) {
    		return new Folder($params['db']);
    	} else {
    		return false;
    	}	
    }
    
    /**
     * function prepareView($params)
     * View list of docs/folders
     * return prepared for smarty params
     * @param $params array of params: db and facilityID
     */    
    function prepareView($params) {
	    $folder = new Folder($params['db']);
	    $InfoTree = $folder->getTreeWithInfo('facility',$params['facilityID']);
	    $result = array(
	    	"InfoTree" => $InfoTree,
	    	"doc_item" => DocContainerItem::DOC_ITEM,
	    	"folder_item" => DocContainerItem::FOLDER_ITEM
	    ); 
	    return $result;
    }
    
    /**
     * function prepareConstants($db)
     * return DocContainerItem constants: doc_item, folder_item
     * @param $db
     */    
    function prepareConstants($db) {
	    $folder = new Folder($db);
	    $result = array(
	    	"doc_item" => DocContainerItem::DOC_ITEM,
	    	"folder_item" => DocContainerItem::FOLDER_ITEM
	    ); 
	    return $result;
    }
    /**
     * function prepareAdd($params)
     * Add docs/folders
     * return prepared for smarty params if needed
     * @param $params array of params: db and all post data(folder, name, description, item_type),facilityID
     */
    function prepareAdd($params) {    	
		    if ($params['folder'] == "none") {
			    $params['folder'] = $params['facilityID'];
			    $params['category'] = "facility";
		    } else {
			    $params['category'] = "folder";
			    $result['folder_id'] = $params['folder'];
		    }
		    $info = array(
			    'name' => Reform::HtmlEncode($params['name']),
				'description' => Reform::HtmlEncode($params['description']),
				'parent_id' => $params['folder'],
				'parent_category' => $params['category']
		    ); 
		    
		    if ($params['item_type'] == DocContainerItem::DOC_ITEM) {
		    	if ($_FILES["inputFile"]['tmp_name'] == '') {
		    		$result['error'] = 'path';
		    						    
				    if ($params['category'] == 'facility') {
				    	$params['folder'] = 'none';
				    }
				    $result['info'] = $params;
		    	} else {
				    $doc = new Doc($params['db']);
				    $doc->addNewDoc($info);
				    //	redirect
				    header("Location: ?action=browseCategory&category=facility&id=".$params['facilityID']."&bookmark=docs&notify=12");
				    die();
		    	}
		    } else {
			    if (trim($info['name'])=='') {
				    $result['error'] = 'name';
				    
				    if ($params['category'] == 'facility') {
				    	$params['folder'] = 'none';
				    }
				    $result['info'] = $params;
			    } else {
				    $folder = new Folder($params['db']);
				    $folder->addNewFolder($info);
				    //	redirect
				    header("Location: ?action=browseCategory&category=facility&id=".$params['facilityID']."&bookmark=docs&notify=14");
				    die();
			    }	
		    }
    	return $result;
    }
    
    /**
     * function prepareEdit($params)
     * Edit docs/folders
     * return prepared for smarty params if needed
     * @param $params array of params: db and all post data(folder, file, name, description), facilityID
     */    
    function prepareEdit($params) {
	    $validFolder = true;
	    if ($params['folder'] == "none") {
		    $params['folder'] = $params['facilityID'];
		    $params['category'] = "facility";
	    } else {
		    $params['category'] = "folder";
		    $result['folder_id'] = $params['folder'];
		    
		    $parent_id = $params['folder'];
		    while ($parent_id != 0) {
			    if ($parent_id == $params['file']) {
				    $result['error'] = 'Not valid folder!';
				    $validFolder = false;
				    $result['info'] = $params;
				    break;
			    }
			    $doc = new Doc($params['db']);
			    $parent_id = $doc->getParentId($parent_id);
		    }
	    } 
	    
	    $info = array(
		    'file' => $params['file'],
			'name' => $params['name'],
			'description' => $params['description'],
			'parent_id' => $params['folder'],
			'parent_category' => $params['category']
	    ); 
	    
	    if ($validFolder) {
		    $doc = new Doc($params['db']);
		    $doc->editDoc($info);
		    //redirect
		    header("Location: ?action=browseCategory&category=facility&id=".$params['facilityID']."&bookmark=docs&notify=15");
		    die();
	    }
	    return $result;
    }    
    
    /**
     * function prepareViewDelete($params)
     * View docs/folders to choose docs for delete
     * return prepared for smarty params
     * @param $params array of params: db, facilityID, xnyo
     */    
    function prepareViewDelete($params) {
	    
	    $doc = new Doc($params['db']);
	    $id_list = $doc->getIdList($params['facilityID'],'facility');
	    
	    foreach($id_list as $id) {
		    $params['xnyo']->filter_post_var("doc_".$id, "text");
		    if ($_POST['doc_'.$id]!=null) {
			    $id_delete [$id]= "true";
			    if ($_POST['delete_type'] == 'all') {
				    $id_sub_list = $doc->getIdList($id);
				    foreach ($id_sub_list as $sub_id) {
					    $id_delete[$sub_id]="true";
				    }
			    }
		    }
	    } 
	    if (count($id_delete)==0) {
		    $result['empty'] = 'true';
	    } else {
		    $result['id_delete'] = $id_delete;
	    }
	    $result['step'] = 'confirm';
	    return $result;
    }
    
    /**
     * function prepareDelete($params)
     * Delete docs/folders
     * return true
     * @param $params array of params: db, facilityID, xnyo
     */    
    function prepareDelete($params) {
	    $doc = new Doc($params['db']);
	    $id_list = $doc->getIdList($params['facilityID'],'facility');
	    
	    foreach($id_list as $id) {
		    $params['xnyo']->filter_post_var("doc_".$id, "text");
		    if ($_POST['doc_'.$id]!=null) {
			    $info = array(
				    'id' => $id,
					'delete_type' =>$_POST['delete_type']
			    ); 	
			    $doc->deleteDocs($info);									
		    }
	    } 
	    return true;
    }
    
    function prepareStorageAdd($params) {
    	$result = $this->prepareView($params);
    	$result['category'] = 'wastestorage';
    	return $result;
    }
    
    function prepareStorageView($params) {
    	extract($params);
    	$doc = new Doc($db);
    	$info = $doc->getDocWithInfoById($id);
    	return array('doc' => $info);
    }
    
    function prepareStorageBrowse($params) {
    	extract($params);
    	$doc = new Doc($db);
    	$linkArray = $doc->getArrayOfLinksByIds($idArray);
    	return array ('docs' => $linkArray);
    }
}
?>