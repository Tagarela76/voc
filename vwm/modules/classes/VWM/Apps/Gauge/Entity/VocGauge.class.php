<?php

namespace VWM\Apps\Gauge\Entity;

class VocGauge extends Gauge {

	protected $currentUsage;

	const GAUGE_TYPE_NAME = 'VOC';
	const VOC_PRIORITY = 1;

	public function __construct(\db $db) {
		$this->db = $db;
		$this->gauge_priority = self::VOC_PRIORITY;
		$this->gauge_type = self::VOC_GAUGE;
	}

	public function getCurrentUsage() {
		$month = 'MONTH(CURRENT_DATE)';
		$year = 'YEAR(CURRENT_DATE)';
		$month=mysql_real_escape_string($month);
		$year=mysql_real_escape_string($year);
		
		if ($forDepartment) {
			$query = "SELECT sum( m.voc ) total_usage , d.voc_limit,  MONTH( FROM_UNIXTIME(m.creation_time) ) creation_month " .
				 "FROM ".TB_DEPARTMENT." d, ".TB_USAGE." m , ".TB_EQUIPMENT." e " .
				 "WHERE m.department_id = d.department_id " .
				 "AND e.equipment_id = m.equipment_id " .
				 "AND e.dept_track = 'yes' " .
				 "AND MONTH(FROM_UNIXTIME(m.creation_time)) = ".$month." " .
				 "AND YEAR(FROM_UNIXTIME(m.creation_time)) = ".$year." " .
				 "AND d.department_id = ".$this->department_id." ".
				 "GROUP BY voc_limit";
		} else {
			$query = "SELECT sum( m.voc ) total_usage , d.voc_limit, MONTH(FROM_UNIXTIME(m.creation_time)) creation_month " .
				 "FROM ".TB_DEPARTMENT." d, ".TB_USAGE." m , ".TB_EQUIPMENT." e " .
				 "WHERE m.department_id = d.department_id " .
				 "AND e.equipment_id = m.equipment_id " .
				 "AND e.facility_track = 'yes' " .
				 "AND MONTH(FROM_UNIXTIME(m.creation_time)) = ".$month." " .
				 "AND YEAR(FROM_UNIXTIME(m.creation_time)) = ".$year." " .
				 "AND d.facility_id = ".$this->facility_id." ".
				 "GROUP BY voc_limit";
		}

		$this->db->query($query);

		$numRows = $this->db->num_rows();
		$currentDate = getdate();
		if ($numRows > 0) {
			$data = $this->db->fetch(0);
			if ((int)$currentDate['mon'] == (int)$data->creation_month) {
				$this->currentUsage = (float)$data->total_usage;
			}

			return (float)$data->total_usage;
		} else {
			$this->currentUsage = 0;
			return $this->currentUsage;
		}
	}

}

?>
