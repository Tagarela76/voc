<?php
/**
 * PFP - pre formulated product
 */
class PFP
{
    /**
     * @var db
     */
    private $db;

	private $id;
	private $description;
	private $company_id;
	private $last_update_time;

    private $products;

	function __construct($PFPProductsArray) {
		$this->products = $PFPProductsArray;

	}


    public function __set($property, $value) {
        $methodName = "set_".$property;
        if(method_exists($this, $methodName)) {
            $this->$methodName($value);
        }
    }

    public function __get($property) {
        $methodName = "get_".$property;
        if(method_exists($this, $methodName)) {
            $this->$methodName();
        }
    }


    /**
     * PFP::products where public one day and there are still some places in VWM where PFP->products is called
     * That is why I need this method
     * @return PFPProduct[]
     */
    public function get_products() {
        return $this->getProducts();
    }


    /**
     * Setter for PFP Products
     * This actually the best place to sorts array in a correct way - primary product should be always on top
     * @param PFPProduct[] $PFPProductsArray
     * @return bool
     */
    public function set_products($PFPProductsArray) {
        if (!is_array($PFPProductsArray)) {
            return false;
        }

        if (!($PFPProductsArray[0] instanceof PFPProduct)) {
            return false;
        }

        $j = 1;
        $count = count($PFPProductsArray);
        for($i=0; $i<$count; $i++) {
            if($PFPProductsArray[$i]->isPrimary()){
                $res[0] = $PFPProductsArray[$i];
            } else {
                $res[$j] = $PFPProductsArray[$i];
                $j++;
            }
        }
        ksort($res);
        $this->products = $res;
        return true;
    }

	public function getProducts() {
        return $this->products;
		$products = array();
		if (isset($this->products)) {
			foreach ($this->products as $item) {
				$product['product_nr'] = $item->product_nr;
				$product['name'] = $item->name;
				$products[] = $product;
			}
		}

		return $products;
	}

	public function getProductsCount() {
		return isset($this->products) ? count($this->products) : "0";
	}

	public function getId() {
		return $this->id;
	}

	public function setID($value){
		$this->id = $value;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getRatio($htmlFormatting = true) {
		$res = array();
        foreach ($this->products as $product) {
            if($product->isPrimary() && $htmlFormatting){
                $res[] = "<b>".$product->getRatio()."</b>";
            } else {
                $res[] = $product->getRatio();
            }
        }
		return implode(':', $res);
	}


	public function setDescription($val) {
		$this->description = $val;
	}

	public function save() {

	}

	public function toJson() {
		$arr = array("descrtiption"=>$this->description, "id" => $this->id, "ratio" => $this->getRatio(), "products" => $this->products);
		return json_encode($arr);
	}

	public function setCompanyID($company_id) {
		$this->company_id = $company_id;
	}

	public function getCompanyID() {
		return $this->company_id;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

	public function getLastUpdateTime() {
		return $this->last_update_time;
	}
}
?>