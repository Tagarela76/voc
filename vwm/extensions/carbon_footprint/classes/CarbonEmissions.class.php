<?php

class CarbonEmissions {
	
	private $db;
	
	public $id;
	public $emission_factor_id;
	public $description;
	public $adjustment;
	public $quantity;
	public $unittype_id;
	public $tco2;
	public $facility_id;
	public $month;
	public $year;
	public $certificate_value;
	public $credit_value;
	
	public $emissionFactor; //EmissionFactor
	

    function CarbonEmissions($db, $id = null) {
    	$this->db = $db;
    	if ($id != null) {
    		$this->id = $id;
    		$this->load();
    	}
    }
    
    public function load($id = null) {
    	if ($id != null) {
    		$this->id = $id;
    	}
    	$query = "SELECT * FROM ".TB_CARBON_EMISSIONS." WHERE id = '$this->id' LIMIT 0,1";
    	$this->db->query($query);
    	$data = $this->db->fetch(0);
    	foreach($data as $property => $value) {
    		if (property_exists($this,$property)) {
    			$this->$property = $value;
    		}
    	}
    	$this->emissionFactor = new EmissionFactor($this->db, $this->emission_factor_id);
    }
    
    public function delete($id = null) {
    	if ($id != null) {
    		$this->id = $id;
    	}
    	$query = "DELETE FROM ".TB_CARBON_EMISSIONS." WHERE id = $this->id LIMIT 1";
    	$this->db->query($query);
    }
    
    public function save() {
    	$unittypeConverter = new UnitTypeConverter();
    	$unittype = new Unittype($this->db);
    	$quantityInNeedUnittype=  $unittypeConverter->convertFromTo($this->quantity,$unittype->getDescriptionByID($this->unittype_id),$unittype->getDescriptionByID($this->emissionFactor->unittype_id));
    	$adjustmentInNeedUnittype=$unittypeConverter->convertFromTo($this->adjustment,$unittype->getDescriptionByID($this->unittype_id),$unittype->getDescriptionByID($this->emissionFactor->unittype_id));
    	$quantityOfConsumption = $quantityInNeedUnittype+$adjustmentInNeedUnittype;
    	if ($this->emission_factor_id == EmissionFactor::ELECTRICITY_FACTOR_ID) {
    		$quantityOfConsumption += $this->certificate_value - $this->credit_value;
    	}
    	$this->tco2 = $quantityOfConsumption*$this->emissionFactor->emission_factor*CarbonFootprint::CONST_FACTOR;
    	$tmp = new CarbonEmissions($this->db);
    	foreach ($this as $property => $value) {
    		if (property_exists($tmp,$property) && $property !='id') {
    			$tmp->$property = (is_null($value) || $value == '')?"'0'":"'$value'";
    		}
    	}
    	if ($this->id != null) {
    		$query = "UPDATE ".TB_CARBON_EMISSIONS." SET emission_factor_id = $tmp->emission_factor_id, description = $tmp->description," .
    				" adjustment = $tmp->adjustment, quantity = $tmp->quantity, unittype_id = $tmp->unittype_id, tco2 = $tmp->tco2, " .
    				"facility_id = $tmp->facility_id, certificate_value = $tmp->certificate_value," .
    				" credit_value = $tmp->credit_value WHERE id = $this->id";
    	} else {
    		$query = "INSERT INTO ".TB_CARBON_EMISSIONS." (id, emission_factor_id, description, adjustment, quantity, unittype_id, tco2, " .
    				" facility_id, month, year, certificate_value, credit_value) VALUES (NULL, $tmp->emission_factor_id, $tmp->description, " .
    				" $tmp->adjustment, $tmp->quantity, $tmp->unittype_id, $tmp->tco2, $tmp->facility_id, $tmp->month, " .
    				" $tmp->year, $tmp->certificate_value, $tmp->credit_value)";
    	}
    	
    	$this->db->query($query);
    }    
    

}
?>