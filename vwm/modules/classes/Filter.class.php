<?php

class Filter {

	private $tableParent;	
	private $db;
	
    function Filter($db,$tableParent){
    	$this->db=$db;
    	$this->tableParent=$tableParent;
    }
    
    public function getJsonFilterArray()
    {    	
    	$query = "SELECT * FROM ".TB_FILTER." WHERE parent='{$this->tableParent}'";
        $this->db->query($query);
    	if ($this->db->num_rows()>0)
    	{
    		$data = $this->db->fetch_all_array();
    		return json_encode($data);
    	}
    	return null;
   	}
   	
   	/*public function getSubQueryWithPseudonym($filterData)
   	{
   		$query = "SELECT pseudonym FROM ".TB_FILTER." WHERE parent='".$this->tableParent.".' AND name_in_table='".$filterData['filterField']."' LIMIT 1";
   		$this->db->query($query);
   		if ($this->db->num_rows()==1)
    	{
    		$row = $this->db->fetch(0);
    		return getSubQuery($filterData,$row->pseudonym);
    	}
    	return null;
   	}*/
   	
   	public function getSubQuery($filterData,$pseudonymTable=null)
   	{
   		if (($filterData['filterField']==null)||($filterData['filterField']=='All')||($value=$filterData['filterValue']===""))
   		{
   			return 'TRUE';
   		}
   		
   		$field=$filterData['filterField'];
   		$value=$filterData['filterValue'];
   		
   		$filter=(($pseudonymTable!=null)?$pseudonymTable.".":"");
   		   		

   		if(isset($filterData['dateFormat']) and $filterData['filterField'] == 'creation_time') {
			
   			$beginDay = DateTime::createFromFormat($filterData['dateFormat'], $value);
   			$beginDay->setTime("00","00","00");
   					
   			$endDay = DateTime::createFromFormat($filterData['dateFormat'], $value);
   			$endDay->setTime("23","59","59");
   		}
		   
   		switch ($filterData['filterCondition'])
   		{
   			case 'dateEquals':
   					//$filter.= $field." = (DATE_FORMAT('$value','%Y-%m-%d'))";
   					$filter.= $field." BETWEEN {$beginDay->getTimestamp()} AND {$endDay->getTimestamp()}";
   					
   				break;
   			case 'dateNotEquals': 
   					
   					
   					//$filter.= $field."<>(DATE_FORMAT('$value','%Y-%m-%d'))";
   					$filter.= $field." < {$beginDay->getTimestamp()} OR creation_time > {$endDay->getTimestamp()}";
   					
   					echo $filter;
   				break;
   			case 'dateLessThan':		
   					$filter.= $field."< {$endDay->getTimestamp()}";
   					//$filter.= $field."<(DATE_FORMAT('$value','%Y-%m-%d'))";
   				break;
   			case 'dateGreaterThan':
   					//$filter.= $field.">(DATE_FORMAT('$value','%Y-%m-%d'))";
   					$filter.= $field." > {$endDay->getTimestamp()}";
   					
   				break;
   			case 'dateLessThanOrEqual': 
   					
   					//$filter.= $field."<=(DATE_FORMAT('$value','%Y-%m-%d'))";
   					$filter.= $field." < {$endDay->getTimestamp()}";
   					
   				break;
   			case 'dateGreaterThanOrEqual':
   					$filter.= $field." > {$beginDay->getTimestamp()}";
   					//$filter.= $field.">=(DATE_FORMAT('$value','%Y-%m-%d'))";
   				break;
   			case 'equals': $filter.= $field."= $value";
   				break;
   			case 'notEquals': $filter.= $field."<> $value";
   				break;
   			case 'lessThan': $filter.= $field."< $value";
   				break;
   			case 'greaterThan': $filter.= $field."> $value";
   				break;
   			case 'lessThanOrEqual': $filter.= $field."<= $value";
   				break;
   			case 'greaterThanOrEqual': $filter.= $field.">= $value";
   				break;
   			case 'equalsStr': $filter.=" IF(($field REGEXP '&[^\s]*;'),HTML_UnEncode($field),$field) = '$value'";
   				break;
   			case 'contains': $filter.=" IF(($field REGEXP '&[^\s]*;'),HTML_UnEncode($field),$field) like '%$value%'";
   				break;
   			case 'notContains': $filter.=" IF(($field REGEXP '&[^\s]*;'),HTML_UnEncode($field),$field) not like '%$value%'";
   				break;
   		}
   		return $filter;
   	}
   	
   	public function getSearchSubQuery($fields,$searchStr)
   	{   
   		$search=" (";		
   		foreach ($fields as $value)
   		{
   			$search.=" $value like '%$searchStr%' OR";
   		}
   		$search = preg_replace('/OR$/','',$search);
   		$search.=" ) ";
   		return $search;
   	}
}
?>