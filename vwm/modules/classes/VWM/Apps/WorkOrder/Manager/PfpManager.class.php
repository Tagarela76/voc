<?php

namespace VWM\Apps\WorkOrder\Manager;

use VWM\Framework\Manager;
use VWM\Framework\Cache\DbCacheDependency;
use VWM\Apps\WorkOrder\Entity\Pfp;

/**
 * PfpManager controls pfp-related processes
 */
class PfpManager extends Manager
{

	const TB_PERFORMULATED_PRODUCT = "preformulated_products";
	const TB_PFP_2_COMPANY = "pfp2company";

	/**
	 * @inheritdoc
	 */
	protected $criteria = array(
		'companyId' => false,
		'industryType' => false,
		'supplierId' => false,
		'pfpType' => false,
		'search' => array(),
	);


	/**
	 *fuction for getting all allowed pfp Count by company Id
	 * @return type
	 */
	public function getPfpAllowedCount()
	{
		$db = \VOCApp::getInstance()->getService('db');
		$queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_available = 1 ";

		$query = "SELECT pfp.id " .
				"FROM " . Pfp::TABLE_NAME . " pfp " .
				$this->applyJoin() . " " .
				$this->applyWhere($queryFilter)." ".
				"GROUP BY pfp.id";
		$db->query($query);

		$pfpCount = $db->num_rows();
		return $pfpCount;
	}

	/**
	 * fuction for getting assigned pfp Count by company Id
	 * @return type
	 */
	public function getPfpAssignedCount()
	{
		$db = \VOCApp::getInstance()->getService('db');
		$queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_assigned = 1 ";

		$query = "SELECT pfp.id " .
				"FROM " . Pfp::TABLE_NAME . " pfp " .
				$this->applyJoin() . " " .
				$this->applyWhere($queryFilter)." ".
				"GROUP BY pfp.id";
		$db->query($query);

		$pfpCount = $db->num_rows();

		return $pfpCount;
	}

	/**
	 *Find all allowed pfps. Allowed Pfp is that Pfp which is open to use for
	 * company. Company should assign it to department to let department use it
	 * @param bool $isAvailable
	 * @param \Pagination $pagination
	 * @return \VWM\Apps\WorkOrder\Entity\Pfp[]
	 */
	public function findAllPfps($isAvailable = 1, \Pagination $pagination = null)
	{
		//check if available
		if($isAvailable == 1){
			$queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_available = 1 ";
		}else{
			$queryFilter = " AND pfp.id = pfp2c.pfp_id AND pfp2c.is_assigned = 1 ";
		}

		$query = "SELECT pfp.id, pfp.description, pfp.company_id, pfp.is_proprietary " .
				"FROM " . Pfp::TABLE_NAME . " pfp " .
				$this->applyJoin() . " " .
				$this->applyWhere($queryFilter) . " GROUP BY pfp.id";
		if (isset($pagination)) {
			$query .= " ORDER BY pfp.description LIMIT " . $pagination->getLimit() ." ".
					  "OFFSET " . $pagination->getOffset() . "";

		}

		return $this->_processGetPFPListQuery($query);
	}

	/**
	 * function for gettin datebase wich we needs
	 *
	 * @return type string
	 */
	protected function applyJoin()

	{
		$join = array();

		$join[] = "LEFT JOIN " . Pfp::TABLE_PFP2PRODUCT . " pfp2p " .
				"ON pfp2p.preformulated_products_id = pfp.id";

		if (count($this->getCriteria('search')) > 0
				|| $this->getCriteria('industryType') !== false
				|| $this->getCriteria('supplierId') !== false) {

			$join[] = "LEFT JOIN " . TB_PRODUCT . " p ".
					  "ON p.product_id = pfp2p.product_id";

		}

		if ($this->getCriteria('companyId') !== false) {
			$join[] = "LEFT JOIN " . Pfp::TABLE_PFP2COMPANY . " pfp2c " .
					"ON pfp2c.pfp_id = pfp.id";
		}

		if ($this->getCriteria('pfpType') !== false) {
			$join[] = "LEFT JOIN " . TB_PFP2PFP_TYPES . " pfp2t ".
					  "ON pfp.id = pfp2t.pfp_id ";
		}

		if ($this->getCriteria('industryType') !== false) {
			$join[] = "LEFT JOIN " . TB_PRODUCT2INDUSTRY_TYPE . " p2t " .
					"ON p.product_id = p2t.product_id";
		}

		if ($this->getCriteria('supplierId') !== false) {
			$join[] = "LEFT JOIN " . TB_SUPPLIER . " s " .
					"ON p.supplier_id = s.supplier_id";
		}

		return implode(" ", $join);
	}


