<?php

namespace VWM\Label;

class LabelManager {
	
	/**	 
	 * @var \db
	 */
	protected $db;
	
	/**
	 *
	 * @var int
	 */
	protected $industryType;
	
	const TABLE_NAME = 'industry_type2label';

	const LABEL_ID_REPAIR_ORDER = 'repair_order';
	const DEFAULT_LABEL_REPAIR_ORDER = 'Repair Order';
	
	public function __construct(\db $db, $industryType) {
		$this->db = $db;
		$this->industryType = $industryType;
	}
	
	public function getLabelList() {
		
		$sql = "SELECT * " .
				"FROM ".self::TABLE_NAME." " .
				"WHERE industry_type_id = {$this->db->sqltext($this->industryType)}";		
		$this->db->query($sql);
		
		if($this->db->num_rows() == 0) {
			// label Repair Order
			$labelOrder["label_id"] = self::LABEL_ID_REPAIR_ORDER;
			$labelOrder["label_text"] = self::DEFAULT_LABEL_REPAIR_ORDER;
			$labelList = array($labelOrder);
		} else {
			$labelList = $this->db->fetch_all_array();
		}
		
		return $labelList;
	}
	
	
	/**
	 * Save Repair Order Label Text
	 * @param string $labelText
	 */
	public function saveRepairOrderLabel($labelText) {
		$sql = "SELECT * " .
				"FROM ".self::TABLE_NAME." " .
				"WHERE industry_type_id = {$this->db->sqltext($this->industryType)} " .
				"AND label_id='" . self::LABEL_ID_REPAIR_ORDER . "'";		
		$this->db->query($sql);
		
		if($this->db->num_rows() == 0) {
			$sql = "INSERT INTO " . self::TABLE_NAME . " " .
					"(industry_type_id, label_id, label_text) " .
					"VALUES(" .
					"{$this->db->sqltext($this->industryType)}, " .
					"'" . self::LABEL_ID_REPAIR_ORDER . "', " .
					"'{$this->db->sqltext($labelText)}'" .		
					")";					   			
			$this->db->query($sql);
		} else {
			$sql = "UPDATE " . self::TABLE_NAME . " " .
					"SET label_text='{$this->db->sqltext($labelText)}' " .
					"WHERE label_id='" . self::LABEL_ID_REPAIR_ORDER . "' " .
					"AND industry_type_id={$this->db->sqltext($this->industryType)}";					   			
			$this->db->query($sql);
		}		
	}
}

?>
