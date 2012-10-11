<?php

namespace VWM\Import\Gom;

use VWM\Framework\Test as Testing;

class MapperTest extends Testing\DbTestCase {
			
	protected $fixtures = array(

	);
	
	
	public function testMapper() {		
		
		$pathToCsv = "/home/developer/Desktop/example.csv";
		$mapper = new GomUploaderMapper($this->db);
		$mapper->doMapping($pathToCsv);
		// we should check if mappedData is array and consist 14 items
		$this->assertEquals(14, count($mapper->mappedData));
		$this->assertTrue(is_array($mapper->mappedData));

		$entityBuilder = new GomUploaderEntityBuilder($mapper);
		$entityBuilder->buildEntities($pathToCsv);
		
		// i want to get gom entity
		//buildEntities -> read csv file and formulate gom object (two flows - successfully and with errors)
		// i get instance of GomUploaderMapper , 7 successfully builded objects
		$goms = $entityBuilder->getSuccessfullyBuildedEntities();
		foreach ($goms as $gom) {
			if ($gom->validate()) {
				$gom->save();
			}
		}
		$this->assertInstanceOf('\VWM\Import\Gom\GomUploaderMapper', $goms[0]);
		$this->assertEquals(7, count($goms));

		// I get 1 gom entity with error
		$errors = $entityBuilder->getErrors();
		$this->assertInstanceOf('\VWM\Import\Gom\GomUploaderMapper', $errors[0]);
		$this->assertEquals(1, count($errors));
		
					
	}	
	
}
?>
