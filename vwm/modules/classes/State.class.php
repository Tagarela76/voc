<?php

class State {
	
	private $db;

    function State($db) {
    	$this->db=$db;
    }
    
    public function getStateList($countryID="") {
    	//$this->db->select_db(DB_NAME);
    	if ($countryID=="")
    		$this->db->query("SELECT * FROM ".TB_STATE." ORDER BY name");
    	else
    		$this->db->query("SELECT * FROM ".TB_STATE." WHERE country_id=".$countryID." ORDER BY name");
			
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$state=array (
					'state_id'			=>	$data->state_id,
					'id'			=>	$data->state_id,
					'name'			=>	$data->name
				);
				if ($countryID!="")
					$state['country_id']=$data->country_id;
				$states[]=$state;
			}
		}
		
		return $states;
    }
    
    
    public function getStateDetails($stateID, $vanilla=false) {
    	//$this->db->select_db(DB_NAME);
    	$this->db->query("SELECT * FROM ".TB_STATE." WHERE state_id=".$stateID);
		$stateDetails=$this->db->fetch_array(0);
		/*$stateDetails=array(
			'state_id'	=>	$data->state_id,
			'name'	=>	$data->name,
			'country_id'	=>	$data->country_id
		);*/
		
		if (!$vanilla){
			$this->db->query("SELECT * FROM ".TB_COUNTRY." WHERE country_id=".$data->country_id);
			$data2=$this->db->fetch(0);
			$stateDetails['country_id']=$data2->name;
		}		
		return $stateDetails;
    }
    
    public function setStateDetails($stateDetails){
    	
    	//$this->db->select_db(DB_NAME);
			
		$query="UPDATE ".TB_STATE." SET ";
		
		$query.="name='".$stateDetails['name']."', ";
		$query.="country_id='".$stateDetails['country_id']."'";
		
		$query.=" WHERE state_id=".$stateDetails['state_id'];
		
		$this->db->query($query);
    }
    
    public function addNewState($stateData) {
	    //$this->db->select_db(DB_NAME);
	    
	    $query="INSERT INTO ".TB_STATE." (name, country_id) VALUES (";
	    
	    $query.="'".$stateData["name"]."', ";
	    $query.="'".$stateData["country_id"]."'";
	    
	    $query.=')';
	    
	    $this->db->query($query);
    }
    
    public function deleteState($stateID) {
	    //$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_STATE." WHERE state_id=".$stateID);
	}
}
?>