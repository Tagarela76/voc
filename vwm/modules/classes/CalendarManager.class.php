<?php
class CalendarManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getEvents($day,$month,$year, $userID = null) {
		if ($userID){
			$sql = " AND user_id = '{$userID}' "; 
		}else{
			$sql = '';
		}
		$query = "select id,title,description 
				from calendar_events 
				left join calendar_cat on calendar_events.cat=calendar_cat.cat_id 
				where day='$day' 
				and month='$month' 
				and year='$year' $sql 	
				order by day,month,year ASC";
		//echo $query;
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{    		
    		return $this->db->fetch_all_array();
    	}
    	else
    		return false;		
	}	
	
	public function getCategory() {

		$query = "select cat_id,cat_name from calendar_cat";
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{    		
    		$result = $this->db->fetch_all_array();
			return $result;
    	}
    	else
    		return false;		
	}	

	
	
}