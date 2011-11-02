<?php

class CASetupRequest extends Controller {

	function CASetupRequest($smarty,$xnyo,$db,$user,$action) {
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
		$this->bookmarkSetupRequest($vars);
	}
	
	protected function bookmarkSetupRequest($vars){
		extract($vars);
		$setupRequest = new SetupRequest($this->db);
		$query = "SELECT * FROM ".TB_COMPANY_SETUP_REQUEST." WHERE 1";
		$this->db->query($query);
		$rows = $this->db->fetch_all();
		foreach ($rows as $row){
			if ($row->category == 'company'){
				$this->db->query("SELECT name FROM ".TB_COUNTRY." WHERE country_id=".$row->country_id);
				$row->country_name = $this->db->fetch(0)->name;
				$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
				$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
				$row->url = "admin.php?action=viewDetails&category=setupRequest&id=".$row->id;
				$setupRequestArray['company'][] = $row;
			} elseif ($row->category == 'facility'){
				$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$row->parent_id);
				$row->parent_name = $this->db->fetch(0)->name;
				$this->db->query("SELECT name FROM ".TB_COUNTRY." WHERE country_id=".$row->country_id);
				$row->country_name = $this->db->fetch(0)->name;
				$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id);
				$row->creater_name = $this->db->fetch(0)->username;
				$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
				$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
				$row->url = "admin.php?action=viewDetails&category=setupRequest&id=".$row->id;
				$setupRequestArray['facility'][] = $row;
			} elseif ($row->category == 'department'){
				$this->db->query("SELECT name, company_id FROM ".TB_FACILITY." WHERE facility_id=".$row->parent_id);
				$row->parent_name = $this->db->fetch(0)->name;
				$companyID = $this->db->fetch(0)->company_id;
				$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$companyID);
				$row->company_name = $this->db->fetch(0)->name;
				$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id);
				$row->creater_name = $this->db->fetch(0)->username;
				$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
				$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
				$row->url = "admin.php?action=viewDetails&category=setupRequest&id=".$row->id;
				$setupRequestArray['department'][] = $row;
			}
		}
		//var_dump($setupRequestArray);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('setupRequest', $setupRequestArray);
		$this->smarty->assign('tpl', 'tpls/setupRequest.tpl');
	}
	
	private function actionViewDetails() {
		if ($_POST['actionSave'] == 'Save') {
			$requestID = $this->getFromRequest('id');
			$setupRequest = new SetupRequest($this->db);
			$setupRequest->setStatus($_POST['selectStatus']);
			$setupRequest->update($requestID);
			if ($_POST['selectStatus'] == 'accept'){
				switch ($_POST['category']){
					case 'company':
						$error = $setupRequest->addNewCompany($requestID, $_POST['comment']);
						break;
					case 'facility':
						$error = $setupRequest->addNewFacility($requestID, $_POST['comment']);	
						break;
					case 'department':
						$error = $setupRequest->addNewDepartment($requestID, $_POST['comment']);
						break;
				}
			} elseif ($_POST['selectStatus'] == 'deny'){
				$setupRequest->denySetupRequest($requestID, $_POST['comment']);
				header ('Location: admin.php?action=browseCategory&category=requests&bookmark=setupRequest');
				die();
			}
			if ($error == ''){
				header ('Location: admin.php?action=browseCategory&category=requests&bookmark=setupRequest');
				die();
			} else {
				$setupRequest->setStatus('new');
				$setupRequest->update($requestID);
				$this->smarty->assign('error', $error);
			}
		}
		$setupRequest = new SetupRequest($this->db);
		$query = "SELECT * FROM ".TB_COMPANY_SETUP_REQUEST." WHERE id=".$this->getFromRequest('id');
		$this->db->query($query);
		$row = $this->db->fetch(0);
		if ($row->category == 'company'){
			$this->db->query("SELECT name FROM ".TB_COUNTRY." WHERE country_id=".$row->country_id);
			$row->country_name = $this->db->fetch(0)->name;
			$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
		} elseif ($row->category == 'facility'){
			$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$row->parent_id);
			$row->parent_name = $this->db->fetch(0)->name;
			$this->db->query("SELECT name FROM ".TB_COUNTRY." WHERE country_id=".$row->country_id);
			$row->country_name = $this->db->fetch(0)->name;
			$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id);
			$row->creater_name = $this->db->fetch(0)->username;
			$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
		} elseif ($row->category == 'department'){
			$this->db->query("SELECT name, company_id FROM ".TB_FACILITY." WHERE facility_id=".$row->parent_id);
			$row->parent_name = ($this->db->fetch(0)->name);
			$companyID = $this->db->fetch(0)->company_id;
			$this->db->query("SELECT name FROM ".TB_COMPANY." WHERE company_id=".$companyID);
			$row->company_name = $this->db->fetch(0)->name;
			$this->db->query("SELECT username FROM ".TB_USER." WHERE user_id=".$row->creater_id);
			$row->creater_name = $this->db->fetch(0)->username;
			$setupRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$row->date = $setupRequest->getDate()->format(DEFAULT_DATE_FORMAT);
		}
		$row->back_url = "admin.php?action=browseCategory&category=requests&bookmark=setupRequest";
		
		$this->smarty->assign('setupRequest', $row);
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('tpl', 'tpls/viewSetupRequest.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}
?>