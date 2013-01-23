<?php

namespace VWM\Import\Process;

use VWM\Import\CsvHelper;
use VWM\Import\EntityBuilder;
use VWM\Apps\Process\Process;
use VWM\Apps\Process\Step;
use VWM\Apps\Process\Resource;

class ProcessUploaderEntityBuilder extends EntityBuilder {

	protected $processes = array();
	protected $pathToCsv;

	public function getPathToCsv() {
		return $this->pathToCsv;
	}

	public function setPathToCsv($pathToCsv) {
		$this->pathToCsv = $pathToCsv;
	}

	public function getProcesses() {
		//$this->buildEntities();
		return $this->processes;
	}

	public function setProcesses($processes) {
		$this->processes = $processes;
	}

	public function buildEntities($pathToCsv = '') {

		if ($pathToCsv == '') {
			$pathToCsv = $this->getPathToCsv();
		}

		if ($pathToCsv == '') {
			throw new \Exception("there is no csv file");
		}

		$csvHelper = new CsvHelper();
		$unittype = new \Unittype($this->db);
		$csvHelper->openCsvFile($pathToCsv);

		$fileData = $csvHelper->getFileContent();

		$processes = array();
		$steps = array();
		$resourses = array();
		$resourseType = array(
			"LABOR"=>1,
			"GOM"=>3,
			"PAINT PRODUCT"=>2,
			"WASTE"=>2
		);

		foreach ($fileData as $data) {

			//	group rows by Process
			if ($data[$this->mapper->mappedData['processName']] == ''
					&& $data[$this->mapper->mappedData['stepNumber']] == ''
					&& $data[$this->mapper->mappedData['optional']] == ''
					&& $data[$this->mapper->mappedData['stepDescription']] == ''
					&& $data[$this->mapper->mappedData['resourceDescription']] == ''
					&& $data[$this->mapper->mappedData['processType']] == ''
					&& $data[$this->mapper->mappedData['qty']] == ''
					&& $data[$this->mapper->mappedData['unitType']] == ''
					&& $data[$this->mapper->mappedData['rate']] == ''
					&& $data[$this->mapper->mappedData['cost']] == ''
					&& $data[$this->mapper->mappedData['rateUnitType']] == '') {
				continue;
			}

			//if rate Unit type is empty we take default type
			if($data[$this->mapper->mappedData['rateUnitType']] == ''){
				$data[$this->mapper->mappedData['rateUnitType']] = $data[$this->mapper->mappedData['unitType']];
			}

			if ($data[$this->mapper->mappedData['processName']] != '') {

				
				if (!isset($currentProcess)) {
					//create first Process
					$currentProcess = new Process($this->db);
					
					$currentProcess->setName($data[$this->mapper->mappedData['processName']]);
					//create first step
					$step = new Step($this->db);
					$step->setDescription($data[$this->mapper->mappedData['stepDescription']]);
					$step->setNumber($data[$this->mapper->mappedData['stepNumber']]);
			
					//create first resource
					$typeDescription = mb_strtolower($data[$this->mapper->mappedData['unitType']]);
					$rateTypeName = mb_strtolower($data[$this->mapper->mappedData['rateUnitType']]);

					$unitType = $unittype->getUnitTypeIdByName($typeDescription);
			
					$rateUnitType = $unittype->getUnitTypeIdByName($rateTypeName);
					
					/*if (is_null($rateUnitType)) {
						throw new \Exception('There is no such type as ' . $rateTypeName);
					}*/
					$resource = new Resource($this->db);
					$resource->setQty($data[$this->mapper->mappedData['qty']]);
					$resource->setRate($data[$this->mapper->mappedData['rate']]);
					$resource->setUnittypeId($unitType);
					$resource->setRateUnittypeId($rateUnitType);
					$resource->setDescription($data[$this->mapper->mappedData['resourceDescription']]);
					$resource->setResourceTypeId($resourseType[$data[$this->mapper->mappedData['processType']]]);
					if($data[$this->mapper->mappedData['cost']]!='' || is_null($data[$this->mapper->mappedData['cost']])){
						$resource->setTotalCost($data[$this->mapper->mappedData['cost']]);
					}
					
					$resourses[] = $resource;
					continue;
				}
				
				$step->setInitResources($resourses);
				$steps[] = $step;
				$currentProcess->setProcessSteps($steps);
				$processes[] = $currentProcess;
				$currentProcess = new Process($this->db);
				//initialization process
				$currentProcess->setName($data[$this->mapper->mappedData['processName']]);
				$steps = array();
				
			}
			
			
			if ($data[$this->mapper->mappedData['stepNumber']] != '') {
				
				
				if ($data[$this->mapper->mappedData['processName']] == '') {
					$step->setInitResources($resourses);
					$steps[] = $step;
				}
				$resourses = array();
				$step = new Step($this->db);
				
				$step->setNumber($data[$this->mapper->mappedData['stepNumber']]);
				$step->setDescription($data[$this->mapper->mappedData['stepDescription']]);
				$step->setOptional($data[$this->mapper->mappedData['optional']]);
				
			}
			
			$typeDescription = mb_strtolower($data[$this->mapper->mappedData['unitType']]);
			$rateTypeName = mb_strtolower($data[$this->mapper->mappedData['rateUnitType']]);
			
			$unitType = $unittype->getUnitTypeIdByName($typeDescription);
			$rateUnitType = $unittype->getUnitTypeIdByName($rateTypeName);
			/*if(is_null($rateUnitType)){
				throw new \Exception('There is no such type as '.$rateTypeName);
			}*/
			
			$resource = new Resource($this->db);
			$resource->setQty($data[$this->mapper->mappedData['qty']]);
			$resource->setRate($data[$this->mapper->mappedData['rate']]);
			$resource->setUnittypeId($unitType);
			$resource->setRateUnittypeId($rateUnitType);
			$resource->setDescription($data[$this->mapper->mappedData['resourceDescription']]);
			$resource->setResourceTypeId($resourseType[$data[$this->mapper->mappedData['processType']]]);
			
			if ($data[$this->mapper->mappedData['cost']] != '' || is_null($data[$this->mapper->mappedData['cost']])) {
				$resource->setTotalCost($data[$this->mapper->mappedData['cost']]);
			}
			$resourses[] = $resource;
		}
		
		$resourses = $resource;
		$step->setInitResources($resourses);
		$steps[] = $step;
		$currentProcess->setProcessSteps($steps);
		$processes[] = $currentProcess;
		$this->processes = $processes;
	}

}

?>
