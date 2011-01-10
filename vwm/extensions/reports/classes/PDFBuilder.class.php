<?php

class PDFBuilder {


    function PDFBuilder($xmlFileName, $reportType) {
    	define( "FPDF_FONTPATH", "extensions/reports/xml2pdf/font/" );
		echo "\n\n";
		
//		switch ($reportType) {
//			case "productQuants":
//				require( "modules/xml2pdf/productquantities2pdf.php" );
//				break;
//			case "toxicCompounds":
//				require( "modules/xml2pdf/toxicCompounds2pdf.php" );
//				break;
//			case "vocLogs":				
//				require( "modules/xml2pdf/voclogs2pdf.php" );
//				break;
//			case "mixQuantRule":
//				require( "modules/xml2pdf/mixQuantRule2pdf.php" );
//				break;
//			case "chemClass":			
//				require( "modules/xml2pdf/chemicalclass2pdf.php" );
//				break;
//			case "exemptCoat":			
//				require( "modules/xml2pdf/exemptCoating2pdf.php" );
//				break;
//			case "projectCoat":			
//				require( "modules/xml2pdf/projectCoating2pdf.php" );
//				break;
//			case "VOCbyRules":
//				require( "modules/xml2pdf/VOCbyRules2pdf.php" );
//				break;
//			case "SummVOC":
//				require( "modules/xml2pdf/SummVOC2pdf.php");
//				break;
//		}		
		
		//if we get here we can use module Reports and selected report for sure, so we dont need to check it again
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		$mReports = new $map['reports'];
		
		$xml2pdfClassFileName = $mReports->getXML2PDFfileName($reportType);
		require($xml2pdfClassFileName);

		$file = $xmlFileName;

		$xml2pdf = new XML2PDF( FALSE );
		$xml2pdf->Open();

		$xml2pdf->Parse($xmlFileName);

		//$xml2pdf->Output( $file . ".pdf", FALSE);
		$xml2pdf->Output();
		echo "\n\n";
    }
}
?>