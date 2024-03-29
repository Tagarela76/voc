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
             case 'EQUIPMENT':
                if (isset($this->equipment)) {
                    $this->equipment = $attribs['DESCRIPTION'];
                    $this->header['EQUIPMENT'] = $attribs['DESCRIPTION'];
                    $this->header['PERMIT'] = $attribs['PERMIT'];
                    $this->header['EQUIPMENTID'] = $attribs['ID'];
                    $this->AddPage('p');
                } else {
                    $this->equipment = $attribs['DESCRIPTION'];
                    $this->header['EQUIPMENT'] = $attribs['DESCRIPTION'];
                    $this->header['PERMIT'] = $attribs['PERMIT'];
                    $this->header['EQUIPMENTID'] = $attribs['ID'];
                    $this->header();
                }
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
            case 'LOGBOOKINSPECTION':
                $this->widths = array(15, 10, 12, 12, 30, 25, 40, 25, 25);
                $this->SetWidths($this->widths);
                $this->SetFont('Arial', '', 7);
                $h = 5;
                $this->rows = array($attribs['DATE'],$attribs['TIME'],$attribs['START'],$attribs['END'],$attribs['GAUGE'],$attribs['INSPECTEDPERSON'],$attribs['INSPECTIONTYPE'], $attribs['CONDITION'],$attribs['DESCRIPTIONNOTES']);								
                $this->Row($this->rows);
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
            case 'COMPANYADDRESS':
                $this->header["COMPANYADDRESS"] = $data;
                break;
            case 'COUNTRY':
                $this->header["COUNTRY"] = $data;
                break;
            case 'FAX':
                $this->header["FAX"] = $data;
                break;
            case 'PHONE':
                $this->header["PHONE"] = $data;
                break;
            case 'CITYSTATEZIP':
                $this->header["CITYSTATEZIP"] = $data;
                break;
            case 'FACILITYNAME':
                $this->header["FACILITYNAME"] = $data;
                break;
            case 'COMPANYNAME':
                $this->header["COMPANYNAME"] = $data;
                //$this->header();
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
        $fontSize = 7;
        if (isset($this->header['TITLE'])) {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(75, 0, $this->header['TITLE'], 0, 0, 'L');
            $this->Ln(4);
            //display company address
            $this->SetFont('Arial', 'B', $fontSize);
            $this->Cell(100, 10, "Company: " . $this->header['COMPANYNAME'], 0, 0, 'L');
            $this->Cell(75, 10, "Address: " . $this->header['COMPANYADDRESS'], 0, 0, 'L');
            $this->Ln(3);
            $this->Cell(100, 10, "Facility: " . $this->header['FACILITYNAME'], 0, 0, 'L');
            $this->Cell(75, 10, "Fax: " . $this->header['FAX'], 0, 0, 'L');
            $this->Ln(3);
            $this->Cell(100, 10, "Country: " . $this->header['COUNTRY'], 0, 0, 'L');
            if($this->header['EQUIPMENTID'] != 0){
                $this->Cell(75, 10, "Equipment: " . $this->header['EQUIPMENT'], 0, 0, 'L');
            }else{
                $this->Cell(75, 10, $this->header['EQUIPMENT'], 0, 0, 'L');
            }
            $this->Ln(3);
            $this->Cell(100, 10, "Phone: " . $this->header['PHONE'], 0, 0, 'L');
            if($this->header['EQUIPMENTID'] != 0){
                $this->Cell(75, 10, "Permit Number: " . $this->header['PERMIT'], 0, 0, 'L');
            }
            $this->Ln(3);
            $this->Cell(100, 10, "City,State,Zip: " . $this->header['CITYSTATEZIP'], 0, 0, 'L');
            /*             * *** */
            $this->Ln(15);

            $this->widths = array(15, 10, 12, 12, 30, 25, 40, 25, 25);
            $this->SetWidths($this->widths);
            $this->SetLineWidth(0.2);
            $this->SetFont('Arial', 'B', 7);
            $h = 15;
            
            $this->rows = array('DATE','TIME','START READING (A)','END READING (B)','GAUGE TYPE','INSPECTED BY','INSPECTION TYPE', 'CONDITION','NOTES');								
			$this->Row($this->rows);
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
