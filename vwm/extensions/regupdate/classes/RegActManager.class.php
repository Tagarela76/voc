<?php

class RegActManager {
	private $db;
	
	private $xmlReview = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULES_UNDER_REVIEW.xml";
	private $xmlCompleted = "http://www.reginfo.gov/public/do/XMLViewFileAction?f=EO_RULE_COMPLETED_30_DAYS.xml";
	
	const CATEGORY_REVIEW = 'review';
	const CATEGORY_COMPLETED = 'completed';

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
	    	$xmlDOM->load(($category == self::CATEGORY_REVIEW)?$this->xmlReview:$this->xmlCompleted);
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
					if ($category == self::CATEGORY_COMPLETED) {
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
	public function getRegActsList($userID = null) {
		$query = "SELECT * FROM ".TB_REG_ACTS." ra".((!is_null($userID))?", ".TB_USERS2REGS." u2r, ".TB_REG_AGENCY." rag " .
				" WHERE ra.rin = u2r.rin AND u2r.user_id = '$userID'":"").
					" AND ra.reg_agency_id = rag.id ".
				" ORDER BY ra.category ";
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
	 * @param int $userID
	 * @param string $category
	 * @param bool $mailed
	 * @return array of RegAct objects
	 */
	public function getUnreadList($userID, $category = null, $mailed = false) {
		$query = "SELECT * FROM ".TB_REG_ACTS." ra, ".TB_USERS2REGS." u2r, ".TB_REG_AGENCY." rag " .
				"WHERE ra.rin = u2r.rin AND u2r.user_id = '$userID' " .
					"AND u2r.readed = '0' AND u2r.mailed = '".((!$mailed)?'0':'1')."' " .
					((!is_null($category))?" AND ra.category = '$category' ":"").
					"AND ra.reg_agency_id = rag.id ".
				"ORDER BY ra.category ";
		$this->db->query($query);//var_dump($query);
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
	 * @param int  $userID
	 * @param string $action = 'readed'/'mailed'
	 * @param array of int $RINarray
	 */
	public function markRIN($userID,$action = 'readed', $RINarray = null) {
		$query = "UPDATE ".TB_USERS2REGS." SET ".(($action == 'readed')?"readed":"mailed")." = '1' " .
				"WHERE user_id = '$userID' ".((!is_null($RINarray))?" AND rin IN ('".implode('\', \'',$RINarray)."')":"");
		$this->db->query($query);var_dump($query);
	}
	
	/**
	 * function getMessageForNotificator
	 * @param int $userID
	 * @return string $textToMail
	 */
	public function getMessageForNotificator($userID) {
		$listToMail = $this->getUnreadList($userID); //its already sorted by category
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
	
	private function arrayIntoRegActObject($actData) {
		$regAct = new RegAct($this->db);
		foreach($actData as $property => $value) {
			if (property_exists($regAct, $property)) {
				$regAct->$property = $value;
			}
		}
		//now lets manage RegAgency in RegActObject
		$agency = new RegAgency($this->db);
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