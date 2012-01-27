<?php
class SalesContactsManager
{
	private $db;
	
	function SalesContactsManager($db) {
		$this->db=$db;
	}
	
	public function getContactsList(Pagination $pagination = null, $contacts_type_name, $filter,$creater_id = null) {
			
		$query = "SELECT c. * FROM " . TB_CONTACTS . " c ";
		$contacts_type_name = mysql_escape_string($contacts_type_name);
		if(isset($contacts_type_name)) {
			$query .=  ", " . TB_BOOKMARKS_TYPE . " ct ";
			$query .= " WHERE c.type = ct.id AND ct.name = '$contacts_type_name'";
		}
                
                if ($filter!='TRUE') {
			$query .= " AND $filter";
		}
/*		if(isset($creater_id)) {
			$query .= " AND c.creater_id = $creater_id";
		}*/		
        $query .= " ORDER BY c.contact ASC";        
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
	
	public function getSalesContact($contactID,$creater_id = null  ) {
		$query = "SELECT * from " . TB_CONTACTS . " WHERE id = $contactID";
			/*	if(isset($creater_id)) {
					$query .= " AND creater_id = $creater_id";
				}	*/	
		$this->db->query($query);
	
		if (! $this->db->num_rows() > 0){
			throw new Exception('Permission denied');			
		}
		$arr = $this->db->fetch_all_array();
		$contactsArr = $arr[0];
		
		$contact = new SalesContact($this->db,$contactsArr);
		return $contact;
	}
	
	public function deleteSalesContact($contactID, $creater_id = null) {
		$query = "DELETE FROM ". TB_CONTACTS . " WHERE id = $contactID";
			/*	if(isset($creater_id)) {
					$query .= " AND creater_id = $creater_id";
				}		*/
				
		$query = mysql_escape_string($query);
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}

	public function getTotalCount( $sub,$creater_id = null ) {              

                $query = "SELECT count(c.id) as 'count' " .
                            "FROM " . TB_CONTACTS . " c, " . TB_BOOKMARKS_TYPE . " ct " .
                            "WHERE ct.name = '".mysql_escape_string($sub)."' " .
                            "AND c.type = ct.id";
				/*if(isset($creater_id)) {
					$query .= " AND c.creater_id = $creater_id";
				}*/

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
					website = '{$c->website}',
					title	= '{$c->title}',
					government_agencies = '{$c->government_agencies}',
					affiliations	= '{$c->affiliations}',
					industry		= '{$c->industry}',
					comments		= '{$c->comments}',
					state			= '{$c->state}',
					city			= '{$c->city}',
					zip_code		= '{$c->zip_code}',
					country_id		= '{$c->country_id}',
					state_id		= $state_id,
					mail			= '{$c->mail}',
					cellphone		= '{$c->cellphone}',
					acc_number		= '{$c->acc_number}',
					creater_id		= '{$c->creater_id}'
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

			$query = "INSERT INTO " . TB_CONTACTS . " (company,contact,phone,fax,email,title,government_agencies,affiliations,industry,comments,state,city,zip_code,creater_id,acc_number,country_id,state_id,mail,cellphone,type) VALUES (
						'{$c->company}', '{$c->contact}', '{$c->phone}', '{$c->fax}', '{$c->email}', '{$c->title}', '{$c->government_agencies}',  
						'{$c->affiliations}','{$c->industry}','{$c->comments}','{$c->state}','{$c->city}','{$c->zip_code}','{$c->creater_id}','{$c->acc_number}'  
						";
			
			/**
				PHP isset function will return every time false on __get magic method =(
			 */
			$country_id = $c->country_id;
			$state_id = $c->state_id;
			
			$query .= isset($country_id) ? " , ".$c->country_id : " , NULL ";
			$query .= isset($state_id) ? " , ".$c->state_id : " , NULL ";
			
			$query .= " , '{$c->mail}', '{$c->cellphone}' ,";
			
			$query .= " (select id from ".TB_BOOKMARKS_TYPE." where name = '".htmlentities($c->type)."' limit 1) ";
			
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
        
        public function contactAutocomplete($occurrence, $sub) {
		$occurrence = mysql_escape_string($occurrence);   
                
                $query = "SELECT * FROM " . TB_BOOKMARKS_TYPE . " WHERE name='".$sub."'";                       
		$this->db->query($query);
		$subNumber = $this->db->fetch(0)->id;
                
                $query = "SELECT id, company, LOCATE('".$occurrence."', company) occurrenceCmp, contact, LOCATE('".$occurrence."', contact) occurrenceCnt FROM ".TB_CONTACTS.
			 " WHERE type = '".$subNumber."' 
                          AND (LOCATE('".$occurrence."', company)>0 OR  LOCATE('".$occurrence."', contact)>0) 
                          LIMIT ".AUTOCOMPLETE_LIMIT;
		$this->db->query($query);
                //var_dump($query);
                //echo $query;
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
	public function countSearchedContacts($contacts, $byField1, $byField2, $subNumber,$creater_id = null ) {
            
                
                $sub = mysql_escape_string($sub);
		$query = "SELECT  * FROM ".TB_CONTACTS."  WHERE type = ".$subNumber." AND (";
		$query = "SELECT  count(id) contactCount FROM ".TB_CONTACTS." c WHERE ((";		
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
				if(isset($creater_id)) {
					$query .= " AND c.creater_id = $creater_id";
				}                
                
       
                $this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			return $this->db->fetch(0)->contactCount;
		} else 
			return false;
	}
        
        
	public function countContacts($subNumber, $filter,$creater_id = null ) {
		
		//$departmentID=mysql_escape_string($departmentID);		
		
		//$this->db->select_db(DB_NAME);
		
		$query = "SELECT count(id) contactsCount FROM ".TB_CONTACTS." WHERE type = $subNumber";                
                if ($filter != 'TRUE') {
					$query .= " AND $filter";
		}
				/*if(isset($creater_id)) {
					$query .= " AND creater_id = $creater_id";
				} */		
                
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
        
		$query = "SELECT  * FROM ".TB_CONTACTS." WHERE type = ".$subNumber." AND ((";
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