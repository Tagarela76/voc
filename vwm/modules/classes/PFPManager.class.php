<?php

use VWM\Framework\Cache\DbCacheDependency;

/**
 * PreFormulatedProductsManager (PFPManager)
 */
class PFPManager
{

    /**
     *
     * @var db
     */
    private $db;
    public $searchCriteria = array();

    function PFPManager($db)
    {
        $this->db = $db;
    }

    public function isUnique($description, $companyID)
    {

        $description = mysql_escape_string($description);
        $companyID = mysql_escape_string($companyID);
        $query = "SELECT count(id) as 'c' from " . TB_PFP . " WHERE description = '$description' AND company_id = $companyID LIMIT 1";
        $this->db->query($query);

        $row = $this->db->fetch_array(0);
        $c = intval($row['c']);

        return $c > 0 ? FALSE : TRUE;
    }

    public function countPFPAll($companyID = 0, $searchString = '', $industryType = 0, $supplierID = 0)
    {
        $queryFilter = "";
        return $this->_countPFP($queryFilter, $companyID, $searchString, $industryType, $supplierID);
    }

    public function countPFPAllowed($companyID = 0, $searchString = '', $industryType = 0, $supplierID = 0)
    {
        $queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_available = 1 ";
        return $this->_countPFP($queryFilter, $companyID, $searchString, $industryType, $supplierID);
    }

    public function countPFPAssigned($companyID = 0, $searchString = '', $industryType = 0, $supplierID = 0)
    {
        $queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_assigned = 1 AND pfp2c.company_id = {$this->db->sqltext($companyID)} ";
        return $this->_countPFP($queryFilter, $companyID, $searchString, $industryType, $supplierID);
    }

    private function _countPFP($queryFilter = '', $companyID = 0, $searchString = '', $industryType = 0, $supplierID = 0)
    {
        //	build mandatory sql
        $query = "SELECT pfp.id as id " .
                "FROM " . $this->_declareTablesForSearchAndListPFPs($companyID, $industryType, $supplierID) . " " .
                "WHERE pfp2p.preformulated_products_id = pfp.id ";

        $query .= $queryFilter;

        if (count($this->searchCriteria) > 0 || $industryType != 0 || $supplierID != 0) {
            $query .= " AND p.product_id = pfp2p.product_id ";
        }

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= "AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " pfp.description LIKE ('%" . $this->db->sqltext($pfp) . "%') " .
                        "OR p.name LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        if ($industryType != 0) {
            //$query .= " AND p.product_id = p2t.product_id AND p2t.type_id = {$this->db->sqltext($industryType)}";
            $query .= " AND p.product_id = p2t.product_id AND (p2t.industry_type_id IN " .
                    "(SELECT id FROM " . TB_INDUSTRY_TYPE . " WHERE parent = {$this->db->sqltext($industryType)}) OR p2t.industry_type_id = {$this->db->sqltext($industryType)})";
        }

        if ($supplierID != 0) {
            $query .= " AND p.supplier_id = s.supplier_id  AND s.original_id = {$this->db->sqltext($supplierID)}";
        }

        if ($companyID != 0) {
            $query .= " AND pfp2c.pfp_id = pfp.id AND pfp2c.company_id = " . $this->db->sqltext($companyID);
        }

        $query .= " GROUP BY pfp.id";

        $this->db->query($query);

