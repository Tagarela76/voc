<?php

interface iCurrency {
    
}

class VPSCurrency implements iCurrency {
   
    /**     
     * @var db
     */
    private $db;
    
    public $id;
	public $iso;
    public $sign;
    public $description;
    
    
    public function __construct(db $db, $id = null) {
        $this->db = $db;
        if ($id !== null) {
			$this->id = $id;
			$sql = "SELECT * FROM ".TB_VPS_CURRENCY." WHERE id = ".mysql_escape_string($id);
			$this->db->query($sql);
			if ($this->db->num_rows() == 0) {
				throw new Exception('Cannot load currency id '.$id);				
			}

			$row = $this->db->fetch(0);
			foreach($row as $property=>$value) {
			    if (property_exists($this, $property)) {
					$this->$property = $value;
			    }
			}
        }
    }
    
}

?>
