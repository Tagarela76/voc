<?php

class XMLBuilder {
	private $db;
	
	function XMLBuilder($db) {		
		$this->db = $db;
	}
	
	public function BuildXML($reportRequest, $fileName) {
		//if we get here we can use module Reports and selected report for sure, so we dont need to check it again
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		$mReports = new $map['reports'];
		$params = array (
			'db' => $this->db,
			'reportRequest' => $reportRequest,
			'fileName' => $fileName
		);
		$mReports->makeXml($params);
		return $fileName;
	}
}

?>