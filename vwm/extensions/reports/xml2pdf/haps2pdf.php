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
    var $cellStack;
    var $equipment;
    const HEADER_FIRST_COLUM_FIELD_LENGTH = 32;
    const HEADER_FIRST_COLUM_VALUE_LENGTH = 40;
    const HEADER_SECOND_COLUM_FIELD_LENGTH = 25;
    const HEADER_SECOND_COLUM_VALUE_LENGTH = 20;

    //Class initializer.  the XML filename and optionally enable debug (set to 1)
    //Also sends PDF content-type header;
    function XML2PDF($debug = FALSE)
    {
        // Initialization
        $this->DebugPrint("initializing...");
        parent::FPDF('P', 'mm', 'A4');

        $this->debug = $debug;
        $this->abort_error = FALSE;

        $this->open_tags = array("page" => FALSE);

        $this->fontstack = array();
        $this->colorstack = array();

        $this->tablestack = array();
        $this->trstack = array();
        $this->tdstack = array();

        $this->links = array();

        $this->add_fonts = array();

        $this->indent = array("ih1" => 0, "ih2" => 5, "ih3" => 10, "ih4" => 15, "ih5" => 20, "ih6" => 25, "current" => 0);
    }

    //Parse through the XML once document and generate PDF (can be called multiple times)
    //Returns XML Error messages if bad XML. otherwise false.
    function Parse($filename)
    {
        $this->header = array();
        $this->footer = array();
        $this->filename = $filename;
        $error = XMLParseFile($this->parser, $this->filename, 0, "", 1, "ISO-8859-1");
        if (strcmp($error, "")) {
            print "Parser Error: $error\n";
            return $error;
        } else {
            $this->WalkXML("0"); //&$this->parser->structure, &$this->parser->positions);
            return false;
        }
    }

    //Parse through the XML once string and generate PDF (can be called multiple times)
    //Returns XML Error messages if bad XML string. otherwise false.
    function ParseString($str)
    {
        $this->header = array();
        $this->footer = array();
        $error = XMLParseString($this->parser, $str, 0, "", 1, "ISO-8859-1");
        if (strcmp($error, "")) {
            print "Parser Error: $error\n";
            return $error;
        } else {
            $this->WalkXML("0"); //&$this->parser->structure, &$this->parser->positions);
            return false;
        }
    }

    /*     * *****************************************************************************
     * END OF PUBLIC FUNCTIONS                             *
     * ***************************************************************************** */

    function WalkXML($path)
    {
        if (is_array($this->parser->structure[$path])) {
            //Beginning Tag
            $this->startElement($path);

            for ($element = 0; $element < $this->parser->structure[$path]["Elements"]; $element++)
                $this->WalkXML($path . ",$element");

            //End Tag
            $this->endElement($path);
        } else {
            //Content
            //Find parent path
            $parentpath = substr($path, 0, strrpos($path, ","));
            $this->DebugPrint("PATH=" . $path . "-" . strrpos($path, ",") . "-" . $parentpath);

            /* preliminary whitespace replace */
            $data = $this->parser->structure[$path];
            $data = preg_replace("/\s*\n\s*/", " ", $data);
            $data = preg_replace("/(\n|\r)/", " ", $data);
            $data = preg_replace("/^\s+/", "", $data);
            $data = preg_replace("/(\ +)/", " ", $data);
            $data = preg_replace("/^\s/", "", $data);

            $data = preg_replace("/&nbsp;/", " ", $data);

            if (strlen($data) > 0) {
                $this->characterData($this->parser->structure[$parentpath]["Tag"], $this->parser->structure[$parentpath]["Attributes"], $data, $path, $parentpath);
            }
        }
    }

    //handles the "beginning" of a tag  and sets parameters appropriatly
    function startElement($path)
    {
        $attribs = &$this->parser->structure[$path]["Attributes"];
        $tag = $this->parser->structure[$path]["Tag"];
        $this->DebugPrint("Start: $tag\n");
        
        switch ($tag) {
            case 'PAGE':
                $this->SetWidths(array(55, 45, 45, 45));
                if ($this->open_tags["page"])
                    return $this->Error("Page already open, ignoring.", FALSE);

                $this->open_tags["page"] = TRUE;

                $this->AddPage(((empty($attribs["ORIENTATION"])) ? ("P") : ($attribs["ORIENTATION"])));
                if (!empty($attribs["TOPMARGIN"])) {
                    $this->SetTopMargin($attribs["TOPMARGIN"]);
                }
                if (!empty($attribs["LEFTMARGIN"])) {
                    $this->SetTopMargin($attribs["LEFTMARGIN"]);
                }
                if (!empty($attribs["RIGHTMARGIN"])) {
                    $this->SetTopMargin($attribs["RIGHTMARGIN"]);
                }
                $this->SetAutoPageBreak(true, 25);
                break;
            case "USAGESUMMARY":
                 $this->widths = array(30,90,30,20,25);
                 $this->SetWidths($this->widths);
                 $this->SetLineWidth(0.4);
                 $this->SetFont('Arial','B',10);
                 
				 $this->Cell($this->widths[0]+$this->widths[1]+$this->widths[2]+
                            $this->widths[3]+$this->widths[4],7,
                        'Hazardous Air Pollutants(HAPs) Usage Summary (YTD)' ,1,0,'C');
                $this->Ln();
                
                //set heidht;
                $h = 10;
                
                $this->SetFont('Arial','B',7);
				$this->Cell($this->widths[0],$h,"CAS Number",1,0,'C');
				$this->Cell($this->widths[1],$h,"Description",1,0,'C');
				$this->Cell($this->widths[2],$h,"% Usage",1,0,'C');
				$this->Cell($this->widths[3],$h,"Amount(gal)",1,0,'C');
				$this->Cell($this->widths[4],$h,"Total Emissions( Ibs)",1,0,'C');
                $this->Ln();
                
                break;
            case "PRODUCTGROUP":
                $this->SetFont('Arial','',12);
                $this->SetFillColor(0,255,0);
                $this->Cell($this->widths[0] + $this->widths[1] + $this->widths[2] +
                        $this->widths[3] + $this->widths[4], 7, $attribs['NAME'], 1, 0, 'C');
                $this->ln();
                break;
            
        }
    }

    //handles the "end" of a tag and (un)sets parameters appropriatly
    function endElement($path)
    {
        $attribs = &$this->parser->structure[$path]["Attributes"];
        $tag = $this->parser->structure[$path]["Tag"];
        $this->DebugPrint("End: $tag\n");
        switch ($tag) {
            case "COMPONENT":
                $this->widths = array(30,90,30,20,25);
                $this->SetWidths($this->widths);
                $this->SetFont('Arial','',7);
                $h = 5;
                $this->Cell($this->widths[0], $h, $attribs['CASNUMBER'], 1, 0, 'C');
                $this->Cell($this->widths[1], $h, $attribs['DESCRIPTION'], 1, 0, 'C');
                $this->Cell($this->widths[2], $h, $attribs['WEIGHT'], 1, 0, 'C');
                $this->Cell($this->widths[3], $h, $attribs['AMOUNT'], 1, 0, 'C');
                $this->Cell($this->widths[4], $h, $attribs['EMISSIONS'], 1, 0, 'C');
                $this->ln();
                break;
            
            case 'SUMMARYCOMPONENT':
                $this->widths = array(30,90,30,20,25);
                $this->SetWidths($this->widths);
                $this->SetFont('Arial','',7);
                $h = 5;
                $this->Cell($this->widths[0], $h, $attribs['NAME'], 1, 0, 'C');
                $this->Cell($this->widths[1], $h, $attribs['COMPONENTDESCRIPTION'], 1, 0, 'C');
                $this->Cell($this->widths[2], $h, $attribs['COMPONENTSUMMARYUSAGE'], 1, 0, 'C');
                $this->Cell($this->widths[3], $h, $attribs['COMPONENTSUMMARYAMOUNT'], 1, 0, 'C');
                $this->Cell($this->widths[4], $h, $attribs['COMPONENTSUMMARYTOTALEMISSION'], 1, 0, 'C');
                $this->ln();
            break;
        
            case 'TOTALSUMMARYUSAGE':
                $this->widths = array(150,20,25);
                $this->SetWidths($this->widths);
                $this->SetFont('Arial','B',7);
                $h = 5;
                
                $this->Cell(150,$h, 'Period: '.$attribs['TOTALSUMMARYUSAGEPERIOD'].'    Total HAPs Usage:', 1, 0, 'C');
                $this->Cell($this->widths[1], $h, $attribs['TOTALSUMMARYAMOUNT'], 1, 0, 'C');
                $this->Cell($this->widths[2], $h, $attribs['TOTALSUMMARYEMISSIONS'], 1, 0, 'C');
            break;
        }
    }

