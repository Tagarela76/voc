<?php
class NewProductRequest {
    /**
     *
     * @var db
     */
    private $db;
    
    private $product_id;
    private $supplier;
    private $name;
    private $description;
    /**
     *
     * @var DateTime
     */
    private $date;
    private $user_id;
    private $status;
    
    private $errors = array();
    
    const STATUS_NEW = 'new';
    
    public function __construct(db $db) {
        $this->db = $db;
        $this->setDate(new DateTime());
        $this->user_id = $_SESSION['user_id'];
        $this->status = self::STATUS_NEW;
    }

    public function setProductId($product_id){
        $this->product_id = $product_id;
    }

    public function setDate(DateTime $date) {
        $this->date = $date;
    }
    
    public function setSupplier($supplier) {
        $this->supplier = $supplier;
    }
    
    public function setName($name) {
        $this->name = $name; 
    }


    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function setStatus($status) {
        $this->status = $status;        
    }


    public function getProductID() {
		return $this->product_id;
    }

    public function getSupplier() {
		return $this->supplier;		
    }

    public function getName() {
		return $this->name;
    }

    public function getDescription() {
	   return $this->description;
    }

    public function getDate() {
		return $this->date;
    }

    public function getUserID() {
		return $this->user_id;    
    }

    
    
    
    public function validate($product) {
        $result["summary"] = "true";
		
		$result["productSupplier"] = "failed";
		if (isset($product["productSupplier"])) {
			$product["productSupplier"] = trim($product["productSupplier"]);
			$supplierLength = strlen($product["productSupplier"]);
			
			if ($supplierLength > 0 && $supplierLength < 120) {
				$result["productSupplier"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		
		$result["productId"] = "failed";
		if (isset($product["productId"])) {
			$product["productId"] = trim($product["productId"]);
			$idLength = strlen($product["productId"]);
			
			if ($idLength > 0 && $idLength < 20) {
				$result["productId"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		
                $result["productName"] = "failed";
		if (isset($product["productName"])) {
			$product["productName"] = trim($product["productName"]);
			$nameLength = strlen($product["productName"]);
			
			if ($nameLength > 0 && $nameLength < 120) {
				$result["productName"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
                
                $result["productDescription"] = "failed";
		if (isset($product["productDescription"])) {
			$product["productDescription"] = trim($product["productDescription"]);
			$descriptionLength = strlen($product["productDescription"]);
			
			if ($descriptionLength > 0 && $descriptionLength < 120) {
				$result["productDescription"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		return $result;
    }
    
    /*public function getErrors() {
        return $this->errors;
    }*/


    public function save() {
        $query = "INSERT INTO ".TB_NEW_PRODUCT_REQUEST." (supplier, product_id, name, description, date, user_id, status) VALUES (
                '".mysql_escape_string($this->supplier)."',
                '".mysql_escape_string($this->product_id)."',
                '".mysql_escape_string($this->name)."',
                '".mysql_escape_string($this->description)."',
                '".mysql_escape_string($this->date->getTimestamp())."',
                '".mysql_escape_string($this->user_id)."',
                '".mysql_escape_string($this->status)."')";
        $this->db->exec($query);
    }
   
}

?>