	/**
	 *function for getting Where condition
	 *
	 * @param string|null $queryFilter
	 *
	 * @return type string
	 */
	protected function applyWhere($queryFilter = null)
	{
		$whereCondition = "";
		$where = array();

		if ($this->getCriteria('companyId') !== false) {
			$where[] = "pfp2c.company_id = " . $this->getCriteria('companyId');
		}

		if ($this->getCriteria('industryType') !== false) {
			$where[] = "p2t.industry_type_id = " . $this->getCriteria('industryType');
		}

		if ($this->getCriteria('supplierId') !== false) {
			$where[] = "s.supplier_id = " . $this->getCriteria('supplierId');
		}

		if ($this->getCriteria('pfpType') !== false) {
			$where[] = "pfp2t.pfp_type_id = " . $this->getCriteria('pfpType');
		}

		if (count($this->getCriteria('search')) > 0 ||
			$this->getCriteria('industryType') != 0 ||
			$this->getCriteria('supplierId') != 0) {

			$where[] = "p.product_id = pfp2p.product_id";
		}

		if (!empty($where)) {
			$whereCondition = "WHERE ";
			$whereCondition .= implode(" AND ", $where);
		}

		if (count($this->getCriteria('search')) > 0) {
			$searchSql = array();
			$whereCondition .= " AND ( ";
			foreach ($this->getCriteria('search') as $pfp) {
				$searchSql[] = " pfp.description LIKE ('%" . $pfp . "%') " .
						"OR p.name LIKE ('%" . $pfp . "%')";
			}
			$whereCondition .= implode(' OR ', $searchSql);
			$whereCondition .= ") ";
		}
		if ($queryFilter) {
			$whereCondition.=$queryFilter;
		}
		return $whereCondition;
	}

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $sql = "SELECT * " .
                "FROM ".Pfp::TABLE_NAME." " .
                "WHERE id = {$db->sqltext($id)}";

        $pfps = $this->_processGetPFPListQuery($sql);
        if (count($pfps) == 0) {

            return false;
        }

        return $pfps[0];
    }

	/**
	 * Process SQL query which gets list of PFPs
     *
	 * @param string $query
     *
	 * @return \VWM\Apps\WorkOrder\Entity\Pfp[]
	 */
	private function _processGetPFPListQuery($query)
	{
		$db = \VOCApp::getInstance()->getService('db');

		$pfps = array(); //Array of objects PFP

		//	try to read from cache
		$cache = \VOCApp::getInstance()->getCache();

		$key = md5('query' . $query);
		if ($cache) {
			$pfps = $cache->get($key);
			if ($pfps) {
				return $pfps;
			}
		}

		$db->query($query);

		//Init PFPProducts for each PFP...
		$pfpArray = $db->fetch_all_array();
		$count = count($pfpArray);

		for ($i = 0; $i < $count; $i++) {

			$PFPProductsArray = array();

			$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " ".
								"WHERE preformulated_products_id = " . $pfpArray[$i]['id']. " " .
                                "ORDER BY isPrimary DESC";
			$db->query($getProductsQuery);
			$products = $db->fetch_all_array();

			$isRangePFP = false;
			foreach ($products as $p) {

				if (!is_null($p['ratio_to']) &&
					!is_null($p['ratio_from_original']) &&
					!is_null($p['ratio_to_original'])) {
					$isRangePFP = true;
				}
				$prodtmp = new \PFPProduct($db);
				$prodtmp->setRatio($p['ratio']);
				$prodtmp->initializeByID($p['product_id']);
				$prodtmp->setIsPrimary($p['isPrimary']);
				$PFPProductsArray[] = $prodtmp;
			}

			$pfp = new Pfp();
			$pfp->setID($pfpArray[$i]['id']);
			$pfp->setDescription($pfpArray[$i]['description']);
			$pfp->setIsProprietary($pfpArray[$i]['is_proprietary']);
			$pfp->products = $PFPProductsArray;
			$pfp->isRangePFP = $isRangePFP;
			$pfps[] = $pfp;
		}

		//save to cache
		if ($cache) {
			$sqlDependency = "SELECT MAX(last_update_time) FROM " . TB_PFP . "";
			$cache->set($key, $pfps, 86400, new DbCacheDependency($db, $sqlDependency));
		}

		return $pfps;
	}

}
