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
			$result['date'] = $userRequest->getDate()->format(DEFAULT_DATE_FORMAT);
			$queryUser = "SELECT username FROM ".TB_USER." WHERE user_id=".$row->user_id;
			$this->db->query($queryUser);
			$result['creator_user'] = $this->db->fetch(0)->username;
			$result['status'] = $row->status;
			if ($row->action == 'add'){
				$result['action'] = 'Add new user';
				$result['username'] = $row->new_username;
				$result['access_level'] = $row->category_type;
				if ($row->category_type == 'facility'){
					$querySelect = "SELECT name FROM ".TB_FACILITY." WHERE facility_id=".$row->category_id;
				} elseif ($row->category_type == 'department') {
					$querySelect = "SELECT name FROM ".TB_DEPARTMENT." WHERE department_id=".$row->category_id;
				}
				$this->db->query($querySelect);
				if ($this->db->num_rows() > 0){
					$result['title'] = $this->db->fetch(0)->name;
				}
				$requests['add'][] = $result;
			} elseif ($row->action == 'delete') {
				$result['action'] = 'Delete user';
				$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->username_id);
				$result['username'] = $this->db->fetch(0)->username;
				$requests['delete'][] = $result;
			} elseif ($row->action == 'change') {
				$result['action'] = 'Change user';
				$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->username_id);
				$result['username'] = $this->db->fetch(0)->username;
				$result['new_username'] = $row->new_username;
				$requests['change'][] = $result;
			}
		}
		
		$this->smarty->assign('requests' ,$requests);
		$this->smarty->assign('tpl', 'tpls/userRequest.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}
?>