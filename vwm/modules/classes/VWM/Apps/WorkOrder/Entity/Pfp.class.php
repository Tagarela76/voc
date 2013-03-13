<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;

class Pfp extends Model
{
    public $id;
    public $description;
    public $company_id = 0;
    public $creator_id;
    public $last_update_time;
    public $is_proprietary = 0;
    public $products = array();
    protected $supplier_id;

    const TABLE_NAME = 'preformulated_products';
    const TABLE_PFP2COMPANY = 'pfp2company';
    const TABLE_PFP2PRODUCT = 'pfp2product';

    public function __construct()
    {
        
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCompanyId()
    {
        return $this->company_id;
    }

    public function setCompanyId($company_id)
    {
        $this->company_id = $company_id;
    }

    public function getCreatorId()
    {
        return $this->creator_id;
    }

    public function setCreatorId($creator_id)
    {
        $this->creator_id = $creator_id;
    }

    public function getLastUpdateTime()
    {
        return $this->last_update_time;
    }

    public function setLastUpdateTime($last_update_time)
    {
        $this->last_update_time = $last_update_time;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function getIsProprietary()
    {
        return $this->is_proprietary;
    }

    public function setIsProprietary($isProprietary)
    {
        $isProprietary = $this->convertPfpIProprietary($isProprietary);
        if ($isProprietary) {
            $this->is_proprietary = $isProprietary;
        } else {
            return $isProprietary;
        }
    }
    
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    public function setSupplierId($supplierId)
    {
        $this->supplier_id = $supplierId;
    }

    
    protected function _insert()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$db->sqltext($this->getLastUpdateTime())}'" : "'NULL'";
        $supplierId = ($this->getSupplierId()) ? "'{$db->sqltext($this->getSupplierId())}'" : "'NULL'";
        $companyId = ($this->getCompanyId()) ? "'{$db->sqltext($this->getCompanyId())}'" : "'NULL'";

        $sql = "INSERT INTO " . self::TABLE_NAME .
                "(description, company_id, creater_id, last_update_time, supplier_id, is_proprietary" .
                ") VALUES (" .
                "'{$db->sqltext($this->getDescription())}', " .
                "{$companyId}, " .
                "NULL, " .
                "{$lastUpdateTime}, " .
                "{$supplierId}, " .
                "{$db->sqltext($this->getIsProprietary())})";
        $response = $db->exec($sql);
        if ($response) {
            $this->setId($db->getLastInsertedID());

            if ($this->getCompanyId() != 0) {
                $sql = "INSERT INTO " . self::TABLE_PFP2COMPANY .
                        "(pfp_id ,company_id" .
                        ") VALUES (" .
                        "{$db->sqltext($this->getId())}, " .
                        "{$db->sqltext($this->getCompanyId())})";
                $db->query($sql);
            }
            return $this->getId();
        } else {
            return false;
        }
    }

    protected function _update()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$db->sqltext($this->getLastUpdateTime())}'" : "'NULL'";
        $supplierId = ($this->getSupplierId()) ? "'{$db->sqltext($this->getSupplierId())}'" : "'NULL'";
        $companyId = ($this->getCompanyId()) ? "'{$db->sqltext($this->getCompanyId())}'" : "'NULL'";
        
        $sql = "UPDATE preformulated_products SET " .
                "company_id = {$companyId}, " .
                "is_proprietary = {$db->sqltext($this->getIsProprietary())}, " .
                "last_update_time = {$lastUpdateTime}, " .
                "supplier_id = {$supplierId} " .        
                "WHERE id = {$db->sqltext($this->getId())}";

        $response = $db->exec($sql);
        if ($response) {
            if ($this->getCompanyId() != 0) {
                $sql = "SELECT * FROM " . self::TABLE_PFP2COMPANY .
                        "WHERE company_id = {$db->sqltext($this->getCompanyId())}";
                $response = $db->exec($sql);
                if (!$response) {
                    $sql = "INSERT INTO " . self::TABLE_PFP2COMPANY .
                            "(pfp_id ,company_id" .
                            ") VALUES (" .
                            "{$db->sqltext($this->getId())}, " .
                            "{$db->sqltext($this->getCompanyId())})";
                    $db->query($sql);
                }
            }
            return $this->getId();
        } else {
            return false;
        }
    }

    public function load()
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($this->getId())) {
            return false;
        }

        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id =" .
                $db->sqltext($this->getId());
        $db->query($sql);
        if ($db->num_rows() == 0) {
            return false;
        }
        $row = $db->fetch(0);
        $this->initByArray($row);
    }

    /**
     * function for converting pfps intellectual proprietary to boolean type
     * @string isProprietary
     * return bool
     */
    private function convertPfpIProprietary($isProprietary = 0)
    {
        //correct values

        if ($isProprietary == '1' || $isProprietary == '0') {
            return $isProprietary;
        } elseif ($isProprietary == 'IP') {
            return 1;
        } elseif (trim($isProprietary == '')) {
            return 0;
        } else {
            return false;
        }
    }

    public function getProductsCount()
    {
        return count($this->getProducts());
    }

    public function getRatio($htmlFormatting)
    {

        $products = $this->getProducts();

        foreach ($products as $product) {
            if ($product->isPrimary() && $htmlFormatting) {
                $res[] = "<b>" . $product->getRatio() . "</b>";
            } else {
                $res[] = $product->getRatio();
            }
        }
        return implode(':', $res);
    }

    /**
     * Delete all pfp products
     * 
     * @return boolean
     */
    public function deleteProducts()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "DELETE FROM " . self::TABLE_PFP2PRODUCT . " " .
                "WHERE preformulated_products_id={$db->sqltext($this->getId())}";

        $responce = $db->query($sql);
        if ($responce) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save pfp product
     */
    public function savePfpProducts()
    {
        $products = $this->getProducts();
        $this->deleteProducts();
        foreach ($products as $product) {
            $product->save();
        }
    }

}