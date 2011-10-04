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
	
	public function createNewType($industryType, $industrySubType){
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
		$query = "SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE type = '".$industryType."' AND parent is NULL";
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$resultSubType = $this->db->fetch(0);
			var_dump($resultSubType);
			$query = "INSERT INTO ".TB_INDUSTRY_TYPE." (type, parent) VALUES ('".$industrySubType."', ".$resultSubType->id.")";
			$this->db->query($query);
			return $this->db->getLastInsertedID();
		}
		
	}
	
	public function getTypeIdByType(){
		
	}
	
	
}
?>