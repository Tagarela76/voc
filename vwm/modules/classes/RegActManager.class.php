<?php

class RegActManager {
	private $db;
	
	private $xmlReview = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULES_UNDER_REVIEW.xml";
	private $xmlCompleted = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULE_COMPLETED_30_DAYS.xml";

    function __construct($db, $xmlFileReviewPath = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULES_UNDER_REVIEW.xml", $xmlFileCompletedPath = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULE_COMPLETED_30_DAYS.xml") {
    	$this->db = $db;
    	
    	$this->xmlReview = $xmlFileReviewPath;
    	$this->xmlCompleted = $xmlFileCompletedPath;
    }
    
    /**
     * function parseXML 
     * parse xml's for chosen category, remove all old info from db and add/update new. Also manage Users2Regs list.  
     * @param string category = "review"/"completed"/null
     */
    public function parseXML($category = null) {
	    $xmlDOM = new DOMDocument();
	    $xmlDOM->preserveWhiteSpace = false;
	    $xmlDOM->formatOutput = true;
	    if (!is_null($category)) {
	    	$xmlDOM->load(($category == 'review')?$this->xmlReview:$this->xmlCompleted);
	    	$XMLoira = $xmlDOM->getElementsByTagName('OIRA_DATA')->item(0);
	    	$XMLregs = $XMLoira->getElementsByTagName('REGACT');
	    	$regsCount = $XMLregs->length;
	    	$regAgency = new RegAgency($this->db);
	    	$rin_array = array();
	    	for($i = 0; $i < $regsCount; $i++) {
	    		$XMLregAct = $XMLregs->item($i);
	    		$agencyID = $regAgency->getAgencyIdByCode($XMLregAct->getElementsByTagName('RIN')->item(0)->nodeValue);
	    		if ($agencyID !== false ) {
	    			$regAct = new RegAct($this->db);
		    		$regAct->rin = $XMLregAct->getElementsByTagName('RIN')->item(0)->nodeValue;
					$regAct->reg_agency_id = $agencyID;
					$regAct->title = $XMLregAct->getElementsByTagName('TITLE')->item(0)->nodeValue;
					$regAct->stage = $XMLregAct->getElementsByTagName('STAGE')->item(0)->nodeValue;
					$regAct->significant = $XMLregAct->getElementsByTagName('ECONOMICALLY_SIGNIFICANT')->item(0)->nodeValue;
					$regAct->date_received = $XMLregAct->getElementsByTagName('DATE_RECEIVED')->item(0)->nodeValue;
					$regAct->legal_deadline = $XMLregAct->getElementsByTagName('LEGAL_DEADLINE')->item(0)->nodeValue;
					if ($category == 'completed') {
						$regAct->date_completed = $XMLregAct->getElementsByTagName('DATE_COMPLETED')->item(0)->nodeValue;
						$regAct->decision = $XMLregAct->getElementsByTagName('DECISION')->item(0)->nodeValue;
					}
					$regAct->category = $category;
					$regAct->save();
					$rin_array []= $regAct->rin;
	    		}
	    	}
	    	//lets delete all acts in that category if it was not in xml! - is it NEDEED?!
	    	$query = "DELETE FROM ".TB_REG_ACTS." WHERE category = '$category' AND rin NOT IN ('".implode('\', \'',$rin_array)."')";
	    	$this->db->query($query);
	    	
	    	//update regActs=>Users Info
	    	$this->updateRegs2Users($rin_array);
	    } else {
	    	$this->parseXML('review');
	    	$this->parseXML('completed');
	    }
    }
    
	public function getRegActsList($customerID = null) {
		
	}
	
	public function getUnreadList($userID, $category = null, $mailed = null) {
		
	}
	
	public function markAsRead($userID, $IDarray = null) {
		
	}
	
	public function markAsMailed($userID, $IDarray = null) {
		
	}
	
	public function getMessageForNotificator($userID) {
		
	}
	
	private function updateRegs2Users($newRINarray) {
		//delete all info with RIN not id db anymore
		$query = "DELETE FROM ".TB_USERS2REGS." WHERE rin NOT IN (SELECT rin FROM ".TB_REG_ACTS." )";
		$this->db->query($query);
		
		//get list for already managed rin's
		$query = "SELECT rin FROM ".TB_USERS2REGS." GROUP BY rin ";
		$this->db->query($query);
		$rins = $this->db->fetch_all_array();
		if ($rins) {
			//lets cut out from array rins was already managed
			foreach($rins as $data) {
				$key = array_search($data['rin'],$newRINarray);
				unset($newRINarray[$key]);
			}
		}
		
		//get all users list
		$user = new User($this->db, null, null, null);
		$userList = $user->getUsersList();
		
		//add new info for not managed RIN's
		$query = "INSERT INTO ".TB_USERS2REGS." (user_id, rin, readed, mailed) VALUES ";
		foreach($userList as $userData) {
			foreach ($newRINarray as $rin) {
				$query .= "('".$userData['user_id']."', '$rin', '0', '0'),";
			}
		}
		$query = substr($query,0,-1);
		$this->db->query($query);
	} 
}
?>