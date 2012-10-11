<?php

namespace VWM\Import;

class CsvHelper {
	
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
	
	public function openCsvFile($pathToCsv) {
		if($this->getCsvFileResourse()) {
			$this->closeCsvFile();			
		}
		
		$fileResourse = fopen($pathToCsv, "r");
		if(!$fileResourse) {
			throw new Exception("Unable to read file ".$pathToCsv);
		}
		
		$this->setCsvFileResourse($fileResourse);		
	}
	
	
	public function readCsvRow() {
		$fileResourse = $this->getCsvFileResourse(); 
		if($fileResourse) {
			return fgetcsv($fileResourse, 1000, $this->fieldDelimiter);	
		} else {
			return false;
		}		
	}
	
	
	public function closeCsvFile() {		
		$fileResourse = $this->getCsvFileResourse();
		if($fileResourse) {
			fclose($fileResourse);
			$this->setCsvFileResourse(false);
		}		
	}
	
	
	/**
	 * @param int $rowCount row count in the table header. By default is 2
	 * @return array
	 */
	public function getTableHeader($rowCount = 2) {

		$header = array();
		for($i=0;$i<$rowCount;$i++) {
			$header[] = $this->readCsvRow();
		}
		
		return $header;
	}
	
	public function getFileContent() {

		$body = array();
		$fileResourse = $this->getCsvFileResourse(); 
		if($fileResourse) {
			$i = 1 ; // row's counter
			while($resourse = fgetcsv($fileResourse, 1000, $this->fieldDelimiter)) {
				if ($i>2) {
					$body[] = $resourse;
				}
				$i++;
			}
		} else {
			return false;
		}
		$this->closeCsvFile();
		
		
		return $body;
	}
}

?>
