<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;

class Company extends Model {
    
    protected $company_id;
    
    protected $name;
    
    protected $address;
    
    protected $city;
    
    protected $zip;
    
    protected $county;
    
    protected $state;
    
    protected $phone;
    
    protected $fax;
    
    protected $email;
    
    protected $contact;
    
    protected $title;
    
    protected $creater_id;
    
    protected $country;
    
    protected $gcg_id;
    
    protected $creation_date;
    
    protected $voc_unittype_id;
    
    protected $date_format_id;
    
    protected $last_update_time;
    
    protected $industryType;

	const TABLE_NAME = 'company';
    
    public function getCompanyId() {
        return $this->company_id;
    }
	
	/*
	 * name of unit_class for getUnitTypeList function
	 * USAWght for default
	 * @var string 
	 */
	protected $unitTypeClass = 'USAWght';
	
	const TB_UNITTYPE = 'unittype';
	const TB_DEFAULT = '`default`';
	const TB_TYPE = 'type';
	const TB_UNITCLASS = 'unit_class';
	const CATEGORY = 'company';
	
	
	/**	 
	 * @return \IndustryType
	 * @throws \Exception
	 */
    public function getIndustryType() {
        if(!$this->getCompanyId()) {
			throw new \Exception('Company ID should be set before calling this method');
		}
        if (!$this->industryType) {
            $industryTypes = $this->getIndustryTypes(); 
            return $industryTypes[0];
        } else {
            return $this->industryType;
        }
    }

