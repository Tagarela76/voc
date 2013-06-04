<?php

namespace VWM\Hierarchy;

class CompanyManager
{
    public function getCompanyList($productCategory = NULL)
    {
        $db = \VOCApp::getInstance()->getService('db');

        if (isset($productCategory) && $productCategory != 0) {
            $sql = "SELECT * " .
                    "FROM " . TB_COMPANY . " c" .
                    " LEFT JOIN " . TB_COMPANY2INDUSTRY_TYPE . " c2it ON c2it.company_id = c.company_id " .
                    "WHERE c2it.industry_type_id={$db->sqltext($productCategory)}";
            $db->query($sql);
        } else {
            $db->query("SELECT * FROM " . TB_COMPANY . " ORDER BY name");
        }

        if ($db->num_rows()) {
            for ($i = 0; $i < $db->num_rows(); $i++) {
                $data = $db->fetch($i);
                $company = array(
                    'id' => $data->company_id,
                    'name' => $data->name,
                    'address' => $data->address,
                    'contact' => $data->contact,
                    'phone' => $data->phone
                );
                $companies[] = $company;
            }
        }

        return $companies;
    }

}
?>
