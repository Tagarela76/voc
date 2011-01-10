<?php

class Issue {
	private $db;

    function Issue($db) {
    	$this->db = $db;
    }
    
    public function addIssue($issueDetails) {
    	
    	//screening of quotation marks
		foreach ($issueDetails as $key=>$value)
		{
			$issueDetails[$key]=mysql_escape_string($value);
		}
    	
    	//$this->db->select_db(DB_NAME);
    	
    	//	New line dances
//    	$issueDetails["description"] = str_replace("\r\n", "<br>", $issueDetails["description"]);
    	
    	$query = "INSERT INTO ".TB_ISSUE." (title, description, creator_id, referer, priority, status) VALUES ("
    			."'".$issueDetails["title"]."'"
    			.", '".$issueDetails["description"]."'"
    			.", ".$issueDetails["creatorID"]
    			.", '".$issueDetails["referer"]."'"
    			.", 'normal'"
    			.", 'new'"
    			.")";
    			
    	$this->db->query($query);
    	
    	//	Send e-mail notifications
    	$mail = new EMail();
    	
    	//	Message formign
    	$from = "issues@vocwebmanager.com";
    	
    	//	Getting issues-mailing-list group
    	//	For now ststaic
    	$to = array("dmitry.vd@kttsoft.com",					
					"denis.nt@kttsoft.com");
    	/*
    	$to = array(
					"oleg.lv@kttsoft.com"
					);
    	*/
    	
    	$subject = "New Issue reported - ".$issueDetails["title"];
    	
    	$message = "Title: ".$issueDetails["title"]."\n\n";
    	$message .= "Description: \n".$issueDetails["description"]."\n\n";
    	$message .= "Referer: ".$issueDetails["referer"];
    	
    	//$message = $issueDetails["description"];
    	
    	//	Sending message
    	$mail->sendMail($from, $to, $subject, $message);
    }
    
    public function getIssuesList($sortBy = "none") {
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "SELECT issue_id, title, status, priority FROM ".TB_ISSUE." WHERE 1";
    	
    	switch($sortBy) {
    		case "none":
    			$query .= " ORDER BY issue_id";
    			break;
    			
    		default:
    			break;
    	}
    	
    	$this->db->query($query);
    	
    	$issues = $this->db->fetch_all_array();
    	
    	return $issues;
    }
    
    public function getIssueDetails($issueID) {
    	
    	$issueID=mysql_escape_string($issueID);
    	
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "SELECT * FROM ".TB_ISSUE." WHERE issue_id=".$issueID." LIMIT 1";
    	$this->db->query($query);
    	
    	$data = $this->db->fetch(0);
    	$issueDetails = array(
    		"issueID"	=>	$data->issue_id,
    		"title"		=>	$data->title,
    		"description"	=>	str_replace("\r\n", "<br>", $data->description),	//	New line dances
    		"creatorID"		=>	$data->creator_id,
    		"referer"		=>	$data->referer,
    		"priority"		=>	$data->priority,
    		"status"		=>	$data->status
    	);
    	
    	return $issueDetails;
    }
    
    public function updateIssueDetails($issue) {
    	
    	//screening of quotation marks
		foreach ($issue as $key=>$value)
		{
			$issue[$key]=mysql_escape_string($value);
		}
    	
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "UPDATE ".TB_ISSUE." SET "
    			."status='".$issue["status"]."', "
    			."priority='".$issue["priority"]."' "
    			." WHERE issue_id=".$issue["issueID"];
    			
    	$this->db->query($query);
    }
}
?>