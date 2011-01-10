<?php
require_once "extensions/iInstaller.php";

class MReportsInstaller implements iInstaller {
	
	private $db;
	public $errors;

    function MReportsInstaller($db) {
    	$this->db = $db;
    }
    
    public function checkAlreadyInstalled() {
    	$query = 'SELECT 1 FROM report, report2company LIMIT 0';
    	$this->db->query($query);
    	if ($this->db->fetch_all() === false) { 
    		return false;
    	} else {
    		$query = 'SELECT id FROM '.TB_MODULE.' WHERE name = \'reports\' LIMIT 1';
    		$this->db->query($query);
    		return ($this->db->num_rows() > 0);
    	}
    }
    
    public function install() {
		//create table report
		$query = "CREATE TABLE IF NOT EXISTS `report` (" .
				"  `report_id` int(11) NOT NULL auto_increment," .
				"  `name` varchar(64) NOT NULL," .
				"  `type` varchar(64) NOT NULL," .
				"  PRIMARY KEY  (`report_id`)" .
				") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;";
		$this->db->query($query);
		//create table reports to company
		$query = "CREATE TABLE IF NOT EXISTS `report2company` (" .
				"  `id` int(11) NOT NULL auto_increment," .
				"  `report_id` int(11) NOT NULL," .
				"  `company_id` int(11) NOT NULL," .
				"  `on_off` tinyint(1) NOT NULL," .
				"  PRIMARY KEY  (`id`)" .
				") ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$this->db->query($query);
		//insert reports names
		$query = "INSERT INTO `report` (`report_id`, `name`, `type`) VALUES" .
				"(1, 'Product List', 'productQuants')," .
				"(2, 'Toxic Compounds', 'toxicCompounds')," .
				"(3, 'Daily Emissions', 'vocLogs')," .
				"(4, 'Product Usage by Rule Summary', 'mixQuantRule')," .
				"(5, 'Chemical Classification Summary Form', 'chemClass')," .
				"(6, 'Exempt Coating Operations', 'exemptCoat')," .
				"(7, 'Project Coating Report', 'projectCoat')," .
				"(8, 'VOC Summary for Each Rule', 'VOCbyRules')," .
				"(9, 'Monthly VOC Summary Total', 'SummVOC');";
		$this->db->query($query);
		//check if reports exists in table module
		$query = "SELECT * FROM module WHERE name = 'reports' LIMIT 1";
		$this->db->query($query);
		if($this->db->num_rows() == 0) {
			//if its not insert it 
			$query = "INSERT INTO `".TB_MODULE."` (name) VALUES ('reports')";
			$this->db->query($query);
		}    }
    
    public function check() {
    	$classlist = array( "CSFBuilder.class.php",
			"CSVBuilder.class.php",
			"MReports.class.php",
			"MReportsInstaller.class.php",
			"PDFBuilder.class.php",
			"RchemClass.class.php",
			"Report.class.php",
			"ReportCreator.class.php",
			"ReportRequest.class.php",
			"RexemptCoat.class.php",
			"RmixQuantRule.class.php",
			"RproductQuants.class.php",
			"RprojectCoat.class.php",
			"RSummVOC.class.php",
			"RtoxicCompounds.class.php",
			"RVOCbyRules.class.php",
			"RvocLogs.class.php",
			"XLSBuilder.class.php",
			"XMLBuilder.class.php",
			"XSLBuilder.class.php"
			);
    	$templatelist = array("createReport.tpl",
			"exemptCoatInput.tpl",
			"projectCoatInput.tpl",
			"reportsAdmin.tpl",
			"standartInput.tpl",
			"vocLogsInput.tpl"
			);
    	$xml2pdflist = array("chemClass2pdf.php",
			"exemptCoat2pdf.php",
			"fpdf.php",
			"mc_table.php",
			"mixQuantRule2pdf.php",
			"productQuants2pdf.php",
			"projectCoat2pdf.php",
			"SummVOC2pdf.php",
			"toxicCompounds2pdf.php",
			"VOCbyRules2pdf.php",
			"vocLogs2pdf.php",
			"xml_parser.php"
			);
		$validation = true;
		foreach ($classlist as $classFile) {
			if(!file_exists('extensions/reports/classes/'.$classFile)) {
				$validation = false;
				$this->errors []= 'No file describing the class '.$classFile;
			}
		}
		foreach ($templatelist as $templateFile) {
			if(!file_exists('extensions/reports/design/'.$templateFile)) {
				$validation = false;
				$this->errors []= 'No file describing the design '.$templateFile;
			}
		}
		foreach ($xml2pdflist as $xml2pdfFile) {
			if(!file_exists('extensions/reports/xml2pdf/'.$xml2pdfFile)) {
				$validation = false;
				$this->errors []= 'No file describing the pdf builder '.$xml2pdfFile;
			}
		}
		if (!class_exists('DOMDocument')) {
			$validation = false;
			$this->errors []= 'No class for creation XML documents(DOMDocument)';
		}
    	return $validation;
    }
}
?>