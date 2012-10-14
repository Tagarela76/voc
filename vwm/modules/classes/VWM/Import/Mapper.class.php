<?php

namespace VWM\Import;

abstract class Mapper {
	
	/**
	 * TODO: make protected and add getter
	 * TODO: remove zero index, like mappedData['gyantCribUnitId'][0] -> 
	 *		mappedData['gyantCribUnitId']
	 * 
	 * data after mappig
	 * "column name" = > "value"
	 * @var array
	 */
	public $mappedData = array();

	/**
	 * This method should be implemented by child classes
	 * @return array key => value
	 */
	public function getMap() {		
		throw new \Exception("GetMap should be implemented by children");
	}
	
	/**
	 * TODO: fit 80 symbols
	 * 
	 * Maps CSV columns to real properties
	 * @param string $pathToCsv
	 * @return array of objects
	 */
	public function doMapping($pathToCsv) {
		$csvHelper = new CsvHelper();
		$csvHelper->openCsvFile($pathToCsv);
		
		//	read first two lines - they are the header
		$header = $csvHelper->getTableHeader();
		// now let's do actual mapping
		$mappedData = array();
		for ($i=0;$i<count($header[1]);$i++) {
			$mapping = $this->getMap();
			foreach ($mapping as $mapKey => $mapHeader) {
				if( ($header[1][$i] != "" && in_array(strtoupper(trim($header[0][$i])), $mapHeader) && in_array(strtoupper(trim($header[1][$i])), $mapHeader)) || 
						($header[1][$i] == "" && in_array(strtoupper(trim($header[0][$i])), $mapHeader))) {
					$mappedData[$mapKey][] = $i;
				}
			}
			
		} 
		$this->mappedData = $mappedData;
	}
}

?>
