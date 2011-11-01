<?php

class CAUserRequest extends Controller {

	function CAUserRequest($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='userRequest';
		$this->parent_category='requests';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	protected function actionBrowseCategory($vars) {
		$this->bookmarkUserRequest($vars);
	}
	
	protected function bookmarkUserRequest($vars){
		extract($vars);
		$userRequest = new UserRequest($this->db);
		$query = "SELECT * FROM ".TB_USER_REQUEST." WHERE 1";
		$this->db->query($query);
		$rows = $this->db->fetch_all();
		foreach ($rows as $row){
			$userRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$row->date = $userRequest->getDate()->format(DEFAULT_DATE_FORMAT);
			$queryUser = "SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id;
			$this->db->query($queryUser);
			$row->creater_user = $this->db->fetch(0)->username;
			if ($row->action == 'add'){
				$row->action = 'Add new user';
				if ($row->category_type == 'facility'){
					$querySelect = "SELECT name FROM ".TB_FACILITY." WHERE facility_id=".$row->category_id;
				} elseif ($row->category_type == 'department') {
					$querySelect = "SELECT name FROM ".TB_DEPARTMENT." WHERE department_id=".$row->category_id;
				} elseif ($row->category_type == 'company') {
					$querySelect = "SELECT name FROM ".TB_COMPANY." WHERE company_id=".$row->category_id;
				}
				$this->db->query($querySelect);
				if ($this->db->num_rows() > 0){
					$row->title = $this->db->fetch(0)->name;
				}
				$row->url = "admin.php?action=viewDetails&category=userRequest&id=".$row->id;
				$requests['add'][] = $row;
			} elseif ($row->action == 'delete') {
				$row->action = 'Delete user';
				$row->url = "admin.php?action=viewDetails&category=userRequest&id=".$row->id;
				$requests['delete'][] = $row;
			} elseif ($row->action == 'change') {
				$row->action = 'Change user';
				$row->url = "admin.php?action=viewDetails&category=userRequest&id=".$row->id;
				$requests['change'][] = $row;
			}
		}
		//var_dump($requests); die();
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('requests' ,$requests);
		$this->smarty->assign('tpl', 'tpls/userRequest.tpl');
	}
	
	private function actionViewDetails() {
		if ($_POST['actionSave'] == 'Save') {
			$requestID = $this->getFromRequest('id');
			$userRequest = new UserRequest($this->db);
			$userRequest->setStatus($_POST['selectStatus']);
			$userRequest->update($requestID);
			if ($_POST['selectStatus'] == 'accept'){
				switch ($_POST['actionType']){
					case 'add':
						$error = $userRequest->addNewUser($requestID, $_POST['comment']);
						break;
					case 'delete':
						$error = $userRequest->deleteUser($requestID, $_POST['comment']);	
						break;
					case 'change':
						$error = $userRequest->changeUser($requestID, $_POST['comment']);
						break;
				}
			} elseif ($_POST['selectStatus'] == 'deny'){
				$userRequest->denyUserRequest($requestID, $_POST['comment']);
				header ('Location: admin.php?action=browseCategory&category=requests&bookmark=userRequest');
				die();
			}
			if ($error == ''){
				header ('Location: admin.php?action=browseCategory&category=requests&bookmark=userRequest');
				die();
			} else {
				$userRequest->setStatus('new');
				$userRequest->update($requestID);
			}
		}
		$userRequest = new UserRequest($this->db);
		$query = "SELECT * FROM ".TB_USER_REQUEST." WHERE id=".$this->getFromRequest('id');
		$this->db->query($query);
		$row = $this->db->fetch(0);
		$userRequest->setDate(DateTime::createFromFormat('U', $row->date));
		$row->date = $userRequest->getDate()->format(DEFAULT_DATE_FORMAT);
		$queryUser = "SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id;
		$this->db->query($queryUser);
		$row->creater_user = $this->db->fetch(0)->username;
		if ($row->action == 'add'){
			$row->action_type = $row->action;
			$row->action = 'Add new user';
		} elseif ($row->action == 'delete') {
			$row->action_type = $row->action;
			$row->action = 'Delete user';			
		} elseif ($row->action == 'change') {
			$row->action_type = $row->action;
			$row->action = 'Change username';
		}
		if ($row->category_type == 'facility'){
			$this->db->query("SELECT name, company_id FROM ".TB_FACILITY." WHERE facility_id=".$row->category_id);
			$row->facility_name = $this->db->fetch(0)->name;
			$companyID = $this->db->fetch(0)->company_id;
			$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$companyID);
			$row->company_name = $this->db->fetch(0)->name;
		} elseif ($row->category_type == 'department') {
			$this->db->query("SELECT name, facility_id FROM ".TB_DEPARTMENT." WHERE department_id=".$row->category_id);
			$row->department_name = $this->db->fetch(0)->name;
			$facilityID = $this->db->fetch(0)->facility_id;
			$this->db->query("SELECT name, company_id FROM ".TB_FACILITY." WHERE facility_id=".$facilityID);
			$row->facility_name = $this->db->fetch(0)->name;
			$companyID = $this->db->fetch(0)->company_id;
			$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$companyID);
			$row->company_name = $this->db->fetch(0)->name;
		} elseif ($row->category_type == 'company') {
			$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$row->category_id);
			$row->company_name = $this->db->fetch(0)->name;
		}
		$row->back_url = "admin.php?action=browseCategory&category=requests&bookmark=userRequest";
		
		$this->smarty->assign('userRequest', $row);
		$this->smarty->assign('tpl', 'tpls/viewUserRequest.tpl');
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->display("tpls:index.tpl");
	}
}
?>