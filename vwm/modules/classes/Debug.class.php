<?php
class Debug{
	
	private $max_memory;
	
	function Debug(){
		
		$this->max_memory = ini_get("memory_limit");
	}
	
	public function printMicrotime($line = "",$file = __FILE__ ){
		
    	$totalMemory = memory_get_usage(TRUE);
    	$usageMemory = xdebug_memory_usage();
    	
    	if(($totalMemory - ($totalMemory / 10)) < $usageMemory) //10%
    	{
    		$warningMemory = true;
    	}else{
    		$warningMemory = false;
    	}
    	
    	echo "<br/>MEMORY LIMIT: " . $this->max_memory;
    	echo "<br/><span style='color:Green;'>Allocated Memory: </span><span style='color:Black;font-weight:bold;'>" . $totalMemory . "</span>";
    	if(!$warningMemory){
    	echo "<br/><span style='color:Green;'> MEMORY USAGE</span>: <span style='color:Black;font-weight:bold;'>". xdebug_memory_usage() . "</span>";
    	}else{
    		echo "<br/><span style='color:Red;font-weight:Bold;'> MEMORY USAGE</span>: <span style='color:Black;font-weight:bold;'>". xdebug_memory_usage() . "</span>";
    	}
    	echo "<br/><span style='color:Green;'> TIME: </span><span style='color:Black;font-weight:bold;'>". xdebug_time_index(). "</span>";
    	echo "<br/>" . $file . "<span style='color:Green;'> ON LINE: </span><span style='color:Black;font-weight:bold;'>" . $line. "</span>";
    	echo "<br/>";
    }
	
}
?>