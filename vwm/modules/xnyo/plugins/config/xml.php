<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/config/xml.php
 *              XML File Configuration Parser
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: xml.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/config/xml.php $
 * Authors:     Robert Amos <bok[at]odynia.org>
 *              Andrew Wellington <proton[at]wiretapped.net>
 *              vort <vort[at]solutionstap.com>
 *
 * Copyright (c) 2001-2004 Robert Amos <bok[at]odynia.org>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
**/

class config_xml_plugin
{
	// parse the thing
	public function parse ($file)
	{
		global $xnyo_parent;

		// check first
		if (is_null($file))
		{
			$xnyo_parent->trigger_error('No configuration file specified.', WARNING);
			return array();
		}

		// check it exists and we can read it
		if (!file_exists($file) || !is_readable($file))
		{
			$xnyo_parent->trigger_error('File <i>'.$file.'</i> not found or isnt readable.', WARNING);
			return array();
		}

		// go!
		$obj = simplexml_load_file($file);
		
		return $obj;
		
	}
}