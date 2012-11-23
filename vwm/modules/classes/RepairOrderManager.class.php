<?php

class RepairOrderManager {

	function __construct(db $db) {
		$this->db = $db;
	}
	
    /**
     * Add department, in which displays wo
     * @param int $woId
     * @param int $departmentId
     */
	public function setDepartmentToWo($woId, $departmentId) {
		
		$query = "INSERT INTO " . TB_WO2DEPARTMENT . " (wo_id, department_id) VALUES ( " .
				"{$this->db->sqltext($woId)}, " .
				"{$this->db->sqltext($departmentId)}" . 	
				")";

		$this->db->query($query);
	}
    
    /**
     * Delete all wo dependences 
     * @param int $woId
     */
	public function unSetDepartmentToWo($woId) {
		
		$query = "DELETE " .
                 " FROM " . TB_WO2DEPARTMENT .
                 " WHERE wo_id={$this->db->sqltext($woId)}";

		$this->db->query($query);
	}
    
    public function getDepartmentsByWo($woId) {
		
		$query = "SELECT  department_id" .
                 " FROM " . TB_WO2DEPARTMENT .
                 " WHERE wo_id={$this->db->sqltext($woId)}";
		$this->db->query($query);
        if($this->db->num_rows() == 0) {
			return false;
		} else {
            $departmetIds = array();
            $rows = $this->db->fetch_all_array();
            foreach ($rows as $row) {
                $departmetIds[] = $row["department_id"];
            }
			return $departmetIds;
		}
	}
}

?>