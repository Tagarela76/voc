<?php
/*
 *  XML class for FPDF
 *  Copyright (c) 2002, Patrick Prasse (patrick.prasse@gmx.net)
 *
 *  Parts of this code is (c) 2001, Edward Rudd and
 *  comes from his "XML Template to PDF Class v1.1"
 *  Credits Edward
 *
 *  Part of the code added and/or modified by Klemen Vodopivec 
 *  <klemen@vodopivec.org>. Changes:
 *  - new meta tag addfont for adding external fonts (useful for
 *    non-ascii languages)
 *  - also read string as valid XML input (useful for template
 *    engines like Smarty)
 *  - img tag for including images in PDF (support for jpg and png)
 *
 *  This library is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Library General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU Library General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */


require_once "xml_parser.php";
//require_once "fpdf.php";
require_once "mc_table.php";


class XML2PDF extends PDF_MC_Table
{
	var $parser;         /* XML Parsed data (xml_parse object) */
	var $debug;          /* Debug ? */
	
	var $abort_error;   /* Abort execution because of a severe error ? */
	
	var $open_tags;
	
	var $fontstack; /* Font stack */
	var $colorstack; /* Color set history */
	
	var $add_fonts;
	
	var $indent;
	
	var $tablestack;
	var $trstack;
	var $tdstack;
	
	var $tdbstack;
	
	var $header;
	var $footer;
	
	var $links;	
	
	var $filename; //XML Filename
	
	var $rows;
	
	/* Custom vars*/
	var $tableFormat;	// =head/body - shows what part of document we parse now
	var $trElem;		// contain all <tr/> elements in head - array of array with all $tdElem of current <tr/> element (it used to print head of table)
	var $tdElem;		// contain all <td/> elements in current <tr/> element - array with data fields was in <td/> tags (ised to print all fields in table)
	var $widths;		// contain $width of current head-row(if we already in body - it contain last header width - for fields of table fields)
	var $upperWidths;	// contain all width for header rows
	var $aligns;		// to set all aligns=L
	var $alignsC;		// to set all aligns=C
	var $fieldsCount;	// number of fields in the table
	var $additional;	// contains additional info to print

	
	//Class initializer.  the XML filename and optionally enable debug (set to 1)
	//Also sends PDF content-type header;
	function XML2PDF( $debug=FALSE )
		{
		// Initialization
		$this->DebugPrint( "initializing..." );
		parent::FPDF( 'P', 'mm', 'A4' );
		
		$this->debug = $debug;
		$this->abort_error = FALSE;
		
		$this->open_tags = array( "page" => FALSE );
		
		$this->fontstack = array();
		$this->colorstack = array( );
		
		$this->tablestack = array( );
		$this->trstack = array( );
		$this->tdstack = array( );
		
		$this->links = array( );
		
		$this->add_fonts = array( );
		
		$this->indent = array( "ih1" => 0, "ih2" => 5, "ih3" => 10, "ih4" => 15, "ih5" => 20, "ih6" => 25, "current" => 0 );
		}
	
	
	//Parse through the XML once document and generate PDF (can be called multiple times)
	//Returns XML Error messages if bad XML. otherwise false.
	function Parse( $filename ) 
		{
		$this->header = array();
		$this->footer = array();
		$this->filename = $filename;
		$error = XMLParseFile ($this->parser, $this->filename, 0, "", 1,"ISO-8859-1");
		if (strcmp ($error, "")) 
		{
			print "Parser Error: $error\n";
			return $error;
		} 
		else 
		{
			$this->WalkXML ("0"); //&$this->parser->structure, &$this->parser->positions);
			return false;
		}
		}
	
	//Parse through the XML once string and generate PDF (can be called multiple times)
	//Returns XML Error messages if bad XML string. otherwise false.
	function ParseString( $str ) 
		{
		$this->header = array();
		$this->footer = array();
		$error = XMLParseString ($this->parser, $str, 0, "", 1,"ISO-8859-1");
		if (strcmp ($error, "")) 
		{
			print "Parser Error: $error\n";
			return $error;
		} 
		else 
		{
			$this->WalkXML ("0"); //&$this->parser->structure, &$this->parser->positions);
			return false;
		}
		}
	
	
	
