<?php

class ProductInventory extends InventoryNew {
	
	
	private $product_id;			
	private $in_stock_unit_type = self::GALLON_UNIT_TYPE_ID;	
		
	private $product_nr;
	

	const GALLON_UNIT_TYPE_ID = 1;

	public function __construct(db $db, Array $array = null) {
		parent::__construct($db, $array);
	}
	

	
	public function save() {

                if ($this->id != NULL){
                        $query = "UPDATE product2inventory SET 
						in_stock = '".mysql_escape_string($this->in_stock)."',
						amount = '".mysql_escape_string($this->amount)."',
						in_stock_unit_type = '".mysql_escape_string($this->in_stock_unit_type)."',
						inventory_limit = '".mysql_escape_string($this->limit)."'
						WHERE inventory_id = {$this->id}";
                }
                else {                        
   
                            $query = "INSERT INTO product2inventory VALUES (NULL,'"
												.($this->product_id)."', NULL,'" 
												
												.($this->facility_id)."','" 
                                                .($this->in_stock)."','" 
												.($this->limit)."','"
												.($this->in_stock_unit_type)."','"
												.($this->amount). "')";
                                               

                }	

		$this->db->query($query);
			
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	 
	
	public function get_in_stock_unit_type(){
		return $this->in_stock_unit_type;
	}
	
	
	
	public function get_product_nr(){
		return $this->product_nr;
	}
	
	public function get_product_id(){
		return $this->product_id;
	}
	
	
	
	public function set_product_nr($value) {
		try {
			$this->product_nr = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}
	
	
	public function set_product_id($value) {
		try {
			$this->product_id = $value;
		} catch(Exception $e) {
			throw new Exception("Product id cannot be empty!" . $e->getMessage());
		}
	}
	
	
	public function set_in_stock_unit_type($value) {

			$this->in_stock_unit_type = $value;

	}	
	
	

}

?>
