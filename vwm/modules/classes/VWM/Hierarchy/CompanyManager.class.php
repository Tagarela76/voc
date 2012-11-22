<?php

namespace VWM\Hierarchy;

class CompanyManager {
    
    public function getCompanyList($productCategory = NULL) {
        
        if (isset($productCategory) && $productCategory!= 0) {
			$sql = "SELECT * ".
					"FROM " . TB_COMPANY . " c" .
					" LEFT JOIN " . TB_COMPANY2INDUSTRY_TYPE . " c2it ON c2it.company_id = c.company_id " .
					"WHERE c2it.industry_type_id={$this->db->sqltext($productCategory)}";
			$this->db->query($sql);
		} else {
            $this->db->query("SELECT * FROM ".TB_COMPANY." ORDER BY name");
        }
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$company=array(
					'id'			=>	$data->company_id,
					'name'			=>	$data->name,
					'address'		=>	$data->address,
					'contact'		=>	$data->contact,
					'phone'			=>	$data->phone
				);
				$companies[]=$company;
			}
		}
		
		return $companies;
	}
}
?>
