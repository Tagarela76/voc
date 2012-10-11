<?php

namespace VWM\Import\Gom;

class GomUploaderEntityBuilder {
	
	/**
	 *
	 * @var array of Gom 
	 */
	private $successfullyBuildedEntities = array();
	
	/**
	 *
	 * @var array of Gom
	 */
	private $entitiesWithErrors = array();
	
	/**
	 *
	 * @var GomUploaderMapper
	 */
	private $mapper;
	
	function __construct(GomUploaderMapper $mapper) {
		
		$this->mapper = $mapper;
	}
	
	public function buildEntities($pathToCsv) {
		$csvHelper = new \VWM\Import\CsvHelper(); 
		$csvHelper->openCsvFile($pathToCsv); 
		
		//	read first two lines - they are the header
		$fileData = $csvHelper->getFileContent();
		foreach ($fileData as $data) {
			// formulate gom object
			$gom = new \VWM\Entity\Product\Gom($this->db);
			$gomName = $data[$this->mapper->mappedData['productOrPartNumbers']];
			var_dump($gomName); die();
			$gom->setName("gom-test");
			$gom->setJobberId("1");
			$gom->setVendorId("1");
			$gom->setCode("code");
			$gom->setProductInstock("2");
			$gom->setProductLimit("20");
			$gom->setProductAmount("2");
			$gom->setProductStocktype("2");
			$gom->setProductPricing("2.00");
			$gom->setPriceUnitType("1");
			$data_tmp[0] = $data[$headerKey['productID']];
			var_dump($data); die();
		}
	}
}

?>