//end Element

    function characterData($tag, $attribs, $data, $path, $parentpath)
    {
        $this->DebugPrint("CharData tag=$tag data=\"$data\"");

        switch ($tag) {
            case "TITLE":
                $this->header["TITLE"] = $data;
                break;
            case "PERIOD":
                $this->header["PERIOD"] = $data;
                break;
            case 'CATEGORYNAME':
                $this->header["CATEGORYNAME"] = $data;
                break;
            case 'CATEGORY':
				$this->header['CATEGORY'] = $data;
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
                $this->header();
				break;
        }
    }

    function Error($text, $abort = FALSE)
    {
        if (!$this->abort_error)
            $this->abort_error = $abort;

        print "Error: $text\n";
        return 0;
    }

    function Header()
    {
        if (isset($this->header["CATEGORYNAME"])) {
            //write title
            $this->SetX(-15);
            $this->setY(0);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(290, 10, $this->header['TITLE'], 0, 1, 'L');

            //write period
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(290, 10, $this->header['PERIOD'], 0, 0, 'L');

            $this->Ln(5);

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 10, $this->header['CATEGORY'] . " Name:", 0, 0, 'L');
            $this->SetY(17.5);
            $this->SetX(42);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(80, 5, $this->header['CATEGORYNAME'],0,'L');
            //$this->setY(24);
            //WRITE FACILITYID
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, $this->header['CATEGORY'] . " ID:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_SECOND_COLUM_VALUE_LENGTH, 5, $this->header['FACILITYID'],0,1);

            //second row
            //write address
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, "Address:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_VALUE_LENGTH, 5, $this->header['ADDRESS'],0,1);
            $this->SetFont('Arial', 'B', 10);
            /*   $this->Cell(self::HEADER_SECOND_COLUM_FIELD_LENGTH,10,"SIC Code:",0,0,'L');
              $this->SetFont('Arial','',10);
              $this->Cell(self::HEADER_SECOND_COLUM_VALUE_LENGTH,10,'VALUE'); */

            //third row
            //write zip code
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, "City, State, Zip:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_VALUE_LENGTH, 5, $this->header['CITYSTATEZIP'],0,1);
            $this->SetFont('Arial', 'B', 10);
            /* $this->Cell(self::HEADER_SECOND_COLUM_FIELD_LENGTH,10,"GRC-Pirk#:",0,0,'L');
              $this->SetFont('Arial','',10);
              $this->Cell(self::HEADER_SECOND_COLUM_VALUE_LENGTH,10,'VALUE'); */
            
            //fourth row
            //write country
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, "County:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_VALUE_LENGTH, 5, $this->header['COUNTY'],0,1);
            $this->SetFont('Arial', 'B', 10);
            /* $this->Cell(self::HEADER_SECOND_COLUM_FIELD_LENGTH,10,"Site Mapk#:",0,0,'L');
              $this->SetFont('Arial','',10);
              $this->Cell(self::HEADER_SECOND_COLUM_VALUE_LENGTH,10,'VALUE'); */
            
            //fifth row
            //write phone
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, "Phone:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_VALUE_LENGTH, 5, $this->header['PHONE'],0,1);
            $this->SetFont('Arial', 'B', 10);
            /* $this->Cell(self::HEADER_SECOND_COLUM_FIELD_LENGTH,10,"Applied Rule#:",0,0,'L');
              $this->SetFont('Arial','',10);
              $this->Cell(self::HEADER_SECOND_COLUM_VALUE_LENGTH,10,'VALUE'); */
            
            //write fax
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_FIELD_LENGTH, 5, "Fax:", 0, 0, 'L');
            $this->SetFont('Arial', '', 10);
            $this->Cell(self::HEADER_FIRST_COLUM_VALUE_LENGTH, 5, $this->header['FAX'],0,1);
            //get last Y 
            $y = $this->getY();
            
            $this->SetY(20);
            $this->SetX(140);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 10, 'Year to Date (YTD) Summary');

            $this->SetY(25);
            $this->SetX(128);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(40, 10, 'Responsible Party:');

            $this->SetY(30);
            $this->SetX(128);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(40, 10, 'Title:');

            //draw a box around the information
            $this->SetLineWidth(0.2);
            $this->SetFillColor(0, 255, 0);
            $this->Line(10, 18, 10, $y);
            $this->Line(127, 18, 127, $y);
            $this->Line(10, 18, 127, 18);
            $this->Line(10, $y, 127, $y);
            $this->SetY($y+5);

            //set table header for units
            $this->widths = array(30, 90, 30, 20, 25);
            $this->SetWidths($this->widths);
            $this->aligns = array();
            $this->SetAligns($this->aligns);
            //$this->SetFillColor(200, 200, 200, 200, 200, 200, 200);
            $this->SetLineWidth(0.4);

            $this->SetFont('Arial', 'B', 10);
            $this->Cell($this->widths[0] + $this->widths[1] + $this->widths[2] +
                    $this->widths[3] + $this->widths[4], 7, 'Hazardous Air Pollutants (HAPs) Totals bv Product (YTD)', 1, 0, 'C');
            $this->Ln();

            //set heidht;
            $h = 10;

            $this->SetFont('Arial', 'B', 5);
            $this->Cell($this->widths[0], $h, "CAS Number", 1, 0, 'C');
            $this->Cell($this->widths[1], $h, "Description", 1, 0, 'C');
            $this->Cell($this->widths[2], $h, "Product Weight (Ibs/ga)", 1, 0, 'C');
            $this->Cell($this->widths[3], $h, "Amount \n\r(gal)", 1, 0, 'C');
            $this->Cell($this->widths[4], $h, "HAPs Emissions \n( Ibs )", 1, 0, 'C');
            $this->Ln();
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetX(-15);
        $this->Cell(0, 10, $this->PageNo(), 0, 0, 'C');
        //$this->SetY(52);
    }

    //DebugPrint wrapper..Only prints when debug==1
    function DebugPrint($message)
    {
        if (!$this->debug)
            return;
        print "$message\n";
    }
}
?>
