<?php
class SalesContactsManager
{
	private $db;
	
	function SalesContactsManager($db) {
		$this->db=$db;
	}
	
	public function getContactsList(Pagination $pagination = null, $contacts_type_name, $filter) {
		$query = "SELECT c. * FROM " . TB_CONTACTS . " c ";
		$contacts_type_name = mysql_escape_string($contacts_type_name);
		if(isset($contacts_type_name)) {
			$query .=  ", " . TB_CONTACTS_TYPE . " ct ";
			$query .= " WHERE c.type = ct.id AND ct.name = '$contacts_type_name'";
		}
                
                if ($filter!='TRUE') {
			$query .= " AND $filter";
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
        
        public function contactAutocomplete($occurrence, int $subNumber) {
		$occurrence = mysql_escape_string($occurrence);                
                $query = "SELECT id, company, LOCATE('".$occurrence."', company) occurrenceCmp, contact, LOCATE('".$occurrence."', contact) occurrenceCnt FROM ".TB_CONTACTS.
			 " WHERE type = ".$subNumber." 
                          AND (LOCATE('".$occurrence."', company)>0 OR  LOCATE('".$occurrence."', contact)>0) 
                          LIMIT ".AUTOCOMPLETE_LIMIT;
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$contacts = $this->db->fetch_all();
			foreach ($contacts as $contact) {
				if($contact->occurrenceCmp) {
					$results[] = $contact->company;
                                        $results = array_unique($results);
				}
                                if($contact->occurrenceCnt) {
					$results[] = $contact->contact;
                                        $results = array_unique($results);
				}
			}
			return (isset($results)) ? $results : false;								
		} else 
			return false;		
	}
        
        /**	 
	 * Count search contacts
	 * @param  $contacts - value of field to search, array or string
	 * @param string $byField - field name
	 */	
	public function countSearchedContacts($contacts, $byField1, $byField2, $subNumber) {
            
                
                $sub = mysql_escape_string($sub);
		$query = "SELECT  * FROM ".TB_CONTACTS." WHERE type = ".$subNumber." AND (";
                //$sub=mysql_escape_string($type);
		$query = "SELECT  count(id) contactCount FROM ".TB_CONTACTS." WHERE ((";		
		if (!is_array($contacts)) {
			$contacts = array($contacts);
		}
		$sqlParts = array();
		foreach ($contacts as $contact) {
			$sqlParts[] = $byField1." LIKE '%".$contact."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.") OR (";
                
		
                $sqlParts = array();
		foreach ($contacts as $contact) {
			$contact=mysql_escape_string($contact);
			$sqlParts[] = $byField2." LIKE '%".$contact."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql."))";
                
                
                
                $this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->contactCount;
		} else 
			return false;
	}
        
        
	public function countContacts($subNumber, $filter) {
		
		//$departmentID=mysql_escape_string($departmentID);		
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT count(id) contactsCount FROM ".TB_CONTACTS." WHERE type = $subNumber";                
                if ($filter != 'TRUE') {
			$query .= " AND $filter";
		}
                
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->contactsCount;
		} else 
			return false;
	}        
        
        
        /**	 
	* Search contacts
	* @param  $contacts - value of field to search, array or string
	* @param string $byField - field name
        * @param string $subNumber - number of subBookmark
	*/
	public function searchContacts($contacts, $byField1, $byField2, $subNumber, Pagination $pagination = null) {
            
		$query = "SELECT  * FROM ".TB_CONTACTS." WHERE type = ".$subNumber." AND (";
		if (!is_array($contacts)) {
			$contacts = array($contacts);
		}
                
		$sqlParts = array();
		foreach ($contacts as $contact) {
			$sqlParts[] = $byField1." LIKE '%".$contact."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.") OR (";
                
		
                $sqlParts = array();
		foreach ($contacts as $contact) {
			$contact=mysql_escape_string($contact);
			$sqlParts[] = $byField2." LIKE '%".$contact."%'";		
		}
                
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.")";		
		
                
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}	
		
		$this->db->query($query);	
		if ($this->db->num_rows() > 0) {	
			$searchedContacts = $this->db->fetch_all_array();
		}
                
                $searchcontacts = array();
		foreach($searchedContacts as $searchedContact) {
			$searchcontact = new SalesContact($this->db,$searchedContact);        
			$searchcontacts[] = $searchcontact;
		}
                
                $searchedContact = $searchcontacts;
		return (isset($searchcontacts)) ? $searchcontacts : null;		
	}   
	
}
?>