<?php
class ProductTypes {
	
	/**
     *
     * @var db
     */
    private $db;
	
	private $industryType;
	private $industrySubType;
	
	public function __construct(db $db) {
        $this->db = $db;
    }
	
	public function createNewType($industryType, $industrySubType = ''){
		$this->industryType = $industryType;
		$this->industrySubType = $industrySubType;
		$query = "INSERT INTO ".TB_INDUSTRY_TYPE." (type, parent) VALUES ('".$industryType."', NULL)";
		$this->db->query($query);
		if ($industrySubType !== ''){
			return $this->createNewSubType($industryType, $industrySubType);
		} else {
			return $this->db->getLastInsertedID();
		}
	}
	
	public function createNewSubType($industryType, $industrySubType){
		$this->industryType = $industryType;
		$this->industrySubType = $industrySubType;
		$query = "SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE UCASE(type) = UCASE('".$industryType."') AND parent is NULL";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$resultSubType = $this->db->fetch(0);
			$query = "INSERT INTO ".TB_INDUSTRY_TYPE." (type, parent) VALUES ('".$industrySubType."', ".$resultSubType->id.")";
			$this->db->query($query);
			return $this->db->getLastInsertedID();
		}
		
	}
	
	public function getAllTypes(){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE parent is NULL";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$allTypes = $this->db->fetch_all_array();
			
			return $allTypes;
		}
	}
	
	public function getAllSubTypes(){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE parent is not NULL ORDER BY parent";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$allSubTypes = $this->db->fetch_all_array();
			$allTypes = $this->getAllTypes();
			for ($i=0; $i<count($allSubTypes); $i++){
				for ($j=0; $j<count($allTypes); $j++){
					if ($allSubTypes[$i]['parent'] == $allTypes[$j]['id']){
						$allSubTypes[$i]['parentType'] = $allTypes[$j]['type'];
					}
				}
			}
			
		return $allSubTypes;
		}
	}


	public function getTypeAndSubTypeByProductID($productID){
		$query = "SELECT it.type, it.parent FROM ".TB_INDUSTRY_TYPE." it, ".TB_PRODUCT2TYPE." p2t ".
				 " WHERE p2t.product_id = ".$productID." AND p2t.type_id = it.id AND it.parent is not NULL";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$result = $this->db->fetch_all_array();
			$i = 0;
			foreach ($result as $key){
				$productType[$i]['industrySubType'] = $key['type'];
				$query = "SELECT type FROM ".TB_INDUSTRY_TYPE.
						 " WHERE id = ".$key['parent']." AND parent is NULL";
				$this->db->query($query);
				$resultType = $this->db->fetch_array(0);
				$productType[$i]['industryType'] = $resultType['type'];
				$i++;
			}
		}
		
		return $productType;
	}
	
	public function getTypeDetails($typeID){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE.
				 " WHERE id = ".$typeID;
		$this->db->query($query);
		$result = $this->db->fetch_array(0);
		
		return $result;
	}
	
	public function getSubTypeDetails($subTypeID){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE id = ".$subTypeID." AND parent is not NULL";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$allSubTypes = $this->db->fetch_all_array();
			$allTypes = $this->getAllTypes();
			for ($i=0; $i<count($allSubTypes); $i++){
				for ($j=0; $j<count($allTypes); $j++){
					if ($allSubTypes[$i]['parent'] == $allTypes[$j]['id']){
						$allSubTypes[$i]['parentType'] = $allTypes[$j]['type'];
					}
				}
			}
			
		return $allSubTypes;
		}
	}

	public function validateBeforeSaveType($data){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE.
				 " WHERE UCASE(type) = UCASE('".$data['industryType_desc']."') AND parent is NULL";
		$this->db->query($query);
		if (($this->db->num_rows() > 0) || ($data['industryType_desc'] == '')){
			$valid['summary'] = 'false';
			$valid['industryType_desc'] = 'alredyExist';
		} else {
			$valid['summary'] = 'true';
		}
		
		return $valid;
	}
	
	public function validateBeforeSaveSubType($data){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE.
				 " WHERE UCASE(type) = UCASE('".$data['industrySubType_desc']."') AND parent = ".$data['industrySubType_parentID'];
		$this->db->query($query);
		if (($this->db->num_rows() > 0) || ($data['industrySubType_desc'] == '')){
			$valid['summary'] = 'false';
			$valid['industrySubType_desc'] = 'alredyExist';
		} else {
			$valid['summary'] = 'true';
		}
		
		return $valid;
	}
	
	public function setType($data){
		$query = "UPDATE ".TB_INDUSTRY_TYPE.
				 " SET type = '".$data['industryType_desc']."', parent = NULL ".
				 " WHERE id = ".$data['industryType_id'];
		$this->db->query($query);
	}
	
	public function setSubType($data){
		$query = "UPDATE ".TB_INDUSTRY_TYPE.
				 " SET type = '".$data['industrySubType_desc']."', parent = ".$data['industrySubType_parentID'].
				 " WHERE id = ".$data['industrySubType_id'];
		$this->db->query($query);
	}


	public function deleteType($industryTypeID){
		$this->db->query("DELETE FROM ".TB_INDUSTRY_TYPE." WHERE id = ".$industryTypeID);
		$this->db->query("DELETE FROM ".TB_INDUSTRY_TYPE." WHERE parent = ".$industryTypeID);
	}
	
	public function deleteSubType($industrySubTypeID){
		$this->db->query("DELETE FROM ".TB_INDUSTRY_TYPE." WHERE id = ".$industrySubTypeID);
	}

	public function getSubTypesByTypeID($typeID){
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE parent = ".$typeID;
		$this->db->query($query);
		$result = $this->db->fetch_all_array();
		
		return $result;
	}
}
?>