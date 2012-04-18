<?php

class Sort {

	private $tableParent;	
	private $db;
	private $defaultSortNum;
	
    function Sort($db,$tableParent,$defaultSortNum) {
    	$this->db=$db;
    	$this->tableParent=$tableParent;
    	$this->defaultSortNum=$defaultSortNum;
    }
    
    public function getSubQuerySort($numSort = null)
    {    	
    	if (!isset($numSort))
    		$numSort=$this->defaultSortNum;
    		
    	$subQuery=" ORDER BY ";
    	switch($this->tableParent)
    	{    		
    		case 'department':    		
    		switch($numSort)
    		{
    			case 1:$subQuery.=" department_id ASC";
    			break;
    			case 2:$subQuery.=" department_id DESC";
    			break;
    			case 3:$subQuery.=" name ASC";
    			break;
    			case 4:$subQuery.=" name DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
		
    		case 'contacts':    		
    		switch($numSort)
    		{
    			case 1:$subQuery.=" c.company ASC";
    			break;
    			case 2:$subQuery.=" c.company DESC";
    			break;
    			case 3:$subQuery.=" c.contact ASC";
    			break;
    			case 4:$subQuery.=" c.contact DESC";
    			break;
    			case 5:$subQuery.=" c.zip_code ASC";
    			break;
    			case 6:$subQuery.=" c.zip_code DESC";
    			break;
    			case 7:$subQuery.=" c.country_id ASC";
    			break;
    			case 8:$subQuery.=" c.country_id DESC";
				break;	
    			case 9:$subQuery.=" c.paint_supplier ASC";
    			break;
    			case 10:$subQuery.=" c.paint_supplier DESC";
    			break;
    			case 11:$subQuery.=" c.paint_system ASC";
    			break;
    			case 12:$subQuery.=" c.paint_system DESC";
    			break;
    			case 13:$subQuery.=" c.jobber ASC";
    			break;
    			case 14:$subQuery.=" c.jobber DESC";
    			break;
 
    			default:$subQuery="";
    			break;  
    		}
    		break;		
    		
    		case 'logbook':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" date ASC";
    			break;
    			case 2:$subQuery.=" date DESC";
    			break;
    			case 3:$subQuery.=" type ASC";
    			break;
    			case 4:$subQuery.=" type DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'mix':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" mix_id ASC";
    			break;
    			case 2:$subQuery.=" mix_id DESC";
    			break;
    			case 3:$subQuery.=" description ASC";
    			break;
    			case 4:$subQuery.=" description DESC";
    			break;
    			case 5:$subQuery.=" voc ASC";
    			break;
    			case 6:$subQuery.=" voc DESC";
    			break;
    			case 7:$subQuery.=" creation_time ASC";
    			break;
    			case 8:$subQuery.=" creation_time DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'chemicalProduct':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" p.product_id ASC";
    			break;
    			case 2:$subQuery.=" p.product_id DESC";
    			break;
    			case 3:$subQuery.=" s.supplier ASC";
    			break;
    			case 4:$subQuery.=" s.supplier DESC";
    			break;
    			case 5:$subQuery.=" p.product_nr ASC";
    			break;
    			case 6:$subQuery.=" p.product_nr DESC";
    			break;
    			case 7:$subQuery.=" p.name ASC";
    			break;
    			case 8:$subQuery.=" p.name DESC";
    			break;
    			case 9:$subQuery.=" coat.coat_desc ASC";
    			break;
    			case 10:$subQuery.=" coat.coat_desc DESC";
    			break;
    			case 11:$subQuery.=" p.voclx ASC";
    			break;
    			case 12:$subQuery.=" p.voclx DESC";
    			break;
    			case 13:$subQuery.=" p.vocwx ASC";
    			break;
    			case 14:$subQuery.=" p.vocwx DESC";
    			break;
    			case 15:$subQuery.=" p.percent_volatile_weight ASC";
    			break;
    			case 16:$subQuery.=" p.percent_volatile_weight DESC";
    			break;
    			case 17:$subQuery.=" p.percent_volatile_volume ASC";
    			break;
    			case 18:$subQuery.=" p.percent_volatile_volume DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'accessory':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" id ASC";
    			break;
    			case 2:$subQuery.=" id DESC";
    			break;
    			case 3:$subQuery.=" name ASC";
    			break;
    			case 4:$subQuery.=" name DESC";
    			break;
    			case 7:$subQuery.=" jname ASC";
    			break;
    			case 8:$subQuery.=" jname DESC";
    			break;			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'equipment':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" equipment_id ASC";
    			break;
    			case 2:$subQuery.=" equipment_id DESC";
    			break;
    			case 3:$subQuery.=" equip_desc ASC";
    			break;
    			case 4:$subQuery.=" equip_desc DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'inventory':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" id ASC";
    			break;
    			case 2:$subQuery.=" id DESC";
    			break;
    			case 3:$subQuery.=" name ASC";
    			break;
    			case 4:$subQuery.=" name DESC";
    			break;
    			case 5:$subQuery.=" description ASC";
    			break;
    			case 6:$subQuery.=" description DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
		
    		case 'orders':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" io.order_id ASC";
    			break;
    			case 2:$subQuery.=" io.order_id DESC";
    			break;
    			case 3:$subQuery.=" io.order_amount ASC";
    			break;
    			case 4:$subQuery.=" io.order_amount DESC";
    			break;
    			case 5:$subQuery.=" io.order_name ASC";
    			break;
    			case 6:$subQuery.=" io.order_name DESC";
    			break; 
    			case 7:$subQuery.=" io.order_status ASC";
    			break;
    			case 8:$subQuery.=" io.order_status DESC";
    			break;	
    			case 9:$subQuery.=" io.order_created_date ASC";
    			break;
    			case 10:$subQuery.=" io.order_created_date DESC";
    			break;			
    			default:$subQuery="";
    			break;  
    		}
    		break;		
		
    		case 'discounts':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" product_id ASC";
    			break;
    			case 2:$subQuery.=" product_id DESC";
    			break;
    			case 3:$subQuery.=" supplier ASC";
    			break;
    			case 4:$subQuery.=" supplier DESC";
    			break;
    			case 5:$subQuery.=" product_nr ASC";
    			break;
    			case 6:$subQuery.=" product_nr DESC";
    			break;
    			case 7:$subQuery.=" discount ASC";
    			break;
    			case 8:$subQuery.=" discount DESC";
    			break;
    			default:$subQuery=" product_id ASC";
    			break;  
    		}
    		break;	
		
    		case 'clients':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" c.company_id DESC";
    			break;
    			case 2:$subQuery.=" c.company_id ASC";
    			break;
    			case 3:$subQuery.=" di.discount ASC";
    			break;
    			case 4:$subQuery.=" di.discount DESC";
    			break;

    			break;  
    		}
    		break;		
		
