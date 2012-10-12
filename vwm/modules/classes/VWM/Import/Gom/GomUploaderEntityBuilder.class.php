<?php

namespace VWM\Import\Gom;

class GomUploaderEntityBuilder {
	
	/**
	 *
	 * @var array of Gom 
	 */
	private $goms = array();
	
	/**
	 *
	 * @var GomUploaderMapper
	 */
	private $mapper;
	
	/**
	 *
	 * @var array of Crib
	 */
	private $cribs = array();
	
	/**
	 *
	 * @var array of Bin
	 */
	private $bins = array();
	
	/*
	 * errors
	 */
	public $errors;

	private $db;
	
	public function getGoms() {
		return $this->goms;
	}

	public function setGoms($goms) {
		$this->goms = $goms;
	}

	public function getCribs() {
		return $this->cribs;
	}

	public function setCribs($cribs) {
		$this->cribs = $cribs;
	}

	public function getBins() {
		return $this->bins;
	}

	public function setBins($bins) {
		$this->bins = $bins;
	}

	function __construct(\db $db, GomUploaderMapper $mapper) {
		$this->db = $db;
		$this->mapper = $mapper;
	}
	
	public function buildEntities($pathToCsv) {
		$csvHelper = new \VWM\Import\CsvHelper(); 
		$csvHelper->openCsvFile($pathToCsv); 

		$fileData = $csvHelper->getFileContent();
		$goms = array();
		$cribs = array();
		$bins = array();
		foreach ($fileData as $data) {
			// formulate gom object
			$gom = new \VWM\Entity\Product\Gom($this->db);
			$gomName = $data[$this->mapper->mappedData['itemAssignedTORobocribBin'][0]];
			$productNr = $data[$this->mapper->mappedData['productOrPartNumbers'][0]];
			$supplierName = $data[$this->mapper->mappedData['manufacturerOrSupplier'][0]];
			$productPricing = $data[$this->mapper->mappedData['productPricing'][0]];
			$addToxicCompounds = $data[$this->mapper->mappedData['addToxicCompounds'][0]];
			$msdsSheet = $data[$this->mapper->mappedData['msdsSheet'][0]];
			$numberOfPartsPerPackage = $data[$this->mapper->mappedData['numberOfPartsPerPackage'][0]];
			
			// supplier Name to supplier Id
			$supplier = new \Supplier($this->db);
			$supplierId = $supplier->getSupplierIdByName($supplierName);
			
			// product Pricing can be as string
			// so we must check it
			$productPricing = str_replace("$","",$productPricing);
			// create GOM object
			$gom->setName($gomName);
			$gom->setProductNr($productNr);
			$gom->setSupplierId($supplierId);
			$gom->setProductPricing($productPricing);
			$gom->setAddToxicCompounds($addToxicCompounds);
			$gom->setMsdsHheet($msdsSheet);
			$gom->setPackageSize($numberOfPartsPerPackage);

			$goms[] = $gom;
			// create CRIB object
			$crib = new \VWM\Entity\Crib\Crib($this->db);
			$serialNumber = $data[$this->mapper->mappedData['gyantCribUnitId'][0]];
			$crib->setSerialNumber($serialNumber);
			$crib->setFacilityId('1'); 
			
			$cribs[] = $crib;
			// create BIN object
			$oneCribBins = array();
			foreach($this->mapper->mappedData['itemLocation'] as $binIndex) {
				$bin = new \VWM\Entity\Crib\Bin($this->db);
				$name = $data[$binIndex];
				$size = $data[$this->mapper->mappedData['binsSizes'][0]];
				$capacity = $data[$this->mapper->mappedData['maximumPackagesPerBin'][0]];
			//	$bin->setNumber('3');
				$bin->setCapacity($capacity);
				$bin->setSize($size);
			//	$bin->setType('3');
				$bin->setName($name); 
				
				$oneCribBins[] = $bin;
			}
			$bins[] = $oneCribBins;
		}
		// set our entities
		$this->setGoms($goms);
		$this->setCribs($cribs);
		$this->setBins($bins);
	}
	
	public function addError($errorString) {
		$this->errors .= $errorString;		
	}
}

?>
