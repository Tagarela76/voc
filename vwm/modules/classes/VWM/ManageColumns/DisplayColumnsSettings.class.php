<?php

namespace VWM\ManageColumns;

use \VWM\Framework\Model;

class DisplayColumnsSettings extends Model {
	
	/**	 
	 * @var \db
	 */
	protected $db;
	
	/**
	 *
	 * @var int
	 */
	protected $id;
	/**	 
	 * @var int
	 */
	protected $browse_category_entity_id;
	
	/**
	 *
	 * @var string
	 */
	protected $value;
	
	/**
	 *
	 * @var int
	 */
	protected $industry_type_id;
	
	/**
	 *
	 * @var datetime
	 */
	protected $last_update_time;
	
	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}
	
	public function getBrowseCategoryEntityId() {
		return $this->browse_category_entity_id;
	}

	public function setBrowseCategoryEntityId($browseCategoryEntityId) {
		$this->browse_category_entity_id = $browseCategoryEntityId;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getIndustryTypeId() {
		return $this->industry_type_id;
	}

	public function setIndustryTypeId($industryTypeId) {
		$this->industry_type_id = $industryTypeId;
	}
	
	public function __construct(\db $db, $industryTypeId = null) {
		$this->db = $db;
		if (!is_null($industryTypeId)) {
			$this->setIndustryTypeId($industryTypeId);
		}		
	}
	
	/**
	 * Insert or Update
	 * @return int
	 */
	public function save() {
		if (is_null($this->getId())) {
			$result = $this->_insert();
		} else {
			$result = $this->_update();
		}
		return $result;
	}

	/**
	 * Insert
	 * @return boolean
	 */
	private function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "INSERT INTO ".TB_DISPLAY_COLUMNS_SETTINGS." (" .
				"browse_category_entity_id, value, industry_type_id, last_update_time" .
				") VALUES ( ".
				"{$this->db->sqltext($this->getBrowseCategoryEntityId())}, " .
				"'{$this->db->sqltext($this->getValue())}', " .
				"{$this->db->sqltext($this->getIndustryTypeId())}, " .
				"{$lastUpdateTime} " .
				")"; 
		$response = $this->db->exec($sql);
		if($response) {
			$this->setId($this->db->getLastInsertedID());	
			return $this->getId();
		} else {
			return false;
		}			
	}
	
	/**
	 * Update event
	 * @return boolean
	 */
	private function _update() {				
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".TB_DISPLAY_COLUMNS_SETTINGS." SET " .
				"browse_category_entity_id={$this->db->sqltext($this->getBrowseCategoryEntityId())}, " .
				"value='{$this->db->sqltext($this->getValue())}', " .
				"industry_type_id={$this->db->sqltext($this->getIndustryTypeId())}, " .			
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";	
	
		$response = $this->db->exec($sql);
		if($response) {			
			return $this->getId();
		} else {
			return false;
		}
	}		
	
	/**
	 * 
	 * @param string $browseCategoryEntityName
	 * @return boolean|\VWM\ManageColumns\DisplayColumnsSettings
	 */
	public function getDisplayColumnsSettings($browseCategoryEntityName) {
		
		$sql = "SELECT cs.* FROM " . TB_DISPLAY_COLUMNS_SETTINGS . " cs " .
			   "JOIN " . TB_BROWSE_CATEGORY_ENTITY . " be " . 
			   "ON cs.browse_category_entity_id= be.id " .
			   "WHERE be.name='{$this->db->sqltext($browseCategoryEntityName)}' " .
			   "AND cs.industry_type_id={$this->db->sqltext($this->getIndustryTypeId())}"; 
 		$this->db->query($sql); 
		$row = $this->db->fetch(0); 
		if ($this->db->num_rows() == 0) {
			return false;
		}
		
		$displayColumnsSettings = new DisplayColumnsSettings($this->db);
		$displayColumnsSettings->setId($row->id);
		$displayColumnsSettings->setBrowseCategoryEntityId($row->browse_category_entity_id);
		$displayColumnsSettings->setIndustryTypeId($row->industry_type_id);
		$displayColumnsSettings->setValue($row->value);
		
		return $displayColumnsSettings;
	}
}

?>
