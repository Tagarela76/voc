<?php
class SalesContactsManager
{
	private $db;
	
	function SalesContactsManager($db) {
		$this->db=$db;
	}
	
	public function getContactsList(Pagination $pagination = null,$contacts_type_name) {
		$query = "SELECT c.* from " . TB_CONTACTS . " c ";
		$contacts_type_name = mysql_escape_string($contacts_type_name);
		if(isset($contacts_type_name)) {
			$query .=  ", " . TB_CONTACTS_TYPE . " ct ";
			$query .= " WHERE c.type = ct.id AND ct.name = '$contacts_type_name' ";
		}
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}

		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$contacts = array();
		foreach($arr as $ca) {
			$contact = new SalesContact($this->db,$ca);
			$contacts[] = $contact;
		}
		return $contacts;
	}
	
	public function getSalesContact($contactID) {
		$query = "SELECT * from " . TB_CONTACTS . " WHERE id = $contactID";
		
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$contactsArr = $arr[0];
		
		$contact = new SalesContact($this->db,$contactsArr);
		return $contact;
	}
	
	public function deleteSalesContact($contactID) {
		$query = "DELETE FROM ". TB_CONTACTS . " WHERE id = $contactID";
		$query = mysql_escape_string($query);
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}

//        	public function getTotalCount() {
	public function getTotalCount( $sub ) {
                
              

                $query = "SELECT count(c.id) as 'count' " .
                            "FROM " . TB_CONTACTS . " c, " . TB_CONTACTS_TYPE . " ct " .
                            "WHERE ct.name = '".mysql_escape_string($sub)."' " .
                            "AND c.type = ct.id";

		$this->db->query($query);
		$r = $this->db->fetch_array(0);
		
                return $r['count'];
	}
	
	public function saveContact(SalesContact $c) {		
		
		$state_id = $c->state_id;
		if(!isset($state_id)) {
			$state_id = " NULL ";
		}
		
		
		
		$query = "UPDATE " . TB_CONTACTS . " SET 
					company = '{$c->company}',
					contact = '{$c->contact}',
					phone 	= '{$c->phone}',
					fax		= '{$c->fax}',
					email	= '{$c->email}',
					title	= '{$c->title}',
					government_agencies = '{$c->government_agencies}',
					affiliations	= '{$c->affiliations}',
					industry		= '{$c->industry}',
					comments		= '{$c->comments}',
					state			= '{$c->state}',
					zip_code		= '{$c->zip_code}',
					country_id		= '{$c->country_id}',
					state_id		= $state_id,
					mail			= '{$c->mail}',
					cellphone		= '{$c->cellphone}'
					WHERE id = {$c->id}";
		
		
		
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	
	public function addContact(SalesContact $c) {
		if(!$c->errors) {

			$query = "INSERT INTO " . TB_CONTACTS . " (company,contact,phone,fax,email,title,government_agencies,affiliations,industry,comments,state,zip_code,country_id,state_id,mail,cellphone,type) VALUES (
						'{$c->company}', '{$c->contact}', '{$c->phone}', '{$c->fax}', '{$c->email}', '{$c->title}', '{$c->government_agencies}',  
						'{$c->affiliations}','{$c->industry}','{$c->comments}','{$c->state}','{$c->zip_code}'  
						";
			
			/**
				PHP isset function will return every time false on __get magic method =(
			 */
			$country_id = $c->country_id;
			$state_id = $c->state_id;
			
			$query .= isset($country_id) ? " , ".$c->country_id : " , NULL ";
			$query .= isset($state_id) ? " , ".$c->state_id : " , NULL ";
			
			$query .= " , '{$c->mail}', '{$c->cellphone}' ,";
			
			$query .= " (select id from contacts_type where name = '{$c->type}' limit 1) ";
			
			$query .= " )";
			
			//For Debug
			//$this->db->beginTransaction(); 
			//echo $query; exit;
			$this->db->query($query);
			
			if(mysql_error() == '') {
				return true;
			} else {
				throw new Exception(mysql_error());
			}
		}
	}
}
?>