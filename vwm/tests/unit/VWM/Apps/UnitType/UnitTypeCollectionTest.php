<?php

namespace VWM\Apps\UnitType\Entity;

use VWM\Framework\Test\DbTestCase;

/**
 * Unit Type Collection Entity test class
 */
class UnitTypeCollectionTest extends DbTestCase {

	public function testLoad() {
		$utc = new UnitTypeCollection($this->db);
	}
}

?>
