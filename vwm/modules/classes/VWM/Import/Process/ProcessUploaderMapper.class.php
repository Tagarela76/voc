<?php

namespace VWM\Import\Process;

class ProcessUploaderMapper extends \VWM\Import\Mapper {


	public function getMap() {
		return array(
			"processName" => array('Process Name'),
			"stepNumber" => array('Step'),
			"optional" => array('Optional'),
			"stepDescription" => array('Description of Step'),
			"resourceDescription" => array('Description of Work'),
			"processType" => array('Process Types'),
			"qty" => array('Qty'),
			"unitType" => array('Unit Type'),
			"rate" => array('Rate'),
			"rateUnitType" => array('Rate Unit Type'),
		);
	}
}
?>
