<?php

class CACountry extends Controller {
	
	function CACountry($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='country';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkCountry($vars) {
		extract($vars);
		$country=new Country($this->db);
		
		$pagination = new Pagination($country->queryTotalCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=country".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 
		
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY name ";
		}
		$countryList=$country->getCountryList($pagination,$filterStr,$sortStr);
		
		$field = 'country_id';
		$list = $countryList;
		$itemsCount = ($list) ? count($list) : 0;//var_dump($countryList);
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=country&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/countryClass.tpl');
		$this->smarty->assign('pagination', $pagination);
		
	}
	
	private function actionViewDetails() {
		$country=new Country($this->db);
		$countryDetails=$country->getCountryDetails($this->getFromRequest('id'));
		
		$state=new State($this->db);
		$stateList=$state->getStateList($countryDetails['country_id']);
		
		$this->smarty->assign("statesCount",count($stateList));
		$this->smarty->assign("states",$stateList);
		$this->smarty->assign("country",$countryDetails);
		$this->smarty->assign('tpl', 'tpls/viewCountry.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$country = new Country($this->db);
		$save = $this->getFromPost('save');
		$id = $this->getFromRequest('id');
		$stateInfo=new State($this->db);	
		
		if (!is_null($save))
		{
			$validation=new Validation($this->db);															
			
			if ($this->getFromPost('stateCount')=="") {
				$stateCount=0;
			} else {
				$stateCount=$this->getFromPost('stateCount');
			}
			for ($i=0;$i<$stateCount;$i++) {
				if (!is_null($this->getFromPost('state_id_'.$i)) && $this->getFromPost('state_id_'.$i) != "") {
					$f=true;
					for ($j=0; $j<count($states); $j++) {
						if ($this->getFromPost('state_name_'.$i)==$states[$j]['name']) {
							$f=false;
							break;
						}
					}	
					if ($f==true) {	
						$state=array(
							"state_id"	=>	$this->getFromPost('state_id_'.$i),
							"name"	=>	$this->getFromPost('state_name_'.$i)
						);
						$states[]=$state;
					}
				}
			}
			
			$countryData=array (
				"country_id"		=>	$id,
				"country_name"		=>	$this->getFromPost("country_name"),
				"date_type"			=>	$this->getFromPost("date_type"),
				"user_id"			=>	18,
				"states" 			=> $states
			);
		}
		//	IF NO POST REQUEST
		else
		{									
			$data=$country->getCountryDetails($id);									
			$stateList=$stateInfo->getStateList($id);
			$data['states']=$stateList;
			$this->smarty->assign('statesAdded', $data);
			$this->smarty->assign("stateCount",count($stateList));															
			//									$smarty->assign("data",$data);
		}
		//	END IF NO POST REQUEST
		
		if ($save=='Save') 
		{
			$validateStatus=$validation->validateRegDataAdminClasses($countryData);								
			if (!$validation->isUniqueName("country", $countryData['country_name'], 'none', $countryData['country_id'])) {
				$validateStatus['summary'] = 'false';
				$validateStatus['country_name'] = 'alredyExist';
			}									
			if ($validateStatus['summary'] == "true") {
				$country->setCountryDetails($countryData);										
				header ('Location: admin.php?action=viewDetails&category=country&id='.$countryData['country_id']);
				die();										
			} 
			
			//	IF ERRORS
			else {										
				$notify=new Notify($this->smarty);
				$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
				$statesAdded['states']=$states;
				$data['country_name']=$this->getFromPost('country_name');
				$data['date_type']=$this->getFromPost('date_type');																
				//										$smarty->assign('validStatus', $validateStatus);
				$this->smarty->assign('statesAdded', $statesAdded);
				$this->smarty->assign('stateCount', count($countryData['states']));										
			}									
		}
		
		if ($save=='Add state to country') 
		{									
			$stateForCheck['state_name']=$this->getFromPost('state_name');
			$validateStatus=$validation->validateRegDataAdminClasses($stateForCheck);
			for ($i=0;$i<$stateCount;$i++) {
				if (trim($this->getFromPost('state_name'))==trim($states[$i]['name']) && trim($this->getFromPost('state_name'))!="") {
					$validateStatus['summary'] = 'false';
					$validateStatus['state_name'] = 'alredyExist';
				}
			}
			if ($validateStatus['summary'] == 'true') {
				$maxStateID=$states[0]['state_id'];
				for ($i=1;$i<count($states);$i++) {
					if ($states[$i]['state_id']>$maxStateID) {
						$maxStateID=$states[$i]['state_id'];
					}
				}										
				$state=array(
					"state_id"	=>	$maxStateID+1,
					"name"	=>	$this->getFromPost('state_name')
				);
				$states[]=$state;
			}
			
			$statesAdded['states']=$states;								
			$this->smarty->assign('statesAdded', $statesAdded);									
			if ($validateStatus['summary'] == 'true')  {
				$data["country_name"]=$this->getFromPost('country_name');
				$data['date_type']=$this->getFromPost('date_type');										
				$this->smarty->assign('stateCount', count($statesAdded['states']));
			} else {
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$data=array(
					"country_name"	=>	$this->getFromPost('country_name'),
					"state_name"	=>	$this->getFromPost('state_name'),
					"date_type"		=>	$this->getFromPost("date_type")
				);										
				//										$smarty->assign('validStatus', $validateStatus);
				$this->smarty->assign('stateCount', count($statesAdded['states']));
			}
			$title=new Titles($this->smarty);
			$title->titleEditItemAdmin($this->getFromRequest('category'));																	
			$doNotShow=true;
		}
		$validStatus = $validateStatus;
		$this->smarty->assign('tpl','tpls/addCountryClass.tpl');
		$this->smarty->assign("data", $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if (!is_null($this->getFromPost('save'))) {
			$country=new Country($this->db);
			
			$validation=new Validation($this->db);
			$stateInfo=new State($this->db);
			
			if ($this->getFromPost('stateCount')=="") {
				$stateCount=0;
			} else {
				$stateCount=$this->getFromPost('stateCount');
			}
			
			for ($i=0;$i<$stateCount;$i++) {
				if (!is_null($this->getFromPost('state_id_'.$i))) {
					$f=true;
					for ($j=0; $j<count($states); $j++) {
						if ($this->getFromPost('state_name_'.$i)==$states[$j]['name']) {
							$f=false;
							break;
						}
					}
					if ($this->getFromPost('state_name_'.$i)=="") {
						$f=false;
					}	
					if ($f==true) {	
						$state=array(
							"state_id"	=>	$this->getFromPost('state_id_'.$i),
							"name"	=>	$this->getFromPost('state_name_'.$i)
						);
						$states[]=$state;
					}
				}
			}
			
			$countryData=array (
				"country_name"	=>	$this->getFromPost("country_name"),
				"name" 			=>	$this->getFromPost("country_name"),
				"date_type"		=>	$this->getFromPost("date_type"),
				"user_id"		=>	18,
				"states"		=>	$states
			);
			
			
			if ($this->getFromPost('save')=="Save") 
			{
				$validateStatus=$validation->validateRegDataAdminClasses($countryData);
				if (!$validation->isUniqueName("country", $countryData['country_name'])) {
					$validateStatus['summary'] = 'false';
					$validateStatus['country_name'] = 'alredyExist';
				}
				
				if ($validateStatus['summary'] == "true") {
					$country->addNewCountry($countryData);
					header ('Location: admin.php?action=browseCategory&category=tables&bookmark=country');
					die();										
				} else {
					//$notify=new Notify($smarty);
					//$notify->formErrors();
					$title=new Titles($this->smarty);
					$title->titleAddItemAdmin($this->getFromRequest('category'));
					$statesAdded['states']=$states;
					$countryData['name']=$this->getFromPost('country_name');
					$countryData['date_type']=$this->getFromPost('date_type');
					$this->smarty->assign('data', $countryData);
					$this->smarty->assign('validStatus', $validateStatus);
					$this->smarty->assign('statesAdded', $statesAdded);
					$this->smarty->assign('stateCount', count($countryData['states']));														
				}
			} 
			
			if ($this->getFromPost('save')=='Add state to country')  
			{
				$stateForCheck['state_name']=$this->getFromPost('state_name');
				$validateStatus=$validation->validateRegDataAdminClasses($stateForCheck);
				for ($i=0;$i<$stateCount;$i++) {
					if (trim($this->getFromPost('state_name'))==trim($states[$i]['name']) && trim($this->getFromPost('state_name'))!="") {
						$validateStatus['summary'] = 'false';
						$validateStatus['state_name'] = 'alredyExist';
					}
				}
				if ($validateStatus['summary'] == 'true') {
					$maxStateID=$states[0]['state_id'];
					for ($i=1;$i<count($states);$i++) {
						if ($states[$i]['state_id']>$maxStateID)
							$maxStateID=$states[$i]['state_id'];
					}
					
					$state=array(
						"state_id"	=>	$maxStateID+1,
						"name"	=>	$this->getFromPost('state_name')
					);
					$states[]=$state;
				}
				
				$statesAdded['states']=$states;
				
				$this->smarty->assign('statesAdded', $statesAdded);
				$this->smarty->assign('id', $this->getFromPost('id'));										
				if ($validateStatus['summary'] == 'true') {
					$countryData["country_name"]=$this->getFromPost('country_name');
					$countryData['date_type']=$this->getFromPost('date_type');
					$this->smarty->assign('data', $countryData);
					$this->smarty->assign('stateCount', count($statesAdded['states']));
				} else {
					//$notify=new Notify($smarty);
					//$notify->formErrors();
					
					$countryData=array(
						"country_name"	=>	$this->getFromPost('country_name'),
						"state_name"	=>	$this->getFromPost('state_name'),
						"date_type"     =>  $this->getFromPost('date_type')
					);
					$this->smarty->assign('data', $countryData);
					$this->smarty->assign('validStatus', $validateStatus);
					$this->smarty->assign('stateCount', count($statesAdded['states']));
				}
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromRequest('category'));
			}
		}						
		$this->smarty->assign('currentOperation', 'addItem');
		$this->smarty->assign('categoryID', 'class');
		$this->smarty->assign('itemID', 'country');
		$this->smarty->assign('tpl', 'tpls/addCountryClass.tpl');
		//								$smarty->display('tpls:editDetailsCategory.tpl');									
		$doNotShow=true; //???
		$this->smarty->display("tpls:index.tpl");
	} 
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$country=new Country($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$countryDetails =	$country->getCountryDetails($this->getFromRequest('item_'.$i));
				$item["id"] =	$countryDetails["country_id"];
				$item["name"] =	$countryDetails["country_name"];
				$itemForDelete [] = $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$country=new Country($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$country->deleteCountry($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>