<?php

class CTDate extends CType {
	private $companyID;
	private $stamp;//last converted stamp

    function CTDate($db, $companyID = null) {
    	parent::CType($db);
    	$this->mainFormat = 'Y-m-d';//!main format should be usable for php and mySQL
    	$this->companyID = $companyID;
    	$this->_loadConfig();
    }
    
    
    private function _loadConfig() {
    	//step 1: we should check for accesslevel(it can be a superuser level)
    	if (is_null($this->companyID)) {
    		//oh? this is a super user level! We should use defaults!
    		$this->format = $this->mainFormat;
    		return;
    	}
    	
    	//step 2: we should get company's date format!
    	$formatDetails = $this->getDateFormatByCompanyID($this->companyID);
    	
    	$this->format = $formatDetails->format;
    	$this->outputFormat = $formatDetails->description;
    }
    
    private function getDateFormatByCompanyID($companyID) {
    	$query = "SELECT c.date_format_id as id, df.format, df.description FROM ".TB_COMPANY." c, ".TB_DATE_FORMAT." df " .
    			" WHERE c.company_id = '$companyID' AND " .
    			" c.date_format_id = df.id " .
    			" LIMIT 1";
    	
    	$this->db->query($query);
    	return $this->db->fetch(0);
    }
    
    public function convert($value, $toMain = false) {
    	if ($toMain) {
    		$this->stamp = $this->getStampByFormat($value,$this->format);
    		if ($this->errors[$value]['outOfFormat']) {
    			//value can be out of format only if its already in main format
    			$this->stamp = strtotime($value);
    			return date($this->mainFormat,$this->stamp);//we need it to be sure the date is valid!
    		}
    		return date($this->mainFormat,$this->stamp);
    	} else {
    		$this->stamp = strtotime($value);
    		return date($this->format,$this->stamp);
    	}
    }
    
    public function getLastStamp() {
    	return $this->stamp;
    }
    
    public function getFormatForCalendar() {
    	
    	
    	if($this->outputFormat){
    		$calendarFormat = str_replace('yyyy','yy',$this->outputFormat);
    	}
    	else {
    		
    		$calendarFormat = str_replace('Y','yy',$this->mainFormat);
    	}
    	return $calendarFormat;
    }


    private function getStampByFormat($value, $format) {
    	$valueInArray = str_split($value);
    	$formatInArray = str_split($format);
    	$formatIndex = 0;
    	$errors = array();
    	$result = array();
    	$delimeter = false;
    	foreach($valueInArray as $key => $val) {
    		if (is_numeric($val) && (!$delimeter || $formatInArray[$formatIndex+1] != $val)) {
    			if ($delimeter) {
    				$formatIndex++;
    				$delimeter = false;
    			}
    			$result[strtolower($formatInArray[$formatIndex])] .= $val; 
    		} else {
    			if ($formatInArray[$formatIndex+1] == $val) {
    				$delimeter = true;
    				$formatIndex++;
    			} else {
    				$errors []= 'Out of format symbol:"'.$val.'" ';
    				$errors ['outOfFormat'] = true;
    			}
    		}
    	}
    	
    	//take stamp for date
    	$stamp = strtotime($result['y'].'-'.$result['m'].'-'.$result['d']);
    	if (!checkdate($result['m'],$result['d'],date('Y',$stamp))) {
    		$errors []= 'Not a valid date for Gregorian calendar!';
    	}
    	$this->errors[$value] = $errors;
    	return $stamp;
    }
    
    
}
?>