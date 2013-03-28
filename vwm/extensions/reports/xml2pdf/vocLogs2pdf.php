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

	var $dayStack;
	var $cellStack;

	var $equipment;

	var $quantityUnittype = 'gal';
	var $vocUnittype = 'Lbs';

	var $companyLevel = false;
    
    
    /**
     * show spent Time 
     * @var boolean
     */
    private $showSpentTime = false;
    
    /**
     *show Total Cost 
     * @var boolean
     */
    private $showTotalCost = false;
    
    /**
     * Product Table Width
     * @var int 
     */
    private $productTableWidth = 270;
    
    /**
     * number of mandatory colums 
     */
    const COLUMNUMBER = 10;
    
    /**
     *width of horizontal line for mandatary colums 
     */
    const HORIZONTAL_LINE = 260;
    
    const TOTAL_TABLE_WIDTH = 250;
    
    
    public function getShowSpentTime()
    {
        return $this->howSpentTime;
    }

    public function setShowSpentTime($howSpentTime)
    {
        $this->howSpentTime = $howSpentTime;
    }

    public function getShowTotalCost()
    {
        return $this->showTotalCost;
    }

    public function setShowTotalCost($showTotalCost)
    {
        $this->showTotalCost = $showTotalCost;
    }

    public function getProductTableWidth()
    {
        return $this->productTableWidth;
    }

    public function setProductTableWidth($productTableWidth)
    {
        $this->productTableWidth = $productTableWidth;
    }

        
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
            $width = array(21, 20, 18, 85, 16, 16, 16, 16, 17, 20, 15);
            if($this->getShowSpentTime()) {
                    $width[] = 12;
                }
            if($this->getShowTotalCost()){
                $width[] = 12;
            }
			$this->SetWidths($width);
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

			case 'FACILITYNAME':
				break;

			case 'EQUIPMENT':
				//if ($this->PageNo() != 1) {
				if (isset($this->equipment)) {
					$this->equipment = $attribs['NAME'];
					$this->header['EQUIPMENT'] = $attribs['NAME'];
					$this->header['PERMITNO'] = $attribs['PERMITNO'];
					$this->header['FACILITYID'] = $attribs['FACILITYID'];
					$this -> AddPage('l');
				} else {
					$this->equipment = $attribs['NAME'];
					$this->header['EQUIPMENT'] = $attribs['NAME'];
					$this->header['PERMITNO'] = $attribs['PERMITNO'];
					$this->header['FACILITYID'] = $attribs['FACILITYID'];
					$this->header();
				}
				$this->rows[0] = 'Date:';
				$this->rows[1] = 'Supplier';
				$this->rows[2] = 'Product No.';
				$this->rows[3] = 'Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#';
				$this->rows[4] = 'VOC of Material';
				$this->rows[5] = 'VOC of Coating';
				$this->rows[6] = 'Mix Ratio';
				$this->rows[7] = 'Qty Used ('.$this->quantityUnittype.')';
				$this->rows[8] = 'Coating as Applied';
				$this->rows[9] = 'Rule Exemption';
				$this->rows[10] = 'Total VOC ('.$this->vocUnittype.')';
                
                $tableRows = self::COLUMNUMBER;
                if($this->getShowSpentTime()){
                    $tableRows++;
                    $this->rows[$tableRows] = 'Spent Time (min)';
                }
                
                if($this->getShowTotalCost()){
                    $tableRows++;
                    $this->rows[$tableRows] = 'Cost ($)';
                }
				$this->Row($this->rows);

				for ($i=0;$i<count($this->rows);$i++) {
					$this->rows[$i] = "";
				}

				$this->SetX(10);
				$this->SetLineWidth(0.6);
                //draw line 
                $horizontalLine = self::HORIZONTAL_LINE;
                if($this->getShowSpentTime()){
                    $horizontalLine+=12;
                }
                if($this->getShowTotalCost()){
                    $horizontalLine+=12;
                }
				$this->Cell($horizontalLine,0,'','T');
				$this->SetLineWidth(0.2);
				$this->ln();
				break;

			case 'DATE':
				$this->rows[0] = $attribs["DAY"];
				$this->dayStack = 1;
				break;

			case 'PRODUCT':
				$this->cellStack = $this->getY();
				break;

			case 'SUMMARY':
				$this->header['EQUIPMENT'] = " ";
				$this->header['PERMITNO'] = " ";
				$this->header['FACILITYID'] = " ";
				$this -> AddPage('l');

                $width = array(15, 20, 85, 16, 16, 20, 20, 20, 20, 18);
				//$this->SetWidths(array(25,30,85,20,20,20,20,20,20,20));
                
                if($this->getShowSpentTime()){
                  $width[] = 12;  
                }
                if($this->getShowTotalCost()){
                  $width[] = 18;  
                }
                
                $this->SetWidths($width);
                
				unset($this->rows);
				$this->rows[0] = 'Date:';
				$this->rows[1] = ($this->companyLevel) ?  'Facility' : 'Equipment';
				$this->rows[2] = 'Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#';
				$this->rows[3] = 'VOC of Material';
				$this->rows[4] = 'VOC of Coating';
				$this->rows[5] = 'Mix Ratio';
				$this->rows[6] = 'Qty Used ('.$this->quantityUnittype.')';
				$this->rows[7] = 'Coating as Applied';
				$this->rows[8] = 'Rule Exemption';
				$this->rows[9] = 'Total VOC ('.$this->vocUnittype.')';
                $columNumbers = self::COLUMNUMBER-1;
                $totalTableWidth = self::TOTAL_TABLE_WIDTH;
                if($this->getShowSpentTime()){
                   $columNumbers++;
                   $this->rows[$columNumbers] = 'Spent Time (min)';
                   $totalTableWidth+=12;
                }
                if($this->getShowTotalCost()){
                  $columNumbers++;
                  $this->rows[$columNumbers] = 'Total Cost ($)';
                  $totalTableWidth+=18;
                }
                
				$this->Row($this->rows);

				$this->SetX(10);
				$this->SetLineWidth(0.6);
				$this->Cell($totalTableWidth,0,'','T');
				$this->SetLineWidth(0.2);
				$this->ln();

				$this->Cell($totalTableWidth,7,"TOTAL VOC EMISSIONS UNDER RULE ".$this->header['RULE'],1,1,"C");
				break;

			case "COMPANY":
				$this->companyLevel = true;
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
                $horizontalLine = self::HORIZONTAL_LINE;
                if($this->getShowSpentTime()){
                    $horizontalLine+=12;
                }
                if($this->getShowTotalCost()){
                    $horizontalLine+=12;
                }
				$this->SetX(10);
				$this->SetLineWidth(0.6);
				$this->Cell($horizontalLine,0,'','T');
				$this->SetLineWidth(0.2);
				$this->ln();
				break;

			case "PRODUCT":
				if ($this->dayStack != 1) {
					$this->rows[0] = " ";
				}

				$this->SetDrawColor(255,255,255);
				$this->Row($this->rows);
				$this->SetDrawColor(0,0,0);
				$x = $this->getX();
				$y = $this->getY();

				$this->Line(10, $this->cellStack, 10, $y);
				$this->Line(31, $this->cellStack, 31, $y);
				$this->Line(51, $this->cellStack, 51, $y);
				$this->Line(69, $this->cellStack, 69, $y);
				$this->Line(154, $this->cellStack, 154, $y);
				$this->Line(170, $this->cellStack, 170, $y);
				$this->Line(186, $this->cellStack, 186, $y);
				$this->Line(202, $this->cellStack, 202, $y);
				$this->Line(218, $this->cellStack, 218, $y);
				$this->Line(235, $this->cellStack, 235, $y);
				$this->Line(255, $this->cellStack, 255, $y);
				$this->Line(270, $this->cellStack, 270, $y);
                
                //get Table Width
                $newTableWidth = $this->getProductTableWidth();
                if ($this->getShowSpentTime()) {
                    $newTableWidth+=12;
                   $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }
                
                if ($this->getShowTotalCost()) {
                    $newTableWidth+=12;
                   $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }

				unset($this->dayStack);
				break;

			case "TOTALONPROJECT":
				if ($this->dayStack != 1) {
					$this->rows[0] = " ";
				}
				$this->rows[1] = "";
				$this->rows[2] = "";
				$this->rows[3] = $attribs['LABEL'];
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = $attribs['MIXRATIO'];
				$this->rows[7] = $attribs['QTY'];
				$this->rows[8] = $attribs['VOC3'];
				$this->rows[9] = $attribs['EXEMPT'];
				$this->rows[10] = $attribs['TOTALVOC'];
                //get colums count
                $tableColumNumber = self::COLUMNUMBER;
                //get spent time information
                if($this->getShowSpentTime()){
                    $tableColumNumber++;
                $this->rows[$tableColumNumber] = $attribs['SPENTTIME'];
                }
                
                //get total cost information
                if($this->getShowTotalCost()){
                    $tableColumNumber++;
                $this->rows[$tableColumNumber] = $attribs['MIXCOST'];
                }
                
				$this->SetFont('Arial','B',10);
				$this->SetDrawColor(255,255,255);
				$this->SetFillColor(236,236,236);
				$this->Row($this->rows,TRUE);
				$this->SetDrawColor(0,0,0);
				$this->SetFillColor(255,255,255);
				$this->SetFont('Arial','',10);
				$x = $this->getX();
				$y = $this->getY();

				$this->Line(10, $this->cellStack, 10, $y);
				$this->Line(31, $this->cellStack, 31, $y);
				$this->Line(51, $this->cellStack, 51, $y);
				$this->Line(69, $this->cellStack, 69, $y);
				$this->Line(154, $this->cellStack, 154, $y);
				$this->Line(170, $this->cellStack, 170, $y);
				$this->Line(186, $this->cellStack, 186, $y);
				$this->Line(202, $this->cellStack, 202, $y);
				$this->Line(218, $this->cellStack, 218, $y);
				$this->Line(235, $this->cellStack, 235, $y);
				$this->Line(255, $this->cellStack, 255, $y);
				$this->Line(270, $this->cellStack, 270, $y);
               
                //get Spent Time Colum if we need to show
                $newTableWidth = $this->getProductTableWidth();
                if ($this->getShowSpentTime()) {
                    $newTableWidth+=12;
                    $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }
                //get Total Cost Colum if we need to show
                if ($this->getShowTotalCost()) {
                    $newTableWidth+=12;
                   $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }
                
				for ($i=0;$i<count($this->rows);$i++) {
					$this->rows[$i] = "";
				}
				break;

		 case "TOTALTOTALVOC":
				if ($this->dayStack != 1) {
					$this->rows[0] = " ";
				}
				$this->SetDrawColor(255,255,255);
				$this->SetFillColor(226,226,226);
				$this->Row($this->rows,TRUE);
				$this->SetFillColor(255,255,255);
				$this->SetDrawColor(0,0,0);
				$x = $this->getX();
				$y = $this->getY();

				$this->Line(10, $this->cellStack, 10, $y);
				$this->Line(31, $this->cellStack, 31, $y);
				$this->Line(51, $this->cellStack, 51, $y);
				$this->Line(69, $this->cellStack, 69, $y);
				$this->Line(154, $this->cellStack, 154, $y);
				$this->Line(170, $this->cellStack, 170, $y);
				$this->Line(186, $this->cellStack, 186, $y);
				$this->Line(202, $this->cellStack, 202, $y);
				$this->Line(218, $this->cellStack, 218, $y);
				$this->Line(235, $this->cellStack, 235, $y);
				$this->Line(255, $this->cellStack, 255, $y);
				$this->Line(270, $this->cellStack, 270, $y);
                
                //get Spent Time Colum if we need to show
                $newTableWidth = $this->getProductTableWidth();
                if ($this->getShowSpentTime()) {
                    $newTableWidth+=12;
                    $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }
                
                 //get Total Cost Colum if we need to show
                if ($this->getShowTotalCost()) {
                    $newTableWidth+=12;
                   $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                }
                
				for ($i=0;$i<count($this->rows);$i++) {
					$this->rows[$i] = "";
				}
				break;

			case "SUMMARYEQUIPMENT":
				$this->SetFillColor(226,226,226);
				$this->Row($this->rows,TRUE);
				$this->SetFillColor(255,255,255);

				$x = $this->getX();
				$y = $this->getY();

				$this->Line(10, $this->cellStack, 10, $y);
				$this->Line(31, $this->cellStack, 31, $y);
				$this->Line(51, $this->cellStack, 51, $y);
				$this->Line(69, $this->cellStack, 69, $y);
				$this->Line(154, $this->cellStack, 154, $y);
				$this->Line(170, $this->cellStack, 170, $y);
				$this->Line(186, $this->cellStack, 186, $y);
				$this->Line(202, $this->cellStack, 202, $y);
				$this->Line(218, $this->cellStack, 218, $y);
				$this->Line(235, $this->cellStack, 235, $y);
				$this->Line(255, $this->cellStack, 255, $y);
				$this->Line(270, $this->cellStack, 270, $y);
              
                //get Spent Time Colum if we need to show
                $horizontalLine = self::HORIZONTAL_LINE;
                $newTableWidth = $this->getProductTableWidth();
                if ($this->getShowSpentTime()) {
                    $newTableWidth+=12;
                    $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                    $horizontalLine+=12;
                }

                 //get Total Cost Colum if we need to show
                if ($this->getShowTotalCost()) {
                   $newTableWidth+=12;
                   $this->Line($newTableWidth, $this->cellStack, $newTableWidth, $y);
                   $horizontalLine+=12;
                }
                
				$this->SetX(10);
				$this->SetLineWidth(0.6);
				$this->Cell($horizontalLine,0,'','T');
				$this->SetLineWidth(0.2);
				$this->ln();

				break;

			case "SUMMARYTOTALEQUIPMENT":
				if ($this->companyLevel) {
					break;
				}
				$this->rows[0] = "";
				$this->rows[1] = $attribs['EQUIPMENT'];
				$this->rows[2] = "";
				$this->rows[3] = "";
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = $attribs['QTY'];
				$this->rows[7] = $attribs['VOC3'];
				$this->rows[8] = "";
				$this->rows[9] = $attribs['TOTALVOC'];
                //get table Colums count
                $tableColumNumber = self::COLUMNUMBER-1;
                
                if($this->getShowSpentTime()){
                    $tableColumNumber++;
                   $this->rows[$tableColumNumber] = $attribs['SPENTTIME']; 
                }
                
                if($this->getShowTotalCost()){
                    $tableColumNumber++;
                   $this->rows[$tableColumNumber] = $attribs['MIXCOST']; 
                }
                
				$this->Row($this->rows);
				break;

			case "SUMMARYTOTALFACILITY":
				if (!$this->companyLevel) {
					break;
				}
				$this->rows[0] = "";
				$this->rows[1] = $attribs['FACILITY'];
				$this->rows[2] = "";
				$this->rows[3] = "";
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = $attribs['QTY'];
				$this->rows[7] = $attribs['VOC3'];
				$this->rows[8] = "";
				$this->rows[9] = $attribs['TOTALVOC'];
                 //get table Colums count
                $tableColumNumber = self::COLUMNUMBER-1;
                
                if($this->getShowSpentTime()){
                    $tableColumNumber++;
                   $this->rows[$tableColumNumber] = $attribs['SPENTTIME']; 
                }
                
                if($this->getShowTotalCost()){
                    $tableColumNumber++;
                   $this->rows[$tableColumNumber] = $attribs['MIXCOST']; 
                }
				$this->Row($this->rows);
				break;

			case "SUMMARYSUM":
				$this->rows[0] = "";
				$this->rows[1] = "";
				$this->rows[2] = $this->header['RULE']." VOC TOTALS (".$this->vocUnittype.")";
				$this->rows[3] = "";
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = $attribs['QTY'];
				$this->rows[7] = $attribs['VOC3'];
				$this->rows[8] = "";
				$this->rows[9] = $attribs['TOTALVOC'];
                 //get table Colums count
                $tableColumNumber = self::COLUMNUMBER-1;
                $totalTableWidth = self::TOTAL_TABLE_WIDTH;
                
                if ($this->getShowSpentTime()) {
                    $tableColumNumber++;
                    $totalTableWidth+=12;
                    $this->rows[$tableColumNumber] = $attribs['SPENTTIME'];
                    
                }
                
                if ($this->getShowTotalCost()) {
                    $tableColumNumber++;
                    $totalTableWidth+=18;
                    $this->rows[$tableColumNumber] = $attribs['MIXCOST'];
                    
                }
				//$this->SetDrawColor(255,255,255);
				$this->SetFillColor(226,226,226);
				$this->Row($this->rows);
				$this->SetFillColor(255,255,255);
				//$this->SetDrawColor(0,0,0);
                
				$x = $this->getX();
				$y = $this->getY();

				$this->Line(10,$this->cellStack,10,$y);
                
				$this->Line($totalTableWidth+10,$this->cellStack,$totalTableWidth+10,$y);

				$this->SetX(10);
				$this->SetLineWidth(0.6);
				$this->Cell($totalTableWidth,0,'','T');
				$this->SetLineWidth(0.2);
				$this->ln();
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

			case 'PERIOD':
				$this->header['PERIOD'] = $data;
				break;

			case 'TITLE2':
				$this->header['TITLE2'] = $data;
				break;

			case 'FACILITYNAME':
				$this->header['FACILITYNANE'] = $data;
				break;

			case 'FACILITYADDRESS':
				$this->header['FACILITYADDRESS'] = $data;
				break;

			case 'FACILITYCITY':
				$this->header['FACILITYCITY'] = $data;
				break;

			case 'FACILITYCOUNTY':
				$this->header['FACILITYCOUNTY'] = $data;
				break;

			case 'FACILITYPHONE':
				$this->header['FACILITYPHONE'] = $data;
				break;

			case 'FACILITYFAX':
				$this->header['FACILITYFAX'] = $data;
				break;

			case 'COMPANYNAME':
				$this->header['COMPANYNANE'] = $data;
				break;

			case 'COMPANYADDRESS':
				$this->header['COMPANYADDRESS'] = $data;
				break;

			case 'COMPANYCITY':
				$this->header['COMPANYCITY'] = $data;
				break;

			case 'COMPANYCOUNTY':
				$this->header['COMPANYCOUNTY'] = $data;
				break;

			case 'COMPANYPHONE':
				$this->header['COMPANYPHONE'] = $data;
				break;

			case 'COMPANYFAX':
				$this->header['COMPANYFAX'] = $data;
				break;

			case 'RULE':
				$this->header['RULE'] = $data;
				break;

			case 'GCG':
				$this->header['GCG'] = $data;
				break;

			case 'RESPONSIBLEPERSON':
				$this->header['RESPONSIBLEPERSON'] = $data;
				break;

			case 'TITLEMANUAL':
				$this->header['TITLEMANUAL'] = $data;
				break;

			case 'NOTES':
				$this->header['NOTES'] = $data;
				break;

			case 'QUANTITYUNITTYPE':
				$this->quantityUnittype = $data;
				break;

			case 'VOCUNITTYPE':
				$this->vocUnittype = $data;
				break;

			case 'SUPPLIER':
				if ($data != "N/A") {
					$this->rows[1] = $data;
				} else {
					$this->rows[1] = "";
				}
				break;

			case 'PRODUCTNO':
				if ($data == "N/A") {
					$this->rows[2] = "";
				} else {
					$this->rows[2] = $data;
				}
				break;

			case 'COATINGSINGLE':
				$this->rows[3] = $data;
				break;

			case 'VOCOFMATERIAL':
				$this->rows[4] = $data;
				break;

			case 'VOC2':
				$this->rows[5] = $data;
				break;

			case 'QTYUSED':
				if ($data != "N/A") {
					$this->rows[7] = $data;
				} else {
					$this->rows[7] = "";
				}

				break;

			case 'VOC3':
				if ($data != "N/A") {
					$this->rows[8] = $data;
				} else {
					$this->rows[8] = "";
				}
				break;

			case 'RULEEXEMPTION':
				if ($data != "N/A") {
					$this->rows[9] = $data;
				} else {
					$this->rows[9] = "";
				}
				break;

			case 'TOTALVOC':
				if ($data != "N/A") {
					$this->rows[10] = $data;
				} else {
					$this->rows[10] = "";
				}
				break;

			case 'TOTALLABEL':
				$this->rows[1] = "";
				$this->rows[2] = "";
				$this->rows[3] = $data;
				break;

			case 'TOTALQTY':
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = "";
				$this->rows[7] = $data;
				break;

			case 'TOTALVOC3':
				$this->rows[8] = $data;
				break;


			case 'TOTALTOTALVOC':
				$this->rows[9] = "";
				$this->rows[10] = $data;
				break;

			case 'SUMMARYEQUIPMENTQTY':
				$this->rows[0] = "";
				$this->rows[1] = "";
				$this->rows[2] = "";
				$this->rows[3] = "Total for ".$this->equipment;
				$this->rows[4] = "";
				$this->rows[5] = "";
				$this->rows[6] = "";
				$this->rows[7] = $data;
				break;

			case 'SUMMARYEQUIPMENTVOC3':
				$this->rows[8] = $data;
				$this->rows[9] = "";
				break;

			case 'SUMMARYEQUIPMENTTOTALVOC':
				$this->rows[10] = $data;
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
    		$this->SetFont('Arial','B',15);
			$this->Cell(75,0,$this->header['TITLE'],0,0,'L');

    		$this->SetFont('Arial','',12);
			$this->Cell(125,0,$this->header['PERIOD'],0,0,'C');

    		$this->SetFont('Arial','B',15);
			$this->Cell(75,0,$this->header['TITLE2'],0,0,'R');
	    	$this->Ln(10);

    	    if (isset($this->header['FACILITYNANE'])) {
    			$this->SetFont('Arial','B',10);
    			$this->Cell(35,5,'Facility Name: ',0,0,'R');
    			$this->SetFont('Arial','',10);
				$this->Cell(50,5,$this->header['FACILITYNANE'],0,0,'L');
    		} else {
    			$this->SetFont('Arial','B',10);
    			$this->Cell(35,5,'Company Name: ',0,0,'R');
    			$this->SetFont('Arial','',10);
				$this->Cell(50,5,$this->header['COMPANYNANE'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Equip: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['EQUIPMENT'],0,0,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(75,5,'Responsible Person: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(90,5,$this->header['RESPONSIBLEPERSON'],0,1,'L');


			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Address: ',0,0,'R');
    		$this->SetFont('Arial','',10);
    		if (isset($this->header['FACILITYADDRESS'])) {
    			$this->Cell(50,5,$this->header['FACILITYADDRESS'],0,0,'L');
    		} else {
    			$this->Cell(50,5,$this->header['COMPANYADDRESS'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Permit No: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['PERMITNO'],0,0,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(75,5,'Title: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(90,5,$this->header['TITLEMANUAL'],0,1,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'City, State, Zip: ',0,0,'R');
    		$this->SetFont('Arial','',10);
    		if (isset($this->header['FACILITYCITY'])) {
				$this->Cell(50,5,$this->header['FACILITYCITY'],0,0,'L');
    		} else {
    			$this->Cell(50,5,$this->header['COMPANYCITY'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Facility ID: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['FACILITYID'],0,1,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'County: ',0,0,'R');
    		$this->SetFont('Arial','',10);
    		if (isset($this->header['FACILITYCOUNTY'])) {
				$this->Cell(50,5,$this->header['FACILITYCOUNTY'],0,0,'L');
    		} else {
    			$this->Cell(50,5,$this->header['COMPANYCOUNTY'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Rule No: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['RULE'],0,1,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Phone: ',0,0,'R');
    		$this->SetFont('Arial','',10);
    		if (isset($this->header['FACILITYPHONE'])) {
				$this->Cell(50,5,$this->header['FACILITYPHONE'],0,0,'L');
    		} else {
    			$this->Cell(50,5,$this->header['COMPANYPHONE'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'GCG No: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['GCG'],0,1,'L');

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Fax: ',0,0,'R');
    		$this->SetFont('Arial','',10);
    		if (isset($this->header['FACILITYPHONE'])) {
				$this->Cell(50,5,$this->header['FACILITYFAX'],0,0,'L');
    		} else {
    			$this->Cell(50,5,$this->header['COMPANYFAX'],0,0,'L');
    		}

			$this->SetFont('Arial','B',10);
    		$this->Cell(35,5,'Notes: ',0,0,'R');
    		$this->SetFont('Arial','',10);
			$this->Cell(50,5,$this->header['NOTES'],0,1,'L');
			$this->cellStack = $this->getY();
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
