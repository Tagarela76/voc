<?php

class Issue {
	
	private $db;

	
	
	
    function __construct($db) {
    	$this->db = $db;
    }
    
    
    
    
    public function addIssue($issueDetails) {
    	
    	//screening of quotation marks
		foreach ($issueDetails as $key=>$value)
		{
			$issueDetails[$key]=mysql_escape_string($value);
		}
    	
    	
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
    	switch (ENVIRONMENT) {    		
    		case "server":
    			//	boss should see this issue
    			$to = array("dmitry.vd@kttsoft.com",
					"denis.nt@kttsoft.com",
    				"denis.yv@kttsoft.com");
    			break;
    		case "sandbox":
    			//	only 4 developers
    			$to = array("denis.nt@kttsoft.com",
    				"denis.yv@kttsoft.com");
    			break;
    		default:
    			//	smth else - do nothing
    			return true;
    				
    	}
    	    	    	
    	$subject = "New Issue reported - ".$issueDetails["title"];
    	
    	$message = "Title: ".$issueDetails["title"]."\n\n";
    	$message .= "Description: \n".$issueDetails["description"]."\n\n";
    	$message .= "Referer: ".$issueDetails["referer"];    	
    	
    	//	Sending message
    	$mail->sendMail($from, $to, $subject, $message);
    }
    
        
    
    
    public function getIssuesList($sortBy = "none") {    	
    	$query = "SELECT issue_id issueID, title, status, priority FROM ".TB_ISSUE." WHERE 1";
    	
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
		foreach ($issue as $key=>$value) {
			$issue[$key]=mysql_escape_string($value);
		}
    	
    	$query = "UPDATE ".TB_ISSUE." SET "
    			."status='".$issue["status"]."', "
    			."priority='".$issue["priority"]."' "
    			." WHERE issue_id=".$issue["issueID"];
    			
    	$this->db->query($query);
    }
    
    public function deleteIssue($issueID) {
    	$query = "DELETE FROM ".TB_ISSUE." WHERE issue_id='$issueID'";
    	$this->db->query($query);
    }
}
?>