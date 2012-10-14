<?php

namespace VWM\Import\Gom;

use VWM\Framework\Test as Testing;
use VWM\Entity\Crib as CribEntity;

class MapperTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_FACILITY,
		CribEntity\Crib::TABLE_NAME,
		CribEntity\Bin::TABLE_NAME,
		TB_GOM,
		TB_SUPPLIER,
	);

	public function testMapper() {

		//TODO:	oh shit....		
		$pathToCsv = "/home/developer/Desktop/example.csv";
		$mapper = new GomUploaderMapper($this->db);//TODO: Why DB?
		$mapper->doMapping($pathToCsv);
		// we should check if mappedData is array and consist 14 items
		$this->assertEquals(15, count($mapper->mappedData));
		$this->assertTrue(is_array($mapper->mappedData));

		$entityBuilder = new GomUploaderEntityBuilder($this->db, $mapper);
		$entityBuilder->buildEntities($pathToCsv);

		// i want to get entities (Gom, Crib, Bin)
		//buildEntities -> read csv file and formulate gom object
		$goms = $entityBuilder->getGoms();
		$cribs = $entityBuilder->getCribs();
		$bins = $entityBuilder->getBins();

		// we get array of entities (38 items)
		$this->assertTrue(is_array($goms));
		$this->assertEquals(38, count($goms)); 
		$this->assertInstanceOf('VWM\Entity\Product\Gom', $goms[0]);

		$this->assertTrue(is_array($cribs));
		$this->assertEquals(38, count($cribs));
		$this->assertInstanceOf('\VWM\Entity\Crib\Crib', $cribs[0]);

		$this->assertTrue(is_array($bins));
		$this->assertEquals(38, count($bins));
		$this->assertTrue(is_array($bins[0]));
		$this->assertInstanceOf('\VWM\Entity\Crib\Bin', $bins[0][0]);

		// we get entitities. Now we can validate and save it

		for ($i = 0; $i < count($goms); $i++) {
			// set id if this product already exist
			$goms[$i]->check(); $gomProductId = $goms[$i]->save(); 
			// add Gom Product
			$violationList = $goms[$i]->validate(); 
			if (count($violationList) == 0) {
				$gomProductId = $goms[$i]->save();
			} else {
				$errorString = "\n Cannot create a Gom object, row is " . $i . "\n";
				foreach ($violationList as $violation) {
					$errorString .= "\n " . $violation->getPropertyPath(). " => " . $violation->getMessage(). "\n";
				}
				$entityBuilder->addError($errorString);	
			}
			
			// set id if this crib already exist
			$cribs[$i]->check();
			// add Crib
			$violationList = $cribs[$i]->validate();
			if (count($violationList) == 0) {
				$cribId = $cribs[$i]->save();
			} else {
				$errorString = "\n Cannot create a Crib object, row is " . $i . "\n";
				foreach ($violationList as $violation) {
					$errorString .= "\n " . $violation->getPropertyPath(). " => " . $violation->getMessage(). "\n";
				}
				$entityBuilder->addError($errorString);	
			}

			// we add bin if exist crib
			if ($cribId) {
				// we can have a lot of bins , so we add bin in cycle
				foreach ($bins[$i] as $bin) {
					// maybe this bin is already exist, we must check it
					$bin->check();
					// we know where this bin exist
					$bin->setCribId = $cribId;
					$violationList = $bin->validate();
					if (count($violationList) == 0) {
						$binId = $bin->save();
					} else {
						$errorString = "\n Cannot create a Bin object, row is " . $i . "\n";
						foreach ($violationList as $violation) {
							$errorString .= "\n " . $violation->getPropertyPath(). " => " . $violation->getMessage(). "\n";
						}
						$entityBuilder->addError($errorString);
					}
					// if we add bin we must add dependency product=>bin
					if ($binId) {
						// We should add product to bin dependency
						$binContext = new \VWM\Entity\Product\BinContext($this->db);
						$binContext->setBinId($binId);
						$binContext->setProductId($gomProductId);
						$binContext->save();
					}
				}
			}
		}
		// write all in log
		$validationLogFile = fopen(DIR_PATH_LOGS."validation-new.log","a");
		fwrite($validationLogFile,$entityBuilder->errors);
		fclose($validationLogFile);
	}

}

?>
