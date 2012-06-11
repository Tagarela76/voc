<?php

class Calculator {

    function Calculator() {
    }
    
    public function calculateVocNew ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult,$recycleResult)
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
    	
		$withoutRecycle =  ($vocWeight+$default)*$percentWithoutWaste/100;
		$withRecycle = $withoutRecycle - ($withoutRecycle*$recycleResult['recyclePercent'])/100;

    	return $withRecycle;
    }
    
    public function calculateVocwx ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult,$recycleResult) {
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
    	
		$withoutRecycle =  ($vocWeight+$default)*$percentWithoutWaste/100;
		$withRecycle = $withoutRecycle - ($withoutRecycle*$recycleResult['recyclePercent'])/100;

    	return $withRecycle;
    }
    
    public function calculateVoclx ($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult,$recycleResult) {
    	$vocWeight=0;     
    	
    	$percentWithoutWaste=100-$wasteResult['wastePercent'];
    	
    	for ($i=0; $i<count($ArrayWeight); $i++) {
    		$vocWeight += ($ArrayWeight[$i]['percent']/100) * $ArrayWeight[$i]['weight'];    		  		    		    	
    	} 
    	
    	$vocVolume=0;
    	
    	for ($i=0; $i<count($ArrayVolume); $i++) {
    		$vocVolume += $ArrayVolume[$i]['voclx'] * $ArrayVolume[$i]['volume']; 	   	    		
    	} 
    	
    	$unitTypeConverter = new UnitTypeConverter($defaultType);
    	$default= $unitTypeConverter->convertToDefault($vocVolume, "lb");        	
    	
		$withoutRecycle =  ($vocWeight+$default)*$percentWithoutWaste/100;
		$withRecycle = $withoutRecycle - ($withoutRecycle*$recycleResult['recyclePercent'])/100;

    	return $withRecycle;
    }
         
     
     private function calculatePercentByValue($value, $total) {
     	return ($value*100)/$total;
     }
          
     private function calculateValueByPercent($percent, $total) {
     	return ($percent*$total)/100;
     }
}
?>