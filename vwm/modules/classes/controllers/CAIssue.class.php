<?php

class CAIssue extends Controller {
	
	function CAIssue($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='issue';
		$this->parent_category='issue';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$title = new Titles($this->smarty);
		$title->titleIssuesList();
		
		$issue = new Issue($this->db);
		$issues = $issue->getIssuesList();
		$itemsCount = count($issues);
		
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=issue&id=".$issues[$i]['issueID'];
			$issues[$i]['url']=$url;
		}
		
		$this->smarty->assign("category", $issues);
		$this->smarty->assign("itemsCount", $itemsCount);
		
		$jsSources = array('modules/js/checkBoxes.js');
		
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/issuesList.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionViewDetails() {
		$issue = new Issue($this->db);
		$issueDetails = $issue->getIssueDetails($this->getFromRequest('id'));
		$issueDetails['author'] = $this->user->getAccessnameByID($issueDetails['creatorID']);
		
		$this->smarty->assign("issue", $issueDetails);
		$this->smarty->assign('tpl', 'tpls/viewIssue.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		//	Group data
		$issue["issueID"] = $this->getFromRequest("id");
		$issue["status"] = $_POST["status"];
		$issue["priority"] = $_POST["priority"];
		
		//	Update Item
		$issueItem = new Issue($this->db);
		$issueItem->updateIssueDetails($issue);
		
		//	Show notify
		//$notify = new Notify($this->smarty);
		//$notify->successEditedAdmin($itemID, "");
		
		//	Display Issue View page
		header ('Location: admin.php?action=browseCategory&category=issue');
		die();
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$issueItem = new Issue($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$issueDetails = $issueItem->getIssueDetails($this->getFromRequest('item_'.$i));
				if ($issueDetails["status"] != 'new') {
					$item["id"]	=	$issueDetails["issueID"];
					$item["name"]=	$issueDetails["title"];
					$itemForDelete []= $item;
				}
			}
		}
		if (count($itemForDelete) == 0) {
			header ('Location: admin.php?action=browseCategory&category='.$this->getFromRequest('category'));
			die();
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$issueItem = new Issue($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$issueItem->deleteIssue($id);
		}
		header ('Location: admin.php?action=browseCategory&category='.$this->getFromRequest('category'));
		die();
	}
}
?>