	/*******************************************************************************
	 * END OF PUBLIC FUNCTIONS                             *
	 *******************************************************************************/
	
	
	function WalkXML ($path) 
		{
		if (is_array($this->parser->structure[$path])) 
		{
			//Beginning Tag
			$this->startElement($path);
			
			for ($element = 0; $element < $this->parser->structure[$path]["Elements"];$element++)
				$this->WalkXML($path.",$element");
			
			//End Tag
			$this->endElement($path);
		} 
		else 
		{
			//Content
			//Find parent path
			$parentpath = substr($path,0,strrpos($path,","));
			$this->DebugPrint("PATH=".$path."-".strrpos($path,",")."-".$parentpath);
			
			/* preliminary whitespace replace */
			$data = $this->parser->structure[$path];
			$data = preg_replace( "/\s*\n\s*/", " ", $data );
			$data = preg_replace( "/(\n|\r)/", " ", $data );
			$data = preg_replace( "/^\s+/", "", $data );
			$data = preg_replace( "/(\ +)/"," ", $data );
			$data = preg_replace( "/^\s/", "", $data );
			
			$data = preg_replace( "/&nbsp;/", " ", $data );
			
			if (strlen($data)>0) 
			{
				$this->characterData($this->parser->structure[$parentpath]["Tag"],
					$this->parser->structure[$parentpath]["Attributes"],
					$data, $path, $parentpath );
			}
		}
		}
	
	
	//handles the "beginning" of a tag  and sets parameters appropriatly
	function startElement($path) 
		{
		$attribs = &$this->parser->structure[$path]["Attributes"];
		$tag = $this->parser->structure[$path]["Tag"];
		$this->DebugPrint( "Start: $tag\n" );
		switch ($tag) 
		{
											
			case 'PAGE':
			$this->SetWidths(array(55,45,45,45));
				if( $this->open_tags["page"] )
					return $this->Error( "Page already open, ignoring.", FALSE );
				
				$this->open_tags["page"] = TRUE;
				
				$this->AddPage( ((empty($attribs["ORIENTATION"]))?("P"):($attribs["ORIENTATION"])) );
				if( !empty($attribs["TOPMARGIN"]) ) 
				{
					$this->SetTopMargin( $attribs["TOPMARGIN"] );
				}
				if( !empty($attribs["LEFTMARGIN"]) ) 
				{
					$this->SetTopMargin( $attribs["LEFTMARGIN"] );
				}
				if( !empty($attribs["RIGHTMARGIN"]) ) 
				{
					$this->SetTopMargin( $attribs["RIGHTMARGIN"] );
				}
				$this-> SetAutoPageBreak(true,10);
				break;
			case 'META':			
				if( ! $this->open_tags["page"] )
					return $this->Error( "Page not open, ignoring.", FALSE );
				
				$name = $attribs["NAME"];
				if( empty( $name ) )
					return $this->Error( "META tag without name, ignoring.", FALSE );
				$value = (empty($attribs["VALUE"])?"":$attribs["VALUE"]);
				switch( strtoupper( $name ) )
				{
					case 'AUTHOR':
						$this->SetAuthor( $value );
						break;
					case 'CREATOR':
						$this->SetCreator( $value );
						break;
					case 'SUBJECT':
						$this->SetSubject( $value );
						break;
					case 'TITLE':
						$this->SetTitle( $value );
						break;
					case 'KEYWORDS':
						$this->SetKeywords( $value );
						break;
						
					case 'COMPRESSION':
						$this->SetCompression( ($value!='0'?TRUE:FALSE) );
						break;
						
					case 'BASEFONT':
						if( empty( $attribs["VALUE"] ) )
							return $this->Error( " META BASEFONT with empty value, ignoring.", FALSE );
						$font = split( ",", $attribs["VALUE"] );
						if( isset( $font[0] ) && !empty( $font[0] ) )
							$this->fontstack[0]["family"] = $font[0];
						if( isset( $font[1] ) )
							$this->fontstack[0]["style"] = $font[1];
						if( isset( $font[2] ) && !empty( $font[2] ) )
							$this->fontstack[0]["size"] = (int)$font[2];
						$this->SetFont($font[0], $font[1], $font[2]);
						break;
						
					case 'TEXT':
						if( empty( $attribs["VALUE"] ) )
							return $this->Error( "META INDENT with empty value, ignoring.", FALSE );
						$indent = split( ",", $attribs["VALUE"] );
						foreach( $indent as $inr => $val )
						{
							$this->indent["ih".($inr+1)] = (int) $val;
						}
						break;
						
					case 'ADDFONT':
						if( empty( $attribs["VALUE"] ) )
							return $this->Error( "META ADDFONT with empty value, ignoring.", FALSE );
						
						$font = split( ",", $attribs["VALUE"] );
						if( !isset( $font[1] ) )
							$font[1] = '';
						if( !isset( $font[2] ) )
							$font[2] = 10;
						
						if( isset( $font[3] ) )	
							$this->AddFont($font[0], $font[1], $font[3]);
						else
							$this->AddFont($font[0], $font[1]);
						$this->SetFont($font[0], $font[1], $font[2]);
						break;
						
					default:
						return $this->Error( "Unknown META name=\"$name\", ignoring.", FALSE );
				}
				break;	
				
			case "ADDITIONAL":
				$this->additional = '';
				break;		
				
			case "SEARCH_TERM":
				break;			
			
			case "TABLE":
				$this->SetFillColor(200,200,200);
				break;	
			
			case "TBODY":
				$this->header();
				$this->tableFormat = 'body';
				break;
				
			case "THEAD":
				$this->tableFormat = 'head';
				$this->trElem = array();	//in trElem we will collect rows in head	
				$this->widths = array();
				$this->upperWidths = array();
				break;
			
			case "TD":				
				if (isset($attribs['WIDTH'])) {
					$this->widths []= $attribs['WIDTH'];
				}
				break;
				
			case "TR":
				$this->tdElem = array();	//in tdElem we will colect fields in row	
				if ($this->tableFormat == 'head') {
					$this->widths = array();
				}
				break;
				
			case "HEADER":
				$this->header['HEADER'] = true;
				break;							
														
		} /* switch */
		
		}
	
