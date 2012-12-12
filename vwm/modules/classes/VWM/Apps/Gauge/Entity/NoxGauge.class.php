<?php

namespace VWM\Apps\Gauge\Entity;


class NoxGauge extends Gauge {

	protected $currentUsage;

	public function __construct(\db $db) {
		$this->db = $db;
		$this->gauge_type = self::NOX_GAUGE;
	}

	public function getCurrentUsage() {
		if($this->currentUsage !== null) {
			return $this->currentUsage;
		}

		$noxManager = new \NoxEmissionManager($this->db);
		if ($this->getDepartmentId()) {
			$totalSumNox = $noxManager->getCurrentUsageOptimizedByDepartment(
					$this->getDepartmentId(),  "department");
		} else {
			$totalSumNox = $noxManager->getCurrentUsageOptimizedByDepartment(
					$this->getFacilityId(),  "facility");
		}

		$this->currentUsage = $totalSumNox;
		return $this->currentUsage;
	}
}

?>
