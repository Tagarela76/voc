<?php

namespace VWM\Label;

class CompanyLevelLabel {
	
	/**	 
	 * @var \db
	 */
	protected $db;
	
	/**	 
	 * @var int
	 */
	protected $companyId;
	
	const TABLE_NAME = 'industry_type2label';
	
	const DEFAULT_LABEL_REPAIR_ORDER = 'Repair Order';
	
	const LABEL_ID_REPAIR_ORDER = 'repair_order';

	public function __construct(\db $db, $companyId) {
		$this->db = $db;
		$this->companyId = $companyId;
	}
	
	/**	 
	 * @return string Label text
	 */
	public function getRepairOrderLabel() {
		$sql = "SELECT label_text FROM ".self::TABLE_NAME." it2l " .
				"JOIN ".TB_COMPANY2INDUSTRY_TYPE." c2it " .
					"ON c2it.industry_type_id = it2l.industry_type_id " .
				"WHERE c2it.company_id = {$this->db->sqltext($this->companyId)} " .
				"AND it2l.label_id = '".self::LABEL_ID_REPAIR_ORDER."'";				
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return self::DEFAULT_LABEL_REPAIR_ORDER;
		}
		
		return $this->db->fetch(0)->label_text;
	}
}

?>
