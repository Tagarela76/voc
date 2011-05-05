<?php

class EmissionLog {
	private $db;

    function EmissionLog($db) {
    	$this->db = $db;
		$this->db->select_db(DB_NAME);
    }
    
    function getEmissionLog($year,$category,$categoryID) {
    	
    	$beginDate = DateTime::createFromFormat("Y-m-d H:i", "$year-01-01 00:00");
    	
    	$endDateTmp = DateTime::createFromFormat("Y-m-d H:i", "$year-12-01 23:59");
    	$lastDayInMonth = $endDateTmp->format("t");
    	
    	$endDate = DateTime::createFromFormat("Y-m-d H:i", "$year-12-$lastDayInMonth 23:59");
    	
    	$beginStamp = $beginDate->getTimestamp();
    	$endStamp = $endDate->getTimestamp();
    	
    	switch ($category) {
    		case 'department':
    			$query = "SELECT  f.`voc_annual_limit` ,  f.`voc_limit`, f.`facility_id` FROM  `".TB_FACILITY."` f, `".TB_DEPARTMENT."` d " .
    					"WHERE  f.`facility_id` = d.`facility_id` AND d.`department_id` = '$categoryID' LIMIT 1";
		    	$this->db->query($query);
		    	$limit = $this->db->fetch(0);
		    	$fac_limit = array('monthly' => $limit->voc_limit, 'annual' => $limit->voc_annual_limit);
		    	$query = "SELECT sum(m.`voc`) AS voc, MONTH( FROM_UNIXTIME(m.`creation_time`) ) AS month, m.`department_id`  FROM `".TB_USAGE."` m, `".TB_DEPARTMENT."` d " .
		    			"WHERE YEAR( FROM_UNIXTIME(m.`creation_time`) ) = '$year' AND d.`facility_id` = '$limit->facility_id' " .
		    			"AND d.`department_id` = m.`department_id` " .
		    			"GROUP BY (concat(m.`department_id`, MONTH( FROM_UNIXTIME(m.`creation_time`)))) ORDER BY MONTH(m.`creation_time`)";
		    	break;
		    case 'facility':
		    	$query = "SELECT  `voc_annual_limit` ,  `voc_limit` FROM  `".TB_FACILITY."` WHERE  `facility_id` =  '$categoryID' LIMIT 1";
		    	$this->db->query($query);
		    	$limit = $this->db->fetch(0);
		    	$fac_limit = array('monthly' => $limit->voc_limit, 'annual' => $limit->voc_annual_limit);
		    	$query = "SELECT sum(m.`voc`) AS voc, MONTH(m.`creation_time`) AS month, m.`department_id`  FROM `".TB_USAGE."` m, `".TB_DEPARTMENT."` d " .
		    			"WHERE YEAR(m.`creation_time`) = '$year' AND d.`facility_id` = '$categoryID' AND d.`department_id` = m.`department_id` " .
		    			"GROUP BY (concat(m.`department_id`,MONTH(m.`creation_time`))) ORDER BY MONTH(m.`creation_time`)";
		    	break;
		    default:
		    	return false;
    	}
    	
    	//echo "<p>".$query;
    	
    	$this->db->query($query);
    	if ($this->db->num_rows() > 0) {
	    	$data = $this->db->fetch_all();
	    	$queryLimits = "SELECT  `department_id` ,  `voc_annual_limit` ,  `voc_limit` FROM  `".TB_DEPARTMENT."` WHERE  `facility_id` =  '".(($category == 'facility')?$categoryID:$limit->facility_id)."'";
	    	$this->db->query($queryLimits);
	    	$dataLimits = $this->db->fetch_all();
	    	$limits = array();
	    	foreach ($dataLimits as $limitData) {
	    		$limits[$limitData->department_id] = array('monthly' => $limitData->voc_limit, 'annual' => $limitData->voc_annual_limit);
	    	}
	    	$tmpResult = array();
	    	$tmpLimitsDep = array();
	    	$tmpAnnualByDep = array();
	    	$annualVocFac = 0;
	    	foreach($data as $record) {
	    		if (($category == 'department' && $record->department_id == $categoryID) || $category == 'facility') {
	    			$tmpResult [$record->month] += $record->voc;
	    		}
	    		$tmpResultFac [$record->month] +=$record->voc;
	    		$annualVocFac += $record->voc;
	    		$tmpLimitsDep[$record->month][$record->department_id] += $record->voc;
	    		$tmpAnnualByDep[$record->department_id] += $record->voc;
	    	}
	    }
    	$result = array();
    	$monthes = array('January','February','March','April','May','June','July','August','September','October','November','December');
    	$annualVoc = 0;
    	for ($i = 1; $i <= 12; $i++) {
    		$resultByMonth = array('month' => $monthes[$i-1]);
    		if (isset($tmpResult[$i])) {
    			$resultByMonth['voc'] = $tmpResult[$i];
    			$annualVoc += $tmpResult[$i];
    			$depLimit = null;
    			if ($category == 'facility') {
    			foreach ($tmpLimitsDep[$i] as $department_id => $voc) {
    				if ($voc > $limits[$department_id]['monthly']) {
    					$depLimit = true; 
    					break;
    				}
    			}
    			} else {
    				if ($tmpLimitsDep[$i][$categoryID] > $limits[$categoryID]['monthly']) {
    					$depLimit = true; 
    				}
    			}
    			$resultByMonth['depLimit'] = ($depLimit === true)?true:false;
    		} else {
    			$resultByMonth['voc'] = 0;
    			$resultByMonth['depLimit'] = false;
    		}
    		$resultByMonth['facLimit'] = (isset($fac_limit['monthly']) && $tmpResultFac[$i] > $fac_limit['monthly'])?true:false;
    		$result []= $resultByMonth;
    	}
    	if ($category == 'facility') {
		    foreach ($tmpAnnualByDep as $department_id => $voc) {
		    	if ($voc > $limits[$department_id]['annual']) {
		    		$depLimitAnnual = true;
		    		break;
		    	}
		    }
    	} else {
	    	if ($tmpAnnualByDep[$i][$categoryID] > $limits[$categoryID]['annual']) {
		    	$depLimitAnnual = true; 
	    	}
    	}
	    $result['total'] = array(
			'voc' => $annualVoc,
			'depLimit' => (isset($depLimitAnnual) && $depLimitAnnual === true)?true:false,
			'facLimit' => (isset($fac_limit['annual']) && $annualVocFac > $fac_limit['annual'])?true:false
		);
		return $result;
    }
}
?>