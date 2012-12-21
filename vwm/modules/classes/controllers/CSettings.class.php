<?php

class CSettings extends Controller {

	function CSettings($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'settings';
		
	}
	
	public function actionLoadRuleList(){
		
		$facilityId = $this->getFromRequest('facilityId');
		$companyID = $this->getFromRequest('companyId');
		
		if($facilityId!='false'){
			$categoryName = 'facility';
		}else{
			$categoryName = 'company';
		}
		
		$rule = new Rule($this->db);
        $ruleList = $rule->getRuleList();
		//$cfd = $this->noname();
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $companyID, $facilityId);
		
		
		$this->smarty->assign('customizedRuleList', $customizedRuleList);
		$this->smarty->assign('companyID', $companyID);
		$this->smarty->assign('facilityID', $facilityId);
		$this->smarty->assign('categoryName', $categoryName);
		$this->smarty->assign('userID', $_SESSION['user_id']);
		$this->smarty->assign('ruleList', $ruleList);
		echo $this->smarty->fetch('tpls/setManageRuleList.tpl');
	}
	
	function noname($request=null) {
        if ($request == null)
            $request = $this->request;
        switch ($request['category']) {
            case 'company':
                $companyID = $request['id'];
                $facilityID = null;
                $departmentID = null;
                $bookmark = null;
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'company');
                break;
            case 'facility':
                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($request['id']);
                $companyID = $facilityDetails['company_id'];
                $facilityID = $request['id'];
                $departmentID = null;
                $bookmark = 'department';
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'facility');
                break;
            case 'department':
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($request['id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($request['id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $request['id'];
                $bookmark = 'mix';
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'department');
                break;
            case 'mix':
                $mix = new Mix($this->db);
                $mixDetails = $mix->getMixDetails($request['id']);
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($mixDetails['department_id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($mixDetails['department_id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $mixDetails['department_id'];
                $bookmark = 'mix';
                //	set permissions
                $this->setListCategoriesLeftNew('department', $departmentID);
                $this->setNavigationUpNew('department', $departmentID);
                $this->setPermissionsNew('department');
                $this->smarty->assign('categoryName', 'department');
                break;
            case 'equipment':
                $equipment = new Equipment($this->db);
                $equipmentDetails = $equipment->getEquipmentDetails($request['id'], true);
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($equipmentDetails['department_id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($equipmentDetails['department_id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $equipmentDetails['department_id'];
                $bookmark = 'equipment';
                $this->setListCategoriesLeftNew('department', $departmentID);
                $this->setNavigationUpNew('department', $departmentID);
                $this->setPermissionsNew('department');
                $this->smarty->assign('categoryName', 'department');
                break;
        }
        return array(
            'companyID' => $companyID,
            'facilityID' => $facilityID,
            'departmentID' => $departmentID,
            'bookmark' => $bookmark
        );
    }

}

?>