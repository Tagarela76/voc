<?php
class SalesContactsManager
{
	private $db;
	
	function SalesContactsManager($db) {
		$this->db=$db;
	}
	
	public function getContactsList(Pagination $pagination = null, $contacts_type_name, $filter,$creater_id = null,$sortStr = null ) {
	
		$query = "SELECT c. * FROM " . TB_CONTACTS . " c ";
		$contacts_type_name = mysql_escape_string($contacts_type_name);
		if(isset($contacts_type_name) && $contacts_type_name != '') {
			$query .=  ", " . TB_BOOKMARKS_TYPE . " bt , contacts2type ct, country co ";
			$query .= " WHERE ct.type_id = bt.id AND bt.name = '$contacts_type_name' AND ct.contact_id = c.id AND co.country_id = c.country_id ";
		}
                
                if ($filter != 'TRUE') {
			$query .= " AND $filter";
		}
/*		if(isset($creater_id)) {
			$query .= " AND c.creater_id = $creater_id";
		}*/	
		if (isset($sortStr)) {
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY c.contact ASC";
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
	public function saveSalesContactType($contactID,$types  ) {
		$query = "DELETE FROM contacts2type WHERE contact_id = '{$contactID}'";
		$this->db->query($query);
		if (isset($types) && $types != null){
			foreach ($types as $type){
				$query = "INSERT INTO contacts2type VALUES ( NULL, '{$contactID}','{$type}')";
				$this->db->query($query);
			}
		}
				$query = "INSERT INTO contacts2type VALUES ( NULL, '{$contactID}','4')";
				$this->db->query($query);		

	}
	
	public function getSalesContactType($contactID) {
		$query = "SELECT ct.type_id, bt.name from contacts2type ct , " . TB_BOOKMARKS_TYPE . " bt WHERE ct.contact_id = $contactID AND ct.type_id = bt.id ";
		$this->db->query($query);

		if (! $this->db->num_rows() > 0){
			throw new Exception('Permission denied');			
		}
		$arr = $this->db->fetch_all_array();
		return $arr;
	}
	
	public function getSalesContactTypeList() {
		$query = "SELECT * from " . TB_BOOKMARKS_TYPE . " WHERE name != 'all'";
		$this->db->query($query);

		if (! $this->db->num_rows() > 0){
			throw new Exception('Permission denied');			
		}
		$arr = $this->db->fetch_all_array();
		return $arr;
	}	
	
	
	public function getSalesContact($contactID,$creater_id = null  ) {
		$query = "SELECT c.* from " . TB_CONTACTS . " c ";
		//$query .= ", ct.type_id  LEFT JOIN contacts2type ct ON c.id = ct.contact_id	";
		$query .= " WHERE c.id = $contactID ";
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
		$types = $this->getSalesContactType($contactID);
		foreach ($types as $type){
			$tmp[] = $type;
		
		}
		$contact->type = $tmp;

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
                            "FROM " . TB_CONTACTS . " c, " . TB_BOOKMARKS_TYPE . " bt , contacts2type ct " .
                            "WHERE ct.type_id = bt.id AND bt.name = '".mysql_escape_string($sub)."' " .
                            "AND ct.contact_id = c.id";
				/*if(isset($creater_id)) {
					$query .= " AND c.creater_id = $creater_id";
				}*/
		if ($sub == 'all'){
			$query = "SELECT count(c.id) as 'count'  FROM " . TB_CONTACTS . " c";
		}

		$this->db->query($query);
		$r = $this->db->fetch_array(0);
		
                return $r['count'];
	}
	
	public function saveContact(SalesContact $c) {		
		
		$state_id = $c->state_id;
		if(!isset($state_id)) {
			$state_id = " NULL ";
		}
/*$web = $c->website;
if (substr($web, 0, 4) != 'http'){
	$website = 'http://'.$c->website;
}else{
	$website = $c->website;
}	*/	
		
		
		$query = "UPDATE " . TB_CONTACTS . " SET 
					company = '".mysql_real_escape_string($c->company)."',
					contact = '".mysql_real_escape_string($c->contact)."',
					phone 	= '".mysql_real_escape_string($c->phone)."',
					fax		= '".mysql_real_escape_string($c->fax)."',
					email	= '".mysql_real_escape_string($c->email)."',
					website = '".mysql_real_escape_string($c->website)."',
					title	= '".mysql_real_escape_string($c->title)."',
					government_agencies = '".mysql_real_escape_string($c->government_agencies)."',
					affiliations	= '".mysql_real_escape_string($c->affiliations)."',
					industry		= '".mysql_real_escape_string($c->industry)."',
					comments		= '".mysql_real_escape_string($c->comments)."',
					state			= '".mysql_real_escape_string($c->state)."',
					city			= '".mysql_real_escape_string($c->city)."',
					zip_code		= '".mysql_real_escape_string($c->zip_code)."',
					country_id		= '".mysql_real_escape_string($c->country_id)."',
					state_id		= $state_id,
					mail			= '".mysql_real_escape_string($c->mail)."',
					cellphone		= '".mysql_real_escape_string($c->cellphone)."',
					acc_number		= '".mysql_real_escape_string($c->acc_number)."',
					paint_supplier		= '".mysql_real_escape_string($c->paint_supplier)."',
					paint_system		= '".mysql_real_escape_string($c->paint_system)."',
					creater_id		= '".mysql_real_escape_string($c->creater_id)."'
					WHERE id = ".mysql_real_escape_string($c->id)."";
		
		
		
		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	
	public function addContact(SalesContact $c) {
		if(!$c->errors) {
/*$web = $c->website;
if (substr($web, 0, 4) != 'http'){
	$website = 'http://'.$c->website;
}else{
	$website = $c->website;
}*/
			$query = "select id from ".TB_BOOKMARKS_TYPE." where name = '".htmlentities($c->type)."' limit 1";
			$this->db->query($query);
			$typeID = '';
			if ($this->db->num_rows() > 0) {
				$typeID = $this->db->fetch(0)->id;
			}

			$query = "INSERT INTO " . TB_CONTACTS . " (company,contact,phone,fax,email,website,title,government_agencies,affiliations,industry,comments,state,city,zip_code,creater_id,acc_number,paint_supplier,paint_system,country_id,state_id,mail,cellphone,type) VALUES (
						'".mysql_real_escape_string($c->company)."', '".mysql_real_escape_string($c->contact)."', '".mysql_real_escape_string($c->phone)."', '".mysql_real_escape_string($c->fax)."', '".mysql_real_escape_string($c->email)."','".mysql_real_escape_string($c->website)."', '".mysql_real_escape_string($c->title)."', '".mysql_real_escape_string($c->government_agencies)."',  
						'".mysql_real_escape_string($c->affiliations)."','".mysql_real_escape_string($c->industry)."','".mysql_real_escape_string($c->comments)."','".mysql_real_escape_string($c->state)."','".mysql_real_escape_string($c->city)."','".mysql_real_escape_string($c->zip_code)."','".mysql_real_escape_string($c->creater_id)."','".mysql_real_escape_string($c->acc_number)."','".mysql_real_escape_string($c->paint_supplier)."','".mysql_real_escape_string($c->paint_system)."'  
						";
			
			/**
				PHP isset function will return every time false on __get magic method =(
			 */
			$country_id = $c->country_id;
			$state_id = $c->state_id;
			
			$query .= isset($country_id) ? " , ".$c->country_id : " , NULL ";
			$query .= isset($state_id) ? " , ".$c->state_id : " , NULL ";
			
			$query .= " , '".mysql_real_escape_string($c->mail)."', '".mysql_real_escape_string($c->cellphone)."' , '{$typeID}'";		

			$query .= " )";
			
			//For Debug
			//$this->db->beginTransaction(); 
			//echo $query; exit;
			$this->db->query($query);
			
			if(mysql_error() == '') {
				$contactID = $this->db->getLastInsertedID();
				$c->id = $contactID;
				
				//	save to MM table
				if ($typeID == 4 || $typeID == '') {
					$this->saveSalesContactType($contactID);
				} else {
					$this->saveSalesContactType($contactID, array($typeID));
				}
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
                
                $query = "SELECT c.id, c.company, LOCATE('".$occurrence."', c.company) occurrenceCmp, c.contact, LOCATE('".$occurrence."', c.contact) occurrenceCnt FROM ".TB_CONTACTS.
			 " c, contacts2type ct  WHERE ct.type_id = '".$subNumber."' AND ct.contact_id = c.id  
                          AND (LOCATE('".$occurrence."', c.company)>0 OR  LOCATE('".$occurrence."', c.contact)>0) 
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
	//	$query = "SELECT  * FROM ".TB_CONTACTS."  WHERE type = ".$subNumber." AND (";
		$query = "SELECT  count(c.id) contactCount FROM ".TB_CONTACTS." c WHERE ((";		
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
		
		$query = "SELECT count(c.id) contactsCount FROM ".TB_CONTACTS." c , contacts2type ct WHERE ct.contact_id = c.id AND ct.type_id = $subNumber";                
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
	public function searchContacts($contacts, $byField1, $byField2, $subNumber, Pagination $pagination = null, $sortStr = null) {
        
		$query = "SELECT  c.* FROM ".TB_CONTACTS." c , contacts2type ct  WHERE ct.type_id = ".$subNumber." AND ct.contact_id = c.id AND ((";
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
		if (isset($sortStr)) {
			$query .=  $sortStr;
		}		
                
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