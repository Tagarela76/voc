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
		
	var $curMonth; //current month to write in total
	
	var $b;
	
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
		//var_dump($path,$attribs,$tag);
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
				$this-> SetAutoPageBreak(true,25);
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
			
			case "TABLE":
				$this->header();
				$this->widths = array(90, 90);
				$this->SetWidths($this->widths);
				$this->aligns = array('L','C');
				$this->SetAligns($this->aligns); 
				$this->SetFillColor(200,200, 200,200);
				break;	
				
			case "MONTH":	
				
				$name = $attribs["NAME"];
			//	$dateObj = DateTime::createFromFormat('Y', $dateBegin);
				$this->Ln(5);
				$this->SetFont('Arial','B',15);
				$this->Cell($this->widths[0]+$this->widths[1],7,$name ,0,0,'L');
				$this->Ln();
				$this->Cell($this->widths[0],7,"Department ",1,0,'C');
				$this->Cell($this->widths[1],7,"Expenses",1,0,'C');
				//$this->Cell($this->widths[2],7,"Total VOC ",1,0,'C');
				$this->Ln();
				$this->SetFont('Arial','',12);
				//Cell($this->widths[0],40,$name,1,0,'C');
			//	$this->rows[0] = $name;
				$this->curMonth = $name;
				$this->b = true;
				break;
				
			case "INFO":
				
			if ( $attribs["DEPNAME"] == 'none' ) {
				/*if ( isset( $attribs["RULE"] ) ) {
					$this->Cell($this->widths[0],7,$this->curMonth,1,0,'C');
				} else {
					$this->Cell($this->widths[0]+$this->widths[1]+$this->widths[2],0.5,"",1,0,'C',true);
					$this->Ln();
					$this->Cell($this->widths[0],7,"Exemption ",1,0,'L');					
				}*/
				
				$this->SetFont('Arial','I',12);
				$this->Cell($this->widths[0],7,"none",1,0,'C');
				$this->Cell($this->widths[1],7,"none",1,0,'C');		
				$this->Ln();		
			} else {	
				if( isset( $attribs["EXPENSES"] ) ) {
					$this->SetFont('Arial','',12);
					$this->rows[0] = $attribs["DEPNAME"];
					$this->rows[1] = "$".$attribs["EXPENSES"];
				} else {
					$this->SetFont('Arial','',12);
					/*if ($this->b) {
						$this->Cell($this->widths[0]+$this->widths[1]+$this->widths[2],0.5,"",1,0,'C',true);
						$this->Ln();
						$this->rows[0] = "Exemption ";
						$this->b = false;
					}*/
					$this->rows[0] = "none";
					$this->rows[1] = "none";
			}
			}
				break;
				
			case "TOTAL":
				break;	
			case "FULLTOTAL":
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
			case "DATE":	
				$this->Ln(5);
				break;																	
			case "YEAR":				
				break;
			case "TOTAL":
				
			/*	$this->rows[0] = "Total VOC's ";
				$this->Row($this->rows);
				for ($i=0; $i<=2; $i++){
					$this->rows[$i] = " ";
				}*/
				$this->SetFont('Arial','I',12);
				$this->Cell($this->widths[0],7,"Total ",1,0,'C',true);
				$this->Cell($this->widths[1],7,"$".$this->header['TOTAL'],1,0,'C',true);
				$this->Ln();
				$this->SetFont('Arial','',12);
				break;	
			case "FULLTOTAL":
				
				$this->SetFont('Arial','B',15);
				$this->Ln(2);
				$this->Cell($this->widths[0],7,"Total for Period:  ",1,0,'C');
				$this->Cell($this->widths[1],7,"$".$this->header['FULLTOTAL'],1,0,'C');	
				break;															
			case "INFO":
				if ($attribs["DEPNAME"]!='none') {										
					$this->Row($this->rows);
					for ($i=0; $i<=2; $i++){
						$this->rows[$i] = " ";
					}	
				}	
				break;										  		 		  	
		 }
		 		
		} //end Element
	
	
	function characterData( $tag, $attribs, $data, $path, $parentpath )
		{
		$this->DebugPrint( "CharData tag=$tag data=\"$data\"" );
		
		switch ($tag) {
			case 'TITLE':
				$this->header['TITLE'] = $data;
				break;
				
			case 'SUBTITLE':
				$this->header['SUBTITLE'] = $data;
				break;

			case 'CATEGORY':
				$this->header['CATEGORY'] = $data;
				break;

			case 'NAME':
				$this->header['NAME'] = $data;
				break;

			case 'DEPARTMENTNAME':
				$this->header['DEPARTMENTNAME'] = $data;
				break;

			case 'ADDRESS':
				$this->header['ADDRESS'] = $data;
				break;
			case 'CITYSTATEZIP':
				$this->header['CITYSTATEZIP'] = $data;
				break;

			case 'COUNTY':
				$this->header['COUNTY'] = $data;
				break;

			case 'PHONE':
				$this->header['PHONE'] = $data;
				break;

			case 'FAX':
				$this->header['FAX'] = $data;
				break;

			case 'FACILITYID':
				$this->header['FACILITYID'] = $data;
				break;

			case 'NOTES':
				$this->header['NOTES'] = $data;
				break;
								
			case 'TOTAL':
				$this->header['TOTAL'] = $data;							
				break;	
			
			case 'FULLTOTAL':
				$this->header['FULLTOTAL'] = $data;							
				break;					

			case 'PERIOD':
				$this->header['PERIOD'] = $data;							
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
    	    	
    	if (isset($this->header['TITLE'])) {
    		$this->SetFont('Arial','B',18);
			$this->Cell(190,10,$this->header['TITLE'],0,0,'C');
			$this->Ln();
    	    
    	    $this->SetFont('Arial','B',15);
    	    $this->Cell(180,10,$this->header['PERIOD'],0,0,'C');
    	    $this->Ln(10);	   
    	    $this->SetFont('Arial','B',10); 
    	    
    	    if ($this->header['CATEGORY']!="Department") {
    	    	$this->Cell(40,10,$this->header['CATEGORY']." Name:",0,0,'R');
    	    	$this->SetFont('Arial','',10);
    	    	$this->Cell(70,10,$this->header['NAME']);  
    	    	
    	    	$this->SetFont('Arial','B',10);     	    
    	    	$this->Cell(20,10,"Report by:",0,0,'R');			
				$this->Line(140,36,180,36);
				$this->Ln(5); 	    	
    	    } else {
     	    	$this->Cell(40,10,"Facility Name:",0,0,'R');
    	    	$this->SetFont('Arial','',10);
    	    	$this->Cell(70,10,$this->header['NAME']);

    	    	$this->SetFont('Arial','B',10);     	    
    	    	$this->Cell(20,10,"Report by:",0,0,'R');			
				$this->Line(140,36,180,36);
				$this->Ln(5);
    	    	    	    	
    	    	$this->SetFont('Arial','B',10); 
    	    	$this->Cell(40,10,$this->header['CATEGORY']." Name:",0,0,'R');
    	    	$this->SetFont('Arial','',10);
    	    	$this->Cell(70,10,$this->header['DEPARTMENTNAME']); 

    	    	$this->SetFont('Arial','B',10); 
    	    	$this->Cell(20,10,"Signature:",0,0,'R');
				$this->Line(140,41,180,41);
    	    	$this->Ln(5);      	    	   	    	
    	    }
			
    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"Address:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['ADDRESS']);
    	    if ($this->header['CATEGORY']!="Department") {
    	    	$this->SetFont('Arial','B',10); 
    	    	$this->Cell(20,10,"Signature:",0,0,'R');
				$this->Line(140,41,180,41);
    	    	$this->Ln(5);  
    	    } else {
    	    	$this->SetFont('Arial','B',10); 
    	    	$this->Cell(20,10,"Date:",0,0,'R');
    	    	$this->Ln(5);  
    	    }
    	    
    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"City, State, Zip:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['CITYSTATEZIP']);
    	    
    	    if ($this->header['CATEGORY']!="Department") {
    	    	$this->SetFont('Arial','B',10); 
    	    	$this->Cell(20,10,"Date:",0,0,'R');  
    	    }
    	    $this->Ln(5);    
    	     	    
    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"County:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['COUNTY']);
    	    $this->Ln(5);

    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"Phone:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['PHONE']);
    	    $this->Ln(5); 

    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"Fax:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['FAX']);
    	    $this->Ln(5);
    	    
    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"Facility ID:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['FACILITYID']);
    	    $this->Ln(5);
    	    
    	    $this->SetFont('Arial','B',10); 
    	    $this->Cell(40,10,"Notes:",0,0,'R');
    	    $this->SetFont('Arial','',10);
    	    $this->Cell(70,10,$this->header['NOTES']);    	        	     
    	    $this->Ln();
    	      	      	    
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
		//    print "<font size=2>".htmlentities($message)."</font><br/>\n";
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
