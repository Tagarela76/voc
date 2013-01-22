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
			throw new \Exception("Unable to read file ".$pathToCsv);
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
	 * @param bool $glue shoud method glue text in the whole column. For example,
	 * row[0] is "Unit", row[1] is "Type". glue true will return "Unit Type"
	 * @return array
	 */
	public function getTableHeader($rowCount = 2, $glue = true) {		
		$rows = array();
		$columnCount = 0;

		for($i=0;$i<$rowCount;$i++) {
			$row = $this->readCsvRow();
			//	get max column count
			$columnCount = (count($row) > $columnCount) ? count($row) : $columnCount;
			$rows[] = $row;
		}

		if($glue) {
			$columns = array();
			$output = array();
			//	group by columns
			foreach ($rows as $row) {
				for($i=0;$i<$columnCount;$i++) {
					$columns[$i][] = $row[$i];
				}
			}
			foreach ($columns as $column) {
				$output[] = trim(implode(' ', $column));
			}
			return $output;
		} else {
			return $rows;
		}
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
