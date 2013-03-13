<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;
use \VWM\Entity\Product\PaintProduct;

class PfpProduct extends PaintProduct
{

    protected $id;
    protected $ratio;
    protected $ratio_to;
    protected $ratio_from_original;
    protected $ratio_to_original;
    protected $product_id;
    protected $preformulated_products_id = null;
    protected $isPrimary = 0;

    /**
     * product name
     * @var string 
     */
    protected $name = null;

    /**
     * product Id
     * @var string
     */
    protected $product_nr = null;

    /**
     * Product Unit Type 
     * @var string 
     */
    protected $unitType = null;

    const TABLE_NAME = 'pfp2product';
    const TABLE_PRODUCT = 'product';

    public function __construct()
    {
        ;
    }

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRatio()
    {
        return $this->ratio;
    }

    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    public function getRatioTo()
    {
        return $this->ratio_to;
    }

    public function setRatioTo($ratio_to)
    {
        $this->ratio_to = $ratio_to;
    }

    public function getRatioFromOriginal()
    {
        return $this->ratio_from_original;
    }

    public function setRatioFromOriginal($ratio_from_original)
    {
        $this->ratio_from_original = $ratio_from_original;
    }

    public function getRatioToOriginal()
    {
        return $this->ratio_to_original;
    }

    public function setRatioToOriginal($ratio_to_original)
    {
        $this->ratio_to_original = $ratio_to_original;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    public function getPreformulatedProductsId()
    {
        return $this->preformulated_products_id;
    }

    public function setPreformulatedProductsId($preformulated_products_id)
    {
        $this->preformulated_products_id = $preformulated_products_id;
    }

    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getProductNr()
    {
        return $this->product_nr;
    }

    public function setProductNr($productNr)
    {
        $this->product_nr = $productNr;
    }

    public function getUnitType()
    {
        return $this->unitType;
    }

    public function setUnitType($unitType)
    {
        $this->unitType = $unitType;
    }

    public function getAtribute()
    {
        
    }

    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $ratioTo = ($this->getRatioTo() !== null) ? $this->db->sqltext($this->getRatioTo()) : "NULL";

        $ratioFromOriginal = ($this->getRatioFromOriginal() !== null) ? $this->db->sqltext($this->getRatioFromOriginal()) : "NULL";

        $ratioToOriginal = ($this->getRatioToOriginal() !== null) ? $this->db->sqltext($this->getRatioToOriginal()) : "NULL";

        $sql = "INSERT INTO " . self::TABLE_NAME .
                "(ratio, ratio_to, ratio_from_original, ratio_to_original, product_id, preformulated_products_id, isPrimary" .
                ") VALUES (" .
                "'{$db->sqltext($this->getRatio())}', " .
                "{$ratioTo}, " .
                "{$ratioFromOriginal}, " .
                "{$ratioToOriginal}, " .
                "{$db->sqltext($this->getProductId())}, " .
                "{$db->sqltext($this->getPreformulatedProductsId())}, " .
                "{$db->sqltext($this->getIsPrimary())})";
        $response = $db->exec($sql);
        if ($response) {
            $this->setId($db->getLastInsertedID());
            return $this->getId();
        } else {
            return false;
        }
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "UPDATE " . self::TABLE_NAME . " SET " .
                "ratio = {$db->sqltext($this->getRatio())}, " .
                "ratio_to = {$db->sqltext($this->getRatioTo())}, " .
                "ratio_from_original = {$db->sqltext($this->getRatioFromOriginal())} " .
                "ratio_to_original = {$db->sqltext($this->getRatioToOriginal())}, " .
                "product_id = {$db->sqltext($this->getProductId())}, " .
                "preformulated_products_id = {$db->sqltext($this->getPreformulatedProductsId())}, " .
                "isPrimary = {$db->sqltext($this->getIsPrimary())}, " .
                "WHERE id = {$db->sqltext($this->getId())}";
        $response = $db->exec($sql);
        if ($response) {
            return $this->getId();
        } else {
            return false;
        }
    }

}
?>
