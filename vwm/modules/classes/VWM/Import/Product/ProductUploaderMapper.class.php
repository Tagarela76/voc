<?php

namespace VWM\Import\Product;

class ProductUploaderMapper extends \VWM\Import\Mapper{
	
	// add possible headers here
	
	public $productID = array ('PRODUCT ID','PRODUCTID','PRODUCT_ID', 'PRODUCT', 'ID');
	public $mfg = array ('MFG','MANUFACTURER','SUPPLIER','PRODUCER');
	public $productName = array ('PRODUCT NAME/COLOR','PRODUCT NAME','COLOR','PRODUCTNAME/COLOR','PRODUCT_NAME/COLOR');
	public $type = array('COATING','COAT','TYPE');
	public $scoating = array('SPECIALTY COATING','SPECIALTY_COATING','COATING (SPECIALTY)','COATING SPECIALTY', 'SPECIALTY', 'COATING');
	public $aerosol = array('AEROSOL','AIROSOL');
	public $substrate = array('SUBSTRATE','SUB STRATE','SUB_STRATE', 'SUB', 'STRATE');
	public $rule = array('RULE','RULES');
	public $vocwx = array('VOCWX','MATERIAL VOC','MATERIAL_VOC','MATERIAL VOCWX','VOCWX MATERIAL', 'MATERIAL', 'VOC');
	public $voclx = array('VOCLX','COATING VOC','COATING_VOC','COATING VOCLX','VOCLX COATING', 'COATING', 'VOC');
	public $case = array('CASE NUMBER','CAS NUMBER','CASE_NUMBER','NUMBER CASE','NUMBER CAS', 'CASE', 'NUMBER', 'CAS');
	public $description = array('DESCRIPTION','DESC');
	public $mmhg = array('MMHG','MM/HG','MM\\HG');
	public $temp = array('TEMP','TMP','TEMPERATURE');
	public $weightFrom = array('WEIGHT','WEIGHT %', 'WEIGHT,%','WEIGHT, %', 'FROM');
	public $weightTo = array('WEIGHT','WEIGHT %', 'WEIGHT,%','WEIGHT, %', 'TO');
	public $gavity = array('GAVITY');
	public $density = array('DENSITY','DENSITY LBS/GAL','DENSITY, LBS/GAL','DENSITY LBS/GAL US','US DENSITY LBS/GAL');
	public $vocpm = array('VOC/PM','VOCPM','VOC PM', 'VOC\\PM');
	public $industryType = array('INDUSTRY TYPE', 'INDUSTRY TYPES');
	public $industrySubType = array('INDUSTRY SUB-CATEGORY', 'INDUSTRY SUB-CATEGORIES', 'INDUSTRY SUB- CATEGORIES');
	public $flashPoint = array('FLASH POINT', 'FLASH-POINT', 'FLASH', 'POINT');
	public $paintOrChemical = array('PAINT COATING CHEMICAL PRODUCTS', 'PAINT COATING', 'CHEMICAL PRODUCTS');
	public $einecsElincs = array('IENECS','ELINCS','IENECS/ELINCS','IENECS / ELINCS', 'IENECS/ ELINCS','IENECS /ELINCS',
									'IENECS\\ELINCS','IENECS \\ ELINCS','IENECS\\ ELINCS', 'IENECS \\ELINCS');
	public $substanceSymbol = array('SYMBOL OF SUBSTANCE','SYMBOL', 'SYMBOL OF');
	public $substanceR = array('R(*) OF SUBSTANCE','RULE OF SUBSTANCE','R OF', 'R(*) OF', 'R (*) OF', 'R', 'R(*)', 'R (*)');
	public $health = array('HEALTH');
	public $productPricing = array('PRODUCT PRICING', 'PRODUCT', 'PRICING');
	public $unitType = array('UNIT TYPE', 'UNIT', 'TYPE');
	public $qty = array('QTY');		
	public $libraryTypes = array('LIBRARY TYPE', 'LIBRARY', 'TYPE');
	public $boilingRangeFrom = array('BOILING RANGE', 'FROM');
	public $boilingRangeTo = array('BOILING RANGE', 'TO');
	public $class = array('CLASS');
	public $irr = array('IRR');
	public $ohh = array('OHH');
	public $sens = array('SENS');
	public $oxy1 = array('OXY-1');
	public $percentVolatileWeight = array('PERCENT VOLATILE', 'BY WEIGHT');
	public $percentVolatileVolume = array('PERCENT VOLATILE', 'BY VOLUME');
	public $waste = array('WASTE');
	
	private $db;
	
	function __construct() {

	}
	
	public function getMap() {
		return array(
			"productID" => $this->productID,
			"mfg" => $this->mfg,
			"productName" => $this->productName,
			"type" => $this->type,
			"scoating" => $this->scoating,
			"aerosol" => $this->aerosol,
			"substrate" => $this->substrate,
			"rule" => $this->rule,
			"vocwx" => $this->vocwx,
			"voclx" => $this->voclx ,
			"case" => $this->case ,
			"description" => $this->description ,
			"mmhg" => $this->mmhg ,
			"temp" => $this->temp ,
			"weightFrom" => $this->weightFrom,
			"weightTo" => $this->weightTo,
			"gavity" => $this->gavity,
			"density" => $this->density,
			"vocpm" => $this->vocpm,
			"industryType" => $this->industryType,
			"industrySubType" => $this->industrySubType,
			"flashPoint" => $this->flashPoint ,
			"paintOrChemical" => $this->paintOrChemical ,
			"einecsElincs" => $this->einecsElincs,
			"substanceSymbol" => $this->substanceSymbol ,
			"substanceR" => $this->substanceR,
			"health" => $this->health,
			"productPricing" => $this->productPricing,
			"unitType" => $this->unitType,
			"qty" => $this->qty ,
			"libraryTypes" => $this->libraryTypes ,
			"boilingRangeFrom" => $this->boilingRangeFrom ,
			"boilingRangeTo" => $this->boilingRangeTo ,
			"class" => $this->class ,
			"irr" => $this->irr ,
			"ohh" => $this->ohh ,
			"sens" => $this->sens ,
			"oxy1" => $this->oxy1,
			"percentVolatileWeight" => $this->percentVolatileWeight ,
			"percentVolatileVolume" => $this->percentVolatileVolume,
			"waste" => $this->waste,
		);
	}
}
?>
