<?php

namespace VWM\Apps\UnitType\Entity;

/**
 * Unit Type Collection Entity class
 */
class UnitTypeCollection {

	/**
	 * @var UnitType[]
	 */
	protected $unitTypes = array();

	protected $unitTypeClases = array();

	public function getUnitTypes() {
		return $this->unitTypes;
	}

	public function setUnitTypes($unitTypes) {
		$this->unitTypes = $unitTypes;
	}

	public function getUnitTypeClases() {
		return $this->unitTypeClases;
	}

	public function setUnitTypeClases($unitTypeClases) {
		$this->unitTypeClases = $unitTypeClases;
	}


}

?>
