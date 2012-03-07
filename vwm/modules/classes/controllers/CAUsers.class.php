<?php

class CAUsers extends Controller {
	
	function CAUsers($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='users';
		$this->parent_category='users';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$bookmark = $this->getFromRequest('bookmark');
		
		/*FILTER*/									
		$filter=new Filter($this->db,'users');	
		
		$this->smarty->assign('filterArray',$filter->getJsonFilterArray());
		$filterData= array
			(
				'filterField'=>$this->getFromRequest('filterField'),
				'filterCondition'=>$this->getFromRequest('filterCondition'),
				'filterValue'=>$this->getFromRequest('filterValue')
			);
		
		if ($this->getFromRequest('searchAction')=='filter') {
			$this->smarty->assign('filterData',$filterData);	
			$this->smarty->assign('searchAction','filter');									
		}
		$filterStr = $filter->getSubQuery($filterData);
		/*/FILTER*/	
		
		/*SORT*/
		$sortStr="";
		if (!is_null($this->getFromRequest('sort')))
		{
			$sort= new Sort($this->db,'users',0);
			$sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));										
			$this->smarty->assign('sort',$this->getFromRequest('sort'));
		}
		else									
			$this->smarty->assign('sort',0);
		
		if (!is_null($this->getFromRequest('searchAction')))									
			$this->smarty->assign('searchAction',$this->getFromRequest('searchAction'));
		/*/SORT*/
		
		$pagination = new Pagination($this->user->queryTotalCount($bookmark,$filterStr));
		$pagination->url = "?action=browseCategory&category=users&bookmark=$bookmark".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 										
		
		
		$usersList=$this->user->getUsersList($bookmark,$pagination, $filterStr,$sortStr);
		$itemsCount=count($usersList);
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=users&bookmark=$bookmark&id=".$usersList[$i]['user_id']; //we can cut out the bookmark(it's no needed)
			$usersList[$i]['url']=$url;
		}

		$this->smarty->assign("category",$usersList);
		$this->smarty->assign("itemsCount",$itemsCount);
		$jsSources = array('modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/users.tpl');
		$this->smarty->assign('pagination', $pagination);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionViewDetails() {
		$userDetails=$this->user->getUserDetails($this->getFromRequest('id'));

		$this->smarty->assign("user", $userDetails);
		$this->smarty->assign('tpl', 'tpls/viewUser.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($_POST['save'] == 'Register') {						
			$data = $this->getFromPost();
			$data['grace']=14;//???
			$check = array();
			foreach($data as $key => $value) {
				$check[$key] = 'ok';
			}
			
			if (strlen(trim($data['password'])) == 0 && strlen(trim($data['confirm_password'])) == 0) {
				$data['password'] 			= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
				$data['confirm_password']	= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
			}						
			if (!$this->user->isUniqueAccessName($data['accessname'])) {
				$check['accessname'] = 'alreadyExist';
			}
			if ($this->user->isValidRegData($data, $check)) 
			{	//echo "HERE USER SAVED WITH NEXT DATA:";var_dump($data);die();						
				$userID = $this->user->addUser($data);
				
				header ('Location: admin.php?action=browseCategory&category=users&bookmark='.$this->getFromRequest('bookmark'));										
				die();								
			} 
			else 
			{
				$data['password']="";
				$data['confirm_password']="";
				if ($data['accesslevel_id']!=3) {
					$company=new Company($this->db);
					$companyList=$company->getCompanyList();
					$this->smarty->assign("company",$companyList);
					if ($data['accesslevel_id']==1 || $data['accesslevel_id']==2) {
						$facility=new Facility($this->db);
						$facilityList=$facility->getFacilityListByCompany($data['company_id']);
						$this->smarty->assign("facility",$facilityList);
					}
					if ($data['accesslevel_id']==2) {
						$department=new Department($this->db);
						$departmentList=$department->getDepartmentListByFacility($data['facility_id']);
						$this->smarty->assign("department",$departmentList);
					}
				}													
				$this->smarty->assign('check', $check);
				$this->smarty->assign("reg_field",$data);									
			}							
		}
		else
		{			
			$bookmark = $this->getFromRequest('bookmark');
			if ($bookmark == 'supplier') {
				$jobberManager = new JobberManager($this->db);
				$supplierList = $jobberManager->getJobberList();

				$this->smarty->assign("jobbers",$supplierList);
			}			
			if ($bookmark != 'admin') {
				$company=new Company($this->db);
				$companyList=$company->getCompanyList();
				$this->smarty->assign("company",$companyList);
				
				if ($bookmark != 'company') {
					$facility=new Facility($this->db);
					$facilityList=$facility->getFacilityListByCompany($companyList[0]['id']);
					$this->smarty->assign("facility",$facilityList);
					
					if ($bookmark != 'facility') {
						$department=new Department($this->db);
						$departmentList=$department->getDepartmentListByFacility($facilityList[0]['id']);
						$this->smarty->assign("department",$departmentList);
					}
				}				
			}
		}
		$this->smarty->assign('bookmark',$this->getFromRequest('bookmark'));
		$this->smarty->assign('tpl', 'reg_form.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		
		if ($_POST['save'] == 'Save')
		{
			$data = $this->getFromPost();
			$data['grace']=14;//???
			$bookmark = $this->getFromRequest('bookmark');
			$check = array();
			foreach($data as $key => $value) {
				$check[$key] = 'ok';
			}
			//$bookmark = $data['access_level']
			
			if (strlen(trim($data['password'])) == 0 && strlen(trim($data['confirm_password'])) == 0) {
				$data['password'] 			= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
				$data['confirm_password']	= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
			}					
			if (!$this->user->isUniqueAccessName($data['accessname'],$this->getFromRequest('id'))) {
				$check['accessname'] = 'alreadyExist';
			}
			if ($this->user->isValidRegData($data, $check)) {
				
				$data['user_id'] = $this->getFromRequest('id'); 
				
				if ($data['password'] == "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__") {
					
					$this->user->setUserDetails($data);							
				} else {
					
					$this->user->setUserDetails($data, true);
				}								
				header ('Location: admin.php?action=browseCategory&category=users&bookmark='.$this->getFromRequest('bookmark'));								
				die();								
			} else {

				$data['password']="";
				$data['confirm_password']="";
				if ($data['accesslevel_id']!=3) {
					$company=new Company($this->db);
					$companyList=$company->getCompanyList();
					$this->smarty->assign("company",$companyList);
					if ($data['accesslevel_id']==1 || $data['accesslevel_id']==2) {
						$facility=new Facility($this->db);
						$facilityList=$facility->getFacilityListByCompany($data['company_id']);
						$this->smarty->assign("facility",$facilityList);
					}
					if ($data['accesslevel_id']==2) {
						$department=new Department($this->db);
						$departmentList=$department->getDepartmentListByFacility($data['facility_id']);
						$this->smarty->assign("department",$departmentList);
					}
					if ($data['accesslevel_id']==5) {
						$suppl = new BookmarksManager($this->db);
						$supplierList = $suppl->getOriginSupplier();
						$supList = $this->user->getSupplierStartPoint($data['user_id']);
						$data['supplier_id'] = $supList[0]['supplier'];
						$this->smarty->assign("supplier",$supplierList);
					}					
					
					
				}								
				$this->smarty->assign('check', $check);								
			}							
		} else {						
			$data = $this->user->getUserDetails($this->getFromRequest('id'), true);
			$data['password']="";//because from user details we get md5 of password
			
			$bookmark = 'supplier';
			if ($bookmark == 'supplier') {
				$jobberManager = new JobberManager($this->db);
				$supplierList = $jobberManager->getJobberList();

				$this->smarty->assign("jobbers",$supplierList);
			}				
				
			$bookmark = 'admin';
			if ($data['accesslevel_id']!=3) {
				$company=new Company($this->db);
				$companyList=$company->getCompanyList();
				$this->smarty->assign("company",$companyList);
				$bookmark = 'company';
				
				if ($data['accesslevel_id']!=0) {
					$facility=new Facility($this->db);
					$facilityList=$facility->getFacilityListByCompany($companyList[0]['id']);
					$this->smarty->assign("facility",$facilityList);
					$bookmark = 'facility';
					
					if ($data['accesslevel_id']!=1) {
						$department=new Department($this->db);
						$departmentList=$department->getDepartmentListByFacility($facilityList[0]['id']);
						$this->smarty->assign("department",$departmentList);
						$bookmark = 'department';
					}
				}				
			}
			if ($data['accesslevel_id'] == 4){
				$bookmark = 'sales';
			}
			if ($data['accesslevel_id'] == 5){
				$bookmark = 'supplier';
			}			
		}
		$this->smarty->assign("bookmark",$bookmark);
		$this->smarty->assign("reg_field",$data);	
		$this->smarty->assign('update','yes');
		$this->smarty->assign('tpl', 'reg_form.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$userDetails=$this->user->getUserDetails($this->getFromRequest('item_'.$i));
				$item["id"]	=	$userDetails["user_id"];
				$item["name"]=	$userDetails["username"];
				$itemForDelete []= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			//next 2 lines was used for old notify system... is voc needs it now?!
			//$userDetails=$this->user->getUserDetails($id);
			//$itemForDeleteName[]=	$userDetails["username"];
			$this->user->deleteUser($id);
		}
		header ('Location: admin.php?action=browseCategory&category='.$this->getFromRequest('category').'&bookmark='.$this->getFromRequest('bookmark'));
		die();
	}
}
?>