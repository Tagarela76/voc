<?php

class EmailNotifications {
	private $db;
	
	private $login_time;
	private $notify_time;
	private $user_id;
	private $id;
	
	private $map = array(
		'daily' => 'Daily Limit Exceeded',
		'department_monthly' => 'Department Limit Exceeded',
		'department_annual' => 'Department Annual Limti Exceeded',
		'facility_monthly' => 'Facility Monthly Limit Exceeded',
		'facility_annual' => 'Facility Annual Limit Exceeded',
		'equipment_expired' => 'Equipment Expired',
		'equipment_preexpired' => 'Equipment Preexpired',
		'waste_storage_critical' => 'Waste Storage Limit Overflow',
		'regupdate' => 'EPA Regulations Updates');

    function EmailNotifications($db, $user_id = null) {
    	$this->db = $db;
		$this->db->select_db(DB_NAME);
    	if (!is_null($user_id)) {
    		$this->_load($user_id);
    	}
    }

    /**
     * function getLimits2Notify($limit_name,$category,$category_id)
     * @param array of string $limit_names_array - array of names of limites 
     * @param string $category - name of category where was this limit expired = department, facility, etc.
     * @param int $category_id - id by category
     */
    public function getLimits2Notify($limit_names_array,$category = null,$category_id = null) {
    	//return array of messages with limits to send by user with $this->user_id
    	switch ($category) {
    		case 'company':
    			$this->db->query('SELECT name FROM '.TB_COMPANY.' WHERE company_id = \''.$category_id.'\'');
    			$category_name = $this->db->fetch(0)->name; 
    			$companyUserQuery = " OR (u.accesslevel_id = 0 AND u.company_id = '$category_id') ";
    			$facilityUserQuery = " ";   
    			$departmentUserQuery = " "; 	
    			break;
    		case 'facility':
    			$this->db->query("SELECT name FROM ".TB_FACILITY." WHERE facility_id='".$category_id."' ORDER BY name");
				$category_name = $this->db->fetch(0)->name;
    			$companyUserQuery = " OR (u.accesslevel_id = 0 AND u.company_id = (SELECT company_id FROM ".TB_FACILITY." " .
    					"WHERE facility_id = '$category_id' LIMIT 1)) ";
    			$facilityUserQuery = " OR (u.accesslevel_id = 1 AND u.facility_id = '$category_id') ";   
    			$departmentUserQuery = " "; 	    			
    			break;
    		case 'department':
				$this->db->query("SELECT name,facility_id FROM ".TB_DEPARTMENT." WHERE department_id='".$category_id."' ORDER BY name");
				$query_result=$this->db->fetch(0);
				$category_name = $query_result->name;
				$this->db->query("SELECT name FROM ".TB_FACILITY." WHERE facility_id='".$query_result->facility_id."' LIMIT 1");
				$facility_name=$this->db->fetch(0)->name;
    			$companyUserQuery = " OR (u.accesslevel_id = 0 AND u.company_id = (SELECT f.company_id FROM ".TB_FACILITY." f, ".TB_DEPARTMENT." d " .
    					"WHERE d.department_id = '$category_id' AND d.facility_id = f.facility_id LIMIT 1)) ";
    			$facilityUserQuery = " OR (u.accesslevel_id = 1 AND u.facility_id = (SELECT facility_id FROM ".TB_DEPARTMENT." " .
    					"WHERE department_id = '$category_id' LIMIT 1)) ";   
    			$departmentUserQuery = " OR (u.accesslevel_id = 2 AND u.department_id = '$category_id') "; 			
    			break;
    	}
    	$limit_names = "(";
    	foreach ($limit_names_array as $name) {
    		$limit_names .= " '$name',";
    	}
		$limit_names = substr($limit_names,0,-1).")";
    	$query = "SELECT u.email, u.user_id, l.limit_name FROM ".TB_USER." u, ".TB_LIMIT2USER." lu, ".TB_LIMITES." l " .
    			"WHERE l.limit_name IN $limit_names AND lu.limit_id = l.limit_id AND u.user_id = lu.user_id " .
    			"AND lu.on_off = '1' AND (u.accesslevel_id = '3' ".$companyUserQuery.$facilityUserQuery.$departmentUserQuery.") ";
    	$this->db->query($query);
    	if ($this->db->num_rows() == 0) {
    		return false;
    	} else {
    		$data = $this->db->fetch_all();    		
    		$emails = array();
    		$notifyList = array();
    		foreach ($data as $user) {
    			$ids[$user->email][]= $user->user_id;
    			if (!in_array($user->limit_name,$emails[$user->email])) {
    				$emails[$user->email][]= $user->limit_name;    				
    			}
    			
    		}    
    		$idsToSend = array();		
    		foreach ($emails as $email => $limits) {
    			$toSend = false;
    			foreach ($ids[$email] as $id) {
    				if ($this->checkUser($id)) {
    					$toSend = true;
    					break;
    				}
    			}
    			
    			if (is_null($category) && is_null($category_id)) {
    				//if we get here its a periodic limit - no need to check users
    				$notifyList []= array('email' => $email, 'limits' => $limits, 'id' => $ids[$email]);
    			} elseif ($toSend) {
    				$idsToSend[$email] = $ids[$email];
    				
    				$point=($category=="department"?("$category \"$category_name\" (facility \"$facility_name\")"):("$category \"$category_name\""));
    				$notify = "Warning! At the $point violations of the following limits: ";
	    			foreach ($limits as $limit) {
	    				$notify .= $limit.", ";
	    			}
	    			$notify = substr($notify,0,-2).".";
	    			$notifyList []= array('email' => $email, 'message' => html_entity_decode($notify), 'id' => $ids[$email]); 
    			}
    		}
    		
    		return $notifyList;
    	}
    }
    
