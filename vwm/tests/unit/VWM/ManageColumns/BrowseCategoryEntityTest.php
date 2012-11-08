<?php

namespace VWM\ManageColumns;

use VWM\Framework\Test\DbTestCase;
	

class BrowseCategoryEntityTest extends DbTestCase {
	
	public $fixtures = array(
		TB_BROWSE_CATEGORY_ENTITY 
	);


	public function testGetBrowseCategoryMix() {
		$browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$entityList = $browseCategoryEntity->getBrowseCategoryMix(); 

		$this->assertTrue(count($entityList) == 1);
		$this->assertTrue($entityList->name == "browse_category_mix");
	}
}

?>
