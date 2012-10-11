<?php

class GomBulkUploaderMapper extends \VWM\Import\Mapper{
	
	// add possible headers here
	public $manufacturerOrSupplier = array ('MANUFACTURER/SUPPLIER');
	public $productOrPartNumbers = array ('PRODUCT/PART NUMBERS');
	public $gyantCribUnitId = array ('GYANTCRIB UNIT ID');
	public $gyantCribNumbers = array ('THIS GYANTCRIB NUMBERS');
	public $itemAssignedTORobocribBin = array ('ITEM ASSIGNED TO ROBOCRIB BIN(S)');
	public $qtyOfBinsAssignToItemActual = array ('QTY OF BINS ASSIGNED TO ITEM ACTUAL');
	public $clientEstimatedPArts = array('CLIENT ESTIMATED PARTS');
	public $binsSizes = array('BINS SIZES.PIE. (1, 2, 3, 4, 6, 12, 1E, 2E)');
	public $numberOfPartsPerPackage = array('NUMBER OF PARTS PER PACKAGE');
	public $maximumPackagesPerBin = array('MAXIMUM  PACKAGES  PER Bin');
	public $maximumPArtsInCribUnit = array('MAXIMUM PARTS IN CRIB UNIT');
	public $gyantCribDefaultReorder = array('GYANT CRIB DEFAULT REORDER');
	public $productPricing = array('PRODUCT PRICING');
	public $addToxicCompounds = array('ADD TOXIC COMPOUNDS');
	public $msdsSheet = array('MSDS SHEET');

	private $db;
	
	function __construct(DB $db) {
		
		$this->db = $db;
	}
	
	public function getMap() {
		return array(
			"manufacturerOrSupplier" => $this->manufacturerOrSupplier,
			"productOrPartNumbers" => $this->productOrPartNumbers,
			"gyantCribUnitId" => $this->gyantCribUnitId,
			"gyantCribNumbers" => $this->gyantCribNumbers,
			"itemAssignedTORobocribBin" => $this->itemAssignedTORobocribBin,
			"qtyOfBinsAssignToItemActual" => $this->qtyOfBinsAssignToItemActual,
			"clientEstimatedPArts" => $this->clientEstimatedPArts,
			"binsSizes" => $this->binsSizes ,
			"numberOfPartsPerPackage" => $this->numberOfPartsPerPackage ,
			"maximumPackagesPerBin" => $this->maximumPackagesPerBin ,
			"maximumPArtsInCribUnit" => $this->maximumPArtsInCribUnit ,
			"gyantCribDefaultReorder" => $this->gyantCribDefaultReorder ,
			"productPricing" => $this->productPricing,
			"addToxicCompounds" => $this->addToxicCompounds,
			"msdsSheet" => $this->msdsSheet,
		);
	}
}
?>
