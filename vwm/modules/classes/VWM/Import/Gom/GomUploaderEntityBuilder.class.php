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
			$productNr = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$jobberId = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$supplierId = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$code = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$productInstock = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$productLimit = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$productAmount = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$productStocktype = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$productPricing = $data[$this->mapper->mappedData['productOrPartNumbers']];
			$priceUnitType = $data[$this->mapper->mappedData['productOrPartNumbers']];
			
			var_dump($gomName); die();
			$gom->setName($gomName);
			$gom->setProductNr($productNr);
			$gom->setJobberId($jobberId);
			$gom->setSupplierId($supplierId);
			$gom->setCode($code);
			$gom->setProductInstock($productInstock);
			$gom->setProductLimit($productLimit);
			$gom->setProductAmount($productAmount);
			$gom->setProductStocktype($productStocktype);
			$gom->setProductPricing($productPricing);
			$gom->setPriceUnitType($priceUnitType);

			var_dump($data); die();
		}
	}
}

?>
