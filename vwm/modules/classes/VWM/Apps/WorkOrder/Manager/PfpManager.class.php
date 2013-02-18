<?php

namespace VWM\Apps\WorkOrder\Manager;

use VWM\Framework\Manager;
use VWM\Apps\WorkOrder\Entity\Pfp;

/**
 * PfpManager controls pfp-related processes
 */
class PfpManager extends Manager
{

    /**
     * @inheritdoc
     */
    protected $criteria = array(
        'companyId'     => false,
        'industryType'  => false,
        'supplierId'    => false,
        'search'        => array(),
    );

    /**
     * Find all allowed pfps. Allowed Pfp is that Pfp which is open to use for
     * company. Company should assign it to department to let department use it
     */
    public function findAllAllowed()
    {
        $db = \VOCApp::getInstance()->getService('db');

        $query = "SELECT pfp.id, pfp.description, pfp.company_id " .
				"FROM ".Pfp::TABLE_NAME." pfp " .
                $this->applyJoin(). " " .
				$this->applyWhere(). " ";

    }

    private function applyJoin()
    {
        $join = array();

        $join[] = "JOIN ".Pfp::TABLE_PFP2PRODUCT." pfp2p " .
                "ON pfp2p.preformulated_products_id = pfp.id";

		if (count($this->getCriteria('search')) > 0
                || $this->getCriteria('industryType') !== false
                || $this->getCriteria('supplierId') !== false) {
            $join[] = "JOIN ".TB_PRODUCT." p ON p.product_id = pfp2p.product_id";
		}

		if ($this->getCriteria('companyId') !== false) {
            $join[] = "JOIN ".Pfp::TABLE_PFP2COMPANY." pfp2c " .
                    "ON pfp2c.pfp_id = pfp.id";
		}

		if ($this->getCriteria('industryType') !== false) {
            $join[] = "JOIN ".TB_PRODUCT2INDUSTRY_TYPE." p2t " .
                    "ON p.product_id = p2t.product_id";
		}

		if ($this->getCriteria('supplierId') !== false) {
			 $join[] = "JOIN ".TB_SUPPLIER." s " .
                    "ON p.supplier_id = s.supplier_id";
		}

		return implode(" ", $join);
    }

    protected function applyWhere()
    {
        $where = array();
        // TODO: continue in the same style as apply join

    }

}
