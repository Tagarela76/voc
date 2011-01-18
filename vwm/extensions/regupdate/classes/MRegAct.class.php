<?php

class MRegAct extends Module {

    function MRegAct() {
    }
    
    public function getNewObject($db) {
    	return new RegActManager($db);
    }
    
    /**
     * function prepareView
     * @param $params - array of params: $db, $userID, $tab 
     * @return array data for smarty
     */
    public function prepareView($params) {
    	extract($params);
    	$regActManager = new RegActManager($db);
    	$regActList = $regActManager->getRegActsList($userID, $tab);
    	$TabsCount = $regActManager->getUnreadCountForCategories($userID);
    	$countByCategory = array();
    	foreach($TabsCount as $data) {
    		$countByCategory[$data['category']] = $data['count'];
    	}
    	return array(
			'data' => $regActList,
			'countForTabs' => $countByCategory
			);
    }
    
    /**
     * function prepareCountUnread
     * @param $params - array of params: $db, $userID
     * @return $count  -  for smarty('unreadedRegUpdatesCount')
     */
    public function prepareCountUnread($params) {
    	extract($params);
    	$regActManager = new RegActManager($db);
    	$data = $regActManager->getUnreadCountForCategories($userID);
    	$count = 0;
    	foreach($data as $countData) {
    		$count += $countData['count'];
    	}
    	return $count;
    }
    
    /**
     * function prepareMarkRead
     * @param $params - array of params: $db, $userID, $mark = 'all'/category/idToMark
     */
    public function prepareMarkRead($params) {
    	extract($params);
    	$regActManager = new RegActManager($db);
    	$rin = null;
    	$category = null;
    	if($mark == RegActManager::CATEGORY_REVIEW || $mark == RegActManager::CATEGORY_COMPLETED) {
    		$category = $mark;
    	} elseif ($mark != 'all') {
    		$rin = array($mark);
    	}
    	$regActManager->markRIN($userID,'readed',$rin,$category);
    }
}
?>