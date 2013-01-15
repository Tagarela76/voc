<?php

namespace VWM\Apps\Gauge\Entity;


class NoxGauge extends Gauge {

	protected $currentUsage;
	const GAUGE_TYPE_NAME = 'NOx';
	const NOX_PRIORITY = 0;

	public function __construct(\db $db) {
		$this->db = $db;
		$this->gauge_priority = self::NOX_PRIORITY;
		$this->gauge_type = self::NOX_GAUGE;
	}

	public function getCurrentUsage() {
		if ($this->currentUsage !== null) {
			return $this->currentUsage;
		}

		$noxManager = new \NoxEmissionManager($this->db);
		if ($this->period == 1) {
			if ($this->getDepartmentId()) {
				$totalSumNox = $noxManager->getNoxCurrentAnnuallyUsage(
						$this->getDepartmentId(), "department");
			} else {
				$totalSumNox = $noxManager->getNoxCurrentAnnuallyUsage(
						$this->getFacilityId(), "facility");
			}
		} else {
			if ($this->getDepartmentId()) {
				$totalSumNox = $noxManager->getNoxCurrentMonthlyUsage(
						$this->getDepartmentId(), "department");
			} else {
				$totalSumNox = $noxManager->getNoxCurrentMonthlyUsage(
						$this->getFacilityId(), "facility");
			}
		}
		$this->currentUsage = round($totalSumNox, 2);
		return $this->currentUsage;
	}
}

?>
