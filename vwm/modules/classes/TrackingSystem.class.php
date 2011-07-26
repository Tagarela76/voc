<?php
	/*	Tracking System Main Class
	 * 		Children: 
	 * 		TrackManager 	- administration
	 * 		Trash 			- represents record at DB
	 * */
	class TrackingSystem {
		
		const TRASH_TB_NAME = 'trash_bin';	//	trash table name
		
		const ALL_DEPENDENCIES = 0;
		const HIDDEN_DEPENDENCIES = 1;
		const VISIBLE_DEPENDENCIES = 2;
		
		private $success;	//	last operation result 
		private $db;
		
				
    	function TrackingSystem($db=null) {
    		$this->db=$db;    		
    	}
    	
        //TODO: trackAutocomplete doesn't work
    	public function trackAutocomplete($occurrence)
    	{	
    		//	no facility is bad..					
			$query = "SELECT `data`, LOCATE('$occurrence',`data`) AS occurrence	FROM trash_bin";

			$this->db->query($query);			
			if ($this->db->num_rows() > 0) 
			{
				$trackData = $this->db->fetch_all_array();
				foreach ($trackData as $value) 
				{
					if ($value['occurrence']) {
						$trackRow = array (
							"productNR"		=>	$value['data'],
							"occurrence"	=>	$value['occurrence']
						);
					}
					$results[] = $trackRow;						
				}		
				return (isset($results)) ? $results : false;								
			} else 
			return false;
    	}	
    	
    	public function isLastOperationSuccessful() {
    		return $this->success;
    	}
    	
    	protected function setSuccess($success) {
    		$this->success = $success;
    	}
    
    	
    	
    	// dependencies
    	protected function loadDependencies() {
    		return array (
    			'company' => array (
    							'field'=>'company_id',
								'foreignField'=>'company_id', 
								///'tables'=>array('1M'=>'facility, product2company', 'M1'=>'gcg_list')
								//'tables'=>array('1M'=>'product2company', 'M1'=>'gcg_list')
								'tables' => array(									
									'1M' => array('visible2user'=>'facility, accessory', 'hidden'=>'product2company'),
									'M1' => array('hidden'=>'gcg_list'),
								)
    						),
    			'facility' => array (
    							'field'=>'facility_id',
								'foreignField'=>'facility_id', 
								//'tables'=>array('1M'=>'department', 'M1'=>'company')
								///'tables'=>array('1M'=>'mix', 'M1'=>'company') //Mix is depended due to voc limits
								'tables' => array(									
									'1M' => array('visible2user'=>'department, inventory'),
									'M1' => array('visible2user'=>'company'),
								)
							),
											
    			'department' => array (
    							'field'=>'department_id', 
								'foreignField'=>'department_id', 
								///'tables' => array('1M'=>'mix, equipment', 'M1'=>'facility')
								//'tables' => array('1M'=>'mix', 'M1'=>'facility')	//Mix is depended due to voc limits
								'tables' => array(									
									'1M' => array('visible2user'=>'mix, equipment'),
									'M1' => array('visible2user'=>'facility'),
								)
							),
											    			
    			'equipment' => array (
    							'field'=>'equipment_id', 
								'foreignField'=>'equipment_id', 
								//'tables' => array('1M'=>'mix', 'M1'=>'department')//	- stable. Mix is depended due to voc limits
								///'tables' => array('M1'=>'department')	//	- experimantal
								'tables' => array(									
									'1M' => array('visible2user'=>'mix'),
									'M1' => array('visible2user'=>'department, inventory'),
								)
							),
											
//    			'inventory' => array (
//								'field'=>'inventory_id', 
//								'foreignField'=>'inventory_id', 
//								//'tables'=>array('1M'=>'equipment, productgroup', 'M1'=>'facility')//	- stable
//							///	'tables'=>array('1M'=>'productgroup', 'M1'=>'facility')	//	- experimental
//								'tables' => array(									
//									'1M' => array('visible2user'=>'equipment', 'hidden'=>'productgroup'),
//									'M1' => array('visible2user'=>'facility'),
//								)
//							),
							
				'inventory' => array (
								'field'=>'id', 
								'foreignField'=>'inventory_id', 								
								'tables' => array(									
									'1M' => array('visible2user'=>'equipment', 'hidden'=>'material2inventory, accessory2inventory'),
									'M1' => array('visible2user'=>'facility'),
								)
							),
											    			    			    		
    			'mix' => array (
								'field'=>'mix_id', 
								'foreignField'=>'mix_id', 
								//'tables'=>array('1M'=>'mixgroup, mix2mix_limit, waste', 'M1'=>'department')
								'tables' => array(									
									'1M' => array('hidden'=>'mixgroup, mix2mix_limit, waste'),
									'M1' => array('visible2user'=>'department, equipment'),
								)
							),
											
    			'mixgroup' => array (
								'field'=>'mixgroup_id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'mix, product'),
								'tables' => array(									
									'M1' => array('visible2user'=>'mix, product'),
								), 
								'updatePerAnus'=>'mix'
							),
											    			
    			'mix2mix_limit'	=> array (	
								'field'=>'id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'mix')
								'tables' => array(									
									'M1' => array('visible2user'=>'mix'),
								)
							),
											
    			'waste'	=> array (	
								'field'=>'id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'mix')
								'tables' => array(									
									'M1' => array('visible2user'=>'mix'),
								),
							),
											
    			'productgroup' => array (
								'field'=>'productgroup_id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'inventory, product'),
								'tables' => array(									
									'M1' => array('visible2user'=>'inventory, product'),
								),
								'updatePerAnus'=>'inventory'
							),
											
    			'product' => array (
								'field'=>'product_id', 
								'foreignField'=>'product_id', 
//								'tables'=>array('1M'=>'components_group, mixgroup, product2chemical_class, product2company, productgroup', //	msds?
//												'M1'=>'coating, supplier'
//												)
								'tables' => array(	
									'1M' => array('hidden'=>'components_group, mixgroup, product2chemical_class, product2company, productgroup'),	//	msds?							
									'M1' => array('visible2user'=>'coating, supplier'),
								),

							),
							
				'components_group' => array (
								'field'=>'component_group_id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'product, substrate, rule, component')
								'tables' => array(																
									'M1' => array('visible2user'=>'product, substrate, rule, component'),
								),								
							),  		
							
				'product2chemical_class' => array (
								'field'=>'id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'product, hazardous_class')
								'tables' => array(																
									'M1' => array('visible2user'=>'product, hazardous_class'),
								),
							),  				
							
				'product2company' => array (
								'field'=>'id', 
								'foreignField'=>'', 
								//'tables'=>array('M1'=>'product, company')
								'tables' => array(																
									'M1' => array('visible2user'=>'product, company'),
								),
							),		
				'gcg_list' => array (
								'field'=>'gcg_id', 
								'foreignField'=>'gcg_id', 
								//'tables'=>array('1M'=>'company')
								'tables' => array(																
									'1M' => array('visible2user'=>'company'),
								),
							),
				
				'material2inventory' => array(
								'field'=>'id', 
								'foreignField'=>'material2inventory_id', 
								'tables' => array(																
									'1M' => array('hidden'=>'use_location2material'),
									'M1' => array('visible2user'=>'inventory, product'),
								),
								'updatePerAnus'=>'inventory'
				),
				
				'accessory2inventory' => array(
								'field'=>'id', 
								'foreignField'=>'', 
								'tables' => array(									
									'M1' => array('visible2user'=>'inventory, product, accessory'),
								),
								'updatePerAnus'=>'inventory'
				),
				
				'use_location2material' => array(
								'field'=>'id', 
								'foreignField'=>'', 
								'tables' => array(									
									'M1' => array('visible2user'=>'department', 'hidden'=>'material2inventory'),
								),
								'updatePerAnus'=>'material2inventory'
				),	
				
				'accessory'	=> array(
								'field'=>'id',
								'foreignField'=>'',
								'tables' => array(
									'1M' => array('hidden'=>'accessory2inventory'),
									'M1' => array('visible2user'=>'company'),
								),
									
				),							
									
    		);
    	}    	    	    	    	    	
    	
    	
    	
    	
    	//	convert xnyo db record obj to associative array
    	public static function properties2array($dataRows) {
    		$records = array();
    		//	dataRows - by $db->fetch_all();
    		if (is_array($dataRows)) {
	    		foreach ($dataRows as $dataRow) {		    		
		    		$records[] = self::_properties2array($dataRow);	
	    		}
	    			
    		//	dataRows - by $db->fetch($i);
    		} else {
    			$records[] = self::_properties2array($dataRows);	
    		}	    	
	    	return $records;				    	
		}
		
		
		
		
		public static function XNYO2Trash($db, $dataRows) {			
			$objects = array();
    		//	dataRows - by $db->fetch_all();
    		if (is_array($dataRows)) {
	    		foreach ($dataRows as $dataRow) {		    		
		    		$objects[] = self::_XNYOObj2TrashObj($db, $dataRow);	
	    		}
	    			
    		//	dataRows - by $db->fetch($i);
    		} else {
    			$objects[] = self::_XNYOObj2TrashObj($db, $dataRows);	
    		}	    	
	    	return $objects;		
		}
		
		
		
		
		public static function getTrashByID($db, $id) {
			$query = "SELECT * FROM ".self::TRASH_TB_NAME." WHERE id = ".$id."";
			$db->query($query);
			if ($db->num_rows() > 0) {
				$data = $db->fetch(0);
				
				$obj = new Trash($db);
				$obj->setID($data->id);
				$obj->setTable($data->table_name);		
				$obj->setData($data->data);
				$obj->setUserID($data->user_id);
				$obj->setCRUD($data->CRUD);
				$obj->setDate($data->date);	//	current time						
				$obj->setReferrer($data->referrer);
				
				return $obj;
			} else {
				return false;
			}
		}
		
		
		
		
		private static function _properties2array($dataRow) {
			$record = array();
			foreach ($dataRow as $property=>$value) {
			    $record[$property] = $value;
		    }
		    return $record;
		} 	
		
		
		
		
		private static function _XNYOObj2TrashObj($db, $dataRow) {
			$obj = new Trash($db);						
			$obj->setData(json_encode(self::_properties2array($dataRow)));			
		    return $obj;
		} 	
	}
?>