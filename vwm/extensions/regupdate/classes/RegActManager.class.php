<?php

class RegActManager {
	private $db;
	
	private $xmlReview = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULES_UNDER_REVIEW.xml";
	private $xmlCompleted = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULE_COMPLETED_30_DAYS.xml";
	
	const CATEGORY_REVIEW = 'review';
	const CATEGORY_COMPLETED = 'completed';

    function __construct($db, $xmlFileReviewPath = XML_FILE_REVIEWED_RULES, $xmlFileCompletedPath = XML_FILE_COMPLETED_RULES) {
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
	    	$xmlDOM->load(($category == self::CATEGORY_REVIEW)?$this->xmlReview:$this->xmlCompleted);
	    	$XMLoira = $xmlDOM->getElementsByTagName('OIRA_DATA')->item(0);
	    	$XMLregs = $XMLoira->getElementsByTagName('REGACT');
	    	$regsCount = $XMLregs->length;
	    	$regAgency = new RegAgency($this->db);
	    	$rin_array = array();
	    	$query = "INSERT INTO ".TB_REG_ACTS." (rin, reg_agency_id, title, stage, significant, date_received, legal_deadline,category, date_completed, decision) VALUES ";
	    	for($i = 0; $i < $regsCount; $i++) {
	    		$XMLregAct = $XMLregs->item($i);
	    		$agencyID = $regAgency->getAgencyIdByCode($XMLregAct->getElementsByTagName('AGENCY_CODE')->item(0)->nodeValue);
	    		if ($agencyID !== false ) {
	    			$rin = mysql_escape_string($XMLregAct->getElementsByTagName('RIN')->item(0)->nodeValue);
	    			$query .= " ( ".
		    		"'".$rin. "', ".
					"'".$agencyID."', ".
					"'".mysql_escape_string($XMLregAct->getElementsByTagName('TITLE')->item(0)->nodeValue)."', ".
					"'".mysql_escape_string($XMLregAct->getElementsByTagName('STAGE')->item(0)->nodeValue)."', ".
					"'".mysql_escape_string($XMLregAct->getElementsByTagName('ECONOMICALLY_SIGNIFICANT')->item(0)->nodeValue)."', ".
					"'".mysql_escape_string($XMLregAct->getElementsByTagName('DATE_RECEIVED')->item(0)->nodeValue)."', ".
					"'".mysql_escape_string($XMLregAct->getElementsByTagName('LEGAL_DEADLINE')->item(0)->nodeValue)."', ".
					"'".$category."' ";
					if ($category == self::CATEGORY_COMPLETED) {
						$query .= ", '".$XMLregAct->getElementsByTagName('DATE_COMPLETED')->item(0)->nodeValue."', ".
								"'".mysql_escape_string($XMLregAct->getElementsByTagName('DECISION')->item(0)->nodeValue)."' ";
					} else {
						$query .= ", NULL, NULL";
					}
					$query .= "), ";
					$rin_array []= $rin;
	    		}
	    	}
	    	$query = substr($query, 0 , -2);
	    	$this->db->query('DELETE FROM '.TB_REG_ACTS.' WHERE category = \''.$category.'\''); //delete all acts was in db
	    	$this->db->query($query);
	    	
	    	//update regActs=>Users Info
	    	$this->updateRegs2Users($rin_array);
	    } else {
	    	$this->parseXML(self::CATEGORY_REVIEW);
	    	$this->parseXML(self::CATEGORY_COMPLETED);
	    }
    }
    
    /**
     * function getRegActsList($userID = null)
     * gets list of acts from db, if was set userId in objects filled info about act was readen by user, was act mailed to user.
     * @param int $userID
     * @return array of RegAct objects 
     */
	public function getRegActsList($userID = null, $category = self::CATEGORY_REVIEW, $pagination = null) {
		$query = "SELECT * FROM ".TB_REG_ACTS." ra ".
			( (!is_null($userID)) ?
				(", ".TB_USERS2REGS." u2r, ".TB_REG_AGENCY." rag " .
				" WHERE ra.rin = u2r.rin AND u2r.user_id = '$userID'".
					" AND ra.reg_agency_id = rag.id " .
					" AND ra.category = '$category' ") : 
				(" WHERE ra.category = '$category' ")
			).
			" ORDER BY ra.category, ra.date_received desc";
		if (!is_null($pagination)) {
				$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
		
		$this->db->query($query);
		if ($this->db->num_rows()>0) {
			$data = $this->db->fetch_all_array();
			
			//the funiest part: make from array of assoc-array an array of objects))
			$objectsList = array();
			foreach($data as $actData) {
				$objectsList []= $this->arrayIntoRegActObject($actData);
			}
			return $objectsList;
		}
		return false;
	}
	
	/**
	 * function getUnreadList
	 * get Unread(by default unmailed too) list to notify about new updates
	 * @param int $userID - can be $id or array of ids
	 * @param string $category
	 * @param bool $mailed
	 * @return array of RegAct objects
	 */
	public function getUnreadList($userID, $category = null, $mailed = false) {
		
		$query = "SELECT * FROM ".TB_REG_ACTS." ra, ".TB_USERS2REGS." u2r, ".TB_REG_AGENCY." rag " .
				"WHERE ra.rin = u2r.rin AND u2r.user_id ".((is_array($userID))?" IN ('".implode('\', \'',$userID)."')":"= '$userID'")." " .
					"AND u2r.readed = '0' AND u2r.mailed = '".((!$mailed)?'0':'1')."' " .
					((!is_null($category))?" AND ra.category = '$category' ":"").
					"AND ra.reg_agency_id = rag.id ".
				"ORDER BY ra.category ";
		$this->db->query($query);
		if ($this->db->num_rows()>0) {
			$data = $this->db->fetch_all_array();
			
			//the funiest part: make from array of assoc-array an array of objects))
			$objectsList = array();
			foreach($data as $actData) {
				$objectsList []= $this->arrayIntoRegActObject($actData);
			}
			return $objectsList;
		}
		return false;
	}
	
	/**
	 * function markRIN
	 * mark RIN for user(was it readen? was it mailed?)
	 * @param int  $userID - can be id or array of id 
	 * @param string $action = 'readed'/'mailed'
	 * @param array of int $RINarray
	 */
	public function markRIN($userID,$action = 'readed', $RINarray = null, $category = null) {
		$query = "UPDATE ".TB_USERS2REGS." u2r SET ".(($action == 'readed')?"u2r.readed":"u2r.mailed")." = '1' " .
				"WHERE u2r.user_id = '$userID' ".((!is_null($RINarray))?" AND u2r.rin IN ('".implode('\', \'',$RINarray)."')":"").
				( (!is_null($category)) ? " AND '$category' = (SELECT ra.category FROM ".TB_REG_ACTS." ra WHERE ra.rin = u2r.rin)" : "" );
		$this->db->query($query);
	}
	
	/**
	 * function getMessageForNotificator
	 * @param int $userID - can be id or array of ids
	 * @return string $textToMail
	 */
	public function getMessageForNotificator($userID) {
		$listToMail = $this->getUnreadList($userID); //its already sorted by category
		if (!$listToMail) {
			return false;
		}
		$textToMail = "New updates in Enviromental Protection Agency Regulations! \n";
		$curCategory = "";
		foreach($listToMail as $regAct) {
			if ($regAct->category != $curCategory) {
				$curCategory = $regAct->category;
				$textToMail .= "\n\n\tExecutive Order Submissions ";
				if ($curCategory == self::CATEGORY_REVIEW) {
					$textToMail .= "Under Review\n";
				} elseif ($curCategory == self::CATEGORY_COMPLETED) {
					$textToMail .= "with Review Completed in Last 30 Days\n";
				}
			}
			$textToMail .= "\nAGENCY: ".$regAct->reg_agency->name." \n";
			$textToMail .= "RIN: ".$regAct->rin." \n";
			$textToMail .= "TITLE: ".$regAct->title." \n";
			$textToMail .= "STAGE: ".$regAct->stage." \n";
			$textToMail .= "ECONOMICALLY SIGNIFICANT: ".$regAct->significant." \n";
			$textToMail .= "RECEIVED DATE: ".$regAct->date_received." \n";
			$textToMail .= "LEGAL DEADLINE: ".$regAct->legal_deadline." \n";
			if ($curCategory == self::CATEGORY_COMPLETED) {
				$textToMail .= "COMLETED: ".$regAct->date_completed." \n";
				$textToMail .= "DECISION: ".$regAct->decision." \n";
			}
		}
		
		$textToMail .= "\n______ \n";
		$textToMail .= "You can swith off it in EmailNotificator option in your VOCWEBMANAGER Settings"; //here we should add some footer))
		return $textToMail;
	}
	
	/**
	 * getUnreadCountForCategries($userID)
	 * @param $userID
	 * @return array - counts for review and for completed
	 */
	public function getUnreadCountForCategories($userID) {
		$query = "SELECT count(ra.category) as count, ra.category " .
				" FROM ".TB_REG_ACTS." ra, ".TB_USERS2REGS." u2r " .
				" WHERE ra.rin = u2r.rin AND u2r.user_id = '$userID' " .
					"AND u2r.readed = '0' " .
				" GROUP BY ra.category ";
		$this->db->query($query);
		return $this->db->fetch_all_array();
	}
	
	public function getCountForCategory($category) {
		$query = "SELECT count(category) as count FROM ".TB_REG_ACTS." WHERE category = '$category' GROUP BY category ";
		$this->db->query($query);
		return $this->db->fetch(0)->count;
	}
	
	private function arrayIntoRegActObject($actData) {
		$regAct = new RegAct($this->db);
		foreach($actData as $property => $value) {
			if (property_exists($regAct, $property)) {
				$regAct->$property = $value;
			}
		}
		//now lets manage RegAgency in RegActObject
		$agency = new RegAgency($this->db);
		$agency->id = $actData['id'];
		$agency->name = $actData['name'];
		$agency->code = $actData['code'];
		$agency->acronym = $actData['acronym'];
		$regAct->reg_agency = $agency;
		return $regAct;
	}
	
	private function updateRegs2Users($newRINarray) {
		//delete all info with RIN not id db anymore
		$query = "DELETE FROM ".TB_USERS2REGS." WHERE rin NOT IN (SELECT rin FROM ".TB_REG_ACTS." )";
		$this->db->query($query);
		
		//get list for already managed rin's
		$query = "SELECT rin FROM ".TB_USERS2REGS." WHERE rin IN ('".implode("', '", $newRINarray)."') GROUP BY rin ";
		$this->db->query($query);
		$rins = $this->db->fetch_all_array();
				
		//get all users list
		$userObj = new User($this->db, null, null, null);
		$userList = $userObj->getUsersList();
		$regUpdateUsers = array();
		foreach($userList as $userData) {
			if ($userData['accesslevel_id'] == 3 || $userObj->checkAccess('regupdate',$userData['company_id'])) {
				$regUpdateUsers []= $userData['user_id'];
			}
		}
		
		//delete all info with users without this module anymore
		$query = "DELETE FROM ".TB_USERS2REGS." WHERE user_id NOT IN ('".implode("', '", $regUpdateUsers)."')";
		$this->db->query($query);
		
		if ($rins) {
			//lets get list for already  managed users
			$query = "SELECT user_id FROM ".TB_USERS2REGS." WHERE rin IN ('".implode("', '", $newRINarray)."') GROUP BY user_id ";
			$this->db->query($query);
			$managedUserList = $this->db->fetch_all_array();
			
			//now check is all users was managed for old rins(there can be new users)
			foreach($managedUserList as $key => $user) {
				$managedUserList[$key] = $user['user_id'];
			}
			
			$newUsers = array();
			foreach($regUpdateUsers as $userID) {
				if (!in_array($userID, $managedUserList)) {
					$newUsers []= $userID;
				}
			}
			
			$queryPartForNewUsers = '';
			
			//lets cut out from array rins was already managed
			foreach($rins as $data) {
				foreach ($newUsers as $userID) {
					$queryPartForNewUsers .= "('".$userID."', '".$data['rin']."', '0', '0'),";
				}
				$key = array_search($data['rin'],$newRINarray);
				unset($newRINarray[$key]);
			}
			$queryPartForNewUsers = substr($queryPartForNewUsers, 0, -1);
			
		}
		
		//add new info for not managed RIN's + manage also new users for old RINS!!!
		$query = "INSERT INTO ".TB_USERS2REGS." (user_id, rin, readed, mailed) VALUES ";
		foreach($regUpdateUsers as $userID) {
			foreach ($newRINarray as $rin) {
				$query .= "('".$userID."', '$rin', '0', '0'),";
			}
		}
		$query = ((!is_null($queryPartForNewUsers) && $queryPartForNewUsers != '')?$query.$queryPartForNewUsers:substr($query, 0, -1));
		$this->db->query($query);
	} 
}
?>