    		case 'productsPrice':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" p.product_id DESC";
    			break;
    			case 2:$subQuery.=" p.product_id ASC";
    			break;
    			case 3:$subQuery.=" price ASC";
    			break;
    			case 4:$subQuery.=" price DESC";
    			break;
   			
    			default:$subQuery="";
    			break;  
    		}
    		break;	
		
    		case 'gomPrice':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" a.name DESC";
    			break;
    			case 2:$subQuery.=" a.name ASC";
    			break;
    			case 3:$subQuery.=" price ASC";
    			break;
    			case 4:$subQuery.=" price DESC";
    			break;
    			case 6:$subQuery.=" a.id ASC";
    			break;
    			case 5:$subQuery.=" a.id DESC";
    			break;			
   			
    			default:$subQuery="";
    			break;  
    		}
    		break;		
    		
    		case 'chemicalInventory':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" i.id ASC";
    			break;
    			case 2:$subQuery.=" i.id DESC";
    			break;
    			case 3:$subQuery.=" i.name ASC";
    			break;
    			case 4:$subQuery.=" i.name DESC";
    			break;
    			case 5:$subQuery.=" i.description ASC";
    			break;
    			case 6:$subQuery.=" i.description DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'wasteStorage':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" s.name ASC";
    			break;
    			case 2:$subQuery.=" s.name DESC";
    			break;
    			case 3:$subQuery.=" s.capacity_volume ASC";
    			break;
    			case 4:$subQuery.=" s.capacity_volume DESC";
    			break;
    			case 5:$subQuery.=" s.density ASC";
    			break;
    			case 6:$subQuery.=" s.density DESC";
    			break;
    			case 7:$subQuery.=" s.max_period ASC";
    			break;
    			case 8:$subQuery.=" s.max_period DESC";
    			break;
    			case 9:$subQuery.=" s.suitability ASC";
    			break;
    			case 10:$subQuery.=" s.suitability DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
		
    		case 'burner':    		
    		switch($numSort)
    		{
    			case 1:$subQuery.=" burner_id ASC";
    			break;
    			case 2:$subQuery.=" burner_id DESC";
    			break;
    			case 3:$subQuery.=" model ASC";
    			break;
    			case 4:$subQuery.=" model DESC";
    			break;
    			case 5:$subQuery.=" serial ASC";
    			break;
    			case 6:$subQuery.=" serial DESC";
    			break;
    			case 7:$subQuery.=" manufacturer ASC";
    			break;
    			case 8:$subQuery.=" manufacturer DESC";
				break;	
    			case 9:$subQuery.=" input ASC";
    			break;
    			case 10:$subQuery.=" input DESC";
    			break;
    			case 11:$subQuery.=" output ASC";
    			break;
    			case 12:$subQuery.=" output DESC";
    			break;
    			case 13:$subQuery.=" btu ASC";
    			break;
    			case 14:$subQuery.=" btu DESC";
    			break;
 
    			default:$subQuery="";
    			break;  
    		}
    		break;	
		
   		case 'nox':    		
    		switch($numSort)
    		{
    			case 1:$subQuery.=" nox_id ASC";
    			break;
    			case 2:$subQuery.=" nox_id DESC";
    			break;
    			case 3:$subQuery.=" description ASC";
    			break;
    			case 4:$subQuery.=" description DESC";
    			break;
    			case 5:$subQuery.=" serial ASC";
    			break;
    			case 6:$subQuery.=" serial DESC";
    			break;
    			case 7:$subQuery.=" manufacturer ASC";
    			break;
    			case 8:$subQuery.=" manufacturer DESC";
				break;	
    			case 9:$subQuery.=" input ASC";
    			break;
    			case 10:$subQuery.=" input DESC";
    			break;
    			case 11:$subQuery.=" output ASC";
    			break;
    			case 12:$subQuery.=" output DESC";
    			break;
    			case 13:$subQuery.=" btu ASC";
    			break;
    			case 14:$subQuery.=" btu DESC";
    			break;
 
    			default:$subQuery="";
    			break;  
    		}
    		break;			
    		
    		//----------------------------------------------------------------------------------//
    		//									ADMIN											//
    		//----------------------------------------------------------------------------------//    		
    		case 'coat':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" coat_id ASC";
    			break;
    			case 2:$subQuery.=" coat_id DESC";
    			break;
    			case 3:$subQuery.=" coat_desc ASC";
    			break;
    			case 4:$subQuery.=" coat_desc DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'components':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" cas ASC";
    			break;
    			case 2:$subQuery.=" cas DESC";
    			break;
    			case 3:$subQuery.=" EINECS ASC";
    			break;
    			case 4:$subQuery.=" EINECS DESC";
    			break;
    			case 5:$subQuery.=" description ASC";
    			break;
    			case 6:$subQuery.=" description DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'agency':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" a.agency_id ASC";
    			break;
    			case 2:$subQuery.=" a.agency_id DESC";
    			break;
    			case 3:$subQuery.=" a.name_us ASC";
    			break;
    			case 13:$subQuery.=" a.name_eu ASC";
    			break;
    			case 14:$subQuery.=" a.name_cn ASC";
    			break;
    			case 4:$subQuery.=" a.name_us DESC";
    			break;
    			case 15:$subQuery.=" a.name_eu DESC";
    			break;
    			case 16:$subQuery.=" a.name_cn DESC";
    			break;
    			case 5:$subQuery.=" a.description ASC";
    			break;
    			case 6:$subQuery.=" a.description DESC";
    			break;
    			case 7:$subQuery.=" c.name ASC";
    			break;
    			case 8:$subQuery.=" c.name DESC";
    			break;
    			case 9:$subQuery.=" a.location ASC";
    			break;
    			case 10:$subQuery.=" a.location DESC";
    			break;    
    			case 11:$subQuery.=" c.contact_info ASC";
    			break;
    			case 12:$subQuery.=" c.contact_info DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'country':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" country_id ASC";
    			break;
    			case 2:$subQuery.=" country_id DESC";
    			break;
    			case 3:$subQuery.=" name ASC";
    			break;
    			case 4:$subQuery.=" name DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'rule':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" rule_nr_us ASC";
    			break;
    			case 2:$subQuery.=" rule_nr_us DESC";
    			break;
    			case 3:$subQuery.=" rule_nr_eu ASC";
    			break;
    			case 4:$subQuery.=" rule_nr_eu DESC";
    			break;
    			case 5:$subQuery.=" rule_nr_cn ASC";
    			break;
    			case 6:$subQuery.=" rule_nr_cn DESC";
    			break;
    			case 7:$subQuery.=" rule_desc ASC";
    			break;
    			case 8:$subQuery.=" rule_desc DESC";
    			break;
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'supplier':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" s.supplier_id ASC";
    			break;
    			case 2:$subQuery.=" s.supplier_id DESC";
    			break;
    			case 3:$subQuery.=" s.supplier ASC";
    			break;
    			case 4:$subQuery.=" s.supplier DESC";
    			break;
    			case 5:$subQuery.=" s.contact_person ASC";
    			break;
    			case 6:$subQuery.=" s.contact_person DESC";
    			break;
    			case 7:$subQuery.=" s.phone ASC";
    			break;
    			case 8:$subQuery.=" s.phone DESC";
    			break;
    			case 9:$subQuery.=" s.address ASC";
    			break;
    			case 10:$subQuery.=" s.address DESC";
    			break;
    			case 11:$subQuery.=" c.name ASC";
    			break;
    			case 12:$subQuery.=" c.name DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'product':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" p.product_id ASC";
    			break;
    			case 2:$subQuery.=" p.product_id DESC";
    			break;    			
    			case 3:$subQuery.=" p.product_nr ASC";
    			break;
    			case 4:$subQuery.=" p.product_nr DESC";
    			break;
    			case 5:$subQuery.=" p.name ASC";
    			break;
    			case 6:$subQuery.=" p.name DESC";
    			break;
    			case 7:$subQuery.=" coat.coat_desc ASC";
    			break;
    			case 8:$subQuery.=" coat.coat_desc DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
    		
    		case 'users':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" user_id ASC";
    			break;
    			case 2:$subQuery.=" user_id DESC";
    			break;    			
    			case 3:$subQuery.=" username ASC";
    			break;
    			case 4:$subQuery.=" username DESC";
    			break;
    			case 5:$subQuery.=" accessname ASC";
    			break;
    			case 6:$subQuery.=" accessname DESC";
    			break;    			
    			default:$subQuery="";
    			break;  
    		}
    		break;
		
    		case 'logging':
    		switch($numSort)
    		{
    			case 1:$subQuery.=" ul.log_id ASC";
    			break;
    			case 2:$subQuery.=" ul.log_id DESC";
    			break;    			
    			case 3:$subQuery.=" ul.user_id ASC";
    			break;
    			case 4:$subQuery.=" ul.user_id DESC";
    			break;
    			case 5:$subQuery.=" ul.action ASC";
    			break;
    			case 6:$subQuery.=" ul.action DESC";
    			break;  
    			case 7:$subQuery.=" ul.date ASC";
    			break;
    			case 8:$subQuery.=" ul.date DESC";
    			break; 	
			
    			case 9:$subQuery.=" ul.user_id ASC";
    			break;
    			case 10:$subQuery.=" ul.user_id DESC";
    			break; 			
    			default:$subQuery="";
    			break;  
    		}
    		break;		
    		
    	}
    	return $subQuery;
    }   
}
?>