    public function getAllLimits() {
    	$query = "SELECT limit_id AS id, limit_name AS name FROM ".TB_LIMITES." ";
    	$this->db->query($query);
    	$data =  $this->db->fetch_all_array();
    	foreach ($data as $key => $record) {
    		$data[$key]['description'] = $this->map[$record['name']];
    	}
    	return $data;
    }
    
    public function saveTime($type = 'login', $user_id = null) {
    	//saves time of user was login-ed
    	if (!is_null($user_id) && ($user_id != $this->user_id)) {
    		$this->user_id = $user_id;
    		$this->id = null;
    	} elseif (is_null($this->user_id)) {
    		return false;
    	}
    	if (is_null($this->id)) {
	    	$query = "SELECT id FROM ".TB_NOTIFY_TIME." WHERE user_id = '$this->user_id' LIMIT 1 ";
	    	$this->db->query($query);
	    	$this->id = $this->db->fetch(0)->id;
    	} 
    	if (!is_null($this->id)) {
    		$query = "UPDATE ".TB_NOTIFY_TIME." SET ".(($type == 'login')?"login_time":"notify_time")." = '".time()."' WHERE id = '".$this->id."' ";
    	} else {
    		$query = "INSERT INTO ".TB_NOTIFY_TIME." (user_id, ".(($type == 'login')?"login_time":"notify_time").") VALUES ('$this->user_id', '".time()."')";
    	}
    	$this->db->query($query);
    }
    
    public function getLimitsListByUser($user_id) {
    	//return list with limits for chosen user - for menu settings
    	if (!is_null($user_id)) {
    		$this->user_id = $user_id;
    	} elseif (is_null($this->user_id)) {
    		return false;
    	}
    	$query = "SELECT l.limit_name AS name, l.limit_id AS id FROM ".TB_LIMITES." l, ".TB_LIMIT2USER." lu WHERE lu.limit_id = l.limit_id AND lu.user_id = '$this->user_id' AND lu.on_off = '1' ";
    	$this->db->query($query);
    	return $this->db->fetch_all_array();
    }
    
