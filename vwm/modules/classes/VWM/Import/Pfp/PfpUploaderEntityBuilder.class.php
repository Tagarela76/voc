<?php

namespace VWM\Import\Pfp;

use VWM\Import\CsvHelper;
use VWM\Import\EntityBuilder;
use VWM\Apps\WorkOrder\Entity\Pfp;
use \VWM\Apps\WorkOrder\Entity\PfpProduct;

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
                    $currentPfp->setProducts($pfpProducts);
                    $this->pfps[] = $currentPfp;
                }
                $currentPfp = new Pfp();
                $pfpProducts = array();
                continue;
            }

            if ($data[$this->mapper->mappedData['number']] != '') {

                if ($data[$this->mapper->mappedData['IP']] == 'IP') {
                    $currentPfp->setIsProprietary(1);
                }

                $currentPfp->setCompanyId($this->getCompanyId());

                //if pfp has it's own description set description and IP    

                $currentPfp->setDescription($data[$this->mapper
                        ->mappedData['productName']]);
                //get pfp id if exist
                $pfpManager = new \VWM\Apps\WorkOrder\Manager\PfpManager();
                $description = $currentPfp->getDescription();
                $newPfp = $pfpManager->getPfpByDescription($description);
                if ($newPfp) {
                    $currentPfp->setId($newPfp->getId());
                }
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
            //check product unitType For getting Process
            if (!$this->isVolumeRatio($pfpProduct->getUnitType())) {
                $pfpProduct = $this->convertRatioToVolume($pfpProduct);
            }
            
            $pfpProducts[] = $pfpProduct;
        }
    }

     /**
     * Check product for volume ratio. Actually Volume is default value,
     * so if it meets empty string this is also Volume
     * @param array $product from CSV file
     * @return boolean
     */
    private function isVolumeRatio($unitType)
    {
        $possibleVolumeStrings = array('VOL', 'VOLUME', '');
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
            throw new \Exception('This is no product in database' . $product[bulkUploader4PFP::PRODUCTNR_INDEX]);
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

}
?>