        return $this->db->num_rows();
    }

    /**
     * Alias for PFPManager::countPFPAssigned()
     * @param int $companyID
     * @param string $searchString
     * @param int $industryType
     * @param int $supplierID
     * @return int
     */
    public function countPFP($companyID = 0, $searchString = '', $industryType = 0, $supplierID = 0)
    {
        return $this->countPFPAllowed($companyID, $searchString, $industryType, $supplierID);
    }

    public function getCompaniesByPfpID($pfpID)
    {
        $query = "SELECT c.company_id, c.name FROM " . TB_COMPANY . " c, " . TB_PFP2COMPANY . " p2c WHERE c.company_id=p2c.company_id AND p2c.pfp_id=" . $pfpID;
        $this->db->query($query);
        $rows = $this->db->fetch_all();

        foreach ($rows as $row) {
            $list[$row->company_id] = $row->name;
        }

        return $list;
    }

    public function getCountPFP($supplier_id = 0)
    {
        $query = "SELECT count(*) AS cnt_pfp FROM " .
                TB_PFP . " pfp, " . TB_PFP2PRODUCT . " p2p, " . TB_PRODUCT . " p, " . TB_SUPPLIER . " s WHERE " .
                " p.supplier_id = s.supplier_id " .
                " AND p2p.isPrimary = 1 " .
                " AND p2p.preformulated_products_id = pfp.id " .
                " AND p2p.product_id = p.product_id ";
        if ($supplier_id) {
            $query .= " AND s.original_id = " . mysql_real_escape_string($supplier_id);
        }

        $this->db->query($query);

        $numRows = $this->db->num_rows();
        if ($numRows == 1) {
            return $this->db->fetch(0)->cnt_pfp;
        } else {
            return false;
        }
    }

    public function getListAll($companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0, $supplierID = 0)
    {
        $queryFilter = "";
        return $this->_getList($queryFilter, $companyID, $pagination, $idArray, $industryType, $supplierID);
    }

    public function getListAllowed($companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0, $supplierID = 0)
    {
        $queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_available = 1 ";
        return $this->_getList($queryFilter, $companyID, $pagination, $idArray, $industryType, $supplierID);
    }

    public function getListAssigned($companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0, $supplierID = 0)
    {
        $queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_assigned = 1 AND pfp2c.company_id = {$this->db->sqltext($companyID)} ";
        return $this->_getList($queryFilter, $companyID, $pagination, $idArray, $industryType, $supplierID);
    }

    private function _getList($queryFilter = '', $companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0, $supplierID = 0)
    {
        //	build mandatory sql
        $query = "SELECT pfp.id, pfp.description, pfp.company_id " .
                "FROM " . $this->_declareTablesForSearchAndListPFPs($companyID, $industryType, $supplierID) . " " .
                "WHERE pfp2p.preformulated_products_id = pfp.id ";

        $query .= $queryFilter;

        if (count($this->searchCriteria) > 0 || $industryType != 0 || $supplierID != 0) {
            $query .= " AND p.product_id = pfp2p.product_id ";
        }

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= "AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " pfp.description LIKE ('%" . $this->db->sqltext($pfp) . "%') " .
                        "OR p.name LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        if ($industryType != 0) {
            //$query .= " AND p.product_id = p2t.product_id AND p2t.type_id = {$this->db->sqltext($industryType)}";
            $query .= " AND p.product_id = p2t.product_id AND (p2t.industry_type_id IN " .
                    "(SELECT id FROM " . TB_INDUSTRY_TYPE . " WHERE parent = {$this->db->sqltext($industryType)}) OR p2t.industry_type_id = {$this->db->sqltext($industryType)})";
        }

        if ($supplierID != 0) {
            $query .= " AND p.supplier_id = s.supplier_id  AND s.original_id = {$this->db->sqltext($supplierID)}";
        }

        if ($companyID !== null) {
            $query .= " AND pfp2c.pfp_id=pfp.id AND pfp2c.company_id = " . $this->db->sqltext($companyID);
        }

        if (isset($idArray) and is_array($idArray) and count($idArray) > 0) {
            $count = count($idArray);
            $query .= " AND pfp.id IN ( ";

            for ($i = 0; $i < $count; $i++) {
                $query .= $idArray[$i];
                if ($i < $count - 1) {
                    $query .= ", ";
                }
            }

            $query .= " ) ";
        }

        $query .= " GROUP BY pfp.id ";

        if (isset($pagination)) {
            $query .= " ORDER BY pfp.description LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        return $this->_processGetPFPListQuery($query);
    }

    public function getList($companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0, $supplierID = 0)
    {
        //	build mandatory sql
        $query = "SELECT pfp.id, pfp.description, pfp.company_id " .
                "FROM " . $this->_declareTablesForSearchAndListPFPs($companyID, $industryType, $supplierID) . " " .
                "WHERE p.product_id = pfp2p.product_id AND pfp2p.preformulated_products_id = pfp.id ";

        if ($companyID != 0) {
            $query .= " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_assigned = 1 AND pfp2c.company_id = {$this->db->sqltext($companyID)} ";
        } else {
            $query .= " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_available = 1 ";
        }

        if ($industryType != 0) {
            //$query .= " AND p.product_id = p2t.product_id AND p2t.type_id = {$this->db->sqltext($industryType)}";
            $query .= " AND p.product_id = p2t.product_id AND (p2t.type_id IN " .
                    "(SELECT id FROM " . TB_INDUSTRY_TYPE . " WHERE parent = {$this->db->sqltext($industryType)}) OR p2t.type_id = {$this->db->sqltext($industryType)})";
        }

        if ($supplierID != 0) {
            $query .= " AND p.supplier_id = s.supplier_id  AND s.original_id = {$this->db->sqltext($supplierID)}";
        }

        if (isset($idArray) and is_array($idArray) and count($idArray) > 0) {
            $count = count($idArray);
            $query .= " AND pfp.id IN ( ";

            for ($i = 0; $i < $count; $i++) {
                $query .= $idArray[$i];
                if ($i < $count - 1) {
                    $query .= ", ";
                }
            }

            $query .= " ) ";
        }

        $query .= " GROUP BY pfp.id ";

        if (isset($pagination)) {
            $query .= " ORDER BY pfp.id LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        return $this->_processGetPFPListQuery($query);
    }

    /**
     * Alias for PFPManager::getListAllowed()
     * @param integer $companyID
     * @param Pagination $pagination
     * @param string $searchString
     * @return array
     */
    public function searchPFP($companyID = 0, Pagination $pagination = null, $searchString = "")
    {
        if (empty($companyID)) {
            $companyID = null;
        }
        $this->searchCriteria[] = $searchString;
        return $this->getListAllowed($companyID, $pagination);
    }

    public function getPfpList($PfpIdArray = null)
    {
        if ($PfpIdArray != null) {
            $pmanager = new Product($this->db);
            $productsbysupplier = $pmanager->getProductListByMFG($PfpIdArray);
            $count = count($productsbysupplier);
            $PFPArray = array();
            for ($i = 0; $i < $count; $i++) {

                $getPfpQuery = "SELECT preformulated_products_id FROM " . TB_PFP2PRODUCT . " WHERE product_id = " . $productsbysupplier[$i]['product_id'];

                $this->db->query($getPfpQuery);
                $pfp = $this->db->fetch_all_array();
                foreach ($pfp as $p) {
                    if ($p['preformulated_products_id']) {
                        $PFPArray[] = $p['preformulated_products_id'];
                    }
                }
            }
            $pfparray = array_merge(array_unique($PFPArray));
            return $pfparray;
        } else {
            return false;
        }
    }

    public function getListSpecial($companyID = null, Pagination $pagination = null, $idArray = null, $industryType = 0)
    {
        if ($idArray != null) {
            if ($companyID) {
                $companyID = mysql_escape_string($companyID);
                //$query = "SELECT * FROM " . TB_PFP . " WHERE company_id = $companyID";
                $query = "SELECT pfp.id, pfp.description, pfp.company_id FROM " . TB_PFP . " pfp, " . TB_PFP2COMPANY . " pfp2c WHERE pfp.id=pfp2c.pfp_id AND pfp2c.company_id = $companyID ";
            } else {
                $query = "SELECT * FROM " . TB_PFP . " WHERE 1 ";
            }
            if (isset($pagination)) {
                $query .= "ORDER BY pfp.id LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
            }

            if (isset($idArray) and is_array($idArray) and count($idArray) > 0) {

                $count = count($idArray);
                $query .= " AND id IN ( ";

                for ($i = 0; $i < $count; $i++) {
                    $query .= $idArray[$i];
                    if ($i < $count - 1) {
                        $query .= ", ";
                    }
                }


                $query .= " )";
            }

            if (!$companyID) {
                $query .= " ORDER BY id";
            }

            $this->db->query($query);

            //Init PFPProducts for each PFP...
            $pfpArray = $this->db->fetch_all_array();
            $count = count($pfpArray);


            $pfps = array(); //Array of objects PFP

            for ($i = 0; $i < $count; $i++) {

                $PFPProductsArray = array();

                $getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray[$i]['id'];
                //echo "<br/>$getProductsQuery";
                $this->db->query($getProductsQuery);
                $products = $this->db->fetch_all_array();
                //var_dump($products);
                foreach ($products as $p) {
                    $prodtmp = new PFPProduct($this->db);
                    $prodtmp->setRatio($p['ratio']);
                    $prodtmp->initializeByID($p['product_id']);
                    $prodtmp->setIsPrimary($p['isPrimary']);
                    $PFPProductsArray[] = $prodtmp;
                }

                $pfp = new PFP($PFPProductsArray);
                $pfp->setID($pfpArray[$i]['id']);
                $pfp->setDescription($pfpArray[$i]['description']);
                $pfp->products = $PFPProductsArray;
                $pfps[] = $pfp;
            }
            return $pfps;
        } else {
            return false;
        }
    }

    // TODO: needs complete rewrite
    public function add(PFP $product, $companyID)
    {

        $count = count($product->getProducts());
        if ($count == 0) {

            return false;
        }

        $this->db->beginTransaction();
        $last_update_time = 'NOW()';
        if (empty($companyID)) {
            $companyID = 0;
        }

        if (isset($companyID) and is_array($companyID) and count($companyID) > 0) {
            $queryAddPFP = "INSERT INTO " . TB_PFP . " (description,company_id, last_update_time) VALUES ('" . $product->getDescription() . "',NULL, {$last_update_time})";

            $this->db->query($queryAddPFP);

            $pfpID = $this->db->getLastInsertedID();
            $i = 0;
            while ($companyID[$i]) {
                $queryAddPFPRelation2Company = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id ,company_id) VALUES (" . $pfpID . ", " . $companyID[$i]['id'] . ")";
                $this->db->query($queryAddPFPRelation2Company);

                $i++;
            }
        } else {
            $queryAddPFP = "INSERT INTO " . TB_PFP . " (description,company_id, last_update_time) VALUES ('" . $product->getDescription() . "', " . $companyID . ", {$last_update_time})";
            $this->db->query($queryAddPFP);

            $pfpID = $this->db->getLastInsertedID();

            $queryAddPFPRelation2Company = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id ,company_id) VALUES (" . $pfpID . ", " . $companyID . ")";
            $this->db->query($queryAddPFPRelation2Company);
        }
        $queryInsertPFPProducts = "INSERT INTO " . TB_PFP2PRODUCT . "(ratio,product_id,preformulated_products_id,isPrimary,ratio_to,ratio_from_original,ratio_to_original) VALUES ";
        $pfpProducts = $product->getProducts();
        for ($i = 0; $i < $count; $i++) {
            $isPrimary = $pfpProducts[$i]->isPrimary() ? "true" : "false";
            if ($pfpProducts[$i]->isRange()) {
                $range_ratio = explode("-", $pfpProducts[$i]->getRangeRatio());
                $ratio_from_original = isset($range_ratio[0]) ? $range_ratio[0] : "null";
                $ratio_to_original = isset($range_ratio[count($range_ratio) - 1]) ? $range_ratio[count($range_ratio) - 1] : "null";
            } else {
                $ratio_from_original = "null";
                $ratio_to_original = "null";
            }
            $queryInsertPFPProducts .= " ( " . $pfpProducts[$i]->getRatio() .
                    ", " . $pfpProducts[$i]->product_id . " , $pfpID , $isPrimary, 1, $ratio_from_original, $ratio_to_original) ";
            if ($i < $count - 1) {
                $queryInsertPFPProducts .= " , ";
            }
        }

        $this->db->query($queryInsertPFPProducts);

        $this->db->commitTransaction();
    }

    public function remove(PFP $product)
    {

        $this->removeProducts($product->getId());
        $delQuery = "DELETE FROM " . TB_PFP . " WHERE id = " . $product->getId();
        $this->db->query($delQuery);
    }

    public function removeList($pfpArray)
    {

        //$this->db->beginTransaction();
        foreach ($pfpArray as $pfp) {

            $this->remove($pfp);
        }
    }

    //TODO: is this error?
    public function unassignPFPFromCompanies($pfpID)
    {

        $query_unassign = "UPDATE " . TB_PFP2COMPANY . " SET is_assigned = 0 WHERE pfp_id = " . $pfpID;
        $this->db->query($query_unassign);
        if (mysql_errno() == 0) {
            $error = "";
        } else {
            $error = "Error!";
        }

        return $error;
    }

    public function unassignPFPFromCompany($pfpID, $companyID)
    {

        $query_unassign = "UPDATE " . TB_PFP2COMPANY . " SET is_assigned = 0 WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
        $this->db->query($query_unassign);
    }

    public function unavailablePFPFromCompany($pfpID, $companyID)
    {
        $query_unavailable = "UPDATE " . TB_PFP2COMPANY . " SET is_available = 0, is_assigned = 0 WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
        $this->db->query($query_unavailable);
    }

    public function assignPFP2Company($pfpID, $companyID)
    {
        $sql_select = "SELECT * FROM " . TB_PFP2COMPANY . " WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
        $this->db->query($sql_select);
        if ($this->db->num_rows() == 0) {
            $query = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id, company_id, is_available, is_assigned) " .
                    " VALUES (" . $pfpID . ", " . $companyID . ", 0, 1)";
            $this->db->query($query);
            if (mysql_errno() == 0) {
                $error = "";
            } else {
                $error = "Error!";
            }
        } else {
            $query = "UPDATE " . TB_PFP2COMPANY . " SET is_assigned = 1 WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
            $this->db->query($query);
            if (mysql_errno() == 0) {
                $error = "";
            } else {
                $error = "Error!";
            }
        }

        return $error;
    }

    public function availablePFP2Company($pfpID, $companyID)
    {
        $sql_select = "SELECT * FROM " . TB_PFP2COMPANY . " WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
        $this->db->query($sql_select);
        if ($this->db->num_rows() == 0) {
            $query = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id, company_id, is_available, is_assigned) " .
                    " VALUES (" . $pfpID . ", " . $companyID . ", 1, 0)";
            $this->db->query($query);
            if (mysql_errno() == 0) {
                $error = "";
            } else {
                $error = "Error!";
            }
        } else {
            $query = "UPDATE " . TB_PFP2COMPANY . " SET is_available = 1 WHERE pfp_id = " . $pfpID . " AND company_id = " . $companyID;
            $this->db->query($query);
            if (mysql_errno() == 0) {
                $error = "";
            } else {
                $error = "Error!";
            }
        }

        return $error;
    }

    public function update(PFP $from, PFP $to)
    {

        $this->db->beginTransaction();

        $last_update_time = 'NOW()';

        $this->removeProducts($from->getId());

        $updatePFPQuery = "UPDATE " . TB_PFP . " SET " .
                " description = '" . $this->db->sqltext($to->getDescription()) . "', " .
                " last_update_time = {$last_update_time}" .
                " WHERE id = " . $from->getId();

        if (!$this->db->query($updatePFPQuery)) {
            $this->db->rollbackTransaction();
        }

        $count = count($to->getProducts());
        $queryInsertPFPProducts = "INSERT INTO " . TB_PFP2PRODUCT . "(ratio,product_id,preformulated_products_id,isPrimary,ratio_to,ratio_from_original,ratio_to_original) VALUES ";
        $pfpProducts = $to->getProducts();
        for ($i = 0; $i < $count; $i++) {
            $isPrimary = $pfpProducts[$i]->isPrimary() ? "true" : "false";
            if ($pfpProducts[$i]->isRange()) {
                $ratio_to = $pfpProducts[$i]->ratio_to;
                $range_ratio = explode("-", $pfpProducts[$i]->range_ratio);
                $ratio_from_original = $range_ratio[0];
                $ratio_to_original = $range_ratio[count($range_ratio) - 1];
            } else {
                $ratio_to = $range_ratio = $ratio_from_original = $ratio_to_original = 'NULL';
            }
            $queryInsertPFPProducts .= " ( " . $pfpProducts[$i]->getRatio() .
                    ", " . $pfpProducts[$i]->product_id . " , {$from->getId()}, $isPrimary, {$ratio_to}, $ratio_from_original, $ratio_to_original) ";
            if ($i < $count - 1) {
                $queryInsertPFPProducts .= " , ";
            }
        }

        if (!$this->db->query($queryInsertPFPProducts)) {
            $this->db->rollbackTransaction();
        }
        $this->db->commitTransaction();
    }

    private function removeProducts($pfpID)
    {
        $pfpID = mysql_escape_string($pfpID);
        $query = "DELETE FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = $pfpID";
        //echo "<br/>".$query;
        $this->db->query($query);
    }

    public function getPFP($id)
    {
        $id = mysql_escape_string($id);
        $query = "SELECT * FROM " . TB_PFP . " WHERE id = $id";

        $this->db->query($query);

        //Init PFPProducts for each PFP...
        $pfpArray = $this->db->fetch_array(0);

        $PFPProductsArray = array();

        $getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray['id'];
        //echo "<br/>$getProductsQuery";
        $this->db->query($getProductsQuery);
        $products = $this->db->fetch_all_array();
        //var_dump($getProductsQuery);die();
        foreach ($products as $p) {
            $prodtmp = new PFPProduct($this->db);
            $prodtmp->setRatio($p['ratio']);
            $prodtmp->setIsPrimary($p['isPrimary']);
            $prodtmp->setId($p['id']);
            if (!empty($p['ratio_to']) && !empty($p['ratio_from_original']) && !empty($p['ratio_to_original'])) {
                $prodtmp->setIsRange(true);
                $prodtmp->setRangeRatio(trim($p['ratio_from_original']) . '-' . trim($p['ratio_to_original']));
            } else {
                $prodtmp->setIsRange(false);
            }
            $prodtmp->initializeByID($p['product_id']);
            $PFPProductsArray[] = $prodtmp;
        }

        //old pfp function
        /* $pfp = new PFP($PFPProductsArray);
          $pfp->setID($pfpArray['id']);
          $pfp->setDescription($pfpArray['description']);
          $pfp->products = $PFPProductsArray; */

        $pfp = new VWM\Apps\WorkOrder\Entity\Pfp();
        $pfp->setId($id);
        $pfp->load();
        $pfp->setProducts($PFPProductsArray);

        return $pfp;
    }

    public function getPFPProduct($id)
    {
        $prodtmp = new PFPProduct($this->db);
    }

    public function getPFPProductsbySopplier($id)
    {

        $query = "SELECT * FROM " . TB_PRODUCT . " WHERE supplier_id = $id";
        $this->db->query($query);
        $pfpproducts = $this->db->fetch_all_array();
        return $pfpproducts;
    }

    public function isPFPModified($pfpOld, $pfp)
    {
        $result = FALSE;
        if ($pfp->getDescription() == $pfpOld->getDescription()) {
            if (count($pfp->products) == count($pfpOld->products)) {
                for ($i = 0; $i < count($pfp->products); $i++) {
                    if ($pfp->products[$i]->product_id == $pfpOld->products[$i]->product_id) {
                        if ($pfp->products[$i]->isPrimary() == $pfpOld->products[$i]->isPrimary()) {
                            if ($pfp->products[$i]->getRatio() == $pfpOld->products[$i]->getRatio()) {
                                
                            } else {
                                $result = TRUE;
                                break;
                            }
                        } else {
                            $result = TRUE;
                            break;
                        }
                    } else {
                        $result = TRUE;
                        break;
                    }
                }
            } else {
                $result = TRUE;
            }
        } else {
            $result = TRUE;
        }

        return $result;
    }

    public function isCreaterPFP($pfpID, $companyID)
    {
        $result = FALSE;
        $query = "SELECT company_id FROM " . TB_PFP . " WHERE id=" . $pfpID;
        $this->db->query($query);
        $companyCreaterID = $this->db->fetch(0)->company_id;
        if ($companyCreaterID == $companyID) {
            $result = TRUE;
        }

        return $result;
    }

    public function searchAutocomplete($occurrence, $companyId = null, $pfpTypeId = null)
    {
        $occurrence = $this->db->sqltext($occurrence);

        if (!is_null($companyId)) {
            $query = "SELECT pfp.description, LOCATE('" . $occurrence . "', pfp.description) occurrence1  " .
                    " FROM " . TB_PFP . " pfp " .
                    "JOIN " . TB_PFP2COMPANY . " pfp2c ON pfp.id = pfp2c.pfp_id " .
                    "LEFT JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                    "WHERE pfp2c.is_available = 1 " .
                    "AND pfp2c.company_id = {$this->db->sqltext($companyId)}
				 AND (LOCATE('" . $occurrence . "', pfp.description)>0)";
            if (!is_null($pfpTypeId)) {
                $query .= " AND pfp2t.pfp_type_id = {$this->db->sqltext($pfpTypeId)}";
            }
            $query .= " LIMIT " . AUTOCOMPLETE_LIMIT;
        } else {
            $query = "SELECT pfp.description, p.name, LOCATE('" . $occurrence . "', pfp.description) occurrence1, LOCATE('" . $occurrence . "', p.name) occurrence2  " .
                    "FROM " . TB_PFP . " pfp, " . TB_PRODUCT . " p, " . TB_PFP2PRODUCT . " pfp2p " .
                    "WHERE p.product_id = pfp2p.product_id AND pfp2p.preformulated_products_id = pfp.id AND (" .
                    "LOCATE('" . $occurrence . "', pfp.description)>0 OR " .
                    "LOCATE('" . $occurrence . "', p.name)>0 " .
                    ")" .
                    " LIMIT " . AUTOCOMPLETE_LIMIT;
        }

        $this->db->query($query);

        if ($this->db->num_rows() > 0) {
            $pfps = $this->db->fetch_all_array();

            foreach ($pfps as $pfp) {
                if ($pfp['occurrence1'] != 0) {
                    $result = array(
                        "pfp" => $pfp['description'],
                        "occurrence" => $pfp['occurrence1']
                    );
                    $results[] = $result;
                } elseif ($pfp['occurrence2'] != 0) {
                    $result = array(
                        "pfp" => $pfp['name'],
                        "occurrence" => $pfp['occurrence2']
                    );
                    $results[] = $result;
                }
            }
            return (isset($results)) ? $results : false;
        }
        else
            return false;
    }

    private function _processGetPFPListQuery($query)
    {
        $pfps = array(); //Array of objects PFP
        //	try to read from cache
        $cache = VOCApp::getInstance()->getCache();
        $key = md5('query' . $query);
        if ($cache) {
            $pfps = $cache->get($key);
            if ($pfps) {
                return $pfps;
            }
        }

        $this->db->query($query);

        //Init PFPProducts for each PFP...
        $pfpArray = $this->db->fetch_all_array();
        $count = count($pfpArray);

        for ($i = 0; $i < $count; $i++) {

            $PFPProductsArray = array();

            $getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray[$i]['id'];
            //echo "<br/>$getProductsQuery";
            $this->db->query($getProductsQuery);
            $products = $this->db->fetch_all_array();
            //var_dump($products);
            $isRangePFP = false;
            foreach ($products as $p) {
                if (!is_null($p['ratio_to']) && !is_null($p['ratio_from_original']) && !is_null($p['ratio_to_original'])) {
                    $isRangePFP = true;
                }
                $prodtmp = new PFPProduct($this->db);
                $prodtmp->setRatio($p['ratio']);
                $prodtmp->initializeByID($p['product_id']);
                $prodtmp->setIsPrimary($p['isPrimary']);
                $PFPProductsArray[] = $prodtmp;
            }

            //var_dump($PFPProductsArray);
            $pfp = new PFP($PFPProductsArray);
            $pfp->setID($pfpArray[$i]['id']);
            $pfp->setDescription($pfpArray[$i]['description']);
            $pfp->products = $PFPProductsArray;
            $pfp->isRangePFP = $isRangePFP;
            $pfps[] = $pfp;
        }

        //save to cache
        if ($cache) {
            $sqlDependency = "SELECT MAX(last_update_time) FROM " . TB_PFP . "";
            $cache->set($key, $pfps, 86400, new DbCacheDependency($this->db, $sqlDependency));
        }

        return $pfps;
    }

    /**
     * Generate string to inculde into SQL from statement for count/list PFP's
     * @param int $companyID
     * @param int $industryType
     * @return string Example preformulated_products pfp, product p, pfp2product pfp2p, product2type p2t
     */
    private function _declareTablesForSearchAndListPFPs($companyID = 0, $industryType = 0, $supplierID = 0)
    {
        $tables = array(
            TB_PFP . " pfp",
            TB_PFP2PRODUCT . " pfp2p"
        );

        if (count($this->searchCriteria) > 0 || $industryType != 0 || $supplierID != 0) {
            array_push($tables, TB_PRODUCT . " p");
        }

        if ($companyID != 0) {
            array_push($tables, TB_PFP2COMPANY . " pfp2c");
        }
        if ($industryType != 0) {
            array_push($tables, TB_PRODUCT2INDUSTRY_TYPE . " p2t ");
        }

        if ($supplierID != 0) {
            array_push($tables, TB_SUPPLIER . " s ");
        }

        return implode(', ', $tables);
    }

    public function isPFPsProductsAssign2Company($pfpId, $companyId)
    {

        //  get all pfp products
        $query = "SELECT product_id FROM " . TB_PFP2PRODUCT . "
				WHERE preformulated_products_id = {$this->db->sqltext($pfpId)}";
        $this->db->query($query);
        $pfpProducts = array();
        $rows = $this->db->fetch_all_array();
        foreach ($rows as $row) {
            $pfpProducts[] = $row['product_id'];
        }
        // get all assign to company products
        $query = "SELECT product_id FROM " . TB_PRODUCT2COMPANY . "
				WHERE company_id = {$this->db->sqltext($companyId)}";
        $this->db->query($query);
        $companyAssignProducts = array();
        $rows = $this->db->fetch_all_array();
        foreach ($rows as $row) {
            $companyAssignProducts[] = $row['product_id'];
        }

        // check if all pfps products assign to company
        $pfpProductsIsAssign = true; // flag
        foreach ($pfpProducts as $pfpProduct) {
            if (!in_array($pfpProduct, $companyAssignProducts)) {
                $pfpProductsIsAssign = false;
            }
        }
        return $pfpProductsIsAssign;
    }

    /**
     * Assign PFP to the PFP Type
     * @param int $pfpID
     * @param int $pfpTypeID
     */
    public function assignPFP2Type($pfpID, $pfpTypeID)
    {
        $this->unAssignPFP2Type($pfpID, $pfpTypeID);

        $sql = "INSERT INTO " . TB_PFP2PFP_TYPES . " (pfp_id, pfp_type_id) VALUES " .
                "({$this->db->sqltext($pfpID)}, {$this->db->sqltext($pfpTypeID)})";
        $this->db->exec($sql);
    }

    /**
     * Remove PFP from PFP type
     * @param int $pfpID
     * @param int $pfpTypeID
     */
    public function unAssignPFP2Type($pfpID, $pfpTypeID)
    {
        $sql = "DELETE FROM " . TB_PFP2PFP_TYPES . " " .
                "WHERE pfp_id = {$this->db->sqltext($pfpID)} " .
                "AND pfp_type_id = {$this->db->sqltext($pfpTypeID)}";
        $this->db->exec($sql);
    }

    /**
     * 
     * @param int $companyId
     * @param int $pfpTypeID
     * @param Pagination $pagination
     * 
     * @return boolean|Pfp[]
     */
    public function getUnAssignPFP2Type($companyId, $pfpTypeID, Pagination $pagination = null)
    {
        $query = "SELECT * FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2COMPANY . " pfp2c ON pfp.id = pfp2c.pfp_id " .
                "LEFT JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE (pfp2t.pfp_type_id != {$this->db->sqltext($pfpTypeID)} OR pfp2t.pfp_type_id IS NULL) " .
                "AND pfp2c.is_available = 1 " .
                "AND pfp2c.company_id = {$this->db->sqltext($companyId)} 
				 AND pfp.id NOT IN (
									SELECT id FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE pfp2t.pfp_type_id = {$this->db->sqltext($pfpTypeID)})";

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= " AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " description LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        $query .= " GROUP BY pfp.id ORDER BY description";

        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        $this->db->query($query);
        $rows = $this->db->fetch_all_array();

        if ($this->db->num_rows() == 0) {
            return false;
        }
        $pfpProducts = array();
        $pfpManager = new PFPManager($this->db);
        foreach ($rows as $row) {
            $pfp = $pfpManager->getPfp($row["id"]);
            $pfpProducts[] = $pfp;
        }
        return $pfpProducts;
    }

    /**
     * 
     * @param int $companyId
     * @param int $pfpTypeID
     * 
     * @return int
     */
    public function getUnAssignPFP2TypeCount($companyId, $pfpTypeID)
    {
        $query = "SELECT count(*) count FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2COMPANY . " pfp2c ON pfp.id = pfp2c.pfp_id " .
                "LEFT JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE (pfp2t.pfp_type_id != {$this->db->sqltext($pfpTypeID)} OR pfp2t.pfp_type_id IS NULL) " .
                "AND pfp2c.is_available = 1 " .
                "AND pfp2c.company_id = {$this->db->sqltext($companyId)} 
				 AND pfp.id NOT IN (
									SELECT id FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE pfp2t.pfp_type_id = {$this->db->sqltext($pfpTypeID)})";

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= " AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " description LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        $this->db->query($query);
        $count = $this->db->fetch(0);
        return $count->count;
    }
}
?>