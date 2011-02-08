<?php

class XMLBuilder {
	private $db;
	
	function XMLBuilder($db) {		
		$this->db = $db;
	}
	
	public function BuildXML($reportRequest, $fileName) {
		//if we get here we can use module Reports and selected report for sure, so we dont need to check it again
	//	$debug = new Debug();
	//	$debug->printMicrotime(__LINE__,__FILE__);
		$ms = new ModuleSystem($this->db);
	//	$debug->printMicrotime(__LINE__,__FILE__);
		$map = $ms->getModulesMap();
	//	$debug->printMicrotime(__LINE__,__FILE__);
		$mReports = new $map['reports'];
		$params = array (
			'db' => $this->db,
			'reportRequest' => $reportRequest,
			'fileName' => $fileName
		);
	//	$debug->printMicrotime(__LINE__,__FILE__);
		$mReports->makeXml($params);
	//	$debug->printMicrotime(__LINE__,__FILE__);
		return $fileName;
	}
}

?>