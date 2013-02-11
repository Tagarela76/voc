<?php

namespace VWM\Import\Pfp;

class PfpUploaderMapper extends \VWM\Import\Mapper {


	public function getMap() {
		return array(
			"productId" => array('PRODUCT ID'),
			"productName" => array('PRODUCT NAME/COLOR'),
			"ratio" => array('MIX RATIO'),
			"unitType" => array('UNIT TYPE'),
			"IP" => array('IP'),
		);
	}
}

?>
