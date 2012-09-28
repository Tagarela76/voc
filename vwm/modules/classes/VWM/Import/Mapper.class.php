<?php

namespace VWM\Import;

abstract class Mapper {
	
	private $csvFileResourse;
	
	private $fieldDelimiter = ";";

	public function getCsvFileResourse() {
		return $this->csvFileResourse;
	}

	public function getFieldDelimiter() {
		return $this->fieldDelimiter;
	}

	public function setCsvFileResourse($csvFileResourse) {
		$this->csvFileResourse = $csvFileResourse;
	}

	/**
	 * This method should be implemented by child classes
	 * @return array key => value
	 */
	public function getMap() {		
	}
	
	/**
	 * Maps CSV columns to real properties
	 * @param string $pathToCsv
	 * @return array of objects
	 * 
	 * @todo FINISH ME
	 */
	public function doMapping($pathToCsv) {
		$this->_openCsvFile($pathToCsv);
		
		//	read first two lines - they are the header
		$header = $this->_getTableHeader();
		
		// now let's do actual mapping
		for ($i=0;$i<count($header[1]);$i++) {
			//
		}
	}
	
	/**
	 * @param int $rowCount row count in the table header. By default is 2
	 * @return array
	 */
	private function _getTableHeader($rowCount = 2) {
		$header = array();
		for($i=0;$i<$rowCount;$i++) {
			$header[] = $this->_readCsvRow();
		}
		
		return $header;
	}
	
	
	private function _openCsvFile($pathToCsv) {
		if($this->getCsvFileResourse()) {
			$this->_closeCsvFile();			
		}
		
		$fileResourse = fopen($pathToCsv, "r");
		if(!$fileResourse) {
			throw new Exception("Unable to read file ".$pathToCsv);
		}
		
		$this->setCsvFileResourse($fileResourse);		
	}
	
	
	private function _readCsvRow() {
		$fileResourse = $this->getCsvFileResourse();
		if($fileResourse) {
			return fgetcsv($fileResourse, 1000, $this->fieldDelimiter);	
		} else {
			return false;
		}		
	}
	
	
	private function _closeCsvFile() {		
		$fileResourse = $this->getCsvFileResourse();
		if($fileResourse) {
			fclose($fileResourse);
			$this->setCsvFileResourse(false);
		}		
	}
}

?>
