<?php

class UnitTypeConverter {

	private $defaultType = "us gallon";
	private $defaultTime = "min";
	const BOXCOUNT = 100;

    function UnitTypeConverter($defaultType = "us gallon") {
    	$this->defaultType = $defaultType;
    }

    public function toDefault($value, $sourceType){
    	switch ($sourceType){
    		//volume
    		case "uk gallon":
    			$out=array (
    				"value" => 1.200949926*$value,
    				"type" => "volume"
    				);
    			break;
    		case "liter":
    			$out=array (
    				"value" => 0.264172052*$value,
    				"type" => "volume"
    				);
    			break;
    		case "milliliter":
    			$out=array (
    				"value" => 0.000264172*$value,
    				"type" => "volume"
    				);
    			break;
    		case "U.S. customary fluid ounce":
    			$out=array (
    				"value" => 0.0078125*$value,
    				"type" => "volume"
    				);
    			break;
    		case "Imperial fluid ounce":
    			$out=array (
    				"value" => 0.007505937*$value,
    				"type" => "volume"
    				);
    			break;
    		case "U.S. dry gallon":
    			$out=array (
    				"value" => 1.164*$value,
    				"type" => "volume"
    				);
    			break;
    		case "cm3":
    			$out=array (
    				"value" => 0.000264*$value,
    				"type" => "volume"
    				);
    			break;
    		case "Pint":
    			$out=array (
    				"value" => 0.125*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Quart":
    			$out=array (
    				"value" => 0.25*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Barrel":
    			$out=array (
    				"value" => 42*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Centiliter":
    			$out=array (
    				"value" => 0.00264172*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Deciliter":
    			$out=array (
    				"value" => 0.02641721*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Dekaliter":
    			$out=array (
    				"value" => 2.641721*$value,
    				"type" => "volume"
    			);
    			break;
    		case "Hectoliter":
    			$out=array (
    				"value" => 26.4172*$value,
    				"type" => "volume"
    			);
    			break;
    		case "kiloliter":
    			$out=array (
    				"value" => 264.1721*$value,
    				"type" => "volume"
    			);
    			break;
    		case "U.S. Dry Bushel":
    			$out=array (
    				"value" => 9.309177*$value,
    				"type" => "volume"
    			);
    			break;
    		case "British bushel":
    			$out=array (
    				"value" => 9.607619*$value,
    				"type" => "volume"
    			);
    			break;
    		//weight
    		case "lb":
    			$out=array (
    				"value" => $value,
    				"type" => "weight"
    			);
    			break;
    		case "grain":
    			$out=array (
    				"value" => 0.000143*$value,
    				"type" => "weight"
    			);
    			break;
    		case "oz":
    			$out = array (
    				"value" => 0.0625*$value,
    				"type" => "weight"
    			);
    			break;
    		case "gram":
    			$out=array (
    				"value" => 0.002207*$value,
    				"type" => "weight"
    			);
    			break;
    		case "milligram":
    			$out=array (
    				"value" => 0.000002207*$value,
    				"type" => "weight"
    			);
    			break;
    		case "ton":
    			$out=array (
    				"value" => 2206.999205*$value,
    				"type" => "weight"
    			);
    			break;
    		case "kilogram":
    			$out=array (
    				"value" => 2.206999*$value,
    				"type" => "weight"
    			);
    			break;
    		case "U.S. CWT":
    			$out=array (
    				"value" => 100*$value,
    				"type" => "weight"
    			);
    			break;
    		case "British CWT":
    			$out=array (
    				"value" => 112*$value,
    				"type" => "weight"
    			);
    			break;
    		case "Dram":
    			$out=array (
    				"value" => 0.003906*$value,
    				"type" => "weight"
    			);
    			break;
    		case "Hectogram":
    			$out=array (
    				"value" => 0.2205*$value,
    				"type" => "weight"
    			);
    			break;
    		default:
    			$out=array (
    				"value" => $value,
    				"type" => "volume"
    			);
    	}

    	return $out;
    }




    public function fromDefaultVolume($value,$destinationType){
    	switch ($destinationType){
    		case "uk gallon":
    			$value=0.832674185*$value;
    			break;
    		case "liter":
    			$value=3.785411784*$value;
    			break;
    		case "milliliter":
    			$value=3785.411784*$value;
    			break;
    		case "U.S. customary fluid ounce":
    			$value=128*$value;
    			break;
    		case "Imperial fluid ounce":
    			$value=133.2*$value;
    			break;
    		case "U.S. dry gallon":
    			$value=0.8594*$value;
    			break;
    		case "cm3":
    			$value=3785.411784*$value;
    			break;
    		case "Pint":
    			$value=8*$value;
    			break;
    		case "Quart":
    			$value=4*$value;
    			break;
    		case "Barrel":
    			$value=0.02381*$value;
    			break;
    		case "Centiliter":
    			$value=378.5412*$value;
    			break;
    		case "Deciliter":
    			$value=37.85412*$value;
    			break;
    		case "Dekaliter":
    			$value=0.3785412*$value;
    			break;
    		case "Hectoliter":
    			$value=0.03785412*$value;
    			break;
    		case "kiloliter":
    			$value=0.003785412*$value;
    			break;
    		case "U.S. Dry Bushel":
    			$value=0.1074209*$value;
    			break;
    		case "British bushel":
    			$value=0.1040841*$value;
    			break;
    	}
    	return $value;
    }

    public function fromDefaultWeight($value, $destinationType){
    	switch ($destinationType){
    		//case "lb":
    		//	$value=2.204622622*$value;
    		//	break;
    		case "grain":
    			$value = 7000*$value;
    			break;
    		case "oz":
    			$value = 16*$value;
    			break;
    		case "gram":
    			$value = 453.59237*$value;
    			break;
    		case "milligram":
    			$value = 453592.37*$value;
    			break;
    		case "ton":
    			$value = 0.000454*$value;
    			break;
    		case "kilogram":
    			$value = 0.45359237*$value;
    			break;
    		case "U.S. CWT":
    			$value = 0.01*$value;
    			break;
    		case "British CWT":
    			$value = 0.008929*$value;
    			break;
    		case "Dram":
    			$value = 256*$value;
    			break;
    		case "Hectogram":
    			$value = 4.536*$value;
    			break;
    	}
    	return $value;
    }

	public function convertCelsiusToFahrenheit($tempC){
		$tempF = round($tempC*0.556 + 32);
		return $tempF;
	}

	public function convertFahrenheitToCelsius($tempF){
	//	$tempC = round(($tempF - 32)*0.556);
		$tempC = ($tempF - 32)*0.556;
		return $tempC;
	}

	public function convertFromToOld($value, $sourceType, $destinationType, $density = false) {
    	if ($destinationType=='KILO') $value=1000*$value;
    		else {
    			$defaultValue = $this->toDefault($value, $sourceType);
    			//$dest = $this->toDefault($value, $destinationType);
    			//var_dump($defaultValue); die;
    			if ($defaultValue["type"] == "weight") {

    				$value = $this->fromDefaultWeight($defaultValue["value"], $destinationType);
    				if ($density) {
    					//	Volume = Weight/Density
    					$defaultValue["value"] = $value/$density;
    				}
    			}
    			$value = $this->fromDefaultVolume($defaultValue["value"], $destinationType);
    		}
    	return $value;
    }

    public function convertFromTo($value, $from, $to, $densityValue = false, $densityType = false) {
    	$fromType = $this->toDefault($value, $from);
    	$toType = $this->toDefault($value, $to);

    	if ($fromType['type'] == $toType['type']){
    		if ($fromType['type'] == 'volume') {
    			$value = $this->fromDefaultVolume($fromType['value'], $to);
			} else {
    			$value = $this->fromDefaultWeight($fromType['value'], $to);
    		}
    	} else {
    		if ($fromType['type'] == 'volume') {
    			// convert Density Type to default
    			$numeratorToDefault = $this->toDefault(1, $densityType['numerator']);
    			$denominatorToDefault = $this->toDefault(1, $densityType['denominator']);

    			// calculate Weight
    			$value = $fromType['value']*$densityValue*($numeratorToDefault['value']/$denominatorToDefault['value']);

    			// convert Weight to destination type
    			$value = $this->fromDefaultWeight($value, $to);
    		} else {
    			// convert Density Type to default
    			$numeratorToDefault = $this->toDefault(1, $densityType['numerator']);
    			$denominatorToDefault = $this->toDefault(1, $densityType['denominator']);

    			// calculate Volume
    			$value = $fromType['value']/($densityValue*($numeratorToDefault['value']/$denominatorToDefault['value']));

    			// convert Volume to destination type
    			$value = $this->fromDefaultVolume($value, $to);
    		}
    	}

    	return $value;
    }

    public function convertToDefault ($value, $sourceType, $density = false, $densityType = false) {
    	return $this->convertFromTo($value, $sourceType, $this->defaultType, $density, $densityType);
    }

    public function setDefaultType($defaultType) {
    	$this->defaultType = $defaultType;
    }

    public function getDefaultType() {
    	return $this->defaultType;
    }


	/**
	 * Convert density unittype
	 * @param int|float $value to convert
	 * @param Density $from
	 * @param Density $to
	 * @param Unittype $unittype object which is responsible for ID to unitype mapping
	 * @return boolean|float 
	 */
    public function convertDensity($value, Density $from, Density $to, Unittype $unittype)
	{
		$numerator = $this->convertFromTo($value, $unittype->getDescriptionByID($from->getNumerator()),
				$unittype->getDescriptionByID($to->getNumerator()));

		$denominator = $this->convertFromTo(1, $unittype->getDescriptionByID($from->getDenominator()),
				$unittype->getDescriptionByID($to->getDenominator()));

		if($denominator == 0) {
			return false;
		}

		return $numerator/$denominator;
	}




	public function convertWeightToVolume($quantity, $unitTypeDetails, $density) {

    }

	public function convertDefaultTime($time, $type) {
		switch ($type) {
			case "min":
				break;
			case "hour":
				$time = $time*60;
				break;
			case "days":
				$time = $time*1440;
				break;
		}
		return $time;
	}
	
	public function convertDefaultCount($count, $type) {
		switch ($type) {
			case "each":
				break;
			case "pr":
				$count = $count*2;
				break;
			case "box":
				$count = $count*100;
				break;
		}
		return $count;
	}
	
	public function convertTimeFromTo($from, $to, $value){
		// default time in minutes
		$defaultTime = $this->convertDefaultTime($value, $from);
		switch ($to) {
			case "min":
				$time = $defaultTime;
				break;
			case "hour":
				$time = $defaultTime/60;
				break;
			case "days":
				$time = $defaultTime/1440;
				break;
		}
		return $time;
		
	}
	
	public function convertCountFromTo($from, $to, $value){
		$defaultCount = $this->convertDefaultCount($value, $from);
		switch ($to) {
			case "ea":
				$count = $defaultCount;
				break;
			case "pr":
				$count = $defaultCount/2;
				break;
			case "box":
				$count = $defaultCount/  self::BOXCOUNT;
				break;
			default;
				break;
		}
		return $count;
	}
}
?>