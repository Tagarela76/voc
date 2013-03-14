<?php

namespace VWM\Import\Pfp;

use VWM\Import\CsvHelper;
use VWM\Import\EntityBuilder;
use VWM\Apps\WorkOrder\Entity\Pfp;
use \VWM\Apps\WorkOrder\Entity\PfpProduct;
use \VWM\Entity\Product\PaintProduct;

class PfpUploaderEntityBuilder extends EntityBuilder
{
    protected $pfps = array();
    protected $companyId = 0;

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    public function getPfps()
    {
        return $this->pfps;
    }

    public function setPfps($pfps)
    {
        $this->pfps = $pfps;
    }

    public function buildEntities($pathToCsv)
    {
        $csvHelper = new CsvHelper();
        $csvHelper->openCsvFile($pathToCsv);

        $fileData = $csvHelper->getFileContent();

        $currentPfp = new Pfp();
        $pfpProducts = array();
        $i = 0;
        $isPrimary = 1; 
        foreach ($fileData as $data) {
            $i++;
            //	group rows by PFP
            if ($data[$this->mapper->mappedData['number']] == '' 
                && $data[$this->mapper->mappedData['productId']] == '' 
                && $data[$this->mapper->mappedData['productName']] == '' 
                && $data[$this->mapper->mappedData['ratio']] == '' 
                && $data[$this->mapper->mappedData['unitType']] == '' 
                && $data[$this->mapper->mappedData['IP']] == '') {
                if(!is_null($currentPfp->getDescription())){
                    $convertPfpProducts = array();
                    if(count($pfpProducts)==1){
                        $convertPfpProducts = $pfpProducts;
                         // RDU or RTS
                         //	keep ratio as 1
                    }else{
                        foreach ($pfpProducts as $pfpProduct) {
                           
                            //check for %
                            if($pfpProduct->getUnitType()=='%'){
                               //do not do enything with this product
                              $pfpProduct = $this->convertRatioToPercent($pfpProduct, $convertPfpProducts[0]->getRatio());
                            }elseif (!$this->isVolumeRatio($pfpProduct->getUnitType())) {
                                $pfpProduct = $this->convertRatioToVolume($pfpProduct);
                            }
                            $convertPfpProducts[] = $pfpProduct;
                        }
                    }
                    //create the hole pfpDescription
                    $description = $currentPfp->getDescription();
                    foreach($convertPfpProducts as $pfpProductDescription){
                       $description.=' / '. $pfpProductDescription->getProductNr();
                    }
                    $currentPfp->setDescription($description.' /');
                    //get pfp id if exist
                    $pfpManager = new \VWM\Apps\WorkOrder\Manager\PfpManager();
                    $newPfp = $pfpManager->getPfpByDescription($description);
                    if ($newPfp) {
                        $currentPfp->setId($newPfp->getId());
                    }
                    $currentPfp->setProducts($convertPfpProducts);
                    $this->pfps[] = $currentPfp;
                }
                $currentPfp = new Pfp();
                $pfpProducts = array();
                $isPrimary = 1;
                continue;
            }
            
            if ($data[$this->mapper->mappedData['number']] != '') {

                if ($data[$this->mapper->mappedData['IP']] == 'IP') {
                    $currentPfp->setIsProprietary(1);
                }

                $currentPfp->setCompanyId($this->getCompanyId());

                //if pfp has it's own description set description and IP    
                $currentPfp->setDescription('/ '.$data[$this->mapper
                        ->mappedData['productName']]);
                
                if ($data[$this->mapper->mappedData['ratio']] == '' && $data[$this->mapper->mappedData['unitType']] == '') {
                    continue;    
                }
            }
            //create pfp Product
            $pfpProduct = new PfpProduct($this->db);
            $pfpProduct->setRatio($data[$this->mapper->mappedData['ratio']]);
            $pfpProduct->setName($data[$this->mapper->mappedData['productName']]);
            $pfpProduct->setProductNr($data[$this->mapper->mappedData['productId']]);
            $pfpProduct->setUnitType($data[$this->mapper->mappedData['unitType']]);
            
            //get Product Id
            $productId = $pfpProduct->getProductIdByProductNr($data[$this->mapper->mappedData['productId']]);
            $pfpProduct->setProductId($productId);
            //get pfp supplier Id by product
            if ($isPrimary == 1 && $productId) {
                $pfpProduct->setIsPrimary($isPrimary);
                $paintProduct = new PaintProduct($this->db, $productId);
                $supplierId = $paintProduct->getSupplierId();
                $currentPfp->setSupplierId($supplierId);
                $isPrimary = 0;
            }
            
            $pfpProducts[] = $pfpProduct;
        }
        $currentPfp->setProducts($pfpProducts);
        $this->pfps[] = $currentPfp;
    }

     /**
     * Check product for volume ratio. Actually Volume is default value,
     * so if it meets empty string this is also Volume
     * @param array $product from CSV file
     * @return boolean
     */
    private function isVolumeRatio($unitType)
    {
        $possibleVolumeStrings = array('VOL', 'VOLUME', '', 'PART');
        $isVolume = false;
        if (in_array($unitType, $possibleVolumeStrings)) {
            $isVolume = true;
        }
        return $isVolume;
    }
    /**
     * 
     * @param \VWM\Apps\WorkOrder\Entity\PfpProduct 
     * 
     * @return \VWM\Apps\WorkOrder\Entity\PfpProduct
     * 
     * @throws \Exception
     * @throws Exception
     */
    private function convertRatioToVolume($product)
    {
        $unitTypeConverter = new \UnitTypeConverter();
        $productObj = new \VWM\Entity\Product\PaintProduct($this->db);
        $productID = $productObj->getProductIdByName($product->getProductNr());

        if (!$productID) {
            //throw new \Exception('This is no product in database ' . $product->getProductNr());
            return $product;
        }
        
        $productObj->setId($productID);
        $productObj->load();
        
        $density = new \Density($this->db, $productObj->getDensityUnitID());

        if (!$density->getNumerator()) {
            throw new \Exception("Failed to load Density with id " . $productObj->getDensityUnitId());
        }

        $densityType = array(
            'numerator' => $density->getNumerator(),
            'denominator' => $density->getDenominator()
        );

        if ($product->getUnitType() == "GRAMS") {
            $volumeQty = $unitTypeConverter->convertToDefault($product->getRatio(), 'gram', $productObj->getDensity(), $densityType);
        } else {
            $volumeQty = $unitTypeConverter->convertToDefault($product->getRatio(), 'oz', $productObj->getDensity(), $densityType);
        }

        $product->setRatio($volumeQty);
        $product->setUnitType('VOL');

        return $product;
    }
    
    /**
     * 
     * getting percent from value
     * 
     * @param int $percent
     * @param int $value
     * 
     * @return \VWM\Apps\WorkOrder\Entity\PfpProduct
     */
    private function convertRatioToPercent($pfpProduct, $value){
        $percent = $pfpProduct->getRatio();
        $value = $percent*$value/100;
        $pfpProduct->setRatio($value);
        $pfpProduct->setUnitType('VOL');
        return $pfpProduct;
    }
}
?>
