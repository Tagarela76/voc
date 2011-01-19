<?php

class CEmissionGraphs extends Controller {

    function CEmissionGraphs($smarty,$xnyo,$db,$user,$action) {
    	parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='logbook';
    }
    
    function runAction() {
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkEmissionGraphs($vars) {
		extract($vars);
		
		$facility->initializeByID($this->getFromRequest('id'));
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
		
		$this->setGraphs('facility',$this->getFromRequest('id'));
	}
	
	protected function bookmarkDEmissionGraphs($vars) {
		extract($vars);
		$this->setGraphs('department',$this->getFromRequest('id'));
	}
    
    private function setGraphs($category, $id) {
    	
    	//dates validation!
    	$endDate = $this->getFromRequest('end');
	    $beginDate = $this->getFromRequest('begin');
	    if (is_null($endDate) && is_null($beginDate)) {
	    	$endDate = date('Y-m-d');
	    	$beginDate = date('Y-m-d', strtotime(' - 30 days'));
	    } elseif (is_null($endDate)) {
	    	$endDate = date('Y-m-d', strtotime($beginDate.' + 30 days'));
	    } elseif (is_null($beginDate)) {
	    	$beginDate = date('Y-m-d', strtotime($endDate.' - 30 days'));
	    }
	    
	    if($beginDate > $endDate) {
	    	$date = $endDate;
	    	$endDate = $beginDate;
	    	$beginDate = $date;
	    }
	    $endDate = date('Y-m-d', strtotime($endDate));
	    $beginDate = date('Y-m-d', strtotime($beginDate));
	    $this->smarty->assign('begin',$beginDate);
	    $this->smarty->assign('end',$endDate);
	    
	    
	    //calc tick for graph
	    $day = 86400; // Day in seconds
	    $daysCount = round((strtotime($endDate) - strtotime($beginDate))/$day) + 1;
	    $tick = round($daysCount/10);
	    $this->smarty->assign('tick',$tick);
	    
	    //Daily Emissions Graph
	    $equip = new Equipment($this->db);
	    $data = $equip->getDailyEmissionsByDays($beginDate,$endDate,$category,$id);   
	    $this->smarty->assign('dataDE',$this->performDataForGraph($data));
	    
	    //Product Usage Graph
	    $product = new Product($this->db);
	    $data = $product->getProductUsageByDays($beginDate,$endDate,$category,$id);
	    $this->smarty->assign('legendPUheight',count($product->getProductNR())*18);
	    $this->smarty->assign('dataPU',$this->performDataForGraph($data));
	    
	    //Department Usage Graph(only for facility)
	    if ($category == 'facility') {
	    	$facility = new Facility($this->db);
	    	$data = $facility->getDepartmentUsageByDays($beginDate,$endDate,$id);
	    	$this->smarty->assign('dataDU',$this->performDataForGraph($data));
	    }
	    
	    $jsSources = array (										
		    'modules/js/flot/jquery.flot.js',
			'modules/js/graph.js'							
	    );
	    $this->smarty->assign('jsSources',$jsSources);
	    $this->smarty->assign('tpl','tpls/graph.tpl');
    }
    
    private function performDataForGraph($array) {
    	$dataForGraph = array();
	    foreach($array as $equip => $data) {
		    $dataEq['data'] = $data;
		    $dataEq['label'] = $equip;
		    $dataForGraph []= ($dataEq);
	    } 
	    return json_encode(($dataForGraph));
    }
    
}
?>