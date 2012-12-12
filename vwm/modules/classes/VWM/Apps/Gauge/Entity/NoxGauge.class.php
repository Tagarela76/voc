<?php

namespace VWM\Apps\Gauge\Entity;


class NoxGauge extends Gauge {


	public function __construct(\db $db) {
		$this->db = $db;
		$this->gauge_type = self::NOX_GAUGE;
	}
}

?>