	//handles the "end" of a tag and (un)sets parameters appropriatly
	function endElement($path) 
		{
		$attribs = &$this->parser->structure[$path]["Attributes"];
		$tag = $this->parser->structure[$path]["Tag"];
		$this->DebugPrint( "End: $tag\n" );
		switch ($tag) 
		 {			 																			
			case "TABLE":			
				break;	
			case "TD":
				if ($this->tableFormat == 'head') {
					$this->tdElem []= $this->header['TD'];
				} else {
					$this->SetFont('Arial','',10);
					$this->tdElem []= $this->header['TD'];
					
					//	clear it, other way <td></td> will produce a bug
					$this->header['TD'] = null;
				}
				break;
			case "TR":
				if ($this->tableFormat == 'head') {
					$this->trElem []= $this->tdElem;
					$fieldsCount = count($this->tdElem);
					$this->fieldsCount = $fieldsCount;
					$w = 280/$this->fieldsCount;
					
					if (!isset($this->widths) || count($this->widths) != $this->fieldsCount) {
						$this->widths = array();
						for($i=0;$i<$this->fieldsCount;$i++) {
							$this->widths []= $w;
							$this->alignsC []= 'C';
							$this->aligns []= 'L';
						}
					} else {
						foreach($this->widths as $key => $width) {
							$this->widths[$key] = substr($width,0,strpos($width,'%'))*280/100;
							$this->alignsC [$key]= 'C';
							$this->aligns [$key]= 'L';
						}
					}
					$this->upperWidths []= $this->widths;
				} else {
					$this->Row($this->tdElem);
				}
				break;	
			
			case "THEAD":
				break;
			
			case "TBODY":
				break;	
				
			case "FILTER":
				if (!is_null($this->header['VALUE'])) {
					$this->additional = " Filtered by field '".$this->header['FIELD']."' ".$this->header['CONDITION']." '".$this->header['VALUE']."'. ";
				}
				break;		
				
			case "SEARCH_TERM":
				if (!is_null($this->header['SEARCH_TERM'])) {
					$this->additional = " Searched by '".$this->header['SEARCH_TERM']."'. ";
				}
				break;						  		 		  	
		 }
		 		
		} //end Element
	
	
	function characterData( $tag, $attribs, $data, $path, $parentpath )
		{
		$this->DebugPrint( "CharData tag=$tag data=\"$data\"" );
		
		switch ($tag) {
			case 'TITLE':
				$this->header['TITLE'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'COMPANY':
				$this->header['COMPANY'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'FACILITY':
				$this->header['FACILITY'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'DEPARTMENT':
				$this->header['DEPARTMENT'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'URL':
				$this->header['URL'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
			case 'USER':
				$this->header['USER'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'DATE':
				$this->header['DATE'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;

			case 'TD':				
				$this->header['TD'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
			
			case 'FIELD':
				$this->header['FIELD'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
			
			case 'CONDITION':
				$this->header['CONDITION'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
				
			case 'VALUE':
				$this->header['VALUE'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
				
			case 'SEARCH_TERM':
				$this->header['SEARCH_TERM'] = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
				break;
			
		}
					
	}
	
	
	function Error( $text, $abort=FALSE )
		{
		if( ! $this->abort_error )
			$this->abort_error = $abort;
		
		print "Error: $text\n";
		return 0;
		}
	
	
	function Header( ) {	
		$this->SetFont('Arial','',10);

    	if (isset($this->header['HEADER'])) 
    	{
    		$this->SetFont('Arial','B',10);
			//$this->Cell(0,10,'URL: ',0,0,'R');
			$this->SetFont('Courier','I',10);
			$this->MultiCell(0,5,$this->header['URL'],0,0,'R');
			$this->Ln(); 
                        $this->Image('images/gyantcompliance_small.jpg',20,10,40, 'jpg');
                        $this->SetFont('Arial','B',10);
                        $this->Cell(60);
			$this->Cell(35,5,'Company: ',0,0,'L');
			$this->SetFont('Arial','',10);
			$this->Cell(115,5,$this->header['COMPANY'],0,0,'L');
			$this->SetFont('Arial','B',10);
			$this->Cell(15,5,'User: ',0,0,'L');
			$this->SetFont('Arial','',10);
			$this->Cell(75,5,$this->header['USER'],0,0,'L');
			$this->Ln(5); 
			
			$this->Cell(60);
			if(isset($this->header['FACILITY'])) {
				$this->SetFont('Arial','B',10);
				$this->Cell(35,5,'Facility: ',0,0,'L');
				$this->SetFont('Arial','',10);
				$this->Cell(115,5,$this->header['FACILITY'],0,0,'L');
			} else {
				$this->Cell(150);
			}
			$this->SetFont('Arial','B',10);
			$this->Cell(15,5,'Date: ',0,0,'L');
			$this->SetFont('Arial','',10);
			$this->Cell(75,5,$this->header['DATE'],0,0,'L');
			$this->Ln(5); 
			
			$this->Cell(60);
			if(isset($this->header['DEPARTMENT'])) {
				$this->SetFont('Arial','B',10);
				$this->Cell(35,5,'Department: ',0,0,'L');
				$this->SetFont('Arial','',10);
				$this->Cell(115,5,$this->header['DEPARTMENT'],0,0,'L');
			} else {
				$this->Cell(150);
			}
			
			$this->Ln();
			  
			$this->SetFont('Arial','B',18);
			$this->Cell(0,18,$this->header['TITLE'],0,0,'C');
			$this->Ln();
			
			if (!is_null($this->additional) && $this->additional != '') {
				$this->SetFont('Arial','I',12);
				$this->MultiCell(0,5,$this->additional);
				$this->Ln(1);
			}
			$this->SetFont('Arial','B',10);
			$this->SetAligns($this->alignsC);
			foreach ($this->trElem as $key => $trElement) {
				$fieldsCount = count($trElement);
				$this->SetWidths($this->upperWidths[$key]);
				$this->Row($trElement,true);
			}
			$this->SetAligns($this->aligns);			
    	}
    	    	
	}
	
	function Footer( )
		{				
			$this->SetY(-15);												
			$this->SetX(-15);						
			$this->Cell(0,10,$this->PageNo(),0,0,'C');	
		}
	
	
	//DebugPrint wrapper..Only prints when debug==1
	function DebugPrint($message) 
		{
		if (!$this->debug)
			return;
		print "$message\n";
		}
	
	
	function _setfont( $family=-1, $style=-1, $size=-1 )
		{
		$i = count( $this->fontstack );
		if( $i != 0 )
		{
			if( $family == -1 )
				$family = $this->fontstack[$i-1]["family"];
			if( $style == -1 )
				$style = $this->fontstack[$i-1]["style"];
			if( $size <= 0  )
				$size = $this->fontstack[$i-1]["size"];
		}
		
		$this->fontstack[$i] = array( "family" => $family, "style" => $style, "size" => $size );
		$this->SetFont( $family, $style, $size );
		}
	
	function _restorefont( )
		{
		$i = count( $this->fontstack ) - 1;
		if( $i < 0 )  return;
		$font = $this->fontstack[$i-1];
		unset( $this->fontstack[$i] );
		$this->SetFont( $font["family"], $font["style"], $font["size"] );
		}
	
	
	function _color( $color )
		{
		if( ! is_string( $color ) )
			return $color;
		
		if( strlen( $color ) == 3 )
		{
			return array( "r" => (int)substr( $color, 1, 2 ), "g" => -1, "b" => -1 );
		}
		else if( strlen( $color ) == 7 )
		{
			return array( "r" => (int)substr( $color, 1, 2 ), 
				"g" => (int)substr( $color, 3, 2 ), 
				"b" => (int)substr( $color, 5, 2 ) );
		}
		else
		{
			$this->Error( "Unknown colorspec \"$color\", ignoring." );
			return -1;
		}
		}
	
	function _setcolor( $drawcolor, $fillcolor, $textcolor )
		{
		$i = count( $this->colorstack );
		if( $i != 0 )
		{
			if( empty( $drawcolor ) )
				$drawcolor = $this->colorstack[$i-1]["drawcolor"];
			if( empty( $fillcolor ) )
				$fillcolor = $this->colorstack[$i-1]["fillcolor"];
			if( empty( $textcolor ) )
				$textcolor = $this->colorstack[$i-1]["textcolor"];
		}
		
		$drawcolor = $this->_color( $drawcolor );
		$fillcolor = $this->_color( $fillcolor ); 
		$textcolor = $this->_color( $textcolor );
		if( !is_array( $drawcolor ) || !is_array( $fillcolor ) || !is_array( $textcolor ) )
		{ /* error processing colors -> use old colors */
			$this->colorstack[$i] = $this->colorstack[$i - 1];
			return;
		}
		else
		{
			$this->colorstack[$i] = array( "drawcolor" => $this->_color( $drawcolor ), 
				"fillcolor" => $this->_color( $fillcolor ), 
				"textcolor" => $this->_color( $textcolor )  );
		}
		
		$this->SetDrawColor( $drawcolor );
		$this->SetFillColor( $fillcolor );
		$this->SetTextColor( $textcolor );
		}
	
	function _restorecolor( )
		{
		$i = count( $this->colorstack ) - 1; 
		if( $i < 0 )  return;
		$color = $this->colorstack[$i-1];
		unset( $this->colorstack[$i] );
		$this->SetDrawColor( $color["drawcolor"] );
		$this->SetFillColor( $color["fillcolor"] );
		$this->SetTextColor( $color["textcolor"] );
		}
	
	
	
	
}

?>
