<?php

class Calculator {

    function Calculator() {
    }
    
    public function calculateVocNew ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult)
    {
    	$vocWeight=0;     
    	
    	$percentWithoutWaste=100-$wasteResult['wastePercent'];
    	
    	for ($i=0; $i<count($ArrayWeight); $i++) {
    		$vocWeight += ($ArrayWeight[$i]['percent']/100) * $ArrayWeight[$i]['weight'];    		  		    		    	
    	} 
    	
    	$vocVolume=0;
    	
    	for ($i=0; $i<count($ArrayVolume); $i++) {
    		$vocVolume += $ArrayVolume[$i]['vocwx'] * $ArrayVolume[$i]['volume']; 	   	    		
    	} 
    	
    	$unitTypeConverter = new UnitTypeConverter($defaultType);
    	$default= $unitTypeConverter->convertToDefault($vocVolume, "lb");        	
    	
    	return ($vocWeight+$default)*$percentWithoutWaste/100;
    }
    
    public function calculateVoc ($percentVolatileWeightArray, $quantityArray, $waste = 0) {
    	$voc = 0;
    	
    	//	get waste percent from total mix quantity 
    	$wastePercent = $this->calculatePercentByValue($waste, array_sum($quantityArray));    	
    	
    	for ($i=0; $i<count($quantityArray); $i++) {
    		$voc += ($percentVolatileWeightArray[$i]/100) * ($quantityArray[$i] - $this->calculateValueByPercent($wastePercent, $quantityArray[$i]));   		    		    	
    	} 
	
    	return $voc;
    }
    
    public function calculateVocwx ($vocwxArray, $quantityArray, $waste = 0) {
    	$vocwx = 0;
    	$quantitiesSum = 0;
    	$quantityArrayWithWaste = array();
    	
    	//	get waste percent from total mix quantity 
    	$wastePercent = $this->calculatePercentByValue($waste, array_sum($quantityArray));
    	
    	for ($i=0; $i<count($quantityArray); $i++) {
    		//$vocwxOld += $vocwxArray[$i]*$quantityArray[$i];
    		//$quantitiesSum += $quantityArray[$i];
    		
    		//calculate quantity without waste
    		$quantityArrayWithWaste[$i] = ($quantityArray[$i] - $this->calculateValueByPercent($wastePercent, $quantityArray[$i]));    		
    		$vocwx += $vocwxArray[$i] * $quantityArrayWithWaste[$i];
    		
    		$quantitiesSum += $quantityArrayWithWaste[$i];
    	}
    	
    	$vocwx /= $quantitiesSum;
    	
    	return $vocwx;
    }
    
    public function calculateVoclx ($voclxArray, $quantityArray, $waste = 0) {
    	$voclx = 0;
    	$quantitiesSum = 0;
    	$quantityArrayWithWaste = array();
    	
    	//	get waste percent from total mix quantity 
    	$wastePercent = $this->calculatePercentByValue($waste, array_sum($quantityArray));
    	
    	for ($i=0; $i<count($quantityArray); $i++) {
    		//$voclxOld += $voclxArray[$i]*$quantityArray[$i];
    		//$quantitiesSum += $quantityArray[$i];
    		
    		//calculate quantity without waste
    		$quantityArrayWithWaste[$i] = ($quantityArray[$i] - $this->calculateValueByPercent($wastePercent, $quantityArray[$i]));    		
    		$voclx += $voclxArray[$i] * $quantityArrayWithWaste[$i];
    		
    		$quantitiesSum += $quantityArrayWithWaste[$i];
    	}
    	
    	$voclx /= $quantitiesSum;
    	
    	return $voclx;
    }
         
     
     private function calculatePercentByValue($value, $total) {
     	return ($value*100)/$total;
     }
          
     private function calculateValueByPercent($percent, $total) {
     	return ($percent*$total)/100;
     }
}
?>