	public function getUsersListByLimitType($limit_id) {
		//return list with users with chosen limit
		$query = "SELECT user_id FROM ".TB_LIMIT2USER." WHERE limit_id = '$limit_id' AND on_off = '1' ";
		$this->db->query($query);
		if($this->db->num_rows()>0) {
			$data = $this->db->fetch_all();
			$users_list = array();
			foreach ($data as $user) {
				$users_list []= $user->user_id;
			}
			return $users_list;
		} else return false;
	}
	
	public function checkLimits($mixValidatorResponse,$category,$category_id) {
		if ($mixValidatorResponse->isSomeLimitExceeded()) {
			$limites = array();
			if ($mixValidatorResponse->isDailyLimitExceeded()) {
				$limites []= 'daily';
			}
			if ($mixValidatorResponse->isDepartmentLimitExceeded()) {
				$limites []= 'department_monthly';
			}
			if ($mixValidatorResponse->getDepartmentAnnualLimitExceeded()) {
				$limites []= 'department_annual';
			}
			if ($mixValidatorResponse->isFacilityLimitExceeded()) {
				$limites []= 'facility_monthly';
			}
			if ($mixValidatorResponse->getFacilityAnnualLimitExceeded()) {
				$limites []= 'facility_annual';
			}
			if ($mixValidatorResponse->isExpired()) {
				$limites []= 'equipment_expired';
			}
			if ($mixValidatorResponse->isPreExpired()) {
				$limites []= 'equipment_preexpired';
			}
			if (false) {
				$limites []= 'waste_storage_critical';
			}
			$notifyList = $this->getLimits2Notify($limites,$category,$category_id);
			$this->sendNotify($notifyList);
			foreach ($notifyList as $notify) {
				foreach ($notify['id'] as $id) {
					$this->saveTime('notify', $id);
				}
			}
		}
	}
    
    public function sendNotify($notifyList) {
    	/*
    	$from = "kttsoft.mailtester@mail.ru";
    	$theme = "Notification: ";
    	
    		function get_data($smtp_conn)
				{
					$data="";
					while($str = fgets($smtp_conn,515)) 
					{
					$data .= $str;
					if(substr($str,3,1) == " ") { break; }
					}
					return $data;
				}
    	
    	foreach ($notifyList as $nonify)
		{	
				$to = $nonify['email'];			
				
					
					$header="Date: ".date("D, j M Y G:i:s")." +0700\r\n"; 
					$header.="From: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode('VOC WEB MANAGER mail Tester')))."?= <kttsoft.mailtester@mail.ru>\r\n"; 
					$header.="X-Mailer: The Bat! (v3.99.3) Professional\r\n"; 
					$header.="Reply-To: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode('VOC WEB MANAGER mail Tester')))."?= <kttsoft.mailtester@mail.ru>\r\n";
					$header.="X-Priority: 3 (Normal)\r\n";
					$header.="Message-ID: <172562218.".date("YmjHis")."@mail.ru>\r\n";
					$header.="To: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode('VOC WEB MANAGER mail Tester')))."?= <$to>\r\n";
					$header.="Subject: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode('test')))."?=\r\n";
					$header.="MIME-Version: 1.0\r\n";
					$header.="Content-Type: text/plain; charset=windows-1251\r\n";
					$header.="Content-Transfer-Encoding: 8bit\r\n";
					
					$text=$nonify['message'];
					
					$smtp_conn = fsockopen("smtp.mail.ru", 25,$errno, $errstr, 10);
					if(!$smtp_conn) {print "соединение с серверов не прошло"; fclose($smtp_conn); exit;}
					$data = get_data($smtp_conn);
					fputs($smtp_conn,"EHLO mail.ru\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 250) {print "ошибка приветсвия EHLO"; fclose($smtp_conn); exit;}
					fputs($smtp_conn,"AUTH LOGIN\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 334) {print "сервер не разрешил начать авторизацию"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,base64_encode("kttsoft.mailtester")."\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 334) {print "ошибка доступа к такому юзеру"; fclose($smtp_conn); exit;}
					
					
					fputs($smtp_conn,base64_encode("developer")."\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 235) {print "не правильный пароль"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,"MAIL FROM:kttsoft.mailtester@mail.ru\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 250) {print "сервер отказал в команде MAIL FROM"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,"RCPT TO:$to\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 250 AND $code != 251) {print "Сервер не принял команду RCPT TO"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,"DATA\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 354) {print "сервер не принял DATA"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,$header."\r\n".$text."\r\n.\r\n");
					$code = substr(get_data($smtp_conn),0,3);
					if($code != 250) {print "ошибка отправки письма"; fclose($smtp_conn); exit;}
					
					fputs($smtp_conn,"QUIT\r\n");
					fclose($smtp_conn);
		}*/
  	 	$email = new EMail();
    	$from = AUTH_SENDER."@".DOMAIN;
    	$theme = "Notification ";
		
		foreach ($notifyList as $nonify)
		{						
			$to = array (			
							$nonify['email']
						);		
			
			$message = $nonify['message'];		
			$email->sendMail($from, $to, $theme, $message);
		}
    }
    
