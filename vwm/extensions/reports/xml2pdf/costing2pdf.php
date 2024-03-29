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
        //attributes
        $attribs = $this->parser->structure[$path]["Attributes"];
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
            case 'TABLE':
                $this->header();
                break;
            case 'WORKORDER':
                $this->header['NUMBER'] = $attribs['NUMBER'];
                $this->header['PROFIT'] = $attribs['PROFIT'];
                $this->header['OVERHEAD'] = $attribs['OVERHEAD'];
                $widths = array(10, 85, 20, 20, 20, 20, 20);
                $this->SetWidths($widths);
                $this->SetFont('Arial', '', 15);
                $h = 10;

                $this->Cell(200, 0, "WorkOrder: " . $attribs['NUMBER'], 0, 0, 'L');
                $this->Ln(4);
                $this->SetFont('Arial', '', 10);
                // write table head
                $this->Cell($widths[0], $h, "Step", 1, 0, 'C');
                $this->Cell($widths[1], $h, "Description", 1, 0, 'C');
                $this->Cell($widths[2], $h, "Date", 1, 0, 'C');
                //get x and y for breakdown cost cell
                $x = $this->getX();
                $y = $this->getY();
                //get cost cell lenght
                $costLenth = $widths[3] + $widths[4] + $widths[5] + $widths[6];

                $this->Cell($costLenth, $h / 2, "Cost", 1, 0, 'C');
                $this->setXY($x, $y + $h / 2);

                $this->Cell($widths[3], $h / 2, "Material", 1, 0, 'C');
                $this->Cell($widths[4], $h / 2, "Labor", 1, 0, 'C');
                $this->Cell($widths[5], $h / 2, "Paint", 1, 0, 'C');
                $this->Cell($widths[6], $h / 2, "Total", 1, 0, 'C');
                $this->Ln();
                break;
            case 'SUMMARY':
                $this->header['TOTALLABORCOST'] = $attribs['TOTALLABORCOST'];
                $this->header['TATALMATERIALCOST'] = $attribs['TATALMATERIALCOST'];
                $this->header['TOTALPAINTCOST'] = $attribs['TOTALPAINTCOST'];
                $this->AddPage('p');
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
            case 'MIX':
                $widths = array(10, 85, 20, 20, 20, 20, 20);
                $this->SetFont('Arial', '', 10);
                $h = 5;
                if($attribs['MIXDESCRIPTION']==''){
                    $attribs['MIXDESCRIPTION']="NONE";
                }
                $this->rows = array($attribs['STEP'], $attribs['MIXDESCRIPTION'], $attribs['CREATIONTIME'], $attribs['MATERIALCOST'], $attribs['LABORCOST'], $attribs['PAINTCOST'], $attribs['TOTALCOST']);
                $this->Row($this->rows);
                break;
            case 'WORKORDER':
                $widths = array(10, 85, 20, 20, 20, 20, 20);
                $workOrderLenght = array_sum($widths) - $widths[6];
                $h = 5;

                $this->Cell($workOrderLenght, $h, "Overhead", 1, 0, 'R');
                $this->Cell($widths[6], $h, $attribs['OVERHEAD'], 1, 0, 'C');
                $this->Ln();

                $this->Cell($workOrderLenght, $h, "Profit", 1, 0, 'R');
                $this->Cell($widths[6], $h, $attribs['PROFIT'], 1, 0, 'C');
                $this->Ln();

                $this->Cell($workOrderLenght, $h, "Total", 1, 0, 'R');
                $this->SetFont('Arial', 'B', 10);
                $this->Cell($widths[6], $h, $attribs['TOTAL'], 1, 0, 'C');
                $this->SetFont('Arial', '', 10);

                $this->Ln(15);
                break;
            case 'SUMMARY':
                $widths = array(48, 48, 48, 48);
                
                $this->Cell($widths[0], 10, "Material", 1, 0, 'C');
                $this->Cell($widths[1], 10, "Labor", 1, 0, 'C');
                $this->Cell($widths[2], 10, "Paint", 1, 0, 'C');
                $this->Cell($widths[3], 10, "Total", 1, 0, 'C');
                $this->Ln();
                
                $this->Cell($widths[0], 10, $attribs['TOTALMATERIALCOST'], 1, 0, 'C');
                $this->Cell($widths[1], 10, $attribs['TOTALLABORCOST'], 1, 0, 'C');
                $this->Cell($widths[2], 10, $attribs['TOTALPAINTCOST'], 1, 0, 'C');
                $this->Cell($widths[3], 10, $attribs['TOTALTOTALCOST'], 1, 0, 'C');
                break;
        }
    }

//end Element
//get valuem of tag
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
            case "CATEGORY":
                $this->header["CATEGORY"] = $data;
                break;
            case "COUNTRY":
                $this->header["COUNTRY"] = $data;
                break;
            case "ADDRESS":
                $this->header["ADDRESS"] = $data;
                break;
            case "CATEGORYNAME":
                $this->header["CATEGORYNAME"] = $data;
                break;
            case "PHONE":
                $this->header["PHONE"] = $data;
                break;
            case "FAX":
                $this->header["FAX"] = $data;
                break;
            case "ZIP":
                $this->header["ZIP"] = $data;
                break;
            case "CITY":
                $this->header["CITY"] = $data;
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
        if (isset($this->header['TITLE'])) {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(75, 10, $this->header['TITLE'], 0, 0, 'L');
            $this->Ln(7);
            $this->SetFont('Arial', '', 12);
            $this->Cell(100, 10, $this->header['PERIOD'], 0, 0, 'L');
            $this->Ln(7);
            
            $this->SetFont('Arial', '', 7);
            $this->Cell(110, 5, $this->header['CATEGORY'] . ": " . $this->header['CATEGORYNAME'], 0, 0, 'L');
            $this->Cell(75, 5, "Phone: " . $this->header['PHONE'], 0, 0, 'L');
            $this->Ln(3);
            
            $this->Cell(110, 5, "Country: " . $this->header['COUNTRY'], 0, 0, 'L');
            $this->Cell(75, 5, "Fax: " . $this->header['FAX'], 0, 0, 'L');
            $this->Ln(3);
            
            $this->Cell(110, 5, "City: " . $this->header['CITY'], 0, 0, 'L');
            $this->Cell(75, 5, "Zip: " . $this->header['ZIP'], 0, 0, 'L');
            $this->Ln(3);
            
            $this->Cell(110, 5, "Address: " . $this->header['ADDRESS'], 0, 0, 'L');
            $this->Ln(10);
            
            
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetX(-15);
        $this->Cell(0, 10, $this->PageNo(), 0, 0, 'C');
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
