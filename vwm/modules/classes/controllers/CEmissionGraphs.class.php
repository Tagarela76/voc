<?php

class CEmissionGraphs extends Controller {

	function CEmissionGraphs($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'logbook';
	}

	protected function bookmarkEmissionGraphs($vars) {
		extract($vars);
		$facility->initializeByID($this->getFromRequest('id'));
		$this->setGraphs('facility', $this->getFromRequest('id'));
	}

	protected function bookmarkDEmissionGraphs($vars) {
		extract($vars);
		$this->setGraphs('department', $this->getFromRequest('id'));
	}

	protected function bookmarkCEmissionGraphs() {
		$this->setGraphs('company', $this->getFromRequest('id'));
	}

	protected function setGraphs($category, $id) {
		
		if (!$_POST){
			$_SESSION['DED'] = '';
			$_SESSION['PUF'] = '';
			$_SESSION['PUD'] = '';
			$_SESSION['selGraph'] ='';
		}
		if ($category == 'company'){
			if ($_POST['facilityList']) {
				$_SESSION['DED'] = $_POST['facilityList'];
				$_SESSION['selGraph'] = '4';
			}
			
			if ($_POST['facilityListPU']) {
				$_SESSION['PUF'] = $_POST['facilityListPU'];
				$_SESSION['selGraph'] = '5';
			}
			
			if ($_POST['departmentListPU']) {
				$_SESSION['PUD'] = $_POST['departmentListPU'];
				$_SESSION['selGraph'] = '6';
			}
		} else {
			$_SESSION['DED'] = '';
			$_SESSION['PUF'] = '';
			$_SESSION['PUD'] = '';
			$_SESSION['selGraph'] ='';
		}
		//dates validation!
		$endDate = new TypeChain($this->getFromRequest('end'), 'date', $this->db, $id, $category);
		$beginDate = new TypeChain($this->getFromRequest('begin'), 'date', $this->db, $id, $category);
		if (!$endDate->formatInput() && !$beginDate->formatInput()) {
			$endDate->setValue(date('Y-m-d'));
			$beginDate->setValue(date('Y-m-d', strtotime(' - 30 days')));
		} elseif (!$endDate->formatInput()) {
			$endDate->setValue(date('Y-m-d', strtotime($beginDate->formatInput() . ' + 30 days')));
		} elseif (!$beginDate->formatInput()) {
			$beginDate->setValue(date('Y-m-d', strtotime($endDate->formatInput() . ' - 30 days')));
		}

		if ($beginDate->formatInput() > $endDate->formatInput()) {
			$date = $endDate;
			$endDate = $beginDate;
			$beginDate = $date;
		}

		$this->smarty->assign('begin', $beginDate);
		$this->smarty->assign('end', $endDate);
		// var_dump($endDate);var_dump($beginDate);//die();
		//calc tick for graph
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput())) / $day) + 1;
		$tick = round($daysCount / 10);
		$this->smarty->assign('tick', $tick);

		//Daily Emissions Graph
		$equip = new Equipment($this->db);
		$data = $equip->getDailyEmissionsByDays($beginDate, $endDate, $category, $id);

		$this->smarty->assign('dataDE', $this->performDataForGraph($data));
		//Daily Emissions Graph by Facilities
		if ($category == 'company') {
			
			$facility = new Facility($this->db);
			$data = $facility->getDailyEmissionsByDays($beginDate, $endDate, $category, $id);
			$data2 = $facility->getProductUsageByDaysByFacilities($beginDate, $endDate, $category, $id);
			$facilityList = $facility->getFacilityListByCompany($id);
			$this->smarty->assign('dataDEF', $this->performDataForGraph($data));
			$this->smarty->assign('dataPUF', $this->performDataForGraph($data2));

			//Daily Emissions Graph by Departments
			$department = new Department($this->db);
			$data = $department->getDailyEmissionsByDays($beginDate, $endDate, $category, $id);
			foreach ($facilityList as $row){
				$departmentList[$row['name']] = $department->getDepartmentListByFacility($row['id']);
			}
			$departmentData = $department->getProductUsageByDaysByDepartments($beginDate, $endDate, $category, $id);
			$this->smarty->assign('dataPUD', $this->performDataForGraph($departmentData));
			$this->smarty->assign('dataDED', $this->performDataForGraph($data));
		}
		//Product Usage Graph
		$product = new Product($this->db);
		$data = $product->getProductUsageByDays($beginDate, $endDate, $category, $id);
		$this->smarty->assign('dataPU', $this->performDataForGraph($data)); //var_dump($data);

		$request = $this->getFromRequest();
		
		
		$toSelectGraph = $_SESSION['selGraph'];
		$toSelectFacility = $_SESSION['DED'];
		$toSelectFacilityPU = $_SESSION['PUF'];
		$toSelectDepartmentPU = $_SESSION['PUD'];
		$this->smarty->assign('selectedGraph', $toSelectGraph);
		$this->smarty->assign('selectedFacility', $toSelectFacility);
		$this->smarty->assign('selectedFacilityPU', $toSelectFacilityPU);
		$this->smarty->assign('selectedDepartmentPU', $toSelectDepartmentPU);

		$this->smarty->assign('request', $request);
		$this->smarty->assign('facilityList', $facilityList);
		$this->smarty->assign('facilityListPU', $facilityList);
		$this->smarty->assign('departmentListPU', $departmentList);
		$this->smarty->assign('legendPUheight', count($product->getProductNR()) * 18);

		//Department Usage Graph(only for facility)
		if ($category == 'facility') {
			$facility = new Facility($this->db);
			$data = $facility->getDepartmentUsageByDays($beginDate, $endDate, $id);
			$this->smarty->assign('dataDU', $this->performDataForGraph($data)); 
		}

		$jsSources = array(
			'modules/js/flot/jquery.flot.js',
			'modules/js/graph.js',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);

		$this->smarty->assign('tpl', 'tpls/graph.tpl');
	}

	protected function performDataForGraph($array) {
		$dataForGraph = array();
		$i = 0;
		foreach ($array as $equip => $data) {
			$dataEq['data'] = $data;
			$dataEq['label'] = $equip;
			$dataEq['color'] = $i;
			$dataForGraph [] = ($dataEq);
			$i++;
		}
		return json_encode(($dataForGraph));
	}

}

?>