    public function setCompanyId($companyId) {
        $this->company_id = $companyId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getZip() {
        return $this->zip;
    }

    public function setZip($zip) {
        $this->zip = $zip;
    }

    public function getCounty() {
        return $this->county;
    }

    public function setCounty($county) {
        $this->county = $county;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function getFax() {
        return $this->fax;
    }

    public function setFax($fax) {
        $this->fax = $fax;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getContact() {
        return $this->contact;
    }

    public function setContact($contact) {
        $this->contact = $contact;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getCreaterId() {
        return $this->creater_id;
    }

    public function setCreaterId($createrId) {
        $this->creater_id = $createrId;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function getGcgId() {
        return $this->gcg_id;
    }

    public function setGcgId($gcgId) {
        $this->gcg_id = $gcgId;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function setCreationDate($creationDate) {
        $this->creation_date = $creationDate;
    }

    public function getVocUnittypeId() {
        return $this->voc_unittype_id;
    }

    public function setVocUnittypeId($vocUnittypeId) {
        $this->voc_unittype_id = $vocUnittypeId;
    }

    public function getDateFormatId() {
        return $this->date_format_id;
    }

    public function setDateFormatId($dateFormatId) {
        $this->date_format_id = $dateFormatId;
    }

    public function getLastUpdateTime() {
        return $this->last_update_time;
    }

    public function setLastUpdateTime($lastUpdateTime) {
        $this->last_update_time = $lastUpdateTime;
    }
	
	public function getUnitTypeClass() {
		return $this->unitTypeClass;
	}

	public function setUnitTypeClass($unitTypeClass) {
		$this->unitTypeClass = $unitTypeClass;
	}

    public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = "Company";
		
		if($id !== null) {
			$this->setCompanyId($id);
			if(!$this->_load()) {
				throw new \Exception('404');
			}
		}
	}
    
    private function _load() {
		if(!$this->getCompanyId()) {
			throw new \Exception('Company ID should be set before calling this method');
		}
		
		$sql = "SELECT * FROM ".TB_COMPANY." " .
				"WHERE company_id = {$this->db->sqltext($this->getCompanyId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		
		return true;
	}
    
    /**
     * Insert company to data base| update company data in data base
     * @return int
     */
    public function save() {		

		if($this->getCompanyId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
    
    /**
     * Insert company to data base
     * @return int
     */
    protected function _insert() {
					
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
		
		$sql = "INSERT INTO ".TB_COMPANY." (" .
				"name, address, city, zip, county, state, " .
				"phone, fax, email, contact, title, creater_id, " .
				"country, gcg_id, creation_date, voc_unittype_id, " .
				"date_format_id, last_update_time " .
				") VALUES ( ".
				"'{$this->db->sqltext($this->getName())}', " .
				"'{$this->db->sqltext($this->getAddress())}', " .
				"'{$this->db->sqltext($this->getCity())}', " .
				"'{$this->db->sqltext($this->getZip())}', " .
				"'{$this->db->sqltext($this->getCounty())}', " .
				"'{$this->db->sqltext($this->getState())}', " .
				"'{$this->db->sqltext($this->getPhone())}', " .
				"'{$this->db->sqltext($this->getFax())}', " .
				"'{$this->db->sqltext($this->getEmail())}', " .
				"'{$this->db->sqltext($this->getContact())}', " .
				"'{$this->db->sqltext($this->getTitle())}', " .
				"{$this->db->sqltext($this->getCreaterId())}, " .
				"{$this->db->sqltext($this->getCountry())}, " .
				"{$this->db->sqltext($this->getGcgId())}, " .
				"'{$this->db->sqltext($this->getCreationDate())}', " .
				"{$this->db->sqltext($this->getVocUnittypeId())}, " .
				"{$this->db->sqltext($this->getDateFormatId())}, " .
				"{$lastUpdateTime} " .
				")"; 
		$result = $this->db->exec($sql);
		if($result) {
			$this->setCompanyId($this->db->getLastInsertedID());	
			return $this->getCompanyId();
		} else {
			return false;
		}
		
		
	}
    
    /**
     * Update company
     * @return int
     */
    protected function _update() {
					
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".TB_COMPANY." SET " .
				"name='{$this->db->sqltext($this->getName())}', " .
				"address='{$this->db->sqltext($this->getAddress())}', " .
				"city='{$this->db->sqltext($this->getCity())}', " .
				"zip='{$this->db->sqltext($this->getZip())}', " .
				"county='{$this->db->sqltext($this->getCounty())}', " .
				"state='{$this->db->sqltext($this->getState())}', " .
				"country='{$this->db->sqltext($this->getCountry())}', " .
				"phone='{$this->db->sqltext($this->getPhone())}', " .
				"fax='{$this->db->sqltext($this->getFax())}', " .
				"email='{$this->db->sqltext($this->getEmail())}', " .
				"contact='{$this->db->sqltext($this->getContact())}', " .
				"title='{$this->db->sqltext($this->getTitle())}', " .
				"creater_id={$this->db->sqltext($this->getCreaterId())}, "	.
				"country={$this->db->sqltext($this->getCountry())}, "	.
                "gcg_id={$this->db->sqltext($this->getGcgId())}, " .
				"creation_date='{$this->db->sqltext($this->getCreationDate())}', " .
				"voc_unittype_id={$this->db->sqltext($this->getVocUnittypeId())}, "	.
				"date_format_id={$this->db->sqltext($this->getDateFormatId())}, "	.
				"last_update_time={$lastUpdateTime} " .
				"WHERE company_id={$this->db->sqltext($this->getCompanyId())}";	
		
		$result = $this->db->exec($sql);
		if($result) {			
			return $this->getCompanyId();
		} else {
			return false;
		}
	}
    
    public function getIndustryTypes() {
        
        if(!$this->getCompanyId()) {
			throw new \Exception('Company ID should be set before calling this method');
		}
        
		$sql = "SELECT * " . 
               " FROM " . TB_COMPANY2INDUSTRY_TYPE .
			   " WHERE company_id={$this->db->sqltext($this->getCompanyId())}"; 
		$this->db->query($sql);
        if($this->db->num_rows() == 0) {
			$industryType = new \IndustryType($this->db, 3); // default value (industrial)
            $industryTypes[] = $industryType;
            return $industryTypes;
		}
		$rows = $this->db->fetch_all_array();
        $industryTypes = array();
		foreach ($rows as $row) {
			$industryType = new \IndustryType($this->db, $row["industry_type_id"]);
			$industryTypes[] = $industryType;
		}
		return $industryTypes;
	}
	
	public function getUnitTypeList() {
		
		$unitTypeCollection = new \VWM\Apps\UnitType\UnitTypeCollection();
		$unitTypesName = array();
		$unitTypes = array();
		
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.system, uc.name " .
				 "FROM " . self::TB_UNITTYPE ." ut ". 
				 "INNER JOIN " . self::TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . self::TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . self::TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = '" .self::CATEGORY."' ".
				 "AND def.id_of_object = {$this->db->sqltext($this->getCompanyId())} ".
				 "AND def.subject = 'unittype' ".
				 "ORDER BY ut.unittype_id";
		
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unittype = new \VWM\Apps\UnitType\Entity\UnitType();
				$unittype->initByArray($data);
				$unittypes[] = $unittype;
			}
		} else {
			return false;
		}

		foreach($unittypes as $unitType){
				$type = array(
				'unittype_id' => $unitType->getUnitTypeId(),
				'type_id' => $unitType->getTypeId(),
				'name' => $unitType->getName()
				);
				$unitTypes[] = $type;
				if(!in_array($unitType->getName(), $unitTypesName)){
					$unitTypesName[] = $unitType->getName();
				}
		}
		$unitTypeCollection->setUnitTypeClases($unittypes);
		$unitTypeCollection->setUnitTypes($unitTypes);
		$unitTypeCollection->setUnitTypeNames($unitTypesName);
		
		return $unitTypeCollection;
	}
	
	public function getDefaultAPMethod(){
		
		$query ="SELECT apm.apmethod_id, apm.apmethod_desc"; 
		$query.=" FROM ".TB_DEFAULT." def, ".TB_APMETHOD." apm WHERE def.id_of_object={$this->db->sqltext($this->getCompanyId())}";
		$query.= " AND apm.apmethod_id=def.id_of_subject";
		$query.=" AND def.subject='apmethod'";
		$query.=" AND def.object='" .self::CATEGORY."'";
		
		$this->db->query($query);
		if ($this->db->num_rows()) {
			for ($j=0; $j < $this->db->num_rows(); $j++) {
				$data=$this->db->fetch($j);				
				$apmethod=array (
					'apmethod_id'			=>	$data->apmethod_id,
					'description'			=>	$data->apmethod_desc
				);	
				$apmethods[]=$apmethod;				
			}
		}else{
			return false;
		} 
		
		return $apmethods;
	}
	
	public function getOldUnitTypeList() {
		
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.system " .
				 "FROM " . self::TB_UNITTYPE ." ut ". 
				 "INNER JOIN " . self::TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . self::TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . self::TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = '" .self::CATEGORY."' ".
				 "AND def.id_of_object = {$this->db->sqltext($this->getCompanyId())} ".
				 "AND uc.name = '{$this->db->sqltext($this->getUnitTypeClass())}' ".
				 "AND def.subject = 'unittype' ".
				 "ORDER BY ut.unittype_id";
		
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$unittype = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system
				);
				
				$unittypes[] = $unittype;
			}
		} else {
			return false;
		}

		return $unittypes;
	}
    
}

?>
