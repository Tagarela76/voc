<?php
class XLSBuilder {
	
	function XLSBuilder($xmlFileName, $reportType) {
		$extraVar = array(
			'commaSeparator' => ',',
			'textDelimiter'	=> '"'
		);    	
		
		$csv = new CSVBuilder($xmlFileName, $reportType,$extraVar,TRUE);
				
		require_once("modules/excel.php");
		//$inputFile="report.csv";
		$inputFile = $csv->getCsvFileName();
		$xlsFile="/tmp/output.xls";			
		$handle = fopen( $inputFile, "r" );
		if( !is_resource($handle) ) {
    		die("Error opening $inputFile\n" );
		}
		
		/* Assuming that first line is column headings */
		if (($columns = fgetcsv($handle, 1024)) == false ) {
    		print( "Error, couldn't get header row\n" );
    		exit(-2);
		}
		
		$numColumns = count($columns);
		for( $i=0; $i<$numColumns;$i++ ) {
			$columns[$i] = "col" . $i;
		}
		$xlsArray = array();
		while (($rows = fgetcsv($handle, 1024)) != FALSE ) {
    		$rowArray = array();
    		for($i=0; $i<$numColumns;$i++) {
        		$key = $columns[$i];
        		$val = $rows[$i];
        		$rowArray[$key] = $val;
    		}
    		$xlsArray[] = $rowArray;
    		unset($rowArray);
		}
		fclose($handle);
		
		$xlsFile = "xlsfile:/".$xlsFile;
		$fOut = fopen( $xlsFile, "wb" );
		if( !is_resource($fOut) ) {
    		die( "Error opening $xlsFile\n" );
		}
		fwrite($fOut, serialize($xlsArray));
		fclose($fOut);
		header ("Content-Type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=\"sample.xls\"" );
		readfile($xlsFile);
	}
	
	private function parseCSV($str,$s,$d) {		
		$quote = FALSE;
		$element = "";
		for ($i=0;$i<strlen($str);$i++) {
			//check quote
			if ($str{$i} == $d) {
				if (!$quote) {
					$quote = TRUE;
				} else {
					$quote = FALSE;
				}
			} else {								
				if (!$quote && $str{$i} == $s) {
					$elements[] = $element;
					$element = "";
				} else {
					$element .= $str{$i};
				}	
			}									
		}
		$elements[] = $element;		
		return $elements;
	}        
}
?>