    private function checkUser($user_id = null) {
    	//check: does user need to get notify?
    	if (!is_null($user_id)) {
    		$this->_load($user_id);
    	}
    	if ((!is_null($this->login_time) && ($this->login_time > $this->notify_time)) || (is_null($this->notify_time)) || (time() > $this->notify_time + (12*(60*60)))) {
    		return true;
    	} else return false;
    }
    
    public function saveLimits2User($limites, $user_id = null) {
    	//save chosen notifies for user - maybe it can be public function, $limites - array of id
    	if (!is_null($user_id)) {
    		$this->user_id = $this->db->sqltext($user_id);
    	} elseif (is_null($this->user_id)) {
    		return false;
    	}
    	$query = "DELETE FROM ".TB_LIMIT2USER." WHERE user_id = '".$this->user_id."' ";
    	$this->db->query($query);
    	$query = "INSERT INTO ".TB_LIMIT2USER." (user_id, limit_id, on_off) VALUES ";
    	foreach($limites as $limit_id) {
    		$query .= "('".$this->user_id."', '".$this->db->sqltext($limit_id)."', 1) ,";
    	}
    	$query = substr($query,0,-1);
    	return $this->db->query($query);
    }
    
    public function checkPeriodicNotifiers() {
    	$periodicNotifiers = array('regupdate');
    	//here we should get List to notify
    	$notifyList = $this->getLimits2Notify($periodicNotifiers);
    	foreach($notifyList as $key => $data2notify) {
    		$message = '';
    		foreach ($data2notify['limits'] as $limit) {
    			$message .= $this->getPeriodicMessage($limit,$data2notify['id'])."\n\n";
    		}
    		$notifyList[$key]['message'] = $message;
    	}
    	//var_dump($notifyList);
    	$this->sendNotify($notifyList);
    	
    	//set notifiers as sent
//    	foreach($notifyList as $key => $data2notify) {
//    		foreach ($data2notify['limits'] as $limit) {
//    			$this->setPeriodicSent($limit,$data2notify['id'])."\n\n";
//    		}
//    		$notifyList[$key]['message'] = $message;
//    	}
    }
    
    private function getPeriodicMessage($limit,$id) {
    	switch ($limit) {
    		case 'regupdate':
    			$regManager = new RegActManager($this->db);
    			$notify = $regManager->getMessageForNotificator($id);
    			break;
    	}
    	return $notify;
    }
    
    private function _load($user_id) {
    	//for 1st load user time
    	$this->user_id = $user_id;
    	$query = "SELECT * FROM ".TB_NOTIFY_TIME." WHERE user_id = '$this->user_id' LIMIT 1";
    	$this->db->query($query);
    	if ($this->db->num_rows() == 0) {
    		return false;
    	}
    	$data = $this->db->fetch(0);
    	$this->id = $data->id;
    	$this->login_time = $data->login_time;
    	$this->notify_time = $data->notify_time;
    	return true;
    }
}
?>